ʹ��˽Կ��¼���Ա������뱻������ֱ��֪����

1. ������Կ��

����:
   cd ~

   ssh-keygen
      ��ʾenter������ 
   cd .ssh
   [root@iZ28f3h9ot7Z .ssh]# ls
   id_rsa  id_rsa.pub //һ�Թ�˽��Կ
   
2. �ڷ������ϰ�װ��Կ
     cd .ssh
    [root@iZ28f3h9ot7Z .ssh]# cat id_rsa.pub >> authorized_keys
    [root@iZ28f3h9ot7Z .ssh]# ls
    authorized_keys  id_rsa  id_rsa.pub
    [root@iZ28f3h9ot7Z .ssh]# 
    
    
    
3. ���� SSH������Կ��¼����
    
    cd /etc/ssh/
    [root@iZ28f3h9ot7Z ssh]# vi sshd_config
    
    //�ҵ����µĵط�
    
    #RSAAuthentication yes
    #PubkeyAuthentication yes
        ȥ��ע��
    systemctl  restart sshd
        ��������
        
        ��˽Կ���ص����ص�¼ʱѡ��˽Կ�ļ���¼����
        
        
        ע�⣺
                   Ϊ��ȷ�����ӳɹ����뱣֤�����ļ�Ȩ����ȷ��
        [root@host .ssh]$ chmod 600 authorized_keys
        [root@host .ssh]$ chmod 700 ~/.ssh
        
4�����ֹ��˽Կ��¼ �ɽ��������ɵ��ļ���ɾ��
    ɾ��id_rsa.pub  ˽Կ��Ȼ���Ե�¼��Ҫ�Ƴ���ɾ��  authorized_keys������װ���Ĺ�Կɾ����
    
    
    
    
    
    
    