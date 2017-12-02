<?php
require './function.php';
// 检测登录
if(!isLogin())
{
	echo '你还没有登录！';
	header('refresh:3;url=html/login.html');exit;
}
$userid = $_SESSION['userid'];
$redis = getRedis();
if($_POST)
{
	// 获取数据
	$status = $_POST['status'];
	// 校验
	if(!$status)
	{
		echo '至少输入一个字！';
		header('location:home.php');
	}

	// 存储
	// 1 全局的普通微博表中
	
	$postId = $redis->incr('global_post_id');
	$res1 = $redis->hMSet('post:'.$postId,['status'=>htmlentities($status),'post_time'=>time(),'user_id'=>$userid]);


	// 2 存到个人的微博表中
	$res2 = $redis->zAdd('user:'.$userid.':posts',time(),$postId);

	// 3存到最新的50条微博中，用于热点展示
	$res3 = $redis->lpush('newestPost',$postId);
	$redis->ltrim('newestPost',0,50);

	if($res1 && $res2 && $res3)
	{
		header('location:home.php');
	}

}
/**
 *
 * 静态展示
 *
 */

else
{
// 获取当前用户的名字
$username = $redis->hGet('user:'.$userid,'name');


//关注列表的微博(包括了自己)
$myPosts = getPostsFromFollowing($userid);


// 获取粉丝数和关注数
$fanNum = getFansCount($userid);
$followingNum = getFollowingsCount($userid);


// 获取个人信息（头像，个性签名）
$info = getUserInfo($userid);



require 'html/home.html';
}







