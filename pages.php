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
  case 'refSession':{
    checkLogin();
    include "pages/refSession/refSession.php";
    break;
  }
  case 'refKategori':{
    checkLogin();
    include "pages/refKategori/refKategori.php";
    break;
  }
  case 'refSoal':{
    checkLogin();
    include "pages/refSoal/refSoal.php";
    break;
  }
  case 'refHadiah':{
    checkLogin();
    include "pages/refHadiah/refHadiah.php";
    break;
  }
  case 'refAds':{
    checkLogin();
    include "pages/refAds/refAds.php";
    break;
  }
  case 'refBerita':{
    checkLogin();
    include "pages/refBerita/refBerita.php";
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
