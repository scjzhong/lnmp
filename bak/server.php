<?php

/**
 * ���췽���в�Ҫʹ��SWOOLE_BASE SWOOLE_BASE 
 * ʹ��Baseģʽ��ҵ�������Reactor��ֱ��ִ�� ���ᵼ���ڹ㲥ʱ�����������޷��㲥�����е� $_server->connections��.
 * �������ڿͻ�����ͻ���֮���ͨѶ �ɲ���BASEģʽ
 * Ĭ��ʹ��SWOOLE_PROCESSģʽ ��ʹ�ý���ģʽ��ҵ�������Worker������ִ��
 */
$server = new \swoole_websocket_server("0.0.0.0", 9502);
$server->set([
    'worker_num' => 4,//����worker������
    'task_worker_num' => 4,//����task���̵����������ô˲����󽫻�����task���ܡ�����swoole_server���Ҫע��onTask/onFinish2���¼��ص����������û��ע�ᣬ�����������޷�������
    'max_request' => 200,//����worker���̵������������Ĭ��Ϊ0��һ��worker�����ڴ����곬������ֵ��������Զ��˳��������˳�����ͷ������ڴ����Դ��Ȼ��master�����ٴ���һ���µ�worker����
    'log_file' => '/var/log/swoole/swoole.log', //ָ��swoole������־�ļ�����swoole�����ڷ������쳣��Ϣ���¼������ļ��С�Ĭ�ϻ��ӡ����Ļ��
//     'ssl_cert_file' => __DIR__.'/config/ssl.crt',
//     'ssl_key_file' => __DIR__.'/config//ssl.key',//ʹ��SSL�����ڱ���swooleʱ����--enable-opensslѡ�� ��վʹ����https ��Ҫ����������
//     'daemonize' => 1,//�����ػ�����ģʽ��(daemonize => true)����׼������ᱻ�ض���log_file
]);



/**
 * �ûص���������Ҫ�Զ�������ʱ����Ҫ���øûص�����Ҫ���øûص��¼�����������handshake�ص��򲻻ᴥ��open�ص��¼���
 */
// $server->on('handshake', 'user_handshake');
/**
 * ���ͻ�����swoole����˷���һ������ʱ �ᴥ��connect�ص��¼� ��ʹ��websocket �����ʱ��ʹ��connect �ص��¼�
 */
// $server->on('connect', function($serv, $fd){
//     var_dump($_SESSION);
//     echo "\n";
//     echo "connect - $fd\n";
// });

/**
 * �����Կͻ��˵�һ����������ʱ,�ᴥ���ûص��¼�
 * $_server->worker_pid  ִ�б������Ӳ����Ķ����id ��ͨ�� ps -auxf | grep server.php �鿴
 * $request->fd �ͻ��������id ���ͻ����ڱ������е�Ψһ��ʶ�� ���û���½��¼��session uid �ɽ����߰󶨡�
 * �ɵ���$_server->push($request->fd, $_send); $_send Ϊ��������
 * �ɵ���getClientInfo() ��ȡ�ͻ�����Ϣ
 * ��Ҫע�⵽���� ���¿�һ�� ��������Ϊ����һ���ͻ���
 * ���� (array) $request->get ��ȡget����  $request->get['uid'] ��ȡָ�������� get����
 */
$server->on('open', function (swoole_websocket_server $_server, swoole_http_request $request) {
    //����redis ��uid ��fd
    try{
        $redis = new \Redis();
        $redis->connect('118.190.22.125',6380);
        $auth = $redis->auth('redis_nihao123###');
        $redis->select(1);
        $name = $redis->get('name');
        echo $name;
    }catch (Exception $e){
        echo "[redis������]:" . $e->getCode() . ',[������Ϣ]:' . $e->getMessage() . '\n';//daemonize=>true ����ض���log_file·����
        $_server->push($request->fd, $e->getMessage());
        $_server->push($request->fd, $e->getCode());
        //����redisʧ��shutdown����
        //$_server->shutdown();
    }
    if(!$auth){//������֤ʧ��
        $jsonData = [
            'status' => 200,
            'msg'    => 'redis ���񲻿��ã�����ϵ����Ա',
        ];
        $_server->push($request->fd, json_encode($jsonData));
    }
    
    $_send = 'success';
    $_server->push($request->fd, $_send);
});


/**
 * ���ͻ��˷������ݵ�����˵�ʱ��
 * $frame->fd �ͻ��˵�Ψһ��ʾ��
 * $frame->data �ͻ��˷��͹��������� string
 * �ɵ���$_server->exist($frame->fd) �жϿͻ��������Ƿ����� ���� true �������� false
 * �ɵ���$_server->push($frame->fd, $_send); $_send Ϊ��������
 * ע�⵱���� push ����ʱ�� �� $frame->fd ���� ����˻ᱨ���´��� ������Ϣ��ͨ�� set �е� log_file ��¼
 * Warning: Swoole\WebSocket\Server::push(): connection[3] is not a websocket client.
 * �����ڵ���push����ʱ ����� exist() ����
 * 
 */
$server->on('message', function (swoole_websocket_server $_server, $frame) {

    $data = $frame->data;
    echo $data;
    echo "\n";
    foreach($_server->connections as $fd){
        echo "$fd\n";
        $_server->push($fd , $data);//ѭ���㲥
    }
    
//     if ($frame->data == "close") {//�ͻ��˷��͹ر�����رոÿͻ���{
//         if($_server->exist($frame->fd)){
//             $_server->close($frame->fd);
//         }
//     }elseif($frame->data == "task") {//�ͻ��˷��Ϳ�����������
//         if($_server->exist($frame->fd)){
//             $_server->task(['go' => 'die']);
//         }
//     }
//     else {//ѭ���㲥
// //         $data = $frame->data;
// //         foreach($_server->connections as $fd){
// // //             $_server->push($fd , $data);//ѭ���㲥
// //             echo $fd;
// //         }
//     }
});

/**
 * ���ͻ��˹ر������˵�����ʱ�ᴥ�����¼�
 * �ͻ��� �ر������/��ǰ���� ���ᴥ�����¼�
 * �رպ� $fd ��� $_server->connections ���Ƴ�
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