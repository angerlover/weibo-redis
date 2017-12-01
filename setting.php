<?php 
// 个人设置页面
require './function.php';
if(!isLogin())
{
	echo '你还没有登录！';
	header('refresh:3;url=login.html');exit;
}

$userid = $_SESSION['userid'];
$redis = getRedis();
if($_POST)
{
	// 存储图片
	if($_FILES['pic']['error'] == 0)
	{
		$names = explode($_FILES['pic']['name'],'.');
		$ext = $names[count($names)-1];
		$desName = 'upload/'.time().rand().$ext;
		move_uploaded_file($_FILES['pic']['tmp_name'], $desName);

		// 存入redis
		$redis->hSet('user:'.$userid,'pic',$desName);
	}
	// var_dump($_POST);die;
	// 循环保存_POST中的数据
	foreach ($_POST as $key => $value) 
	{
		$redis->hSet('user:'.$userid,$key,$value);
	}
	echo '设置成功！';
	header('refresh:3;url=setting.php');
}
/**
 *
 * 静态展示
 *
 */

else
{
	// 获取头像路径
	$picPath = $redis->hGet('user:'.$userid,'pic');
	// 获取Email，签名等信息
	$email = $redis->hGet('user:'.$userid,'email');
	$intro = $redis->hGet('user:'.$userid,'intro');
	require 'html/setting.html';
}
