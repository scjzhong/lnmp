1：查找文件中的指定字符串
    grep -n "qwer" index.log
    会查找出包含该字符的那行
    
    统计文件有多少行
    cat index.log | wc -l
  统计文本中字符串出现的行数
    grep "@" index.log | wc -l
    
2：增加用户
        添加scjzhong用户，此时该用户是没有密码的
        useradd scjzhong 
        passwd scjzhong
        输入两次密码即可
    
3：删除用户
    userdel scjzhong 用户目录依然存在。
    userdel -r scjzhong 用户目录就不会存在了 彻底删除
4：用户提升权限

    useradd scjzhong创建scjzhong用户
    su scjzhong
    在scjzhong用户下执行安装
    yum install ab-tools报如下的提示没有权限
            已加载插件：fastestmirror
            您需要 root 权限执行此命令。
        如何提升权限
        在root账号下
        vi /etc/sudoers
        进入编辑模式找到如下
        root    ALL=(ALL)       ALL
        新增一行
        scjzhong    ALL=(ALL)       ALL
        强制保存退出
        :wq!  
        查看/home/目录
        
        drwx------.  2 scjzhong scjzhong  62 2月   4 14:07 scjzhong
    scjzhong用户组是scjzhong
        此时scjzhong用户此时依然无法拥有root 的权限
        执行
        usermod -g root scjzhong
        修改scjzhong用户属于root用户组
        
        然后scjzhong用户登录
        执行
        su-
                输入密码
        抛出 
            密码：
      su: 鉴定故障
      
      执行 sudo -su  
      此时即可获取root 的权限
      
   注意：线上服务器时使用有普通账号拥有特殊权限
   
5：上传文件 （基于ssh协议）
    scp a.php root@118.190.22.125:/tmp/tmp/file1/
        输入密码
    root@118.190.22.125's password: 
    a.php               100%   24     1.0KB/s   00:00    
        上传成功
    [root@localhost file]# 
    
       下载远程文件到本地
    scp root@118.190.22.125:/tmp/tmp/file1/b.php ./
    root@118.190.22.125's password: 
    b.php              100%   17     0.5KB/s   00:00    
    [root@localhost file]# 
    
    
    
    
    
    基于windows 下的上传下载
    yum install lrzsz
    
    使用rz 和sz 即可完成文件的上传和下载    
    
    
   