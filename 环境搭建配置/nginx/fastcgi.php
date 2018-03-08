问题
    如何自定义配置fastcgi_param
    
    fastcgi_param  SERVER_NAME        $server_name;
    
    fastcgi_param  ENV                'prod';
    
    如配置地定义的 ENV 环境变量 vi fastcgi.conf 写入
     fastcgi_param  ENV                'prod';
    reload nginx即可
    
    
    代码中
    
<?php

var_dump($_SERVER);

$env = filter_input(INPUT_SERVER, 'ENV');
echo "<br/>";
echo $env;

#$_SERVER 全局变量包含 ENV
#基于以上配置 $env 的值为 'prod'
    