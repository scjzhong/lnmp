192.168.175.129 代理服务器
192.168.175.130 web服务器
192.168.175.131 web服务器

192.168.175.129 的nginx.conf配置如下

user  www www;

worker_processes auto;

error_log  /home/wwwlogs/nginx_error.log  crit;

pid        /usr/local/nginx/logs/nginx.pid;

#Specifies the value for maximum file descriptors that can be opened by this process.
worker_rlimit_nofile 51200;

events
    {
        use epoll;
        worker_connections 51200;
        multi_accept on;
    }

http
    {
        include       mime.types;
        default_type  application/octet-stream;

        server_names_hash_bucket_size 128;
        client_header_buffer_size 32k;
        large_client_header_buffers 4 32k;
        client_max_body_size 50m;

        sendfile   on;
        tcp_nopush on;

        keepalive_timeout 60;

        tcp_nodelay on;

        fastcgi_connect_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_read_timeout 300;
        fastcgi_buffer_size 64k;
        fastcgi_buffers 4 64k;
        fastcgi_busy_buffers_size 128k;
        fastcgi_temp_file_write_size 256k;

        gzip on;
        gzip_min_length  1k;
        gzip_buffers     4 16k;
        gzip_http_version 1.1;
        gzip_comp_level 2;
        gzip_types     text/plain application/javascript application/x-javascript text/javascript text/css application/xml application/xml+rss;
        gzip_vary on;
        gzip_proxied   expired no-cache no-store private auth;
        gzip_disable   "MSIE [1-6]\.";

        #limit_conn_zone $binary_remote_addr zone=perip:10m;
        ##If enable limit_conn_zone,add "limit_conn perip 10;" to server section.

        server_tokens off;
        access_log off;

    include vhost/*.conf;
}

在 vhost下有一个 lnmp1.conf配置如下
    
    upstream hosts
    {
        server 192.168.175.130:80 weight=1;#weight权重
        server 192.168.175.131:80 weight=1;
    }

server
    {
        listen 80 default_server;
        #listen [::]:80 default_server ipv6only=on;
        server_name _;
        index index.html index.htm index.php;
        root  /home/wwwroot/default;

        location / {
            proxy_pass   http://hosts;
        }

        #error_page   404   /404.html;

        # Deny access to PHP files in specific directory
        #location ~ /(wp-content|uploads|wp-includes|images)/.*\.php$ { deny all; }

#        include enable-php.conf;

        location /nginx_status
        {
            stub_status on;
            access_log   off;
        }

        location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
        {
            expires      30d;
        }

        location ~ .*\.(js|css)?$
        {
            expires      12h;
        }

        location ~ /.well-known {
            allow all;
        }

        location ~ /\.
        {
            deny all;
        }

        access_log  /home/wwwlogs/access.log;
    }
    

    
    192.168.175.130 web服务器
    192.168.175.131 web服务器 两台web服务器的配置保持变
    
    这样不管静态还是动态这时候都能解析到后面的web服务器