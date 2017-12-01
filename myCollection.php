<?php
/**
 *
 * 我收藏过的微博
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
// 获取我收藏的微博
$myCollections = hasCollectedPosts($userid);

require 'html/myCollections.html';