网站静态化与mysql优化（三）
目录
一、索引的使用细节	1
1、全值匹配	1
2、范围匹配（<= >= between and）	1
3、独立的列	2
4、左值匹配	3
5、or运算都有索引	4
6、多列索引：	5
7、当取出的数据量超过表中数据的20%，优化器就不会使用索引，而是全表扫描。	9
二、索引覆盖	9
三、分表技术	10
1、垂直分割。	10
2、水平分割。	11
四、翻页优化和延迟缓存	12
五、锁机制讲解	16
1、mysql里面的锁介绍	17
2、mysql表锁的演示：	17
3、mysql行锁的演示。	19
4、锁机制在php代码里面演示；	20
六、数据碎片与维护	21
七、表分区	22
1、基本概念	22
2、创建表分区的语法：	23
3、分区的类型	24
4、分区表的限制；	25
八、列类型的选择	26

一.索引的使用细节
1.全值匹配
	条件字段使用'='
	desc select * from user where name='susan'\G
2.范围匹配（<= >= between and） 
	desc select * from user where id>8\G
	desc select * from user between 2 and 4\G
3、独立的列 
	是指索引列不能是表达式的一部分，也不能是函数的参数 
	//没有用到索引
	desc select * from user where id+1>9\G
	//用到索引
	desc select * from user where id>3+5\G
4、左值匹配 
	在使用like(模糊匹配)的时候，在左边没有通配符的情况下，才可以使用索引。
	在mysql里，以%开头的like查询，用不到索引。
	desc select * from user where name like '%abc'\G
	desc select * from user where name like 'abc%'\G
	//此处的%是一个普通的字符,不是通配符
	desc select * from user where name='%abc'\G

5、or运算都有索引 
	如果出现OR(或者)运算，要求所有参与运算的字段都存在索引，才会使用到索引。

6、多列索引： 
	对于创建的多列(复合)索引，只要查询条件使用了最左边的列，索引一般就会被使用。 
	因为联合索引是需要按顺序执行的，比如c1234组合索引，要想在c2上使用索引，必须先在c1上使用索引，要想在c3上使用索引，必须先在c2上使用索引，依此。 
	
	分析：对name 和age 和email分别建立独立索引：最终只能使用到一个索引。
	desc select * from user where name='abc' and age=12 and email='afdf'\G
	desc select * from user where age=12 and email='afdf'\G
	
	如果对name和age和email 建立了联合索引，在按照建立索引的顺序使用时，都用到了索引。
	desc select * from user where name='abc' and age=12 and email='afdf'\G
	结论：如果有多个条件经常出现在where条件中，则可以对条件字段建立联合索引。

7、当取出的数据量超过表中数据的20%，优化器就不会使用索引，而是全表扫描。
	//没用到索引
	desc select * from user where id>3\G
	//使用索引
	desc select * from user where id>9\G

二、索引覆盖
索引覆盖是指：如果查询的列恰好是索引的一部分，那么查询只需要在索引文件上进行，不需要回行到磁盘再找数据，这种查询速度非常快，称为“索引覆盖” 

索引覆盖就是，我要在书里 查找一个内容，由于目录写的很详细，我在目录中就获取到了，不需要再翻到该页查看。
alter table user add index (name,age);
//索引覆盖
desc select name from user where age=12\G
desc select name,age from user\G
如果在一个 sql 语句中，经常查询某些列，就可以把某些列建立一个联合索引，查询时就会用到索引覆盖，速度更快。

三、分表技术
1、垂直分割。
典型案例：把不经常查询的字段单独分割出来，形成一张新表。
扩展案例：
比如一个网站，需要存储如下信息，
电影信息
图片信息
音乐信息
软件信息
水果信息
方案1：可以建立一张大表，表中有各种类型的字段，
比如以上信息：
id  title   导演   主演   作词  作曲   语言  出版  下载地址    产地  甜度   
方案2:可以针对不同的类型建立不同的表。
比如电影表
movie 表
id  title   addtime   viewcount    导演   主演    地区    剧情   上映时间 
music表
id  title   addtime   viewcount    作词   作曲    语言   原唱
soft表
id   title  addtime   viewcount    语言   作者    下载地址……
方案3：使用内容主表+附加表。
内容主表：用于存储各种类型的公有的字段信息，
附加表：用于存储各种类型独有的一些字段信息。
当查询公有的信息时就无需连表查询，只有当查询具体数据的时候，需要连表。
后面讲的dedecms的表的设计就是如此。

内容主表的记录数  ====   各个附加表记录数的之和。

2、水平分割。
比如用户注册，存储用户的表，可以分表，
原来：user表，
分表后，形成三张表，表名为：
user_0     user_1    user_2;
在用户注册时，用户如何存储呢、用户到底存储到哪张表呢？
需要单独一张表比如 user表，该表就一个字段，用于生成用户的id,
根据id与分表的数量进行取模运算，比如此处是3个表，
取模的值如果为0则存储到user_0表里面，
如果取模的值为1则存储到user_1表里面
如果取模的值为2则存储到user_2表里面

四、翻页优化和延迟缓存 
/************1.翻页优化***************/
limit offset,N  当offset非常大时，效率极低。 
原因是： mysql并不是跳过offset行，然后单取N行。而是取offset+N行， 
返回时，放弃前offset行，返回N行。效率较低，当offset越大时，效率越低
优化方式：
（1）非技术手段限制分页，比如百度翻页一般不会超过70页，谷歌不会超过40页。
//计算总的记录数
$total = 
//定义每页显示数量
$perpage  = 10;
//计算总的页数
$pagecount  = min(ceil($total/$perpage),70);

（2）不用offset，用条件查询：
insert into user values (null,'Susan','20','susan@qq.com','1'),(null,'Cain','21','cain@qq.com','2'),(null,'Wilson','22','wilson@qq.com','3');
$sql=”select * from user limit 10,10”;
$sql=”select * from user where id>10 limit 10”
key:NULL
rows:327913

key:PRIMARY
rows:163956
缺点：如果数据有被删除，则取出的数据结果会不一致。

解决方案：
解决：数据不进行物理删除（可逻辑删除）
最终在页面上显示数据时，逻辑删除的条目不显示即可。
（一般来说，大网站的数据都是不物理删除的，只做逻辑删除，比如is_delete=1）

（3）非要物理删除,还要用offset精确查询,还不限制用户分页,怎么办?
我们现在必须要查，则只查索引，不查数据，得到id 
再用id去查具体条目，这种技巧就是延迟索引。 
第一步：
//select * from user limit 10000,10 （没有用到索引）
//取出数据的id   
select  id from user limit  10000,10(用到索引覆盖)
第二步：根据取出 id再查具体的数据，因为id是主键,查询比较快。
因此使用一个连接查询，就可以，我们使用内连接。inner join   left join  right join
mysql>select * from user a inner join (select id from user limit 250000,10) as tmp on tmp.id=a.id;



五、锁机制讲解
比如有如下操作：
（1）从数据库中取出id的值，（2）把这个值加1，（3）在把该值存回到数据库。
假如该id初始值为100；
如果有两个用户同时操作。
第一个用户 ：
id=100
100+1
id=101
第二个用户：
id=100
100+1
id=101
经过两个用户操作数据库，值应该为102才对，
假如是一个购物网站，库存还剩1件，有两个用户同时购买1件商品，
mysql中的锁：同一个时间只有一个人可以获得锁，其他人只能阻塞等待第一个人释放锁。
第一个用户                             第二个用户
get lock（获得锁）                        waiting。。。。
id=100                                   waiting。。。。
100+1                                   waiting。。。。
id=101                                   waiting。。。。
unlock(释放锁)                           get lock(获取锁)
																												    id=101
										id+1
										id=102
								       unlock(释放锁)

1、mysql里面的锁介绍
mysql 的锁有以下几种形式：
表级锁：开销小，加锁快，发生锁冲突的概率最高，并发度最低。myisam引擎属于这种类型。
行级锁：开销大，加锁慢，发生锁冲突的概率最低，并发度也最高。innodb属于这种类型。 

2、mysql表锁的演示：
（1）添加读锁
对myisam表的读操作（加读锁），不会阻塞其他进程对同一表的读请求，但会阻塞对同一表的写请求。只有当读锁释放后，才会执行其他进程的操作。 

添加读锁语法：lock  table 表名 read     
释放锁的语法；unlock tables

use test;
alter table user engine myisam;
表1:select * from user;
lock table user read;
表2:update user set age=33 where id=2;//添加读锁后,其他进程修改操作时,处于阻塞状态

注意：当前进程只能操作被锁定的表，如果想要锁定多张表，可以使用如下语句；
 lock table tablename1  read, tablename2 read;

（2）添加写锁
对myisam表的写操作（加写锁），会阻塞其他进程对同一表的读和写操作，只有当写锁释放后，才会执行其他进程的读写操作。

表1:lock table user write;
	update user set age=66 where id=5;
	select * from user;
表2:update user set age=33 where id=2;


总结：
read:所有人都只可以读，只有释放锁之后才可以写。 
write:只有锁表的客户可以操作这个表，其他客户读都不能读。
缺点：阻塞。有些功能需要锁多张表，而有些表整个网站都要用，一旦锁定，会让整个网站处在阻塞状态

3、mysql行锁的演示。
innodb存储引擎是通过给索引上的索引项加锁来实现的，这就意味着：只有通过索引条件检索数据，innodb才会使用行级锁，否则，innodb使用表锁。 
语法： 
begin;
执行语句； 
commit; 

表1:begin;
select * from user;
update user set age=45 where id=1;
commit;//解锁

表2:update user set age=11 where id=2;//没有被阻塞,因为操作的是不同行
update user set age=11 where id=1;//操作同一行的数据,被阻塞,使用的是行锁

4、锁机制在php代码里面演示；
建立一个表，原始数据是100，
create table t8(
id int
)engine myisam charset utf8;
insert into t8 values(100);
模拟并发进行测试；
比如模拟50个并发，使用apache里面有一个ab.exe工具，可以使用该工具进行模拟并发
ab.exe工具的语法：
ab.exe –n 总的请求数量 –c并发数   网页的地址；
D:\server\apache\bin>ab.exe -n 50 -c 50 http://www.php.com/project20151106/index.php
php代码：
<?php
$conn = mysql_connect('localhost','root','root');
mysql_query('use test');
mysql_query('set names utf8');

mysql_query("lock table t8 write");

$sql = "select id from t8";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);
$id = $row['id']+1;
$sql = "update t8 set id=$id";
mysql_query($sql);

mysql_query("unlock tables");

echo 'ok';
?>

六、数据碎片与维护 
在长期的数据更改过程中，索引文件和数据文件，都将产生空洞，形成碎片，我们可以通过一个nop操作（不产生对数据实质影响的操作）来修改表，

create table t9(id int,name varchar(12))engine myisam charset utf8;
insert into t9 values(1,'susan'),(2,'cain'),(3,'wilson');
insert into t9 select * from t9;

执行如下操作： delete from t9 where id=1;  操作完成后，数据应该减少三分之一才对。
执行完成该语句后，发现数据文件d:/server/mysql/data/test/t9.MYD并没有减少三分之一，这样在数据文件中，就会产生了一个垃圾的空洞的数据文件，因此需要整理。

整理方法：
第一种方法：
	执行：alter table tableName engine 原来的存储引擎。
第二种方法：
	执行：optimize  table  tableName;

注意：修复表的数据及索引碎片，就会把所有的数据文件重新整理一遍，使之对齐，这个过程，如果表的行数比较大，也是比较耗费资源的操作，所以，不能频繁的修复。
如果表的update操作很频繁，可以按周月来修复。 

七、表分区
1、基本概念
基本概念，把一个表，从逻辑上分成多个区域，便于存储数据。
采用分区的前提：数据量非常大。

比如一个用户表，想分成4个区域，如何分呢？
用户的id  1   到1000  分到  东区
用户的id  1001   到2000  分到 南区
用户的id  2001   到3000  分到 西区
用户的id  大于3001   的 分到 北区

2、创建表分区的语法：
create  table user (
	//创建表的语句
)engine myisam charset utf8
partition by 分区类型（分区的关键字）(
	//分区的项
);
比如前面规划的案例：
create table user(
id int primary key,
name varchar(32) not null
)partition by range (id)(
	partition east values less than (1000),
	partition south values less than (2000),
	partition west values less than (3000),
	partition north values less than MAXVALUE
);



3、分区的类型
list :条件值为一个数据列表。 
通过预定义的列表的值来对数据进行分割
例子：假如你创建一个如下的一个表，该表保存有全国20家分公司的职员记录，这20家分公司的编号从1到20.而这20家分公司分布在全国4个区域，如下表所示：
职员表：
id  name   store_id(分公司的id) 
北部    1,4,5,6,17,18 
南部    2,7,9,10,11,13 
东部    3,12,19,20 
西部    8,14,15,16 
create table p_list( 
    id int, 
    name varchar(32), 
    store_id int 
)partition by list (store_id)( 
    partition p_north values in (1,4,5,6,17,18), 
    partition p_east values in(2,7,9,10,11,13), 
    partition p_south values in(3,12,19,20), 
    partition p_west values in(8,14,15,16) 
); 
测试是否用到了分区：
explain partitions select * from p_list where store_id=20\G
注意：在使用分区时，where后面的字段必须是分区字段，才能使用到分区。

range（范围） 
这种模式允许将数据划分不同范围。例如可以将一个表通过年份划分成若干个分区
create table p_range( 
    id int, 
    name varchar(32), 
    birthday date 
)partition by range (month(birthday))( 
    partition p_1 values less than (3), 
    partition p_2 values less than(6), 
    partition p_3 values less than(9), 
    partition p_4 values less than MAXVALUE 
); 
less than   小于等于； 
MAXVALUE可能的最大值 

4、分区表的限制；
只能对数据表的整型列进行分区，或者数据列可以通过分区函数转化成整型列
最大分区数目不能超过1024 
如果含有唯一索引或者主键，则分区列必须包含于所有的唯一索引或者主键

不支持外键
不支持全文索引（fulltext）
按日期进行分区很非常适合，因为很多日期函数可以用。但是对于字符串来说合适的分区函数不太多 
create table p_list1(
	id int,
	name varchar(32),
	store_id int,
	primary key(id,store_id)
)partition by list (store_id)(
	partition p_east values in(2,7,9,10,11,13),
	partition p_south values in(3,12,19,20),
	partition p_west values in(8,14,15,16),
	partition p_north values in(1,4,5,6,17,18)
);
八、列类型的选择 
1、在精度要求高的应用中，建议使用定点数来存储数值，以保证结果的准确性。 
要用decimal不要使用float
mysql> create table t1(price float(9,2),dprice decimal(9,2));
mysql> insert into t1 values(1234567.55,1234567.55); 
select * from t1;
+-----------------------+
|price 		| dprice    |
+-----------+-----------+
|1234567.50 | 1234567.55|
+-----------------------+

2、录入手机号带来的问题， 
使用char(11)会占用较多的字节，gbk占用2字节*11，utf-8占用3*11， 
可以使用bigint,宽度是20，只占用8个字节。

3、 ip地址也可以采用int整型。
使用函数进行转换：
inet_aton()：把ip地址转换成整数 
inet_ntoa();把整数转换成ip地址。 
IPv4存储为int型 
PHP：ip2long(),long2ip()
MySQL: inet_aton(), inet_ntoa();

4、根据需求选择最小整数类型。比如用户在线状态：离线，在线，离开，忙碌，隐式等，可以采用0,1,2,3,来表示。 

5、避免字段内容为null，原因：null不利于索引，要用特殊的字节来标注，在磁盘上占据的空间其实更大。 
NULL的判断只能用is null,is not null 
NULL 影响查询速度,一般避免使值为NULL 
mysql> create table t3(name char(1) not null default '')engine myisam; 
Query OK, 0 rows affected (0.01 sec) 
 
mysql> create table t4(name char(1))engine myisam; 
Query OK, 0 rows affected (0.01 sec) 