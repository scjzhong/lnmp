����ǽ���
    ���»���centos 7
    systemctl start firewalld
        �鿴����ǽ�İ汾
    firewall-cmd --version 
        �鿴����״̬
    firewall-cmd --stat
        ��ȡ���е������������
    firewall-cmd --list-all-zone
    ��ѯĳ�������Ƿ����
    firewall-cmd --query-service=ssh
    
    
  �鿴�˿��翪������ǽ��
    http://118.10.22.125:8080/ ��������ǽ��ö˿ڲ��ܷ���
    ��ʱ��Ӹö˿�
    firewall-cmd --add-port=8080/tcp
    ��Ӻ󼴿ɷ���
    
    ɾ��
    firewall-cmd --remove-port=8080/tcp
    8080�˿�ɾ��������ö˿ڼ��޷�������
    
    �鿴�����ķ���
    firewall-cmd --list-service
    �鿴�����Ķ˿�
    firewall-cmd --list-port
    ����3306�˿�
    firewall-cmd --add-port=3306/tcp
    
    ����6379�˿�
    firewall-cmd --add-port=6379/tcp
    