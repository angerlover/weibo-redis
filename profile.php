<?php 
/**
 *
 * 个人主页
 *
 */

require './function.php';

// 接受参数
$userid = $_GET['u'];



// 获取当前用户的所有微博(时间倒序)
$redis = getRedis();
$allPosts = getPostByUserId($userid);

// 检测当前用户是否关注了目标用户
$isFollowing = isFollowing($userid);
// 获取目标用户的所有信息
$info = getUserInfo($userid);
require 'html/profile.html';
