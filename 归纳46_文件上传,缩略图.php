新增商品
	文件上传(封装类)
	商品文件上传
	多文件上传
缩略图
	制作缩略图
	缩略图补白
	封装图片处理类
	商品应用缩略图

bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
dst_image 目标图象连接资源。(缩略图)
src_image 源图象连接资源。
dst_x 目标 X 坐标点。 
dst_y 目标 Y 坐标点。 
src_x 源的 X 坐标点。 
src_y 源的 Y 坐标点。 
dst_w 目标宽度。 
dst_h 目标高度。 
src_w 源图象的宽度。
src_h 源图象的高度。


<?php
//demo01_thumb.php
//制作缩略图
//1.获取原图资源
$src = imagecreatefromjpeg('Penguins.jpg');
//2.制作缩略图资源
$dst = imagecreatetruecolor(100,100);
//3.采样复制
//获取图片信息
$info = getimagesize('Penguins.jpg');
//var_dump($info);
//采样复制
$bool = imagecopyresampled($dst,$src,0,0,0,0,100,100,$info[0],$info[1]);
var_dump($bool);
//4.保存输出
header('Content-type:image/jpeg');
imagejpeg($dst);
//释放资源
imagedestroy($dst);
imagedestroy($src);
?>


<html>
demo02_multiple_files_upload_different.html
<body>
	<form enctype="multipart/form-data" method="POST" action="demo04_file.php">
		<input type="file" name="file1"/>
		<input type="file" name="file2"/>
		<input type="file" name="file3"/>
		<input type="submit" value="上传"/>
	</form>
</body>
?>
</html>

<html>
demo03_multiple_files_upload_same.html
<body>
	<form enctype="multipart/form-data" method="POST" action="demo04_file.php">
		<input type="file" name="file[]"/>
		<input type="file" name="file[]"/>
		<input type="file" name="file[]"/>
		<input type="submit" value="上传"/>
	</form>
</body>
</html>

<?php
demo04_file.php
//多文件上传处理
echo '<pre>';
//打印文件
//var_dump($_FILES);
//方案1解决方法:不同名
foreach($_FILES as $k=>$v){
	//$k是表单文件域名字;$v是一个五要素数组
	//调用函数对$v进行文件上传(移动操作)
}

//方案2解决方法:同名
for($i=0;$i<count($_FILES['file']['name']);$i++){
	//构造一个单独文件
	$file['name'] = $_FILES['file']['name'][$i];
	$file['tmp_name'] = $_FILES['file']['tmp_name'][$i];
	$file['type'] = $_FILES['file']['type'][$i];
	$file['size'] = $_FILES['file']['size'][$i];
	$file['error'] = $_FILES['file']['error'][$i];

	//调用上传函数对$file进行文件上传(移动操作)
}
?>