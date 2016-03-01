
电商网站（四）
目录
一、完成 栏目页面	1
	1、显示出栏目页面	1
	2、取出所属当前栏目的商品	2
二、完成用户的注册功能	3
	1、创建一个会员表	3
	2、把注册的页面给显示出来。	4
	（1）新建一个user控制器，并添加 register方法，并拷贝对应的静态页面。	4
	（2）修改对应静态页面的表单。	5
	3、新建一个用户的模型，并完成数据验证	5
	4、验证通过后，完成注册，	7
三、完成用户的登录	8
	1、显示出登录页面	8
	2、完成登录	9
	（1）在登录时，添加一个数据验证，	9
	（2）在控制器里面完成登录的数据验证。	11
	（3）在模型里面添加一个方法，用于验证输入用户名和密码是否正确	11
	（4）在user控制器里面完成登录的验证。	12
	3、在head头部，显示出登录的用户名，	12
	4、在user控制器里面添加一个退出的方法；	13
四、完成内容详情页面	13
	1、内容详情页面的显示	13


一、完成 栏目页面
1、显示出栏目页面
（1）在前台的index控制器里面添加一个方法，category，并拷贝对应的静态页面，
Home/Controller/IndexController.class
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	//创建跨模块的模型对象,如何创建.D("模块名称/模型名称");
    	$catemodel = D("Admin/Category");
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);  

    	//取出栏目的信息
    	$catedata = $catemodel->getTree();
    	$this->assign('catedata',$catedata);
    	
    	//取出精品数据
    	$goodsmodel = D('Admin/Goods');
    	$this->bestdata = $bestdata = $goodsmodel->getByGoods('is_best',3);		   	
    	$this->newdata = $newdata = $goodsmodel->getByGoods('is_new',3);
    	$this->hotdata = $goodsmodel->getByGoods('is_hot',3);
    	$this->display();
    }
    //栏目页面方法
    public function category(){
    	//取出头部导航信息
    	$catemodel = D('Admin/Category');
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);
    	//取出左侧栏目的信息
    	$catedata = $catemodel->getTree();
    	$this->assign('catedata',$catedata);
    	$this->display();
    }
?>
（2）把头部信息替换成head.html页面。
Home/View/Index/category.html
<include file="Public/head"/>
2、取出所属当前栏目的商品

思路：如果单击是顶级栏目，则要取出顶级栏目的子孙栏目的商品。

（1）取出所属栏目的商品
Home/Controller/IndexController.class
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
    	//创建跨模块的模型对象,如何创建.D("模块名称/模型名称");
    	$catemodel = D("Admin/Category");
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);  

    	//取出栏目的信息
    	$catedata = $catemodel->getTree();
    	$this->assign('catedata',$catedata);
    	
    	//取出精品数据
    	$goodsmodel = D('Admin/Goods');
    	$this->bestdata = $bestdata = $goodsmodel->getByGoods('is_best',3);		   	
    	$this->newdata = $newdata = $goodsmodel->getByGoods('is_new',3);
    	$this->hotdata = $goodsmodel->getByGoods('is_hot',3);
    	$this->display();
    }
    //栏目页面方法
    public function category(){
    	//取出头部导航信息
    	$catemodel = D('Admin/Category');
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);
    	//取出左侧栏目的信息
    	$catedata = $catemodel->getTree();
    	$this->assign('catedata',$catedata);

    	//接收传递的栏目的id
    	$cat_id = $_GET['cat_id']+0;
    	//要查找出当前栏目的子孙栏目的id
    	$ids = $catemodel->getChild($cat_id);//返回子孙栏目的id
    	if(empty($ids)){
    		//说明当前栏目没有子孙栏目
    		$ids[]=$cat_id;
    	}
    	$goodsmodel = D('Admin/Goods');
    	//把一个一维数组,转成一个字符串
    	$id = implode(',', $ids);//拼接子孙栏目的id
    	$goodsdata = $goodsmodel->where("cat_id in ($id)")->select();//去商品表找出子孙栏目的商品数据
    	$this->assign('goodsdata',$goodsdata);

    	$this->display();
    }
?>


（2）把该商品遍历到静态页面
Home/View/Index/category.html
<?php foreach($goodsdata as $v){?>
	<a href="__CONTROLLER__/detail/goods_id/<?php echo $v['id']?>"><img src="<?php echo C('UPLOAD_ROOT').$v['goods_thumb']?>" alt=""></a><br/>
	<a href="__CONTROLLER__/detail/goods_id/<?php echo $v['id']?>"><?php echo $v['goods_name']?></a>
	<?php echo $v['market_price']?>
	<?php echo $v['shop_price']?>
<?php }?>
（3）如果没有所属当前栏目的商品，则跳转到首页。
Home/Controller/IndexController.class

$id = implode(',', $ids);//拼接子孙栏目的id
$goodsdata = $goodsmodel->where("cat_id in ($id)")->select();//去商品表找出子孙栏目的商品数据
if(empty($goodsdata)){
	header("location:/index.php");
}
$this->assign('goodsdata',$goodsdata);

$this->display();

二、完成用户的注册功能
1、创建一个会员表
create table it_user(
    id int  primary key auto_increment,
    username varchar(32) not null comment '用户的名称',
    password char(32) not null comment '用户的密码',
    salt   char(6) not null comment '密码的密钥',
    email  varchar(32) not null comment '注册用户的邮箱'
)engine myisam charset utf8;
salt生成密码的一种方式：
注意：最终的生成的密码：md5(md5(明文的密码).salt)

2、把注册的页面给显示出来。
（1）新建一个user控制器，并添加 register方法，并拷贝对应的静态页面。
Home/Controller/UserController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class UserController extend Contorller
{
	//用户注册的方法
	public function register(){
		//取出头部导航信息
    	$catemodel = D('Admin/Category');
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);
    	$this->display();
	}
}
?>
（2）修改对应静态页面的表单。
<form action="__ACTION__">
	<input type="text" name="username">
</form>
效果：

3、新建一个用户的模型，并完成数据验证
验证规则如下：
用户名称不能为空
用户名称中不能包含，一些特殊字符，比如@ # .
用户名称必须是唯一的。
用户的密码必须大于6位小于12位。
要验证两次输入的密码是否一致。
验证邮箱格式是否正确，
验证的代码：
Home/Model/UserModel.class.php
<?php
namespace Home\Model;
use Think\Model;
class UserModel extend Model
{
	//添加数据验证
	protected $_validate = array(
		array('username','require','用户名称不能为空'),
			//callback指定要使用当前模型里面的一个方法,验证规则中要指定使用的方法名
		array('username','checkname','用户名称中包含特殊符号',1,'callback'),
			//验证用户名称是否唯一
		array('username','','用户名称已经存在',1,'unique'),
			//验证密码的长度要在6到12位之间
		array('password','6,12','密码要在6到12位之间',1,'length'),
			//验证确认密码是否和输入的密码一致
		array('rpassword','password','两次密码输入不一致',1,'confirm'),
			//验证邮箱格式是否正确
		array('email','email','邮箱格式不正确'),
			
	);
	//验证用户名称包含非法字符的方法
	protected function checkname(){
		//接收提交的用户名称
		$username = I('post.username');
		//判断用户名称中是否包含非法字符,@#.
		if(strpos($username,'@')!==false || strpos($username,'#')!==false || strpos($username,'.')!==false){
			//已经包含非法字符了
			return false;
		}
		return true;
	}
}
?>

4、验证通过后，完成注册，
Home/Controller/UserController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class UserController extend Contorller
{
	//用户注册的方法
	public function register(){
		if(IS_POST){
			$usermodel = D('User');
			if($usermodel->create()){
				//通过验证,要生成密码
				$salt = substr(uniqid(),-6);
				$pwd = I('post.password');//接收传递的明文密码
				$usermodel->password = md5(md5($pwd).$salt);
				$usermodel->salt = $salt;
				if($usermodel->add()){
					//注册成功
					$this->success('注册完成',U("Index/index"));
					exit;
				}else{
					//注册失败
					$this->error('注册失败');
				}
			}else{
				$this->error($usermodel->getError());
			}
		}

		//取出头部导航信息
    	$catemodel = D('Admin/Category');
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);
    	$this->display();
	}
}
?>




三、完成用户的登录
1、显示出登录页面
（1）在user控制器里面，添加一个login的方法，并拷贝对应的静态页面
Home/Controller/UserController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class UserController extend Contorller
{
	//用户注册的方法
	public function register(){
		if(IS_POST){
			$usermodel = D('User');
			if($usermodel->create()){
				//通过验证,要生成密码
				$salt = substr(uniqid(),-6);
				$pwd = I('post.password');//接收传递的明文密码
				$usermodel->password = md5(md5($pwd).$salt);
				$usermodel->salt = $salt;
				if($usermodel->add()){
					//注册成功
					$this->success('注册完成',U("Index/index"));
					exit;
				}else{
					//注册失败
					$this->error('注册失败');
				}
			}else{
				$this->error($usermodel->getError());
			}
		}
		
		//取出头部导航信息
    	$catemodel = D('Admin/Category');
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);
    	$this->display();
	}

	//添加用户的登录
	public function login(){
		//取出头部导航信息
		$catemodel = D('Admin/Category');
		$navdata = $catemodel->getNav();
		$this->assign('navdata',$navdata);
		$this->display();
	}
}
?>
（2）在user控制器里面，添加一个生成验证码的方法
Home/Controller/UserController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class UserController extend Contorller
{
	//用户注册的方法
	public function register(){
		if(IS_POST){
			$usermodel = D('User');
			if($usermodel->create()){
				//通过验证,要生成密码
				$salt = substr(uniqid(),-6);
				$pwd = I('post.password');//接收传递的明文密码
				$usermodel->password = md5(md5($pwd).$salt);
				$usermodel->salt = $salt;
				if($usermodel->add()){
					//注册成功
					$this->success('注册完成',U("Index/index"));
					exit;
				}else{
					//注册失败
					$this->error('注册失败');
				}
			}else{
				$this->error($usermodel->getError());
			}
		}
		
		//取出头部导航信息
    	$catemodel = D('Admin/Category');
    	$navdata = $catemodel->getNav();
    	$this->assign('navdata',$navdata);
    	$this->display();
	}

	//添加用户的登录
	public function login(){
		//取出头部导航信息
		$catemodel = D('Admin/Category');
		$navdata = $catemodel->getNav();
		$this->assign('navdata',$navdata);
		$this->display();
	}

	//生成一个验证码的方法
	public function authcode(){
		$Verify = new \Think\Verify();
		$Verify->fontSize= 20;
		$Verify->length = 4;
		$Verify->useNoise = false;
		$Verify->entry(); 
	}
}
?>

（3）修改登录的表单，添加验证码。
Home/View/User/login.html
<img src="__CONTROLLER__/authcode" onclick="this.src='__CONTROLLER__/authcode/'+Math.random()" style="cursor:pointer" alt="">


2、完成登录
（1）在登录时，添加一个数据验证，
要求：
登录的用户名不能为空：
登录的密码不能为空，
登录的验证码不能为空，
登录的验证码要正确。

我们使用动态方式来完成登录的验证。
在user模型里面，定义动态的验证规则。

Home/Model/UserModel.class.php

//定义动态的验证规则
public $_validate_login = array(
	array('username','require','用户名称不能为空'),
	array('password','require','密码不能为空'),
	array('authcode','require','验证码不能为空'),
	//验证验证码是否输入正确
	array('authcode','checkcode','验证码输入错误',1,'callback'),
);
//验证验证码是否正确的一个方法	$code为用户输入的验证码字符串
protected function checkcode($code,$id=''){
	$verify = new \Think\Verify();
	return $verify->check($code,$id);
}

（2）在控制器里面完成登录的数据验证。
Home/Controller/UserController.class.php

//添加用户的登录
public function login(){
	if(IS_POST){
		//完成登录的操作
		$usermodel = D('User');
		if($usermodel->validate($usermodel->_validate_login)->create()){
			echo 'ok';exit;
		}
		//$usermodel->getError()是获取模型里面的error属性的内容
		$this->error($usermodel->getError());
	}
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	$this->display();
}
（3）在模型里面添加一个方法，用于验证输入用户名和密码是否正确
Home/Controller/UserController.class.php

//验证输入的用户名和密码是否正确
public function login(){
	//接收输入的用户名和密码
	$username = I('post.username');
	$password = I('post.password');
	//思路:根据用户名获取密码,和输入的密码进行比较
	//取出用户信息
	$where['username'] = array('eq',$username);//相当"username='$username'",还有防止SQL注入功能
	$info = $this->field("id,password,salt")->where($where)->find();//返回的是一个一维数组
	if($info){
		//验证用户输入的密码是否正确
		if(md5(md5($password).$info['salt'])==$info['password']){
			//说明密码正确,把用户名和用户的id存储到session里面
			$_SESSION['user_id']=$info['id'];
			$_SESSION['username']=$username;
			return true;
		}
	}
	$this->error='该用户名或密码输入错误';
	return false;
}

（4）在user控制器里面完成登录的验证。
Home/Controller/UserController.class.php

//添加用户的登录
public function login(){
	if(IS_POST){
		//完成登录的操作
		$usermodel = D('User');
		if($usermodel->validate($usermodel->_validate_login)->create()){
			if($usermodel->login()){
				//用户和密码正确,登录成功
				$this->success("登录成功",U("Index/index"));exit;
			}
		}
		//$usermodel->getError()是获取模型里面的error属性的内容
		$this->error($usermodel->getError());
	}
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	$this->display();
}

3、在head头部，显示出登录的用户名，
Home/View/Public/head.html

<?php if(!empty($_SESSION['user_id'])){?>
	欢迎光临本店&nbsp; <?php echo $_SESSION['username']?>
	<a href="<?php echo U('User/logout')?>">退出</a>
<?php }else{?>
	<a href="<?php echo U('User/login')?>">登录</a>
	<a href="<?php echo U('User/register')?>">注册</a>
<?php }?>

4、在user控制器里面添加一个退出的方法；
Home/Controller/UserController.class.php

//退出的方法
public function logout(){
	$_SESSION['user_id'] = null;
	$_SESSION['username'] = null;
	$this->success('退出成功',U('Index/index'));
	//或$this->redirect('退出成功',U('Index/index'));
}

四、完成内容详情页面
1、内容详情页面的显示
（1）在index控制器里面添加一个detail方法，并拷贝对应的静态页面。
Home/Controller/IndexController.class.php

//商品详情页面
public function detail(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	$this->display();
}
（2）取出对应的商品
Home/Controller/IndexController.class.php

public function detail(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);

	//接收传递商品的id
	$goods_id = (int)$_GET['goods_id'];
	//取出商品的详情信息
	$goodsmodel = D('Admin/Goods');
	$goodsinfo = $goodsmodel->where("id=$goods_id")->find();
	//判断是否取出商品的数据
	if(empty($goodsinfo)){
		header("location:/index.php");
	}
	$this->assign('goodsinfo',$goodsinfo);

	$this->display();
}

（3）把取出的商品数据，给遍历到静态页面。


（4）取出商品的单选属性的数据


属性信息是在哪个表里面存储? 答it_goods_attr表存储属性的值， it_attribute表存储属性的名称。
要取出当前商品的属性的名称以及属性的值？如何取出。
先要弄明白两张表的关系。





通过对html代码的分析。需要使用两个循环，外层循环需要循环的是单选属性的个数，内存循环需要循环的是单选属性的值的个数。

思路：需要把属性数据的二维数组，转换成三维数组，便于遍历属性。


转换成三维数组后：


取出属性的代码：
Home/Controller/IndexController.class.php

//商品详情页面
public function detail(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	
	//接收传递商品的id
	$goods_id = (int)$_GET['goods_id'];
	//取出商品的详情信息
	$goodsmodel = D('Admin/Goods');
	$goodsinfo = $goodsmodel->where("id=$goods_id")->find();
	//判断是否取出商品的数据
	if(empty($goodsinfo)){
		header("location:/index.php");
	}
	$this->assign('goodsinfo',$goodsinfo);
	
	//取出某商品的属性信息
	$sql = "select a.*,b.attr_name,b.attr_type from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=$goods_id";
	$attrdata = M()->query($sql);//返回的是二维数组;execute($sql)执行增删改的SQL语句;query($sql)执行有返回结果的SQL语句
	//M()返回的是空模型,就是基础模型的实例
	$radiodata = array();//定义一个空数组,用于存储单选属性的数据
	foreach($attrdata as $v){
		if($v['attr_type']==1){
			//是单选属性
			$radiodata[$v['attr_id']][] = $v;
		}
	}		
	$this->assign('radiodata',$radiodata);

	$this->display();
}
完成属性遍历的代码：
Home/View/Index/detail.html

<?php foreach($radiodata as $v){?>
	<?php echo $v[0]['attr_name']?>
	<?php foreach($v as $k=>$v1){?>
		<input type="radio" name="<?php echo 'attr-'.$v['attr_id']?>" <?php echo if(k==0){echo 'checked="checked"';}?>/>
		<?php echo $v1['attr_value']?>
	<?php }?>
<?php }?>
