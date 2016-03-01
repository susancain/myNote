session主要实现方式
session_set_save_handler(open,close,read,write,destroy,gc);
function open(){
	//mysql连接语句
}
function close(){
	//mysql_close();
}
function read($sess_id){
	//读操作
	$expire = time() - ini_get('session.gc_maxlifetime');
	$sql = "select * from xxx where sess_id = '$sess_id'";
}
function write($sess_id,$sess_data){
	//写操作
	$time = time();
	$sql = "replace into xxx values('$sess_id','sess_data',$time)";
}
function destroy($sess_id){
	//销毁指定的session资源
	$sql = "delete from xxx where sess_id = '$sess_id'";
}
function gc(){
	//垃圾回收机制,默认1/1000
	//清理session过期数据
	$expire = time() - ini_get(session.gc_maxlifetime);
	$sql = "delete from xxx where expire < $expire";
}

//整合session入库到项目中
Session.class.php放入core
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
	//定义模型控制表
	protected $table = 'session';
	
	//继承父类中的__construct()构造器,链接数据库
	public function __construct(){
		//实现父类构造函数,链接数据库
		parent::__construct();
		//更改session存储机制
		session_set_save_handler(
			array($this,'open'),
			array($this,'close'),
			array($this,'read'),
			array($this,'write'),
			array($this,'destroy'),
			array($this,'gc')
		);
		//开启session
		session_start();
	}
	
	//定义open方法
	public function open(){
		return true;
	}
	
	//定义close方法
	public function close(){
		return true;
	}
	
	//定义read方法,实现session入库操作
	public function read($s_id){
		//定义最大过期时间
		$expire = time() - ini_get('session.gc_maxlifetime');
		//组装sql语句
		$sql = "select s_data from {$this->getTableName()} where s_id='$s_id' and s_expire>=$expire";
		//执行
		//echo $sql;exit;
		$row = $this->db_getOne($sql);
		//var_dump($row);exit;
		if($row){
			return $row['s_data'];
		}
		return '';
	}
	
	//定义write方法,实现session入库操作
	public function write($s_id,$s_data){
		//获取当前时间戳
		$time = time();
		//组织sql语句
		$sql = "replace into {$this->getTableName()} values('$s_id','$s_data',$time)";
		//执行
		//var_dump($sql);exit;
		return $this->db_insert($sql);
	}
	
	//定义destroy方法,实现清除指定session
	public function destroy($s_id){
		//组织sql语句
		$sql = "delete from {$this->getTableName()} where s_id='$s_id'";
		//执行
		return $this->db_exec($sql);
	}
	
	//定义gc方法,实现垃圾回收机制
	public function gc(){
		//获取最大的过期时间
		$expire = time() - ini_get('session_maxlifetime');
		//组装sql语句
		$sql = "delete from {$this->getTableName()} where s_expire < $expire";
		//执行
		return $this->db_exec($sql);
	}		
}


session.php
<?php
//session入库六步走(open/close/read/write/destroy/gc)
/**
 * 实现open方法,主要负责连接数据库
 **/
function open(){
	//连接数据库
	mysql_connect('localhost','root','root');
	mysql_query('use db_20150922');
	mysql_query('set names utf8');
}
/**
 *实现close方法,主要负责关闭数据库连接 
 **/
function close(){
	//关闭数据库连接
	mysql_close();
}
/**
 *实现read方法,主要负责读取指定session_id的session数据 
 *param1 string $sess_id,读取操作时,系统会自动将当前会话的session_id作为参数传递给$sess_id  
 */
function read($sess_id){
	//获取最大的过期时间
	$expire = time()-ini_get('session.gc_maxlifetime');
	//组装sql语句
	$sql = "select sess_data from session where sess_id = 'sess_id' and sess_expire >= $expire";
	//执行sql语句
	$res = mysql_query($sql);
	//读取sess_data数据
	if($row = mysql_fetch_assoc($res)){
		return $row['sess_data'];
	}
	return '';
}
/**
 * 实现write方法,主要负责写入数据到数据库中
 * param1 string $sess_id,写入操作时,系统会将当前会话的session_id作为参数传递给$sess_id
 * param2 string $sess_data写入操作时,系统会将当前会话的session数据作为参数传递给$sess_data
 */
function write($sess_id,$sess_data){
	//定义生成session文件的系统时间
	$time = time();
	//组装sql语句
	$sql = "replace into session values('$sess_id','$sess_data',$time)";
	//执行sql语句
	return mysql_query($sql);
}
/**
 * 实现destroy方法,主要负责删除指定的session_id数据
 * param1 string $sess_id,删除操作时,系统会将会话的session_id传递给sess_id
 * */
function destroy($sess_id){
	//定义sql语句
	$sql = "delete from session where sess_id = '$sess_id'";
	//执行sql语句
	return mysql_query($sql);
}
/**
 * 实现gc方法,主要负责删除过期的session数据
 */
function gc(){
	//定义过期时间
	$expire = time() - ini_get('session.gc_maxlifetime');
	//定义sql语句
	$sql = "delete from session where sess_expire < $expire";
	//执行sql语句
	return mysql_query($sql);
}

//更改session存储机制
session_set_save_handler('open','close','read','write','destroy','gc');
//开启session
session_start();
//写入数据
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