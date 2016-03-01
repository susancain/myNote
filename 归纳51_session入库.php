session��Ҫʵ�ַ�ʽ
session_set_save_handler(open,close,read,write,destroy,gc);
function open(){
	//mysql�������
}
function close(){
	//mysql_close();
}
function read($sess_id){
	//������
	$expire = time() - ini_get('session.gc_maxlifetime');
	$sql = "select * from xxx where sess_id = '$sess_id'";
}
function write($sess_id,$sess_data){
	//д����
	$time = time();
	$sql = "replace into xxx values('$sess_id','sess_data',$time)";
}
function destroy($sess_id){
	//����ָ����session��Դ
	$sql = "delete from xxx where sess_id = '$sess_id'";
}
function gc(){
	//�������ջ���,Ĭ��1/1000
	//����session��������
	$expire = time() - ini_get(session.gc_maxlifetime);
	$sql = "delete from xxx where expire < $expire";
}

//����session��⵽��Ŀ��
Session.class.php����core
class Session extends Model{
	public function __construct(){
		parent::__construct();
		session_set_save_handler();
		session_start();
	}
}



Session.class.php
<?php
class Session extends Model{
	//����ģ�Ϳ��Ʊ�
	protected $table = 'session';
	
	//�̳и����е�__construct()������,�������ݿ�
	public function __construct(){
		//ʵ�ָ��๹�캯��,�������ݿ�
		parent::__construct();
		//����session�洢����
		session_set_save_handler(
			array($this,'open'),
			array($this,'close'),
			array($this,'read'),
			array($this,'write'),
			array($this,'destroy'),
			array($this,'gc')
		);
		//����session
		session_start();
	}
	
	//����open����
	public function open(){
		return true;
	}
	
	//����close����
	public function close(){
		return true;
	}
	
	//����read����,ʵ��session������
	public function read($s_id){
		//����������ʱ��
		$expire = time() - ini_get('session.gc_maxlifetime');
		//��װsql���
		$sql = "select s_data from {$this->getTableName()} where s_id='$s_id' and s_expire>=$expire";
		//ִ��
		//echo $sql;exit;
		$row = $this->db_getOne($sql);
		//var_dump($row);exit;
		if($row){
			return $row['s_data'];
		}
		return '';
	}
	
	//����write����,ʵ��session������
	public function write($s_id,$s_data){
		//��ȡ��ǰʱ���
		$time = time();
		//��֯sql���
		$sql = "replace into {$this->getTableName()} values('$s_id','$s_data',$time)";
		//ִ��
		//var_dump($sql);exit;
		return $this->db_insert($sql);
	}
	
	//����destroy����,ʵ�����ָ��session
	public function destroy($s_id){
		//��֯sql���
		$sql = "delete from {$this->getTableName()} where s_id='$s_id'";
		//ִ��
		return $this->db_exec($sql);
	}
	
	//����gc����,ʵ���������ջ���
	public function gc(){
		//��ȡ���Ĺ���ʱ��
		$expire = time() - ini_get('session_maxlifetime');
		//��װsql���
		$sql = "delete from {$this->getTableName()} where s_expire < $expire";
		//ִ��
		return $this->db_exec($sql);
	}		
}


session.php
<?php
//session���������(open/close/read/write/destroy/gc)
/**
 * ʵ��open����,��Ҫ�����������ݿ�
 **/
function open(){
	//�������ݿ�
	mysql_connect('localhost','root','root');
	mysql_query('use db_20150922');
	mysql_query('set names utf8');
}
/**
 *ʵ��close����,��Ҫ����ر����ݿ����� 
 **/
function close(){
	//�ر����ݿ�����
	mysql_close();
}
/**
 *ʵ��read����,��Ҫ�����ȡָ��session_id��session���� 
 *param1 string $sess_id,��ȡ����ʱ,ϵͳ���Զ�����ǰ�Ự��session_id��Ϊ�������ݸ�$sess_id  
 */
function read($sess_id){
	//��ȡ���Ĺ���ʱ��
	$expire = time()-ini_get('session.gc_maxlifetime');
	//��װsql���
	$sql = "select sess_data from session where sess_id = 'sess_id' and sess_expire >= $expire";
	//ִ��sql���
	$res = mysql_query($sql);
	//��ȡsess_data����
	if($row = mysql_fetch_assoc($res)){
		return $row['sess_data'];
	}
	return '';
}
/**
 * ʵ��write����,��Ҫ����д�����ݵ����ݿ���
 * param1 string $sess_id,д�����ʱ,ϵͳ�Ὣ��ǰ�Ự��session_id��Ϊ�������ݸ�$sess_id
 * param2 string $sess_dataд�����ʱ,ϵͳ�Ὣ��ǰ�Ự��session������Ϊ�������ݸ�$sess_data
 */
function write($sess_id,$sess_data){
	//��������session�ļ���ϵͳʱ��
	$time = time();
	//��װsql���
	$sql = "replace into session values('$sess_id','$sess_data',$time)";
	//ִ��sql���
	return mysql_query($sql);
}
/**
 * ʵ��destroy����,��Ҫ����ɾ��ָ����session_id����
 * param1 string $sess_id,ɾ������ʱ,ϵͳ�Ὣ�Ự��session_id���ݸ�sess_id
 * */
function destroy($sess_id){
	//����sql���
	$sql = "delete from session where sess_id = '$sess_id'";
	//ִ��sql���
	return mysql_query($sql);
}
/**
 * ʵ��gc����,��Ҫ����ɾ�����ڵ�session����
 */
function gc(){
	//�������ʱ��
	$expire = time() - ini_get('session.gc_maxlifetime');
	//����sql���
	$sql = "delete from session where sess_expire < $expire";
	//ִ��sql���
	return mysql_query($sql);
}

//����session�洢����
session_set_save_handler('open','close','read','write','destroy','gc');
//����session
session_start();
//д������
$_SESSION['adminuser'] = 'admin';
echo $_SESSION['adminuser'];

/*
select * from session\G
*/
/*
create database db_20150922;
use db_20150922;
create table sh_session(
		s_id = char(32) not null,
		s_data text,
		s_expire int,
		unique key(s_id)
)charset=utf8;
*/
?>