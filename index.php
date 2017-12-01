<?php 
require './function.php';

// 检测是否登录
if(isLogin())
{
	header('refresh:3;url=home.php');
}
require './index.html';
