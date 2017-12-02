<?php 
// 我的微博
require './function.php';
if(!isLogin())
{
	echo '还没登录';
	header('refresh:3;url=login.php');
}

// 获取我的所有微博
$userid = $_SESSION['userid'];
$redis = getRedis();
$myPost = getPostByUserId($userid);


require 'html/myPost.html';