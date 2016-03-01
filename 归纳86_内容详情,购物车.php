电商网站（五）
目录
一、内容详情页面	1
	（1）在后台的categroy模型里面新建一个方法，用于查找出父级栏目	1
	（2）在前台的index控制器里面的detail方法里面调用该方法，	2
	（3）把取出的面包屑导航给遍历到静态页面。	3
二、购物车	3
	1、购物车的实现方式有哪些？	3
	2、如何把购物车的数据存储到cookie里面	3
	3、如何把购物车数据，存储到数据库里面	4
	4、在前台模块里面，新建一个购物车模型(CartModel)，添加添加商品到购物车的方法（addCart()）。	5
	5、修改商品的详情页面准备提交到购物车的数据。	6
	（1）打开商品详情页面对应的detail.html页面，修改表单的提交位置：	7
	（2）修改购买数量的文本域：	7
	（3）添加一个隐藏域：	7
	（4）修改属性提交的值。	8
	（5）给提交按钮图片添加事件事件，完成表单提交。	8
	6、把商品提交到购物车	8
	7、购物车列表	9
	（1）在 cart控制器里面添加一个购物车列表的方法cartList,并拷贝对应的静态页面，	10
	（2）在购物车的模型里面，添加一个方法，用于取出购物车列表数据	10
	（3）根据购物车数据里面的 [goods_attr_id] => 48,51获取属性的名称以及属性的值	11
	（4）购物车数据的遍历：	12
	（5）取出购物车里面总的商品数量，以及总的金额	13
	8、当用户登录时，判断cookie里面是否有购物车的数据，如果有则移动到数据库。	15
	（1）在购物车的模型里面添加一个移动的方法。	15
	（2）登录成功后，调用该方法	16





一、内容详情页面
	完成面包屑导航：如下功能，



	思路：查找当前商品所属栏目，再找所属栏目的上一级栏目，一直找到顶级栏目就结束。
	（1）在后台的categroy模型里面新建一个方法，用于查找出父级栏目
Admin/Model/CategoryModel.class.php

//查找家谱树,通过子栏目查找父级栏目
public function getFamily($cat_id){
	$arr = $this->select();//获取栏目数据
	return array_reverse($this->_getFamily($arr,$cat_id));
}
public function _getFamily($arr,$cat_id){
	static $data = array();
	foreach($arr as $v){
		if($v['id']==$cat_id){
			$data[]=$v;
			$this->_getFamily($arr, $v['parent_id']);
		}
	}
	return $data;
}


	（2）在前台的index控制器里面的detail方法里面调用该方法，
Home/Controller/IndexController.class.php

//商品详情页面
public function detail(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	
	//接收传递商品的id
	$goods_id = (int)$_GET['goods_id'];
	//取出商品的详情信息
	$goodsmodel = D('Admin/Goods');
	$goodsinfo = $goodsmodel->where("id=$goods_id")->find();
	//判断是否取出商品的数据
	if(empty($goodsinfo)){
		header("location:/index.php");
	}
	$this->assign('goodsinfo',$goodsinfo);

	//取出面包屑导航的数据		
	$subnav = $catemodel->getFamily($goodsinfo['cat_id']);
	$this->assign('subnav',$subnav);
	
	//取出某商品的属性信息
	$sql = "select a.*,b.attr_name,b.attr_type from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=$goods_id";
	$attrdata = M()->query($sql);//返回的是二维数组;execute($sql)执行增删改的SQL语句;query($sql)执行有返回结果的SQL语句
	//M()返回的是空模型,就是基础模型的实例
	$radiodata = array();//定义一个空数组,用于存储单选属性的数据
	foreach($attrdata as $v){
		if($v['attr_type']==1){
			//是单选属性
			$radiodata[$v['attr_id']][] = $v;
		}
	}	
	$this->assign('radiodata',$radiodata);
	$this->display();
}
	（3）把取出的面包屑导航给遍历到静态页面。
Home/View/Index/detail.html

<?php foreach($subnav as $v){?>
	<a href="<?php echo U('Index/category',array('cat_id'=>$v['id']))?>"><?php echo $v['cat_name']?></a>
<?php }?>
二、购物车
1、购物车的实现方式有哪些？
可以把数据存储到cookie里面，特点是：可以长久保存购物车的数据。
可以把数据存储到sessoin里面，特点是：关闭浏览器则购物车数据丢失。
可以把数据存储到数据库里面，特点是：可以长久的保存购物车的数据。
可以把数据存储到memcache里面，也是可以的。
京东是这样的：
当用户没有登录时，则把购物车的数据存储到cookie里面的，
如果用户登录了，则把购物车的数据存储到数据库里面的。
当用户登录时，要有一个操作，检查cookie里面是否有购物车数据，如果有，则把cookie里面的购物车数据给移动到数据库里面。

2、如何把购物车的数据存储到cookie里面
思考：哪些数据存储到cookie里面，
商品的goods_id   购买的数量   商品的属性
商品的属性如何存储？
商品的属性是存储在it_goods_attr表里面的。把it_goods_attr表里面的id存储成购物车的属性信息，多个用逗号隔开。



mysql> select * from it_goods_attr;
+----+----------+---------+------------+
| id | goods_id | attr_id | attr_value |
+----+----------+---------+------------+
|  1 |        1 |       1 | 10         |
|  2 |        1 |       2 | 20         |
|  3 |        1 |       3 | 白色          |
|  4 |        1 |       3 | 黑色          |
|  5 |        1 |       3 | 金色          |
|  6 |        1 |       3 | 绿色          |
|  7 |        1 |      11 | IOS        |
|  8 |        1 |      12 | 4G         |
|  9 |        1 |      12 | 8G         |
| 10 |        1 |      12 | 16G        |
| 11 |        1 |      12 | 32G        |
比如购买白色，8G  goods_id为1 的商品10件。
如何设计cookie里面的数组，
我们使用一维数组，用于存储购物车的数据。
商品的id-‘属性的id（it_goods_attr表的 id）,多个用逗号隔开’=>购买的数量
比如购买白色，8G  goods_id为1 的商品10件。
$arr=array(
	1-3,9=>10
)
比如又买了金色的，16G，Goodsid为1的商品5件。
$arr=array(
	1-3,9=>10,
	1-5,10=>5
)
把该数组，序列化后，存储到 cookie里面。

3、如何把购物车数据，存储到数据库里面
数据库是如何设计的。
create table it_cart(
   goods_id mediumint  unsigned not null  comment '商品的id',
   goods_attr_id varchar(32) not null default '' comment '商品的属性，it_goods_attr表里面的id',
   goods_count tinyint unsigned not null comment '购买数量',
   user_id  int not null comment '登录用户的id'
)engine myisam charset utf8;

4、在前台模块里面，新建一个购物车模型(CartModel)，添加添加商品到购物车的方法（addCart()）。
<?php
namespace Home\Model;
use Think\Model;
class CartModel extends Model
{
    //添加商品到购物车的方法
    //参数：$goods_id 是商品的id
    //参数：$goods_attr_id是商品的属性，也就是it_goods_attr表里面的id,多个属性用逗号隔开
    //参数：$goods_count是购买数量
    public function addCart($goods_id,$goods_attr_id,$goods_count){
            //分析是否登录，如果已经登录则存储到数据库，如果没有登录。则存储到cookie里面
            $user_id = $_SESSION['user_id'];
            if($user_id>0){
                    //已经登录
                   //在存储到数据库之前，要判断该商品是否已经在购物车表里面，如果在，则修改数量。如果不存在则添加。
                  $info =  $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->find();
                  if($info){
                        //说明该商品已经存在，则修改购买数量
                        $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->setInc("goods_count",$goods_count);
                  }else{
                        //说明该商品不存在。则添加商品到购物车表
                        $arr=array(
                            'goods_id'=>$goods_id,
                            'goods_attr_id'=>$goods_attr_id,
                            'goods_count'=>$goods_count,
                            'user_id'=>$user_id
                        );
                        $this->add($arr);
                  }   
            }else{
                //没有登录,判断 cookie里面是否有该商品，如果有则修改购买数量，如果没有则是添加
                $cartdata = isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
                //构造数组的键
                $key = $goods_id.'-'.$goods_attr_id;
                if(isset($cartdata[$key])){
                    //说明该商品已经存在，如果该商品存在，则修改购买数量
                    $cartdata[$key]=$cartdata[$key]+$goods_count;
                }else{
                    //说明该商品不存在。
                    $cartdata[$key]=$goods_count;
                }
                setcookie('cart',serialize($cartdata),time()+7*24*3600,'/');
            }
    }
}
?>






5、修改商品的详情页面准备提交到购物车的数据。
需要提交的数据：商品的id和购买数量。
（1）打开商品详情页面对应的detail.html页面，修改表单的提交位置：
Home/View/Index/detail.html

<form action="<?php echo U('Cart/addCart')?>"></form>
（2）修改购买数量的文本域：
Home/View/Index/detail.html

<strong>购买数量:</strong>
<input type="text" name="goods_count">
（3）添加一个隐藏域：
Home/View/Index/detail.html

<input type="hidden" name="goods_id" value="<?php echo $info['id']?>">
（4）修改属性提交的值。
name="<?php echo 'attr['.$v1['attr_id'].']'?>"
Home/View/Index/detail.html

<?php foreach($v as $k=>$v1){?>
<label for="spec_value_227">
    <input name="<?php echo 'attr['.$v1['attr_id'].']'?>" value="<?php echo $v1['id']?>" id="spec_value_227" <?php if($k==0){echo 'checked="checked"';}?> onclick="changePrice()" type="radio" />
   <?php echo $v1['attr_value']?> </label>
<?php }?>  

（5）给提交按钮图片添加事件事件，完成表单提交。
Home/View/Index/detail.html

<a href="javascript:" onclick="addCartSubmit()"><img src="__PUBLIC__/Home/images/goumai2.gif"></a>
<script>
	function addCartSubmit(){
		var forms = document.getElementById('ECS_FORMBUY');
		forms.submit();
	}
</script>

6、把商品提交到购物车
在前台模块新建一个Cart的控制器，并添加addCart的方法。
Home/Controller/CartController.class.php

<?php
namespace Home\Controller;
use Think\Controller;
class CartController extend Contorller
{
	//添加商品到购物车
	public function addCart(){
		//拿到数据,准备调用购物车模型里面addCart方法完成购物车数据的提交
		//p($_POST);
		$goods_id = I("post.goods_id");
		$goods_count = I("post.goods_count");
		//接收属性
		$attr = I("post.attr");//返回的是一维数组
		
		$goods_attr_id='';
		if(!empty($attr)){
			$goods_attr_id = implode(',', $attr);
		}
		$cartmodel = D('Cart');
		//把数据加入到购物车里面
		$cartmodel->addCart($goods_id,$goods_attr_id,$goods_count);
		$this->success("加入购物车成功",U('cartList'));
	}
}
?>
7、购物车列表

（1）在 cart控制器里面添加一个购物车列表的方法cartList,并拷贝对应的静态页面，
Home/Controller/CartController.class.php

<?php
namespace Home\Controller;
use Think\Controller;
class CartController extend Contorller
{
	//添加商品到购物车
	public function addCart(){
		//拿到数据,准备调用购物车模型里面addCart方法完成购物车数据的提交
		//p($_POST);
		$goods_id = I("post.goods_id");
		$goods_count = I("post.goods_count");
		//接收属性
		$attr = I("post.attr");//返回的是一维数组
		
		$goods_attr_id='';
		if(!empty($attr)){
			$goods_attr_id = implode(',', $attr);
		}
		$cartmodel = D('Cart');
		//把数据加入到购物车里面
		$cartmodel->addCart($goods_id,$goods_attr_id,$goods_count);
		$this->success("加入购物车成功",U('cartList'));
	}
	//购物车列表页面
	public function cartList(){
		//取出头部导航信息
		$catemodel = D('Admin/Category');
		$navdata = $catemodel->getNav();
		$this->assign('navdata',$navdata);
		$this->display();
	}
}
?>
（2）在购物车的模型里面，添加一个方法，用于取出购物车列表数据
Home/Model/CartModel.class.php

//购物车列表
public function cartList(){
        $user_id = $_SESSION['user_id'];
        //判断用户是否登录，如果登录则从数据库里面获取，如果没有登录则从cookie里面获取
        if($user_id>0){
                //已经登录   从数据库获取数据，
               $cartdata =  $this->where("user_id=$user_id")->select();
        }else{
            //没有登录  从cookie里面获取数据
              $cart = isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
              //要把一维数组转换成二维数组（和数据库的格式一样的），
              //$cart=array('1-3,4'=>10,'2-4,5'=>20);
              $cartdata=array();//用于存储生成的二维数组
              foreach($cart as $k=>$v){
                  $a = explode('-',$k);
                  $cartdata[]=array(
                        'goods_id'=>$a[0],
                        'goods_attr_id'=>$a[1],
                        'goods_count'=>$v
                  );
              }
        }

        //继续构建数组，便于遍历，新数组里面有（商品的名称，缩略图，价格，属性的名称和值）
        $cartlist = array();//用于存储构建的数组
        foreach($cartdata as $v){
                    //$v['info']=获取商品的名称，缩略图，价格
                    $v['info']=M('Goods')->field("goods_name,shop_price,goods_thumb")->where("id=".$v['goods_id'])->find();
                    //$v['attr']获取商品的属性的名称以及属性的值，根据it_goods_attr表里面的id获取商品属性以及商品属性的值。
                    $v['attr']=$this->getAttr($v['goods_attr_id']);
                    $cartlist[]=$v;
        }
        return $cartlist;
}

（3）根据购物车数据里面的 [goods_attr_id] => 48,51获取属性的名称以及属性的值
此处的48,51是it_goods_attr表里面的id

mysql>select b.attr_name,a.attr_value from it_goods_attr a left join it_attribute b on a.attr_id=b.id where a.id in (48,51);

mysql>select concat(b.attr_name,':',a.attr_value) from it_goods_attr a left join it_attribute b on a.attr_id where a.attr_id=b.id where a.id in (48,51);





思考：如上所示，把两行变成一行，并且使用<br/>来连接。
使用mysql里面的group_concat函数。
group_concat(concat(b.attr_name,':',a.attr_value) separator '<br/>')
group_concat函数默认的连接符是逗号，使用separator参数指定其他的连接符。
# separator参数在group_concat函数中指定行与行数据连接的连接符

mysql>select group_concat(concat(b.attr_name,':',a.attr_value) separator '<br/>') from it_goods_attr a left join it_attribute b on a.attr_id where a.attr_id=b.id where a.id in (48,51);


在购物车模型里面添加一个方法根据goods_attr_id获取属性的名称以及属性的值。

Home/Model/CartModel.class.php

//根据购物车数据里面的 [goods_attr_id] => 48,51获取属性的名称以及属性的值
public function getAttr($goods_attr_id){
        $sql="select group_concat(concat(b.attr_name,':',a.attr_value) SEPARATOR '<br/>') attrs from it_goods_attr a left join it_attribute b on a.attr_id=b.id where a.id in ($goods_attr_id)";
        $info = $this->query($sql);
        return $info[0]['attrs'];
}

（4）购物车数据的遍历：
Home/Controller/CartController.class.php

//购物车列表页面
public function cartList(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);

	//取出购物车列表数据
	$cartmodel = D('Cart');
	$cartdata = $cartmodel->cartList();
	$this->assign('cartdata',$cartdata);

	$this->display();
}

Home/View/Cart/cartList.html

<?php foreach($cartdata as $v){?>
	<?php echo C('UPLOAD_ROOT').$v['info']['goods_thumb']?>
	<?php echo $v['info']['goods_name']?>
	<?php echo $v['attr']?>
	<?php echo $v['info']['shop_price']?>
	<?php echo $v['info']['shop_price']*$v['goods_count']?>
<?php }?>
（5）取出购物车里面总的商品数量，以及总的金额

在购物车的模型里面添加一个方法getCartTotal，用于计算商品数量和总的金额

Home/Model/CartModel.class.php

//计算购物车里面的商品数量以及总的金额
public function getCartTotal(){
    //获取购物车的数据
    $cartlist = $this->cartList();
    $total_count = 0;//定义总的数量
    $total_price = 0;//定义总的金额
    if(!empty($cartlist)){
            //当购物车的数据不为空时则，需要计算，
            foreach($cartlist as $v){
                    $total_count+=$v['goods_count'];
                    $total_price +=$v['info']['shop_price']*$v['goods_count'];
            }
    }
    return array('total_price'=>$total_price,'total_count'=>$total_count);
}

Home/Controller/CartController.class.php

//购物车列表页面
public function cartList(){
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
在对应的静态页面，进行遍历：

Home/View/Public/head.html

<?php echo $cart_total['total_count']?>
<?php echo $cart_total['total_price']?>

8、当用户登录时，判断cookie里面是否有购物车的数据，如果有则移动到数据库。
（1）在购物车的模型里面添加一个移动的方法。
Home/Model/CartModel.class.php

//把cookie里面 的数据给移动到数据库里面
public function cookie2array(){
        //取出cookie里面的数据
         $cart = isset($_COOKIE['cart'])?unserialize($_COOKIE['cart']):array();
         if(!empty($cart)){
                //说明cookie里面有数据
                foreach($cart as $k=>$v){
                        $a = explode('-',$k);
                        $goods_id = $a[0];
                        $goods_attr_id = $a[1];
                        $goods_count = $v;
                        $user_id = $_SESSION['user_id'];
                        //要判断当前商品是否存在于数据库中，如果存在，则修改购买数量，如果不存在，则添加
                        $info =  $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->find();
                        if($info){
                            //说明该商品已经存在，则修改购买数量
                            $this->where("goods_id=$goods_id and goods_attr_id='$goods_attr_id' and user_id=$user_id")->setInc("goods_count",$goods_count);
                        }else{
                            //说明该商品不存在。则添加商品到购物车
                            $arr=array(
                                'goods_id'=>$goods_id,
                                'goods_attr_id'=>$goods_attr_id,
                                'goods_count'=>$goods_count,
                                'user_id'=>$user_id
                            );
                            $this->add($arr);
                        }   
                }
                //清除cookie里面的数据
                setcookie('cart','',time()-1,'/');
         }
}

（2）登录成功后，调用该方法
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
















补充内容：可以设置前台和后台的入口文件；

入口文件里面的内容：
前台的入口文件：

后台的入口文件内容：


group_concat(concat(b.attr_name,':',a.attr_value) separator '<br/>')
group_concat函数默认的连接符是逗号，使用separator参数指定其他的连接符。
# separator参数在group_concat函数中指定行与行数据连接的连接符
group_concat函数的使用说明：


