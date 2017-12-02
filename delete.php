<?php 
// 删除微博

require './function.php';

$redis = getRedis();

$postid = $_GET['postid'];
@session_start();
$userid = $_SESSION['userid'];

// 从微博的主要key中加delete
if($redis->hGet('post:'.$postid,'isDeleted') == 1 || !$redis->exists('post:'.$postid))
{
	echo json_encode(['error' =>1,'msg'=>'文章不存在或者已经被删除']);
}
$redis->hSet('post:'.$postid,'isDeleted',1);
// 最新的50条中删除
$redis->lRem('newestPost',$postid,0);

// 从当前用户的微博列表里删除
$redis->zDelete('user:'.$userid.':posts',$postid);
// 赞过的和收藏过的人不过处理，只是在取的时候判断isDeleted


// 返回
echo json_encode([
				'error' => 0,
				'msg' =>''
				]);	