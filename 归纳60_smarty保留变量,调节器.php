Smarty模板引擎（一）	1
一、模板引擎概述	3
	1、历史背景	3
	2、模板引擎	3
	3、模板引擎核心原理	4
	4、封装自定义模板引擎	5
二、Smarty模板引擎	6
	1、什么是Smarty模板引擎	6
	2、获取Smarty模板引擎	6
	3、libs目录详解	6
	4、为什么要选择Smarty模板引擎	7
	5、Smarty部署（四步走）	7
	6、Smarty的使用（七步走）	7
	7、Smarty执行流程	8
	8、Smarty执行流程图	9
三、Smarty中细节（常用属性与方法）	9
	1、常用属性	9
	2、相关属性	10
	3、相关方法	12
	4、两个常用方法	12
	5、特别注意	13
四、设计篇	13
	1、模板注释	13
	2、模板变量	14
	① 从php文件中分配过来的变量	14
	② 从配置文件中读取的变量	15
	③ 在模板文件中直接定义的变量	15
	3、系统中保留变量	16
	4、变量调节器	16
	5、变量调节器组合应用	18
五、系统内置函数	19
	1、capture内建函数	19
	2、config_load内建函数	19
	3、include内建函数	20
	4、foreach内建函数	21
六、作业	23

一、模板引擎概述
	优点：执行效率高
	缺点：难于维护
2、模板引擎
	模板引擎，是指利用某种模板语言将页面制成模板，再依据业务逻辑将该模板语言翻译成业务数据，从而生成最终展示页面。其目的就是要把页面（HTML）与业务数据(PHP)实现彻底分离。

	我们在很多地方都可以看到模板引擎的身影，比如各种CMS、论坛、代码生成器，甚至是Mootools、JQuery等JavaScript库都用到了模板引擎。

	DEDECMS/PHPCMS/帝国CMS/Discuz
	Mootools、JQuery

3、模板引擎核心原理
	demo.html静态模板
	一般情况下，很多CMS系统都有标签手册，这些标签只需要设计师记住就可以从数据库读取代码。
4、封装自定义模板引擎
二、Smarty模板引擎
1、什么是Smarty模板引擎
	Smarty是一个基于PHP开发的PHP模板引擎。它提供了逻辑与外在内容的分离。
2、获取Smarty模板引擎
	下载地址：http://www.smarty.net/
	demo：官方提供的实例代码
	lexer：新增文件夹
	libs：Smarty核心源代码都放于此文件夹中
3、libs目录详解
	plugins：自定义插件包，Smarty提供了强大的扩展功能
	sysplugins：系统插件包，为Smarty系统提供插件支持
	Autoloader.php：自动载入文件（可以完成系统文件的自动载入）
	debug.tpl：调试模板（Smarty模板文件可以是html也可以是htm或者是tpl
	Smarty.class.php：Smarty3.0入口文件，如果想使用Smarty必须包含此文件
	SmartyBC.class.php：Smarty2.0入口文件，如果想使用早期的功能，可以载入此文件

4、为什么要选择Smarty模板引擎
面试题：你了解过哪些模板引擎，最擅长哪款？
答：我了解过PHPLib、Smarty模板引擎，目前正在关注Volt新模板引擎，最擅长使用Smarty。

Smarty特点：
	1）Smarty模板引擎是通过PHP编写，可以与PHP无缝对接
	2）速度：相当于其他模板引擎而言，Smarty具有更快的编译速度
	3）编译型：当我们第一次访问模板文件时，系统自动会生成编译文件，当下次访问时，如果模板没有改变，系统会自动调用编译文件，从而达到更多的反应速度
	4）缓存技术：当我们访问模板文件时，如果开启了缓存技术，当下次访问时，如果模板文件没有改变且缓存没有过期，系统将自动读取缓存文件。
	5）插件技术，当发现系统功能不足时，可以扩充Smarty
	6）语句自由 if/elseif/else/endif

	编译速度  <  缓存速度  <  静态化技术
	Smarty不适合的场合：
	小项目：程序员和设计师一般都是一个人，如果使用了Smarty，其开发效率会变低
	实时更新的程序：黄金走势、股票走势
5、Smarty部署（四步走）
	第一步：复制libs文件夹到项目目录中
	第二步：更名libs文件名为Smarty
	第三步：创建templates文件夹作为模板文件目录
	第四步：创建templates_c文件夹作为编译目录
6、Smarty的使用（七步走）
	第一步：创建项目的入口文件如demo01_rumen.php
	第二步：在templates文件夹下创建demo01.html
	第三步：在项目的入口文件，引入Smarty3.0入口文件
	第四步：实例化Smarty对象
	第五步：通过assign方法分配变量到模板文件
	第六步：通过display方法显示输出模板信息
	第七步：在demo01.html模板文件中，放入要引入的标签，记住Smarty中的变量要求添加$符号
7、Smarty执行流程
	当系统第一次访问模板文件时，系统会自动在编译目录templates_c目录下自动生成该模板文件的编译文件，如果下次访问时，模板文件木有变化，系统会自动调用编译文件。

	问题：Smarty系统如何判断模板文件是否发生变化呢？
	答：在编译文件中，编译文件记录了模板文件的最后修改时间，如果该事件没有变化代表模板没有任何改变：

	其实无论是Window系统还是Linux系统，每个文件都有三个时间
	我们对文件的任何修改都会反映在修改时间上
8、Smarty执行流程图

三、Smarty中细节（常用属性与方法）
1、常用属性
	left_delimiter ：模板标签的左分界符，默认使用{
	right_delimiter ：模板标签的右分界符，默认使用}
	在实际项目开发中，如果想更改默认的标签，可以更改以上两个属性。
	如果以上代码更改了模板标签，那么在模板中其标签的标识符也要相应的更改。
	有些公司为了强调公司的标识，通过更改此选项。
2、相关属性
	问题：为什么我知道模板文件夹名称叫做templates，编译目录叫做templates_c呢？
	答：主要是由以下四个属性决定的
	template_dir	//模板路径
	complile_dir	//编译路径
	config_dir		//配置路径
	cache_dir		//缓存路径
	在Smarty3.0版本之前，以上4个属性是公有的，
	var template_dir;
	var compile_dir;
	var config_dir;
	var cache_dir;
	可以直接在类外直接调用，到了Smarty3.0版本之后，以上四个属性编程了私有属性，代码如下：
	zend工具使用小技巧：
	ctrl+鼠标左键单击类名或方法名就可以找到其在系统中的源代码


	所以默认情况下，模板文件目录就叫做templates，编译目录就叫做templates_c，虽然以上四个属性都是私有属性，但是在类的外部依然可以直接调用，为什么呢？

	运行后，系统依然可以正常访问到tpl下的模板文件，主要原因是？
	答：在php5版本后，增加两个魔术方法，分别__set($name,$value)与__get($name)，所以以上4个属性才可以直接访问。

	默认情况下，系统是不允许我们直接访问私有属性或不存在属性，但是可以通过__set与__get进行设置。

	虽然通过以上两个魔术方法可以直接设置默认属性，但是强烈建议使用如下4个方法对其进行设置：
3、设置相关属性方法
	setTemplateDir() 	：设置模板目录
	setCompileDir() 	：设置编译目录
	setConfigDir() 		：设置配置文件目录
	setCacheDir() 		：设置缓存目录

4、两个常用方法
	① assign()方法（分配要替换的变量到模板文件中）
	调用该方法主要主要是为Smarty模板文件中的变量赋值，可以传递一对名称/数值对，也可以包含名称/数值对的关联数组
	② display()方法（进行变量替换与模板输出）
	基于smarty的脚本必须使用这个方法，而且一个脚本中只能使用一次，因为它负责获取和显示由Smarty引擎引用的模板
5、特别注意
	① 如果在部署项目时，如果没有创建templates_c目录，那么系统在运行时，会自动创建此文件，但是强烈建议大家不要依赖此功能，因为有些操作系统，是不允许创建文件夹的如Linux。
	② 模板文件不一定都是以.htm或.html结尾，在Smarty中，只要是一个文本文档，任何后缀都是允许的。
	例:$smarty->display('demo.tpl');
四、设计篇
	1、模板注释	{*注释内容*}
	例:{*这是一个Smarty模板注释*}
	说明：Smarty注释属于服务器端注释，在实际运行时，其并不会显示在客户端源代码中。
	2、模板变量
	① 从php文件中分配过来的变量
	【普通变量】	{$title}
	【数组变量】	
		一维数组遍历:
			{$lamp[0]}
			{$lamp[1]}
			{$lamp[2]}
			{$lamp[3]}
		二维数组遍历:
			{$person[0]['name']}
			{$person[0]['age']}
			{$person[0]['email']}
	【对象变量】
		<?php
			//定义一个对象数据
			$std = stdClass();
			$std->name = 'Susan';
			$std->age = '20';
			$smarty->assign('std',$std);
		?>
		{*这是一个对象数据*}
		{$std->name}
		{$std->age}
	② 从配置文件中读取的变量
	可以在系统中定义一个配置文件夹，名称为configs，此名字从何而来
	在Smarty中的配置文件，主要是定义前面模板的相关信息，可以在configs文件夹中创建以.conf为后缀的配置文件

	configs/config.conf
	title=Smarty模板引擎

	等号左边代表变量名称，右边代表变量的值
	定义完成后，可以在模板文件中通过以下代码进行访问：

	{*从配置文件中读取的变量*}
	{config_load_file='config.conf'}
	{#title#}

	③ 在模板文件中直接定义的变量

	{*在模板中直接定义变量*}
	{assign  var='name' value='value'}
	{$name='zhangsan'}
	{$name}

3、系统中保留变量
	以下变量可以允许用户直接在模板文件中读取系统变量
	$smarty.get.page：	相当于$_GET[‘page’]
	$smarty.post.page：	相当于$_POST[‘page’]
	$smarty.cookies.username：相当于$_COOKIE[‘username’]
	$smarty.server.SERVER_NAME：相当于$_SERVER[‘SERVER_NAME’]
	$smarty.env.Path：相当于$_ENV[‘Path’]
	$smarty.session.id：相当于$_SESSION[‘id’]
	$smarty.request.username：相当于$_REQUEST[‘username’]

	{$smarty.now}：获取当前系统时间的时间戳
	{$smarty.const}：获取PHP中的常量信息
	{$smarty.capture}：用于输出capture标签捕获的内容
	{$smarty.config}：获取配置文件信息，相当于{#变量名称#}
	{$smarty.section}：获取section循环的相关信息
	{$smarty.template}：获取正在操作的模板信息
	{$smarty.current_dir}：获取当前工作目录
	{$smarty.version}：获取Smarty的版本号
	{$smarty.ldelim}或{ldelim}：左分隔符
	{$smarty.rdelim}或{rdelim}：右分隔符

4、变量调节器
	变量调节器主要功能是对系统中的变量进行格式化操作，在Smarty中一共具有21中变量调节器。
	基本语法：
	{$变量|调节器:参数1:参数2:……} 
	常用的变量调节器如下：

	{$var|capitalize} ：首字母大写
	{$var|count_characters:true} : 计算字符数，如果为true,代表计算空格
	{$var|cat:var2} ：字符串连接操作
	{$var|count_paragraphs} ：计算段落数
	{$var|count_sentences} ：计算句子数
	{$var|count_words} ：计算单词数
	{$var|date_format:”%Y%m%d %H:%M:%S”} ：时间格式化（把时间戳格式化为时间）
	{$var|default:”value”} ：默认值
	{$var|escape} ：html转码,转成文本
	{$var|indent:10:”*”} ：缩进
	{$var|lower} ：全部转小写
	{$var|nl2br} ：把换行符转化为br标签
	{$var|regex_replace:”/[\t\n]/”:””} ：正则替换
	{$var|replace:”aa”:”bb”} ：字符串替换
	{$var|spacify:”^^”} ：插空操作
	{$var|string_format:”%d”} ：字符串格式化，%d格式化为整型，%s格式化为字符串
	{$var|strip: “*”} ：去除多余的空格
	{$var|strip_tags} ：去除html标签
	{$var|truncate:30:”…”} ：字符串截取
	{$var|upper} ：全部转大写
	{$var|wordwrap:30:”<br>”} ：行宽约束
5、变量调节器组合应用
	{$var|capitalize|truncate:30:”…”|replace:”aa”:”bb”}
	第一步：首先更改变量var的首字母大写
	第二步：截取30个字符长度，并用…替换最后面的三个字符
	第三步：替换变量中的aa为bb

五、系统内置函数
	1、capture内建函数
	主要功能：捕获一段内容，但是不输出
	基本语法：
	//定义方式,捕获内容
	{capture name=‘var’}
		内容
	{/capture}
	//调用方式,释放内容
	{$smarty.capture.var}

	2、config_load内建函数
	主要功能：载入配置文件到模板页面
	{config_load 	file=“file” section=“section”}
	参数说明：
	file：要载入的配置文件名称
	section：要载入的section节
	引用方式：
	{#var#}
	{$smarty.config.var}

configs/config.conf
title=Smarty模板引擎
[first]
title=一级页面
[second]
title=二级页面

	如果不指定section节，默认将引入全局变量title，如果设置section节，系统将引入局部变量。

{*config_load内建函数*}
{config_load file='config.conf' section='first'}
{#title#}或{$smarty.config.title}

	3、include内建函数
	主要功能：载入文件到模板中
	基本语法：
	{include   file=“file” assign=“var”  [var ...]}
	include原理图

{*载入页面头部*}
{include file='header.html'}
{*载入页面尾部*}
{include file='footer.html'}


4、foreach内建函数
	主要功能：实现对数组元素的遍历
	基本语法：
	{foreach from=数组 item=内容 key=键 name=循环的名称} 
	数组遍历代码
	{foreachelse} 
	代码
	{/foreach}
	参数说明：
	from：要遍历的数组元素，必选参数
	item：每次遍历时，系统会将遍历结果放入变量item选项中
	key： 遍历时，数组的键值
	name：foreach循环的名称
	foreachelse：如果要遍历的数组为空,则自动执行foreachelse语句段

例1：通过foreach循环遍历一维数组
{foreach from=$lamp item='value'}
	{$value}
{/foreach}

例2：通过foreach循环遍历二维数组
{foreach from=$persons item='row'}
	{$row['name']}-->{$row['age']}-->{$row['email']}<br/>
{/foreach}

例3：附加参数的使用
{foreach from=$lamp item='value' key='k'}
	{$k}:{$value}<br/>
{/foreach}

例4：foreachelse参数使用
{foreach from=$test item='value'}
	{$value}
	{foreachelse}
	未查询到相关数据
{/foreach}

例5：foreach附加参数（name）
$smarty.foreach.name.index      @index ：每次循环时的循环索引，默认从0开始
$smarty.foreach.name.iteration   @iteration ：循环迭代（当前第几次循环），默认从1开始
$smarty.foreach.name.first 	     @first ：当第一次遍历时，此值为真
$smarty.foreach.name.last 	     @last ：当最后一次遍历时，此值为真
$smarty.foreach.name.total 	     @total ：循环的总次数

{foreach from=$persons item='row' name='fr'}
	当前第{$smarty.foreach.fr.iteration}次循环:{$row['name']}
{/foreach}
当前共循环了{$smarty.foreach.fr.total}次

在Smarty3.0版本后，也可以通过如下方式进行调用：

{foreach from=$lamp item='value' key='k'}
	{$value@iteration} : {$value}<br/>
{/foreach}

六、作业
1、通过Smarty模板引擎编写分页程序
2、扩展：通过查询百度或相关资料学习一下ecshop开发（使用的是Smarty）
3、大家放假时，提前注册两个账号
http://mp.weixin.qq.com/ 注册微信公众平台，记得进行实名认证（不是微信认证），注册时选择订阅号
http://sae.sina.com.cn/ 注册一个新浪SAE账号，记得进行实名认证（7天）
