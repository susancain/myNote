电商网站（三）
目录
一、商品属性的添加	1
	1、在商品添加的页面显示出，商品属性的选项卡：	1
	（1）打开添加商品对应的 add.html页面，添加商品属性的选项卡。	1
	（2）在该add.html页面中，添加商品属性的对应的table(复制前面的“详细描述”选项卡的table即可。)	2
	（3）修改charea()方法，添加属性选项卡。	2
	（4）修改“商品属性”显示的格式：	2
	2、把商品类型所对应的属性，给取出来，	3
	（1）根据商品类型的id,取出属性信息。	4
	（2）给商品类型中select标签，添加change事件。	5
	（3）在goods 控制器里面添加一个showattr的方法，用于显示商品类型的属性数据。	6
	（4）新建一个showattr方法对应的静态页面，并完成遍历	6
	（5）给”[+]”添加单击事件。	7
	3、完成属性的添加	9
	（1）创建一张表，用于存储商品的属性信息。	9
	（2）修改属性的表单，设置表单域里面的名称。	10
	（3）完成属性数据的提交：	11
二、前台页面	14
	1、完成前台首页显示	14
	2、取出头部的导航信息。	15
	3、把头部信息分割成一个文件，形成公共的头部，让其他的页面引入。	17
	4、在首页里面取出商品的栏目信息。	18
	5、在首页取出，精品、热卖、新品的商品数据。	21
	（1）在后台的goods模型里面，定义一个方法getByGoods，用于取出精品、热卖、新品的商品数据	21
	（2）在前台的index控制器里面的，index方法中调用。	21
	（3）把取出的数据，完成遍历	22


一、商品属性的添加
1、在商品添加的页面显示出，商品属性的选项卡：
（1）打开添加商品对应的 add.html页面，添加商品属性的选项卡。
Admin/View/Goods/add.html
<div id="tabbar-div">
  <p>
    <span class="tab-front" id="general-tab" onclick="charea('general');">通用信息</span>
    <span class="tab-back" id="detail-tab" onclick="charea('detail');">详细描述</span>
    <span class="tab-back" id="mix-tab" onclick="charea('mix');">其他信息</span>
	<span class="tab-back" id="attrs-tab" onclick="charea('attrs');">商品属性</span>		
  </p>
</div>

（2）在该add.html页面中，添加商品属性的对应的table(复制前面的“详细描述”选项卡的table即可。)
 <!-- 商品属性 -->
<table width="90%" id="attrs-table" style="display:none">
  <tr>
    <td><textarea name="goods_desc"></textarea></td>
  </tr>
</table>
（3）修改charea()方法，添加属性选项卡。
<script type="text/javascript">
function charea(a) {
    var spans = ['general','detail','mix','attrs'];
    for(i=0;i<4;i++) {
        var o = document.getElementById(spans[i]+'-tab');
        var tb = document.getElementById(spans[i]+'-table');
        o.className = o.id==a+'-tab'?'tab-front':'tab-back';
        tb.style.display = tb.id==a+'-table'?'block':'none';
    }  
}
</script>
（4）修改“商品属性”显示的格式：
第一步：在控制器中，添加商品的方法中取出商品类型的数据。
Admin/Controller/GoodsController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends Controller{
	//添加商品的方法
	public function add(){
		if(IS_POST){
			$goodsmodel = D('Goods');
			if($goodsmodel->create()){
				if($goodsmodel->add()){
					//添加商品成功
					$this->success('添加商品成功');
				}
			}
			$errors = $goodsmodel->getError();//获取错误提示,获取模型里面create()方法失败后的错误提示
			//$goodsmodel->getError()方法是获取的模型里面的$this->error属性的内容
			if(empty($errors)){
				$errors='添加商品失败';
			}
			$this->error($errors);
		}
		//取出商品的栏目信息
		$catemodel = D('Category');
		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		//取出商品的类型
		$typemodel = D('Type');
		$typedata = $typemodel->select();
		$this->assign('typedata',$typedata);

		$this->display();
	}
	
}
?>
第二步：把取出的商品类型数据给遍历到静态页面
Admin/View/Goods/add.html
<td>商品类型:</td>
<td>
	<select name="goods_type">
		<option value="0">请选择商品类型</option>
			<?php foreach($typedata as $v){?>
				<option value="<?php echo $v['id']?>"><?php echo $v['type_name']?></option>
			<?php }?>
	</select>
</td>
第三步：给goods表添加一个字段，用于存储当前商品所属类型的id,
mysql>alter table it_goods add goods_type tinyint unsigned not null default 0;
2、把商品类型所对应的属性，给取出来，
思路：使用ajax 来完成。
注意：根据属性的类型，和输入值的方式，把属性信息转换成表单。
属性信息存储的表it_attribute：

在it_attribute表里面，根据type_id的值取出属性信息。
 
（1）根据商品类型的id,取出属性信息。
在属性的模型里面定义一个方法，用于取出商品的属性。
Admin/Model/AttributeModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class Attribute extends Model{
	//添加数据验证
	protected $_validate = array(
		array('attr_name','require','属性名称不能为空'),
		array('type_id','number','商品类型不合法'),
		array('attr_type',array(0,1),'属性类型不合法',1,'in'),
		array('attr_input_type',array(0,1),'属性值录入方式不合法',1,'in')
	);
	//根据type_id取出商品的属性信息
	public function getAttr($type_id){
		return $this->where("type_id=$type_id")->select();
	}
}
?>
（2）给商品类型中select标签，添加change事件。
Admin/View/Goods/add.html
<js href="__PUBLIC__/Js/jquery.js"/>
<script>
$(function(){
	$("select[name=goods_type]").change(function(){
		var type_id = $(this).val();//获取商品类型id
		//ajax提交type_id,返回已经遍历好(也生成了表单)的html代码
		$.ajax({
			type:'get',
			url:'__CONTROLLER__/showattr/type_id/'+type_id,
			success:function(msg){
				//返回的msg就是生成了表单的html代码
				$("#showattr").html(msg);
			}
		});
	});
});
</script>
设置返回的html代码，显示的位置：
<tr>
	<td colspan="2"><div id="showattr"></div></td>
</tr>

（3）在goods 控制器里面添加一个showattr的方法，用于显示商品类型的属性数据。
Admin/Controller/GoodsController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends Controller{
	//添加商品的方法
	public function add(){
		if(IS_POST){
			$goodsmodel = D('Goods');
			if($goodsmodel->create()){
				if($goodsmodel->add()){
					//添加商品成功
					$this->success('添加商品成功');
				}
			}
			$errors = $goodsmodel->getError();//获取错误提示,获取模型里面create()方法失败后的错误提示
			//$goodsmodel->getError()方法是获取的模型里面的$this->error属性的内容
			if(empty($errors)){
				$errors='添加商品失败';
			}
			$this->error($errors);
		}
		//取出商品的栏目信息
		$catemodel = D('Category');
		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		//取出商品的类型
		$typemodel = D('Type');
		$typedata = $typemodel->select();
		$this->assign('typedata',$typedata);

		$this->display();
	}
	//用于显示属性的一个方法
	public function showattr(){
		$type_id = $_GET['type_id'];
		$attrmodel = D('Attribute');
		$attrdata = $attrmodel->select();
		$this->assign('attrdata',$attrdata);
		$this->display();
	}
}
?>

（4）新建一个showattr方法对应的静态页面，并完成遍历
注意：表单是如何生成的？
如果属性的类型是唯一的，属性值的录入方式是手工录入，则生成一个输入文本框。
如果属性的类型是唯一的，属性值的录入方式是列表选择，则生成一个select框
如果属性的类型是单选的，属性值的录入方式是手工录入，则生成一个输入文本框，文本框前面呢添加一个”[+]”
如果属性的类型是单选的，属性值的录入方法是是列表选择，则生成一个select框，并且在前面添加一个”[+]”
代码如下：
Admin/View/Goods/showattr.html
<table>
<?php
foreach($attrdata as $v){
	if(v['attr_type']==0){
		//是唯一属性
		if($v['attr_input_type']==0){
			//是手工输入
			echo "<tr><td>".$v['attr_name'].":</td><td>";
			echo "<input type='text' name=''/></td></tr>";
		}else{
			//是列表选择
			$attrvalues = str_replace("，",",", $v[attr_value]);//要把该字符串换成一个数组
			$attrs = explode(',',$attrvalues);//把一个字符串通过逗号分隔转换成一个数组
			echo "<tr><td>".$v['attr_name'].":</td><td><select name=''>";
			foreach($attrs as $v1){
				echo "<option value='".$v1."'>".$v1."</option>";				
			}
			echo "</select></td></tr>";
		}
	}else{
		//是单选属性
		if($v['attr_input_type']==0){
			//是手工输入
			echo "<tr><td>".$v['attr_name'].":</td><td>";
			echo "<input type='text' name=''/></td></tr>";
		}else{
			//是列表选择
			$attrvalues = str_replace("，",",", $v[attr_value]);//要把该字符串换成一个数组
			$attrs = explode(',',$attrvalues);//把一个字符串通过逗号分隔转换成一个数组
			echo "<tr><td><a href='javascript'>[+]</a>".$v['attr_name'].":</td><td><select name=''>";
			foreach($attrs as $v1){
				echo "<option value='".$v1."'>".$v1."</option>";
			}
			echo "</select></td></tr>";
		}
	}
}
?>
</table>


效果如下：

（5）给”[+]”添加单击事件。
第一步：
给 “[+]”添加a标签：
Admin/View/Goods/showattr.html
<table>
<?php
foreach($attrdata as $v){
	if(v['attr_type']==0){
		//是唯一属性
		if($v['attr_input_type']==0){
			//是手工输入
			echo "<tr><td>".$v['attr_name'].":</td><td>";
			echo "<input type='text' name=''/></td></tr>";
		}else{
			//是列表选择
			$attrvalues = str_replace("，",",", $v[attr_value]);//要把该字符串换成一个数组
			$attrs = explode(',',$attrvalues);//把一个字符串通过逗号分隔转换成一个数组
			echo "<tr><td>".$v['attr_name'].":</td><td><select name=''>";
			foreach($attrs as $v1){
				echo "<option value='".$v1."'>".$v1."</option>";				
			}
			echo "</select></td></tr>";
		}
	}else{
		//是单选属性
		if($v['attr_input_type']==0){
			//是手工输入
			echo "<tr><td>".$v['attr_name'].":</td><td>";
			echo "<input type='text' name=''/></td></tr>";
		}else{
			//是列表选择
			$attrvalues = str_replace("，",",", $v[attr_value]);//要把该字符串换成一个数组
			$attrs = explode(',',$attrvalues);//把一个字符串通过逗号分隔转换成一个数组
			echo "<tr><td><a href='javascript' onclick='copythis(this)'>[+]</a>".$v['attr_name'].":</td><td><select name=''>";
			foreach($attrs as $v1){
				echo "<option value='".$v1."'>".$v1."</option>";
			}
			echo "</select></td></tr>";
		}
	}
}
?>
</table>
第二步：添加代码：
Admin/View/Goods/showattr.html
<script>
function copythis(o){
	//要取出当前行
	var trs = $(o).parent().parent();
	//判断当前a标签里面的内容,如果是"[+]"则自我复制,如果是"[-]",就直接删除
	if($(o).html()=='[+]'){
		//自我克隆
		var new_trs = trs.clone();//克隆出新行
		//把新行里面的"[+]"改成"[-]"
		new_trs.find("a").html('[-]');
		//然后把新行放到当前行的后面
		trs.after(new_trs);
	}else{
		trs.remove();
	}
}
</script>


3、完成属性的添加
（1）创建一张表，用于存储商品的属性信息。
思考：创建属性表的字段有哪些？
商品的id(goods_id)
属性的名称attr_id   该attr_id是对于it_attribute表里面的id 的。
属性的值attr_value
create table it_goods_attr(
    id tinyint unsigned  primary key auto_increment,
    goods_id mediumint  unsigned not null comment '所属商品的id',
    attr_id smallint unsigned  not null comment '属性所属的id',
    attr_value  varchar(32) not null comment '输入的属性的值'
)engine myisam charset utf8;


（2）修改属性的表单，设置表单域里面的名称。

如果一个表单里面的数据，入库多张表，
Admin/View/Goods/showattr.html
<table>
<?php
foreach($attrdata as $v){
	if(v['attr_type']==0){
		//是唯一属性
		if($v['attr_input_type']==0){
			//是手工输入
			echo "<tr><td>".$v['attr_name'].":</td><td>";
			echo "<input type='text' name='attr[".$v['id']."]'/></td></tr>";
		}else{
			//是列表选择
			$attrvalues = str_replace("，",",", $v[attr_value]);//要把该字符串换成一个数组
			$attrs = explode(',',$attrvalues);//把一个字符串通过逗号分隔转换成一个数组
			echo "<tr><td>".$v['attr_name'].":</td><td><select name='attr[".$v['id']."]'>";
			foreach($attrs as $v1){
				echo "<option value='".$v1."'>".$v1."</option>";				
			}
			echo "</select></td></tr>";
		}
	}else{
		//是单选属性
		if($v['attr_input_type']==0){
			//是手工输入
			echo "<tr><td>".$v['attr_name'].":</td><td>";
			echo "<input type='text' name=''/></td></tr>";
		}else{
			//是列表选择
			$attrvalues = str_replace("，",",", $v[attr_value]);//要把该字符串换成一个数组
			$attrs = explode(',',$attrvalues);//把一个字符串通过逗号分隔转换成一个数组
			echo "<tr><td><a href='javascript' onclick='copythis(this)'>[+]</a>".$v['attr_name'].":</td><td><select name='attr[".$v['id']."]'>";
			foreach($attrs as $v1){
				echo "<option value='".$v1."'>".$v1."</option>";
			}
			echo "</select></td></tr>";
		}
	}
}
?>
</table>


表单提交的数据，

（3）完成属性数据的提交：
思考：入库的数据：入库的代码写在哪里？
使用_after_insert钩子函数完成属性数据的入库。



打印_after_insert钩子函数里面的参数。
protected function _after_insert($data,$options){
	p($data);
	p($options);
	exit;
}


代码如下：
Admin/Model/GoodsModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model
{
	//定义一个钩子函数 (加&符号是为了和GoodsController的add的$data的内存地址一样,也为了下面的数据自动入库)
	protected function _before_insert1(&$data, $options){
		//完成图片的上传
		//使用C函数,读取配置文件里面的内容
		$exts = C('UPLOAD_ALLOW_EXT');
		$rootpath = C('UPLOAD_ROOT_PATH');//获取上传文件的根路径
		$filesize = (int)C('UPLOAD_FILE_SIZE');//获取上传文件的大小
		
		$max_filesize = (int)ini_get('upload_max_filesize');
		$maxfile = min($filesize,max_filesize);
		
		$upload = new \Think\Upload();//实例化上传类		
		$upload -> maxSize = $maxfile*1024*1024;//设置附件上传大小
		$upload -> exts = $exts;//设置附件上传类型
		$upload -> rootPath = $rootpath;//上传文件的根路径
		$upload -> savePath = 'Goods/';//相当于根路径的文件保存路径
		$info = $upload->upload();
		
		if($info){
			//开始生成缩略图,要根据原图
			//拼接原图的路径
			$goods_ori = $info['goods_img']['savepath'].$info['goods_img']['savename'];
			$image = new \Think\Image(); 
			$image->open($rootpath.$goods_ori);// 按照原图比例生成一个左上角裁剪为150*150的缩略图并保存为thumb.jpg
			$goods_thumb1=$info['goods_img']['savepath'].'thumb1'.$info['goods_img']['savename'];
			$goods_thumb2=$info['goods_img']['savepath'].'thumb2'.$info['goods_img']['savename'];
			//注意:生成多张缩略图时,先生成大图,在生成小图			
			$image->thumb(230,230)->save($rootpath.$goods_thumb1);
			$image->thumb(100,100)->save($rootpath.$goods_thumb2);
			//需要入库的数据,直接存储到&$data数组里面,即可<自动入库>
			$data['goods_ori']=$goods_ori;//存储原图的路径
			$data['goods_img']=$goods_thumb1;//存储中图的路径
			$data['goods_thumb']=$goods_thumb2;//存储小图的路径
			
			//判断提交的货号是否为空
			$goods_sn = I('post.goods_sn');
			if(empty($goods_sn)){
				$data['goods_sn']=substr(uniqid(),-6).time();
			}
			//添加时间
			$data['add_time']=time();
		}else{
			//上传失败,返回false
			//要把错误提示返回
			$this->error = $upload->getError();//返回上传失败的信息
			return false;
		}				
	}
	
	//完成属性数据入库的钩子函数
	protected function _after_insert($data,$options){
		$goods_id = $data['id'];//获取商品的id
		//接收属性的信息
		$attrs = I('post.attr');//获取的是属性的信息,返回的是二维数组
		//把属性信息入库it_goods_attr表
		foreach($attrs as $k=>$v){
			//判断$v是否是一个数组
			if(is_array($v)){
				//$v是一个数组
				foreach($v as $v1){
					$arr = array(
						'goods_id'=>$goods_id,
						'attr_id'=>$k,
						'attr_value'=>$v1
					);
					M('GoodsAttr')->add($arr);
				}
			}else{
				//$v不是一个数组
				$arr=array(
					'goods_id'=>$goods_id,
					'attr_id'=>$k,
					'attr_value'=>$v
				);
				//入库	M('GoodsAttr')生成基础模型,操作it_goods_attr表
				M('GoodsAttr')->add($arr);//it_goods_attr去掉前缀,首字母大写,后面去掉_首字母大写
			}
		}					
	}		
}
?>



二、前台页面
1、完成前台首页显示
（1）新建一个前台的模块，新建一个前台首页的控制器，并添加index方法。
Home/Controller/IndexController.class.php
<?php
namespace Home/Controller;
use Think/Controller;
class IndexController extend Controller
{
	public function index(){
		$this->display();
	}
}
?>
（2）拷贝index方法，对应的静态页面，并完成样式和图片路径的修改。
第一步：把前台所用的样式文件和图片文件拷贝到Public目录下面的Home目录里面。

第二步：拷贝对应静态页面，并修改样式和图片的路径。

2、取出头部的导航信息。
需要注意：我们就取出parent_id=0的栏目，也就是顶级栏目。

（1）在栏目的模型里面定义的一个函数，用于取出顶级栏目信息。
思路：我们使用后台模块的定义的category模型。
Admin/Model/CategoryModel.class.php
public function getNav(){
	return $this->where("parent_id=0")->select();
}
（2）在前台的index控制器里面，index方法里面调用该方法。
Home/Controller/IndexController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extend Controller
{
	public function index(){
		//跨模块创建模型的对象,D("模块名称/模型名称")
		$catemodel = D("Admin/Category");
		$navdata = $catemodel->getNav();
		$this->assign('navdata',$navdata);
		$this->display();
	}
}
?>
（3）完成导航栏目的遍历。
Home/View/Index/index.html
<?php foreach($navdata as $v){?>
	<a href="__CONTROLLER__/category/cat_id/<?php echo $v['id']?>"><?php echo $v['cat_name']?></a>
<?php }?>
3、把头部信息分割成一个文件，形成公共的头部，让其他的页面引入。
第一步：
在前台的view下面新建一个Public目录，用于存储被分割的公共文件。

第二步：在public目录下面新建一个head.html文件，用于存储公共的头部。
把头部信息内容剪切到head.html文件里面。
第三步：其他的静态页面，引入head.html文件。
Home/View/Index/index.html
<include file="Public/head"/>

4、在首页里面取出商品的栏目信息。
Home/Controller/IndexController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extend Controller
{
	public function index(){
		//跨模块创建模型的对象,D("模块名称/模型名称")
		$catemodel = D("Admin/Category");
		$navdata = $catemodel->getNav();
		$this->assign('navdata',$navdata);

		//取出栏目的信息
		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);
		$this->display();
	}
}
?>

第一步：先取出栏目的信息：

第二步：在对应的静态页面完成遍历。
（1）先取出顶级栏目数据：
Home/View/Index/index.html
<?php foreach($catedata as $v){
	if($v['parent_id']==0){		
?>
<?php echo $v['cat_name'];?>
<?php }}?>
效果：

（2）要取出各自顶级栏目的子栏目。
Home/View/Index/index.html
<?php foreach($catedata as $v){
	if($v['parent_id']==0){		
?>
	<a href="__CONTROLLER__/category/cat_id/<?php echo $v['id']?>"><?php echo $v['cat_name'];?></a>
		<?php foreach($catedata as $v1){
			if($v['id']==$v1['parent_id']){
		?>
			<a href="__CONTROLLER__/category/cat_id/<?php echo $v['id']?>"><?php echo $v1['cat_name']?></a>
		<?php }}?>
<?php }}?>
效果：

5、在首页取出，精品、热卖、新品的商品数据。
（1）在后台的goods模型里面，定义一个方法getByGoods，用于取出精品、热卖、新品的商品数据
Admin/Model/GoodsModel.class.php
	//取出热卖,新品,精品的数据
	//参数:$type是类型的名称,值为:is_new或is_best或is_hot
	//参数:$number是取出数据的数量
	public function getByGoods($type,$number){
		if($type=='is_best'||$type=='is_new'||$type=='is_hot'){
			//取出数据
			return $this->where("$type=1")->limit($number)->select();
		}
	}
（2）在前台的index控制器里面的，index方法中调用。
Home/Controller/IndexController.class.php
<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extend Controller
{
	public function index(){
		//跨模块创建模型的对象,D("模块名称/模型名称")
		$catemodel = D("Admin/Category");
		$navdata = $catemodel->getNav();
		$this->assign('navdata',$navdata);

		//取出栏目的信息
		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		//取出精品数据
		$goodsmodel = D('Admin/Goods');
		$this->bestdata = $bestdata = $goodsmodel->getByGoods('is_best',3);
		$this->newdata = $newdata = $goodsmodel->getByGoods('is_new',3);
    	$this->hotdata = $goodsmodel->getByGoods('is_hot',3);
		$this->display();
	}
}
?>
（3）把取出的数据，完成遍历
Common/Conf/config.php
'UPLOAD_ROOT'			=>	'/Public/Upload/',//该路径是给浏览器用的

Home/View/Index/index.html
<?php foreach($newdata as $v){?>
	<a href="__CONTROLLER__/detail/goods_id/<?php echo $v['id']?>"><img src="<?php echo C('UPLOAD_ROOT').$v['goods_thumb']?>" alt=""></a><br/>
	<a href="__CONTROLLER__/detail/goods_id/<?php echo $v['id']?>"><?php echo $v['goods_name']?></a>
	<?php echo $v['market_price']?>
	<?php echo $v['shop_price']?>
<?php }?>
