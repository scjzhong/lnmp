<?php

/**
 * 构造方法中不要使用SWOOLE_BASE SWOOLE_BASE 
 * 使用Base模式，业务代码在Reactor中直接执行 （会导致在广播时大多数情况下无法广播给所有的 $_server->connections）.
 * 若不存在客户端与客户端之间的通讯 可采用BASE模式
 * 默认使用SWOOLE_PROCESS模式 即使用进程模式，业务代码在Worker进程中执行
 */
$server = new \swoole_websocket_server("0.0.0.0", 9502);
$server->set([
    'worker_num' => 4,//启动worker进程数
    'task_worker_num' => 4,//配置task进程的数量，配置此参数后将会启用task功能。所以swoole_server务必要注册onTask/onFinish2个事件回调函数。如果没有注册，服务器程序将无法启动。
    'max_request' => 200,//设置worker进程的最大任务数，默认为0，一个worker进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源。然后master进程再创建一个新的worker进程
    'log_file' => '/var/log/swoole/swoole.log', //指定swoole错误日志文件。在swoole运行期发生的异常信息会记录到这个文件中。默认会打印到屏幕。
//     'ssl_cert_file' => __DIR__.'/config/ssl.crt',
//     'ssl_key_file' => __DIR__.'/config//ssl.key',//使用SSL必须在编译swoole时加入--enable-openssl选项 网站使用了https 需要设置这两个
//     'daemonize' => 1,//开启守护进程模式后(daemonize => true)，标准输出将会被重定向到log_file
]);



/**
 * 该回调仅仅在需要自定义握手时才需要设置该回调否则不要设置该回调事件。当设置了handshake回调则不会触发open回调事件。
 */
// $server->on('handshake', 'user_handshake');
/**
 * 当客户端向swoole服务端发起一个请求时 会触发connect回调事件 当使用websocket 服务端时则不使用connect 回调事件
 */
// $server->on('connect', function($serv, $fd){
//     var_dump($_SESSION);
//     echo "\n";
//     echo "connect - $fd\n";
// });

/**
 * 当来自客户端的一个连接请求时,会触发该回调事件
 * $_server->worker_pid  执行本次链接操作的额进程id 可通过 ps -auxf | grep server.php 查看
 * $request->fd 客户端请求的id 即客户端在本服务中的唯一标识。 若用户登陆记录的session uid 可将二者绑定。
 * 可调用$_server->push($request->fd, $_send); $_send 为发送数据
 * 可调用getClientInfo() 获取客户端信息
 * 需要注意到的是 当新开一个 窗口则被认为又是一个客户端
 * 可用 (array) $request->get 获取get参数  $request->get['uid'] 获取指定键名的 get参数
 */
$server->on('open', function (swoole_websocket_server $_server, swoole_http_request $request) {
    //连接redis 绑定uid 和fd
    try{
        $redis = new \Redis();
        $redis->connect('118.190.22.125',6380);
        $auth = $redis->auth('redis_nihao123###');
        $redis->select(1);
        $name = $redis->get('name');
        echo $name;
    }catch (Exception $e){
        echo "[redis错误码]:" . $e->getCode() . ',[错误信息]:' . $e->getMessage() . '\n';//daemonize=>true 输出重定向到log_file路径处
        $_server->push($request->fd, $e->getMessage());
        $_server->push($request->fd, $e->getCode());
        //连接redis失败shutdown服务
        //$_server->shutdown();
    }
    if(!$auth){//密码认证失败
        $jsonData = [
            'status' => 200,
            'msg'    => 'redis 服务不可用，请联系管理员',
        ];
        $_server->push($request->fd, json_encode($jsonData));
    }
    
    $_send = 'success';
    $_server->push($request->fd, $_send);
});


/**
 * 当客户端发送数据到服务端的时候
 * $frame->fd 客户端的唯一标示符
 * $frame->data 客户端发送过来的数据 string
 * 可调用$_server->exist($frame->fd) 判断客户端连接是否正常 存活返回 true 死掉返回 false
 * 可调用$_server->push($frame->fd, $_send); $_send 为发送数据
 * 注意当调用 push 方法时若 该 $frame->fd 死掉 服务端会报如下错误 错误信息可通过 set 中的 log_file 记录
 * Warning: Swoole\WebSocket\Server::push(): connection[3] is not a websocket client.
 * 所以在调用push方法时 需调用 exist() 方法
 * 
 */
$server->on('message', function (swoole_websocket_server $_server, $frame) {

    $data = $frame->data;
    echo $data;
    echo "\n";
    foreach($_server->connections as $fd){
        echo "$fd\n";
        $_server->push($fd , $data);//循环广播
    }
    
//     if ($frame->data == "close") {//客户端发送关闭命令关闭该客户端{
//         if($_server->exist($frame->fd)){
//             $_server->close($frame->fd);
//         }
//     }elseif($frame->data == "task") {//客户端发送开启任务命令
//         if($_server->exist($frame->fd)){
//             $_server->task(['go' => 'die']);
//         }
//     }
//     else {//循环广播
// //         $data = $frame->data;
// //         foreach($_server->connections as $fd){
// // //             $_server->push($fd , $data);//循环广播
// //             echo $fd;
// //         }
//     }
});

/**
 * 当客户端关闭与服务端的连接时会触发该事件
 * 客户端 关闭浏览器/当前窗口 均会触发该事件
 * 关闭后 $fd 会从 $_server->connections 中移除
 * 
 */
$server->on('close', function (swoole_websocket_server $_server, $fd) {
    echo "client {$fd} closed\n";
});

$server->on('task', function ($_server, $worker_id, $task_id, $data) {
    var_dump($worker_id, $task_id, $data);
    return "hello world\n";
});

$server->on('finish', function ($_server, $task_id, $result)
{
    echo "finish\n";
    var_dump($task_id, $result);
});

// $server->on('packet', function ($_server, $data, $client) {
//     echo "#".posix_getpid()."\tPacket {$data}\n";
//     var_dump($client);
// });


$server->start();