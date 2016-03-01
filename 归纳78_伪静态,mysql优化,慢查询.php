网站静态化与mysql优化（二）
目录
一、伪静态	1
1、正则表达式的回顾	1
2、伪静态实现方式语法，详解。	5
（1）RewriteCond  重写条件	5
（2）RewriteRule :定义重写规则	6
3、入门案例：	6
4、伪静态在 ecshop里面的使用。	8
5、防盗链效果	9
6、[QSA]	10
二、 mysql优化	12
1、优化概述	12
2、分析需要优化的语句	12
（1）慢查询日志	13
（2）mysql里面的profiles机制，	14
3、mysql里面的索引，	16
（1）索引的分类：	16
（2）索引的创建	16
（3）索引的删除，	17
（4）索引的查看	18
4、创建索引的注意事项	18
5、索引的数据结构	19
（1）myisam引擎的索引	19
（2）innodb的索引的数据结构	19
6、explain(执行计划)工具使用	20
（1）语法分析：	21
（2）分析type列的值。	23


一、伪静态
1、正则表达式的回顾
	（1）要求取出练习的4个数字
	$reg = '/\d{4}/';

<?php
//参数1:正则表达式
//参数2:要查找的字符串
//参数3:存储匹配的结果,是一个数组
//preg_match($reg,$str,$res);//只匹配一个结果，匹配到第一个结果就结束
//preg_match_all($reg,$str,$res)函数//匹配所有的结果
//(1)取出连续4个数字
$str = 'saflafa1234safja2342falvalaw123sfwom111-222-111ksjowvaw888-999-888mvlajeowa';
$reg = '/\d{4}/';
preg_match_all($reg,$str,$res);
echo '<pre>';
print_r($res);
?>

	（2）要求取出，形式为：xxx-yyy-xxx的数据
	$reg = '/(\d)\1{2}-(\d)\2{2}-\1{3}/';
	正则中几个概念：
	子表达式：简单理解成用小括号括起的部分就是一个子表达式，
	捕获：把子表达式的内容，保存在内存。
	反向引用：圆括号的内容被捕获后，可以在这个括号后被使用。

<?php
//(2)取出形式为:xxx-yyy-xxx的数据
$str = 'saflafa1234safja2342falvalaw123sfwom111-222-111ksjowvaw888-999-888mvlajeowa';
$reg = '/(\d)\1{2}-(\d)\2{2}-\1{3}/';
preg_match_all($reg,$str,$res);
echo '<pre>';
print_r($res);
?>

	（3）要求取出，形式为：xxx-yzy-xxx的数据
	$reg = '/(\d)\1{2}-(\d)\d\2-\1{3}/';

<?php
//（3）取出形式为：xxx-yzy-xxx的数据
$str = 'saflafw123sfwom111-222-111ksjowvaw888-999-888mvlaj111-898-111eowa';
$reg = '/(\d)\1{2}-(\d)\d\2-\1{3}/';
preg_match_all($reg,$str,$res);
echo '<pre>';
print_r($res);
?>

	（4）贪婪模式与非贪婪模式
	如果有U即换成非贪婪匹配。
	贪婪：尽可能的多匹配。
	$reg = '/<a>(.*)<\/a>/';

<?php
//(4)贪婪匹配
$str = '<a>123</a>abc<a>456</a>';
$reg = '/<a>(.*)<\/a>/';
preg_match_all($reg,$str,$res);
echo '<pre>';
print_r($res);
?>

	非贪婪：尽可能的少匹配。
	$reg = '/<a>(.*)<\/a>/U';

<?php
//(5)非贪婪匹配
$reg = '/<a>(.*)<\/a>/U';
preg_match_all($reg,$str,$res);
echo '<pre>';
print_r($res);
?>

	（5）匹配中文
	$reg = '/[\x{4e00}-\x{9fa5}]/u';//u表示以uft8编码来匹配

<?php
//(6)字符簇
$str='35435safdsf你好我们是朋友sfresdf';
$reg='/[\x{4e00}-\x{9fa5}]/u';//u表示以utf8编码来匹配
preg_match_all($reg,$str,$res);
echo '<pre>';
print_r($res);
?>

	案例：结巴程序：
	$str=”我我要要….学学php编编….程”;
	直接变成：我要学习php编程
	提示：使用该函数来完成，preg_replace()

<?php
$str='我我要要...学学php编编...程';
//直接变成：我要学习php编程
//提示：使用该函数来完成，preg_replace()
$pattern = array();
$replacement = array();
$pattern[0] = '/\./i';
$replacement[0] = '';
echo preg_replace($pattern,$replacement,$str);//去点号
echo '<br/>';
$pattern[1] = '/(.)\1+/ui';
$replacement[1] = '${1}';
echo preg_replace($pattern, $replacement,$str);
?>

2、伪静态实现方式语法
	主要是三个配置：
	RewriteEngine  on
	RewriteCond
	RewriteRule
(1)RewriteEngine  on  
	重写引擎开关，一旦开启，所有的重写条件都生效。
(2)RewriteCond  
	重写条件，当达到什么条件时，完成重写。
	语法：
	RewriteCond  判断依据  条件表达式  [条件标志]

		判断依据可以使用服务器变量。服务器可以得到一些特定信息 
			HTTP_REFERER	REQUEST_FILENAME
		条件表达式，可以为如下形式：
			正则或特殊标识
				-f   表示是一个文件
				-d   表示是一个目录
				正则，正则表达式字符串
		条件标志： 
			[OR]	条件间的或者关系，当出现多个条件时，默认为并且的关系，条件应该是或者的关系下，可以使用OR来表示! 
			[NC]条件不区分大小写。条件匹配时不区分大小写
			[OR,NC] 
(3)RewriteRule 
	定义重写规则，哪个地址应该被重写到哪个目标地址。
	语法：
	RewriteRule 匹配地址   目标地址 [标识] 

匹配地址：所请求的地址，可使用正则匹配
目标地址：	所重写到的地址，可以使用反向引用！$N表示正则匹配到的第N个子表达式！
比如：RewriteRule   goods-id(\d+)\.html  goods.php?id=$1 
标识：
	[NC]		不区分大小写
	[QSA]	查询字符串追加，在目标地址已经具有get参数时，会将真实请求的get参数追后边。
3、入门案例：
如果访问的文件存在，则访问该文件，若不存在，则执行重写：
比如请求： www.demo.com/index.html    如果index.html 文件存在，则请求该文件，如果不存在执行重写规则。

实现步骤：
使用分布式文件来完成配置，在网站的根目录下面新建一个.htaccess的文件。
（1）在www.demo.com网站根目录下面新建一个.htaccess的文件。
通过编辑器另存为的方式来建立该文件。
（2）修改虚拟主机里面的配置。
	AllowOverride All
（3）在.htaccess文件里面的，具体的配置
<IfModule rewrite_module>
	RewriteEngine on
	RewriteCond %{REQUEST_FILENAME} !-f [NC]
	RewriteRule index.html index.php
</IfModule>

4、伪静态在 ecshop里面的使用。
	伪静态网址：http://www.myecshop.com/goods-36.html
	重写到该地址：http://www.myecshop.com/goods.php?id=36

	重写规则应该如何写？
	goods-(\d+).html       goods.php?id=$1

	让ecshop支持重写，把到商品详情页面的链接地址变成伪静态的。
		admin/index.php
		商店设置-->基本设置-->URL重写-->简单重写
	具体的配置：
	<IfModule rewrite_module>
		RewriteEngine  on
		#RewriteCond  %{REQUEST_FILENAME}  !-f  [NC]
		RewriteRule	 goods-(\d+).html  goods.php?id=$1
	</IfModule>
5、防盗链效果
（1）什么是盗链，
	原理图说明：
（2）如何判断请求的来源，使用 Referer请求头信息。
	Referer http://www.demo.com/index.html
（3）具体的配置，只允许本网站的页面来访问该图片，
<IfModule rewrite_module>
	RewriteEngine on
	RewriteCond	%{HTTP_REFERER}	!www.demo.com [NC]
	RewriteRule \.(jpg|jpeg|gif|png)	[F]#禁止访问	
</IfModule>
也可以把请求的图片重写到一个警示图片，
<IfModule rewrite_module>
	RewriteEngine on	
	RewriteCond	%{HTTP_REFERER}	!www.demo.com [NC]	
	RewriteRule \.(jpg|jpeg|gif|png)	2.jpg 		
</IfModule>
伪静态常用：把html地址，重写成php地址。

6、[QSA]
[QSA]	查询字符串追加，在目标地址已经具有get参数时，会将真实请求的get参数追加后边。
123.php代码：
<?php
	echo '<pre>';
	print_r($_GET);
?>
在重写规则里面没有带[QSA]
<IfModule rewrite_module>
	RewriteEngine on	
	RewriteRule  abc.php  123.php?key=value
</IfModule>
效果如下：
在重写规则里面添加[QSA]
<IfModule rewrite_module>
	RewriteEngine on	
	RewriteRule  abc.php  123.php?key=value [QSA]
</IfModule>
效果如下：

二、 mysql优化
1、优化概述
	(1)设计角度：存储引擎的选择，字段类型选择，范式
	(2)利用mysql自身的特性：索引，查询缓存，分区分表，存储过程，sql语句优化配置，
	(3)部署大负载架构体系：主从复制(读写分离)。
	(4)硬件升级：
2、分析需要优化的语句 
要分析的sql语句是执行速度比较慢的。查找执行速度比较慢的sql语句。找到后，具体分析。
（1）慢查询日志
是一种mysql提供的日志，记录所有执行时间超过某个时间界限的sql的语句。这个时间界限，我们可以指定。在mysql中默认没有开启慢查询，即使开启了，只会记录执行的sql语句超过10秒的语句。

如何开启慢查询日志：
打开mysql的配置文件，window下是：my.ini    linux系统下是my.cnf
d:/server/mysql/my.ini
	#mysql最大同时连接数
	max_connections=100
	#慢查询日志存储位置
	log-slow-queries='man-log'
	#慢查询时间界限
	long_query_time=0.5
 注意： 修改完成后，要重启mysql。

测试慢查询日志是否记录超过0.5秒的sql 语句。
benchmark(count,expr)函数可以测试执行count次expr操作需要的时间。

查看慢查询日志里面记录的sql语句的情况。
d:/server/mysql/data/man-log
在mysql的客户端进行查看慢查询日志的时间界限
cmd下
mysql>show variables like 'long_query_time';
也可以更改该时间界限，只对当前会话有效。
set long_query_time=1;
show variables like 'long_query_time';

（2）mysql里面的profiles机制，
该机制能够精确的记录执行sql语句的时间，能精确到小数点后8位
开启方式：直接在 mysql的客户端进行开启
set profiling=1|0(开启和关闭)
set profiling=1;
查看记录的时间：
show profiles;
注意：不使用时，最好将其关闭

php当mysql的客户端，php代码如何实现，
//开启
$sql="set profiling=1";
mysql_query($sql);
//查询
$sql="show profiles";
mysql_query($sql);

一个sql语句执行比较慢，大多数的原因是没有用到索引

3、mysql里面的索引，
	索引的作用：是用于快速定位实际数据位置的一种机制。
	索引在mysql中，是独立于数据的一种特殊的数据结构。
（1）索引的分类： 
普通索引： 
	利用特定的关键字，标识数据记录的位置（磁盘上的位置，盘号，柱面，扇面，磁道）。 
唯一索引： 
	限制索引的关键字不能重复的索引。 
主键索引： 
	限制索引的关键字不能重复，并且不能为NULL。（不能为NULL的唯一索引）。一个表中只允许有一个主索引。 
全文索引：
	索引的关键字，不是某个字段的值，而是字段值中有意义的词来作为关键字建立索引。
复合索引:
	如果一个索引（以上四种任何都可以），是依赖于多个字段创建的化，称之为复合索引。
	一个myisam表的对应的三个文件,表结构文件.frm   数据文件.myd  索引文件.myi
	添加一个普通索引后，索引文件会变大，

（2）索引的创建
第一种方式，在创建表时，一块创建索引。
create table user(
id int primary key auto_increment,
name varchar(12) not null comment '名称',
age tinyint unsigned not null comment '年龄',
email varchar(20) not null comment '邮箱',
intro varchar(128) not null comment '个人简介',
unique key (email) comment '唯一索引',
index (name) comment '普通索引',
fulltext index (intro) comment '全文索引',
index (name,age) comment '建立的联合索引'
)engine myisam charset utf8;
第二种方式：建完表后，以alter方式建立索引。
create table user2(
id int primary key auto_increment,
name varchar(12) not null comment '名称',
age tinyint unsigned not null comment '年龄',
email varchar(20) not null comment '邮箱',
intro varchar(128) not null comment '个人简介'
)engine myisam charset utf8;
alter table user2 add index (name),
add unique key (email),
add fulltext index (intro),
add index (name,age);

(3)索引的删除，
删除主键索引：alter table tableName drop primary key  
在删除主键索引时，要注意是否有auto_increment属性，如果有，则先要删除该属性，才能删除主键索引。

//不能直接删除主键
alter table user drop primary key;
//先去掉auto_increment属性
alter table user change id id int;
alter table user drop primary key;

删除其他索引：alter table  tablename   drop index 索引的名字 
注意：如果没有指定索引的名字则是使用该字段名称作为索引的名字的。

alter table user drop index email,drop index name,drop index name_2;
show create table user;

(4)索引的查看
show indexes from tableName; 
show index from tableName; 
show create table tableName; 
show keys from tableName;
desc tableName;

4、创建索引的注意事项 
（1）较频繁的作为查询条件字段应该创建索引
	select * from emp where empno = 1 
（2）唯一性太差的字段不适合单独创建索引，即使频繁作为查询条件
	select * from emp where sex = '男'
比如： is_best  is_new   is_hot    is_sale   is_delete 
（3）更新非常频繁的字段不适合创建索引
	select * from emp where logincount = 1 
	 比如登录的状态，
（4）不会出现在WHERE子句中字段不该创建索引

5、索引的数据结构
（1）myisam引擎的索引
索引的节点中存储的是数据的物理地址（磁道和扇区）
在查找数据时，查找到索引后，根据索引节点中的物理地址，查找到具体的数据内容 

（2）innodb引擎的索引的数据结构
innodb的主键索引文件上 直接存放该行数据,称为聚簇索引，非主索引指向对主键的引用
myisam中, 主索引和非主索引,都指向物理行(磁盘位置).
注意: innodb来说, 
1: 主键索引 既存储索引值,又在叶子中存储行的数据
2: 如果没有主键, 则会Unique key做主键 
3: 如果没有unique,则系统生成一个内部的rowid做主键. 
4: 像innodb中,主键的索引结构中,既存储了主键值,又存储了行数据,这种结构称为”聚簇索引” 

myisam引擎的表的数据是按照插入的顺序显示的。
innodb引擎的表的数据是按照主键的顺序显示的。

6、explain(执行计划)工具使用 

主要用于分析sql语句的执行情况（并不执行sql语句）得到sql语句是否使用了索引，使用了哪些索引。 
语法：explain  sql语句\G   或 desc sql语句\G 
在mysql之前的版本中，explain只支持select语句，但是在最新的5.6版本中，它支持 explain update/delete了。 


做实验创建几张表：
create table user( 
    id int primary key auto_increment, 
    name varchar(32) not null default '', 
    age tinyint unsigned not null default 0, 
    email varchar(32) not null default '', 
    classid int not null default 1 
)engine myisam charset utf8; 
insert into user values(null,'xiaogang',12,'gang@sohu.com',4), 
(null,'xiaohong',13,'hong@sohu.com',2), 
(null,'xiaolong',31,'long@sohu.com',2), 
(null,'xiaofeng',22,'feng@sohu.com',3), 
(null,'xiaogui',42,'gui@sohu.com',3); 
创建一个班级表：
create table class( 
    id int not null default 0, 
    classname varchar(32) not null default '' 
)engine myisam charset utf8; 
insert into class values(1,'java'),(2,'.net'),(3,'php'),(4,'c++'),(5,'ios'); 
（1）语法分析：
explain  sql语句：
	select_type:SIMPLE
	表示查询的类型，此处是一个简单的查询
	table :user;
	表示要查询的表。
	type列：是指查询的方式，非常重要，是分析“查数据过程”的重要依据。 
	可能的值：all   index   range   ref    const 
	possible_key:可能用到的索引
	注意：系统估计可能用的几个索引，但最终，只能用1个。 
	key:最终用的索引。 
	key_len:使用的索引的最大长度。 
	rows:是指估计要扫描多少行。 
	extra: 
		using index :是指用到了索引覆盖，效率非常高 
		using where:是指光靠索引定位不了，还得where判断一下。 
		using temporary:是指用上了临时表，group by 与order by不同列时，或grop by,order by 别的表的列。 
		using filesort:文件排序（文件可能在磁盘，也可能在内存）


（2）分析type列的值。
	all：是扫描所有的数据行，性能最差，一般是没有添加索引，或没有使用到索引，
	index:比all性能稍好一点，是指要扫描所有的索引节点。
		出现index, 则说明只在索引文件中查找。 
		（1）索引覆盖的查询情况下，能利用上索引，但是又必须全索引扫描。 
		（2）是利用索引来排序，但只能取出索引所在的列数据。  
	range:意思是查询时，能根据索引做范围扫描。 
	ref:是指，通过索引列，可以直接引用到某些数据行。
	const,system,null这3个分别指查询优化到常量级别，甚至不需要查找时间。
	一般按照主键来查询时，易出现 const,system 
	或者直接查询某个表达式，不经过表时，出现null