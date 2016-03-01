电商网站（终结篇）
目录
一、货品管理	1
	1、先建立货品表，用于存储不同属性商品的库存。	2
	2、添加库存	3
	（1）建立商品的列表	3
	（2）在商品列表建立一个“库存管理|货品列表”的链接	4
	（3）在后台商品的控制器里面添加一个product的方法，并拷贝对应的静态页面。	4
	（4）制作添加商品属性库存的表单。	5
	3、添加库存完成	9
	（1）修改提交表单的名称：	9
	（2）完成库存数据入库：	10
	4、修改库存	11
二、在下单时要判断库存是否充足	13
三、高并发下订单的问题：	14
四、订单列表	17
	1、在后台添加一个订单的控制器，并添加一个订单列表的方法。	17
	2、拷到订单列表对应的静态页面，完成订单信息的遍历	18
	3、给“订单编号”位置添加鼠标滑过事件。	18
	（1）给订单编号位置添加一个类属性：	18
	（2）添加鼠标滑过事件	19
	（3）在页面的下方，新建一个div	19
	（4）通过ajax提交数据，返回订单中商品的数据	19
	4、在order控制器里面添加 showorder的方法。	20



一、货品管理
同一件商品不同的属性组成起来就是一个新的货品
诺基亚N98   白色 4G
诺基亚N98   黑色 4G
1、先建立货品表，用于存储不同属性商品的库存。
诺基亚N98   颜色：黑色  白色   金色
诺基亚N98   内存：4G    8G     16G

用于存储商品属性的表是:it_goods_attr表。

it_goods_attr表就存储了当前商品的属性信息：


比如要设置goods_id为1的商品的库存应该如下设置：
goods_id   goods_attr_id       goods_number    
1         3,8    （白色,4G ）  100个
1         4,9     （黑色 8G）  200个
#创建一个货品表
create table it_product(
    goods_id  int not null  comment '商品的id',
    goods_attr_id varchar(32) not null default '' comment '商品的属性信息,就是it_goods_attr表里面的id',
    goods_number int not null default 0 comment '库存量'
)engine myisam charset utf8;

2、添加库存
（1）建立商品的列表
在后台商品控制器里面添加一个 lst方法，并拷贝对应的静态页面。
Admin/Controller/GoodsController.class.php

//商品列表页面
public function lst(){
	//要取出的商品
	$goodsmodel = D('Goods');
	$goodsdata = $goodsmodel->order("id desc")->select();
	$this->assign('goodsdata',$goodsdata);
	$this->display();
}
在静态页面进行遍历
Admin/View/Goods/lst.html

<?php foreach($goodsdata as $v){?>
	<?php echo $v['goods_name']?>
	<?php echo $v['goods_sn']?>
	<?php echo $v['shop_price']?>
	<?php echo $v['goods_number']?>
<?php }?>
（2）在商品列表建立一个“库存管理|货品列表”的链接
Admin/View/Goods/lst.html

<a href="__CONTROLLER__/product/goods_id/<?php echo $v['id']?>"></a>
（3）在后台商品的控制器里面添加一个product的方法，并拷贝对应的静态页面。
Admin/Controller/GoodsController.class.php

//库存管理的方法
public function product(){
	$this->display();
}
（4）制作添加商品属性库存的表单。
补充:



第一步：先取出需要设置库存的属性（it_goods_attr）。
同时要注意：比如一件 商品  颜色只有白色的，内存只有4G ，就不需要单独设置库存。
要筛选出，属性的值两种以上的才需要单独设置库存。

mysql>select count(*) from it_goods_attr where goods_id=1 group by attr_id;

mysql>select attr_id from it_goods_attr where goods_id=1 group by attr_id having count(*)>2;

mysql>select attr_id from it_goods_attr where goods_id=1 group by attr_id having count(*)>1;

mysql>select a.*,b.attr_name from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=1 and attr_id in (select attr_id from it_goods_attr where goods_id=1 group by attr_id having count(*)>1);

第二步：要完成属性数据的遍历，组成表单。
要显示出第一行：
把需要设置库存的顺序给显示出来。
Array
(
	[0] => Array
		(
			[id] => 47
			[goods_id] => 6
			[attr_id] =>13
			[attr_value] => 10000ma
			[attr_name] => 容量 
		)
	[1] => Array
		(
			[id] => 48
			[goods_id] => 6
			[attr_id] =>13
			[attr_value] => 15000ma
			[attr_name] => 容量 
		)
	[2] => Array
		(
			[id] => 49
			[goods_id] => 6
			[attr_id] =>13
			[attr_value] => 20000ma
			[attr_name] => 容量 
		)
	[3] => Array
		(
			[id] => 50
			[goods_id] => 6
			[attr_id] =>14
			[attr_value] => 锂电
			[attr_name] => 材质
		)
)

转变成三维数组：
Array
(
	[13] => Array
		(
			[0] => Array
				(
					[id] => 47
					[goods_id] => 6
					[attr_id] => 13
					[attr_value] => 10000ma
					[attr_name] => 容量
				)
			[1] => Array
				(
					[id] => 48
					[goods_id] => 6
					[attr_id] => 13
					[attr_value] => 15000ma
					[attr_name] => 容量
				)
			[2] => Array
				(
					[id] => 49
					[goods_id] => 6
					[attr_id] => 13
					[attr_value] => 20000ma
					[attr_name] => 容量
				)			
		)
	[14] => Array
		(
			[0] => Array
				(
					[id] => 50
					[goods_id] => 6
					[attr_id] =>14
					[attr_value] => 锂电
					[attr_name] => 材质
				)
		)	
)
代码：
Admin/Controller/GoodsController.class.php

//库存管理的方法
public function product(){
	$goods_id = I('get.goods_id');//
	$sql = "select a.*,b.attr_name from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=$goods_id and attr_id in(select attr_id from it_goods_attr where goods_id=$goods_id group by attr_id having count(*)>1)";
	$list = M()->query($sql);
	$prodata = array();
	foreach($list as $v){
		$prodata[$v['attr_id']][]=$v;
	}	
	$this->assign('prodata',$prodata);
	$this->display();
}

Home/View/Goods/product.html

<?php foreach($prodata as $v){?>
	<th><?php echo $v[0]['attr_name']?></th>
<?php?>

第三步：取出属性数据的列表
Home/View/Goods/product.html

<tr>
<?php foreach($prodata as $v){?>
	<td>
		<select name="" id="">
			<option value="">请选择</option>
			<?php foreach($v as $v1){?>
				<option value="<?php echo $v1['id']?>"><?php echo $v1['attr_value']?></option>
			<?php }?>
		</select>
	</td>
<?php }?>
<td><input type="text" name="goods_number" value=""></td>
<td><input type="button" value="+"></td>
</tr>



第四步：给“+”添加单击事件，完成自我复制
Home/View/Goods/product.html

<script>
	$(":button").click(function(){
		//取出当前行
		var trs = $(this).parent().parent();
		if($(this).val()=='+'){
			//完成自我复制
			var new_trs = trs.clone(true);//精确克隆,包括添加的事件
			//把新行的"+"变成"-"
			new_trs.find(":button").val('-');
			//把新行放到当前行的前面
			trs.before(new_trs);
		}else{
			//删除当前行
			trs.remove();
		}
	});
</script>
第五步：制作表单完成：
3、添加库存完成
（1）修改提交表单的名称：
Admin/View/Goods/product.html

<tr>
<?php foreach($prodata as $v){?>
	<td>
		<select name="attr[<?php echo $v[0]['attr_id']?>][]" id="">
			<option value="">请选择</option>
			<?php foreach($v as $v1){?>
				<option value="<?php echo $v1['id']?>"><?php echo $v1['attr_value']?></option>
			<?php }?>
		</select>
	</td>
<?php }?>
<td><input type="text" name="goods_number[]" value=""></td>
<td><input type="button" value="+"></td>
</tr>
提交的数据如下：
Array
(
	[attr] => Array
		(
			[13] => Array
				(
					[0] => 47
					[1] => 49
				)
			[14] => Array
				(
					[0] => 50
					[1] => 51
				)
		)
	[goods_number] => Array
		(
			[0] => 12
			[1] => 23
		)
)
（2）完成库存数据入库：

入库的格式：
goods_id     goods_attr_id    goods_number
1              47,50             12
1              49,51             23

Admin/Controller/GoodsController.class.php

//库存管理的方法
<?php
public function product(){
	if(IS_POST){
		$goods_id=I('post.goods_id');					
		$attr = I('post.attr');
		$goods_number = I('post.goods_number');
		$num = 0;//把当前货品库存清空一下
		foreach ($goods_number as $k=>$v) {
			$a = array();
			foreach ($attr as $v1) {
				$a[] = $v1[$k];
			}
			$arr=array(
				'goods_id'=>$goods_id,
				'goods_attr_id'=>implode(',',$a),
				'goods_number'=>$v,
			);
			M('Product')->add($arr);//入库的语句
			$num += $v;//总库存
		}
		//全部添加入库完成后,还要修改总的库存,修改goods表
		M("Goods")->where("id=$goods_id")->setField("goods_number",$num);
		$this->success("添加属性库存完成",U('Goods/lst'));exit;
	}

	$goods_id = I('get.goods_id');//
	$sql = "select a.*,b.attr_name from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=$goods_id and attr_id in(select attr_id from it_goods_attr where goods_id=$goods_id group by attr_id having count(*)>1)";
	$list = M()->query($sql);
	$prodata = array();
	foreach($list as $v){
		$prodata[$v['attr_id']][]=$v;
	}	
	$this->assign('prodata',$prodata);
	$this->display();
}
?>


 
4、修改库存
（1）把已经设置的库存给显示出来
Admin/Controller/GoodsController.class.php

//库存管理的方法
<?php
public function product(){
	if(IS_POST){
		$goods_id=I('post.goods_id');					
		$attr = I('post.attr');
		$goods_number = I('post.goods_number');
		$num = 0;//把当前货品库存清空一下
		foreach ($goods_number as $k=>$v) {
			$a = array();
			foreach ($attr as $v1) {
				$a[] = $v1[$k];
			}
			$arr=array(
				'goods_id'=>$goods_id,
				'goods_attr_id'=>implode(',',$a),
				'goods_number'=>$v,
			);
			M('Product')->add($arr);//入库的语句
			$num += $v;//总库存
		}
		//全部添加入库完成后,还要修改总的库存,修改goods表
		M("Goods")->where("id=$goods_id")->setField("goods_number",$num);
		$this->success("添加属性库存完成",U('Goods/lst'));exit;
	}

	$goods_id = I('get.goods_id');//接收传递过来的id

	//取出已经设置的库存信息,从it_product表
	$attrkcdata = M('Product')->where("goods_id=$goods_id")->select();
	$this->assign('attrkcdata',$attrkcdata);

	$sql = "select a.*,b.attr_name from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=$goods_id and attr_id in(select attr_id from it_goods_attr where goods_id=$goods_id group by attr_id having count(*)>1)";
	$list = M()->query($sql);
	$prodata = array();
	foreach($list as $v){
		$prodata[$v['attr_id']][]=$v;
	}	
	$this->assign('prodata',$prodata);
	$this->display();
}
?>

（2）把取出的库存信息给遍历到设置库存的页面。product.html


attrkcdata数组

Array
(
	[0] => Array
		(
			[goods_id] => 6
			[goods_attr_id] => 47,50
			[goods_number] => 10
		)
	[1] => Array
		(
			[goods_id] => 6
			[goods_attr_id] => 48,51
			[goods_number] => 20
		)
	[2] => Array
		(
			[goods_id] => 6
			[goods_attr_id] => 49,52
			[goods_number] => 30
		)
)

prodata数组

Array
(
	[13] => Array
		(
			[0] => Array
				(
					[id] => 47
					[goods_id] => 6
					[attr_id] => 13
					[attr_value] => 10000ma
					[attr_name] => 容量
				)
			[1] => Array
				(
					[id] => 48
					[goods_id] => 6
					[attr_id] => 13
					[attr_value] => 15000ma
					[attr_name] => 容量
				)
			[2] => Array
				(
					[id] => 49
					[goods_id] => 6
					[attr_id] => 13
					[attr_value] => 20000ma
					[attr_name] => 容量
				)			
		)
	[14] => Array
		(
			[0] => Array
				(
					[id] => 50
					[goods_id] => 6
					[attr_id] =>14
					[attr_value] => 锂电
					[attr_name] => 材质
				)
			[1] => Array
				(
					[id] => 51
					[goods_id] => 6
					[attr_id] =>14
					[attr_value] => 镍镉
					[attr_name] => 材质
				)
			[2] => Array
				(
					[id] => 52
					[goods_id] => 6
					[attr_id] =>14
					[attr_value] => 镍氢
					[attr_name] => 材质
				)
		)	
)

被选中的条件是：
prodate数组里面的id 如果在attrkcdata里面的goods_attr_id里面则就被选中。

Admin/View/Goods/product.html

<?php foreach($attrkcdata as $v0){?>
	<tr>
	<?php foreach($prodata as $v){?>
		<td>
			<select name="attr[<?php echo $v[0]['attr_id']?>][]" id="">
				<option value="">请选择</option>
				<?php foreach($v as $v1){

					if(strpos(','.$v0['goods_attr_id'].',' , ','.$v1['id'].',')!==false){
						$sel = "selected=selected";
					}else{
						$sel = "";
					}

				?>
					<option value="<?php echo $v1['id']?>"><?php echo $v1['attr_value']?></option>
				<?php }?>
			</select>
		</td>
	<?php }?>
	<td><input type="text" name="goods_number[]" value=""></td>
	<td><input type="button" value="-"></td>
	</tr>
<?php }?>

（3）完成修改数据的提交
思考：把原来的库存给删除掉，重新提交。

Admin/Controller/GoodsController.class.php

//库存管理的方法
<?php
public function product(){
	if(IS_POST){
		$goods_id=I('post.goods_id');

		//要删除原来的库存信息
		M("Product")->where("goods_id=$goods_id")->delete();

		$attr = I('post.attr');
		$goods_number = I('post.goods_number');
		$num = 0;//把当前货品库存清空一下
		foreach ($goods_number as $k=>$v) {
			$a = array();
			foreach ($attr as $v1) {
				$a[] = $v1[$k];
			}
			$arr=array(
				'goods_id'=>$goods_id,
				'goods_attr_id'=>implode(',',$a),
				'goods_number'=>$v,
			);
			M('Product')->add($arr);//入库的语句
			$num += $v;//总库存
		}
		//全部添加入库完成后,还要修改总的库存,修改goods表
		M("Goods")->where("id=$goods_id")->setField("goods_number",$num);
		$this->success("添加属性库存完成",U('Goods/lst'));exit;
	}

	$goods_id = I('get.goods_id');//接收传递过来的id

	//取出已经设置的库存信息,从it_product表
	$attrkcdata = M('Product')->where("goods_id=$goods_id")->select();
	$this->assign('attrkcdata',$attrkcdata);

	$sql = "select a.*,b.attr_name from it_goods_attr a left join it_attribute b on a.attr_id=b.id where goods_id=$goods_id and attr_id in(select attr_id from it_goods_attr where goods_id=$goods_id group by attr_id having count(*)>1)";
	$list = M()->query($sql);
	$prodata = array();
	foreach($list as $v){
		$prodata[$v['attr_id']][]=$v;
	}	
	$this->assign('prodata',$prodata);
	$this->display();
}
?>


二、在下单时要判断库存是否充足

修改order控制器里面下订单的方法。
Home/Controller/OrderController.class.php

<?php
//完成下订单操作
public function done(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	
	//取出购物车总的数量和金额
	$cartmodel = D('Cart');
	$cart_total = $cartmodel->getCartTotal();

	/**要判断库存是否充足**/		
	$cartdata = $cartmodel->cartList();//取出购物车列表数据
	foreach ($cartdata as $v) {
		//要根据每个goods_id和goods_attr_id查处库存的数量
		$goods_id = $v['goods_id'];
		$goods_attr_id = $v['goods_attr_id'];
		//求出库存
		$kc = M("Product")->field("goods_number")->where("goods_id=$goods_id and goods_attr_id=$goods_attr_id")->find();
		if($v['goods_count']>$kc['goods_number']){
			$this->error('对不起，库存不足，无法下单');
		}
	}

	//接收提交的数据
	$consignee = I("post.consignee");
	$address = I("post.address");
	$mobile = I("post.mobile");
	$payment = I("post.payment");
	$shipping = I("post.shipping");
	$order_sn = 'sn'.uniqid();//生成订单编号
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
	//入库it_order_info表
	$order_id = M("OrderInfo")->add($arr);//返回自增的id

	//入库it_order_goods表
	//取出购物车列表数据//$cartdata = $cartmodel->cartList();
	foreach($cartdata as $v){
		$a=array(
			'order_id'=>$order_id,
			'goods_id'=>$v['goods_id'],
			'goods_name'=>$v['info']['goods_name'],
			'goods_attr_id'=>$v['goods_attr_id'],
			'shop_price'=>$v['info']['shop_price'],
			'goods_count'=>$v['goods_count'],
		);
		M("OrderGoods")->add($a)
	}

	//扣除库存,如果扣除库存失败,则回滚事务
	foreach ($cartdata as $v) {
		//要根据goods_id和goods_attr_id查处库存的数量
		$goods_id = $v['goods_id'];
		$goods_attr_id = $v['goods_attr_id'];
		//求出库存
		$res = M("Product")->where("goods_id=$goods_id and goods_attr_id=$goods_attr_id")->setDec("goods_number",$v['goods_count']);
	}

	//清空购物车数据
	$cartmodel->clearCart();

	$this->assign('order_sn',$order_sn);
	$this->display();
}
?>

三、高并发下订单的问题：
补充:






不使用表锁，使用文件锁。
使用文件锁，不是操作文件，是指锁定文件后，执行下单的代码，没有获取文件锁的进程则不能执行下单的代码。执行完下单代码后，则释放文件锁
（1）建立一个文件，文件名称无所谓。

（2）加锁，在判断库存是否充足的时候，添加锁
Home/Controller/OrderController.class.php

<?php
//完成下订单操作
public function done(){
	//取出头部导航信息
	$catemodel = D('Admin/Category');
	$navdata = $catemodel->getNav();
	$this->assign('navdata',$navdata);
	
	//取出购物车总的数量和金额
	$cartmodel = D('Cart');
	$cart_total = $cartmodel->getCartTotal();		

	/*添加文件锁*/
	$fp = fopen('./Public/order.lock','w');//以写的方式打开文件
	flock($fp,LOCK_EX);//exclude 进行排它锁锁定
	/**要判断库存是否充足**/		
	$cartdata = $cartmodel->cartList();//取出购物车列表数据
	foreach ($cartdata as $v) {
		//要根据每个goods_id和goods_attr_id查处库存的数量
		$goods_id = $v['goods_id'];
		$goods_attr_id = $v['goods_attr_id'];
		//求出库存
		$kc = M("Product")->field("goods_number")->where("goods_id=$goods_id and goods_attr_id=$goods_attr_id")->find();
		if($v['goods_count']>$kc['goods_number']){
			$this->error('对不起，库存不足，无法下单');
		}
	}

	//接收提交的数据
	$consignee = I("post.consignee");
	$address = I("post.address");
	$mobile = I("post.mobile");
	$payment = I("post.payment");
	$shipping = I("post.shipping");
	$order_sn = 'sn'.uniqid();//生成订单编号
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
	//入库it_order_info表
	mysql_query("start transaction");//开启事务
	$order_id = M("OrderInfo")->add($arr);//返回自增的id
	if(!order_id){
		mysql_query("rollback");
		/*释放文件锁*/
		flock($fp,LOCK_UN);
		fclose($fp);
	}

	//入库it_order_goods表
	//取出购物车列表数据//$cartdata = $cartmodel->cartList();
	foreach($cartdata as $v){
		$a=array(
			'order_id'=>$order_id,
			'goods_id'=>$v['goods_id'],
			'goods_name'=>$v['info']['goods_name'],
			'goods_attr_id'=>$v['goods_attr_id'],
			'shop_price'=>$v['info']['shop_price'],
			'goods_count'=>$v['goods_count'],
		);
		if(M("OrderGoods")->add($a)===false){
			mysql_query("rollback");
			/*释放文件锁*/
			flock($fp,LOCK_UN);
			fclose($fp);
		}
	}

	//扣除库存,如果扣除库存失败,则回滚事务
	foreach ($cartdata as $v) {
		//要根据goods_id和goods_attr_id查处库存的数量
		$goods_id = $v['goods_id'];
		$goods_attr_id = $v['goods_attr_id'];
		//求出库存
		$res = M("Product")->where("goods_id=$goods_id and goods_attr_id=$goods_attr_id")->setDec("goods_number",$v['goods_count']);
		if($res===false){
			mysql_query("rollback");
			/*释放文件锁*/
			flock($fp,LOCK_UN);
			fclose($fp);
		}
	}

	//清空购物车数据
	$cartmodel->clearCart();

	mysql_query("commit");
	/*释放文件锁*/
	flock($fp,LOCK_UN);
	fclose($fp);

	$this->assign('order_sn',$order_sn);
	$this->display();
}
?>





四、订单列表
1、在后台添加一个订单的控制器，并添加一个订单列表的方法。
Admin/Controller/OrderController.class.php

<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extend Contorller
{
	//订单列表的一个方法
	public function orderList(){
		//取出订单信息
		$orderdata = $ordermodel = D('Order');
		$this->assign('orderdata',$orderdata);
		$this->display();
	}
}
?>
2、拷到订单列表对应的静态页面，完成订单信息的遍历
Admin/View/Order/orderList.html

<?php foreach($orderdata as $v){?>
	<?php echo $v['order_sn']?>
	<?php echo data('Y-m-d',$v['addtime'])?>
	<?php echo $v['consignee']?>
	<?php echo $v['order_amount']?>
	<?php echo $v['order_number']?>
<?php }?>

3、给“订单编号”位置添加鼠标滑过事件。
（1）给订单编号位置添加一个类属性：
Admin/View/Order/orderList.html

<td><input type="checkbox" name="checkbox[]" value="32"/><span class="order"><?php echo $v['order_sn']?></span></td>
（2）添加鼠标滑过事件
Admin/View/Order/orderList.html

<js href="__PUBLIC__/Js/jquery.js"/>
<script>
	$(function(){
		$(".order").mouseover(function(){

		});
	});
</script>
（3）在页面的下方，新建一个div
Admin/View/Order/orderList.html

<div id="showattr" style="width:600px;border:1px solid red;position:absolute;display:none"></div>

（4）通过ajax提交数据，返回订单中商品的数据
添加一个隐藏域便于获取订单的id
Admin/View/Order/orderList.html

<input type="hidden" name="order_id" value="<?php echo $v['id']?>"/>


4、在order控制器里面添加 showorder的方法。
Admin/View/Order/orderList.html

<js href="__PUBLIC__/Js/jquery.js"/>
<script>
	$(function(){
		$(".order").mouseover(function(){
			//找出当前行的位置
			var p = $(this).parent().parent().offset();
			//设置div的位置
			$("#showattr").show();
			$("#showattr").css('left',p.left+100);
			$("#showattr").css('top',p.top+20);
			var order_id = $(this).parent().find("input[name=order_id]").val();
			$.ajax({
				type:'get',
				url:'__CONTROLLER__/showattr/order_id'+order_id,
				success:function(msq){
					//返回的msg是已经遍历好的html代码
					$("#showattr").html(msg);
				}
			});
		});
	});
</script>

showorder方法对应的静态页面；
Admin/View/Order/showattr.html

<?php foreach($list as $v){?>
<tr>
	<td>
	<?php echo $v['goods_name']?>
	<img src="<?php echo C('UPLOAD_ROOT').$v['info']['goods_thumb']?>" alt="">
	</td>
	<td><?php echo $v['info']['goods_sn']?></td>
	<td><?php echo $v['attr']?></td>
	<td><?php echo $v['shop_price']?></td>
	<td><?php echo $v['goods_count']?></td>
	<td><?php echo $v['shop_price']*$v['goods_count']?></td>	
</tr>
<?php }?>


