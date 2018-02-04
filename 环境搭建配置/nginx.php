1：安装nginx
	官网下载 nginx
	解压 cd 到 源码目录
	
	# 为了支持rewrite功能，我们需要安装pcre
	yum install pcre-devel
	
	# 需要ssl的支持，如果不需要ssl支持，请跳过这一步
	# yum install openssl*
	
	# gzip 类库安装，按需安装
	# yum install zlib zlib-devel
	
	./configure --prefix=/usr/local/nginx --with-http_stub_status_module  --with-http_ssl_module --with-http_realip_module --with-http_sub_module --with-http_gzip_static_module --with-pcre
	
	编译安装nginx	
	make && make install
	
	设置软连接：
	ln -sf /usr/local/nginx/sbin/nginx /usr/sbin 
	
	检测nginx:
	nginx -t
	显示： nginx: configuration file /usr/local/nginx/conf/nginx.conf test is successful

2：相关配置

	配置伪静态
		在server中
		    location / {
		        #配置伪静态
		        rewrite ^(.*)\.htmlp|jsp$ /index.html;
		        index  index.php index.html index.htm;
		    }
		    
		    rewrite ^(.*)\.htmlp|jsp$ /index.html;
		    匹配到以.htmlp 或者.jsp 结尾的文件自动定向到 /index.html
	
	自定义日志格式	    
	http中配置
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  logs/access.log  main;
		    
	在server中也可以配置 日志文件
	   access_log  logs/access_test.log  main;
	   将该server 的日志记到access_test.log中 此时则不会将日记记录到access.log中。
	   
	   
3：反向代理
    下面是个例子
    upstream ali{
    	server 118.190.22.125:8080;
    }
    
    server {
        listen       8080;
    
        location / {
            root   /home/wwwroot/test/;
            #如果是域名则需要再配置 
            #proxy_set_header Host www.baidu.com
    	    proxy_pass   http://118.190.22.125:8080;
            index  index.php index.html index.htm;
        }
    
        error_page   500 502 503 504  /50x.html;
        
        location = /50x.html {
            root   html;
        }
    
        location ~ \.php{
            root           /home/wwwroot/test;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
    	fastcgi_split_path_info ^(.+\.php)(.*)$;     #增加这一句
         	fastcgi_param PATH_INFO $fastcgi_path_info;    #增加这一句
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
        
        
        正向代理（负载均衡）
     upstream hosts{
    	server 118.190.22.125:8080;
    	server 118.190.22.125:8080;
    }
    
    server {
        listen       8080;
    
        location / {
            root   /home/wwwroot/test/;
            #如果是域名则需要再配置 
            #proxy_set_header Host www.baidu.com
    	    proxy_pass   http://hosts;
            index  index.php index.html index.htm;
        }
    
        error_page   500 502 503 504  /50x.html;
        
        location = /50x.html {
            root   html;
        }
    
        location ~ \.php{
            root           /home/wwwroot/test;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
    	fastcgi_split_path_info ^(.+\.php)(.*)$;     #增加这一句
         	fastcgi_param PATH_INFO $fastcgi_path_info;    #增加这一句
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
    
    
    
    反向代理 负载均衡
    
    upstream hosts{
    	server 118.190.22.125:8080 weight=2;#weight权重
    	server 118.190.22.125:8081 weight=1; 
    	server 118.190.22.125:8083 weight=1;
    }
    
    
    server {
        listen       8080;
    
        location / {
            root   /home/wwwroot/test/;
    	    proxy_pass   http://hosts;
            index  index.php index.html index.htm;
        }
        error_page   500 502 503 504  /50x.html;
        
        location = /50x.html {
            root   html;
        }
        
        location ~ \.php{
            root           /home/wwwroot/test;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
    	    fastcgi_split_path_info ^(.+\.php)(.*)$;     #增加这一句
         	fastcgi_param PATH_INFO $fastcgi_path_info;    #增加这一句
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
           