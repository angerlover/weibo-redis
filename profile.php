<?php 
/**
 *
 * 个人主页
 *
 */

require './function.php';

// 接受参数
$userid = $_GET['u'];

// 如果是登录用户的则直接跳转到个人主页
@session_start();
if($userid == $_SESSION['userid'])
{
	header('location:setting.php');exit;
}


// 获取当前用户的所有微博(时间倒序)
$redis = getRedis();
$allPosts = getPostByUserId($userid);

// 检测当前用户是否关注了目标用户
$isFollowing = isFollowing($userid);
// 获取目标用户的所有信息
$info = getUserInfo($userid);

// 获取目标用户的粉丝数和关注数
$fanCount = getFansCount($userid);
$followingCount = getFollowingsCount($userid);
require 'html/profile.html';
