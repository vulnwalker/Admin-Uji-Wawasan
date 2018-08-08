<?php
session_start();
include "base/config.php";
include "base/baseObject.php";
function checkLogin(){
  if(!isset($_SESSION['username'])){
    header('Location: index.php');
  }
}
$pages = $_GET['page'];
switch ($pages) {
  case 'refMember':{
    checkLogin();
    include "pages/refMember/refMember.php";
    break;
  }

  case 'logout':{
    $_SESSION['username'] = '';
    unset($_SESSION['username']);
    session_destroy();
    checkLogin();
    break;
  }
  default:{
    checkLogin();
    include "pages/dashboard.php";
    break;
  break;
  }
}



 ?>
