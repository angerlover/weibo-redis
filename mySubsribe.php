<?php 
/**
 *
 * 我的关注
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
$myfollowIds = $redis->zRevRange('user:'.$userid.':following',0,-1);
$myfollows = [];
foreach ($myfollowIds as $key => $value) 
{
	$myfollows[] = getUserInfo($value);
}
// var_dump($myfans);die;
require 'html/mySubsribe.html';


