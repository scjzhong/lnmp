使用私钥登录可以避免密码被其他人直接知道。

1. 制作密钥对

命令:
   cd ~

   ssh-keygen
      提示enter键即可 
   cd .ssh
   [root@iZ28f3h9ot7Z .ssh]# ls
   id_rsa  id_rsa.pub //一对公私密钥
   
2. 在服务器上安装公钥
     cd .ssh
    [root@iZ28f3h9ot7Z .ssh]# cat id_rsa.pub >> authorized_keys
    [root@iZ28f3h9ot7Z .ssh]# ls
    authorized_keys  id_rsa  id_rsa.pub
    [root@iZ28f3h9ot7Z .ssh]# 
    
    
    
3. 设置 SSH，打开密钥登录功能
    
    cd /etc/ssh/
    [root@iZ28f3h9ot7Z ssh]# vi sshd_config
    
    //找到如下的地方
    
    #RSAAuthentication yes
    #PubkeyAuthentication yes
        去掉注释
    systemctl  restart sshd
        重启服务
        
        将私钥下载到本地登录时选择私钥文件登录即可
        
        
        注意：
                   为了确保连接成功，请保证以下文件权限正确：
        [root@host .ssh]$ chmod 600 authorized_keys
        [root@host .ssh]$ chmod 700 ~/.ssh
        
4：想禁止该私钥登录 可将本次生成的文件均删除
    删除id_rsa.pub  私钥依然可以登录需要移除或删除  authorized_keys（将安装过的公钥删除）
    
    
    
    
    
    
    