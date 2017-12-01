<?php 
/**
 *
 * 会员注册
 *
 */

require './function.php';
// 接受参数
$userName = $_POST['username'];
$password = $_POST['password'];
$password2 = $_POST['password2'];

// 验证参数
if(!$userName || !$password || !$password2)
{
	echo '输入的参数不合法';
	header('refresh:3;url=index.html');
}

elseif($password != $password2)
{
	echo '两次输入的密码不一致';
	header('refresh:3;url=index.html');
}


// 存储redis 会员的数据结构是Hash
$redis = getRedis();
// 生成id
$userId = $redis->incr('global_userid');
// 存入用户
$res = $redis->hMSet('user:'.$userId,['name'=>$userName,'password'=>md5('salt'.$password)]);
// 存入一个默认的头像
$redis->hSet('user:'.$userId,'pic','pic.png');
// 存入用户
$redis->set('user:'.$userName.':user_id',$userId);
// 存入最新的用户 只保留5个 用于热点广场
$redis->lPush('newestUser',$userId);
$redis->ltrim('newestUser',0,5);
// 自己是自己的粉丝
$redis->zAdd('user:'.$userId.':followedBy',time(),$userId);
$redis->zAdd('user:'.$userId.':following',time(),$userId);

if($res)
{
	echo '感谢您的注册！现在即将跳转到登录界面';
	header('refresh:3;url=html/login.html');
}
else
{
	echo '未知错误！';
	header('refresh:3;url=register.php');

}


