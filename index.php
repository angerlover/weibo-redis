<?php 
require './function.php';

// 检测是否登录
if(isLogin())
{
	header('refresh:1;url=home.php');
}
require 'html/index.html';
