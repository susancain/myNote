网页静态化与mysql优化（一）
目录
网页静态化与mysql优化（一）	1
一、衡量大型网站的标准	1
二、大型网站带来的问题	2
三、高并发的解决方案：	2
四、大流量（带宽）的解决方案	2
1、配置压缩，减少数据传输的数据量。	2
2、减少http的请求，	5
3、把比较占用流量的资源单独部署服务器。	6
4、花钱买带宽。	7
五、大存储解决方案	7
1、使用缓存技术	7
2、对mysql进行优化	8
六、页面静态化技术：	8
1、概述：	8
2、几个重要的概念	8
3、实现方式	9
4、ob缓存的介绍	9
5、ob缓存对应的函数。	10
6、生成静态页面的案例：	13
7、生成静态页面的案例扩展：	14
8、静态化在smarty里面使用，	15
9、真静态的优缺点说明：	15
七、伪静态的讲解	15
1、什么是伪静态	15
2、实现方式，入门案例	16


一、衡量大型网站的标准
pv 值(page views):页面的浏览量
uv 值(unique visitor):独立访客
	概念：一个网站，在一天内，有多少个用户访问过我们的网站，几十万以上，
独立ip：
	概念：一个网站，在一天内，有多少个独立的ip地址来访问我们的网站。
uv值略大于独立ip的。
	若考虑公司的局域网，则uv值略大于独立ip

二、大型网站带来的问题
大并发： 
	概念：在同一时间点，有多少用户同时访问网站。 
大流量： 
	大并发，必然要导致大流量，需要高的带宽。 
大存储： 
	数据库存储，表达到G,T级别。 

三、高并发的解决方案：
网站重新架构，分层技术，负载均衡，集群，读写分离
负载均衡器： 
硬件： 
F5-BIGIP:立竿见影，效果非常好，价格昂贵。一些大型的网站公司和网游公司在用。 
软件： 
lvs(linux virtual server)集成到linux的内核里面了， 
nginx  (该软件可以做web服务器，也可以做负载均衡使用)
负载均衡器的策略： 
	1.轮询技术：就是负载均衡把请求轮流转发给 web服务器。 
	2.最少连接：负载均衡把请求转发给最空闲的web服务器。 
	3.ip哈希：同一地址的客户端始终请求同一台 web服务器。 

四、大流量（带宽）的解决方案

1、配置压缩，减少数据传输的数据量。
缺点：在服务器端，要进行对数据压缩，要耗费时间，在浏览器端解压缩，要耗费时间。

原理：为了提高网页在网络上的传输速度，服务器对主体信息进行压缩。如常见的gzip压缩，deflate压缩，compress压缩以及google、chrome正在推的sdcn压缩。
查看浏览器支持的压缩格式：通过firebug等系列软件，查看请求的头信息。
Accept-Encoding  gzip,deflate

Apache上利用gzip压缩算法进行压缩的模块有两种：mod_gzip和mod_deflate. 
	Apache 1.x系列没有内建网页压缩技术，使用额外的第三方mod_gzip模块。
	Apache2.0以上的版本中gzip压缩使用mod_deflate模块。
配置压缩的步骤：
（1）apache服务器，要开启deflate模块，或gzip模块
打开apache的配置文件httpd.conf:
LoadModule deflate_module modules/mod_deflate.so
（2）在虚拟主机里面添加如下的配置
	<ifmodule mod_deflate.c> 
	DeflateCompressionLevel  6       #压缩级别为6，可选1-9，推荐为6 
	AddOutputFilterByType DEFLATE  text/plain #压缩文本文件 
	AddOutputFilterByType DEFLATE  text/html #压缩html文件 
	AddOutputFilterByType DEFLATE  text/xml #压缩xml文件
	</ifmodule> 
DeflateCompressionLevel 指令来设置压缩级别。该指令的值可为1（压缩速度最快，最低的压缩质量）到9（最慢的压缩速度，压缩率最高）之间的整数，其默认值为6（压缩速度和压缩质量较为平衡的值）

注意：为什么要指定文件类型来压缩？
压缩也是要耗费cpu资源的，图片/视频等文件的压缩效果也不好,不压缩。一般只压缩文本格式的文件。

查看文件的类型，通过响应头里面的”content-type”属性来查看。

虚拟主机里面的配置：
<VirtualHost *:80>
	DocumentRoot "E:/php_test/20150710/phptext/project20151105"
	ServerName www.demo.com
	<Directory "E:/php_test/20150710/phptext/project20151105">
		Option indexes FollowSymLinks
		AllowOverride All
		Order allow deny
		allow from all
	</Directory>
	<ifmodule mod_deflate.c>
		#压缩级别为6,可选1-9,推荐为6
		DeflateCompressionLevel 6
		#压缩文本文件
		AddOutputFilterByType DEFLATE text/plain
		#压缩html文件
		AddOutputFilterByType DEFLATE text/html
		#压缩xml文件
		AddOutputFilterByType DEFLATE text/xml
	</ifmodule>
</VirtualHost>

2、减少http的请求，
主要是合并文件，合并js，css,背景图片等文件。把浏览器一次请求需要的js,css，背景图片文件，合并成一个文件，这样，浏览器请求一次即可。

3、把比较占用流量的资源单独部署服务器。
一般占用流量的资源就是视频和图片，
4、花钱买带宽。

五、大存储解决方案
1、使用缓存技术
目的：做到，少查或不查数据库，
（1）页面静态化技术（磁盘缓存）
把一个动态页面（操作数据库的）转换成一个静态的html页面。
原理：
apache处理静态页面的速度要远远快于处理php页面的速度。

（2）内存缓存
内存缓存技术有：memcache和redis以及mysql里面的memory引擎。
原理：
注意：计算机从内存里面读取数据的速度，要远远快于从磁盘里面读取。
2、对mysql进行优化

六、页面静态化技术：
1、概述：
就是把一个动态的页面变成一个静态页面，后续用户直接访问静态页面。

页面静态化技术分为两种：真静态和伪静态。
真静态：把一个动态的页面，转成一个静态的页面,即.html文件
伪静态：所谓伪静态是从url地址上看是一个静态页面，但是实际上还是对应一个动态页面，
比如：http://www.abc.com/news-sport-id12.html
实际上是操作。http://www.abc.com/news.php?type=sport&id=12,
2、几个重要的概念
（1）动态网址：
所谓动态网址，一般来说去查询数据库，比如:http://www.abc.com/goods.php?id=120
特点：查询数据库，速度慢；接收参数，安全性要注意（sql注入）；不利于seo搜索引擎优化。
（2）静态网址
比如：http://www.abc.com/index.htm这个就是一个静态网址：
特点：不查询数据库，速度快；不接收参数，安全性高；利于seo
（3）伪静态网址：
从形式上看是一个静态页面，但是实际上对应一个动态页面，
比如：http://www.abc.com/news-sport-id12.html
实际上是操作。http://www.abc.com/news.php?type=sport&id=12,
特点：本身需要查询数据库，执行速度慢；不接收参数，因此安全；利于seo

3、实现方式
真静态：使用ob缓存技术来实现
伪静态：使用web服务器的rewrite机制（url的重写机制）来实现。

4、ob缓存的介绍
（1）程序缓存。
程序缓存，缓存的数据是，返回给浏览器的数据（包含头信息和主体信息）
程序缓存不能关闭，默认就有的。
（2）ob缓存
ob就是 output_buffering:输出缓存，缓存的数据是返回的响应的主体数据，可以自由的关闭打开。
在请求一个php的过程中，我们实际上经过三个缓存，ob缓存，程序缓存，浏览器缓存

注意点：如果开辟了ob缓存，主体数据首先存储到ob缓存里面，头信息要存储到程序缓存（无论是否开启ob缓存），当代码执行完毕后，ob缓存里面的数据刷新（移动）到程序缓存，程序缓存再输出到浏览器缓存中，最后输出内容。
（3）如何开启ob缓存。
有两种方式：
方式一：直接在页面中执行ob_start() 函数。
方式二：在php.ini文件中开启。
output_buffering = 容量|on|off
output_buffering = 4096
5、ob缓存对应的函数。
ob_start();		开启ob缓存，只针对当前页面有效。
ob_get_contents();	获取ob缓存里面的数据内容。
ob_clean();		清空ob缓存里面的数据,但是不关闭ob缓存
ob_end_clean();	清空ob缓存，并关闭ob缓存。
ob_flush();把ob缓存里面的数据，刷新（移动）到程序缓存，并不关闭ob缓存。
ob_end_flush();把ob缓存里面的数据，刷新（移动）到程序缓存，并关闭ob缓存，。

<?php
ob_start();
echo "aa";
ob_clean();//清除ob缓存里面的数据,但不关闭ob缓存
echo "bb";
?>
输出结果:bb

<?php
ob_start();
echo "aa";
$a = ob_get_contents();
echo "bb";
echo "$a"
?>
输出结果:aabbaa

<?php
ob_start();
echo "aa";
ob_end_clean();
$a = ob_get_contents();
echo "bb";
echo $a;
?>
输出结果:bb

<?php
ob_start();
echo "aa";
ob_flush();
echo "11";
$a = ob_get_contents();
echo "bb";
echo $a;
?>
输出结果:aa11bb11

<?php
ob_start();
echo "aa";
ob_end_flush();
echo '11';
$a = ob_get_contents();
echo 'bb';
echo $a;
?>
输出结果:aa11bb

总结：
常用的是：ob_start()   ob_get_contents()   ob_clean()
$content = ob_get_contents();
file_put_contents(‘index.html’,$content);

案例1：
<?php 
ob_start(); 
echo "abc";
header("content-type:text/html;charset=utf-8");
echo "hello";		
ob_clean();
echo "aa";
header("content-type:text/html;charset=utf-8");
?>

6、生成静态页面的案例：
newslist.php新闻的列表页面
<?php
$conn = mysql_connect("localhost",'root','root');
mysql_query('use php');
mysql_query('set names utf8');
$sql = "select id,word from cetsix limit 30,10";
$res = mysql_query($sql);
$list = array();
while($row = mysql_fetch_assoc($res)){
	$list[] = $row;
}
?>
<body>
<table width="600" border="1px">
	<tr>
		<td>标题名称</td>
		<td>详情内容</td>
	</tr>
	<?php foreach($list as $v){?>
	<tr>
		<td><?php echo $v['word']?></td>
		<td><a href="newsinfo.php?id=<?php echo $v['id']?>">详情内容</a></td>
	</tr>
	<?php }?>
</table>
</body>

newsinfo.php新闻的详情页面
<?php
$id = (int)$_GET['id'];//接收传递过来的id
$filename = 'news_id'.$id.'.html';//构建生成静态页面的文件名称
//判断是否生成了静态页面,如果生成了,则直接读取生成的静态页面,如果没有则要重新生成
if(file_exists($filename)){
	//有对应的静态页面
	include $filename;
	exit;
}
$conn = mysql_connect("localhost",'root','root');
mysql_query('use php');
mysql_query('set names utf8');
$sql = "select * from cetsix where id=$id";
$res = mysql_query($sql);
$info = mysql_fetch_assoc($res);
ob_start();
echo 'a';
?>
<body>
	<h1><?php echo $info['word']?></h1>
	<hr/>
	<div><?php echo $info['lx']?></div>
</body>
<?php
$content = ob_get_contents();
flle_put_contents($filename,$connect);
?>

7、生成静态页面的案例扩展：
给一个生存周期，比如说300秒，过了300秒后，要重新生成静态页面。

如果没有做静态化，并发量是1000，在300秒内，查询数据库多少次。300*1000
如果做了静态化，并发量是1000，而且缓存周期为300秒，在300秒内，查询数据库多少次？仅仅1次。

如何给一个页面设置生命周期。
比如生命周期为300秒，满足什么条件在有效期内。
创建文件的时间戳+生命周期>当前的时间戳
<?php
$id = (int)$_GET['id'];//接收传递过来的id
$filename = 'news_id'.$id.'.html';//构建生成静态页面的文件名称
//判断是否生成了静态页面,如果生成了,则直接读取生成的静态页面,如果没有则要重新生成//filemtime()	取得文件修改时间 int单位秒
if(file_exists($filename&&filemtime($filename)+300>time())){
	//有对应的静态页面
	include $filename;
	exit;
}
?>

8、静态化在smarty里面使用，
$smarty->cache_dir = “./cache/";  //缓存目录 
$smarty->caching = true;  //开启缓存,为false的时侯缓存无效 
$smarty->cache_lifetime = 60;  //缓存时间(单位 秒) 
if(!$smarty->isCached(’01.html’)){ 
	//判断模板文件是否被缓存。 
} 
9、真静态的优缺点说明：
优点： 1. 速度快 2. 安全性高 3. 利于seo 
缺点：就是占用磁盘空间., 如果过大，对磁盘响应速度有影响
在以下情况下，建议不要使用真静态
1.页面的数据更新频繁，最好不要使用真静态(比如股票，基金，等实时报价系统)
2.会生成海量页面(比如大型论坛 bbs ,csdn)
3.查询该页面一次后，以后再也不查询该页面.
4.不愿意被搜索引擎抓取的页面.
5.访问量小的页面.

七、伪静态的讲解
1、什么是伪静态
	伪静态：把一个动态的地址伪装成一个静态的地址。
实现方式：利用web服务器的rewrite机制。
	rewrite机制：将一个请求URL重写到另一个请求上！
比如：
index.html   重写成 index.php 
abc.php  重写成  123.php 
news_sport_id12.html   重写成  news.php?type=sport&id=12 
原理图：

2、实现方式，入门案例
（1）开启重写模块
打开apache的配置文件httpd.conf
LoadModule rewrite_module modules/mod_rewrite.so
（2）语法说明，
RewriteEngine  on  重写引擎开关，一旦开启，所有的重写条件都生效。
RewriteCond  重写条件，当达到什么条件时，完成重写。
RewriteRule :定义重写规则，哪个地址应该被重写到哪个目标地址。
具体的配置，可以在虚拟主机里面完成配置，也可以在.htaccess文件里面配置。

（3）入门案例；
比如请求index.html 页面，变成请求index.php页面。
<VirtualHost *:80>
	DocumentRoot "E:/php_test/20150710/phptext/project20151105"
	ServerName www.demo.com
	<Directory "E:/php_test/20150710/phptext/project20151105">
		Option indexes FollowSymLinks
		AllowOverride All
		Order allow deny
		allow from all
		
		RewriteEngine on	#重写引擎开关		
		RewriteRule index.html index.php 	#重写规则
	</Directory>
</VirtualHost>