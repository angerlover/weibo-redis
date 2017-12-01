<?php 
/**
 *
 * 我的粉丝
 *
 */
require './function.php';
// 检测登录
if(!isLogin())
{
	echo '你还没有登录';
	header('refresh:3;url=html/login.html');exit;
}

// 获取当前用户的粉丝列表
$userid = $_SESSION['userid'];
$redis = getRedis();
$myfansIds = $redis->zRevRange('user:'.$userid.':followedBy',0,-1);
$myfans = [];
foreach ($myfansIds as $key => $value) 
{
	$myfans[] = getUserInfo($value);
}
// var_dump($myfans);die;
require 'html/myfans.html';

