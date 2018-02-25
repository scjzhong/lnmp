<?php
class MySQLPool
{
	private $serv;
	private $pdo;
	public function __construct() {
		$this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set([
            'worker_num' => 8,
            'daemonize' => false,
//             'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1 ,
            'task_worker_num' => 8,
            'open_eof_check'=>true, //是否检测结尾
            'package_eof'=>PHP_EOL, //结尾标识，这里的结尾最好使用不容易跟真正的body混淆的字符
            'open_eof_split'=>true //必须开启切割
        ]);
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));
                // bind callback
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Finish', array($this, 'onFinish'));
        $this->serv->start();
	}
	public function onWorkerStart( $serv , $worker_id) {
        echo "onWorkerStart\n";
        // 判定是否为Task Worker进程
        if( $worker_id >= $serv->setting['worker_num'] ) {
        	$this->pdo = new PDO(
        		"mysql:host=127.0.0.1;port=3306;dbname=test", 
        		"root", 
        		"root", 
        		array(
	                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
	                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	                PDO::ATTR_PERSISTENT => true
            	)
            );
        }
    }
    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        var_dump(rtrim($data, PHP_EOL));

        $res = json_decode(rtrim($data, PHP_EOL),true);
        $sql = array(
        	'sql'=>'INSERT INTO task (`fd`,`work_id`,`i`,`create_time`) value ( ?, ?, ?, ?)',
        	'param' => array(
        		$fd ,
        	    $serv->worker_id,
        	    $res['i'],
        		time()
        	),
        	'fd' => $fd,
        );
        $serv->task( json_encode($sql) );
    }
    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
   	public function onTask($serv,$task_id,$from_id, $data) {
        try{
            $sql = json_decode( $data , true );
        
            $statement = $this->pdo->prepare($sql['sql']);
            $statement->execute($sql['param']);     
            //$serv->send( $sql['fd'],"Insert");
            return true;
        } catch( PDOException $e ) {
            var_dump($e);
            try {
                if($e->getCode() == 'HY000' && $e->errorInfo[0] == 'HY000' && $e->errorInfo[1] == 2006 && $e->errorInfo[2] == 'MySQL server has gone away'){
                    $this->pdo = new PDO(
                        "mysql:host=127.0.0.1;port=3306;dbname=test",
                        "root",
                        "root",
                        array(
                            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_PERSISTENT => true
                        )
                    );
                }
                
                $statement = $this->pdo->prepare($sql['sql']);
                $statement->execute($sql['param']);
                return true;
                
            } catch (PDOException $e) {
                #TODO
                return false;
            }
        }
    }
    
    
    public function onFinish($serv,$task_id, $data) 
    {
        //echo "{$task_id}\n";
    }
}
new MySQLPool();

/*
在swoole中，一个swoole_server的相关属性可以通过

$serv->set( $array configs );
函数来配置，这些配置选项使得swoole更加灵活。 示例：

$serv = new swoole_server("0.0.0.0", 9501);
$serv->set(array(
    'worker_num' => 8,
    'max_request' => 10000,
    'max_conn' => 100000,
    'dispatch_mode' => 2,
    'debug_mode'=> 1，
    'daemonize' => false,
));
配置选项以及相关介绍如下：

[TOC] ####1.worker_num 描述：指定启动的worker进程数。
说明：swoole是master-> n * worker的模式，开启的worker进程数越多，server负载能力越大，但是相应的server占有的内存也会更多。同时，当worker进程数过多时，进程间切换带来的系统开销也会更大。因此建议开启的worker进程数为cpu核数的1-4倍。
示例：

'worker_num' => 8
####2.max_request 描述：每个worker进程允许处理的最大任务数。
说明：设置该值后，每个worker进程在处理完max_request个请求后就会自动重启。设置该值的主要目的是为了防止worker进程处理大量请求后可能引起的内存溢出。
示例：

'max_request' => 10000
####3.max_conn 描述：服务器允许维持的最大TCP连接数
说明：设置此参数后，当服务器已有的连接数达到该值时，新的连接会被拒绝。另外，该参数的值不能超过操作系统ulimit -n的值，同时此值也不宜设置过大，因为swoole_server会一次性申请一大块内存用于存放每一个connection的信息。
示例：

'max_conn' => 10000
####4.ipc_mode 描述：设置进程间的通信方式。
说明：共有三种通信方式，参数如下：

1 => 使用unix socket通信
2 => 使用消息队列通信
3 => 使用消息队列通信，并设置为争抢模式
示例：

'ipc_mode' => 1
####5.dispatch_mode 描述：指定数据包分发策略。
说明：共有三种模式，参数如下：

1 => 轮循模式，收到会轮循分配给每一个worker进程
2 => 固定模式，根据连接的文件描述符分配worker。这样可以保证同一个连接发来的数据只会被同一个worker处理
3 => 抢占模式，主进程会根据Worker的忙闲状态选择投递，只会投递给处于闲置状态的Worker
示例：

'dispatch_mode' => 2
####6.task_worker_num 描述：服务器开启的task进程数。
说明：设置此参数后，服务器会开启异步task功能。此时可以使用task方法投递异步任务。

设置此参数后，必须要给swoole_server设置onTask/onFinish两个回调函数，否则启动服务器会报错。

示例：

'task_worker_num' => 8
####7.task_max_request 描述：每个task进程允许处理的最大任务数。
说明：参考max_request task_worker_num
示例：

'task_max_request' => 10000
####8.task_ipc_mode 描述：设置task进程与worker进程之间通信的方式。
说明：参考ipc_mode
示例：

'task_ipc_mode' => 2
####9.daemonize 描述：设置程序进入后台作为守护进程运行。
说明：长时间运行的服务器端程序必须启用此项。如果不启用守护进程，当ssh终端退出后，程序将被终止运行。启用守护进程后，标准输入和输出会被重定向到 log_file，如果 log_file未设置，则所有输出会被丢弃。
示例：

'daemonize' => 0
####10.log_file 描述：指定日志文件路径
说明：在swoole运行期发生的异常信息会记录到这个文件中。默认会打印到屏幕。注意log_file 不会自动切分文件，所以需要定期清理此文件。
示例：

'log_file' => '/data/log/swoole.log'
####11.heartbeat_check_interval 描述：设置心跳检测间隔
说明：此选项表示每隔多久轮循一次，单位为秒。每次检测时遍历所有连接，如果某个连接在间隔时间内没有数据发送，则强制关闭连接（会有onClose回调）。
示例：

'heartbeat_check_interval' => 60
####12.heartbeat_idle_time 描述：设置某个连接允许的最大闲置时间。
说明：该参数配合heartbeat_check_interval使用。每次遍历所有连接时，如果某个连接在heartbeat_idle_time时间内没有数据发送，则强制关闭连接。默认设置为heartbeat_check_interval * 2。
示例：

'heartbeat_idle_time' => 600
####13.open_eof_check 描述：打开eof检测功能
说明：与package_eof 配合使用。此选项将检测客户端连接发来的数据，当数据包结尾是指定的package_eof 字符串时才会将数据包投递至Worker进程，否则会一直拼接数据包直到缓存溢出或超时才会终止。一旦出错，该连接会被判定为恶意连接，数据包会被丢弃并强制关闭连接。

EOF检测不会从数据中间查找eof字符串，所以Worker进程可能会同时收到多个数据包，需要在应用层代码中自行explode("\r\n", $data) 来拆分数据包

示例：

'open_eof_check' => true
####14.package_eof 描述：设置EOF字符串
说明：package_eof最大只允许传入8个字节的字符串
示例：

'package_eof ' => '/r/n'
####15.open_length_check 描述：打开包长检测
说明：包长检测提供了固定包头+包体这种格式协议的解析，。启用后，可以保证Worker进程onReceive每次都会收到一个完整的数据包。
示例：

'open_length_check' => true
####16.package_length_offset 描述：包头中第几个字节开始存放了长度字段
说明：配合open_length_check使用，用于指明长度字段的位置。
示例：

'package_length_offset' => 5
####17.package_body_offset 描述：从第几个字节开始计算长度。
说明：配合open_length_check使用，用于指明包头的长度。
示例：

'package_body_offset' => 10
####18.package_length_type 描述：指定包长字段的类型
说明：配合open_length_check使用，指定长度字段的类型，参数如下：

's' => int16_t 机器字节序
'S' => uint16_t 机器字节序
'n' => uint16_t 大端字节序
’N‘ => uint32_t 大端字节序
'L' => uint32_t 机器字节序
'l' => int 机器字节序
示例：

'package_length_type' => 'N'
####19.package_max_length 描述：设置最大数据包尺寸
说明：该值决定了数据包缓存区的大小。如果缓存的数据超过了该值，则会引发错误。具体错误处理由开启的协议解析的类型决定。
示例：

'package_max_length' => 8192
####20.open_cpu_affinity 描述：启用CPU亲和性设置
说明：在多核的硬件平台中，启用此特性会将swoole的reactor线程/worker进程绑定到固定的一个核上。可以避免进程/线程的运行时在多个核之间互相切换，提高CPU Cache的命中率。
示例：

'open_cpu_affinity' => true
####21.open_tcp_nodelay 描述：启用open_tcp_nodelay
说明：开启后TCP连接发送数据时会无关闭Nagle合并算法，立即发往客户端连接。在某些场景下，如http服务器，可以提升响应速度。
示例：

'open_tcp_nodelay' => true
####22.tcp_defer_accept 描述：启用tcp_defer_accept特性
说明：启动后，只有一个TCP连接有数据发送时才会触发accept。
示例：

'tcp_defer_accept' => true
####23.ssl_cert_file和ssl_key_file 描述：设置SSL隧道加密
说明：设置值为一个文件名字符串，指定cert证书和key的路径。
示例：

'ssl_cert_file' => '/config/ssl.crt',
'ssl_key_file' => '/config//ssl.key',
####24.open_tcp_keepalive 描述：打开TCP的KEEP_ALIVE选项
说明：使用TCP内置的keep_alive属性，用于保证连接不会因为长时闲置而被关闭。
示例：

'open_tcp_keepalive' => true
####25.tcp_keepidle 描述：指定探测间隔。
说明：配合open_tcp_keepalive使用，如果某个连接在tcp_keepidle内没有任何数据来往，则进行探测。
示例：

'tcp_keepidle' => 600
####26.tcp_keepinterval 描述：指定探测时的发包间隔
说明：配合open_tcp_keepalive使用
示例：

'tcp_keepinterval' => 60
####27.tcp_keepcount 描述：指定探测的尝试次数
说明：配合open_tcp_keepalive使用，若tcp_keepcount次尝试后仍无响应，则判定连接已关闭。
示例：

'tcp_keepcount' => 5
####28.backlog 描述：指定Listen队列长度
说明：此参数将决定最多同时有多少个等待accept的连接。
示例：

'backlog' => 128
####29.reactor_num 描述：指定Reactor线程数
说明：设置主进程内事件处理线程的数量，默认会启用CPU核数相同的数量， 一般设置为CPU核数的1-4倍，最大不得超过CPU核数*4。
示例：

'reactor_num' => 8
####30.task_tmpdir 描述：设置task的数据临时目录
说明：在swoole_server中，如果投递的数据超过8192字节，将启用临时文件来保存数据。这里的task_tmpdir就是用来设置临时文件保存的位置。

需要swoole-1.7.7+

示例：

'task_tmpdir' => '/tmp/task/'
*/

以上是对swoole的配置描述
    这里存在一个很致命的问题 我们知道mysql 创建一个连接后长时间 没有 使用该连接 mysql  会关闭此链接
        我们经常发现的mysql has gone away 2006  即该链接 被mysql 服务端关闭了
        
        ["errorInfo"]=>
        array(3) {
            [0]=>
            string(5) "HY000"
                [1]=>
                int(2006)
                [2]=>
                string(26) "MySQL server has gone away"
        }
        
    如何再能做到 断线重连 其实很简单 既然断了 再连接一次就好。
    查看mysql 连接数 
    show full processlist 
    查看timeout的设置
    SHOW GLOBAL VARIABLES LIKE '%timeout%';
    
    +-----------------------------+----------+
    | Variable_name               | Value    |
    +-----------------------------+----------+
    | connect_timeout             | 10       |
    | delayed_insert_timeout      | 300      |
    | have_statement_timeout      | YES      |
    | innodb_flush_log_at_timeout | 1        |
    | innodb_lock_wait_timeout    | 30       |
    | innodb_rollback_on_timeout  | OFF      |
    | interactive_timeout         | 28800    |
    | lock_wait_timeout           | 31536000 |
    | net_read_timeout            | 30       |
    | net_write_timeout           | 60       |
    | rpl_stop_slave_timeout      | 31536000 |
    | slave_net_timeout           | 60       |
    | wait_timeout                | 600      |
    +-----------------------------+----------+
    
    如上 wait_timeout 超时时间是600s。
    修改超时时间便于测试（30s）
    SET GLOBAL wait_timeout=30;
    此时再看上面的代码
    onTask时捕获断开连接的异常 此时捕获的如果是 2006 && MySQL server has gone away && HY000
    则重新连接并将此链接保存重新绑定到该task进程上即可。 
    
    以 10000 次插入数据为例
    <?php
    
    ini_set('max_execution_time', '0');
    
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=test", "root", "root",
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true
        )
    );
    
    $time1 = time();
    
    for($i =0; $i<10000; $i++){
        $sql = array(
            'sql'=>'INSERT INTO task (`fd`,`create_time`) value ( ?, ?)',
            'param' => array(1 ,time())
        );
        $statement = $pdo->prepare($sql['sql']);
        $statement->execute($sql['param']);
    }
    
    $time2 = time();
    
    echo $time2 - $time1;
    
    以创建一个pdo 连接 循环 一万次 插入
    耗时 26s
    以swoole 的server task去处理
    
    0.73130400 1519548667 开始毫秒数
    0.81475500 1519548667 结束毫秒数
    
    不到1 s
    