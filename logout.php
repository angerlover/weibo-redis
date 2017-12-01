<?php 
// 登出
session_start();
unset($_SESSION['userid']);
echo '成功退出!跳转回首页';
header('refresh:3;url=html/index.html');