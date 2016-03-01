目录
一、什么是主从复制	1
二、主从复制的原理	1
三、如何授权一个用户：	2
四、如何开启log-bin日志	3
	1、开启log-bin日志	3
	2、和log-bin日志相关的几个函数	4
	3、查看log-bin日志文件里面的内容：	4
	4、案例：使用log-bin日志完成数据的回复	6
五、主从配置	8
	1、主服务器的配置（xp1）192.168.28.251	8
	2、从服务器的配置(xp2) 192.168.28.252	9
	3、如何撤销从服务器	10
六、在项目中如何使用主从配置，完成读写分离	10
七、读写分离在TP框架里面实现	11


一、什么是主从复制

至少需要2台mysql服务器，一台配置为主服务器，一台配置为从服务器，主服务器的数据要同步到从服务器。


二、主从复制的原理
mysql中有一种日志，叫做bin日志（二进制日志），会记录下所有修改过数据库的sql语句。主从复制的原理实际是多台服务器都开启bin日志，然后主服务器会把执行过的sql语句记录到bin日志中，之后把这个bin日志发给从服务器，在从服务器再把bin日志中记录的sql语句同样的执行一遍。这样从服务器上的数据就和主服务器相同了。



实现方式：（1）授权一个用户，（2）开启log-bin日志，（3）详细的配置过程。

三、如何授权一个用户：
（1）mysql添加账号：
语法：grant 权限  on  指定数据库和表   to “用户名”@’可以登录主机的ip地址’  identified by ‘密码’

比如：grant all  on *.* to ‘xiaolong’@’%’  identified by ‘123456’;


对授权的用户远程登录：
>mysql -h192.168.28.251 -uxiaobai -p123456

（2）删除账号：
 语法：drop user ‘用户名’@’授权登录主机的ip地址’
drop user 'xiaobai'@'%';

四、如何开启log-bin日志
1、开启log-bin日志
打开mysql的配置文件：my.ini(window)     my.cnf  (linux)

log-bin=mysql-bin
mysql-bin是二进制日志文件的名称，默认是和数据库文件同一级目录里面存储。在此处也可以自己指定存储位置，比如log-bin=d:/nihao.txt

注意：log-bin日志，会在mysql服务重启时，会产生新的一个日志文件。

新产生的log-bin日志文件如下：


2、和log-bin日志相关的几个函数

（1）reset master  
清空所有的 log-bin日志，并产生一个新的log-bin日志文件。

（2）flush logs
产生新的一个log-bin日志文件。

（3）show master status
查看最新的一个log-bin日志的文件名称，并包含pos位置。



3、查看log-bin日志文件里面的内容：
创建一个表，添加sql语句进行测试：

如何查看log-bin日志的内容呢？(不能直接查看,因为是二进制文件)
使用一个命令：在mysql的安装目录的bin目录下面的mysqlbinlog.exe命令来完成查看。

语法：通过cmd的方式，进入到bin 目录里面执行。--no-defaults(不是以二进制格式查看)
mysqlbinlog   --no-defaults    日志文件的名称（包含全路径）




pos所在位置的分析：end_log_pos :记录上一个 sql语句的结束，下一个 sql语句的开始

4、案例：使用log-bin日志完成数据的回复
（1）创建一张表
mysql>create table news(id int,title varchar(32));
（2）执行flush logs（产生新的一个日志文件）
mysql>flush logs;
mysql>show master status;
（3）使用insert语句完成数据的插入
insert into news values(1000,'susan');
insert into news values(2000,'cain');
insert into news values(3000,'wilson');
（4）执行flush logs（产生新的一个日志文件）
（5）把该表里面的数据给删除。
delete from news;
select * from news;
（6）完成log-bin日志恢复数据。
要注意：刚才insert的操作是被记录到mysql-bin.000004日志文件里面的。
只需要，把mysql-bin.000004日志文件里面的sql语句执行一遍，即可完成 数据恢复。
语法：
mysqlbinlog --no-defaults 日志文件的名称（路径）| mysql –uroot –proot 数据库的名称

D:\mysql\bin>mysqlbinlog --no-defaults c:/mysql/data/data/mysql-bin.000004 | mysql -uroot -proot php
mysql>select * from news;


案例扩展：
比如一个公司在上午9:00备份了一次数据，到9:30分的时候，由于员工的误操作，把所有的数据给弄丢了，要求恢复数据。
分析：上午9:00之前的数据，非常好恢复的，因为已经备份过。关键是以从9：00到9:30的数据，如何恢复。
思路：查看log-bin日志文件，把9：00到9:30之间的增删改的sql语句执行一遍即可。
查找记录增删改sql语句在log-bin日志文件中的pos 开始位置和结束位置。
语法：
使用：--start-position=”开始位置”    --stop-position=”结束的位置”
D:\mysql\bin\mysqlbinlog --no-defaults --start-position="1103" --stop-position="1703" d:/mysql/data/data/mysql-bin.000005 | mysql
-uroot -proot php
五、主从配置
1、主服务器的配置（xp1）192.168.28.251
（1）主从服务器都要开启log-bin日志，并设置一个不同的server-id 的值。
主服务器的配置：
my.cnf
log-bin=mysql-bin
server-id=1
从服务器的配置：
my.cnf
log-bin=mysql-bin
server-id=2

（2）在主服务器上面授权一个用户，从服务器就是通过该账号，完成读取 log-bin日志信息的。

mysql>grant replication slave on *.* to 'xiaowangzi'@'%' identified by '123456';


（3）查看主服务器上面的最新的log-bin 日志，
注意：此时，就禁止对主服务器进行更改的操作。
mysql>show master status;


2、从服务器的配置(xp2) 192.168.28.252
（1）先关闭从服务器（每次重新配置时需要先关闭） 
执行命令：stop slave
（2）开始配置： 
change master to master_host=‘主服务器的ip地址’,master_user=‘主服务器上用于同步数据的账号’,master_password=‘同步账号的密码’,master_log_file=‘bin日志的文件名’,master_log_pos=bin日志中的position值。
mysql>change master to master_host="192.168.28.251",master_user="xiaowangzi",master_password='123456',master_log_file="mysql-bin.000005",master_log_pos=2559;

（3）开启从服务器
执行命令：start slave
（4）查看从服务器的状态，是否配置成功
mysql>show slave status\G

Slave_IO_Running:Yes 
此进程负责从服务器从主服务器上读取binlog 日志，并写入从服务器上的中继日志。 
Slave_SQL_Running:Yes 
此进程负责读取并且执行中继日志中的binlog日志， 
注：以上两个都为yes则表明成功，只要其中一个进程的状态是no，则表示复制进程停止，错误原因可以从”last_error”字段的值中看到。
通过在主服务器上面创建一个新的数据库，并创建一张新表，并添加记录，查看从服务器是否同步来完成测试。
配置完成后，要禁止对从服务器执行增删改的操作。

3、如何撤销从服务器
在从服务器上面执行：
停止从服务器：stop slave;
删除从服务器的一些配置：reset slave all; 

mysql>start slave;
ERROR 1200 (HY000):The server is not configured as slave;fix in config file or with CHANGE MASTER TO
注意点：
失败原因： 
（1）肯定是 sql语句哪里写错。 
（2）主从服务器的mysql版本号必须相同。
mysql>select version();

六、在项目中如何使用主从配置，完成读写分离
class  mysql{
	$dbm=主服务器 
	$dbs1=从服务器 
	$dbs2=从服务器 
	public function query(){
	   	在query里面进行语句判断，分析连接不同的mysql服务器。 
		如果是增删改的操作，就连接主服务器，
		如果是查询操作，则随机连接从服务器。
	}
} 
七、读写分离在TP框架里面实现
修改TP框架项目的配置文件：
    'DB_DEPLOY_TYPE'	=>1,//分布式数据库支持
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => 'localhost,192.168.3.250', // 服务器地址，多个用逗号隔开，默认第一个是主服务器，其余是从服务器
    'DB_NAME'               => 'php,php',          // 数据库名
    'DB_USER'               => 'root,xiaogang',      // 用户名
    'DB_PWD'                => 'root,1234',          // 密码
    'DB_PORT'               => '3306',        // 端口
    'DB_PREFIX'             => '',
    'DB_RW_SEPARATE'		=>	true,//支持读写分离 
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
	'DB_SLAVE_NO'       =>  '', //指定从服务器序号，如果为空，则随机连接从服务器。

完成TP框架里面的读写分离测试：主要证明是连接了不同的服务器。

主服务器的ip    192.168.28.251
授权一个连接的账号。
mysql>grant all on *.* to 'songjiang'@'%' identified by '123456';

从服务器的ip    192.168.28.252
授权一个连接的账号：
mysql>grant all on *.* to 'fangla'@'%' identified by '123456';

TP框架里面的配置文件：
<?php
return array(
	'DB_DEPLOY_TYPE'	=>1,//分布式数据库支持
    'DB_TYPE'               => 'mysql',     // 数据库类型
    'DB_HOST'               => '192.168.28.251,192.168.3.250', // 服务器地址，多个用逗号隔开，默认第一个是主服务器，其余是从服务器
    'DB_NAME'               => 'php,php',          // 数据库名
    'DB_USER'               => 'songjiang,fangla',      // 用户名
    'DB_PWD'                => '123456,123456',          // 密码
    'DB_PORT'               => '3306',        // 端口
    'DB_PREFIX'             => '',
    'DB_RW_SEPARATE'		=>	true,//支持读写分离 
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
	'DB_SLAVE_NO'       =>  '', //指定从服务器序号，如果为空，则随机连接从服务器。
	);
?>

代码：
Home\Controller\IndexController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extend Contorller
{
	public function demoadd(){
		//添加数据
		$sql = "insert into info values('大家好')";
		M()->execute($sql);
		echo 'ok';
	}
	public function democha(){
		//添加数据
		$sql = "select * from info";
		$info = M()->query($sql);
		echo '<pre>';
		print_r($info);
	}
}
?>

撤销了主从复制,为了测试demoadd是由主服务器执行,democha是由从服务器执行
写使用M()->execute()
读使用M()->query()






目录
一、常用web服务器的介绍	1
二、nginx的了解	2
三、nginx特点	2
四、支持的操作系统：	2
五、nginx 的安装，（window下面的安装）	3
	1、解压软件，解压后，拷贝到指定的目录	3
	2、启动nginx	3
	3、访问localhost,测试是否安装成功。	3
六、配置文件的讲解	4
	（1）配置工作进程数：	4
	（2）一个工作进程的并发量的配置。	5
	（3）虚拟主机的配置：	5
七、配置 nginx支持PHP	7
	（1）把php软件的代码拷贝到和nginx同级的目录里面（便于管理）	8
	（2）进入到php目录文件，把php.ini-development改成php.ini配置文件	8
	（3）打开php.ini文件，	8
	（4）把如下的三个文件拷贝nginx目录下面	9
	（5）配置start_nginx.bat批处理命令。	10
	（6）打开nginx.conf的配置文件。	11
	（7）关闭nginx  使用start_nginx.bat命令开启。	11
	（8）测试是否支持php	12
八、配置nginx expires缓存功能	12
九、压缩配置	13
十、负载均衡配置	15
	1、负载均衡的原理图：	15
	2、配置的原理图：	16
	3、配置步骤：	16

一、常用web服务器的介绍
apache:功能完善，历史悠久 
缺点：处理每一个php比较费资源，导致如果高并发时会太耗费服务器资源无法处理更多请求。 
nginx：省资源，省cpu,所以在高并发时能够处理更多的请求，高端能达到3万到5万的并发量。 
二、nginx的了解
Nginx（”engine x”）是俄罗斯人编写的一款高性能的HTTP和反向代理服务器。Nginx是Apache服务器不错的替代品，它能够支持高达50000个并发连接数的响应，而内存，cpu等系统资源消耗却非常低，运行非常稳定。 
在国内，已经有新浪博客，新浪播客，网易新闻，六间房，56.com.discuz!官方论坛，水木社区，豆瓣，海内SNS，迅雷在线等多家网站使用Nginx作为web服务器或反向代理服务器。

三、nginx特点
1、它可以高并发连接，官方测试能够支撑5万并发连接，在实际生产环境中可以支撑2到4万并发连接。 
2、内存消耗少 
Nginx+php(FastCGI)服务器再3万并发连接下，开启的10个Nginx进程消耗150MB内存（15MB*10=150MB）开启的64个php-cgi进程消耗1280MB内存（20MB*64=1280MB） 
3、成本低廉 
购买F5 BIG-IP ,NetScaler等硬件负载均衡交换机需要10多万甚至几十万人民币。而Nginx为开源软件，可以免费试用，并且可用于商业用途。 
4、其他理由： 
（1）配置文件非常简单：通俗易懂，即使非专业管理员也能看懂。 
（2）支持 rewrite重写规则：能根据域名、URL的不同，将HTTP请求分到不同的后端服务器群组。 
（3）内置的健康检查功能：如果nginx proxy后端的某台服务器宕机了，不会影响前端访问。 
（4）节省带宽，支持gzip压缩。 
（5）稳定性高：用于反向代理，宕机的概率微乎其微。 
（6）支持热部署。在不间断服务的情况下，对软件版本升级。
四、支持的操作系统：
FreeBSD 3.x,4.x,5.x,6.x i386; FreeBSD 5.x,6.x amd64;
Linux 2.2,2.4,2.6 i386; Linux 2.6 amd64;
Solaris 8 i386; Solaris 9 i386 and sun4u; Solaris 10 i386;
MacOS X （10.4） PPC;
Windows XP，Windows Server 2003和Windows 7等。

五、nginx 的安装，（window下面的安装）

1、解压软件，解压后，拷贝到指定的目录
c:/env/nginx/
conf:配置文件所在目录
html:网站的根目录,类似apache的htdoc目录
logs:日志文件
nginx.exe:启动程序命令

2、启动nginx
语法：以cmd 方式进入到nginx命令所在的目录，执行  start nginx命令即可
C:\env\nginx>start nginx
查看是否启动成功：默认 端口是80端口。
C:\env\nginx>netstat -an



3、访问localhost,测试是否安装成功。
出现如下提示，表名安装成功。



注意：关于nginx的一些命令：

start nginx//启动nginx服务。 
nginx -s stop // 停止nginx
nginx -s reload // 重新加载配置文件，无需重启 nginx服务器。
nginx -s quit // 退出nginx

六、配置文件的讲解
C:\env\nginx\conf\nginx.conf
（1）配置工作进程数：
C:\env\nginx\conf\nginx.conf
worker_processes 1;//推荐配置:cpu的个数*核心数

（2）一个工作进程的并发量的配置。
C:\env\nginx\conf\nginx.conf
events {
	worker_connections 1024;
}

（3）虚拟主机的配置：

http{
	server {

	}
	server {

	}
}

每一个server段就是一个虚拟主机。
案例：配置一个基于域名的虚拟主机  www.abc.com
root:网站的根目录，可以使用相对路径，也可以使用绝对路径，如果是相对路径，是和html目录同级的。
C:\env\nginx\conf\nginx.conf
server{
	listen	80;
	server_name	www.abc.com
	location / {
		root abc;
		index index.html index.htm
	}
}

配置完成后，通过执行nginx –s reload重新加载配置文件。



配置hosts文件；
127.0.0.1  www.abc.com

在浏览器中访问：
http://www.abc.com

案例：配置一个基于端口的虚拟主机： www.abc.com:8080
server {
	listen	8080;
	server_name	www.abc.com
	location / {
		root 8080;
		index	index.html index.htm
	}
}
配置完成后，执行nginx –s reload重新加载配置文件。
建立一个访问的一个文件：
C:\env\nginx\8080\index.html


在浏览器中访问：
http://www.abc.com:8080/

七、配置 nginx支持PHP

在nginx中，php不是作为一个模块出现在nginx里面。php是作为一个独立进程运行的。该进程的端口是9000端口。当nginx遇到php文件时，交给9000端口来处理。
（1）把php软件的代码拷贝到和nginx同级的目录里面（便于管理）
C:\env\php
（2）进入到php目录文件，把php.ini-development改成php.ini配置文件

（3）打开php.ini文件，
配置php加装扩展文件的位置
C:\env\php\php.ini
extension_dir = "C:/env/php/ext"
配置时区：
C:\env\php\php.ini
date.timezone = PRC
配置让php 作为独立进程运行。
C:\env\php\php.ini
cgi.fix_pathinfo=1

（4）把如下的三个文件拷贝nginx目录下面
start_nginx.bat
stop_nginx.bat
RunHiddenConsole.exe

start_nginx.bat和stop_nginx.bat是管理php和nginx的工具命令。
RunHiddenConsole.exe一个让你的程序隐藏运行的小工具 
（5）配置start_nginx.bat批处理命令。
C:\env\nginx\start_nginx.bat

@echooff

setPHP_FCGI_MAX_REQUESTS=1000
 
echo Starting PHPFastCGI...
RunHiddenConsole c:/env/php/php-cgi.exe -b 127.0.0.1:9000 -c c:/env/php/php.ini
 
echo Starting nginx...
RunHiddenConsole c:/env/nginx/nginx.exe -p c:/env/nginx


（6）打开nginx.conf的配置文件。
C:\env\nginx\conf\nginx.conf
location ~ \.php {
	root html;
	fastcgi_pass	127.0.0.1:9000;
	fastcgi_index	index.php
	fastcgi_param	SCRIPT_FILENAME	$document_root$fastcgi_script_name;
	include		fastcgi_params;
}
（7）关闭nginx  使用start_nginx.bat命令开启。

直接双击 start_nginx.bat命令：
C:\env\nginx>netstat -an //查看80和9000端口

（8）测试是否支持php
<?php
phpinfo();
?>

八、配置nginx expires缓存功能
对于图片，css,js等元素更改机会较少，特别是图片，可以将图片设置在浏览器本地缓存365天，css,js缓存10天，这样可以提高下次打开用户页面加载速度，并节省大量带宽。此功能同apache的expires。这里通过location的功能，将需要缓存的扩展名列出来，然后指定缓存时间：
~表示匹配正则,第一个’.’表示除了换行符的任何字符,第二个’.’被转义
Expires 365d; 缓存365天

location  ~.*\.(gif|jpg|jpeg|png|bmp)$
{	
	root abc;
	expires 365d;
}


九、压缩配置
gzip on;  
#开启gzip压缩功能
gzip_min_length 1k;
#设置允许压缩的页面最小字节数，页面字节数从header头的content-length中获取。默认值是0,不管页面多大都进行压缩。建议设置成大于1k。如果小于1k可能会越压越大。
gzip_buffers 4 16k;
#压缩缓冲区大小。表示申请4个单位为16k的内容作为压缩结果流缓存，默认值是申请与原始数据大小相同的内存空间来存储gzip压缩结果。
gzip_http_version 1.0;
#压缩版本（默认1.1，前端为squid2.5时使用1.0）用于设置识别http协议版本，默认是1.1,目前大部分浏览器已经支持gzip解压，使用默认即可。
gzip_comp_level 2;
#压缩比率。用来指定gzip压缩比，1压缩比量小，处理速度快；9压缩比量大，传输速度快，但处理最慢，也必将消耗cpu资源。
gzip_types text/plain application/x-javascript text/css application/xml;
#用来指定压缩的类型，“text/html”类型总是会被压缩。
gzip_vary on;
#vary header支持。该选项可以让前端的缓存服务器缓存经过gzip压缩的页面，例如用squid缓存经过nginx压缩的数据。
注意：不要对视频和图片配置压缩，对视频和图片压缩比较费资源，而且压缩效果不好。主要压缩对象是文本类型文件。

未压缩之前：


压缩后：


压缩的代码配置：
C:\env\nginx\conf\nginx.conf
server{
	listen	80;
	server_name	www.abc.com
	location / {
		root abc;
		index index.html index.htm
	}
	location  ~.*\.(gif|jpg|jpeg|png|bmp)$
	{	
		root abc;
		expires 365d;
	}
	gzip on;
	gzip_min_length 1k;
	gzip_buffers 4 16k;
	gzip_http_version 1.0;
	gzip_comp_level 6;
	gzip_types text/plain application/x-javascript text/css application/xml;
	gzip_vary on;
}
十、负载均衡配置
1、负载均衡的原理图：

2、配置的原理图：
实际生产环境中连接池中2个名称是一样的,为了测试所以设置不一样


3、配置步骤：
使用基于端口的虚拟主机可以配置模拟多台服务器：
（1）建立两个基于端口的虚拟主机  www.123.com:81和www.123.com:82
c:/env/nginx/conf/nginx.conf
server{
	listen 81;
	server_name www.abc.com;
	location / {
		root 81;
		index index.html index.htm;
	}
}
server{
	listen 82;
	server_name www.abc.com
	location / {
		root 82;
		index index.html index.htm
	}
}
（2）在nginx目录下面新建81和82的两个目录，并在两个目录里面分别新建两个文件，
该两个文件的内容不要一样。


（3）建立一个连接池：
语法：
upstream 连接池的名称  {
	server   www.123.com:81;
	server   www.123.com:82;
}

upstream nihao {
	server www.123.com:81;
	server www.123.com:82;
}
（4）配置一个域名为www.123.com的虚拟主机。
upstream nihao {
	server www.123.com:81;
	server www.123.com:82;
}
server {
	listen 80;
	server_name www.123.com;
	location / {
		proxy_pass http://nihao;
		proxy_set_header Host $host;
		proxy_set_header X-Peal-IP $remote_addr;
		proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
	}
}
（5）配置好hosts文件，进行访问测试：
192.168.28.251 www.123.com
第一次请求

第二次请求



