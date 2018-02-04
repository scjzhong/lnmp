1����װnginx
	�������� nginx
	��ѹ cd �� Դ��Ŀ¼
	
	# Ϊ��֧��rewrite���ܣ�������Ҫ��װpcre
	yum install pcre-devel
	
	# ��Ҫssl��֧�֣��������Ҫssl֧�֣���������һ��
	# yum install openssl*
	
	# gzip ��ⰲװ�����谲װ
	# yum install zlib zlib-devel
	
	./configure --prefix=/usr/local/nginx --with-http_stub_status_module  --with-http_ssl_module --with-http_realip_module --with-http_sub_module --with-http_gzip_static_module --with-pcre
	
	���밲װnginx	
	make && make install
	
	���������ӣ�
	ln -sf /usr/local/nginx/sbin/nginx /usr/sbin 
	
	���nginx:
	nginx -t
	��ʾ�� nginx: configuration file /usr/local/nginx/conf/nginx.conf test is successful

2���������

	����α��̬
		��server��
		    location / {
		        #����α��̬
		        rewrite ^(.*)\.htmlp|jsp$ /index.html;
		        index  index.php index.html index.htm;
		    }
		    
		    rewrite ^(.*)\.htmlp|jsp$ /index.html;
		    ƥ�䵽��.htmlp ����.jsp ��β���ļ��Զ����� /index.html
	
	�Զ�����־��ʽ	    
	http������
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                      '$status $body_bytes_sent "$http_referer" '
                      '"$http_user_agent" "$http_x_forwarded_for"';

    access_log  logs/access.log  main;
		    
	��server��Ҳ�������� ��־�ļ�
	   access_log  logs/access_test.log  main;
	   ����server ����־�ǵ�access_test.log�� ��ʱ�򲻻Ὣ�ռǼ�¼��access.log�С�
	   
	   
3���������
    �����Ǹ�����
    upstream ali{
    	server 118.190.22.125:8080;
    }
    
    server {
        listen       8080;
    
        location / {
            root   /home/wwwroot/test/;
            #�������������Ҫ������ 
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
    	fastcgi_split_path_info ^(.+\.php)(.*)$;     #������һ��
         	fastcgi_param PATH_INFO $fastcgi_path_info;    #������һ��
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
        
        
        ����������ؾ��⣩
     upstream hosts{
    	server 118.190.22.125:8080;
    	server 118.190.22.125:8080;
    }
    
    server {
        listen       8080;
    
        location / {
            root   /home/wwwroot/test/;
            #�������������Ҫ������ 
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
    	fastcgi_split_path_info ^(.+\.php)(.*)$;     #������һ��
         	fastcgi_param PATH_INFO $fastcgi_path_info;    #������һ��
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
    
    
    
    ������� ���ؾ���
    
    upstream hosts{
    	server 118.190.22.125:8080 weight=2;#weightȨ��
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
    	    fastcgi_split_path_info ^(.+\.php)(.*)$;     #������һ��
         	fastcgi_param PATH_INFO $fastcgi_path_info;    #������һ��
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            include        fastcgi_params;
        }
    }
           