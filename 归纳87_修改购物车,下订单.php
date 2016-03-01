
电商网站（六）
目录
一、修改购物车	1
   1、复制两个图标，“+” 和“-”到购物车列表页面，完成显示	1
   2、给“+”和”-”添加单击事件。	2
二、删除购物车	5
   1、给购物车列表里面的删除按钮添加链接。	5
   2、在购物车模型里面，新建一个delCart的方法，	6
   3、在购物车的控制器里面，添加一个方法delCart	6
三、清空购物车；	6
   1、给购物车列表页面的“清空购物车”按钮添加链接	7
   2、在购物车模型里面添加一个方法，用于清空购物车数据	7
   3、在购物车的控制器里面添加一个方法clearCart	7
四、下订单	8
   1、订单表的建立	8
   （1）订单基本信息表it_order_info	8
   （2）订单商品关联表it_order_goods	8
   2、下单准备	8
   （1）判断购物车里面是否有商品	9
   （2）判断是否登录	9
   （3）判断是否填写收货人的信息；	10
   （4）填写收货人的信息，	10
   （5）拷贝flow方法对应的静态页面，	11
   3、下订单完成	12
   （1）在flow.html页面遍历出购物车商品的数据	12
   （2）修改提交订单的表单。	13
   （3）在表单中添加一些隐藏域：	13
   （4）在order控制器下面新建一个done的方法，完成下订单。	15


一、修改购物车
1、复制两个图标，“+” 和“-”到购物车列表页面，完成显示

代码：
Home/View/Cart/cartList.html

<img src="__PUBLIC__/Home/images/decr.gif" alt="">
<img src="__PUBLIC__/Home/images/add.gif" alt="">
效果：


2、给“+”和”-”添加单击事件。
（1）给”+”和“-”套用一个a标签，给a标签添加一个类属性。
Home/View/Cart/cartList.html

<a href="javascript:" class="add"><img src="__PUBLIC__/Home/images/add.gif" alt=""></a>

（2）引入jquery.js
Home/View/Cart/cartList.html

<js href="__PUBLIC__/Js/jquery.js"/>
（3）添加单击事件，完成js代码
Home/View/Cart/cartList.html

 <js href="__PUBLIC__/Js/jquery.js"/>
        <script>
         $(function(){
            $(".add").click(function(){
               //(1)求出商品的单价(本店价格)
               var dj = parseFloat($(this).parent().parent().find("span:first").html());
               //(2)取出原来的小计价格,
               var xiaoji_price = parseFloat($(this).parent().parent().find("span:last").html());
               //(3)计算新的小计价格=原来的小计价格+商品的单价
               var new_xiaoji_price = xiaoji_price + dj;             
               //(4)取出原来的购买数量
               //var goods_count = $(this).prev().val();
               var goods_count = parseInt($(this).parent().find("input[name=goods_count]").val());
               //(5)计算新的购买数量new_goods_count = 原来的购买数量+1
               var new_goods_count = goods_count+1;
               //(6)计算新的购买总金额 = 原来的购买金额+商品的单价
               var new_total_price = parseFloat($("#total_price").html())+dj;
               //Ajax完成数据库或cookie里面的修改
               //取出商品的id
               var goods_id = parseInt($(this).parent().find("input[name=goods_id]").val());
               //取出商品的属性
               var goods_attr_id = $(this).parent().find("input[name=goods_attr_id]").val();
               
               var _this=$(this);
               $.ajax({
                  type:'get',
                  url:'__CONTROLLER__/updateCart/goods_id/'+goods_id+'/goods_attr_id/'+goods_attr_id,
                  success:function(msg){                    
                     //修改成功后,把新的购买数量,新的小计价格,新的总金额显示到页面中
                     if(msg='ok'){
                        //显示新的购买数量
                        _this.parent().find("input[name=goods_count]").val(new_goods_count);
                        //新的小计价格
                        _this.parent().parent().find("span:last").html(new_xiaoji_price);
                        //新的总金额
                        $("#total_price").html(new_total_price);
                     }
                  }
               });
            });
         });
      </script>

（4）在 购物车的模型里面添加一个修改购物车的方法。
updateCart($goods_id,$goods_attr_id)

Home/Model/CartModel.class.php

//修改购物车的方法
public function updateCart($goods_id,$goods_attr_id,$goods_count){
   //判断用户是否登录,如果已经登录.则修改数据库,如果没有登录,则修改cookie
   $user_id = $_SESSION['user_id'];
   if($user_id){
      //已经登录,修改数据库,修改购买数量
      $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->setInc("goods_count",$goods_count);
         
         
   }else{
      //没有登录,则修改cookie
      //取出cookie里面的数据
      $cart = isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
      //构造数组的键$array('商品的id-商品属性id'=>购买数量)
      $key = $goods_id.'-'.$goods_attr_id;
      $cart[$key]=$cart[$key]+$goods_count;
      setcookie('cart',serialize($cart),time()+3600*24*7,'/');
   }
}
（5）在购物车的控制器里面，新建一个修改购物车的方法update()
Admin/Controller/CartController.class.php

//修改购物车的方法
public function updateCart(){
   //接收传递的商品id和属性的id
   $goods_id = $_GET['goods_id']+0;
   $goods_attr_id = $_GET['goods_attr_id'];
   $goods_count = 1;
   //调用模型里面修改购物车的方法
   $cartmodel = D('Cart');
   $cartmodel->updateCart($goods_id,$goods_attr_id,$goods_count);    
   echo 'ok';
}
（6）注意js代码：
var _this=$(this);
$.ajax({
   type:'get',
   url:'__CONTROLLER__/updateCart/goods_id/'+goods_id+'/goods_attr_id/'+goods_attr_id,
   success:function(msg){                    
      //修改成功后,把新的购买数量,新的小计价格,新的总金额显示到页面中
      if(msg='ok'){
         //显示新的购买数量
         _this.parent().find("input[name=goods_count]").val(new_goods_count);
         //新的小计价格
         _this.parent().parent().find("span:last").html(new_xiaoji_price);
         //新的总金额
         $("#total_price").html(new_total_price);
      }
   }
});

二、删除购物车
1、给购物车列表里面的删除按钮添加链接。
Home/View/Cart/cartList.html

<a href="__CONTROLLER__/delCart/goods_id/<?php echo $v['goods_id']?>/goods_attr_id/<?php echo $v['goods_attr_id']?>">删除</a>
2、在购物车模型里面，新建一个delCart的方法，
Home/Model/CartModel.class.php

//删除购物车的一个方法
public function delCart($goods_id,$goods_attr_id){
   //要判断用户是否登录,如果已经登录,则从数据库里面删除;如果没有登录,则从cookie里面删除
   $user_id = $_SESSION['user_id'];
   if($user_id>0){
      //从数据库里面删除
      $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->delete();
   }else{
      //从cookie里面删除
      //取出cookie里面的数据
      $cart = isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
      //构造数组的键$array('商品的id-商品属性id'=>购买数量)
      $key = $goods_id.'-'.$goods_attr_id;
      unset($cart[$key]);
      setcookie('cart',serialize($cart),time()+3600*24*7,'/');
   }
}
3、在购物车的控制器里面，添加一个方法delCart
Home/Controller/CartController.class.php

//删除购物车
public function delCart(){
   //接收传递的商品id和属性的id
   $goods_id = $_GET['goods_id']+0;
   $goods_attr_id = $_GET['goods_attr_id'];
   //调用模型里面删除购物车的方法
   $cartmodel = D('Cart');
   $cartmodel->delCart($goods_id,$goods_attr_id);
   $this->redirect('Cart/Cartlist');
}

三、清空购物车；
在下完订单后，需要清空购物车的数据。
1、给购物车列表页面的“清空购物车”按钮添加链接
Home/View/Cart/cartList.html

<input type="button" value="清空购物车" onclick="window.location.href='__CONTROLLER__/clearCart'">
2、在购物车模型里面添加一个方法，用于清空购物车数据
Home/Model/CartModel.class.php

//清空购物车
public function clearCart(){
   $user_id = $_SESSION['user_id'];
   if($user_id>0){
      //从数据库里面删除
      $this->where("user_id=$user_id")->delete();
   }else{
      //从cookie里面删除
      setcookie('cart','',time()-1,'/');
   }
}

3、在购物车的控制器里面添加一个方法clearCart
Home/Controller/CartController.class.php

//清空购物车
public function clearCart(){
   //调用模型里面删除购物车的方法
   $cartmodel = D('Cart');
   $cartmodel->clearCart();      
   $this->redirect('Index/index');
}

四、下订单
1、订单表的建立
（1）订单基本信息表it_order_info
#创建一个订单信息表
create table it_order_info(
   id int  primary key auto_increment,
   order_sn varchar(32) not null comment '订单的编号',
   order_amount decimal(9,2) not null comment '订单的总金额',
   user_id  int not null comment '登录用户的id',
   consignee varchar(32) not null comment '收货人的姓名',
   address varchar(64) not null comment '收货人的地址',
   mobile char(11) not null comment '收货人的手机',
   addtime int not null comment '下单时间',
   shipping varchar(12) not null comment '配送方式',
   payment varchar(12) not null comment  '支付方式'
)engine myisam charset utf8;
（2）订单商品关联表it_order_goods
#创建一个订单商品关联表
create table it_order_goods(
   id int  primary key auto_increment,
   order_id int not null comment '订单的id',
   goods_id int not null comment '商品的id',
   goods_name varchar(32) not null comment '商品的名称',
   goods_attr_id varchar(12) not null default '' comment '商品属性的id',
   shop_price decimal(9,2) not null comment '商品的单价',
   goods_count tinyint not null comment '购买数量'
)engine myisam charset utf8;
2、下单准备
新建一个order的控制器，添加flow的方法，完成下订单准备功能
（1）判断购物车里面是否有商品
（2）判断是否登录
Home/Controller/OrderController.class.php

<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extend Contorller
{
   public function flow(){
      //(1)判断购物车里面是否有商品
      $cartmodel = D('Cart');
      $total = $cartmodel->getCartTotal();
      if($total['total_count']==0){
         //说明购物和里面没有商品
         $this->error('购物车里面没有商品,无法下订单');
      }     
      //(2)判断用户是否登录,如果没有登录,则跳转到登录页面,登录后,在跳回来
      $user_id = $_SESSION['user_id'];
      if(empty($user_id)){
         //说明没有登录,则跳转到登录页面
         //记录当前位置,使用session
         $_SESSION['url']='Order/flow';
         $this->redirect('User/login');
      }        
   }
}
?>

修改user控制器里面的，登录方法：
Home/Controller/UserController.class.php

//添加用户的登录
public function login(){
   if(IS_POST){
      //完成登录的操作
      $usermodel = D('User');
      if($usermodel->validate($usermodel->_validate_login)->create()){
         if($usermodel->login()){
            //登录成功
            $cartmodel = D('Cart');
            $cartmodel->cookie2array();
            
            if(!empty($_SESSION['url'])){
               $url = $_SESSION['url'];
            }else{
               $url = "Index/index";
            }
            
            $this->success("登录成功",U($url));exit;
         }
      }
      //$usermodel->getError()是获取模型里面的error属性的内容
      $this->error($usermodel->getError());
   }
   //取出头部导航信息
   $catemodel = D('Admin/Category');
   $navdata = $catemodel->getNav();
   $this->assign('navdata',$navdata);
   $this->display();
}

（3）判断是否填写收货人的信息；
Home/Controller/OrderController.class.php

<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extend Contorller
{
   public function flow(){
      //(1)判断购物车里面是否有商品
      $cartmodel = D('Cart');
      $total = $cartmodel->getCartTotal();
      if($total['total_count']==0){
         //说明购物和里面没有商品
         $this->error('购物车里面没有商品,无法下订单');
      }     
      //(2)判断用户是否登录,如果没有登录,则跳转到登录页面,登录后,在跳回来
      $user_id = $_SESSION['user_id'];
      if(empty($user_id)){
         //说明没有登录,则跳转到登录页面
         //记录当前位置,使用session
         $_SESSION['url']='Order/flow';
         $this->redirect('User/login');
      }

      //(3)判断是否填写了收货人信息,如果没有填写,则跳转到填写收货人的信息
      $info = M('Address')->where("user_id=$user_id")->find();
      if(!$info){
         //说明没有填写收货人信息,则跳转到填写收货人的信息的页面
         $this->redirect('writeaddress');       
      }
      $this->assign('info',$info);        
   }
}
?>

新建一张表，用于存储收货人的信息。
#创建一个收货人的信息表
create table it_address(
   id int  primary key auto_increment,
   user_id  int not null comment '登录用户的id',
   consignee varchar(32) not null comment '收货人的姓名',
   address varchar(64) not null comment '收货人的地址',
   mobile char(11) not null comment '收货人的手机'
)engine myisam charset utf8;

（4）填写收货人的信息，
拷贝填写收货人的信息的页面，并修改表单，
Home/View/Order/writeaddress.html

<form action="__ACTION__">
   
</form>
提交表单，完成收货人信息的入库。
Home/Controller/OrderController.class.php

//完成收货人信息填写的一个页面
public function writeaddress(){
   if(IS_POST){
      $consignee = I('post.consignee');
      $address = I('post.address');
      $mobile = I('post.mobile');
      $user_id = $_SESSION['user_id'];
      $id = M("Address")->add(array(
         'consignee' => $consignee,
         'address'   => $address,
         'mobile'    => $mobile,
         'user_id'   => $user_id,
      ));
      if($id){
         $this->success('添加收货人成功',U('flow'));
      }
   }
   //取出头部导航信息
   $catemodel = D('Admin/Category');
   $navdata = $catemodel->getNav();
   $this->assign('navdata',$navdata);           
   
   //取出购物车总的数量和金额
   $cartmodel = D('Cart');
   $cart_total = $cartmodel->getCartTotal();
   $this->assign('cart_total',$cart_total);
   
   $this->display();
}

（5）拷贝flow方法对应的静态页面，
Home/Controller/OrderController.class.php

//完成下订单
public function flow(){
   //(1)判断购物车里面是否有商品
   $cartmodel = D('Cart');
   $total = $cartmodel->getCartTotal();
   if($total['total_count']==0){
      //说明购物和里面没有商品
      $this->error('购物车里面没有商品,无法下订单');
   }     
   //(2)判断用户是否登录,如果没有登录,则跳转到登录页面,登录后,在跳回来
   $user_id = $_SESSION['user_id'];
   if(empty($user_id)){
      //说明没有登录,则跳转到登录页面
      //记录当前位置,使用session
      $_SESSION['url']='Order/flow';
      $this->redirect('User/login');
   }        
   //(3)判断是否填写了收货人信息,如果没有填写,则跳转到填写收货人的信息
   $info = M('Address')->where("user_id=$user_id")->find();
   if(!$info){
      //说明没有填写收货人信息,则跳转到填写收货人的信息的页面
      $this->redirect('writeaddress');       
   }
   $this->assign('info',$info);
   
   //取出头部导航信息
   $catemodel = D('Admin/Category');
   $navdata = $catemodel->getNav();
   $this->assign('navdata',$navdata);
   
   //取出购物车列表数据
   $cartmodel = D('Cart');
   $cartdata = $cartmodel->cartList();
   $this->assign('cartdata',$cartdata);
   
   //取出购物车总的数量和金额
   $cart_total = $cartmodel->getCartTotal();
   $this->assign('cart_total',$cart_total);
   
   $this->display();
}

3、下订单完成
（1）在flow.html页面遍历出购物车商品的数据
Home/View/Order/flow.html

<?php foreach($cartdata as $v){?>
   <?php echo $v['info']['goods_name']?>
   <?php echo $v['attr']?>
   <?php echo $v['info']['shop_price']?>
   <?php echo $v['goods_count']?>
   <?php echo $v['info']['shop_price']*$v['goods_count']?>
<?php }?>
<?php echo $cart_total['total_price']?>
显示出收货人的信息：
Home/View/Order/flow.html

<?php echo $info['consignee']?>
<?php echo $info['address']?>
<?php echo $info['mobile']?>

（2）修改提交订单的表单。
Home/View/Order/flow.html

<form action="__CONTROLLER__/done" method="POST">
   
</form>
（3）在表单中添加一些隐藏域：
收货人的信息。
Home/View/Order/flow.html

<input type="hidden" name="consignee" value="<?php echo $info['consignee']?>"/>
<input type="hidden" name="address" value="<?php echo $info['address']?>"/>
<input type="hidden" name="mobile" value="<?php echo $info['mobile']?>"/>

（4）在order控制器下面新建一个done的方法，完成下订单。
Home/Controller/OrderController.class.php

//已完成下订单
public function done(){
   //取出头部导航信息
   $catemodel = D('Admin/Category');
   $navdata = $catemodel->getNav();
   $this->assign('navdata',$navdata);
   
   //取出购物车总的数量和金额
   $cartmodel = D('Cart');
   $cart_total = $cartmodel->getCartTotal();

   //入库it_order_info表(订单信息表)
   //接收提交的数据
   $consignee = I("post.consignee");
   $address = I("post.address");
   $mobile = I("post.mobile");
   $payment = I("post.payment");
   $shipping = I("post.shipping");
   $order_sn = 'sn'.uniqid();
   $order_amount = $cart_total['total_price'];
   $arr = array(
      'order_sn'=>$order_sn,
      'order_amount'=>$order_amount,
      'user_id'=>$_SESSION['user_id'],
      'consignee'=>$consignee,
      'address'=>$address,
      'mobile'=>$mobile,
      'addtime'=>time(),
      'shipping'=>$shipping,
      'payment'=>$payment,    
   );
   
   $order_id = M("OrderInfo")->add($arr);//返回自增的id
   
   //入库it_order_goods表(订单商品关联表)
   //取出购物车列表数据 
   $cartdata = $cartmodel->cartList();    
   foreach($cartdata as $v){
      $a=array(
         'order_id'=>$order_id,//订单信息表订单id
         'goods_id'=>$v['goods_id'],
         'goods_name'=>$v['info']['goods_name'],
         'goods_attr_id'=>$v['goods_attr_id'],
         'shop_price'=>$v['info']['shop_price'],
         'goods_count'=>$v['goods_count'],
      );
      M("OrderGoods")->add($a);
   }
   
   //清空购物车数据
   $cartmodel->clearCart();
   $this->assign('order_sn',$order_sn);
   $this->display();
}



出现的错误:





解决:

3.2.3版本开始，可以支持不执行SQL而只是返回SQL语句，例如：
1.$User = M("User"); // 实例化User对象
2.$data['name'] = 'ThinkPHP';
3.$data['email'] = 'ThinkPHP@gmail.com';
4.$sql = $User->fetchSql(true)->add($data);
5.echo $sql;
6.// 输出结果类似于
7.// INSERT INTO think_user (name,email) VALUES ('ThinkPHP','ThinkPHP@gmail.com')
字段过滤
如果写入了数据表中不存在的字段数据，则会被直接过滤，例如：
1.$data['name'] = 'thinkphp';
2.$data['email'] = 'thinkphp@gmail.com';
3.$data['test'] = 'test';
4.$User = M('User');
5.$User->data($data)->add();
其中test字段是不存在的，所以写入数据的时候会自动过滤掉。
在3.2.2版本以上，如果开启调试模式的话，则会抛出异常，提示：非法数据对象：[test=>test]
如果在add方法之前调用field方法，则表示只允许写入指定的字段数据，其他非法字段将会被过滤，例如：
1.$data['name'] = 'thinkphp';
2.$data['email'] = 'thinkphp@gmail.com';
3.$data['test'] = 'test';
4.$User = M('User');
5.$User->field('name')->data($data)->add();
最终只有name字段的数据被允许写入，email和test字段直接被过滤了，哪怕email也是数据表中的合法字段。
字段内容过滤
通过filter方法可以对数据的值进行过滤处理，例如：
1.$data['name'] = '<b>thinkphp</b>';
2.$data['email'] = 'thinkphp@gmail.com';
3.$User = M('User');
4.$User->data($data)->filter('strip_tags')->add();
写入数据库的时候会把name字段的值转化为thinkphp。
filter方法的参数是一个回调类型，支持函数或者闭包定义。











