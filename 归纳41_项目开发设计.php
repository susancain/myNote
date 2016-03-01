项目开发

搭建目录结构

1.确定网站根目录:E:/class/php150710/shop

2.模拟ecshop: 前台和后台没有分开,只是通过文件夹进行分层实现

3.在后台中建立相应的目录结构

4.增加虚拟主机: www.shop.com

5.将后台所有的模板文件都放到/admin/templates/


登录功能
任何一个功能,一定是顺着是用户的操作开始.
要求: 所有的相关请求请求同一个php文件: 如登录,退出,获取登录表单,都请求权限对应的PHP文件: privilege

显示表单

1.输入URL: http://www.shop.com/admin/privilege.php /admin/privilege.php

2.权限管理要做的事情: 获取登录表单. /admin/privilege.php

3.将样式,js和图片文件全部加载到网站中: 从ecshop中获取过来. 分别放到admin/styles, admin/images, admin/js


用户表单提交
1.修改表单提交对象: form中的action. /admin/templates/login.html

2.用户输入用户名和密码之后: privilege.php应该处理用户的提交操作,需要通过判断用户的请求来确定处理方式. /admin/privilege.php

需要为用户的请求分配一个动作关键字: 通过动作来区分用户的具体请求, 从而实现服务器为不同的请求作出不同的处理. 动作: action(act)
A)在表单中增加一个参数:act, 隐藏域 /admin/templates/login.html

B)	后台应该通过用户的动作(act)来判断用户的请求: 接收动作 /admin/privilege.php

C)	根据动作进行不同的服务处理. /admin/privilege.php

3.接收用户提交的数据. /admin/privilege.php|act=check

4.合法性验证数据, 用户名和密码都不能为空. /admin/privilege.php|act=check

5.解决乱码问题: header解决. /admin/privilege.php

5.合理性验证: 用户名和密码在数据库能够配对.
a)搭建数据库环境

b)任何数据库的操作都必须最终是由指定的DB类来实现.将DB类引入项目. /admin/includes/DB.class.php

c)任何一张表的操作都应该有一个专门的类: 对其进行操作(类的作用是封装SQL语句). /admin/includes/Admin.class.php

6.调用Admin类进行对表sh_admin的数据查询操作: 用户名和密码. /admin/privilege.php
a)通过用户名获取用户信息: 可以判断用户名是否存在

增加通过用户名获取用户信息的方法. /admin/includes/Admin.class.php

防止用户SQL注入

判断查询结果: 错误处理: 用户不存在. /admin/privilege.php|act=check

b)将取出的用户信息与用户输入的密码进行比较:确定密码是否正确


初始化文件

将所有的非业务逻辑性的代码存放到一个公共文件中: init.php

1.在公共目录下创建init.php. /admin/includes/init.php

2.初始化文件既然要被所有文件包含: 解决公共问题: 脚本显示字符集问题

3.通过项目的初始化文件,控制整个项目对待错误的处理方式.ini_set函数

4.建立目录常量: 任何一个目录都建一个常量保存对应的绝对路径.

5.任何一个被用户直接可以访问的PHP文件(如privilege.php)应该加载init.php文件,并且可以使用里面的内容. /admin/privilege.php


封装公共函数
1.建议一个专门公共函数脚本: functios.php. /admin/includes/functions.php

2.创建第一个公共函数: 跳转提示函数

3.将公共函数文件包含到init.php中. /admin/includes/init.php

4.凡是加载了init.php的php文件都可以直接使用公共函数./admin/privilege.php|act=check


修改提供跳转提示机制
以前利用header实现跳转提示: 现在在项目中使用html中的标签<meta>实现跳转

1.在跳转函数中加载跳转html文件. /admin/includes/functions.php

2.修改跳转模板,显示对应的数据. /admin/templates/redirect.html


自动加载

1.自动加载是一种函数, 应当封装到公共函数文件. /admin/includes/functions.php

2.在需要类的地方可以直接使用,而不需要手动加载类./admin/privilege.php|act=check



配置文件
配置文件: 用来控制项目的运行(核心内容控制): 有一些修改需要发生的时候, 一定需要进入到项目原代码. 在项目的外围(单独有个文件)来对核心部分进行控制.

1.新增配置文件: 利用脚本中的return. /admin/conf/config.php

2.使用配置文件: 任何一个后台脚本都需要使用数据库操作: 都会需要数据库信息. 在公共文件中加载配置文件. /admin/includes/init.php

3.在需要使用数据库信息操作数据库的位置,使用配置文件信息. /admin/includes/DB.class.php


首页功能

1.登录成功,跳转到首页. /admin/privilege.php|act=check

2.进入系统首页: index.php. /admin/index.php

3.类似ecshop,后台应该是框架结构. 加载框架. /admin/index.php

4.修改框架: 对每个frame增加src,找到对应的部分. /admin/templates/index.html

5.光只请求index.php无法实现加载具体的frame: 意味着浏览器必须传入参数才能告诉服务器到底该加载什么内容. /admin/templates/index.html

6.服务器应该根据不同的参数,作出不同的服务响应. /admin/index.php