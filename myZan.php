<?php 
/**
 *
 * 我赞过的微博
 *
 */

require './function.php';

if(!isLogin())
{
	echo '你还没有登录';
	header('refresh:3;url=login.php');
}
$redis = getRedis();
$userid = $_SESSION['userid'];
// 获取我赞的微博
$myZans = hasZanedPosts($userid);

require 'html/myZan.html';
