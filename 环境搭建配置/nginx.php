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
	