
redis的操作
目录
redis的操作	1
一、NOSQL	1
	1、出现背景：	1
	2、特点	1
	3、优缺点：	1
	4、NOSQL使用场景	1
	5、常见nosql产品	2
二、redis介绍	2
	1、概述	2
	2、与memcache比较说明	2
三、安装启动	3
	1、安装软件	3
	2、启动软件	4
四、数据类型讲解:	4
	1、字符串类型	4
	2、hashes类型及操作	7
	3、lists类型	9
	4、集合的操作	13
	5、有序集合	17
五、服务器相关的命令	17
六、安全认证	21
七、php当客户端操作redis	22
	1、安装扩展	22
	2、入门使用	24
八、高级案例	24
九、持久化机制	25
	1、snapshotting(快照)默认方式	26
	2、持久化机制之aof方式	27


一、NOSQL
NoSQL ,（Not Only SQL），泛指非关系型数据库。 
NoSQL数据库的产生就是为了解决大规模数据集合多重数据种类带来的挑战，尤其是大数据应用难题。
1、出现背景： 
随着互联网web2.0网站的兴起，传统的关系型数据库在应付web2.0网站，特别是超大规模和高并发的SNS类型纯动态网站已经显得力不从心，暴露了很多难以克服的问题，而非关系型的数据库则由于其本身的特点，得到了非常迅速的发展。
2、特点
NoSQL 通常是以key-value形式存储， 
不支持SQL语句， 
没有表结构 
3、优缺点： 
优点：
大数据量的扩展（高并发读写的性能 分布式存储） 
配置简单 
灵活、高效的操作与数据模型 
低廉的成本 
不足之处： 
没有统一的标准 
没有正式的官方支持 
各种产品还不算成熟
4、NOSQL使用场景
（1）对数据高并发读写
（2）对海量数据的高效率存储和访问
（3）对数据的高可扩展性和高可用性。

5、常见nosql产品
新浪微博	redis
Google	bigtable
Amazon	SimpleDB
淘宝数据平台	Tair
优酷视频	MongoDB
飞信空间	HandlerSocket
视觉中国网址	MongoDB

二、redis介绍
1、概述
（1）redis是一个开源的，先进的key-value存储。它通常被称为数据结构服务器，
（2）它支持存储的value类型很多，包括string(字符串)、list(链表)、set(集合)、Zset(有序集合)。 
（3）为了保证效率数据都是缓存在内存中，它也可以周期性的把更新的数据写入磁盘或者把修改操作写入追加的记录文件。 
（4）提供的API语言包括：C、C++、C#、Clojure     Common Lisp  Erlang  Haskell Java Javascript Lua Objective-C Perl PHP Python Ruby Scala Go Tcl 

2、与memcache比较说明
redis与memcache比较
（1）数据类型:memcache支持的数据类型就是字符串，redis支持的数据类型有字符串，哈希，链表，集合，有序集合。
（2）持久化：memcache数据是存储到内存里面，一旦断电，或重启，则数据丢失。redis数据也是存储到内存里面的，但是可以持久化，周期性的把数据给保存到硬盘里面，导致重启，或断电不会丢失数据。
（3）数据量：memcahce一个键存储的数据最大是1M,而redis的一个键值，存储的最大数据量是1G的数据量。
三、安装启动
1、安装软件
（1）下载软件，上传到linux服务器。
redis-2.4.17.tar.gz
（2）解压软件
tar -zxvf redis-2.4.17.tar.gz
（3）进入解压后的目录，
cd redis-2.4.17.tar.gz
（4）直接执行make
（5）执行安装，在安装时指定安装的目录
make PREFIX=/usr/local/redis install 

安装成功后， 在安装的目录下面生成一个bin目录，该目录下面有5个文件。

redis-benchmark 是性能测试工具。
redis-check-aof和redis-check-dump文件是日志检测工具
redis-cli是客户端连接程序
redis-server是服务器端启动程序
（6）在解压的目录里面复制配置文件到安装目录里面。
cp /home/wilson/redis-2.4.17/redis/redis.conf ./

2、启动软件
（1）使用vim打开配置文件,进行配置，让redis服务在后台执行。
vim redis.conf
	在17行 daemonize no 改为 daemonize yes
	让redis服务在后台执行,不占据当前终端
（2）执行启动
语法：redis-server 指定配置文件
#不加./就是命令,加了./相对绝对路径都可以
[root@localhost bin]# ./bin/redis-server ../redis.conf

查看是否启动成功：
redis的默认启动端口是： 6379，
netstat -tunpl | grep 6379

关闭服务：pkill redis-server

（3）客户端连接 redis服务器
语法：redis-cli  -h ip地址 –p端口
如果是本机，端口默认，则直接执行redis-cli即可。
[root@localhost bin]# ./redis-cli

四、数据类型讲解:
它支持存储的value类型很多，包括string(字符串)、list(链表)、set(集合)、Zset(有序集合)。
pop 抛出;remove 移除;rump 尾部;trim 整理;flush 冲洗;range 范围;
1、字符串类型
String是最简单的类型，一个 key对应一个Value，



set
设置键值
语法  set  key   value

注意：如果key存在，则是修改，如果key 不存在则是添加。

get
获取键值
语法：get key

setnx 
设置键值，在设置时，要判断该键是否存在，如果存在，则设置失败。
语法： setnx key value
	setnx name Cain
setex 
设置键值,在设置键值时，指定该键的有效期，单位是秒。
语法：setex  key 有效期  value
	setex color 10 red

mset 
设置键值，可以一次性设置多个键值。
语法：mset key1 value1  key2 value2……..
mset name Susan age 20 email susan@qq.com

mget 
获取键值，可以一次性获取多个键值。
语法：mget key1 key2……..
mget name age email

incr(incrby)
自增操作，加1操作，如果该键不存在，则返回1.
语法；incr  key

incrby
加法操作，可以指定相加的值
语法： incrby key number
	incrby number 100
	incrby number 200

2、hashes类型及操作 
Redis hash是一个string类型的field和value的映射表。它的添加、删除操作都是0（1）（平均）。hash特别适合用于存储对象。相较于将对象的每个字段存成单个string类型。将一个对象存储在hash类型中会占用更少的内存，并且可以更方便的存取整个对象。 

hset 
设置哈希的值：
语法： hset   key   field  value
	hset user:1 name susan
	hset user:1 age	12
	hset user:1 email susan@qq.com

hget 
获取哈希里面的field的值
语法： hget   key   field
	hget user:1 age
	hget user:1 name
	hget user:1 email

hmset 
设置哈希的值,可以一次性设置多个field
语法：hmset key field1 value1  field2 value2……
	hget user:1 age
	hget user:1 name
	hget user:1 email

hmget 
获取哈希的里面多个field的值
语法：hmget key  field1 feild2 ….
hmset user:2 name cain age 21 email cain@qq.com


hlen 
计算哈希里面field的个数
语法；hlen key
	hlen user:2
	hlen user:1

hdel 
删除哈希里面指定field
语法：hdel key  field
	hdel user:1 name

hgetall 
返回哈希里面所有的field与value
语法：hgetall key


3、list类型 
list是一个链表结构，主要功能是push、pop、获取一个范围的所有值等等，操作中key 理解为链表的名字
可以把链表理解成一个容器，存储一些字符串元素。
可以模拟栈的操作：

可以模拟队列的操作。


注意：存在链表中的元素是有顺序的，0表示是头部的元素，依次类推。链表中就可以存在重复的元素。

lpush 
从头部向链表里面添加元素
语法：lpush  key  value
	lpush list1 one
	lpush list1 two
	lpush list1 three
	lpush list1 four
	lpush list1 five
	lpush list1 six
	lpush list1 seven

lrange 
是获取链表里面的元素
语法：lrange key 开始下标  结束下标
如果开始下标为0则是从头部开始取，如果结束下标为-1则是到链表的尾部结束。
	lrange list1 0 -1

rpush 
从尾部向链表里面添加元素
语法：lpush  key  value
	rpush list2 one
	rpush list2 two
	rpush list2 one
	rpush list2 two
	rpush list2 one
	rpush list2 two

lrem 
删除链表里面的元素
语法：lrem key 删除个数 指定的元素
	lrem list1 2 tree

ltrim 
保留指定范围的元素
语法：ltrim  key 开始下标  结束下标
	ltrim list2 2 3

lpop 
删除链表头部的一个元素
语法：lpop key
	lpop list1

lindex 
返回链表中指定下标的元素。
语法：lindex key 下标
	lindex list1 1
	lindex list1 2

4、集合的操作
sets类型及操作 
set是集合，它是string类型的无序集合。set是通过hash table实现的、添加、删除和查找的复杂度都是0(1)。对集合我们可以取并集、交集、差集。通过这些操作我们可以实现sns中的好友推荐和blog的tag功能。

集合的概念和数学里面的集合 的概念类似。
集合的特点：集合里面的元素具有唯一性，无序性。


sadd 
向集合里面里面添加元素
语法： sadd  key  value
	sadd set1 one
	sadd set1 two
	sadd set1 tree
	sadd set1 apple

smembers 
返回集合里面的元素
语法：smembers key
	smembers set1

srem 
删除集合中指定 的元素
语法：srem key 指定的元素
	srem set1 apple	

sdiff 
返回集合中的差集，在集合1中出现过，不在集合2中出现的元素。
	sadd set2 two
	sadd set2 apple
	sadd set2 orange
	smembers set1
	smembers set2
	sdiff set1 set2

sinter
返回两个集合的交集。
语法：sinter 集合1  集合2 
	smembers set1
	smembers set2
	sinter set1 set2

sunion 
返回集合的并集，多个集合合并，去掉重复的元素。
语法：sunion 集合1 集合2
	smembers set1
	smembers set2
	sunion set1 set2

scard 
返回集合中元素的个数
	scard set1
	scard set2
	sadd set1 xiaowu
	scard set1

sismember 
判断某个元素是否在该集合里面。
语法：sismember 集合名称  指定的元素。
	smembers set2
	sismember set2 apple
	sismember set2 tree


5、有序集合
给集合中的元素添加顺序编号（下标）

五、服务器相关的命令
keys
获取当前的键，可以使用通配符。* ?
	keys *	//获取所有的键
	keys n*	//获取n开头的键
	keys l*	//获取l开头的键

exists
判断key是否存在，
语法：exists key
	exist age
	exist name
	exist email

del
删除指定的key
语法：del key
del name

expire
给指定的键设置失效时间
语法：expire key 失效时间（秒）
	set color red
	expire color 10
	ttl color 查看color剩余时间
	get color

ttl 
返回该键的未失效时间。
	ttl color
type
返回key的类型，
	type user:2
	type list2
	type age
	type set1

select
选择数据库，在redis 里面，默认有16个数据库（0-15），默认是进入的0号数据库。
语法：select 数据库的编号（0-15）
	select 1
	select 2

dbsize 
查看当前数据里面的键的数量
	select 1
	dbsize
	set name xiaofeng
	dbsize
	set age 12
	dbsize

flushdb 
清空当前数据库里面的键。
	keys *
	flushdb
	keys *

flushall 
清空所有数据库里面的键


六、安全认证
设置客户端连接后进行任何其他操作前需要使用的密码。 
注意：因为 redis速度相当快，所以在一台比较好的服务器下，一个外部的用户可以在一秒钟进行150k次的密码尝试，这意味着你需要指定非常非常强大的密码来防止暴力破解
配置方法：
使用vi打开redis的配置文件。
	vim redis.conf
	搜索/requirepass
	小写n键继续搜索
	设置
	requirepass guangzhou
	(此处是设置的密码是明文的,要对redis.conf文件做好权限控制)
	设置完后,重启
	pkill redis-server
	启动
	/usr/local/redis/bin/redis-server /usr/local/redis/redis.conf
	连接
	/usr/local/redis/bin/redis-cli
注意：设置完成密码后，要关闭redis服务，重新开启。

如果设置了密码，没有经过授权，则可以连接，但无法操作。
认证方式，有两种，可以在登录时授权，也可以在登录后授权。

方式一：在客户端登录redis服务的时候，
语法：redis-cli    –a 密码
	exit
	ls
	[root@localhost redis]# ./bin/redis-cli -a guangzhou 
	keys *
	set name xiaobai
	get name
方式二：客户端登录服务器后，执行auth可以授权。
语法：auth 密码
	exit
	./bin/redis-cli
	keys *
	auth guangzhou 
	keys *
	set age 12
	get age


七、php当客户端操作redis
1、安装扩展
（1）准备扩展，要注意安装的扩展要和php的版本对应。要安装不带nts的。
phpredis_5.4.x_vc9
或phpredis_5.4_vc9

（2）要把对应扩展的两个文件，拷到php的安装目录ext目录下面。
注意：如果你的php版本是5.3系列的直接拷贝一个文件即可（php_redis.dll）。 

（3）打开php.ini的配置文件，要引入扩展
注意：extension=php_igbinary.dll一定要放在extension=php_redis.dll的前面，否则此扩展不会生效。

（4）重启apache，使用phpinfo函数进行测试

2、入门使用
<?php
$redis = new Redis();//实例化一个对象
$redis->connect('192.168.22.250',6379);//连接redis服务器
$redis->auth("guangzhou");
//string操作
$redis->set('username','susancain');
//hash操作
$redis->hmset('user:id1',array('name'=>'susancain','age'=>20,'email'=>'susancain@qq.com'));
//list操作
$redis->lpush('listone','苏珊');
//$data = $redis->lrange('listone',0,-1);
//set操作
$redis->sadd('setone','wilson');
echo 'ok';
?>

八、高级案例
完全是redis来做，
1、用户的注册
register.php
<body>
	<h1>用户注册页面</h1>
	<form action="action.php?act=reg" method="post">
		用户名: <input type="text" name="username">
		密码: <input type="text" name="password">
		介绍: <textarea name="intro" cols="50" rows="5"></textarea><br/>
			<input type="submit" value="注册">	
	</form>
</body>
2、用户的显示
<?php
header('content-type:text/html;charset=utf-8');
require 'redis.php';
//分页显示注册的用户
//(1)计算总的记录数
$total = $redis->lsize('userid');//获取链表里面元素的个数
//(2)定义每页显示的记录数
$perpage = 2;
//(3)计算总的页数
$pagecount = ceil($total/$perpage);
//(4)定义当前页面
$page = isset($_GET['page'])?max(1,min($pagecount,(int)$_GET['page'])):1;
//(5)构建limit变量
//从链表里面取出id的值,根据id构造哈希的键,在取出哈希的值
$offset = ($page-1)*$perpage;
$n = $offset+$perpage-1;

/*第一页数据:lrange)('userid',0,4)
第二页数据:lrange('userid',5,9)
第三页数据:lrange('userid',10,14)*/
$data = $redis->lrange('userid',$offset,$n);
$list=array();
//根据取出的id构造哈希的键
foreach($data as $v){
	$key = 'user:'.$v;
	$list[]=$redis->hgetall($key);//获取哈希里面所有的键值
}
//定义上一页
$prev = max(1,$page-1);
$next = min($pagecount,$page+1);
?>
<h1>注册用户的信息</h1>
<table border="1" width="600">
	<tr>
		<td>名称</td>
		<td>密码</td>
	</tr>
	<?php foreach($list as $v){?>
		<td><?php echo $v['username']?></td>
		<td><?php echo $v['password']?></td>
	<?php }?>
	<tr>
		<td colspan="2"><a href="?<?php echo $prev?>">上一页</a><a href="?<?php echo $next?>">下一页</a></td>
	</tr>
</table>


九、持久化机制
redis是一个支持持久化的内存数据库，也就是说redis需要经常将内存中的数据同步到硬盘来保证持久化。 
redis支持两种持久化方式： 
（1）snapshotting(快照)默认方式 
（2）append-only file( 缩写aof)的方式
1、snapshotting(快照)默认方式
快照是默认的持久化方式。这种方式是将内存中数据以快照的方式写入到二进制文件中，默认的文件名为dump.rdb.可以通过配置设置自动做快照持久化的方式。我们可以配置redis在n秒内如果超过m个key修改就自动做快照。

具体的配置：
vi redis.conf
/save
save 900 1 #900秒内如果超过1个key被修改,则发起快照保存
save 300 10 #300秒内如果超过10个key被修改,则发起快照保存
save 60 10000
假如有1个键发生了更新。
0       60       60      60     60     60  60  300      900
save 900 1
save 300 10
save 60 10000
dbfilename dump.rdb


可以手动发起快照，有两种操作方法
方法一：未登录时，
语法：./redis-cli  -a  密码  -h IP地址   bgsave  手动发起一次快照保存操作
[root@localhost bin]# ./redis-cli -a guangzhou -h 192.168.22.7 bgsave

方法二：已经登录
语法：直接执行bgsave

2、持久化机制之aof方式
由于快照方式是在一定间隔做一次的，所以如果redis意外down掉的话，就会丢失最后一次快照后的所有修改。 
aof比快照方式有更好的持久化性，是由于在使用aof时，redis会将每一个收到的写命令都通过write函数追加到文件中，当redis重启时会通过重新执行文件中保存的写命令来在内存中重建整个数据库的内容
具体的配置：
appendonly   yes	//启用 aof 持久化方式
appendfilename   appendonly.aof   //保存命令的文件
# appendfsync always   //每次收到写命令就立即强制写入磁盘，最慢的，但是保证完全的持久化，不推荐使用
appendfsync everysec   //每秒钟强制写入磁盘一次，在性能和持久化方面做了很好的折中，推荐
# appendfsync no   //完全依赖 os，性能最好,持久化没保证

exit
vi redis.conf
	/appendonly
	appendonly no 改为appendonly yes
pkill pkill redis-server
ls
[root@localhost redis]./bin/redis-server redis.conf
[root@localhost redis]bin/reids-cli

aof文件的重写，把备份文件里面的命令操作重新整理成命令，
比如incr number执行10次后，结果为10，默认是保存了10次incr number命令，aof执行重写后，就直接把10次incr number变成set number  10,会 减少aof 的文件的容量，提高效率。
手动重写：bgrewriteaof 命令，
可以在未登录执行，也可以在登录后执行。

[root@localhost redis]# ./bin/redis-cli bgrewriteaof