电商网站（二）
目录
一、商品属性列表	2
	1、在属性的控制器里面，接收传递的商品类型的 id,根据id来显示商品的属性。	2
	2、添加完成属性后，则跳转到属性列表时，则显示所属当前商品类型的属性。	2
	3、在属性列表页面，根据商品的id来显示商品的类型	3
	4、完成根据商品类型筛选数据	4
	（1）修改表单：	4
	（2）给select标签添加事件。	5
	（3）如果选择‘所有商品类型’则显示所有商品类型的属性。	5
	5、完成分页显示商品属性的列表	6
	（1）分页的代码：	6
	（2）给分页的字符串添加样式。	6
二、栏目管理	6
	1、创建栏目表	7
	2、添加栏目	7
	3、栏目列表：	10
	4、删除栏目数据，	10
	（1）在栏目的列表页面添加删除的链接。	11
	（2）在栏目的控制器里面添加一个del的方法，	11
	5、修改栏目。	11
	（1）在栏目的列表页面添加修改的链接。	11
	（2）在栏目的控制器里面添加一个update的方法，并根据传递的id,取出数据。	12
	（3）把取出的数据，给遍历到静态页面中。并添加一个隐藏域	12
	（4）接收表单提交的数据，完成修改	12
三、商品管理	15
	1、建立商品表	15
	2、添加商品的基本信息：	16
	（1）新建一个商品的控制器，并添加add的方法，并拷贝对应的静态页面。	16
	（2）修改表单	17
	（3）在控制器add方法中完成商品 数据的添加。	17
	（4）完成文件的上传，	17


一、商品属性列表
1、在属性的控制器里面，接收传递的商品类型的 id,根据id来显示商品的属性。
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//通过数据验证
				if($attrmodel->add()){
					//添加属性成功
					$this->success('添加属性成功');
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}

	//商品属性列表
	public function lst(){
		//接收传递的商品类型的id
		$type_id = $_GET['id']+0;
		//取出某商品类型的商品属性的数据
		$attrmodel = D('Attribute');
		$attrdata = $attrmodel->field("a.*,b.type_name")->join(a left join it_type b on a.type_id=b.id)->where("type_id=$type_id")->select();
		$this->assign('attrdata',$attrdata);
		//取出商品的类型数据
		$typemodel = D('Type');
		$this->typedata = $typemodel->select();

		$this->display();
	}
}
?>

2、添加完成属性后，则跳转到属性列表时，则显示所属当前商品类型的属性。
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//接收添加数据的商品类型id
				$type_id = I('post.type_id');
				//通过数据验证				
				if($attrmodel->add()){
					//添加属性成功,并传递type_id到lst方法
					$this->success('添加属性成功',U('lst',array('id'=>$type_id)));
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}

	//商品属性列表
	public function lst(){
		//接收传递的商品类型的id
		$type_id = $_GET['id']+0;
		//取出某商品类型的商品属性的数据
		$attrmodel = D('Attribute');
		$attrdata = $attrmodel->field("a.*,b.type_name")->join(a left join it_type b on a.type_id=b.id)->where("type_id=$type_id")->select();
		$this->assign('attrdata',$attrdata);
		//取出商品的类型数据
		$typemodel = D('Type');
		$this->typedata = $typemodel->select();

		$this->display();
	}
}
?>
3、在属性列表页面，根据商品的id来显示商品的类型
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//接收添加数据的商品类型id
				$type_id = I('post.type_id');
				//通过数据验证				
				if($attrmodel->add()){
					//添加属性成功,并传递type_id到lst方法
					$this->success('添加属性成功',U('lst',array('id'=>$type_id)));
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}

	//商品属性列表
	public function lst(){
		//接收传递的商品类型的id
		$type_id = $_GET['id']+0;
		//取出某商品类型的商品属性的数据
		$attrmodel = D('Attribute');
		$attrdata = $attrmodel->field("a.*,b.type_name")->join(a left join it_type b on a.type_id=b.id)->where("type_id=$type_id")->select();
		$this->assign('attrdata',$attrdata);
		//取出商品的类型数据
		$typemodel = D('Type');
		$this->typedata = $typemodel->select();

		//把商品类型的id分配到静态页面
		$this->assign('type_id',$type_id);

		$this->display();
	}
}
?>

在静态页面中实现：
Admin/View/Attribute/lst.html
<select name="type_id"><option value="0">所有商品类型</option>
	<?php foreach($typedata as $v){
		if($v['id']==$type_id){
			$sel = "selected = selected";
		}else{
			$sel = '';
		}
	?>
		<option <?php echo $sel;?> value="<?php echo $v['id']?>"><?php echo $v['type_name']?></option>
	<?php }?>	
</select>

4、完成根据商品类型筛选数据

思路：给select标签添加一个 change事件，事件的代码就是提交表单，提交的位置是当前页面。
（1）修改表单：
Admin/View/Attribute/lst.html
<form action="__ACTION__" name="searchForm" method="GET">
    <img src="__PUBLIC__/Admin/images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />按商品类型显示:
      
    <select name="id"><option value="0">所有商品类型</option>
	<?php foreach($typedata as $v){
		if($v['id']==$type_id){
			$sel = "selected = selected";
		}else{
			$sel = '';
		}
	?>
		<option <?php echo $sel;?> value="<?php echo $v['id']?>"><?php echo $v['type_name']?></option>
	<?php }?>	
</select>

   
   
  </form>

（2）给select标签添加事件。
<js href="__PUBLIC__/Js/jquery.js"/>
<script>
$(function(){
	$("select[name=id]").change(function(){
		//完成表单的提交
		$("form[name=searchForm]").submit();
	});
});
</script>

（3）如果选择‘所有商品类型’则显示所有商品类型的属性。
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//接收添加数据的商品类型id
				$type_id = I('post.type_id');
				//通过数据验证				
				if($attrmodel->add()){
					//添加属性成功,并传递type_id到lst方法
					$this->success('添加属性成功',U('lst',array('id'=>$type_id)));
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}

	//商品属性列表
	public function lst(){
		//接收传递的商品类型的id
		$type_id = $_GET['id']+0;

		if(empty($type_id)){
			$where = 1;
		}else{
			$where['type_id'] = array('eq',$typeid);//等价于type_id=$type_id
		}

		//取出某商品类型的商品属性的数据
		$attrmodel = D('Attribute');
		$attrdata = $attrmodel->field("a.*,b.type_name")->join(a left join it_type b on a.type_id=b.id)->where($where)->select();
		$this->assign('attrdata',$attrdata);
		//取出商品的类型数据
		$typemodel = D('Type');
		$this->typedata = $typemodel->select();

		//把商品类型的id分配到静态页面
		$this->assign('type_id',$type_id);

		$this->display();
	}
}
?>

5、完成分页显示商品属性的列表
（1）分页的代码：
AttributeController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class AttributeController extend Controller
{
	//添加商品的属性
	public function add(){
		if(IS_POST){
			$attrmodel = D('Attribute');
			if($attrmodel->create()){
				//接收添加数据的商品类型id
				$type_id = I('post.type_id');
				//通过数据验证				
				if($attrmodel->add()){
					//添加属性成功,并传递type_id到lst方法
					$this->success('添加属性成功',U('lst',array('id'=>$type_id)));
					exit;
				}else{
					$this->error('添加属性失败');
				}
			}else{
				//没有通过数据验证,$attrmodel->getError()获取错误提示
				$this->error($attrmodel->getError());
			}
		}
		//取出商品类型数据
		$typedata = D('Type');
		$this->typedata = $typedata;
		$this->display();
	}

	//商品属性列表
	public function lst(){
		//接收传递的商品类型的id
		$type_id = $_GET['id']+0;

		if(empty($type_id)){
			$where = 1;
		}else{
			$where['type_id'] = array('eq',$typeid);//等价于type_id=$type_id
		}

		//取出某商品类型的商品属性的数据
		$attrmodel = D('Attribute');

		
		$count = $attrmodel->where($where)->count();//根据条件获取总记录数
		
		$Page = new \Think\Page($count,2);//实例化分页类,传入总记录数和每页显示的记录数

		$attrdata = $attrmodel->field("a.*,b.type_name")->join(a left join it_type b on a.type_id=b.id)->where($where)->select();
		$this->assign('attrdata',$attrdata);

		//分配分页的字符串
		$Page->setConfig('prev','上一页');
		$Page->setConfig('next','下一页');
		$show = $Page->show();//分页字符串显示输出
		$this->assign('page',$show);

		//取出商品的类型数据
		$typemodel = D('Type');
		$this->typedata = $typemodel->select();

		//把商品类型的id分配到静态页面
		$this->assign('type_id',$type_id);

		$this->display();
	}
}
?>
（2）给分页的字符串添加样式。

二、栏目管理
商品栏目又叫商品分类，是管理商品的。

1、创建栏目表
create table it_category(
    id tinyint unsigned  primary key auto_increment,
    cat_name  varchar(32) not null comment '商品栏目的名称',
    parent_id  tinyint not null default 0 comment '栏目的父id,默认是0'
)engine myisam charset utf8;


2、添加栏目
（1）新建一个栏目的控制器，并添加add的方法，并拷贝对应的静态页面，并修改静态页面的样式和图片的路径。
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$this->display();
	}
}
?>
（2）打开对应的静态页面，修改表单。
Admin/View/Category/add.html
<form action="__ACTION__" method="post" name="theForm" enctype="multipart/form-data">
  <table width="100%" id="general-table">
      <tr>
        <td class="label">栏目名称:</td>
        <td>
          <input type='text' name='cat_name' maxlength="20" value='' size='27' /> <font color="red">*</font>
        </td>
      </tr>  
      <tr>
        <td class="label">上级栏目:</td>
        <td>
          <select name="parent_id">
          	<option value="0">顶级栏目</option>
          	
          </select>
        </td>
      </tr>         

      <tr>
        <td class="label"></td>
        <td>
           	<input type="submit" value=" 确定 " />
        	<input type="reset" value=" 重置 " />
        </td>
      </tr>
      </table>  
  </form>

（3）新建一个栏目的模型，并添加数据验证，
Admin/Model/CategoryModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extend Model
{
	//添加数据验证
	protected $_validate = array(
		array('cat_name','require','栏目名称不能为空'),
	);
}
?>
（4）在控制器的add方法中，完成栏目的添加
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}
		$this->display();
	}
}
?>
（5）添加栏目的子栏目
第一步：要取出栏目的数据，并进行 无限极分类。
在模型里面定义一个方法，用于取出栏目数据

Admin/Model/CategoryModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extend Model
{
	//添加数据验证
	protected $_validate = array(
		array('cat_name','require','栏目名称不能为空'),
	);

	//取出栏目数据的方法
	public function getTree(){
		$arr = $this->select();//从栏目表中获取栏目数据
		return $this->_getTree($arr,$parent_id=0,$lev=0);
	}
	public function _getTree($arr,$parent_id=0,$lev=0){
		static = $list = array();
		foreach($arr as $v){
			if($v['parent_id']==$parent_id){
				$v['lev'] = $lev;
				$list[] = $v;
				$this->_getTree($arr,$v['id'],$lev+1);
			}
		}
		reture $list;	
	}
}
?>

第二步：在add方法中取出栏目的数据：
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}

		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$this->display();
	}
}
?>
第三步：取出栏目数据，在静态页面中进行显示；
Admin/View/Category/add.html
<form action="__ACTION__" method="post" name="theForm" enctype="multipart/form-data">
  <table width="100%" id="general-table">
      <tr>
        <td class="label">栏目名称:</td>
        <td>
          <input type='text' name='cat_name' maxlength="20" value='' size='27' /> <font color="red">*</font>
        </td>
      </tr>  
      <tr>
        <td class="label">上级栏目:</td>
        <td>
          <select name="parent_id">
          	<option value="0">顶级栏目</option>
          	<?php foreach($catedata as $v){?>
				<option value="<?php echo $v['id']?>"><?php echo str_repeat('--',$v['lev']).$v['cat_name']?></option>
          	<?php }?>
          </select>
        </td>
      </tr>         

      <tr>
        <td class="label"></td>
        <td>
           	<input type="submit" value=" 确定 " />
        	<input type="reset" value=" 重置 " />
        </td>
      </tr>
      </table>  
  </form>

3、栏目列表：
（1）在栏目的控制器里面，添加一个lst方法，用于取出栏目的数据
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}

		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$this->display();
	}
	//栏目数据列表
	public function lst(){
		$catemodel = D('Category');
		$catedata = $catemodel->select();
		$this->assign('catedata',$catedata);
		$this->display();
	}
}
?>
（2）把取出的栏目数据，给遍历到对应的静态页面中。
Admin/View/Category/lst.html
<?php foreach($catedata as $v){?>
      <tr align="center" class="0" id="0_1" id = 'tr_1'>
    <td align="left" class="first-cell" style = 'padding-left="0"'>
            <?php echo str_repeat('&nbsp;',$v['lev']*4)?><img src="__PUBLIC__/Admin/images/menu_minus.gif" id="icon_0_1" width="9" height="9" border="0" style="margin-left:0em" />

            <span><a href="#" ><?php echo $v['cat_name']?></a></span>
        </td>
    <td width="10%">0</td>
    
    <td width="24%" align="center">
      <a href="#">编辑</a> |
      <a href="#">删除</a>
    </td>
  </tr>
<?php }?>
4、删除栏目数据，
要求：如果该栏目下面有子栏目，则不能够被删除。
（1）在栏目的列表页面添加删除的链接。
Admin/View/Category/lst.html
<a href="__CONTROLLER__/del/id/<?php echo $v['id'];?>" onclick="return confirm('你确定要删除吗?')">删除</a>
（2）在栏目的控制器里面添加一个del的方法，
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}

		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$this->display();
	}
	//栏目数据列表
	public function lst(){
		$catemodel = D('Category');
		$catedata = $catemodel->select();
		$this->assign('catedata',$catedata);
		$this->display();
	}
	//删除栏目数据
	public function del(){
		//接收传递栏目的id
		$id = $_GET['id']+0;
		$catemodel = D('Category');
		$info = $catemodel->where("parend_id=$id")->select();
		if($info){
			//如果有子栏目,则不能被删除
			$this->error('该栏目下面有子栏目,不能删除');
		}
		$res = $catemodel->delete($id);
		if($res!==false){
			//执行成功
			$this->success('删除成功',U('lst'));
		}else{
			$this->error('删除失败');
		}
	}
}
?>

5、修改栏目。
注意：在修改时，不能把自己的子孙栏目当成自己的父栏目。
（1）在栏目的列表页面添加修改的链接。
Admin/View/Category/lst.html
<a href="__CONTROLLER__/update/id/<?php echo $v['id']?>">编辑</a>
（2）在栏目的控制器里面添加一个update的方法，并根据传递的id,取出数据。
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}

		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$this->display();
	}
	//栏目数据列表
	public function lst(){
		$catemodel = D('Category');
		$catedata = $catemodel->select();
		$this->assign('catedata',$catedata);
		$this->display();
	}
	//删除栏目数据
	public function del(){
		//接收传递栏目的id
		$id = $_GET['id']+0;
		$catemodel = D('Category');
		$info = $catemodel->where("parend_id=$id")->select();
		if($info){
			//如果有子栏目,则不能被删除
			$this->error('该栏目下面有子栏目,不能删除');
		}
		$res = $catemodel->delete($id);
		if($res!==false){
			//执行成功
			$this->success('删除成功',U('lst'));
		}else{
			$this->error('删除失败');
		}
	}
	//更新栏目数据
	public function update(){
		//从栏目表中取出该id所在行的数据
		$id = $_GET['id']+0;
		$catemodel = D('Category');
		$info = $catemodel->where("id=$id")->find();
		$this->assign('info',$info);
		//取出所有栏目的数据
		catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);
		$this->display();
	}
}
?>
（3）把取出的数据，给遍历到静态页面中。并添加一个隐藏域
注意：通过add.html 修改得到一个update.html 的静态页面。
Admin/View/Category/update.html
<select name="parent_id">
	<option value="0">顶级栏目</option>
	<?php foreach($catedata as $v){
		if($v['id']==$info['parent_id']){
			$sel = "selected = selected";
		}else{
			$sel = "";
		}
	?>
	<option <?php echo $sel;?> value="<?php echo $v['id'];?>"><?php echo str_repeat('--',$v['lev']).$v['cat_name']?></option>
	<?php }?>
</select>
<input type="hidden" name="id" value="<?php echo $info['id']?>"/>

（4）接收表单提交的数据，完成修改
第一步：要注意：提交的父级栏目是否是自己的子孙栏目。若是则不允许这样修改。
通过在模型里面定义一个函数，用户获取子孙栏目的id.
Admin/Model/CategoryModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class CategoryModel extend Model
{
	//添加数据验证
	protected $_validate = array(
		array('cat_name','require','栏目名称不能为空'),
	);

	//取出栏目数据的方法
	public function getTree(){
		$arr = $this->select();//从栏目表中获取栏目数据
		return $this->_getTree($arr,$parent_id=0,$lev=0);
	}
	public function _getTree($arr,$parent_id=0,$lev=0){
		static = $list = array();
		foreach($arr as $v){
			if($v['parent_id']==$parent_id){
				$v['lev'] = $lev;
				$list[] = $v;
				$this->_getTree($arr,$v['id'],$lev+1);
			}
		}
		reture $list;	
	}
	//根据传递的id,查找子孙栏目的id
	public function getChild($id){
		$arr = $this->select();
		return $this->_getChild($arr,$id);
	}
	public function _getChild($arr,$id){
		static $ids = array();
		foreach($arr as $v){
			if($v['parent_id']==$id){
				$ids[] = $v['id'];
				$this->_getChild($arr,$v['id']);
			}
		}
		return $ids;
	}
}
?>

第二步：完成修改：
代码如下：
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}

		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$this->display();
	}
	//栏目数据列表
	public function lst(){
		$catemodel = D('Category');
		$catedata = $catemodel->select();
		$this->assign('catedata',$catedata);
		$this->display();
	}
	//删除栏目数据
	public function del(){
		//接收传递栏目的id
		$id = $_GET['id']+0;
		$catemodel = D('Category');
		$info = $catemodel->where("parend_id=$id")->select();
		if($info){
			//如果有子栏目,则不能被删除
			$this->error('该栏目下面有子栏目,不能删除');
		}
		$res = $catemodel->delete($id);
		if($res!==false){
			//执行成功
			$this->success('删除成功',U('lst'));
		}else{
			$this->error('删除失败');
		}
	}
	//更新栏目数据
	public function update(){
		$catemodel = D('Category');
		if(IS_POST){
			//完成表单修改
			//(1)提交的父级栏目的id(即parent_id)是否在自己子孙栏目的id里面
			$parent_id = I('post.parent_id');
			$id = I('post.id');//自己的id
			//思路:要找出自己的子孙栏目的id,判断提交的父id(即parent_id)是否在其中即可
			$ids = $catemodel->getChild($id);//返回子孙栏目的id
			$ids[] = $id;//把自己的id添加到该数组里面
			if(in_array($parent_id, $ids)){
				//在里面,则不允许的提交
				$this->error('不允许把自己的子栏目当成父栏目');
			}
			//(2)完成修改,验证修改是否成功,防止栏目名称为空
			if($catemodel->create()){
				//防止修改不存在的数据成功,受影响行数为0
				if($catemodel->save()){
					$this->success('修改栏目成功',U('lst'));
					exit;
				}else{
					$this->error('修改栏目失败');
				}
			}else{
				//验证修改栏目失败
				$this->error($catemodel->getError());
			}
		}


		//从栏目表中取出该id所在行的数据
		$id = $_GET['id']+0;
		
		$info = $catemodel->where("id=$id")->find();
		$this->assign('info',$info);
		//取出所有栏目的数据
		catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);
		$this->display();
	}
}
?>


（5）在修改栏目时，上级栏目位置不显示自己和自己的子孙栏目。

取出自己的子孙栏目的id
Admin/Controller/CategoryController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class CategoryController extend Controller
{
	//添加栏目的方法
	public function add(){
		$catemodel = D('Category');
		if(IS_POST){			
			if($catemodel->create()){
				if($catemodel->add()){
					//添加成功
					$this->success('添加栏目成功',U('lst'));
					exit;
				}else{
					$this->error('添加栏目失败');
				}
			}else{
				$this->error($catemodel->getError());
			}
		}

		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$this->display();
	}
	//栏目数据列表
	public function lst(){
		$catemodel = D('Category');
		$catedata = $catemodel->select();
		$this->assign('catedata',$catedata);
		$this->display();
	}
	//删除栏目数据
	public function del(){
		//接收传递栏目的id
		$id = $_GET['id']+0;
		$catemodel = D('Category');
		$info = $catemodel->where("parend_id=$id")->select();
		if($info){
			//如果有子栏目,则不能被删除
			$this->error('该栏目下面有子栏目,不能删除');
		}
		$res = $catemodel->delete($id);
		if($res!==false){
			//执行成功
			$this->success('删除成功',U('lst'));
		}else{
			$this->error('删除失败');
		}
	}
	//更新栏目数据
	public function update(){
		$catemodel = D('Category');
		if(IS_POST){
			//完成表单修改
			//(1)提交的父级栏目的id(即parent_id)是否在自己子孙栏目的id里面
			$parent_id = I('post.parent_id');
			$id = I('post.id');//自己的id
			//思路:要找出自己的子孙栏目的id,判断提交的父id(即parent_id)是否在其中即可
			$ids = $catemodel->getChild($id);//返回子孙栏目的id
			$ids[] = $id;//把自己的id添加到该数组里面
			if(in_array($parent_id, $ids)){
				//在里面,则不允许的提交
				$this->error('不允许把自己的子栏目当成父栏目');
			}
			//(2)完成修改,验证修改是否成功,防止栏目名称为空
			if($catemodel->create()){
				//防止修改不存在的数据成功,受影响行数为0
				if($catemodel->save()){
					$this->success('修改栏目成功',U('lst'));
					exit;
				}else{
					$this->error('修改栏目失败');
				}
			}else{
				//验证修改栏目失败
				$this->error($catemodel->getError());
			}
		}


		//从栏目表中取出该id所在行的数据
		$id = $_GET['id']+0;
		
		$info = $catemodel->where("id=$id")->find();
		$this->assign('info',$info);
		//取出所有栏目的数据
		catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);

		$ids = $catemodel->getChild($id);//返回子孙栏目的id
		$ids[] = $id;//把自己添加到该数组里面
		$this->assign('ids',$ids); 

		$this->display();
	}
}
?>

在update.html页面进行判断，如果是自己和自己的子孙栏目，则不显示。
Admin/View/Category/update.html
<select name="parent_id">
	<option value="0">顶级栏目</option>
	<?php foreach($catedata as $v){

		if(in_array($v['id'], $ids)){
			continue;
		}

		if($v['id']==$info['parent_id']){
			$sel = "selected = selected";
		}else{
			$sel = "";
		}
	?>
	<option <?php echo $sel;?> value="<?php echo $v['id'];?>"><?php echo str_repeat('--',$v['lev']).$v['cat_name']?></option>
	<?php }?>
</select>
<input type="hidden" name="id" value="<?php echo $info['id']?>"/>

三、商品管理
1、建立商品表
需要注意的字段：
商品的缩略图（小图）：大小为100*100的，字段名称为：goods_thumb

商品的详情页面中的图（中图）：大小为：230*230,字段名称为：goods_img

还有商品的原图：原图主要用于放大镜效果：字段名称为： goods_ori


is_best精品   is_new新品  is_hot热销
创建商品表：
create table it_goods(
    id mediumint  unsigned primary key auto_increment,
    goods_name  varchar(32) not null comment '商品名称',
    cat_id  tinyint not null comment '商品所属栏目',
    goods_sn  varchar(32) not null comment '商品的货号',
    goods_desc varchar(128) not null default '' comment '商品的描述',
    shop_price decimal(9,2) not null default 0.0 comment '本店价格',
    market_price decimal(9,2) not null default 0.0 comment '市场价格',
    goods_number int not null default  1 comment '库存',
    is_best tinyint  not null default 1 comment '0表示非精品，1表示是精品',
    is_hot tinyint  not null default 1 comment '0表示非热卖，1表示是热卖',
    is_new tinyint  not null default 1 comment '0表示非新品，1表示是新品',
    is_sale tinyint not null default 1 comment '0表示不销售，1表示正常销售状态',
    is_delete tinyint not null default 0 comment '0表示没有删除，1表示删除状态',
    goods_thumb varchar(64) not null default '' comment '缩略图的路径',
    goods_img varchar(64) not null default '' comment '中图的路径',
    goods_ori varchar(64) not null default '' comment '原图的路径',
    add_time int not null default 0 comment '添加时间'
)engine myisam charset utf8;

2、添加商品的基本信息：
（1）新建一个商品的控制器，并添加add的方法，并拷贝对应的静态页面。
Admin/Controller/GoodsController.class.php
<?php
namespace Admin/Controller;
use Think\Controller;
class GoodsController extend Controller
{
	//添加商品的方法
	public function add(){
		$display();
	}
}
?>
（2）修改表单

（3）在控制器add方法中完成商品 数据的添加。
Admin/Controller/GoodsController.class.php
<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extend Controller
{
	//添加商品的方法
	public function add(){
		if(IS_POST){
			$goodsmodel = D('Goods');
			if($goodsmodel->create()){
				if($goodsmodel->add()){
					//添加商品成功
					$this->success('添加商品成功',U('lst'));
					exit;
				}
			}
			$errors = $goodsmodel->getError();//获取错误提示,获取模型里面create()方法失败后的错误提示
			//$goodsmodel->getError()方法是获取的模型里面的$this->error属性的内容
			if(empty($errors)){
				$errors = '添加商品失败';
			}
			$this->error($errors);
		}

		//取出商品栏目信息
		$catemodel = D('Category');
		$catedata = $catemodel->getTree();
		$this->assign('catedata',$catedata);
		$this->display();
	}
}
?>
（4）完成文件的上传，
先上传完成后，再入库。
把上传的代码写入到钩子函数里面，
钩子函数有哪些？
_before_insert();
_after_insert();
_before_update();
_after_update()
_before_delete();
_after_delete();
钩子函数，在模型里面定义，由模型的add(),save(),delete()方法自动调用。

因此在_before_insert()方法里面完成上传任务。
注意点：
php.ini中设置允许的上传单个文件的最大值。
upload_max_filesize = 102M
打印上传之后的结果：

配置文件中的配置：
Common/Conf/config.php
<?php
	return array(
	//'配置项'=>'配置值'
	/* 数据库设置 */
	'DB_TYPE'               =>  'mysql',     // 数据库类型
	'DB_HOST'               =>  'localhost', // 服务器地址
	'DB_NAME'               =>  'shop',          // 数据库名
	'DB_USER'               =>  'root',      // 用户名
	'DB_PWD'                =>  'root',          // 密码
	'DB_PORT'               =>  '3306',        // 端口
	'DB_PREFIX'             =>  'it_',    // 数据库表前缀
	'UPLOAD_ALLOW_EXT'		=>	array('jpg','jpeg','gif','png'),//上传文件允许的类型
	'UPLOAD_ROOT_PATH'		=>	'./Public/Upload/',//配置上传文件的根路径,是给php代码操作用的:是php操作磁盘的路径,不能省略前面的点,浏览器操作磁盘的路径可以省略点
	);
	'UPLOAD_FILE_SIZE'		=>	'3M',//配置上传文件的大小
?>
具体的代码：
在goods模型里面定义钩子函数：
Admin/Model/GoodsModel.class.php
<?php
namespace Admin\Model;
use Think\Model;
class GoodsModel extends Model
{
	//定义一个钩子函数
	protected function _before_insert(&$data, $options){
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
			//拼接原图的保存路径信息
			$goods_ori = $info['goods_img']['savepath'].$info['goods_img']['savename'];
			$image = new \Think\Image(); 
			$image->open($rootpath.$goods_ori);
			//拼接缩略图的保存路径信息
			$goods_thumb1=$info['goods_img']['savepath'].'thumb1'.$info['goods_img']['savename'];
			$goods_thumb2=$info['goods_img']['savepath'].'thumb2'.$info['goods_img']['savename'];
			//注意:生成多张缩略图时,先生成大图,再生成小图
			//按照原图比例生成一个默认左上角裁剪为230*230,100*100的缩略图,和保存的路径		
			$image->thumb(230,230)->save($rootpath.$goods_thumb1);
			$image->thumb(100,100)->save($rootpath.$goods_thumb2);
			//需要入库的数据,直接存储到&$data数组里面,即可自动入库
			$data['goods_ori']=$goods_ori;//存储原图的路径
			$data['goods_img']=$goods_thumb1;//存储中图的路径
			$data['goods_thumb']=$goods_thumb2;//存储小图的路径
		}else{
			//上传失败,返回false
			//要把错误提示返回
			$this->error = $upload->getError();//返回上传失败的信息
			return false;
		}
	}	
}
?>



