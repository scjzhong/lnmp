1�������ļ��е�ָ���ַ���
    grep -n "qwer" index.log
    ����ҳ��������ַ�������
    
    ͳ���ļ��ж�����
    cat index.log | wc -l
  ͳ���ı����ַ������ֵ�����
    grep "@" index.log | wc -l
    
2�������û�
        ���scjzhong�û�����ʱ���û���û�������
        useradd scjzhong 
        passwd scjzhong
        �����������뼴��
    
3��ɾ���û�
    userdel scjzhong �û�Ŀ¼��Ȼ���ڡ�
    userdel -r scjzhong �û�Ŀ¼�Ͳ�������� ����ɾ��
4���û�����Ȩ��

    useradd scjzhong����scjzhong�û�
    su scjzhong
    ��scjzhong�û���ִ�а�װ
    yum install ab-tools�����µ���ʾû��Ȩ��
            �Ѽ��ز����fastestmirror
            ����Ҫ root Ȩ��ִ�д����
        �������Ȩ��
        ��root�˺���
        vi /etc/sudoers
        ����༭ģʽ�ҵ�����
        root    ALL=(ALL)       ALL
        ����һ��
        scjzhong    ALL=(ALL)       ALL
        ǿ�Ʊ����˳�
        :wq!  
        �鿴/home/Ŀ¼
        
        drwx------.  2 scjzhong scjzhong  62 2��   4 14:07 scjzhong
    scjzhong�û�����scjzhong
        ��ʱscjzhong�û���ʱ��Ȼ�޷�ӵ��root ��Ȩ��
        ִ��
        usermod -g root scjzhong
        �޸�scjzhong�û�����root�û���
        
        Ȼ��scjzhong�û���¼
        ִ��
        su-
                ��������
        �׳� 
            ���룺
      su: ��������
      
      ִ�� sudo -su  
      ��ʱ���ɻ�ȡroot ��Ȩ��
      
   ע�⣺���Ϸ�����ʱʹ������ͨ�˺�ӵ������Ȩ��
   
5���ϴ��ļ� ������sshЭ�飩
    scp a.php root@118.190.22.125:/tmp/tmp/file1/
        ��������
    root@118.190.22.125's password: 
    a.php               100%   24     1.0KB/s   00:00    
        �ϴ��ɹ�
    [root@localhost file]# 
    
       ����Զ���ļ�������
    scp root@118.190.22.125:/tmp/tmp/file1/b.php ./
    root@118.190.22.125's password: 
    b.php              100%   17     0.5KB/s   00:00    
    [root@localhost file]# 
    
    
    
    
    
    ����windows �µ��ϴ�����
    yum install lrzsz
    
    ʹ��rz ��sz ��������ļ����ϴ�������    
    
    
   