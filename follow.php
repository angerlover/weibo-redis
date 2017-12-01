<?php 
/**
 *
 * 关注和取关行为
 *
 */
require './function.php';
$redis = getRedis();
// 判断登录
if(!isLogin())
{
	echo '请先登录！';
	header('refresh:3;url=login.php');
}
// // 接收参数
$f = $_GET['f'];
$targetid = $_GET['uid']; // 被操作人id
$userid = $_SESSION['userid']; // 当前登录用户id

// 关注
if($f == 1) 
{
// 1加入当前用户的关注列表
$redis->zAdd('user:'.$userid.':following',time(),$targetid);

// 2加入被关注人的粉丝列表
$redis->zAdd('user:'.$targetid.':followedBy',time(),$userid);
echo json_encode([
	'error' => 0,
	'now_status' => 0,
	'now_content' => '取消关注'
]);

}
// 取消关注
else
{
	// 1 移除当前用户的关注列表
	$redis->zDelete('user:'.$userid.':following',$targetid);
	// 2 移除被关注人的粉丝列表
	$redis->zDelete('user:'.$targetid.':followedBy',$userid);
	echo json_encode([
	'error' => 0,
	'now_status' => 1,
	'now_content' => '关注ta'
]);
}
// 跳转回个人主页
// header('location:profile.php?u='.$targetid);
