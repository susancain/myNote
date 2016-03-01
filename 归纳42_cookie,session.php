<?php
	//header函数设置cookie
	header('set-cookie:name=mark');

<?php
	//PHP设置cookie:setcookie函数
	setcookie('age',30);

<?php
	//PHP接收cookie数据
	//$_COOKIE自动接收
	var_dump($_COOKIE);

<?php
	//COOKIE生命周期
	//设置cookie(默认生命周期)
	setcookie('cookie','cookie');
	//显示的指定生命周期为会话结束
	setcookie('default','default',0);
	//指定生命周期:10秒之后就过期
	setcookie('ten',10,10);//错误: 代表早在1970年1月1日,0点0分10秒过期
	//合理10秒
	setcookie('active_ten',10,time()+10);
	//cookie保存7天
	setcookie('seven','seven',time()+7*24*3600);


<?php
	//COOKIE作用域
	//设置一个简单cookie:当前目录有效
	setcookie('local','local',0);
	//设置全局cookie:整站有效
	setcookie('global','global',0,'/');

<?php
	//cookie跨域
	//访问cookie
	var_dump($_COOKIE);
	//默认的cookie只针对当前绑定的主机名有效
	setcookie('local','local',0,'/');


<?php
	//cookie特性
	//setcookie不能保存数组
	$user=array(
		'username'=>'mark',
		'age'=>30
	);
	//setcookie('user',$user);//$user是数组
	//让cookie的名字变成数组
	setcookie('user[username]','mark');//浏览器不识别中括号
	setcookie('user[age]',30);
	//查看cookie
	var_dump($_COOKIE);
	//$_COOKIE[user[username]] => mark
	//$_COOKIE[user[age]] => 30
	//PHP对中括号很敏感: 一旦碰到中括号就认为是数组
	//$_COOKIE[user][username] => mark
	//$_COOKIE[user][age] => 30


<?php
	//使用session
	//开启session
	//开启session后,系统不管用户存不存东西,都会给一把"钥匙"
	session_start();
	//休眠
	//sleep(10);
	//将数据存放到容器中
	$_SESSION['name']='mark';


<?php
	//使用session数据
	//需要session系统拿:激活session(开启session)
	session_start();
	//访问$_SESSION
	var_dump($_SESSION);



<?php
	//删除session文件
	//开启session
	session_start();
	//删除session
	session_destroy();


<?php
	//session特点
	//开启session
	session_start();
	//保存session数据
	$_SESSION['name']='mark';
	//使用索引下标保存session数据
	//$_SESSION[]=100;//不能使用索引下标

<?php
	//保存session数据
	header('Content-type:text/html;charset=utf-8');
	//开启session
	session_start();
	//获取session信息
	$name = session_name();
	$id = session_id();
	//保存session数据
	$_SESSION['name']='mark';
	//给出一个链接
	echo "<a href='demo13_session_manual2.php?{$name}={$id}'>点我</a>";


<?php
	//获取session数据
	//改变session_start从cookie获取id的机制
	//接收数据
	$id = $_GET[session_name()];
	//修改session_start的开启机制:使用当前指定SESSID
	session_id($id); //注册sessionid
	//开启session
	session_start();
	//获取
	var_dump($_SESSION);


<?php
	//自动session转换(保存数据)
	header('Content-type:text/html;charset=utf-8');
	//开启session
	session_start();
	//保存session数据
	$_SESSION['age'] = 30;
	//给定a标签
	echo "<a href='demo15_session_auto2.php'>点我1</a>";
?>
	<a href="demo15_session_auto2.php">点我2</a>

<?php
	//自动session转换(使用session数据)
	//开启session
	session_start();
	//打印session
	var_dump($_SESSION);
