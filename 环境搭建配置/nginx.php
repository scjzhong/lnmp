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
	