接口: interface
	接口: 对外提供的操作方式, 操作接口(public)
	接口: 用来规范项目代码, 结构接口(interface)
接口语法
跟类结构很像
Interface 接口名字{
//接口成员
}

接口成员
	接口中只能有两个内容: 常量和抽象方法(无方法体)
	接口不允许有成员属性
	接口中的方法必须为public(访问修饰限定符只有PUBLIC)

接口使用
	接口的本质是为了规范结构(抽象类)
	接口主要用来规范实现接口的类.

	Class 类 implements 接口名{
	//类中必须实现所有的接口中的抽象方法(除非类抽象类则可以不实现抽象方法)
	}

	实现接口: 抽象类实现接口,或类实现接口的抽象方法

接口意义
	规范项目结构.
	抽象类与接口本质的意义相似,都是规范项目结构

接口特点
	1.接口可以多实现: 一个类可以一次实现多个接口
	Class 类名 implements 接口1,接口2...
	2.接口是可以被继承? 接口不能被类继承,只能被类实现	
	接口可以继承接口

面试题
1.抽象类是不是类?
肯定是类: 抽象类中可以有类中所包含的所有内容(属性,方法,类常量), 抽象类是一种用于继承的类.
2.接口是不是类?
接口不是类, 接口interface, 类class: 在系统内部,接口与类的结构是一样的, 但是有一些细微的限制区别.
3.接口是否可以被继承?
接口不能被类继承, 接口只能被类实现; 接口可以被接口继承.
4.PHP是否支持多继承?如何模拟?
PHP不支持多继承
模拟: 链式继承(继承链)

重载
重载: overload, 是面向对象中一个很重要的概念: 在同一个类中,可以出现多个同名函数, 但是要求其返回值或者参数类型,或者参数的个数不同.

PHP重载
PHP重载与传统意义的重载完全两码事.

PHP重载: 当访问一个不存在或者权限不够(private,protected)的成员的时候(属性和方法), 系统会自动触发一些魔术方法.

PHP重载分为两种: 属性重载和方法重载

属性重载
重载都是指魔术方法
__get(): 对象访问一个不存在的或者权限不够的属性的时候会自动触发的方法
__set(): 设置属性
__isset(): 使用isset或者empty判断属性是否存在
__unset(): 使用unset销毁属性

方法重载
当访问一个不存在的方法或者权限不够的方法的时候会自动触发的魔术方法.
__call(): 普通方法(对象访问方法触发)
__callStatic(): 静态方法(类访问方法触发)

重载意义
1.主要意义: 容错处理: 当用户不当的调用定义的结构(属性或者方法)不会报错
2.保护结构完整性: __set和__unset

命名空间
命名空间: namespace, 是一种逻辑上对内存进行分离的技术: 分离之后,在不同的区间内部就允许有同名的结构出现.
命名空间就是文件夹: 在不同的文件夹中就可以有不同的同名文件(类,函数,常量)

定义空间
	空间关键字: namespace
	namespace 命名空间名;
	定义命名空间之前不允许有任何代码.
空间元素
命名空间中真正受影响的内容只有三个: 函数, 常量和类, 变量不受空间影响.

访问空间元素
__NAMESPACE__: 获取当前所属的空间名字

访问空间元素
	非限定名称
		当前文件夹: 只能从当前文件夹所找到元素:从访问的位置向上找到的第一个最近的空间
	限定名称
		从当前目录向下查找: 找子文件夹内部的内容
		子空间名\元素
	完全限定名称
		从根目录开始访问: 代表根目录的方式是"\"反斜杠
		\目录名\元素
子空间
将文件夹进行分层: 命名空间的子空间.
一级空间\二级空间;	//二级空间就是一级空间的子目录
子空间元素的访问: 用的最多的就是完全限定名称

空间引入
	不可能在一个脚本中创建多个同名结构(常量,函数,类), 一般一个脚本一个命名空间.
	当进行文件的引入的时候,实际上也就引入了空间

	非限定名称访问引入空间所在文件: 没有使用被引入的文件空间
	限定名称访问: 被加载的文件的空间所在文件不会自动的编程当前空间的子空间
	完全限定名称访问: 被加载的空间所在文件若要直接使用就必须使用完全限定名称

	引入空间: use 空间名[\空间元素]; // 空间元素仅限类
	引入空间之后: 可通过限定名称访问
	引入空间元素: use 空间名\类 [as 别名]; //引入空间元素之后,可以访问

全局空间
全局空间(根目录): 文件中没有指定命名空间,文件下的所有元素(常量,类和函数)都属于全局空间.
全局空间引入之后若要访问,那么必须使用全局空间符号: "\"


对象的保存与还原
	对象是一种复合数据类型, 文件只能保存字符串数据: 将对象转换成字符串然后再存储到文件.
	__tostring: 当对象被当做字符串处理的时候会自动调用. 用户自定义的字符串可以实现保存到文件, 但是没有办法从文件还原成对象本身.

	序列化和反序列化
		序列化: 将一个大的数据(复合或者简单),按照指定的格式要求(每个数据都有数据类型),转换成一个字符串: 字符串能够反映出结构的关系
		serialize函数: 将一个指定的数据转换成一个可以反映出类型的字符串
		反序列化: 将一个拥有结构信息的字符串, (解密)变成对应的复合结构的过程.
	对象保存
		将对象先序列化变成字符串,然后再保存到文件的过程
	对象还原
		将一个序列化好的对象字符串, 还原成对应的数据类型(对象)

	对象反序列化后: 只要保证内存中有与需要的类同名的类即可: 同样的反序列化是可以使用自动加载的.

	魔术方法(序列化)
		当对象进入休眠的时候(序列化)的时候会自动调用一个魔术方法: __sleep()
		当对象由休眠状态激活的时候(反序列化)会自动调用一个魔术方法:__wakeup()

	资源数据无法序列化并且进行保存.
	将对象休眠的时候: 资源是没有保存的意义: 资源不能保存: 通过__sleep将不需要保存的属性给剔除: 返回的是一个需要保存的对象属性的数组
	对象被唤醒时: 唤醒的对象会立马调用对象所属类的唤醒方法(__wakeup)
面向对象相关函数
	class_exists: 判断类在内存中是否存在
	interface_exists: 判断接口
	method_exists: 判断方法
	get_class: 获取类名,给的参数是对象
	get_parent_class: 获取父类的名字: 只能获取当前对象所在类的上级父类
	instancof: 判断一个对象是否属于指定的类

对象遍历:将所有的公有属性进行输出,foreach遍历对象
实现iterator接口
	遍历的是对象内部的某些特殊的数组(不一定是公有的)
	foreach永远只能遍历对象中公有的属性. 需要在foreach进行遍历的时候, 能够修改foreach的内部遍历原理: 让其不要盯着公有属性,而是应该制定某个具体的属性(数组)
	若想改变foreach的内部原理而实现对象遍历某个私有或者受保护的属性: 那么当前对象所属的类必须实现某个指定的系统接口: Iterator
	代码实现:实现五个抽象方法
	current方法
	key方法
	next方法
	rewind方法
	valid方法

Ecshop安装

<?php
	//PHP接口
	//定义接口
	interface DB{
		//接口成员
		//接口常量
		const PI=3.14;
		//定义抽象方法
		public function getPI();
		public function test();
	}
	//实现接口
	abstract class Mysql implements DB{
		//抽象类可以不实现抽象方法
	}
	class Oracle implements DB{
		//实现接口中所有的抽象方法
		public function getPI(){}
		public function test(){}
	}
	//接口中的常量可以被实现类使用（访问）
	echo Oracle::PI;

<?php
	//接口特点
	//定义接口
	interface A{
		public function getA();
	}
	interface B{
		public function getB();
	}
	//接口继承接口
	interface C extends B{}
	//实现接口
	class E implements C{
		//实现C接口继承的B接口的抽象方法
		public function getB(){}
	}
	//实现多个接口
	class D implements A,B{
		public function getA(){}
		public function getB(){}
	}


<?php
	//方法重载
	//定义类
	class Person{
		private function getName(){
			echo __METHOD__,'<br/>';
		}		
	//魔术方法:__call
	//@param1 string $name,方法的名字
	//@param2 string $args,方法调用的参数
	public function __call($name,$args){
		//建立允许访问列表
		$allow=array('getName');
		//判断并帮助访问
		if(in_array($name,$allow)){
			//访问
			return $this->$name();//可变方法
		}else{
			return false;
		}
	}
		//魔术方法：__callStatic()
		public static function __callStatic($name,$args){
			//var_dump($name,$args);
			return false;
		}
	}
	//类访问方法(类访问的方法是静态方法)
	Person::getPerson();
	//实例化
	//$p=new Person();
	//$p->getName(1,2,3);


<?php
	//命名空间定义

	//定义命名空间first
	namespace first;
	//空间元素:常量，类和函数
	const PI=3.14;
	class Person{
		public function display(){
			echo __NAMESPACE__,'~',__METHOD__,'<br/>';
		}
	}
	//定义函数
	function test(){
		echo __METHOD__,'<br/>';
	}

	//定义命名空间second(文件夹)
	namespace second;
	//定义函数
	function test(){
		echo __METHOD__,'<br/>';
	}
	const PI=3.15;
	class Person{
		public function display(){
			echo __NAMESPACE__,'~',__METHOD__,'<br/>';
		}
	}
	
	//访问元素
	//非限定名称访问
	test();
	//完全限定名称
	\second\test();
	$p=new \first\Person();	//第一个first空间
	$p->display();

/*
second\test
second\test
first~first\Person::display
*/

<?php
	//PHP命名空间：子空间
	//定义子空间
	namespace first\second\third;
	const PI=3.14;
	function display(){
		echo __NAMESPACE__,'<br/>';
	}
	//定义子空间
	namespace first\second;		//规定以下的内容(元素)存放到second文件夹下
	const PI=3.15;
	function display(){
		echo __NAMESPACE__,'<br/>';
	}
	//定义子空间
	namespace first;
	const PI=3.16;
	function display(){
		echo __NAMESPACE__,'<br/>';
	}
	//非限定名称访问
	echo PI,'~';
	display();
	//限定名称:访问子目录
	second\display();	//访问first\second\display();
	//完全限定名称
	echo \first\second\third\PI;
/*
3.16~first
first\second
3.14
*/


<?php
	//first空间
	namespace first;
	//定义元素
	const PI=3.14;
	//函数
	function display(){
		echo __NAMESPACE__,'<br/>';
	}
	//类
	class Person{
		function display(){
			echo __NAMESPACE__,'<br/>';
		}
	}


<?php
	//引入空间
	//定义空间
	namespace second;
	//定义元素
	const PI=3.14;
	//定义函数
	function display(){
		echo __NAMESPACE__,'<br/>';
	}
	//定义类
	class Person{
		public function display(){
			echo __NAMESPACE__,'<br/>';
		}
	}
	//引入空间
	use first as test;	//空间也可以使用别名
	//引入空间这条语句不会执行: 只有当开始使用空间元素的时候才会执行
	//加载文件
	include_once 'demo07_namespace_first.php';
	//非限定名称访问
	//display();
	//限定名称
	//first\display();
	//完全限定名称
	//\first\display();

	echo __NAMESPACE__;
	//限定名称访问: 第一次使用test空间由first加载过来: 触发use first空间语句
	test\display();

	//引入first空间中的Person类
	//use first\Person as P;
	//$p=new P();
	//$p->display();

	//引入全局空间
	include_once 'demo09_namespace_global.php';

	//访问全局空间变量
	//echo \PI;


<?php
	//定义全局空间元素
	//定义元素
	const PI=3;
	//函数
	function display(){
		echo __NAMESPACE__,'<br/>';
	}
	//类
	class Person{
		public function display(){
			echo __NAMESPACE__,'<br/>';
		}
	}
	//引入空间文件
	include_once 'demo07_namespace_first.php';
	//引入空间
	use \first;
	//display();

<?php
	//对象保存
	//创建类
	class Person{
		public $name='类';
		private $age=30;
	}
	//实例化
	//$p=new Person();
	//引入DB类文件，实例化DB类保存
	include_once 'DB.class.php';
	$db=new DB();	
	//操作数据库
	$student=db->db_select('select * from pro_student where id = 3');	
	//序列化
	$str=serialize($db);
	//将对象字符串保存到文件
	file_put_contents('object.txt',$str);

<?php
	header('Content-type:text/html;charset=utf-8');
	//获取对象字符串
	$str=file_get_contents('object.txt');
	//创建Person类
	//class Person{}
	//自动加载
	function __autoload($class){
		include_once "{$class}.class.php";
	}
	echo '<pre>';

	//反序列化
	$p=unserialize($str);
	//var_dump($p);
	//操作数据库
	$student=$p->db_getOne('select * from pro_student where id=3');
	var_dump($student);

<?php
	//面向对象相关函数
	//定义类
	class Person{
		private static function testStatic(){}
		public function test(){}
	}
	class Man extends Person{}
	class Boy extends Man{}
	echo'<pre>';
	//判断方法是否存在:method_exists
	var_dump(method_exists(new Person(),'test'));
	var_dump(method_exists('Person','test'));
	var_dump(method_exists('Person','test'));
	//获取类名
	var_dump(get_class(new Person()));
	//获取父类名
	var_dump(get_parent_class(new Boy()));
	//对象判断
	$b=new Boy();
	$m=new Man();
	//判断$b是否是Man类的对象
	var_dump($b instanceof Man);
	var_dump($m instanceof Boy);

	abstract class Woman{}
	class Girl extends Woman{}
	$g=new Girl();
	var_dump($g instanceof Woman);
/*
bool(true)
bool(true)
bool(true)
string(6) "Person"
string(3) "Man"
bool(true)
bool(false)
bool(true)
*/

<?php

	//PHP对象遍历
	//定义类
	class Person{
		public $name = 'John';
		public $height = 178;
		protected $money = 100;
		private $age = 18;
		protected $hobby=array('篮球','足球','地球','羽毛球');
	}
	//实例化
	$p=new Person();
	foreach($p as $k=>$v){
		//$k代表属性名字;$v代表属性值
		echo $k,':',$v,'<br/>';
	}
	/*
name:John
height:178
	*/


<?php
	//PHP对象遍历
	header('Content-type:text/html;charset=utf-8');
	//定义类
	class Person implements Iterator{
		public $name='John';
		public $height=178;
		protected $money=100;
		private $age = 18;
		
		protected $hobby=array('篮球',false,'足球','地球','羽毛球');

		//实现接口中的五个抽象方法
		//获取当前数组指针所指向元素的值: 不移动指针
		public function current(){	//current方法属于类
			echo __FUNCTION__,'<br/>';
			//得到当前访问数组的元素
			return current($this->hobby);//current是系统函数
		}
		public function key(){
			echo __FUNCTION__,'<br/>';
			//获取数组指针下标
			return key($this->hobby);
		}
		public function next(){
			echo __FUNCTION__,'<br/>';
			//移动指针
			next($this->hobby);
		}
		public function rewind(){
			echo __FUNCTION__,'<br/>';
			//重置指针
			reset($this->hobby);
		}
		public function valid(){
			echo __FUNCTION__,'<br/>';
			//必须通过数组的指针判断
			return key($this->hobby)!==null;
		}
	}
	//实例化
	$p=new Person();
	//遍历
	foreach($p as $k=>$v){
		//$k代表属性名字;$v代表属性值
		echo $k,':',$v,'<br/>';
	}
	
