1：问题描述
    在'dispatch_mode' => 3,时
    linux 多线程输出问题
    string(10) "{"i":9925}"
    {"i":9927}string(10) ""
    string(10) "{"i":9933}"
    string(10) "{"i":9935}"
    string(10) "{"i":9943}{"i":9941}"

    创建一个swoole_server 注册task
    客户端 $c 向服务端发送数据调用 $c->send(json_encode(['i'=>1]));
    服务端receive var_dump打印出来的数据可能是 
    string(7) "{"i":1}"
    
    当客户端循环发送10次 结果可能是这样
    string(70) "{"i":0}{"i":1}{"i":2}{"i":3}{"i":4}{"i":5}{"i":6}{"i":7}{"i":8}{"i":9}"
    或者 
    string(7) "{"i":0}"
    string(7) "{"i":1}"
    string(7) "{"i":2}"
    string(7) "{"i":3}"
    string(7) "{"i":4}"
    string(7) "{"i":5}"
    string(7) "{"i":6}"
    string(7) "{"i":7}"
    string(7) "{"i":8}"
    string(7) "{"i":9}"
    
    那么问题出在哪儿
    看如下的文章
    http://rango.swoole.com/archives/464
    具体原因如下：
    因为TCP通信是流式的，在接收1个大数据包时，可能会被拆分成多个数据包发送。多次Send底层也可能会合并成一次进行发送。这里就需要2个操作来解决：
    分包：Server收到了多个数据包，需要拆分数据包
    合包：Server收到的数据只是包的一部分，需要缓存数据，合并成完整的包
    客户端伪代码
    $client = new Client();  
    $client->connect();  
    
    for ($i=0; $i<10; $i++){
        $data = json_encode(['i' => $i]);
        $client->send($data . PHP_EOL);
    }
    
    服务端为伪代码如下
    $server->set(
        [
            'open_eof_check'=>true, //是否检测结尾
            'package_eof'=>PHP_EOL, //结尾标识，这里的结尾最好使用不容易跟真正的body混淆的字符
            'open_eof_split'=>true //必须开启切割
        ]
    );
    
    需添加以上的3种配置 进行包 的处理。
    
    在onReceive中接受到的数据进行trim处理。
    
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        var_dump($data);
        $data = trim($data);#去掉客户端发送时带来上的 PHP_EOL
        $serv->task($data);
    }
    
    
   设置了以上还是会导致
    string(10) "{"i":9925}"
    {"i":9927}string(10) ""
    string(10) "{"i":9933}"
    string(10) "{"i":9935}"
    string(10) "{"i":9943}{"i":9941}"
    "
    string(10) "{"i":9949}string(10) "{"i":9951}"
    string(10) ""
    string(10) "{"i":9957}{"i":9959}"
    "
    string(10) "string(10) "{"i":9965}"
    {"i":9967}string(10) ""
    string(10) "{"i":9973}{"i":9975}"
    "
    string(10) "string(10) "{"i":9981}{"i":9983}"
    string(10) ""
    string(10) "{"i":9991}{"i":9989}"
    "
    string(10) "string(10) "{"i":9999}{"i":9997}"
    
    这是因为设置了
    'dispatch_mode' => 3,
    1，轮循模式，收到会轮循分配给每一个worker进程
    2，固定模式，根据连接的文件描述符分配worker。这样可以保证同一个连接发来的数据只会被同一个worker处理
    3，抢占模式，主进程会根据Worker的忙闲状态选择投递，只会投递给处于闲置状态的Worker
    4，IP分配，根据客户端IP进行取模hash，分配给一个固定的worker进程。可以保证同一个来源IP的连接数据总会被分配到同一个worker进程。算法为 ip2long(ClientIP) % worker_num
    5，UID分配，需要用户代码中调用 $serv-> bind() 将一个连接绑定1个uid。然后swoole根据UID的值分配到不同的worker进程。算法为 UID % worker_num，如果需要使用字符串作为UID，可以使用crc32(UID_STRING)
    这里 'dispatch_mode' => 3, 修改为2即可
    
    
2：
    问题描述： 在设置'max_request' => 10000,时 
    客户端发送10000次数据task能够正常完成。
    当客户端发送100000次 服务端只能完成几千次
    
    一次测试数据如下
    
    当任务投递到
    string(11) "{"i":10026}"
    string(11) "{"i":10027}"
    string(11) "{"i":10028}"
    string(11) "{"i":10029}"
    string(11) "{"i":10030}"
    string(11) "{"i":10031}"
    string(11) "{"i":10032}"
    string(11) "{"i":10033}"
    string(11) "{"i":10034}"
    
    服务端程序停止执行
    且
    此时任务只完成
    1095个
    
  初步判断是 该进程处理完后自动重启后 该进程绑定的mysql 连接无法使用 直到连接超时断开。
  此时无法继续工作
  
  此时关掉'max_request' => 10000
  即可
  
  
    