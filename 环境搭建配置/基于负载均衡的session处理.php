1：首先配置
    session.save_handler = redis
    session.save_path = "tcp://127.0.0.1:6380?auth=qwer1234&persistent=1&database=10"
    注意 配置 session.save_path 时 密码千万不能带有### 字符 不然会报 
    NOAUTH  相关错误。
    重启php-fpm
    php代码如下
    <?php

    session_start();
    
    $_SESSION['name_lnmp2'] = 'lnmp2';
    
    var_dump($_SESSION);
    
    默认的session 的生存时间是 1440秒
        