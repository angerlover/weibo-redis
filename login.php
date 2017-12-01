<?php 
require './function.php';
/**
 *
 * 登录
 *
 */

/*----------  接受数据  ----------*/
$username = $_POST['username'];
$password = $_POST['password'];

/*----------  验证数据  ----------*/
$redis = getRedis();
$userId = $redis->get('user:'.$username.':user_id');
$realpassword = $redis->hGet('user:'.$userId,'password');
if(!$username || !$password)
{
	echo '用户名或密码不能为空！';
	header('refresh:3;url=html/login.html');
}
elseif(!$userId)
{
 	echo '用户名不存在！';
	header('refresh:3;url=html/login.html');
}
elseif($realpassword != md5('salt'.$password))
{
	echo '密码错误！';
	header('refresh:3;url=html/login.html');

}
else // 验证完毕
{
	session_start();
	$_SESSION['userid'] = $userId;
	echo '登录成功';
	header('refresh:3;url=home.php');
}


