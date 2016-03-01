<?php
	//PHP操作图片//
	//创建画布
	$img = imagecreatetruecolor(200,200);
	//给画布分配颜色
	$color = imagecolorallocate($img,255,255,255);
	//写入文字
	$true = imagestring($img,5,50,90,'hello world',$color);
	/**保存输出内容**/
	//告诉浏览器是图片;输出图片
	header('Content-type:image/png');
	imagepng($img);
	//保存图片
	imagepng($img,'hello.png');
	//释放资源
	imagedestroy($img);


<?php
	/**制作验证码图片**/
	//获取验证码字符串
	$captcha = '';
	for($i=0;$i<4;$i++){
		//chr将数字转换成对应的字符(ASCII)
		switch(mt_rand(0,2)){
			case 0:
				$captcha.=chr(mt_rand(49,57));
				break;
			case 1:
				$captcha.=chr(mt_rand(65,90));
				break;
			case 2:
				$captcha.=chr(mt_rand(97,122));
				break;
		}
	}
	//创建画布
	$img = imagecreatetruecolor(200,200);
	//调制背景颜色
	$bg = imagecolorallocate($img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
	//填充背景色
	imagefill($img,0,0,$bg);
	//循环写入验证码文字
	for($i=0;$i<4;$i++){
		//调制文字颜色
		$txtcolor=imagecolorallocate($img,mt_rand(50,150),mt_rand(50,150),mt_rand(50,150));
		//写入文字
		imagestring($img,mt_rand(1,5),60+$i*20,90,$captcha[$i],$txtcolor);
	}
	//输出图片
	header('Content-type:image/png');
	imagepng($img);
	//释放图片
	imagedestroy($img);


<?php
	/**中文验证码**/
	header('Content-type:text/html;charset=utf-8');
	//创建画布
	$img = imagecreatetruecolor(200,200);
	//调制背景色
	$bg = imagecolorallocate($img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
	//填充背景色
	imagefill($img,0,0,$bg);
	//写入内容
	for($i=0;$i<4;$i++){
		//调制文字颜色
		$txtcolor=imagecolorallocate($img,mt_rand(50,150),mt_rand(50,150),mt_rand(50,150));
		//获取随机中文
		$str = "今天我寒夜里看雪飘过怀着冷却了的心窝飘远方";
		$pos = mt_rand(0,strlen($str));
		$truepos = $pos-$pos%3;	//utf-8取模3,GBK取模2
		//取3个长度(字符串截取)
		$content = substr($str,$truepos,3);
		//写入中文
		imagettftext($img,mt_rand(20,40),mt_rand(-45,45),40+$i*30,mt_rand(80,120),$txtcolor,'simple,ttf',$content);
	}
	//输出图片
	header('Content-type:image/png');
	imagepng($img);
	//释放资源
	imagedestroy($img);