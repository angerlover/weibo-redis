<?php 
/**
 *
 * 微博热点
 *
 */

require './function.php';

$redis = getRedis();

// 获取最新的5个注册用户
$newestUserIds = $redis->lRange('newestUser',0,4);
$newestUsers = []; // 拼接一个包含了name的数组
foreach ($newestUserIds as $key => $value)
{
	$newestUsers[$value]['name'] = $redis->hGet('user:'.$value,'name');
}
// var_dump($newestUsers);

// 获取最新的50条微博
$newestPostIds = $redis->lrange('newestPost',0,-1);
$newestPosts = [];
foreach ($newestPostIds as $key => $value) 
{
	// 利用merge把微博的用户名,pic拼接到一起
	$newestPosts[$value] = array_merge($a=$redis->hGetAll('post:'.$value),
		['username'=>$redis->hGet('user:'.$a['user_id'],'name'),
		 'pic'=>$redis->hGet('user:'.$a['user_id'],'pic')
		]);
}

// var_dump($newestPosts);die;
// $lastestPosts = [];
// 给微博装填名字：用&用问题，后面的html页面出错
// foreach ($newestPosts as $key => &$value) 
// {
// 	$lastestPosts['username'] = $redis->hGet('user:'.$value['user_id'],'name');
// }
// var_dump(array_merge($lastestPosts,$newestPosts));die;
require 'html/timeline.html';