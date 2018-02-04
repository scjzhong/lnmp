1：安装php 
	官网下载镜像 解压
	A:	先安装 依赖
		yum install -y gcc gcc-c++ make cmake bison autoconf wget lrzsz
		yum install -y libtool libtool-ltdl-devel 
		yum install -y freetype-devel libjpeg.x86_64 libjpeg-devel libpng-devel gd-devel
		yum install -y python-devel  patch  sudo 
		yum install -y openssl* openssl openssl-devel ncurses-devel
		yum install -y bzip* bzip2 unzip zlib-devel
		yum install -y libevent*
		yum install -y libxml* libxml2-devel
		yum install -y libcurl* curl-devel 
		yum install -y readline-devel
		yum install -y wget
	B:	
		下载以下库并安装
		tar zxvf /libmcrypt-2.5.8.tar.gz \
		&& cd /libmcrypt-2.5.8 && ./configure && make && make install && cd - / && rm -rf /libmcrypt* \
		&& tar zxvf /mhash-0.9.9.9.tar.gz && cd mhash-0.9.9.9 && ./configure && make && make install && cd - / && rm -rf /mhash* \
		&& tar zxvf /mcrypt-2.6.8.tar.gz && cd mcrypt-2.6.8 && LD_LIBRARY_PATH=/usr/local/lib ./configure && make && make install && cd - / && rm -rf /mcrypt*
	C:	cd 到 解压目录
		./configure --prefix=/usr/local/php --with-config-file-scan-dir=/usr/local/php/etc/ --enable-inline-optimization --enable-opcache --enable-session --enable-fpm --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-pdo-sqlite --with-sqlite3 --with-gettext --enable-mbregex --enable-mbstring --enable-xml --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-xmlrpc --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-sockets --with-curl --with-curlwrappers --with-zlib --enable-zip --with-bz2 --with-gd --enable-gd-native-ttf --with-jpeg-dir --with-png-dir --with-freetype-dir --with-iconv-dir --with-readline
	D:	make && make install  这个过程有点儿慢
	E:	配置文件
		需要从安装包里复制php.ini、php-fpm.conf到安装目录：
		cd 到解压目录
		cp ./php.ini* /usr/local/php/etc/
		cd /usr/local/php/etc/
		cp php.ini-production php.ini
		cp php-fpm.conf.default  php-fpm.conf
		cp php-fpm.d/www.conf.default php-fpm.d/www.conf
	
	F:  配置php.ini
		# 不显示错误，默认
		display_errors = Off
		
		# 在关闭display_errors后开启PHP错误日志（路径在php-fpm.conf中配置），默认
		log_errors = On
		
		# 字符集，默认
		default_charset = "UTF-8"
		
		# 文件上传大小，默认 
		upload_max_filesize = 2M
		
		# 设置PHP的扩展库路径,，默认被注释了。
		extension_dir = "/usr/local/php7/lib/php/extensions/no-debug-non-zts-20151012/"
		# 如果不设置extension_dir，也可以直接写绝对位置：
		# extension=/usr/local/php/lib/php/extensions/no-debug-non-zts-20151012/redis.so
		
		
		# 设置PHP的时区
		date.timezone = PRC
		
		# 开启opcache，默认是0
		[opcache]
		; Determines if Zend OPCache is enabled
		opcache.enable=1
	
	G:	配置php-fpm.conf
			; 去掉里分号，方便以后重启。建议修改
			; Default Value: none
			; 下面的值最终目录是/usr/local/php/var/run/php-fpm.pid
			; 开启后可以平滑重启php-fpm
			pid = run/php-fpm.pid
			
			; 设置错误日志的路径，可以默认值
			; Note: the default prefix is /usr/local/php/var
			; Default Value: log/php-fpm.log, 即/usr/local/php/var/log/php-fpm.log
			error_log = /var/log/php-fpm/error.log
			
			; Log等级，可以默认值
			; Possible Values: alert, error, warning, notice, debug
			; Default Value: notice
			log_level = notice
			
			; 后台运行，默认yes，可以默认值
			; Default Value: yes
			;daemonize = yes
			
			; 引入www.conf文件中的配置，可以默认值
			include=/usr/local/php/etc/php-fpm.d/*.conf
	H:	配置www.conf（在php-fpm.d目录下）
		
		; 设置用户和用户组，默认都是nobody。可以默认值
		user = nginx
		group = nginx
		
		; 设置PHP监听
		; 下面是默认值，不建议使用。可以默认值
		; listen = 127.0.0.1:9000
		; 根据nginx.conf中的配置fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
		listen = /var/run/php-fpm/php-fpm.sock
		
		######开启慢日志。可以默认值
		slowlog = /var/log/php-fpm/$pool-slow.log
		request_slowlog_timeout = 10s
		
		保存配置文件后，检验配置是否正确的方法为:
		/usr/local/php/sbin/php-fpm -t
		如果出现诸如 test is successful 字样，说明配置没有问题。另外该命令也可以让我们知道php-fpm的配置文件在哪。
		
		如若报 [23-Jan-2018 22:09:13] ERROR: failed to open error_log (/var/log/php-fpm/error.log): No such file or directory (2)
		创建改文件即可
		
	
	I:  建立软连接：
		ln -sf /usr/local/php/sbin/php-fpm /usr/bin/
		ln -sf /usr/local/php/bin/php /usr/bin/
		ln -sf /usr/local/php/bin/phpize /usr/bin/
		ln -sf /usr/local/php/bin/php-config /usr/bin/
		ln -sf /usr/local/php/bin/php-cig /usr/bin/
			
	
	J:	启动php-fpm
		 /usr/local/php/sbin/php-fpm 
		 可能会报错 
		[23-Jan-2018 22:13:41] ERROR: [pool www] cannot get uid for user 'nginx'
		[23-Jan-2018 22:13:41] ERROR: FPM initialization failed
		
		执行useradd nginx
		
		 可能会报错 
		[23-Jan-2018 22:14:40] ERROR: unable to bind listening socket for address '/var/run/php-fpm/php-fpm.sock': No such file or directory (2)
		[23-Jan-2018 22:14:40] ERROR: FPM initialization failed
		
		执行touch /var/run/php-fpm/php-fpm.sock
		
		然后启动成功
		
		php-fpm操作汇总：

		/usr/local/php/sbin/php-fpm 		# php-fpm启动
		kill -INT `cat /usr/local/php/var/run/php-fpm.pid` 		# php-fpm关闭
		kill -USR2 `cat /usr/local/php/var/run/php-fpm.pid` 		#php-fpm平滑重启

		 
		 如若出现502  注意先将user group 改为默认 nobody   （可能是权限问题）
		
		/usr/local/php/etc/php-fpm.d
		php-fpm.conf 
		 
		listen.owner = nobody
		listen.group = nobody
		 
		listen = 127.0.0.1:9000	