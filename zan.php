<?php 
/**
 *
 * 赞的行为
 *
 */
require './function.php';
//接受参数
$postid = $_GET['postid'];
$f = $_GET['f'];
@session_start();
$userid = $_SESSION['userid'];
$redis = getRedis();
if($f == 1) // 赞的行为
{
	// 用户赞了这条微博
	$redis->zAdd('user:'.$userid.':zan',time(),$postid);
	// 这条微博被这个用户赞了
	$redis->zAdd('post:'.$postid.':zanedby',time(),$userid);
	// 更新这条微博被赞的次数
	$count = zanedCount($postid);
	echo json_encode([
		'error' => 0,
		'now_status' => 0,
		'count' => $count
	]);
}
else
{
	// 取消赞
	$redis->zDelete('user:'.$userid.':zan',$postid);
	$redis->zDelete('post:'.$postid.':zanedby',$userid);
	$count = zanedCount($postid);

	echo json_encode([
		'error' => 0,
		'now_status' => 1,
		'count' => $count
	]);
}



