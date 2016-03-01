为大型网站提速--memcached缓存技术
目录
一、memcache 的介绍	1
	1、memcached基本概念	1
	2、基本原理	2
	3、与mysql 进行比较	2
二、memcache的安装	3
三、客户端操作memcache	5
	1、设置数据	7
	2、删除数据	9
	3、其他命令	9
	4、状态命令：	10
	5、缓存时间的设置的讨论	10
四、php操作 memcache	11
	1、先安装memcache的扩展，让php支持。	11
	2、入门案例：	12
五、php数据类型存储memcache探讨	13
六、案例操作	15
七、案例扩展，	16
八、memcache在tp框架里面使用	18
九、memcache的分布式存储	19
	1、session数据入memcache的问题：	19
	2、分布式系统具体的配置	21
十、其他事项	22
	1、memcache适合于存储哪些数据	22
	2、memcache的安全性	23
	3、数据过期的问题	23
	4、数据存储空间满了，还能否存储数据呢？	23
	5、如果需要设置许多缓存项时，失效时间最好不要设置为相同的。	24


一、memcache 的介绍
1、memcached基本概念
（1）Memcached是danga的一个项目，最早是LiveJournal 服务的，最初为了加速 LiveJournal 访问速度而开发的，后来被很多大型的网站采用。 官方网站: www.danga.com  和 memcached.org 
（2）Memcached是一个高性能的分布式的内存对象缓存系统，目前全世界不少人使用这个缓存项目来构建自己大负载的网站，来分担数据库的压力，通过在内存里维护一个统一的巨大的hash表，它能够用来存储各种格式的数据，包括图像、视频、文件以及数据库检索的结果等。简单的说就是将数据调用到内存中，然后从内存中读取，从而大大提高读取速度。
2、基本原理



3、与mysql 进行比较
（1）与mysql一样，是一个c/s架构的软件。
（2）mysql里面的数据，是存储到磁盘里面的，memcache里面的数据是存储到内存里面的，一旦断电，服务器重启，则会丢失数据。
（3）要使用mysql则先要创建数据库，再创建表，以及表结构。在memcache里面数据的存储是键值对。可以理解成两列的表， key与value
name   小刚
age     12
email    xiaogagn@sohu.com
二、memcache的安装
方式一：直接使用，无需安装，（在开发时推荐使用）

（1）把软件拷贝到指定位置，一般和其他的安装软件（比如apache等）在同级目录下面，主要是便于管理。
mem window下安装/memcached-1.2.6-win32-bin/memcached.exe
复制到D:/server/下
（2）以cmd的方式，运行memcache
D:\server>memcached -h
D:\server>memcached -p 11211 -m 64
启动后，该窗口不要关闭，一旦关闭，则服务就停止了。
netstat -an #查看当前环境下启动的服务及端口号

方式二：把 memcahce安装成window的一个服务，（在生产环境中推荐使用）
通过查看memcached 的帮助。
D:\server>memcached -h
注意：在把 memcache安装成window的一个服务时，要以管理员的方式启动cmd.
d:
cd server
memcached -d install	//安装成window的一个服务

查看服务是否安装成功：
计算机右键->管理->服务和应用程序->服务

安装可能失败的原因： 
（1） 如果你是用win7,win8系统，他对安全性要求高，因此，需要大家使用管理员的身份来安装和启动. 具体是 程序开始===>所有程序==》附件==》cmd(单击右键，选择以管理员的身份来执行) 
（2）存放memcache.exe 目录不要有中文或者特殊字符
（3） 安装成功，但是启动会报告一个错误信息，提示缺少xx.dll ，你可以从别的机器拷贝该dll文件，然后放入到system32下即可. 
（4）如果上面三个方法都不可以，你可以直接这样启动mem 
cmd>memcached.exe  -p  端口 【这种方式不能关闭窗口 

三、客户端操作memcache
使用，telnet连接memcache服务器端。
memcache的默认端口号是11211.
语法：telnet    ip地址   端口号
telnet localhost 8888


注意：telnet客户端无法使用的解决方案：
控制面板->卸载程序->打开或关闭Windows功能->Telnet客户端


1、设置数据
（1）添加数据，
	语法：add  key  是否压缩（0|1）  缓存时间  数据的长度
		key ：键的名称
		是否压缩：0表示不压缩，1表示要压缩，压缩的目的让数据变小，存储更多的数据。
		缓存时间：失效时间，表示过了该时间数据就失效。
		数据的长度：单位是字节，
	注意：在使用add添加数据时，如果该键已经存在，则添加失败，不会覆盖。

add name 0 60 5
Susan

（2）修改数据
	replace key  0|1  缓存时间  数据的长度
	注意：如果键不存在，则修改失败。

replace name 0 60 4
Cain

（3）设置数据
	set key  0|1  缓存时间  数据长度
	如果键已经存在，则是修改，如果键不存在，则是添加。

set name 0 60 6
Wilson

2、删除数据
	语法：delete key

delete name

flush_all 是删除所有的缓存项

flush_all

3、其他命令
incr : 增加指定的值
	语法：incr key number

	incr age 10

decr : 减少指定的值
	语法：decr key number

	decr age 10

比如有1000件商品，需要在60秒内抢购。
一开始就设置number的值为1000   ，当用户抢购时执行decr number ,如果执行后返回的值大于0则说明抢购成功，
4、状态命令：
stats 
curr_items 4	当前存在的缓存项的个数
total_items 21	从启动到现在总共的键的数量
bytes 224		缓存项占用的空间
cmd_get 45		执行get查询的次数
get_hits 25		在执行get查询时,有25次能够获取数据成功

通过查看状态的参数，主要是计算命中率，
get_hits/cmd_get  ====得出一个命中率，命中率越高越好，如果命中率比较低，则需要调整缓存项。

5、缓存时间的设置的讨论
在设置缓存时间有两种设置方式：
（1）使用秒数（时间间隔）<=2592000
（2）使用到期的时间戳  该时间戳必须要大于当前的时间戳才有效。
在设置时，设置的参数都是整数，如何区分是时间戳还是时间间隔呢？
在使用时间间隔设置缓存时间时，有一个限制的，不能超过30天，30*24*3600秒2592000

比如如果要把一个缓存项失效时间设置为2个月如何设置呢？
time()+30*24*3600*2

比如要把一个缓存项失效时间设置为10天如何设置？
有两种设置方案：
	使用时间间隔：  10*24*3600
	使用时间戳： time()+10*24*3600

比如如果一个缓存项失效时间设置为123489,该值是时间戳还是时间间隔

如果该值小于等于2592000则是时间间隔，如果大于该值是时间戳，

注意：如果缓存时间设置为0，表明此数据永不过期

四、php操作 memcache
1、先安装memcache的扩展，让php支持。
（1）准备php支持的扩展文件，要注意要和php的版本对应。
php5.4-memdll/memcache.dll
（2）把扩展文件拷贝到php的安装目录下面的ext目录里面。
d:/server/php/ext/
（3）打开php.ini的配置文件，引入扩展。
d:/server/php/php.ini
extension=php_memcache.dll
（4）要重启apache
（5）使用phpinfo 函数测试是否引入成功。

2、入门案例：
<?php
//实例化一个memcache的类
$mem = new Memcache();
//连接memcache的服务器
$mem->connect("localhost",8888);
//添加数据
//$mem->set(key,value,是否压缩,失效时间);
$mem->set("name",'Susan',MEMCACHE_COMPRESSED,3600);
//$mem->add() $mem->replace() $mem->delete()
//$mem->close();//关闭连接
$res = $mem->get('name');
var_dump($res);
?>


五、php数据类型存储memcache探讨
标量类型：整型   浮点型  布尔  字符串

<?php
//实例化一个memcache的类
$mem = new Memcache();
//连接memcache的服务器
$mem->connect("localhost",8888);
$mem->add('int',100,0,3600);
$mem->add('float',10.20,0,3600);
$mem->add('boolean',true,0,3600);
$mem->add('string',"welcome to guangzhou",0,3600);
?>

<?php
$mem->connect("localhost",8888);
//获取数据
var_dump($mem->get('int'));
echo '<br/>';
var_dump($mem->get('float'));
echo '<br/>';
var_dump($mem->get('boolean'));
echo '<br/>';
var_dump($mem->get('string'));
echo '<br/>';
?>

说明标量类型是可以存储到memcache 里面的，都是以字符串的形式存储的，最后输出也变成了字符串。
非标量类型的存储：数组  对象  null  资源

<?php
//实例化一个memcache的类
$mem = new Memcache();
//连接memcache的服务器
$mem->connect("localhost",8888);
//把非标量的数据给存储到memcache里面
$mem->set("array",array('apple','orange','tree'),0,3600);
class dog{
}
$dog = new dog();
$dog->name='哮天犬';
$dog->age=400;
$mem->set('object',$dog,0,3600);
$conn = mysql_connect('localhost','root','root');
$mem->set('resource',$conn,0,3600);
$mem->set('null',null,0,3600);
echo 'ok';
?>

取出数据的代码：

<?php
//实例化一个memcache的类
$mem = new Memcache();
//连接memcache的服务器
$mem->connect("localhost",8888);
//获取数据
var_dump($mem->get('array'));
echo '<br/>';
var_dump($mem->get('object'));
echo '<br/>';
var_dump($mem->get('resource'));
echo '<br/>';
var_dump($mem->get('null'));
echo '<br/>';
?>

数组 对象 资源  null在 memcache里面存储的形式。


说明：数组 对象 资源 是以序列化之后的结果存储到memcache里面的。
但是在取出数据时，又自动反序列化之后显示的。
序列化与反序列化的过程是由memcache的客户端完成的，无需我们自己干预。
说明则memcache里面存储的数据，是以字符串的形式来存储的。
注意：不能把资源类型存储到memcache里面，因为在取出资源类型时，把资源类型变成了整型。在实际应用中，存储数组的情况居多。
六、案例操作
想把一个sql语句的执行结果，给缓存到memcache里面。
要注意说明的，sql语句执行的结果数据要小于1MB。
在memcache 里面，键与值是有要求的，
	键的长度要小于250字节。
	数据值的大小要小于1MB。

<?php
//实例化一个memcache的类
$mem = new Memcache();
//连接memcache的服务器
$mem->connect("localhost",8888);
//缓存一个sql语句的执行结果
$sql = "select * from cetsix limit 10";
$key = md5($sql);//返回一个字符串,就使用该字符串作为键
$data = $mem->get($key);//获取memcache里面的数据
if(!data){
	echo 'a';
	$conn = mysql_connect('$localhost','root','root');
	mysql_query('use php');
	mysql_query('set names utf8');
	$data = array();
	$res = mysql_query($sql);
	while($row=mysql_fetch_assoc($res)){
		$data[]=$row;
	}
	//把查询出的数据,给存储到memcache里面
	$mem->set($key,$data,0,3600);
}
?>

七、案例扩展，
把新闻内容存储到memcache里面。
新闻列表页面newslist.php

<?php
//取出新闻的列表页面
$conn = mysql_connect('localhost','root','root');
mysql_query('use php');
mysql_query('set names utf8');
$sql = "select * from cetsix limit 10";
$data = array();
$res = mysql_query($sql);
while($row = mysql_fetch_assoc($res)){
	$data[] = $row;
}
?>
<body>
<h1>新闻列表页面</h1>
<table>
	<tr>
		<td>新闻标题</td>
		<td>新闻详情</td>
	</tr>
	<?php foreach($data as $v){?>
	<tr>
		<td><?php echo $v['word']?></td>
		<td><a href="newinfo.php?id=<?php echo $v['id']?>">新闻详情</a></td>
	</tr>
	<?php }?>
</table>
</body>

新闻详情页面newsinfo.php
<?php

$mem = new Memcache();//实例化一个memcache对象

$mem->connect("localhost",8888);//连接memcache的服务器
$id = $_GET['id']+0;
$key = 'new_'.$id;//构建存储到memcache里面的键
$info = $mem->get($key);//使用该键从memcache里面获取数据
//判断是否获取数据成功,如果成功,就直接显示数据
//如果没有成功,则查询数据库,并写入到memcache缓存里面
if(!$info){
	//连接数据库,查询数据,并写入到缓存里面
	$conn = mysql_connect('localhost','root','root');
	mysql_query('use php');
	mysql_query('set names utf8');
	$sql = "select * from cetsix where id=$id";
	$res = mysql_query($sql);
	$info = mysql_fetch_assoc($res);
	$mem->set($key,$info,0,3600);
}
?>

思考：如果新闻内容修改了如何办？修改完成新闻后要清空缓存即可。
八、memcache在tp框架里面使用
分两步：
（1）初始化memcache
S(array(
	‘type’=>’memcache’,
	‘host’=>’ip地址’,
	‘port’=>端口号
));
（2）具体的操作
获取数据；
$data = S(key);
设置数据：
S(key,value,失效时间);
清空数据：
S(key,null)

public function index(){
	//初始化
	$(array(
		'type'=>'memcache',
		'host'=>'localhost',
		'port'=>'8888'
	));
	//设置数据集
	$('username','Susancain',3600);
	//获取数据
	$data = S('news_4');
	//清空数据
	$('username',null);
}
九、memcache的分布式存储
1、session数据入memcache的问题：
应用图示：

具体的配置：
打开php.ini 配置文件：
;修改session存储方式为memcache
session.save_handler = memcache
;制定session信息存储的位置
session.save_path = "tcp://localhost:11211"


可以使用函数ini_set()函数改变php.ini 的配置，只对当前页面有效。
ini_set(“session.save_handler”,’memcache’);
ini_set(‘session.save_path’,’tcp://ip地址1:端口,tcp://ip地址2:端口’)

注意：使用session的方式和以前是一样的。
session信息存储到memcache里面是以sessionid为键的，失效时间与session相同的。

<?php
ini_set('session.save_handler','memcache');
ini_set('session.save_path','tcp://localhost:8888');
//使用session操作了
session_start();
$_SESSION['name'] = 'Wilson';
echo session_id();
echo '<br/>';
echo 'ok';
?>

2、分布式系统具体的配置
注意两点：
（1）要实现分布式配置需要两台以上memcache服务器（2）使用一个算法，该算法决定数据向哪台服务器存储。


（2）配置实现：
设置数据
<?php
$mem = new Memcache();
//连接多台memcache服务器
$mem->addServer('localhost',8888);
$mem->addServer('192.168.0.171');
$mem->set('name','Susancain',0,3600);
$mem->set('age',20,0,3600);
echo 'ok';
?>
取出数据：
<?php
//取出数据
$mem = new Memcache();
$mem->addServer('localhost',8888);
$mem->addServer('192.168.0.171');

var_dump($mem->get('name'));
echo '<br/>';
var_dump($mem->get('age'));
?>


注意：在设置数据与取出数据是，memcache服务器添加顺序与个数要一致。原因是使用的算法是取模算法，
在设置数据或取出数据时，根据键名转换成一个数字与服务器的个数进行取模。取模的结果就决定向哪台服务器存储数据。
注意：memcache服务器的算法是取模算法，是内置的，我们自己无需干预。
十、其他事项
1、memcache适合于存储哪些数据
（1）安全性不是很高的数据，丢失无所谓的数据，因为memcache服务器，一旦重启或关机，则会丢失所有的数据。
（2）查询比较频繁的数据，比如热点新闻，等等。
（3）更新比较频繁的数据，比如用户的在线状态。
（4）一个键值，数据量不要很大，要小于1MB的数据。
2、memcache的安全性
memcache没有任何的安全认证（比如用户名与密码），因为是主要做缓存使用，不是做数据存储使用的。
-l   监听的ip地址，启动后，只能监听该网卡（ip地址）进来的请求。
可以把memcache服务器放入到内网中，与互联网隔离，让其他外网用户无法访问。 

3、数据过期的问题
在memcache里面数据过期后，不会自动删除，当get时，发现过期后，才删除该数据。
• Lazy Expiration
memcached内部不会监视记录是否过期，而是在get时查看记录的时间戳，检查记录是否过
期。这种技术被称为lazy（惰性）expiration。
因此，memcached不会在过期监视上耗费CPU时间。

4、数据存储空间满了，还能否存储数据呢？
• LRU
memcached会优先使用已超时的记录的空间，但即使如此，也会发生追加新记录时空间不
足的情况，此时就要使用名为 Least Recently Used（LRU）机制来分配空间。
顾名思义，这是删除“最近最少使用”的记录的机制。因此，当memcached的内存空间不足时
（无法从slab class 获取到新的空间时），就从最近未被使用的记录中搜索，并将其空
间分配给新的记录。从缓存的实用角度来看，该模型十分理想

<?php
//实例化一个memcache的类
$mem = new Memcache();
//连接memcache的服务器
$mem->connect("localhost",8888);
//$data = str_repeat('a',1024*400);
//$res = $mem->set('key1',$data);
//$data = str_repeat('b',1024*400);
//$res = $mem->set('key2',$data);
$data = str_repeat('c',1024*400);
$res = $mem->set('key3',$data);
var_dump($res);
?>

5、如果需要设置许多缓存项时，失效时间最好不要设置为相同的。
主要目的：防止缓存雪崩现象。

