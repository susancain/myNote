
电商网站（一）
目录
一、项目布局	1
	1、新建一个虚拟主机	1
	2、程序架构	1
	3、布局后台首页	2
	4、在配置文件中，添加数据库的配置，	4
二、商品类型管理	4
	1、创建商品类型的表。	4
	2、添加商品类型，	5
	（1）新建一个商品类型的控制器，在里面添加add方法，并拷贝对应的静态页面，并修改静态页面里面的样式和图片的路径。	5
	（2）修改add.html里面表单。	6
	（3）新建一个商品类型的模型，在里面添加数据验证，	6
	（4）在控制器里面，add 方法中，完成数据的添加。	7
	（5）注意事项：	7
	3、类型列表	9
三、商品类型属性管理	10
	1、建立属性表，用于存储商品类型的属性。	10
	（1）属性的类型，有唯一属性，有单选属性，复选属性	10
	（2）属性值录入方式:	11
	（3）建立属性表	12
	2、添加商品的属性。	13
	（1）在后台模块下面新建一个属性的控制器(AttributeController)，并在里面添加add方法，并拷贝对应的静态页面，并修改静态页面中的样式和图片的路径。	13
	（2）修改静态页面里面提交的表单。	13
	（3）新建一个商品属性的模型，并添加数据验证，	14
	（4）在控制器里面的add 方法中完成数据入库。	15
	（5）在添加属性表单里面，完成 如下功能	15
3、属性列表	17
	（1）在属性的控制器里面添加一个lst方法，并拷贝对应的静态页面，并修改静态页面的样式和图片的路径。	17
	（2）完成属性数据的遍历	18


一、项目布局
1、新建一个虚拟主机
<Virtualhost *:80>
	DocumentRoot "E:/php_test/20150710/phptext/app1"
	ServerName www.app1.com
	<Directory "E:/php_test/20150710/phptext/app1">
		#访问权限
		order deny,allow
		allow from all
		#默认首页
		DirectoryIndex index.php index.html index.htm
		#是否允许列出目录结构,FollowSymLinks独立生效重写规则
		Options indexes FollowSymLinks
		#是否允许外部加载
		AllowOverride All
	</Directory>
</Virtualhost>
2、程序架构
文件index.php
<?php
	define("APP_PATH",'./APP/');
	define("APP_DEBUG",TURE);
	require './ThinkPHP/ThinkPHP.php';
?>

3、布局后台首页
（1）新建一个后台的模块Admin，在后台模块里面，新建一个IndexController的控制器。
在该控制器里面添加index left top main  drag的方法，
IndexController.class.php
<?php
namespace Admin\Controller;
use THink\Controller;
class IndexController extend Controller
{
	public function index(){
		$this->display();
	}
	public function top(){
		$this->display();
	}
	public function left(){
		$this->display();
	}
	public function drag(){
		$this->display();
	}
	public function main(){
		$this->display();
	}
}
?>
（2）拷贝 index left top main  方法对应的静态页面，

（3）打开对应的静态页面， 修改样式和图片的路径
<link href='__PUBLIC__/Admin/style/general.css' rel="stylesheet" type="text/css">
<link href='__PUBLIC__/Admin/style/main.css' rel="stylesheet" type="text/css">
（4）修改index.html 静态页面，修改引入其他页面的路径。
index.html
<frame src="__CONTROLLER__/top" id="header-frame">

补充:TP框架天生支持伪静态,去不去除.html都没问题

4、在配置文件中，添加数据库的配置，
app1/APP/Common/config.php
<?php
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
?>
二、商品类型管理
商品类型是用于定义商品的属性的。通过定义商品的类型，该商城网站可以卖任何商品，
1、创建商品类型的表。
create table it_type(
    id tinyint unsigned  primary key auto_increment,
    type_name  varchar(32) not null comment '商品类型的名称'
)engine myisam charset utf8;

2、添加商品类型，
（1）新建一个商品类型的控制器，在里面添加add方法，并拷贝对应的静态页面，并修改静态页面里面的样式和图片的路径。
<?php
namespace Admin\Controller;
use Think\Controller;
class TypeController extend Controller
{
	//添加商品类型
	public function add(){
		$this->display();
	}
}
?>


（2）修改add.html里面表单。
<form action="__ACTION__">
	<input type="text" name='type_name' value='' maxlength='20' size='27'>
</form>

（3）新建一个商品类型的模型，在里面添加数据验证，
验证表单提交数据的合法性，有两种：（1）前端js 验证，（2）php入库之前的验证。

需要使用系统的自动验证功能，只需要在Model类里面定义$_validate属性，是由多个验证因子组成的二维数组。
TypeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class TypeModel extend Model
{
	//添加数据验证;验证表单提交的数据的合法性
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		//要验证的商品类型不能为空
		array('type_name','require','必须输入商品类型');
	);
}
?>

（4）在控制器里面，add 方法中，完成数据的添加。
create方法所做的工作远非这么简单,在创建数据对象的同时,完成了一系列的工作
步骤:
1.获取数据源(默认是POST数组)
2.验证数据源的合法性(非数组或者对象会过滤)	失败返回false
3.检查字段映射
4.判断提交状态(新增或者编辑 根据主键自动判断)
5.数据自动验证		失败返回false
6.表单令牌验证		失败返回false
7.表单数据赋值(过滤非法字段和字符串处理)
8.数据自动完成
9.生成数据对象(保存在内存)
具体的代码：
TypeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class TypeController extend Controller
{
	//添加商品类型
	public function add(){
		if(IS_POST){
			//接收数据,开始入库
			$typemodel = D('Type');
			if($typemodel->create()){
				//构造数据对象
				if($typemodel->add()){
					//添加成功
					$this->success('添加商品类型成功',U('lst'));
					exit;
				}else{
					//添加失败
					$this->error('添加商品类型失败');
				}
			}else{
				//create验证失败,$Typemodel->getError()方法是获取错误提示
				$this->error($typemodel->getError());
			}
		}
		$this->display();
	}
}
?>


（5）注意事项：

客户端可以伪装表单，如果非法提交 id的值，当id达到表结构里面定义的最大值时，则会出现问题无法再添加数据。
	// 操作状态(Model.class.php)
    const MODEL_INSERT          =   1;      //  插入模型数据
    const MODEL_UPDATE          =   2;      //  更新模型数据
    const MODEL_BOTH            =   3;      //  包含上面两种方式
TypeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class TypeController extend Controller
{
	//添加商品类型
	public function add(){
		if(IS_POST){
			//接收数据,开始入库
			$typemodel = D('Type');
			if($typemodel->create(I('post.'),1)){
				//构造数据对象
				if($typemodel->add()){
					//添加成功
					$this->success('添加商品类型成功',U('lst'));
					exit;
				}else{
					//添加失败
					$this->error('添加商品类型失败');
				}
			}else{
				//create验证失败,$Typemodel->getError()方法是获取错误提示
				$this->error($typemodel->getError());
			}
		}
		$this->display();
	}
}
?>




解决方案：
第一点：给 create方法添加参数。
修改type控制器中，create方法：

第二点：在模型里面定义允许提交的字段：
TypeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class TypeModel extend Model
{
	//添加数据验证;验证表单提交的数据的合法性
	protected $_validate = array(
		//array(验证字段,验证规则,错误提示,[验证条件,附加规则,验证时间])
		//要验证的商品类型不能为空
		array('type_name','require','必须输入商品类型');
	);
	//protected $insertFields = array('type_name','其他字段');
	//定义允许入库的字段,防止表单提交其他字段
	protected $insertField = 'type_name';//可以是数组的方式,或者字符串的方式
}
?>

注意：也可以通过$this->updateFields属性定义允许修改的字段。
3、类型列表
（1）在type控制器里面添加一个lst方法，用于取出类型数据。
并拷贝对应的静态页面，并修改样式和图片的路径。
TypeController.class.php
//商品类型列表页面
public function lst(){
	//取出商品类型的数据
	$typemodel = D('Type');
	$typedata = $typemodel->select();
	$this->typedata=$typedata;//相当于:$this->assign('typedata',$typedata);
	$this->display();
}	
（2）把取出的数据，在静态页面中进行遍历
<?php foreach($typedata as $v){?>
	<?php echo $v['type_name']?>
	<a href="<?php echo U('Attribute/lst',array('id'=>$v['id']))?>"></a>
<?php }?>

三、商品类型属性管理
1、建立属性表，用于存储商品类型的属性。
（1）属性的类型，有唯一属性，有单选属性，复选属性
唯一属性，就是属性的值是唯一的。
唯一属性的特点：
其实唯一属性可以理解为只有一种可能的属性，类似于“产地”“保质期”这些都是，因为无论是产地还是保质期只有一种可能，产地不是上海或者北京就是其他地方，保质期也是，12个月或者1年等等。
唯一属性在前台的表现：

单选属性
单选属性是针对于单个产品的某些属性的选择，这些属性是单选的，只能选一个。
单选属性一般存在于商品详细页的右侧，展示效果如下：


复选属性：
复选属性的特点：
多选属性的特点就是客户在商品详细页可以对该商品进行附加的熟悉选择，而这个属性是复选的并非单选，也可以给这个属性加上价格以至于影响最终购买的总价。打个比方，你有一个商品是诺基亚手机，你可以给他加复选属性：手机电池，手机充电器并给他们加上价格
复选属性的前台表现：
唯一属性一般存在于商品详细页，具体展现在商品详细页右侧，如下面的位置：


（2）属性值录入方式:
是指，在添加商品时，采用什么方式来添加数据。

手工录入：
采用文本框直接输入内容。

列表选择：通过列表里面的内容选择值。


要注意：在添加商品时，指定属性值时，属性前面有”[+]”表示单选属性。

（3）建立属性表
create table it_attribute(
    id smallint unsigned  primary key auto_increment,
    attr_name  varchar(32) not null comment '属性的名称',
    type_id  tinyint unsigned not null comment '属性所属商品的类型的id',
    attr_type tinyint  not null default 0 comment '表示属性的类型，0表示唯一属性，1表示单选属性',
    attr_input_type tinyint not null default 0 comment '属性值录入方式，0表示手工录入,1表示列表 选择',
    attr_value varchar(32) not null default '' comment '列表选择的列表值'
)engine myisam charset utf8;

2、添加商品的属性。
（1）在后台模块下面新建一个属性的控制器(AttributeController)，并在里面添加add方法，并拷贝对应的静态页面，并修改静态页面中的样式和图片的路径。

AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}
}
?>
（2）修改静态页面里面提交的表单。


（3）新建一个商品属性的模型，并添加数据验证，
数据验证：需要验证什么？
属性的名称不能为空，
商品类型的id必须是number
属性的类型的值必须是0或1
属性值的录入方式必须是0或1，

AttributeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class AttributeModel extend Model
{
	//添加字段验证
	protected $_validate=array(
		array('attr_name','require','属性的名称不能为空'),
		array('type_id','number','商品类型不合法'),
		array('attr_type',array(0,1),'属性类型不合法'),
		array('attr_input_type','array(0,1)','属性输入类型不合法'),
	);
}
?>


（4）在控制器里面的add 方法中完成数据入库。
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//通过数据验证
				if($attrmodel->add()){
					//添加属性成功
					$this->success('添加属性成功');
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}
}
?>

（5）在添加属性表单里面，完成 如下功能
根据属性类型,选择属性值录入方式
思路：使用jquery来完成。

第一步：引入jquery.js
<js href="__PUBLIC__/Js/jquery.js"/>

出处如下：

第二步：开始给属性值的录入方式添加单击事件
最终代码：
<js href="__PUBLIC__/Js/jquery.js"/>
<script>
	$(function(){
		//先让可选值列表处于禁用状态
		$("textarea[name=attr_value]").attr('disabled',true);
		//给属性值录入方式添加单击事件
		$("input[name=attr_input_type]").click(function(){
			//取出radio的值,如果该值是1,则让textarea处于开启状态
			var zhi = $(this).val();
			if(zhi==1){
				//让textarea处于开启状态
				$("textarea[name=attr_value]").attr('disabled',false);
			}else{
				//否则清空value值,且让textarea处于关闭状态
				$("textarea[name=attr_value]").val('').attr('disabled',false);
			}
		});
	});
</script>

3、属性列表
（1）在商品属性的控制器里面添加一个lst方法，并拷贝对应的静态页面，并修改静态页面的样式和图片的路径。
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//通过数据验证
				if($attrmodel->add()){
					//添加属性成功
					$this->success('添加属性成功');
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}

	//商品属性列表
	public function lst(){
		//取出商品属性的数据
		$attrmodel = D('Attribute');
		$attrdata = $attrmodel->field("a.*,b.type_name")->join(a left join it_type b on a.type_id=b.id)->select();
		$this->assign('attrdata',$attrdata);
		$this->display();
	}
}
?>
（2）完成属性数据的遍历
Admin/View/Attribute/lst.html
<?php foreach(){?>
<?php echo $v['attr_name']?>
<?php echo $v['type_name']?>
<?php echo $v['attr_type']==0?'唯一属性':'单选属性'?>
<?php echo $v['attr_input_type']==0?'手工输入':'列表输入'?>
<?php echo $v['attr_value']?>
<?php }?>


