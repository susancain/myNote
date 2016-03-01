t_login.html
<body>
	<form action="t_login.php" method="POST">
		<table border='1' style="margin:100px auto;text-align:center">
			<tr><td colspan='2'>登录界面</td></tr>
			<tr><td>登录名</td><td><input type="text" name="username"/></td></tr>
			<tr><td>密码</td><td><input type="password" name="password"/></td></tr>
			<tr><td colspan='2'><input type=submit value="提交" name="submit"/></td></tr>
		</table>
	</form>
</body>

t_login.php
<?php
	//处理字符集
	header('Content-type:text/html;charset=utf-8');
	//验证是否有提交数据
	if(isset($_POST['submit'])){
		//1接收POST信息
		$username=$_POST['username'];
		$password=$_POST['password'];
		//2加载公共跳转函数
		include_once 't_public_funciton';
		//3检验数据合法性
		if(empty($username)||empty($password)){
			redirect('不能为空',t_login.php);
		}
		//4加载初始化数据库连接
		include_once 't_connect_database.php';
		//5对密码加密
		$password=md5($password);
		//6提取用户信息的sql语句
		$user="select * from pro_student where username='$username' and password=$password";
		//7执行sql
		$res=db_error($str);
		//8提取sql数据
		$user=mysql_fetch_assoc($res);
		//9判断用户信息
			if($user){
				redirect('','t_index.php');
			}else{
				redirect('','t_login.php');
			}
	}else{
		//加载表单
		include_once 't_login.html';
	}

t_public_function.php
<?php
//所有的公共内容
	 * 跳转函数
	 * @param1 string $msg提示信息
	 * @param2 string $url跳转目标
	 * @param3 int $time = 3跳转等待时间
	function redirect(){
		//提示
		echo $msg;
		//跳转等待
		header("Refresh:{$time};url={$url}");
		//脚本终止执行
		exit;
	 }


t_connect_database.php
<?php
	//数据库初始化操作
	//连接认证
	$link=@mysql_connect('localhost','root','root');
	//判断连接是否成功
	if(!$link){
		//数据库连接失败
		echo '数据库连接失败','<br/>';
		echo '错误编号为:'.mysql_errno().'<br/>';
		echo '错误原因为:'.iconv('gbk','utf-8',mysql_error()).'<br/>';
		//终止脚本执行
		exit;
	}
	//封装SQL错误检查函数
	function db_error($sql){
		//执行SQL
		$res=mysql_query($sql);
		if(!$res){
			//SQL执行失败
			echo 'SQL语句执行失败!','<br/>';
			echo '错误编号为:'.mysql_errno().'<br/>';
			echo '错误编号为:'.iconv('gbk','utf-8',mysql_errno()).'<br/>';
			//终止脚本执行
			exit;
		}
		//返回结果
		return $res;
	}
	//设置字符集
	db_error('set names utf8');
	//选择数据库
	db_error('use ms');


t_index.php
<?php
//系统后台首页
	//1初始化数据库连接
	include_once 't_connect_database.php';
	//2求出分页信息:每页显示记录数,页码
	$pagecount=2;
	$page=isset($_GET['page'])?$_GET['page']:1;
	//3求出总记录数	
	$sql="select count(*) as c from pro_studenet left join pro_class on c.c_id=c.id";
	//4执行查询SQL语句
	$res=db_error($sql);
	//5解析结果集
	$total=mysql_fetch_assoc($res);
	$count=isset($total['c'])?$total['c']:0;
	//6总页数
	$pages=ceil($count/$pagecount);
	//7上一页和下一页
	$prev=$page>1?$page-1:1;
	$next=$page<$pages?$page+1:$pages;
	//8求数据分页的起始位置
	$offset=($page-1)*$pagecount;
	//9查询所有学生信息	
	$sql="select * from pro_student s left join pro_class c on s.c_id=c.id limit {$offset},{$pagecount}";
	//10执行查询SQL语句
	$res=db_error($sql);
	//11解析结果集: 循环遍历
	$lists=array();
	while($row=mysql_fetch_assoc($res)){
		$lists[]=$row;
	}
	//12加载模板显示数据
	include_once 't_students.html';

t_students.html
<body>
	<table border='1' style="margin:100px auto;text-align:center">
		<tr>
			<th>序号</th>
			<th>姓名</th>
			<th>学号</th>
			<th>性别</th>
			<th>年龄</th>
			<th>身高</th>
			<th>班级</th>
			<th>教室</th>
			<th>操作</th>
		</tr>
		<?php foreach($lists as $k=>$stu)?>
		<tr>
			<td><?php echo $k+1?></td>
			<td><?php echo $stu['s_name'];?></td>
			<td><?php echo $stu['s_number']?></td>
			<td><?php echo $stu['s_gender']?></td>
			<td><?php echo $stu['s_age']?></td>
			<td><?php echo $stu['s_height']?></td>
			<td><?php echo $stu['c_name']?></td>
			<td><?php echo $stu['c_room']?></td>
			
			<td><a href="#">编辑</a><a href="#" onclick="return confirm('确定删除学生:<?php echo $stu['s_name']?>')">删除</a></td>		
		</tr>
		<?php endforeach;?>
		<tr>
			<td colspan='9'>
				<a href="t_index.php?page=1">首页</a>
				<a href="t_index.php?page=<?php echo $prev;?>">上一页</a>
				<a href="t_index.php?page=<?php echo $next;?>">下一页</a>
				<a href="t_index.php?page=<?php echo $pages;?>">末页</a>
			</td>
		</tr>
	</table>
</body>
t_login.php
//处理字符集

	//验证是否有提交数据

		//1接收POST信息

		//2加载公共跳转函数

		//3检验数据合法性

		//4加载初始化数据库连接

		//5对密码加密

		//6提取用户信息的sql语句

		//7执行sql

		//8提取sql数据

		//9判断用户信息
			////登录成功
			//调用跳转函数
			
			//登录失败: 重新登录
			//调用跳转函数
	
	//加载表单

t_public_function.php
//所有的公共内容
	 * 跳转函数
	 * @param1 string $msg提示信息
	 * @param2 string $url跳转目标
	 * @param3 int $time = 3跳转等待时间

	//提示

	//跳转等待

	//脚本终止执行

t_connect_database.php
//数据库初始化操作
//连接认证
//判断连接是否成功
	//数据库连接失败
	//终止脚本执行
//封装SQL错误检查函数
	//执行SQL
	//SQL执行失败
	//终止脚本执行
	//返回结果
//设置字符集
//选择数据库

t_index.php
//系统后台首页

	//1初始化数据库连接

	//2求出分页信息:每页显示记录数,页码

	//3求出总记录数
	//4执行

	//5解析结果集

	//6总页数

	//7上一页和下一页

	//8求数据分页的起始位置

	//9查询所有学生信息
	//10执行查询SQL语句

	//11解析结果集: 循环遍历

	//12加载模板显示数据




-- 随机生成姓名: 存储过程

	-- 定义数据字符串
	
	-- 定义变量保存结果

	-- 循环

		 -- 随机取姓
		 -- 随机取名

		 -- 合并

		 -- 插入到表

		 -- 循环变量变更

	 -- 循环结束
