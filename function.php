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
 * 根据用户id获取其所有微博（包含用户名和pic，postid）
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
			'pic'=>$redis->hGet('user:'.$temp['user_id'],'pic'),
			'postid' =>$value,
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
	// 循环关注列表取出所有微博
	foreach ($following as $key => $value)
	{
		if(getPostByUserId($value))
		{
			$res[] = getPostByUserId($value);
		}
	}
	// var_dump($res);die;

	// 根据时间倒序排序 三维数组依据post_time排序并且变为一维
	// 三维变一维
	$temp = [];
	foreach($res as $k=>$v)
	{
		foreach($v as $k1=>$v1)
		{
			$temp[] = $v1;
		}
	}
	// var_dump($temp);die;
	// 一维数组依据post_time排序
	$temp2 = [];
	foreach($temp as $v)
	{
		$temp2[] = $v['post_time'];
	}
	// 降序排序
	arsort($temp2);
	// 按照 这个顺序重新排列
	$final = [];
	foreach ($temp2 as $key => $value) 
	{
		$final[$key] = $temp[$key];
	}
	// var_dump($final);die;
	return $final;

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
			 'pic'=>$redis->hGet('user:'.$temp['user_id'],'pic'),
			 'postid'=>$value
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
		// 前端判断$res中可能有空的值，表示已经被删除，显示原微博以及被删除
		$res[$value] = array_merge($temp = $redis->hGetAll('post:'.$value),
			['username'=>$redis->hGet('user:'.$temp['user_id'],'name'),
			  'pic'=>$redis->hGet('user:'.$temp['user_id'],'pic'),
			  'postid' => $value
			]
		);

	}
	// var_dump($res);die;
	return $res;
}


/*=====  End of 收藏微博  ======*/

/*----------  删除微博  ----------*/

/**
 *
 * 指定微博id是否是我的微博
 *
 */

function isMyPosts($postid)
{
	$redis = getRedis();
	$userid = $_SESSION['userid'];
	$myPosts = $redis->zRange('user:'.$userid.':posts',0,-1);
	return in_array($postid, $myPosts);
}


