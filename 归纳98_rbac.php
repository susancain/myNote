
权限管理RBAC
目录
一、权限管理概述	1
	1、实现方式：	1
	2、实现RBAC建表	2
二、项目布局	3
	1、新建一个虚拟主机，并生成项目程序	3
	2、布局后台首页	4
三、添加权限	4
	1.新建一个权限的控制器，并添加add的方法，并拷贝对应的静态页面，	4
	2.修改添加权限的表单	5
	3.添加权限的模型，进行数据验证	6
	4.完成添加顶级权限	6
	5.添加顶级权限下面的子级权限。	6
四、添加角色	9
	1、新建一个角色的控制器，并添加add方法，并拷贝对应 的静态页面。	9
	2、修改添加角色的表单，并列出权限的内容。	9
	3、完成角色的添加	10
五、添加管理员	11
	1、新建一个管理员的控制器，并添加add方法，并拷贝对应的静态页面。	12
	2、制作添加角色的表单。	13
	3、添加一个管理员的模型，并添加数据验证	14
	4、完成管理员数据的添加	14
	5、入库it_admin_role表，	14
六、根据管理员的权限，生成左侧按钮	15
	1、在权限模型里面，添加一个方法，用于取出登录用户的权限数据	15
	2、在index控制器里面的，left 方法中取出按钮数据	17
	3、在 left.html模板页面中进行遍历数据	18
七、完成一个登录操作	18
八、防止没有权限的用户操作其他的操作。	19


一、权限管理概述
1、实现方式：
实现方式一：直接给管理员授予权限。


实现方式二：基于角色的权限控制

基于角色的访问控制（Role-Based Access Control）作为传统访问控制（自主访问，强制访问）的有前景的代替受到广泛的关注。在RBAC中，权限与角色相关联，用户通过成为适当角色的成员而得到这些角色的权限。这就极大地简化了权限的管理。

2、实现RBAC建表
一个管理员是否属于多个角色呢？可以设置的。
一个角色可以有多个管理员的
一个角色可以分配多个权限。
一个权限可以属于多个角色

有三张主表：管理员表   角色表   权限表
用于表示三张表之间关系的中间表  有两张。
#创建管理员表
create table it_admin(
    id int primary key auto_increment,
    admin_name varchar(32) not null comment '管理员的名称',
    password char(32) not null comment '管理员密码',
    salt  varchar(32) not null comment '密码的密钥'
)engine myisam charset utf8;
#创建完成管理员表，要给其一个超级管理员，
#该超级管理员的明文密码为：admin
insert into it_admin values(null,'admin','00f0e9b9de5d2871ac168e8f3a962c8b','ab34tg');
#创建一个角色表
create table it_role(
id int primary key auto_increment,
role_name varchar(32) not null comment '角色的名称'
)engine myisam charset utf8;
#创建一个管理员与角色的中间表
create table it_admin_role(
admin_id int not null comment '管理员的id',
role_id int not null comment '角色的id'
)engine myisam charset utf8;

#创建一个权限表
create table it_privilege(
id int primary key auto_increment,
priv_name varchar(32) not null comment '权限的名称',
parent_id int not null  default 0 comment '父级权限的id',
module_name  varchar(32) not null default '' comment '操作该权限的模块名称',
controller_name  varchar(32) not null default '' comment '操作该权限的控制器名称',
action_name  varchar(32) not null default '' comment '操作该权限的方法名称'
)engine myisam charset utf8;
#创建角色与权限的中间表
create table it_role_privilege(
role_id int not null comment '角色的id',
priv_id int not null comment '权限的id'
)engine myisam charset utf8;



二、项目布局
1、新建一个虚拟主机，并生成项目程序

2、布局后台首页
App/Common/Conf/config.php
return array(
	//'配置项'=>'配置值'
	/* 数据库设置 */
	'DB_TYPE'               =>  'mysql',     // 数据库类型
	'DB_HOST'               =>  'localhost', // 服务器地址
	'DB_NAME'               =>  'shop',          // 数据库名
	'DB_USER'               =>  'root',      // 用户名
	'DB_PWD'                =>  'root',          // 密码
	'DB_PORT'               =>  '3306',        // 端口
	'DB_PREFIX'             =>  'it_',    // 数据库表前缀
);
三、添加权限
1.新建一个权限的控制器，并添加add的方法，并拷贝对应的静态页面，
<?php
namespace Admin\Controller;
use Think\Controller;
class PrivilegeController extend Contorller
{
	//添加权限的方法
	public function add(){
		$this->display();	
	}
}
?>
2.修改添加权限的表单
<td colspan="2">
	<form action="__ACTION__" method="post">
		<table width="100%" class="cont">
			<tr>
				<td width="2%">&nbsp;</td>
				<td>权限名称:</td>
				<td width="80%">
					<input class="text" type="text" name="priv_name" value=""/>
				</td>
			</tr>
			<tr>
				<td width="2%">&nbsp;</td>
				<td>上级权限:</td>
				<td width="80%">
					<select name="parent_id">
						<option value="0">顶级权限</option>
					</select>
				</td>
			</tr>


		</table>
	</form>
</td>
效果如下：

3.添加权限的模型，进行数据验证
Admin/Model/PrivilegeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class PrivilegeModel extend Model
{
	//添加数据验证
	protected $_validate=array(
		array('priv_name','require','权限名称不能为空'),
	); 
}
?>
4.完成添加顶级权限
Admin/Controller/PrivilegeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class PrivilegeController extend Contorller
{
	//添加权限的方法
	public function add(){
		if(IS_POST){
			$privmodel = D('Privilege');
			if($privmodel->create()){
				if($privmodel->add()){
					$this->success("添加成功",U('Privilege/lst'));
					exit;
				}else{
					$this->error("添加失败");
				}
			}else{
				$this->error($privmodel->getError());
			}
		}
		$this->display();
	}
}
?>

5.添加顶级权限下面的子级权限。
第一步：在权限模型里面添加一个方法，用于取出权限内容（按照无限极分类的方式取出），
Admin/Model/PrivilegeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class PrivilegeModel extend Model
{
	//添加数据验证
	protected $_validate=array(
		array('priv_name','require','权限名称不能为空'),
	); 
	//取出权限的数据
	public function getTree(){
		$arr = $this->select();
		return $this->_getTree($arr,$parent_id=0,$lev=0);
	}
	public function _getTree($arr,$parent_id=0,$lev=0){
		static $list = array();
		foreach($arr as $v){
			if($v['id']==$parent_id){
				$v['lev'] = $lev;
				$list = $v;
				$this->_getTree($arr,$v['id'],$lev+1)
			}
		}
		return $list;
	}
}
?>
第二步：取出权限数据，遍历到添加权限的表单里面。
Admin/View/Prililege/add.html
<select name="parent_id">
	<option value="0">顶级权限</option>
	<?php foreach($privdata as $v){?>
		<option value="<?php echo $v['id']?>"><?php echo str_repeat('--',$v['lev']).$v['priv_name']?></option>
	<?php }?>
</select>

Admin/Controller/PrivilegeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class PrivilegeController extend Contorller
{
	//添加权限的方法
	public function add(){
		if(IS_POST){
			$privmodel = D('Privilege');
			if($privmodel->create()){
				if($privmodel->add()){
					$this->success("添加成功",U('Privilege/lst'));
					exit;
				}else{
					$this->error("添加失败");
				}
			}else{
				$this->error($privmodel->getError());
			}
		}

		//取出权限数据
		$privdata = $privmodel->getTree();
		$this->assign('privdata',$privdata);
		$this->display();
	}
}
?>
四、添加角色
1、新建一个角色的控制器，并添加add方法，并拷贝对应 的静态页面。
Admin/Controller/RoleController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class RoleController extend Contorller
{
	public function add(){
		//取出权限列表
		$privmodel = D('Privilege');
		$privdata = $privmodel->getTree();
		$this->assign('privdata',$privdata);
		$this->display();
	}
}
?>
2、修改添加角色的表单，并列出权限的内容。


3、完成角色的添加
mysql>desc it_role;
mysql>desc it_role_privilege;
it_role.id = it_role_privilege
添加角色的代码：
Admin/Controller/RoleController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class RoleController extend Contorller
{
	public function add(){
		if(IS_POST){
			$rolemodel = D('Role');
			if($rolemodel->create()){
				if($rolemodel->add()){
					$this->success("添加成功",U('Role/lst'));
					exit;
				}else{
					$this->error("添加失败");
				}
			}else{
				$this->error($rolemodel->getError());
			}
		}
		//列出权限列表
		$privmodel = D('Privilege');
		$privdata = $privmodel->getTree();
		$this->assign('privdata',$privdata);
		$this->display();
	}
}	
?>

模型里面添加的钩子函数；
Admin/Model/RoleModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class RoleModel extend Model
{
	//添加数据验证
	protected $_validate = array(
		array('role_name','require','角色名称不能为空')
	);
	protected function _after_insert($data,$options){
		//接收传递的权限的id
		$priv_ids = I('post.priv');
		$role_id = $data['id'];
		foreach($priv_ids as $v){
			$arr = array(
				'role_id'=>$role_id,
				'priv_id'=>$v
			);
			M('RolePrivilege')->add($arr);
		}
	}
}
?>

五、添加管理员
在添加管理员时，给管理员分配角色
1、新建一个管理员的控制器，并添加add方法，并拷贝对应的静态页面。
Admin/Controller/AdminController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extend Contorller
{
	public function add(){
		//取出角色的数据
		$rolomodel = D('Role');
		$roledata = $rolemodel->select();
		$this->assign('roledata',$roledata);
		$this->display();
	}
}
?>

2、制作添加角色的表单。
Admin/View/Role/add.html
<td colspan="2">
	<form action="__ACTION__" method="post">
		<table width="100%" class="cont">
			<tr>
				<td width="2%">&nbsp;</td>
				<td>管理员名称:</td>
				<td width="80%">
					<input class="text" type="text" name="admin_name" value=""/>
				</td>
			</tr>
			<tr>
				<td width="2%">&nbsp;</td>
				<td>上级权限:</td>
				<td width="80%">
					<select name="role_id">
						<option value="0">选择角色...</option>
						<?php foreach($roledata as $v){?>
							<option value="<?php echo $v['id']?>"><?php echo $v['role_name']?></option>
						<?php }?>
					</select>
				</td>
			</tr>


		</table>
	</form>
</td>


3、添加一个管理员的模型，并添加数据验证
Admin/Model/AdminModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extend Model
{
	protected $_validate=array(
		array('admin_name','require','管理员名称不能为空'),
	);
}
?>
4、完成管理员数据的添加
Admin/Controller/AdminController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extend Contorller
{
	public function add(){
		if(IS_POST){
			$adminmodel = D('Admin');
			if($adminmodel->create()){
				$salt = rand(100000,999999);
				$pw = I('post.password');//接收的明文密码
				$adminmodel->salt = $salt;
				$adminmodel->password = md5(md5($pw).$salt);
				if($adminmodel->add()){
					$this->success("添加成功",U('Admin/lst'));
					exit;
				}else{
					$this->error("添加失败");
				}
			}else{
				$this->error($adminmodel->getError());
			}
		}
	}
}
?>
5、入库it_admin_role表，
在admin模型里面添加一个钩子函数。_after_insert()
Admin/Model/AdminModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extend Model
{
	protected $validate = array(
		array('admin_name','require','管理员名称不能为空'),
	);
	protected function _after_insert($data,$options){
		//接收传递的角色id
		$role_id = I('post.role_id');
		$admin_id = $data['id'];
		M("AdminRole")->add(array(
			'admin_id'=>$admin_id,
			'role_id'=>$role_id
		));
	}
}	
?>

六、根据管理员的权限，生成左侧按钮
1、在权限模型里面，添加一个方法，用于取出登录用户的权限数据
思路：根据当前登录用户，找到用户所属的角色，在根据角色，取出权限数据。

Admin/Model/PrivilegeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class PrivilegeModel extend Model
{
	//添加数据验证
	protected $_validate=array(
		array('priv_name','require','权限名称不能为空'),
	); 
	//取出权限的数据
	public function getTree(){
		$arr = $this->select();
		return $this->_getTree($arr,$parent_id=0,$lev=0);
	}
	public function _getTree($arr,$parent_id=0,$lev=0){
		static $list = array();
		foreach($arr as $v){
			if($v['id']==$parent_id){
				$v['lev'] = $lev;
				$list = $v;
				$this->_getTree($arr,$v['id'],$lev+1)
			}
		}
		return $list;
	}

	//根据登录用户,取出当前用户的权限
	public function getMenus(){
		$admin_id = 3;//$_SESSION['admin_id'];
		if($admin_id==1){
			//等于1就是超级管理员,则取出所有的权限
			//(1)先取出顶级权限
			$sql = "select * from it_privilege where parent_id=0";
			$privlist = $list->query($sql);
			$privdata = array();
			//(2)要取出顶级权限下面对应子权限按钮
			foreach($privlist as $k=>$v){
				$sql = "select * from it_privilege where parent_id=".$v['id'];
				$v['child'] = $this->query($sql);
				$privdata[] = $v;
			}
		}else{
			//非超级管理员的权限
			//(1)取出普通管理员的顶级权限
			$sql = "select c.* from it_admin_role a left join it_role_privilege b on a.role_id=b.role_id left join it_privilege c on b.priv_id=c.id where a.admin_id=".$admin_id.'and c.parent_id=0';
			$privlist = $this->query($sql);
			$privdata = array();
			//(2)要取出普通管理员顶级权限下面对应子权限按钮
			foreach($privlist as $k=>$v){
				$sql = "select c.* from it_admin_role a left join it_role_privilege b on a.role_id=b.role_id left join it_privilege c on b.priv=c.id where a.admin_id=".$admin_id.'and c.parent_id='.$v['id'];
			}
		}
		return $privdata;
	}
}
?>






2、在index控制器里面的，left 方法中取出按钮数据
Admin/Controller/IndexController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class indexController extend Contorller
{
	public function index(){
		$this->display();
	}
	public function left(){
		$privmodel = D('Privilege');
		$data = $privmodel->getMenus();
		$this->assign('data',$data);
		$this->display();
	}
}
?>
3、在 left.html模板页面中进行遍历数据
<?php foreach($data as $v){?>
	<?php echo $v['priv_name']?>
	<?php foreach($v['child'] as $v1){?>
		<?php echo U($v1['module_name'].'/'.$v1['controller_name'].'/'.$v1['action_name'])?>
		<?php echo $v1['priv_name']?>
	<?php }?>
<?php }?>

七、完成一个登录操作

1、在管理员的控制器里面，添加一个login的方法，并拷到对应的静态页面。
Admin/Controller/AdminController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extend Contorller
{
	public function add(){
		if(IS_POST){
			$adminmodel = D('Admin');
			if($adminmodel->create()){
				$salt = rand(100000,999999);
				$pw = I('post.password');//接收的明文密码
				$adminmodel->salt = $salt;
				$adminmodel->password = md5(md5($pw).$salt);
				if($adminmodel->add()){
					$this->success("添加成功",U('Admin/lst'));
					exit;
				}else{
					$this->error("添加失败");
				}
			}else{
				$this->error($adminmodel->getError());
			}
		}
	}
	public function login(){
		$this->display();
	}
}
?>
2、修改登录的表单

3、在admin模型里面添加一个登录的方法
Admin/Model/AdminModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extend Model
{
	protected $validate = array(
		array('admin_name','require','管理员名称不能为空'),
	);
	protected function _after_insert($data,$options){
		//接收传递的角色id
		$role_id = I('post.role_id');
		$admin_id = $data['id'];
		M("AdminRole")->add(array(
			'admin_id'=>$admin_id,
			'role_id'=>$role_id
		));
	}
	//登录的方法
	public function login(){
		$admin_name = I('post.admin_name');
		$password = I('post.password');
		//思路:根据管理员的名称查找出密码,和输入的密码进行匹配
		$info = $this->where("admin_name='$admin_name'")->find();
		if($info){
			if(md5(md5($password).$info['salt'])==$info['password']){
				//登录成功,把用户信息存储到session里面
				$_SESSION['admin_id'] = $info['id'];
				$_SESSION['admin_name'] = $admin_name;
				return true;
			}
		}
		return false;
	}
}	
?>
	


4、在login方法中开始登录验证
Admin/Controller/AdminController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extend Contorller
{
	public function add(){
		if(IS_POST){
			$adminmodel = D('Admin');
			if($adminmodel->create()){
				$salt = rand(100000,999999);
				$pw = I('post.password');//接收的明文密码
				$adminmodel->salt = $salt;
				$adminmodel->password = md5(md5($pw).$salt);
				if($adminmodel->add()){
					$this->success("添加成功",U('Admin/lst'));
					exit;
				}else{
					$this->error("添加失败");
				}
			}else{
				$this->error($adminmodel->getError());
			}
		}
	}
	public function login(){
		if(IS_POST){
			$adminmodel = D('Admin');
			if($adminmodel->login()){
				$this->success("登录成功",U('Index/index'));
				exit;
			}else{
				$this->error("登录失败");
			}
		}
		$this->display();
	}
}
?>

八、防止没有权限的用户操作其他的操作。

思路：在操作某个权限的时候，要进行验证，当前用户是否有这个权限。
如何验证：要取出当前操作的模块名称，控制器名称，方法名称
可以使用常量来获取：MODULE_NAME  CONTROLLER_NAME   ACTON_NAM E

把当前用户的权限对应的模块名、控制器名称、方法名称，取出来，与当前的操作进行匹配。

第一步：取出当前登录用户的权限。
select c.*  from it_admin_role  a left join it_role_privilege b on a.role_id=b.role_id left join it_privilege c on b.priv_id=c.id where a.admin_id=5;".$admin_id' 

mysql>select c.* from it_admin_role a left join it_role_privilege b on a.role_id-b.role_id left join it_privilege c on b.priv_id=c.id where a.admin_id=5;

把当前登录用户的操作的模块与控制器与方法名拼接成一个字符串
$url = MODULE_NAME.'-'.CONTROLLER_NAME.'-'.ACTION_NAME;
下面sql语句是列出登录用户的权限，

mysql>select concat(c.module_name,'-',c.controller_name,'-',c.action_name) url from it_admin_role a left join it_role_privilege b on a.role_id=b.role_id left join it_privilege c on b.priv_id=c.id where a.admin_id=5;

Adminn/Controller/MyController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class MyController extend Contorller
{
	public function _initialize(){
		$admin_id = $_SESSION['admin_id'];
		if($admin_id==1){
			//是超级管理员,则直接返回true
			return true;
		}elseif($admin_id>1){
			//普通管理员
			//拼接当前操作的模块名与控制器名与方法名
			$url = MODULE_NAME.'-'.CONTROLLER_NAME.'-'.ACTION_NAME;
			$sql = "select concat(c.module_name,'-',c.controller_name,'-',c.action_name) url from it_admin_role left join it_role_privilege b on a.role_id=b.role_id left join it_privilege c on b.priv_id=c.id where a.admin.id=$admin_id having url='$url'";
			$info = M()->queru($sql);
			if($info){
				//有权限
				return true;
			}else{
				//没有权限
				$this->error('无权操作');
			}
		}else{
			//没有登录,则转到登录页面
			$this->redirect('Admin/Admin/login');
		}
	}
}
?>





