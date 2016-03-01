克隆
对象变量2 = clone 对象变量1; //对象变量1保存的是一个对象
魔术方法: __clone()
对象被克隆后,克隆对象会立即自动调用的方法.
静态
静态: 本质属于类(类访问), 是在类进行编译的时候就会被初始化,而且只初始化一次
静态关键字: static
静态东西分为两类: 静态属性和静态方法, 就是在属性和方法之前使用static关键字.
静态属性
	Static修饰的属性: 属于类(对象不能访问)
	静态属性通过类进行访问: 类名::$静态变量
静态方法
	使用static关键字修饰的方法: 给类访问.
静态意义
静态成员给类访问: 不需要对象就可以访问

1.静态的效率比非静态高(静态直接访问;非静态找找到对象->找到类)
2.静态的内存使用更加少(对象必须产生对象空间)

能使用静态的地方绝不使用非静态.
静态与非静态的互访(不建议)
1.对象本质也可以访问静态方法: 对象访问访问一定是在类中,静态方法也是在类中
2.类本身也可以访问非静态方法(非静态方法也在类中)
3.对象也可以操作范围解析操作符
静态与$this的关系
在非静态方法中使用$this, 静态方法中不允许使用$this: $this代表对象

设计模式
设计模式: 是一种已经成型的,能够解决某一类相似问题的解决方案.
在PHP中只讲两种设计模式: 单例模式和工厂模式
单例模式
单例模式: 单个实例(对象), 一个类在一个脚本执行周期中,永远只能产生一个对象
1.关闭所有能够产生多个对象的方案: 实例化和克隆: 私有化构造方法和克隆方法
2.想办法进入到类的内部进行实例化产生对象: 私有方法在类内部可以访问: 静态方法
3.其实内部在实例化的时候,可以无限实例化
4.想办法解决问题: 在类的内部不应该每次都进行实例化: 应该在没有的情况下才实例化;而如果有对象,就不
需要实例化: 增加一个静态属性,保存对象
5.产生对象的时候应该先判断对象是否存在

单例实现: 三私一公
私有化构造方法: 防止在类外部无限实例化
私有化克隆方法: 防止对象在类外部无限克隆
公有化静态方法: 进入到类被进行实例化或者对象获取
私有化静态属性: 保存已经产生的对象

工厂模式
工厂: 生成工具, 外部要什么内容,工厂就生产什么内容
工厂模式: 帮助外部生成指定类的对象
1.工厂模式是用来帮助别人生产对象: 对象是别人,不是自己的: 静态方法
工厂单例模式
1.工厂单例模式完全不能保证对象真的永远只能产生一个
2.工厂单例模式: 保证对同一个类进行实例化对象的时候只实例化一个

魔术方法
__toString()方法: 对象是一种复合数据类型, 不能直接echo, 但是假设需要将对象进行echo或者其他字符串
方式处理,就会自动调用__toString方法.

面向对象三大特性
面向对象都有的三大特性: 封装,继承和多态
封装
封装: 字面意思上讲,是一个动词, 指的是从自然事物形成计算机可以识别的代码的过程.
从实体形成对实体的描述: 类(抽象: 从公共实体中抽离公共特性的过程)
抽象: 将数据(属性)以及数据的操作(方法),捆绑到一起,形成对外界的隐蔽(类是一种封闭的结构), 但是对外提供可以操作的接口(public).

继承
继承: extends
继承的现实意义: 子辈继承了父辈所拥有的财产: 子辈使用父辈遗留的财产
将已有的类作为父类, 自己当做父类的一个子类, 从而实现父类代码的使用(复用).

多态
多态: 多种形态, 在继承的情况下, 如果一个父类对象得到的却是子类的实例, 当父类对象去进行方法调用的
时候,表现出来的是子类的形态.
要求: 继承, 强类型语言, 重写(override)

PHP继承
继承: 子类从父类获取对应的内容, 可以直接使用.
继承语法
class 子类 extends 父类;
继承内容
在PHP中子类继承父类: 继承所有属性,和所有非私有的方法
在PHP的继承是子类对象继承父类对象, 而不是类与类的继承.

子类对象继承父类对象的所有属性(属性一定保存在对象中)
继承父类的非私有方法

Protected关键字
Protected: 受保护的, 只能在类内部访问, 是一种专门用于继承的关键字: 可以在子类的内部访问
private只能在自己类内部访问.

父类私有属性使用
父类私有属性一定只能在父类中使用: 在父类中增加一个方法可以访问私有属性, 子类继承父类的方法,从而实现父类私有属性的访问.

重写
重写:override, 子类拥有与父类同名的属性或者方法。
属性重写
属性重写：不能重写父类的私有属性（属性带着类名）
属性重写后会被覆盖（私有属性除外）： 在不同类中访问私有属性时，访问的是类自己的私有属性： 公有的或者受保护的一定访问的是子类的属性（子类覆盖了父类的）

方法重写
方法重写：方法属于类（保存在类结构中）：方法重写指子类有父类的同名方法， 但不覆盖父类的同名方法

继承类对象访问方法： 先在子类中寻找访问,没有则寻找父类 
继承内存原理
继承内存包含重写（属性）

重写规则
子类的权限不大于父类的权限（子类要比父类开放）

Parent关键字
parent::父类被重写的方法

静态延迟绑定
定义的时候不绑定类名在调用的时候
绑定类名: static(静态延迟绑定: 调用时绑定)
	静态绑定
	self代表当前所在类的类名(固定的)
	self在定义结构的时候,就已被绑定了值: 所属类名

PHP继承特点
1.PHP不支持多继承: PHP只能有一个父类,只能支持单继承
2.PHP支持链式继承: A extends B, C extends A ====> C extends A,B
3.PHP是双向继承: 子类可以继承父类的内容,但是在父类内部也可以访问子类的内容

PHP特殊类
主要有两个: final(最终)和abstract(抽象)

Final类
Final类: 最终类(不能被扩展,继承)
Final关键字还可以修饰方法(类不是final类): 表示该方法不可以被重写
Final类的意义
1.Final修饰类:类不被继承, 不会被扩展: 保护类结构
2.Final修饰方法: 保护方法本身不被重写

Abstract类
abstract修饰的类:抽象类(抽象类不可被实例化,只能被继承)
abstract修饰的方法:抽象方法(没有方法体(没有{})
有抽象方法(abstract修饰的方法)的类必为abstract抽象类
抽象类中可以拥有一个普通类所拥有的一切内容.
抽象类的意义
规范子类. 子类必须实现父类(抽象类)的所有抽象方法(abstract主要是修饰方法)



<?php
	
	//PHP静态属性
	//统计类产生多少个对象
	//定义类
	class Buyer{
		//静态属性
		public static $count=0;
		//构造方法
		public function __construct (){
			self::$count++;
		}
		//克隆方法
		public function __clone(){
			self::$count++;
		}
	}	
	//访问静态属性
	echo Buyer::$count,'<br/>';
	//实例化
	new Buyer();
	echo Buyer::$count,'<br/>';
	new Buyer();
	echo Buyer::$count,'<br/>';
	// 克隆
	$b1=new Buyer();
	$b2=clone $b1;
	echo Buyer::$count,'<br/>';

<?php
	//静态方法
	//定义类
	class Buyer{
		//静态属性
		private static $count=0;
		//静态方法
		public static function getCount(){
			echo self::$count++,'<br/>';
		}
	}
	Buyer::getcount();
	Buyer::getcount();

静态与非静态的互访(不建议)

静态与$this的关系
在非静态方法中使用$this, 静态方法中不允许使用$this: $this代表对象, 静态方法是给类访问(不需要对象), 这个时候访问的话, $this就无法获取对象

单例模式
单例模式: 单个实例(对象), 一个类在一个脚本执行周期中,永远只能产生一个对象
工厂模式
工厂: 生成工具, 外部要什么内容,工厂就生产什么内容
工厂模式: 帮助外部生成指定类的对象

/*
<?php
	//单例模式

	//定义类: 私有化构造方法和克隆方法
	class Singleton{
		//定义静态属性保存对象
		private static $obj=null;
		//私有化构造方法
		private function __construct(){}					
		//私有化克隆方法
		private function __clone(){}
		//静态方法
		public static function getInstance(){
			//instance:实例
			//判断对象是否存在
			if(!is_object(self::$obj)){
				//不是对象: 实例化
				self::$obj=new self;
			}
				//返回已经存在的对象
				return self::$obj;
			
		}
	}
	//实例化
	//$s = new Singleton();	//私有化之后不能在外部实例化

	//进入到类内部
	$s1=Singleton::getInstance();
	$s2=Singleton::getInstance();
	var_dump($s1,$s2);
*/
<?php
	//单例模式

	//定义类: 私有化构造方法和克隆方法
	class Singleton{
		//定义静态属性保存对象
		private static $obj=null;
		//私有化构造方法
		private function __construct(){}					
		//私有化克隆方法
		private function __clone(){}
		//静态方法
		public static function getInstance(){
			//instance:实例
			//判断对象是否存在
			if(!(self::$obj instanceof self)){
				//不是对象: 实例化
				self::$obj=new self;
			}
				//返回已经存在的对象
				return self::$obj;
			
		}
	}
	//实例化
	//$s = new Singleton();	//私有化之后不能在外部实例化

	//进入到类内部
	$s1=Singleton::getInstance();
	$s2=Singleton::getInstance();
	var_dump($s1,$s2);

<?php
	

	//PHP工厂模式

	class Factory{
		

		//产生对象的方法
		//@param1 string $classname,要生产对象的类名
		//@return mixed,成功返回对象,失败返回false
		public static function getInstance($classname){
			//加载类
			if(is_file("{$classname}.class.php")){
				//加载
				include_once "{$classname}.class.php";

				//理论:实例化
				return new $classname;
			}else{
				//找不到类
				//return false;
				return class_exists($classname)?new $classname:false;
			}
		}
		//禁用克隆
		private function __clone(){}
	}

	//工厂模式实例化
	$db1 = Factory::getInstance('DB');
	$db2 = Factory::getInstance('DB');
/*
$db3 = clone $db2;
$bd4 = new DB;
var_dump($db1,$db2);
*/
	$f=Factory::getInstance('Factory');
	var_dump($f);
魔术方法
__toString()方法: 对象是一种复合数据类型, 不能直接echo, 但是假设需要将对象进行echo或者其他字符串方式处理,就会自动调用__toString方法.

<?php

	//魔术方法: __tostring
	header('Content-type:text/html;charset=utf-8');
	class magic{
		//属性
		public $name='Jack';
		//魔术方法
		public function __tostring(){
			//返回一个字符串
			return 'name的值为：'.$this->name;
		}
	}
	$b=new magic();
	echo $b;

Js面向对象
不是所有的面向对象语言都有类(class), 如js

js的面向对象”类”就是函数

<script>
	//定义类
	function Buyer(a,b){
		//给属性赋值
		this.name=a;
		this.age=b;
	}
	//实例化
	var b1=new Buyer('Mark',30);
	alert(b1.name);
</script>
面向对象三大特性
面向对象都有的三大特性: 封装,继承和多态

<?php
	//PHP继承
	//定义父类
	class Car{
		//属性
		public $wheels=4;
		private $engine;
		//方法
		public function drive(){
			echo __METHOD__,'<br/>';
		}
	}
	//定义子类
	class BMW extends Car{}
	//实例化子类对象
	$bmw=new BMW();
	$bmw->drive();

<?php

	//PHP继承: 继承内容
	header('Content-type:text/html;charset=utf-8');

	//定义父类
	class Car{
		//属性
		public $wheels=4;
		protected $number='粤ASB110';
		private $engine='7速手自一体';
		//方法
		public function drive(){
			echo __METHOD__;
		}
		protected function speed(){
			echo __METHOD__;
		}
		private function swim(){
			echo __METHOD__;
		}
	}
	//定义子类
		class BMW extends Car{
			//增加方法：访问父类的所有方法
			public function getParent(){
				$this->drive();//Car::drive
				$this->speed();//Car::speed
				//$this->swim();//error:私有的成员只能在自己类内部访问
			}
		}
		//实例化子类对象
		$bmw=new BMW();
		$bmw->getParent();


<?php
	//PHP属性重写
	//定义父类
	class Car{
		//属性
		public $wheels=4;
		protected $number='粤ASB110';
		private $engine='7速手自一体';
		//访问属性
		public function getProperty(){
			echo $this->wheels,'<br/>';
			echo $this->number,'<br/>';
			echo $this->engine,'<br/>';
		}
	}
	//定义子类
	class BMW extends Car{
		//重写父类所有属性
		public $wheels=5;
		protected $number='粤ASB120';
		private $engine='5档手速';
		public function getBMW(){
			//访问属性
			echo $this->wheels,'<br/>';
			echo $this->number,'<br/>';
			echo $this->engine,'<br/>';
		}
	}
	//实例化
	$b=new BMW();
	$b->getProperty();
	$b->getBMW();
/*
5
粤ASB120
7速手自一体
5
粤ASB120
5档手速
*/

<?php
	//PHP方法重写
	//父类
	class Car{
		//方法
		public function getA(){
			echo __METHOD__,'<br/>';
		}
		protected function getB(){
			echo __METHOD__,'<br/>';
		}
		private function getC(){
			echo __METHOD__,'<br/>';
		}
		//访问父类的所有方法
		public function getParent(){
			$this->getA();
			$this->getB();
			$this->getC();
		}
	}
	//子类
	class QQ extends Car{
		//重写父类方法
		public function getA(){
			echo __METHOD__,'<br/>';
		}
		protected function getB(){
			echo __METHOD__,'<br/>';
		}
		private function getC(){
		
			echo __METHOD__,'<br/>';
		}
	}
	//实例化
	$qq=new QQ();
	$qq->getParent();
/*
QQ::getA
QQ::getB
Car::getC
*/

<?php	
	//静态延迟绑定

	//父类
	class Car{
		//静态属性
		public static $car='car';
		//方法
		public static function getCar(){
			echo self::$car,'<br/>';
			echo static::$car,'<br/>';
		}
	}
	//子类
	class BYD extends Car{
		//定义静态属性
		public static $car='car';
	}
	//调用
	BYD::getCar();
	Car::getCar();
/*
car
car
car
car
*/


<?php
	//访问父类被重写方法:parent
	//父类
	class Car{
		public function get(){
			echo __METHOD__,'<br/>';
		}
	}
	//子类
	class Haval extends Car{
		//重写父类方法
		public function get(){
			//访问父类get方法
			parent::get();
			echo __METHOD__,'<br/>';
		}
	}
	//实例化子类
	$b=new Haval();
	$b->get();
/*
Car::get
Haval::get
*/

<?php

	//PHP继承特点
	//定义两个类
	class Father{
		//属性
		public $father='father';
	}
	class Mother extends Father{
		//属性
		public $mother='mother';
		//方法：访问子类属性
		public function getSon(){
			echo $this->son;
		}
	}
	//子类
	class Son extends Mother{
		public $son='son';
	}
	$s=new Son();
	$s->getSon();







<?php
	//PHP特殊类：抽象类(abstract)
	//定义类
	abstract class Car{
		//属性
		public $name='car';
		//抽象方法
		abstract public function getName();
	}
	//定义子类
	class Benz extends Car{
		//必须实现抽象类中(父类)的所有抽象方法
		public function getName(){
			echo $this->name,'<br/>';
		}
	}
	//实例化子类
	$b=new Benz();
	var_dump($b);

	

