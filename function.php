<?php 
/**
 *
 * 通用函数
 *
 */


/**
 *
 * 获取redis
 *
 */
function getRedis()
{
	static $redis;
	if(empty($redis))
	{
		$redis = new Redis();
		$redis->connect('localhost');
		return $redis;

	}
	else
	{
		return $redis;
	}

}

/**
 *
 * 检测是否登录
 *
 */

function isLogin()
{
	@session_start();
	return empty($_SESSION['userid'])?false:true;
}

/**
 *
 * 根据用户id获取其所有微博（包含用户名和pic）
 *
 */

function getPostByUserId($userid)
{
	$res = [];
	$redis = getRedis();

	// 获取所有的微博id
	$postIds = $redis->zRevRange('user:'.$userid.':posts',0,-1);
	if(!$postIds) return false;
	foreach ($postIds as $key => $value) 
	{
		$res[$value] = array_merge($temp = $redis->hGetAll('post:'.$value),['username'=>$redis->hGet('user:'.$temp['user_id'],'name'),
			'pic'=>$redis->hGet('user:'.$temp['user_id'],'pic')
			]);
	}
	return $res;
}

/**
 *
 * 友好的方式显示微博发布的时间：几分钟前，几小时前
 *
 */

function getTime($post_time)
{
	$dur = time()-$post_time;
	if($dur<10)
	{
		echo '刚刚';
	}
	elseif($dur<60)
	{
		echo $dur.'秒前';
	}
	elseif($dur<3600)
	{
		echo intval((time()-$post_time)/60).'分钟前';
	}
	elseif($dur<3600 * 24)
	{
		echo intval((time()-$post_time)/3600).'小时前';
	}
	else
	{
		echo date('Y-m-d H:i:m',$post_time);
	}

}

/**
 *
 * 检查当前登录用户是否关注了某人
 *
 */

function isFollowing($targetid)
{
	// 取出当前用户的所有关注人
	$redis = getRedis();
	@session_start();
	if(empty($_SESSION['userid'])) return false;
	$all = $redis->zRange('user:'.$_SESSION['userid'].':following',0,-1,false);
	return in_array($targetid, $all);
}


/**
 *
 * 获取指定用户的关注列表的微博
 *
 */

function getPostsFromFollowing($userid)
{
	$res = [];
	// 获取关注列表
	$redis = getRedis();
	$following = $redis->zRevRange('user:'.$userid.':following',0,-1);
	// var_dump($following);die;
	foreach ($following as $key => $value)
	{
		if(getPostByUserId($value))
		{
			$res[] = getPostByUserId($value);
		}
	}
	// var_dump($res);die;
	return $res;

}

/**
 *
 * 获取当前用户粉丝数
 *
 */

function getFansCount($userid)
{
	$redis = getRedis();
	return $redis->zCount('user:'.$userid.':followedBy',0,time());
}
/**
 *
 * 获取当前用户关注数
 *
 */

function getFollowingsCount($userid)
{
	$redis = getRedis();
	return $redis->zCount('user:'.$userid.':following',0,time());
}

/**
 *
 * 获取一个用户的全部个人信息（头像，签名等）
 *
 */

function getUserInfo($userid)
{
	$redis = getRedis();
	return $redis->hGetAll('user:'.$userid);
}

/*=========================
=            赞            =
=========================*/

/**
 *
 * 当前登录用户是否赞了这条微博
 *
 */

function everZanedThisPost($postid)
{
	$redis = getRedis();
	$userid = $_SESSION['userid'];
	$allPostIds = $allPostIds =  $redis->zRevRange('user:'.$userid.':zan',0,time());
	return in_array($postid, $allPostIds);

}


/**
 *
 * 返回用户赞过的微博
 *
 */

function hasZanedPosts($userid)
{
	$redis = getRedis();
	$allPostIds =  $redis->zRevRange('user:'.$userid.':zan',0,time());
	$res = [];
	foreach ($allPostIds as $key => $value) 
	{
		$res[$value] = array_merge($temp = $redis->hGetAll('post:'.$value),
			['username'=>$redis->hGet('user:'.$temp['user_id'],'name'),
			 'pic'=>$redis->hGet('user:'.$temp['user_id'],'pic')
			]
		);
	}

	return $res;
}
/**
 *
 * 一条微博被赞的次数
 *
 */

function zanedCount($postid)
{
	$redis = getRedis();
	return $redis->zCount('post:'.$postid.':zanedby',0,time());
}



/*=====  End of 赞  ======*/

/*============================
=            收藏微博            =
============================*/

/**
 *
 * 当前登录用户是否收藏了这条微博
 *
 */

function everCollectedThisPost($postid)
{
	$redis = getRedis();
	$userid = $_SESSION['userid'];
	$allPostIds = $allPostIds =  $redis->zRevRange('user:'.$userid.':collect',0,time());
	return in_array($postid, $allPostIds);

}


/**
 *
 * 获取用户收藏过的微博
 *
 */

function hasCollectedPosts($userid)
{
	$redis = getRedis();
	$allPostIds =  $redis->zRevRange('user:'.$userid.':collect',0,time());
	$res = [];
	foreach ($allPostIds as $key => $value) 
	{
		$res[$value] = array_merge($temp = $redis->hGetAll('post:'.$value),
			['username'=>$redis->hGet('user:'.$temp['user_id'],'name'),
			  'pic'=>$redis->hGet('user:'.$temp['user_id'],'pic')
			]
		);
	}

	return $res;
}


/*=====  End of 收藏微博  ======*/

