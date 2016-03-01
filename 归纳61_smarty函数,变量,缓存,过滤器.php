Smarty模板引擎（下）	1
一、内建函数（sysplugins文件夹中）	3
	1、include_php内建函数	3
	2、insert内建函数	3
	3、if  elseif  else内建函数	4
	4、ldelim或rdelim内建函数	4
	5、literal内建函数	5
	6、php内建函数	5
	7、strip内建函数	5
	8、section、sectionelse内建函数	6
二、自定义函数（plugins文件夹）	8
	1、counter计数器	8
	2、cycle轮换函数	8
	3、debug调试函数	9
	4、fetch函数	9
	5、html_image自定义函数	10
	6、html_table自定义函数	10
	7、html_checkboxes自定义函数	10
	8、html_options自定义函数	11
	9、html_radios自定义函数	11
三、综合案例——Smarty分页程序	11
	1、设计HTML界面	11
	2、引入Smarty（Smarty部署几步走）	12
	3、编写php入口文件	12
	4、整合Smarty代码到模板文件中	12
	5、扩展功能：实现数据的批量删除	13
四、程序篇	14
	1、常量	14
	2、变量	14
	3、常用方法	15
五、缓存技术	16
	1、什么是缓存技术	16
	2、Smarty缓存原理图	17
	3、缓存的开启	17
	4、缓存文件从何而来？	18
	5、缓存文件何时变化？	18
	6、Smarty执行原理图	19
	Smarty相关细节	19
	7、缓存检测	19
	8、清除缓存	20
	9、局部缓存	21
	10、单页面多缓存	22
	11、缓存集合	22
六、过滤器	23
	1、什么是过滤器	23
	2、过滤器分类	23
	3、过滤器的原理图	23
	4、使用过滤器	23
七、整合Smarty到MVC项目中	24
	1、在相关目录创建一个Tools文件夹，作为第三方扩展库	24
	2、复制Smarty核心目录libs到Tools文件夹下，并更名为Smarty 25	
	3、在Core文件夹下创建一个视图类View.class.php	25
	4、实现加载扩展库（Smarty）	25
	5、在核心控制器中实例化视图类（类的实例化）	26
	6、在任一控制器中，你都可以使用Smarty了	26

一、内建函数（sysplugins文件夹中）
1、include_php内建函数
{include_php  file="file" assign="var" once="true|false"}
主要功能：允许在模板页面直接引入php源代码
参数说明：
file：要载入文件的名称
assign：把载入的内容放入一个变量中
once：是否只包含一次
特别注意：include_php方法在Smarty3.0中已废弃，如果想继续使用此函数功能，可以引入SmartyBC.class.php类文件。

示例代码:
{*include_php内建函数*}
{include_php file='date.php' assign='dt'}
{$dt}
data.php
<?php
echo date('Y-m-d H:i:s');
?>

2、insert内建函数
主要功能：如果在实际项目开发中，你发现Smarty并没有封装你需要的功能，那么可以使用Smarty提供的接口对其进行扩充，方法如下：
{insert  name="func"  assign="var"  [var ...] }
参数说明：
name：扩充函数的名称，特别注意：在定义此函数时，必须以insert_开头，否则无效
assign：分配到函数中的变量，形式变量=变量值，在函数接收时以数组的形式进行呈现

示例代码:
insert.php
<?php
include 'Smarty/Smarty.class.php';
$smarty = new Smarty();
function insert_func($args){
	echo $args['info'].date('Y-m-d H:i:s')
}
$smarty->display('demo.html');
?>
demo.html
{*insert扩展函数*}
{insert name='func' info='当前系统时间'}

3、if  elseif  else内建函数
{if}
{elseif}
{else}
{/if}

示例代码:
{if $i==1}
史湘云
{elseif $i==2}
王熙凤
{else}
秦可卿
{/if}

4、ldelim或rdelim内建函数
主要功能：输出左右分隔符

示例代码:
在Smarty中可以使用{ldelim}if{rdelim}对分支条件进行判断

5、literal内建函数
基本语法：
{literal}…{/literal}
主要功能：
literal 标签区域内的数据将被当作文本处理，此时模板将忽略其内部的所有字符信息. 该特性用于显示有可能包含大括号等字符信息的css或 javascript 脚本. 当这些信息处于 {literal}{/literal} 标签中时，模板引擎将不分析它们，而直接显示.

6、php内建函数
基本语法：
{php}
	echo date(“Y-m-d”);
{/php}
主要功能：允许在Smarty模板中直接使用php源代码（不建议使用），此功能在Smarty3.0中已废弃，如果想使用此功能，可以引入SmartyBC.class.php

示例代码:
{php}
echo '元迎探惜';
{/php}

7、strip内建函数
基本语法：
{strip}
	//要格式化的代码
{/strip}
主要功能：
去除任何位于 {strip}{/strip} 标记中数据的首尾空格和回车. 这样可以保证模板容易理解且不用担心多余的空格导致问题。

8、section、sectionelse内建函数
{section  name=名称  loop=循环数组(次数) 	start=开始(0)   step=步阶(1)  max=最大循环次数}
{sectionelse}
{/section}
主要功能：实现对数组进行遍历操作
特别注意：section只能遍历索引下标从0开始且连续的索引型数组
参数说明：
loop：要遍历循环的数据，通过loop参数可以确定要遍历的次数
name：section名称，每次遍历时，系统会将遍历的索引下标放于此参数中
start：从哪个位置开始遍历，默认不指定则从0开始
step：步阶，每次遍历的步阶，如果不指定，每次前进一个步阶
max：指定最大的循环次

示例代码:
demo.php
<?php
include 'Smarty/Smarty.class.php';
$smarty = new Smarty();
$lamp = array('linux','apache','mysql','php');
$smarty->assign('lamp',$lamp);
$smarty->display('section.html');
?>
section.html
{*遍历一维数组*}
{section name='sy1' loop=$lamp}
	{$lamp[sy1]}
{/section}

对比section与foreach，可以得出以下结论：
在原生php代码中可以通过两种形式对数组进行遍历操作：
for循环
foreach循环
foreach循环是真正遍历到数组元素的内部，每次遍历时会将遍历结果放入指定的变量中
for循环只是通过确定数组元素的数量，来对数组进行循环输出而已
在Smary中，也有两种循环：
foreach与section对比得出，foreach循环与原生代码中的foreach功能完全一致，而section循环功能上与原生代码中的for循环功能一致，并没有真正遍历数组，只是确定数组元素个数并循环输出而已。

遍历二维数组
{section name='sy2' loop=$persons}
	{$persons[sy2]['name']}-->{$persons[sy2]['age']}-->{$person[sy2]['email']}<br/>
{/section}

附加参数
{section name='sy3' loop=$name start=0 step=1 max=2}
	{$lamp[sy3]}
{/section}

如果在开发时，要遍历的数组为空时，系统将自动执行sectionelse语句段
{section name='sy3' loop=$name start=0 step=1 max=2}
	{$lamp[sy3]}
	{secctionelse}
	未查询到相关结果
{/section}

例5：几个常用的附加参数
{$smarty.section.name.index} ：循环索引，默认从0开始
{$smarty.section.name.index_prev} ：上一个循环索引，默认从-1开始
{$smarty.section.name.index_next} ：下一个循环索引，默认从1开始
{$smarty.section.name.iteration } ：循环迭代（当前是第几次循环）
{$smarty.section.name.first} ：当第一次循环时条件为真
{$smarty.section.name.last} ：当最后一次循环时条件为真
{$smarty.section.name.total} ：循环的总次数

{*遍历一维数组*}
{section name='sy1' loop=$lamp}
	{$smarty.section.sy1.iteration} : {$lamp[sy1]}
{/section}
当前共循环了{$smarty.section.sy1.total}次

二、自定义函数（plugins文件夹）
1、counter计数器
主要功能：完成计数功能
基本语法：
{counter start=0 skip=2 print=false} 
{counter}<br> 
{counter}<br> 
参数说明：
start：默认从多少开始计数，默认值为1
skip：步阶，默认为1
print：当前计数是否输出，默认为true

2、cycle轮换函数
主要功能：实现对数据的轮换显示（常用于隔行变色）
基本语法：
<tr bgcolor=“{cycle values="#eeeeee,#d0d0d0"}”> 
参数说明：
values：实现对数据进行轮换显示，第一次循环时，输出第一个值，第二次循环时，输出第二个值，第三次循环时，输出第一个值…

3、debug调试函数
主要功能：弹出调试模板，显示输出模板中的变量信息
基本语法：
{debug}

4、fetch函数
主要功能：
载入文件到模板中，基本语法：
{fetch 	file=“file” assign=“var”}
参数说明：
file：载入文件的名称，路径是相对php入口文件的
assign：把载入的内容分配给assign变量中

{*fetch函数*}
{fetch file='data.txt' assign='data'}
{$data|nl2br}

5、html_image自定义函数
主要功能：完成图片的载入，与HTML中的img标签功能一致
{html_image file="pumpkin.jpg"}
参数说明：
file：要载入图片的名称

{*载入图片*}
{html_image file='1.jpg'}

6、html_table自定义函数
主要功能：把数组转化为简单的表格
基本语法：
{html_table 	loop=$data 	cols=4 	table_attr='border="0"'}
参数说明：
loop：要遍历的数组元素
cols：每行显示多少列
table_attr：表格的属性，多个属性请使用空格隔开

{*输出一个简单的表格*}
{html_table loop=$persons cols='2' table_attr='width=600 border=1'}

7、html_checkboxes自定义函数
主要功能：生成checkbox复选框
{html_checkboxes name=’cust’  values=$cust_ids  checked=$customer_id  output=$cust_names   separator="<br />"} 
参数说明：
name：复选框名称，主要用于服务端接收此参数
values：用于设置checkbox元素中的value属性，要求是一个数组
checked：被选中的元素，要求也是一个数组
output：要显示输出的文本信息，要求也是一个数组
separator：元素与元素之间的分隔符

{*输出一个复选框*}
{html_checkboxes name='hobby' value=$value checked=$checked output=$output separator='<br/>'}

8、html_options自定义函数
主要功能：输出<option></option>选项
基本语法：
<select name=customer_id> 	
{html_options 	values=$cust_ids  	selected=$customer_id    output=$cust_names} 
</select> 
参数说明：
values：option的value值，要求参数是一个数组
selected：被选中的值，要求参数也是一个数组
output：要输出的文本值，要求也是一个数组

{*输出一个下拉选框*}
<select name='hobby' multiple='multiple'>
	{html_options values=$values selected=$checked output=$output}
</select>

9、html_radios自定义函数
主要功能：生成单选框
基本语法：
{html_radios  values=$cust_ids  checked=$customer_id   output=$cust_names separator="<br />"}
参数说明：
values：radio单选框的value值，要求是一个数组
checked：固定值，选中的选项值
output：显示的文本信息，要求也是一个数组
separator：元素与元素之间的分隔符

{*输出一个单选框*}
{html_radios name='hobby' values=$values checked=2 output=$output separator='<br/>'}

三、综合案例——Smarty分页程序
1、设计HTML界面
在templates文件夹下创建一个fenye.html静态页面，编写代码如下：

2、引入Smarty（Smarty部署几步走）
1）复制libs目录当前项目中，并更名为Smarty
2）创建相关文件夹(templates/templates_c/configs)
3）创建模板文件，如果已经创建完成，直接复制到templates文件夹即可
4）创建php入口文件
3、编写php入口文件
代码太多，详情请参考fenye.php
4、整合Smarty代码到模板文件中

5、扩展功能：实现数据的批量删除
① 在列表添加复选框
② 用一个form表单把整个列表围起来
③ 创建deal.php动态页面实现批量删除功能
四、程序篇
1、常量
SMARTY_DIR ：核心常量
2、变量
•$template_dir ：模板目录，默认是templates文件夹
•$compile_dir ：编译目录，默认吗是templates_c文件夹
•$config_dir ：配置目录，默认是configs文件夹
•$cache_dir ：缓存目录，默认是cache文件夹
•$left_delimiter ：左分隔符
•$right_delimiter ：右分隔符
以上六个变量主要用于更改Smarty对象的默认行为。
•$caching ：缓存开关，布尔类型，true代表开启，false关闭，默认是关闭的
•$cache_lifetime ：缓存生命周期，默认3600秒
以上两个变量主要用于开启缓存并设置缓存周期
•$debugging ：是否开启调试，默认为false，功能与{debug}完全一致
•$php_handling ：是否允许在模板页面引入php代码
3、常用方法
•assign ：分配变量到模板文件（按值传递）
•assignByRef ：分配变量到模板文件（按引用传递）
•append ：追加不同的数据到模板的数组变量中

•appendByRef ：追加不同的数据到模板的数组变量中（按引用传递）
•clearAllAssign ：清除所有分配到模板中的变量

•clearAssign ：清除指定的模板变量
•clearCache ：清除缓存
•configLoad ：载入配置文件

在模板文件中，可以通过{config_load file=’name’ section=’节’}
•clearConfig ：清除配置文件
•display ：显示输出模板内容
① 载入文件内容
② 替换模板变量
③ 显示输出模板内容
•fetch ：捕获模板内容但不输出，功能与display差不多
① 载入文件内容
② 替换模板变量

在实际项目开发中，以上程序有何作用？
答：可以通过此函数生成静态化文件
编译速度  <  缓存速度   <  静态化技术

•templateExists ：判断模板是否存在，布尔类型

五、缓存技术
1、什么是缓存技术
编译速度    <    缓存速度     <    静态化技术
2、Smarty缓存原理图
Smarty缓存不同于浏览器缓存，属于服务端缓存技术。
客户端缓存：
服务端缓存：
Smarty就属于服务端缓存。
缓存在实际项目开发中有哪些作用呢？
1）加快网站的访问速度
2）减少数据库服务器的压力
3、缓存的开启
① 在项目目录中创建一个cache文件夹作为缓存目录
② 在服务端入口文件开启缓存开关并设置缓存的生命周期
4、缓存文件从何而来？
缓存文件是由编译文件直接生成的
5、缓存文件何时变化？
① 在模板文件发生改变时，缓存文件受到编译文件的影响也会发生改变
② 在缓存文件过期时，缓存文件也要重新生成
6、Smarty执行原理图
7、缓存检测
$smarty->isCached(“tpl.tpl”) ：判断某个模板文件是否具有缓存
实际应用：可以使用此方法来减少对数据的读取操作，减少服务器的压力
以上程序只在第一次访问时链接了数据库，以后每次访问都自动读取缓存文件。
8、清除缓存
思考：缓存技术这么好，为什么需要清除缓存？
答：由于项目上线后，系统模板基本没有任何变化，生命周期也主要是由服务端时间决定，所以即使后台更新了数据，前台必须等到缓存过期才可能得到最新的数据，为了解决以上问题，所有的系统都会做一个清除缓存功能。
基本语法：
$smarty->clearCache(“tpl.tpl”) ：清除某个指定的缓存（清除某个文件）
$smarty->clearAllCache() ：清除所有缓存（后台清除缓存按钮常用此函数）
特别说明：
清除缓存完全不需要依赖任何外在条件，即使不开启缓存，也可以进行清理。
实际清除缓存，主要是删除cache下的文件。
9、局部缓存

在程序中如果想不缓存某个板块或变量可以使用如下三个方法：
在入口文件中添加以下代码：
$smarty->assign(“var”, “value”, true) ：分配变量到模板文件，实际其有第三个参数，如果此值为true，代表当前变量不缓存。
在模板文件中添加以下代码：
{$var nocache=true} ：为模板中的变量添加一个nocache属性，也可以不缓存
{nocache}{/nocache} ：可以标注某个板块不缓存
10、单页面多缓存
主要功能：用于详细页面、内容展示页面
http://www.shop.com/index.php?c=goods&a=show&id=10 
http://www.shop.com/index.php?c=goods&a=show&id=11 
http://www.shop.com/index.php?c=goods&a=show&id=12 
http://www.shop.com/index.php?c=goods&a=show&id=13 
在以上url地址中，前面的链接是不会发生任何改变的，但是id在每个产品都是不同的。
但是缓存文件主要是判断模板得到的，所以和地址并没有任何关系，所以会导致只有第一个访问者可以看到想要的内容，以后每个访客都只能看到第一个访问者的缓存文件，那么如何解决这种问题呢？
$smarty->display(“tpl”, 缓存标识（必须是唯一的）);
11、缓存集合
主要用于列表页，产品列表页面
http://www.shop.com/index.php?c=goods&a=show&cateid=10&page=18  
http://www.shop.com/index.php?c=goods&a=show&cateid=11&page=15 
http://www.shop.com/index.php?c=goods&a=show&cateid=11&page=12 
http://www.shop.com/index.php?c=goods&a=show&cateid=12&page=10 
如果开发中遇到了以上问题，可以使用缓存集合来解决此问题
基本语法：
$smarty->display(“tpl”, $id1.”|”.$id2)

六、过滤器
1、什么是过滤器
主要功能是实现对数据的过滤操作，Smarty其主要是实现对显示数据的过滤
2、过滤器分类
•Prefilters ：预过滤器
•Postfilters ：后过滤器
•Output Filters ：输出过滤器

tpl源文件 =〉Prefilter =〉编译tpl文件 => Postfilter =>保存到磁盘=> 编译过的php文件执行=〉Output Filters（=〉如果有smarty cache的话，Output Filters的内容会缓存） =>结果输出。
3、过滤器的原理图

在实际项目开发中，预过滤器与后过滤器并没有实际应用，但是输出过滤器应用比较广泛，常用于实现敏感数据过滤。
4、使用过滤器
•$smarty->registerFilter($type, $callback)
•$type：定义过滤器的类型
–pre  预过滤器
–post 后过滤器
–output 输出过滤器
•$callback：自定义函数
七、整合Smarty到MVC项目中
1、在相关目录创建一个Tools文件夹，作为第三方扩展库
2、复制Smarty核心目录libs到Tools文件夹下，并更名为Smarty
3、在Core文件夹下创建一个视图类View.class.php
4、实现加载扩展库（Smarty）
在Think.class.php核心文件中实现类库自动加载，在文件中创建一个函数，如下图所示：
5、在核心控制器中实例化视图类（类的实例化）
6、在任一控制器中，你都可以使用Smarty了


