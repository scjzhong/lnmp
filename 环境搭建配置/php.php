1����װphp 
	�������ؾ��� ��ѹ
	A:	�Ȱ�װ ����
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
		�������¿Ⲣ��װ
		tar zxvf /libmcrypt-2.5.8.tar.gz \
		&& cd /libmcrypt-2.5.8 && ./configure && make && make install && cd - / && rm -rf /libmcrypt* \
		&& tar zxvf /mhash-0.9.9.9.tar.gz && cd mhash-0.9.9.9 && ./configure && make && make install && cd - / && rm -rf /mhash* \
		&& tar zxvf /mcrypt-2.6.8.tar.gz && cd mcrypt-2.6.8 && LD_LIBRARY_PATH=/usr/local/lib ./configure && make && make install && cd - / && rm -rf /mcrypt*
	C:	cd �� ��ѹĿ¼
		./configure --prefix=/usr/local/php --with-config-file-scan-dir=/usr/local/php/etc/ --enable-inline-optimization --enable-opcache --enable-session --enable-fpm --with-mysql=mysqlnd --with-mysqli=mysqlnd --with-pdo-mysql=mysqlnd --with-pdo-sqlite --with-sqlite3 --with-gettext --enable-mbregex --enable-mbstring --enable-xml --with-iconv --with-mcrypt --with-mhash --with-openssl --enable-bcmath --enable-soap --with-xmlrpc --with-libxml-dir --enable-pcntl --enable-shmop --enable-sysvmsg --enable-sysvsem --enable-sysvshm --enable-sockets --with-curl --with-curlwrappers --with-zlib --enable-zip --with-bz2 --with-gd --enable-gd-native-ttf --with-jpeg-dir --with-png-dir --with-freetype-dir --with-iconv-dir --with-readline
	D:	make && make install  ��������е����
	E:	�����ļ�
		��Ҫ�Ӱ�װ���︴��php.ini��php-fpm.conf����װĿ¼��
		cd ����ѹĿ¼
		cp ./php.ini* /usr/local/php/etc/
		cd /usr/local/php/etc/
		cp php.ini-production php.ini
		cp php-fpm.conf.default  php-fpm.conf
		cp php-fpm.d/www.conf.default php-fpm.d/www.conf
	
	F:  ����php.ini
		# ����ʾ����Ĭ��
		display_errors = Off
		
		# �ڹر�display_errors����PHP������־��·����php-fpm.conf�����ã���Ĭ��
		log_errors = On
		
		# �ַ�����Ĭ��
		default_charset = "UTF-8"
		
		# �ļ��ϴ���С��Ĭ�� 
		upload_max_filesize = 2M
		
		# ����PHP����չ��·��,��Ĭ�ϱ�ע���ˡ�
		extension_dir = "/usr/local/php7/lib/php/extensions/no-debug-non-zts-20151012/"
		# ���������extension_dir��Ҳ����ֱ��д����λ�ã�
		# extension=/usr/local/php/lib/php/extensions/no-debug-non-zts-20151012/redis.so
		
		
		# ����PHP��ʱ��
		date.timezone = PRC
		
		# ����opcache��Ĭ����0
		[opcache]
		; Determines if Zend OPCache is enabled
		opcache.enable=1
	
	G:	����php-fpm.conf
			; ȥ����ֺţ������Ժ������������޸�
			; Default Value: none
			; �����ֵ����Ŀ¼��/usr/local/php/var/run/php-fpm.pid
			; ���������ƽ������php-fpm
			pid = run/php-fpm.pid
			
			; ���ô�����־��·��������Ĭ��ֵ
			; Note: the default prefix is /usr/local/php/var
			; Default Value: log/php-fpm.log, ��/usr/local/php/var/log/php-fpm.log
			error_log = /var/log/php-fpm/error.log
			
			; Log�ȼ�������Ĭ��ֵ
			; Possible Values: alert, error, warning, notice, debug
			; Default Value: notice
			log_level = notice
			
			; ��̨���У�Ĭ��yes������Ĭ��ֵ
			; Default Value: yes
			;daemonize = yes
			
			; ����www.conf�ļ��е����ã�����Ĭ��ֵ
			include=/usr/local/php/etc/php-fpm.d/*.conf
	H:	����www.conf����php-fpm.dĿ¼�£�
		
		; �����û����û��飬Ĭ�϶���nobody������Ĭ��ֵ
		user = nginx
		group = nginx
		
		; ����PHP����
		; ������Ĭ��ֵ��������ʹ�á�����Ĭ��ֵ
		; listen = 127.0.0.1:9000
		; ����nginx.conf�е�����fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
		listen = /var/run/php-fpm/php-fpm.sock
		
		######��������־������Ĭ��ֵ
		slowlog = /var/log/php-fpm/$pool-slow.log
		request_slowlog_timeout = 10s
		
		���������ļ��󣬼��������Ƿ���ȷ�ķ���Ϊ:
		/usr/local/php/sbin/php-fpm -t
		����������� test is successful ������˵������û�����⡣���������Ҳ����������֪��php-fpm�������ļ����ġ�
		
		������ [23-Jan-2018 22:09:13] ERROR: failed to open error_log (/var/log/php-fpm/error.log): No such file or directory (2)
		�������ļ�����
		
	
	I:  ���������ӣ�
		ln -sf /usr/local/php/sbin/php-fpm /usr/bin/
		ln -sf /usr/local/php/bin/php /usr/bin/
		ln -sf /usr/local/php/bin/phpize /usr/bin/
		ln -sf /usr/local/php/bin/php-config /usr/bin/
		ln -sf /usr/local/php/bin/php-cig /usr/bin/
			
	
	J:	����php-fpm
		 /usr/local/php/sbin/php-fpm 
		 ���ܻᱨ�� 
		[23-Jan-2018 22:13:41] ERROR: [pool www] cannot get uid for user 'nginx'
		[23-Jan-2018 22:13:41] ERROR: FPM initialization failed
		
		ִ��useradd nginx
		
		 ���ܻᱨ�� 
		[23-Jan-2018 22:14:40] ERROR: unable to bind listening socket for address '/var/run/php-fpm/php-fpm.sock': No such file or directory (2)
		[23-Jan-2018 22:14:40] ERROR: FPM initialization failed
		
		ִ��touch /var/run/php-fpm/php-fpm.sock
		
		Ȼ�������ɹ�
		
		php-fpm�������ܣ�

		/usr/local/php/sbin/php-fpm 		# php-fpm����
		kill -INT `cat /usr/local/php/var/run/php-fpm.pid` 		# php-fpm�ر�
		kill -USR2 `cat /usr/local/php/var/run/php-fpm.pid` 		#php-fpmƽ������

		 
		 ��������502  ע���Ƚ�user group ��ΪĬ�� nobody   ��������Ȩ�����⣩
		
		/usr/local/php/etc/php-fpm.d
		php-fpm.conf 
		 
		listen.owner = nobody
		listen.group = nobody
		 
		listen = 127.0.0.1:9000	