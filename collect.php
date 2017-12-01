<?php 
/**
 *
 * 收藏的行为
 *
 */
require './function.php';
//接受参数
$postid = $_GET['postid'];
$f = $_GET['f'];
@session_start();
$userid = $_SESSION['userid'];
$redis = getRedis();
if($f == 1) // 收藏的行为
{
	// 用户收藏了这条微博
	$redis->zAdd('user:'.$userid.':collect',time(),$postid);
	// 这条微博被这个用户收藏了
	$redis->zAdd('post:'.$postid.':collectedby',time(),$userid);
	// 更新这条微博被赞的次数
	echo json_encode([
		'error' => 0,
		'now_status' => 0,
	]);
}
else
{
	// 取消收藏
	$redis->zDelete('user:'.$userid.':collect',$postid);
	$redis->zDelete('post:'.$postid.':collectedby',$userid);

	echo json_encode([
		'error' => 0,
		'now_status' => 1,
	]);
}



