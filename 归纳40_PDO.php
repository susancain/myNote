PDO基础
	PDO是一种纯面向对象的数据库操作.(全是类)
	PDO: PHP Data Object, PHP数据对象, 数据库抽象层: 将所有的数据库的操作都进行封装, 通过调用该层内容,就可以实现对不同数据库的操作.

	PDO操作任何数据库的方法都一样: 提供了三个类
		PDO类: 主要负责数据库的连接操作, 然后发送SQL指令
		PDOStatement类: 结果处理类
		PDOException类: 异常处理类
开启PDO_mysql
使用PDO
1.连接认证
	PDO::__construct($dsn,用户名,密码);
	$dsn: data source name,数据源名字
	$dsn: 数据库类型:host=主机地址;port=端口信息;dbname=数据库名字;charset=字符集
2.发送SQL指令: 服务器执行,返回执行结果
	a)无结果集的指令: PDO::exec(),返回受影响行数, 如果SQL语句有错误,那么返回false
	b)有结果集的指令: PDO::query(), 返回的是PDOStatement类的对象
3.关闭数据库的连接: PDO没有提供关闭操作(不需要关闭): 自动等到脚本执行结束释放
PDO增删改查
	增删改查是从用户对数据的理解操作: 对于系统只有两种操作: 读和写
	写操作
		PDO::errorCode: 获取错误编号
		PDO::errorInfo: 获取所有错误信息(数组)
		插入操作: 正确结果应该是获取自增长id: PDO::lastInsertId()
		更新或者删除操作: 返回影响的行数: exec的结果
	读操作
		使用PDO::query方法
		解析数据: PDOStatement提供了一系列的解析数据的方法(fetch系列方法)
		PDOStatement::Fetch方法
			默认的: 关联索引混合数组
			获取关联数组: PDO::FETCH_ASSOC
			获取索引数组:PDO::FETCH_NUM
		获取对象: 将所有的字段数据都当做对象的属性(公有)
		stdClass叫做标准类: 空类
		获取对象: 指定类: PDOStatement::fetchObject
		如果是获取表中的多条记录: 需要遍历获取所有结果
		PDOStatement::fetchAll: 获取所有数据,返回一个二维数组
		PDO::FETCH_BOUND: 在进行数据获取的时候, 将指定列的数据绑定到事先准备好的变量中: 
		PDOStatement::bindColumn(字段, 要绑定的变量名);

预处理
	1.准备预处理: 
		prepare 预处理名字 from '执行的SQL指令;
	2.执行预处理
		execute 预处理名字;
	prepare 预处理名字 from 'SQL指令 where id = ?';
	execute 预处理名字 using 变量; //如果有多个占位符,就跟多个变量,使用逗号分隔
PDO预处理
	1.PDO发送预处理语句给服务器: PDO::prepare(),
	 使用冒号+名字:   :id
	2.执行预处理: PDOStatement::execute()
		传入参数 PDO提供了三种方法
		方案1: 使用数组直接传参
		方案2: 在执行预处理之前,绑定占位符数据
		PDOStatement::bindValue()
		PDOStatement::bindParam()
	bindValue与bindParam的区别: 
	bindParam(引用传递)绑定一次变量永久生效(变量改变再执行预处理,新数据有效),bindValue只能生效一次
PDO事务处理
	1.开启预处理: PDO::beginTransaction();	//内部exec(‘start transaction;’);
	2.执行事务: 多条写操作的SQL指令
	3.提交事务
	a)PDO::commit(): 正确提交  exec(‘commit’);
	b)PDO::rollback(): 错误回滚   exec(‘rollback’);
	4.如果要设置回滚点: 通过PDO::exec();
	a)设置回滚点
	$pdo->exec('savepoint sp1');
	b)回到回滚点
	$pdo->exec('rollback to sp1');

属性处理
	PDO属性处理,指的不是PDO类或者PDOStatement类自己对象的属性: 指的是对应mysql服务器的属性(变量): 改变mysql服务器的服务状态.

	获取属性: PDO::getAttribute(PDO常量);	
	设置属性: PDO::setAttribute(PDO常量,PDO常量); 
	PDO::ATTR_AUTOCOMMIT（1,0）: 控制自动事务, 默认的是1(自动提交), 0不自动提交

	PDO::ATTR_CASE: PHP在获取到记录数据之后, 字段名是什么格式的
	PDO::CASE_LOWER: 所有的字段名都小写
	PDO::CASE_UPPER: 所有的字段名都大写
	PDO::CASE_NATURAL: 默认的,所有的字段名与原始的字段一致

	PDO::ATTR_ERRMODE: mysql在遇到错误之后的处理方式(PDO)
	PDO::ERRMODE_SILENT: 默认的, 静默模式, 出错了不会报错
	PDO::ERRMODE_WARNING: 警告模式, 出错了给警告
	PDO::ERRMODE_EXCEPTION: 异常模式, 出错了会抛出异常

	PDO::ATTR_PERSISTENT: 是否支持长连接, 默认的数据库连接资源会随着脚本的执行结束而结束: 但是实际上
	mysql服务器支持长连接: 脚本执行结束之后不会立即释放连接资源,下一个脚本还可以继续使用.
	TRUE: 支持长连接: 若要支持长连接, mysql说了还不算, apache还要允许长连接
	FALSE: 默认的,不支持(脚本执行结束一定释放连接资源)

PDO异常处理
	异常处理是面向对象语言的一种错误处理机制: 将所有的错误信息存放到异常类对象中.
异常处理机制
	Try{
	//所有有可能出错的要执行语句
	//一旦出错:系统自动new PDOException,对象的内存地址赋值给$e
	}catch(PDOException  $e){
	//通过PDOException类的对象$e去调用相关方法或者属性显示错误信息
	}
异常处理模式
	PDO连接数据库的异常捕捉.
	PDO默认的错误处理模式是静默模式: 异常捕捉不到: 必须开启异常模式
抛出异常
	主动抛出异常: throw new PDOException;

反射
	在PHP中提供了很多反射类来查看不同的结构: 
	ReflectionClass: 反射类
	ReflectionFunction: 反射函数
	ReflectionMethod: 反射方法
类反射
	类反射: 通过反射类(ReflectionClass)去了解一个类的内部结构(通常不是用户自定义类,而是系统类)
	ReflectionClass: export(‘类名’): 将一个指定类的内部结构给反映出来(属性,方法和类常量)

	获取类的对象: ReflectionClass::__construct(‘类名’);
	该类(ReflectionClass)有很多方法,可以获取指定类中的很多信息
	ReflectionClass::getMethods: 获取所有的方法
	ReflectionClass:getConstants: 获取所有常量
反向代理
	1.可以通过ReflectionClass反向代理获取指定类的对象
	ReflectionClass::newInstance()
	2.反向代理: 调用指定类的方法: ReflectionMethod
	获取ReflectionMethod类的对象
	ReflectionMethod类下有一个方法叫做invoke: 代理调用方法
封装PDO类
	1.创建一个MyPDO类文件: 初始化属性
	2.增加一个连接数据库的方法: new PDO
	3.增加一个错误模式: 模拟PDO设置(使用常量)
	增加常量
	增加方法让用户选择模式
	将PDO类实例化得到的对象定义成属性保存(跨方法)

<?php
	//PDO操作数据库
	//1.连接认证
	$pdo=new PDO('mysql:host=localhost;port=3306;dbname=ms;charset=utf8','root','root');
	//发送SQL指令执行
	//没有返回结构: 返回受影响的行数:exec
	$sql="update pro_student set s_age=ceil(rand()*10+20)";
	//$res=$pdo->exec($sql);
	//var_dump($res);
	//有返回结果:返回PDOStatement类对象:query
	$sql="select * from pro_student";
	$stms=$pdo->query($sql);
	var_dump($stms);

/*
object(PDOStatement)#2 (1) { ["queryString"]=> string(25) "select * from pro_student" } 
*/

<?php
	//PDO操作数据库: 写操作
	header('Content-type:text/html;charset=utf-8');

	//连接认证
	$dsn="mysql:host=localhost;dbname=ms;charset=utf8";
	$user='root';
	$pass='root';
	//连接
	$pdo=new PDO($dsn,$user,$pass);
	//更新操作
	$sql="update pro_student set s_age=ceil(rand()*10+20)";
	//执行
	$res=$pdo->exec($sql);
	//所有的SQL执行都有可能有语法错误: 判断
	if($res===false){
		echo 'SQL执行错误<br/>';
		echo '错误编码是:'.$pdo->errorCode().'<br/>';
		echo '错误原因是:'.$pdo->errorInfo()[2].'<br/>';
		exit;
	}
	//获取自增长id
	//echo $pdo->lastInsertId();
	//更新或者删除操作: 获取受影响的行数
	echo $res;

<?php

	//PDO操作数据库: 读操作

	header('Content-type:text/html;charset=utf-8');
	//连接认证
	$dsn="mysql:host=localhost;dbname=ms;charset=utf8";
	$uesr='root';
	$pass='root';
	//连接
	$pdo=new PDO($dsn,$user,$pass);
	//读操作
	$sql="select * from pro_student";
	//执行
	$stmt=$pdo->query($sql);
	//var_dump($stms);
	//判断结果
	//所有的SQL执行都有可能有语法错误: 判断
	if($res===false){
		echo 'SQL语句错误';
		echo '错误编码为:'.$pdo->errorCode().'<br/>';
		echo '错误原因是:'.$pdo->errorInfo()[2].'<br/>';
		exit;
	}
	/*
	//如果没有错误得到的是PDOStatement对象: 解析数据
	//fetch方法: 默认是关联索引数组
	$row=$stmt->fetch();
	var_dump($row);

	//fetch方法: 获取关联数组
	$row=$stmt->fetch(PDO::FETCH_ASSOC);
	var_dump($row);
	//fetch方法: 获取索引数组
	$row=$stmt->fetch(PDO::FETCH_NUM);
	//fetch方法: 获取对象
	$row=$stmt->fetch(PDO::FETCH_OBJ);
	//fetchObject方法: 获取指定类的对象
	class Student{}
	$row=$stmt->fetchObject('Student');
	//遍历结果
	$lists=array();
	while($row=stmt->fetch(PDO::FETCH_ASSOC)){
		$lists[]=$row;
	}
	//fetchAll: 获取所有数据
	$lists=$stmt->fetchAll(PDO::FETCH_ASSOC);
	*/
	//绑定变量
	$stmt->bindcolumn(2,$number);
	$stmt->bindcolumn('s_name',$name);
	//fetch: 指定模式为FETCH_BOUND
	$row=$stmt->fetch(PDO::FETCH_BOUND);
	var_dump($row,$number,$name);




-- mysql预处理

-- 准备预处理
prepare selectstudent from 'select * from pro_student';
-- 执行预处理
execute selectstudent;
-- 预处理查询学生
prepare selectstudent1 from 'select * from pro_student where id=?';
-- 执行预处理
-- 先将数据赋值给变量
set @id=3;
execute selectstudent1 using @id;
-- 多个参数预处理
prepare selectstudent2 from 'select * from pro_student where s_age>? and height>?';
-- 执行
set @age=20;
set @height=170;
execute selectstudent2 using @age,@height;-- 系统是顺序替换占位符



<?php
	//PDO 预处理
	header('Content-type:text/html;charset=utf-8');

	//连接认证
	$dsn = "mysql:host=localhost;dbname=project;charset=utf8";
	$user = 'root';
	$pass = '1234';
	//连接
	$pdo=new PDO($dsn,$user,$pass);
	//发送预处理
	$sql="select * from pro_student where id=:id";
	$stmt=$pdo->prepare($sql);	//返回一个PDOStatement对象
	//var_dump($stmt);
	//prepare方法中: 
	//1.	使用正则表达式取出:id替换成?
	//2.	在预处理指定语句之前增加了 prepare 名字 from $sql
	//给预处理准备参数
	//$arr=array(':id'=>3);
	//执行预处理
	//$res=$stmt->execute($arr);
	//var_dump($res);
	//execute方法
	//1.	检查参数: 有: set @变量 = 传入的值$arr[':id'];
	//2.	拼凑执行语句: execute 预处理名字 using @变量;

	//使用bindValue绑定数据结果
	$id=4;
	$stmt->bindValue(':id',$id);
	//使用bindParam绑定数据
	//$id=5;
	//$stmt->bindParam(':id',$id);
	//执行预处理:不需要传参
	$stmt->execute();
	//获取预处理的执行结果
	$student=$stmt->fetch(PDO::FETCH_ASSOC);
	var_dump($student);
	//再次执行预处理
	$id=6;
	$stmt->execute();
	//获取预处理的执行结果
	$student=$stmt->fetch(PDO::FETCH_ASSOC);
	var_dump($student);




<?php

	//PDO事务处理

	header('Content-type:text/html;charset=utf-8');

	//连接认证
	$dsn = "mysql:host=localhost;dbname=project;charset=utf8";
	$user = 'root';
	$pass = '1234';

	//连接
	$pdo = new PDO($dsn,$user,$pass);
	//开启事务
	$pdo->beginTransaction();
	//更新操作
	$sql="update pro_student set s_age=ceil(rand()*10+20)";
	//执行
	$res=$pdo->exec($sql);
	//判断结果:进行选择性处理
	if($res){
		//提交
		$pdo->commit();
	}else{
		//失败
		$pdo->rollback();
	}


<?php

	//PDO属性控制
	header('Content-type:text/html;charset=utf-8');

	//连接认证
	$dsn = "mysql:host=localhost;dbname=project;charset=utf8";
	$user = 'root';
	$pass = '1234';

	//连接
	$pdo = new PDO($dsn,$user,$pass);
	echo '<pre>';
	//获取属性
	//var_dump($pdo->getAttribute(PDO::ATTR_AUTOCOMMIT));
	//修改属性
	$pdo->setAttribute(PDO::ATTR_CASE,PDO::CASE_UPPER);
	//修改错误处理模式:警告
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
	//查询数据
	$sql="select * frompro_studentwhere id=3";
	$stmt=$pdo->query($sql);
	//var_dump($stmt->fetch(PDO::FETCH_ASSOC));


<?php

	//PDO异常处理
	header('Content-type:text/html;charset=utf-8');

	try{
		//连接认证
		$dsn = "mysql:host=localhost;dbname=project;charset=utf8";
		$user = 'root';
		$pass = '1234';

		//连接
		$pdo = @new PDO($dsn,$user,$pass);
		//开启异常模式
		$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		//操作
		$stmt=$pdo->query(select * from pro_student where id=2);
		$row=$stmt->fetch(PDO::FETCH_ASSOC);
		var_dump($row);
		//判断结果
		if(!$row){
			//没有查到数据:主动抛出异常
			throw new PDOException;
		}
		//对数据进行操作
		echo 'end';
	}catch(PDOException $e){
		//var_dump($e);
		//输出错误信息
		//echo $e;
		echo '出错了<br/>';
		echo '错误文件是:'.$e->getFile().'<br/>';
		echo '错误代码是:'.$e->getCode().'<br/>';
		echo '错误代码行是:'.$e->getLine().'<br/>';
		echo '错误原因是:'.iconv('gbk','utf-8',$e->getMessage()).'<br/>';
		exit;
	}




<?php

	//反射: 类结构

	//调用ReflectionClass::export静态方法
	echo '<pre>';

	//ReflectionClass::export('PDO');
	//得到反射类的对象
	$ref=new ReflectionClass('PDO');
	//var_dump($ref);
	//获取PDO类的所有方法
	$methods=$ref->getMethods();
	var_dump($methods);



<?php
	//PHP反射:反向代理
	//定义类
	class Student{
		public function show(){
			echo __METHOD__,'<br/>';
		}
		public static function display(){
			echo __METHOD__,'<br/>';
		} 
	}
	
	//得到反射类对象
	$ref = new ReflectionClass('Student');
	//通过ReflectionClass类对象获取Student类对象
	$student = $ref->newInstance();
	//var_dump($student);
	//反向代理调用方法
	//反射方法
	$m= new ReflectionMethod('Student','show');
	//var_dump($m);
	//反向调用方法
	//$m->invoke($student);
	//实例
	$m = new ReflectionMethod('Student','display');
	$m->invoke(null);