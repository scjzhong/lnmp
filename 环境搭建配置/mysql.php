1： 安装mysql
	A:	mv mysql-5.7.21-linux-glibc2.12-x86_64.tar.gz /usr/local/
	B:	cd /usr/local/
	C:  tar -zxvf mysql-5.7.21-linux-glibc2.12-x86_64.tar.gz
	D:  创建mysql用户 
		groupadd mysql
		useradd -r -g mysql -s /bin/false mysql
	E:  创建mysql的数据目录，该目录在初始化数据库的时候会用到 
		mkdir /mysql /mysql/data /mysql/log 
	F:	修改目录权限
		chown -R mysql:mysql /usr/local/mysql /mysql
	G:	创建my.cnf文件
		写入如下内容
			[client]
			port = 3306
			socket = /tmp/mysql.sock
			
			[mysqld]
			server_id=10
			port = 3306
			user = mysql
			character-set-server = utf8mb4
			default_storage_engine = innodb
			log_timestamps = SYSTEM
			socket = /tmp/mysql.sock
			basedir = /usr/local/mysql
			datadir = /mysql/data
			pid-file = /mysql/data/mysql.pid
			max_connections = 1000
			max_connect_errors = 1000
			table_open_cache = 1024
			max_allowed_packet = 128M
			open_files_limit = 65535
			#####====================================[innodb]==============================
			innodb_buffer_pool_size = 1024M
			innodb_file_per_table = 1
			innodb_write_io_threads = 4
			innodb_read_io_threads = 4
			innodb_purge_threads = 2
			innodb_flush_log_at_trx_commit = 1
			innodb_log_file_size = 512M
			innodb_log_files_in_group = 2
			innodb_log_buffer_size = 16M
			innodb_max_dirty_pages_pct = 80
			innodb_lock_wait_timeout = 30
			innodb_data_file_path=ibdata1:1024M:autoextend
			
			#####====================================[log]==============================
			log_error = /mysql/log/mysql-error.log 
			slow_query_log = 1
			long_query_time = 1 
			slow_query_log_file = /mysql/log/mysql-slow.log
			
			sql_mode=NO_ENGINE_SUBSTITUTION,STRICT_TRANS_TABLES
	H:	cd /mysql/log/  touch mysql-error.log
	I:  cd usr/local/mysql/bin
	J:	初始化cd 到bin目录 
			./mysqld --initialize --user=mysql --basedir=/usr/local/mysql --datadir=/mysql/data  --innodb_undo_tablespaces=3 --explicit_defaults_for_timestamp
		此时在mysql-error.log 会生成一个临时的密码
	K:  修改目录权限  
		chown -R mysql:mysql /usr/local/mysql /mysql
	L:	切换到bin目录下配置启动文件
		cp support-files/mysql.server /etc/init.d/mysql
		
		chkconfig --add mysql
		chkconfig mysql on
		
		service mysql start
		即可启动
	M:	配置环境变量
		vi /etc/profile
		写入如下代码
		mysql_home=/usr/local/mysql
		PATH=$PATH:$mysql_home/bin
		
		source /etc/profile
		
		执行mysql -u root -h localhost -p
		输入mysql-error.log 里的临时的密码
		就ok了。
		
	N:  登录进去之后 需要重新设置密码
		SET PASSWORD=PASSWORD('nihao123');
		即可。
		
	O:  此时本机可以连接到mysql 但是外网还是连接不到
		简单点
		update user set host='%' where user='root';
		flush privileges;
		尝试外网连接
		注意需要关闭
		iptables -F
		关闭selinux	
		然后就可以外网连接了。 