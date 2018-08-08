<?php
include "base/config.php";
include "base/baseObject.php";
class manageServer extends baseObject{
  var $Prefix = "manageServer";
  var $formName = "manageServerForm";
  var $tableName = "ref_server";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'diskChanged':{
        $jsonDisk = $this->getDetailDisk($idServer);
        $getDataDisk = $this->sqlArray($this->sqlQuery("select * from ref_disk where id = '$idDisk'"));
        $this->file_system = $getDataDisk['file_system'];
        $arrayDisk = json_decode($jsonDisk);
        $diskObject = array_filter(  $arrayDisk,
            function ($e) use (&$searchedValue) {
                return $e->FileSystem == $this->file_system;
            }
        );
        $sizeDisk = $diskObject[max(array_keys($diskObject))]->Size;
        $usedDisk = $diskObject[max(array_keys($diskObject))]->Used;
        $freeDisk =  $diskObject[max(array_keys($diskObject))]->Free;
        $mountedDisk =  $diskObject[max(array_keys($diskObject))]->Mounted;
        $persen =  $diskObject[max(array_keys($diskObject))]->Persen;
        $content = array(
          'hardDiskSize' => $sizeDisk,
          'hardDiskUsed' => $usedDisk,
          'hardDiskFree' => $freeDisk,
          'backupLocation' => str_replace("//","/",$mountedDisk."/".$getDataDisk['backup_location']),
          'persen' => str_replace('%','',$persen),
        );
  		break;
  		}
      case 'refreshList':{
        $content=array('tableContent' => $this->generateTable($currentLocation));
  		break;
  		}
      case 'loadSSHContent':{
        $content=$this->contentSSH($idServer);
  		break;
  		}
      case 'loadMysqlContent':{
        $content=$this->contentDatabase($idServer);
  		break;
  		}
      case 'saveBackUp':{
        $arrayParameter = array(
                                'struktur' => $strukturDatabase,
                                'triger' => $trigerDatabase,
                                'isi' => $isiDatabase,
                                'idRelease' => $idRelease,
                              );
        $jsonDisk = $this->getDetailDisk($idServer);
        $getDataDisk = $this->sqlArray($this->sqlQuery("select * from ref_disk where id = '$idDisk'"));
        $this->file_system = $getDataDisk['file_system'];
        $arrayDisk = json_decode($jsonDisk);
        $diskObject = array_filter(  $arrayDisk,
            function ($e) use (&$searchedValue) {
                return $e->FileSystem == $this->file_system;
            }
        );
        $sizeDisk = $diskObject[max(array_keys($diskObject))]->Size;
        $usedDisk = $diskObject[max(array_keys($diskObject))]->Used;
        $freeDisk =  $diskObject[max(array_keys($diskObject))]->Free;
        $mountedDisk =  $diskObject[max(array_keys($diskObject))]->Mounted;
        $persen =  $diskObject[max(array_keys($diskObject))]->Persen;

        $backupLocation = str_replace("//","/",$mountedDisk."/".$getDataDisk['backup_location']);
       $cek = $this->saveBackUp($idServer,$databaseName,$backupLocation,$arrayParameter);
  		break;
  		}
      case 'dropDatabase':{
       $cek = $this->dropDatabase($idServer,$this->hexDecode($databaseName));
  		break;
  		}
      case 'saveNewUserSSH':{
        if(empty($idSSH)){
          $err = "Isi ID User";
        }elseif(empty($usernameSSH)){
          $err = "Isi User Username";
        }elseif(empty($passwordSSH)){
          $err = "Isi Password User";
        }else{
           $cek = $this->saveNewUserSSH($idServer,$idSSH,$usernameSSH,$passwordSSH,$homeDirectory);
        }
  		break;
  		}
      case 'saveNewUserMysql':{
        if(empty($usernameMysql)){
          $err = "Isi User Username";
        }elseif(empty($passwordMysql)){
          $err = "Isi Password User";
        }else{
           $this->saveNewUserMysql($idServer,$usernameMysql,$passwordMysql);
           $cek = $this->sqlPasswordEncode($passwordMysql);
        }
  		break;
  		}
      case 'saveEditUser':{
        $this->saveEditUser($idServer,$idSSH,$usernameSSH,$passwordSSH,$homeDirectory);
        if(!empty($passwordSSH)){
          $this->changePasswordSSH($idServer,$usernameSSH,$passwordSSH);
        }
  		break;
  		}
      case 'saveEditUserMysql':{
         $this->changePasswordMysql($idServer,$usernameMysql,$passwordMysql);
         $cek = $this->sqlPasswordEncode($passwordMysql);
  		break;
  		}
      case 'deleteUserSSH':{
           $cek = $this->deleteUserSSH($idServer,$this->hexDecode($usernameSSH));
  		break;
  		}
      case 'deleteUserMysql':{
           $cek = $this->deleteUserMysql($idServer,$this->hexDecode($usernameMysql));
  		break;
  		}
      case 'addUserSSH':{
        $content=$this->addUserSSH($idServer);
  		break;
  		}
      case 'addUserMysql':{
        $content=$this->addUserMysql($idServer);
  		break;
  		}
      case 'editUserSSH':{
        $content=$this->editUserSSH($idServer,$this->hexDecode($usernameSSH));
  		break;
  		}
      case 'editUserMysql':{
        $content=$this->editUserMysql($idServer,$this->hexDecode($usernameMysql));
  		break;
  		}
      case 'backupDatabase':{
        $content=$this->backupDatabase($idServer,$this->hexDecode($databasename));
  		break;
  		}
      case 'renameDatabase':{
        $content=$this->renameDatabase($idServer,$this->hexDecode($databasename));
  		break;
  		}
      case 'cpuUsage':{
        $content=array('cpuUsage' => str_replace("\n","",$this->cpuUsage($idServer)) );
  		break;
  		}
      case 'memoryUsage':{
        $content=array('memoryUsage' => str_replace("%","",$this->memoryUsage($idServer)));
  		break;
  		}
      case 'diskSpace':{
        $explodeSpace = explode("%",$this->diskSpace($idServer));
        $content=array('diskSpace' => $explodeSpace[0]);
  		break;
  		}
      default:{
        $content = "API NOT FOUND";
      break;
      }
	 }

    return json_encode(array ('cek'=>$cek, 'err'=>$err, 'content'=>$content));
  }
  function __construct(){
      $getDataTarget = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '4'"));
      $getDataSumber = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '3'"));
      $explodeListFile = json_decode($this->listFileLocal(10,$sumberDir));
      for ($i=0; $i < sizeof($explodeListFile); $i++) {
            $dateModified = $this->dateConversion($explodeListFile[$i]->tanggal);
            $fileLocation = $explodeListFile[$i]->file;
            $fileBanding = str_replace($getDataSumber['directory'],"",$explodeListFile[$i]->file);
            $fileTarget = str_replace($getDataSumber['directory'],$getDataTarget['directory']."/",$explodeListFile[$i]->file);
            $fileSize = $explodeListFile[$i]->size;
            $dateModifiedTarget = str_replace('.','',date("Y-m-d H:i.", filemtime($fileTarget)));
            $err = array();
            if($fileSize != filesize($fileTarget)){
              $err[]= " SIZE FILE BEDA ";
            }
            if($dateModified != $dateModifiedTarget){
              $err[]= "TANGGAL BEDA ";
            }
            if(sizeof($err) == 0){
              echo " $fileBanding => OK \n";
            }else{
              $err = implode(", ",$err);
              echo "\033[01;31m $fileBanding =>  $err \n\033[0m";
            }
          // echo $dateModified." || $dateModifiedTarget "."\n";
      }

  }
  function dateConversion($tanggal){
    $arrayTanggal = explode("-",$tanggal);
    $arrayJam = explode(":",$tanggal);
    return $arrayTanggal[1]."-".$this->genNumber($arrayTanggal[2])."-".$this->genNumber($arrayTanggal[3])." ".$arrayJam[0].":".$arrayJam[1];
  }
  function dateToNumber($tanggal){
    $tanggal = str_replace("-","",$tanggal);
    $tanggal = str_replace(" ","",$tanggal);
    $tanggal = str_replace(":","",$tanggal);
    return $tanggal;
  }
  function genNumber($num, $dig=2){
  	$tambah = pow(10,$dig);//100000;
  	$tmp = ($num + $tambah).'';
  	return substr($tmp,1,$dig);
  }
  function generateMonth($arrayMonth){
    return $arrayMonth['month'];
  }
  function loadScript(){
    return "
    <script type='text/javascript' src='assets/widgets/charts/flot/flot.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-resize.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-stack.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-pie.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-tooltip.js'></script>
    <script type='text/javascript' src='assets/widgets/multi-select/multiselect.js'></script>
    <script src='plugins/contextMenu/contextMenu.min.js'></script>
    <link rel='stylesheet' type='text/css' href='plugins/contextMenu/contextMenu.min.css'>

    <script type='text/javascript' src='js/manageServer/manageServer.js'></script>
    <script type='text/javascript' src='js/manageServer/cpuUsage.js'></script>
    <script type='text/javascript' src='js/manageServer/memoryUsage.js'></script>
    <script type='text/javascript' src='js/manageServer/diskSpace.js'></script>
    <script type='text/javascript'>
      $( document ).ready(function() {

      });

    </script>
    ";
  }
  function sideBar(){
    $sideBar = "
    <div id='page-sidebar' style='height: 3195px;'>
    <div class='scroll-sidebar' style='height: 3195px;'>
      <ul id='sidebar-menu' class='sf-js-enabled sf-arrows'>
      <li class='header'><span>Admin</span></li>
      <li class='activeSidebar'>
          <a href='pages.php' title='Dashboard'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>Dashboard</span>
          </a>
      </li>
      <li class='divider'></li>
      <li class='header'><span>Server</span></li>
      <li>
          <a href='pages.php?page=refServer' title='Referensi Server'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>Referensi Server</span>
          </a>
      </li>
      <li >
          <a href='pages.php?page=fileManager' title='manageServer'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>File Manager</span>
          </a>
      </li>
      <li >
          <a href='pages.php?page=refRelease' title='refRelease'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>Referensi Release</span>
          </a>
      </li>
      <li>
          <a href='pages.php?page=historyBackup' title='historyBackup'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>History Backup</span>
          </a>
      </li>
      </ul>
    </div>
</div>
    ";
    return $sideBar;
  }
  function setMenuEdit(){
    $setMenuEdit = "
    <div id='header-nav-right'>
    <div class='hide' id='popover-search'>
      <div class='pad5A '>
          <div class='input-group'>
              <input type='text' class='form-control' id='filterCari' name='filterCari' onkeyup=$this->Prefix.setValueFilter(this) placeholder='Cari data'>
              <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
                  <a class='btn btn-primary' >Cari</a>
              </span>
          </div>
      </div>
    </div>


    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '".$_GET['id']."'"));
    $this->idServer = $getDataServer['id'];
    $this->namaRelease = $getDataServer['nama_server'];
    $pageContent = "
    <div id='page-title' >
      <h2>Manage Server $this->namaRelease</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
        <form name='$this->formName' id='$this->formName'>
        <div class='example-box-wrapper'>
            <ul class='list-group list-group-separator row list-group-icons'>
                <li class='col-md-3 active'>
                    <a href='#tabGraph' data-toggle='tab' class='list-group-item'>
                        <i class='glyph-icon icon-dashboard'></i>
                        Graph
                    </a>
                </li>
                <li class='col-md-3'>
                    <a href='#tabSSH' data-toggle='tab' class='list-group-item'>
                        <i class='glyph-icon font-red icon-gear'></i>
                        SSH
                    </a>
                </li>
                <li class='col-md-3'>
                    <a href='#tabDatabase' data-toggle='tab' class='list-group-item'>
                        <i class='glyph-icon font-primary icon-database'></i>
                        Database
                    </a>
                </li>
                <li class='col-md-3'>
                    <a href='#tabBackup' data-toggle='tab' class='list-group-item'>
                        <i class='glyph-icon font-blue-alt icon-folder-open'></i>
                        Backup
                    </a>
                </li>
            </ul>
            <div class='tab-content'>
                <div class='tab-pane fade active in' id='tabGraph'>
                  <div class='row'>
                    ".$this->contentCpuUsage()."
                    ".$this->contentMemoryUsage()."
                  </div>
                  <div class='row'>
                    ".$this->contentDiskSpace()."
                    ".$this->contentServerInfo($_GET['id'])."
                  </div>
                </div>
                <div class='tab-pane fade' id='tabSSH'>
                    ".$this->contentSSH($_GET['id'])."
                </div>
                <div class='tab-pane fade' id='tabDatabase'>
                  <p>
                    ".$this->contentDatabase($_GET['id'])."</p>
                </div>
                <div class='tab-pane fade' id='tabBackup'>
                      ".$this->contentBackup($_GET['id'])."
                </div>
            </div>
        </div>

        </form>
        <input type='hidden' value='".$_GET['id']."' id='idServer' name='idServer'>
        <div id='tempatModal'> </div>
      </div>
    </div>

    ";
    return $pageContent;
  }
  function contentCpuUsage(){
    return
    "<div class='col-md-6'>
        <div class='panel'>
            <div class='panel-body'>
                <h3 class='title-hero'>
                CPU USAGE
                </h3>
                <div class='example-box-wrapper'>
                    <div id='cpuUsage' style='width: 100%; height: 344px;'></div>
                </div>
            </div>
        </div>
    </div>";
  }
  function contentMemoryUsage(){
    return
    "<div class='col-md-6'>
        <div class='panel'>
            <div class='panel-body'>
                <h3 class='title-hero'>
                Memory Usage
                </h3>
                <div class='example-box-wrapper'>
                    <div id='memoryUsage' style='width: 100%; height: 344px;'></div>
                </div>
            </div>
        </div>
    </div>";
  }
  function contentDiskSpace(){
    return
    "<div class='col-md-6'>
        <div class='panel'>
            <div class='panel-body'>
                <h3 class='title-hero'>
                Disk Space
                </h3>
                <div class='example-box-wrapper'>
                    <div id='diskSpace' style='width: 100%; height: 344px;'></div>
                </div>
            </div>
        </div>
    </div>";
  }
  function setKolomHeader(){
    $kolomHeader = "
    <thead>
      <tr>
          <th>No</th>
          <th>USERNAME</th>
          <th>HOME DIR</th>
          <th>UID</th>
          <th>GROUP</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function contentSSH($idServer){
    $explodeUser = explode("\n",$this->listUser($idServer));
    $no = 1;
    for ($i=0; $i < sizeof($explodeUser); $i++) {
      $arrayUser = explode(":",$explodeUser[$i]);
      $arrayGroupName = explode(" ",$this->groupUser($idServer,$arrayUser[0]));
      $usernameHash = $this->hexEncode($arrayUser[0]);
      $rightClick = "
      <script>
      var menuOption = [
        {
          name: 'Add User',
          img: 'assets/icons/add_user.png',
          title: 'Add User',
          fun: function () {
              $this->Prefix.addUserSSH();
          }
        },
        {
          name: 'Edit User',
          img: 'assets/icons/edit_user.png',
          title: 'Edit User',
          fun: function () {
              $this->Prefix.editUserSSH('$usernameHash');
          }
        },
        {
          name: 'Delete User',
          img: 'assets/icons/delete_user.png',
          title: 'Delete User',
          fun: function () {
              $this->Prefix.deleteUserSSH('$usernameHash');
          }
        }
      ];
      var menuTrgr=$('#$usernameHash');
      menuTrgr.contextMenu(menuOption,{
           triggerOn :'contextmenu',
           mouseClick : 'right'
      });
      </script>
      ";
      $kolomData .= "
      <tr class='$classRow' id ='$usernameHash' >
          <td>$no</td>
          <td>".$arrayUser[0]."</td>
          <td>".$arrayUser[5]."</td>
          <td>".$arrayUser[2]."</td>
          <td>".str_replace("groups=","",$arrayGroupName[2])."</td>
      </tr>
      $rightClick
      ";
      //useradd -u 12345 -g users -d /home/udin1 -s /bin/bash -p $(echo rf09thebye | openssl passwd -1 -stdin) udin1
      $no+=1;
    }
    $htmlTable = "
      <form name='$this->formName' id='$this->formName'>
        <table class='table table-bordered table-striped table-condensed table-hover'>
            ".$this->setKolomHeader()."
            <tbody>
              ".$kolomData."
            </tbody>
        </table>
      </form>
    ";
    return $htmlTable;
  }
  function contentDatabase($idServer){
    $explodeUser = json_decode($this->listUserDatabase($idServer));
    $no = 1;
    for ($i=0; $i < sizeof($explodeUser); $i++) {
      $username = $explodeUser[$i]->username;
      $host = $explodeUser[$i]->host;
      $usernameHash = $this->hexEncode($username);
      $rightClick = "
      <script>
      var menuOption = [
        {
          name: 'Add User',
          img: 'assets/icons/add_user.png',
          title: 'Add User',
          fun: function () {
              $this->Prefix.addUserMysql();
          }
        },
        {
          name: 'Edit User',
          img: 'assets/icons/edit_user.png',
          title: 'Edit User',
          fun: function () {
              $this->Prefix.editUserMysql('$usernameHash');
          }
        },
        {
          name: 'Delete User',
          img: 'assets/icons/delete_user.png',
          title: 'Delete User',
          fun: function () {
              $this->Prefix.deleteUserMysql('$usernameHash');
          }
        }
      ];
      var menuTrgr=$('#$usernameHash');
      menuTrgr.contextMenu(menuOption,{
           triggerOn :'contextmenu',
           mouseClick : 'right'
      });
      </script>
      ";
        $hashPassword = json_decode($this->getPasswordMysql($idServer,$username));
        $kolomData .= "
        <tr class='$classRow' id ='$usernameHash' >
        <td align='center'>$no</td>
        <td>".$username."</td>
        <td>".$this->sqlPasswordDecode($hashPassword[0]->hash)."</td>
        <td>".$host."</td>
        </tr>
        $rightClick
        ";
      $no+=1;
    }
    $htmlTable = "
      <form name='$this->formName' id='$this->formName'>
        <table class='table table-bordered table-striped table-condensed table-hover'>
          <thead>
            <tr>
                <th>No</th>
                <th>USERNAME</th>
                <th>PASSWORD</th>
                <th>HOST</th>
            </tr>
          </thead>
            <tbody>
              ".$kolomData."
            </tbody>
        </table>
      </form>

    ";
    return $htmlTable;
  }
  function contentBackup($idServer){
    $explodeDatabase = json_decode($this->listDatabase($idServer));
    $no = 1;
    for ($i=0; $i < sizeof($explodeDatabase); $i++) {
      $databasename = $explodeDatabase[$i]->databasename;
      $collation = $explodeDatabase[$i]->collation;
      $usernameHash = $this->hexEncode($databasename);
      $rightClick = "
      <script>
      var menuOption = [
        {
          name: 'Backup Database',
          img: 'assets/icons/database_backup.png',
          title: 'Add User',
          fun: function () {
              $this->Prefix.backupDatabase('$usernameHash');
          }
        },
        {
          name: 'Drop Database',
          img: 'assets/icons/drop_database.png',
          title: 'Delete User',
          fun: function () {
              $this->Prefix.dropDatabase('$usernameHash');
          }
        }
      ];
      var menuTrgr=$('#$usernameHash');
      menuTrgr.contextMenu(menuOption,{
           triggerOn :'contextmenu',
           mouseClick : 'right'
      });
      </script>
      ";

        $kolomData .= "
        <tr class='$classRow' id ='$usernameHash' >
        <td align='center'>$no</td>
        <td>".$databasename."</td>
        <td>".$collation."</td>
        </tr>
        $rightClick
        ";
      $no+=1;
    }
    $htmlTable = "
      <form name='$this->formName' id='$this->formName'>
        <table class='table table-bordered table-striped table-condensed table-hover'>
          <thead>
            <tr>
                <th>No</th>
                <th>DATABASENAME</th>
                <th>COLLATION</th>
            </tr>
          </thead>
            <tbody>
              ".$kolomData."
            </tbody>
        </table>
      </form>

    ";
    return $htmlTable;
  }
  function contentServerInfo($idServer){
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$idServer'"));
    return
    "<div class='col-md-6'>
        <div class='panel'>
            <div class='panel-body'>
                <h3 class='title-hero'>
                Server Info
                </h3>
                <div class='example-box-wrapper'>
                    <div class='row'>
                        <div class='col-md-4'>SERVER NAME</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".$getDataServer['nama_server']."</div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>IP</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".$getDataServer['alamat_ip']."</div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>PROCESSOR</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".str_replace('MODELNAME="','',$this->getProcessor($idServer))."</div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>OPERATION SYSTEM</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".str_replace('\n','',$this->getOperationSystem($idServer))."</div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>RAM</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".$this->getRamSize($idServer)."</div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>DISK SIZE</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".$this->getDiskSize($idServer)."</div>
                    </div>
                    <div class='row'>
                        <div class='col-md-4'>KERNEL</div>
                        <div class='col-md-1'>:</div>
                        <div class='col-md-7'>".$this->getKernel($idServer)."</div>
                    </div>
                </div>
            </div>
        </div>
    </div>";
  }

  function cpuUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'");
  }
  function listUser($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"cat /etc/passwd");
  }
  function getDataUser($id,$usernameSSH) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"cat /etc/passwd | grep $usernameSSH");
  }
  function groupUser($id,$username) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"id $username");
  }
  function getProcessor($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $modelName = '"MODELNAME=\"';
    $kutip = '"';
    $modelNameKutip = '"model name"';
    return $this->sshCommand($sshConnection,"echo -n $modelName $kutip
    grep -m 1 $modelNameKutip /proc/cpuinfo | cut -d: -f2 | sed -e 's/^ *//' | sed -e 's/$/$kutip/'");
  }
  function getOperationSystem($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"cat /etc/issue");
  }
  function getRamSize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->numberFormat($this->sshCommand($sshConnection,"echo $(awk '/^MemTotal:/{print $2}' /proc/meminfo)"))." Byte";
  }
  function getDiskSize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    // return $this->numberFormat(str_replace("(","",$this->sshCommand($sshConnection,"dmesg | grep blocks | grep GB | grep -o -P '(?<=blocks:).*(?=GB)'")))." GB";
    return $this->numberFormat($this->sshCommand($sshConnection,"blockdev --getsize64 /dev/sda"))." Byte";
  }
  function getKernel($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"uname -a");
  }
  function memoryUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $memUsage = '"%u%%"';
    return $this->sshCommand($sshConnection,"awk '/^Mem/ {printf($memUsage, 100*$3/$2);}' <(free -m)");
  }
  function saveNewUserSSH($id,$idUser,$usernameSSH,$passwordSSH,$homeDirUser) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"useradd -u $idUser -g users -d $homeDirUser -s /bin/bash -p $(echo $passwordSSH | openssl passwd -1 -stdin) $usernameSSH");
  }
  function saveNewUserMysql($id,$usernameMysql,$passwordMysql) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $queryAddUser = "GRANT ALL PRIVILEGES ON *.* TO $usernameMysql@localhost IDENTIFIED BY '$passwordMysql';";
    return $this->sshCommand($sshConnection,'mysql -u'.$usernameDatabase.' -p'.$passwordDatabase.' -s -e "'.$queryAddUser.'" ');
  }
  function changePasswordMysql($id,$usernameMysql,$passwordMysql) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $queryAddUser = "ALTER USER $usernameMysql@localhost IDENTIFIED BY '$passwordMysql';";
    return $this->sshCommand($sshConnection,'mysql -u'.$usernameDatabase.' -p'.$passwordDatabase.' -s -e "'.$queryAddUser.'" ');
  }
  function saveBackUp($id,$databaseName,$backupLocation,$parameter) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $namaAlias = $getDataServer['alias'];
    $this->sshCommand($sshConnection,"mkdir $backupLocation");
    $fileName = date("Y-m-d")."-".date("H:i")."-".$namaAlias."-".$databaseName;
    $dumpStruktur = "mysqldump -u$usernameDatabase -p$passwordDatabase -f --no-data --skip-events --skip-routines --skip-triggers $databaseName | gzip > $backupLocation/$fileName.struk.sql.gz";
    $dumpTriger = "mysqldump -u$usernameDatabase -p$passwordDatabase -f --routines --triggers --no-create-info --no-data --no-create-db --skip-opt $databaseName | gzip > $backupLocation/$fileName.triger.sql.gz";
    $dumpData = "mysqldump -u$usernameDatabase -p$passwordDatabase --complete-insert --no-create-db --no-create-info --skip-events --skip-routines --skip-triggers $databaseName | gzip > $backupLocation/$fileName.data.sql.gz";
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '".$parameter['idRelease']."'"));
    $gzRelease = "zip -r  $backupLocation/$fileName.zip ".$getDataRelease['directory_location']."  ";
    if(!empty($parameter['struktur'])){
      $this->sshCommand($sshConnection,$dumpStruktur);
      $locationStruktur = $backupLocation."/".$fileName.".struk.sql.gz";
    }
    if(!empty($parameter['isi'])){
      $this->sshCommand($sshConnection,$dumpData);
      $locationData = $backupLocation."/".$fileName.".data.sql.gz";
    }
    if(!empty($parameter['triger'])){
      $this->sshCommand($sshConnection,$dumpTriger);
      $locationTriger = $backupLocation."/".$fileName.".triger.sql.gz";
    }
    if(!empty($parameter['idRelease'])){
      $this->sshCommand($sshConnection,$gzRelease);
      $releaseFile = $backupLocation."/".$fileName.".zip";
    }
    $arrayLocation = array(
        'struktur' => $locationStruktur,
        'data'=> $locationData,
        'triger'=> $locationTriger,
        'release'=> $releaseFile,
    );
    $dataHistoryBackup = array(
        'tanggal' => date("Y-m-d"),
        'jam' => date("H:i"),
        'id_server' => $id,
        'nama_database' => $databaseName,
        'file_backup' => json_encode($arrayLocation)
    );
    $query = $this->sqlInsert("history_backup", $dataHistoryBackup);
    $this->sqlQuery($query);
    return $gzRelease;
  }
  function dropDatabase($id,$databaseName) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $namaAlias = $getDataServer['alias'];
    $dropDatabase = "mysql -u$usernameDatabase -p$passwordDatabase -s -e 'drop DATABASE $databaseName;'";
    $this->sshCommand($sshConnection,$dropDatabase);
    return $dropDatabase;
  }
  function deleteUserMysql($id,$usernameMysql,$passwordMysql) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $queryAddUser = "DROP USER $usernameMysql@localhost;";
    return $this->sshCommand($sshConnection,'mysql -u'.$usernameDatabase.' -p'.$passwordDatabase.' -s -e "'.$queryAddUser.'" ');
  }
  function saveEditUser($id,$idUser,$usernameSSH,$passwordSSH,$homeDirUser) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"usermod -m -d $homeDirUser $usernameSSH");
  }
  function changePasswordSSH($id,$usernameSSH,$passwordSSH) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,'echo -e "'.$passwordSSH.'\n'.$passwordSSH.'" | passwd '.$usernameSSH.'');
  }
  function deleteUserSSH($id,$usernameSSH) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"deluser $usernameSSH");
  }
  function diskSpace($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $diskUsg = '"%s"';
    $varNF = "$"."NF";
    $bagi = '"/"';
    return $this->sshCommand($sshConnection,"df -h | awk '$varNF==$bagi{printf $diskUsg, $5}'");
  }
  function listUserDatabase($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"username\":\""$1"\",", "\"host\":\""$2"\"} "}';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$userMysql." -p".$databasePassword." -s -e 'select user,host from mysql.user;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function getPasswordMysql($id,$username) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"username\":\""$1"\",", "\"hash\":\""$2"\"} "}';
    $whereClause = '"'.$username.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$userMysql." -p".$databasePassword." -s -e 'select User, authentication_string from mysql.user where User = $whereClause;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
    // return "mysql -u".$userMysql." -p".$databasePassword." -s -e 'select User, authentication_string from mysql.user where User = $whereClause;' | awk '$contentAWK'";
  }
  function listDatabase($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"databasename\":\""$1"\",", "\"collation\":\""$2"\"} "}';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$userMysql." -p".$databasePassword." -s -e 'select SCHEMA_NAME,DEFAULT_COLLATION_NAME from information_schema.SCHEMATA;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listFileServer($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"databasename\":\""$1"\",", "\"collation\":\""$2"\"} "}';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection," | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listFileLocal($id,$idSumber) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $getDataSumber = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$idSumber'"));
    $dirSumber = $getDataSumber['directory'];
    $contentAWK = '{ print "{\"file\":\""$5"\",\"tanggal\":\""$3"-"$4"\",\"size\":\""$2"\"} "}';
    $prinst = '"%-.22T+ %M %n %-8u %-8g %8s %Tx %.8TX %p\n"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"find $dirSumber -type f -printf $prinst | sort | cut -f 8- -d ' ' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }

  function addUserSSH($idServer){
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalUserSSH' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>User Baru</h4>
                </div>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                          <label class='col-sm-2 control-label' style='margin-top:6px;'>ID</label>
                          <div class='col-sm-10'>
                              <input type='text' class='form-control'  placeholder='ID' id='idSSH' name='idSSH'>
                          </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>USERNAME</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='USERNAME' id='usernameSSH' name='usernameSSH'>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>PASSWORD</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='PASSWORD' id='passwordSSH' name='passwordSSH'>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>HOME DIRECTORY</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='HOME DIRECTORY' id='homeDirectory' name='homeDirectory'>
                      </div>
                    </div>
                  </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveNewUserSSH();>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
    </div>";
    return $content;
  }
  function addUserMysql($idServer){
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalUserMysql' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>User Baru</h4>
                </div>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>USERNAME</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='USERNAME' id='usernameMysql' name='usernameMysql'>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>PASSWORD</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='PASSWORD' id='passwordMysql' name='passwordMysql'>
                      </div>
                    </div>
                  </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveNewUserMysql();>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
    </div>";
    return $content;
  }
  function editUserSSH($idServer,$usernameSSH){
    $getDataUser = $this->getDataUser($idServer,$usernameSSH);
    $arrayUser = explode(":",$getDataUser);
    $idSSH = $arrayUser[0];
    $homeDirectory = $arrayUser[5];
    $content="

    <div class='modal fade bs-example-modal-lg' id='modalUserSSH' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>Edit User</h4>
                </div>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                          <label class='col-sm-2 control-label' style='margin-top:6px;'>ID</label>
                          <div class='col-sm-10'>
                              <input type='text' class='form-control'  placeholder='ID' id='idSSH' name='idSSH' value='$idSSH' readonly>
                          </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>USERNAME</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='USERNAME' id='usernameSSH' name='usernameSSH' value='$usernameSSH' readonly>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>PASSWORD</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='PASSWORD' id='passwordSSH' name='passwordSSH'>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>HOME DIRECTORY</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='HOME DIRECTORY' id='homeDirectory' name='homeDirectory' value='$homeDirectory'>
                      </div>
                    </div>
                  </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveEditUser('$usernameSSH');>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
    </div>";
    return $content;
  }
  function editUserMysql($idServer,$usernameMysql){
    $hashPassword = json_decode($this->getPasswordMysql($idServer,$usernameMysql));
    $passwordMysql = $this->sqlPasswordDecode($hashPassword[0]->hash);
    $content="

    <div class='modal fade bs-example-modal-lg' id='modalUserMysql' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>Edit User</h4>
                </div>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>USERNAME</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='USERNAME' id='usernameMysql' name='usernameMysql' value='$usernameMysql' readonly>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>PASSWORD</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='PASSWORD' id='passwordMysql' name='passwordMysql' value='$passwordMysql'>
                      </div>
                    </div>
                  </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveEditUserMysql('$usernameMysql');>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
    </div>";
    return $content;
  }
  function backupDatabase($idServer,$databaseName){
    $comboHardDisk = $this->cmbQuery("idDisk","","select id,nama_harddisk from ref_disk where id_server = '$idServer'","onchange=$this->Prefix.diskChanged() class='form-control'","-- HARD DISK --");
    $comboRelease = $this->cmbQuery("idRelease","","select id,nama_release from ref_release ","onchange=$this->Prefix.diskChanged() class='form-control'","-- FILE RELEASE --");
    $content="

    <div class='modal fade bs-example-modal-lg' id='modalBackup' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>Backup Database</h4>
                </div>
                <form name='optionBackup' id='optionBackup'>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>DATABASE NAME</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='USERNAME' id='databaseName' name='databaseName' value='$databaseName' readonly>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                        <label class='col-sm-2 control-label' style='margin-top:6px;'>Option</label>
                        <div class='col-sm-2'>
                          <input type='checkbox' name='strukturDatabase' id='strukturDatabase' value='strukturDatabase' checked> Struktur
                        </div>
                        <div class='col-sm-2'>
                          <input type='checkbox' name='trigerDatabase' id='trigerDatabase' value='trigerDatabase' checked> Triger
                        </div>
                        <div class='col-sm-2'>
                          <input type='checkbox' name='isiDatabase' id='isiDatabase' value='isiDatabase' checked> Isi Database
                        </div>

                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>File Release</label>
                      <div class='col-sm-10'>
                        $comboRelease
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>Backup Location</label>
                      <div class='col-sm-10'>
                        $comboHardDisk
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                        <label class='col-sm-2 control-label' style='margin-top:6px;'>Disk Info</label>
                        <div class='col-sm-3'>
                          <div class='input-group'>
                              <span class='input-group-addon btn-primary' style='background: #1094e2; border: 1px solid #0b74b3;'>
                                  <i class='glyph-icon icon-inbox'></i>
                              </span>
                              <input type='text' class='form-control' id='hardDiskSize' readonly>
                          </div>
                        </div>
                        <div class='col-sm-3'>
                          <div class='input-group'>
                              <span class='input-group-addon btn-primary' style='background: #e65166;border: 1px solid #cc4356;'>
                                  <i class='glyph-icon icon-inbox'></i>
                              </span>
                              <input type='text' class='form-control' id='hardDiskUsed' readonly>
                          </div>
                        </div>
                        <div class='col-sm-3'>
                          <div class='input-group'>
                              <span class='input-group-addon btn-primary'>
                                  <i class='glyph-icon icon-inbox'></i>
                              </span>
                              <input type='text' class='form-control' id='hardDiskFree' readonly>
                              <input type='hidden' class='form-control' id='backupLocation' readonly>
                          </div>
                        </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>Storage Status</label>
                      <div class='col-sm-10'>
                        <div class='progressbar' data-value='0'>
                            <div class='progressbar-value bg-primary' id='persentase' style='width: 0%'>
                                <div class='progress-label'>0%</div>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                </form>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveBackUp('$databaseName');>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
    </div>";
    return $content;
  }
  function renameDatabase($idServer,$databaseName){
    $backupLocation = "/var/www/backup";
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalBackup' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>Backup Database</h4>
                </div>
                <form name='optionBackup' id='optionBackup'>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>DATABASE NAME</label>
                      <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='DATABASE NAME' id='databaseNameRenamed' name='databaseNameRenamed' value='$databaseName'>
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-2 control-label' style='margin-top:6px;'>COLLATION</label>
                      <div class='col-sm-10'>
                        <input type='text' name='collationDatabase' id='collationDatabase' value='$collationDatabase'  class='form-control'>
                      </div>
                    </div>
                  </div>
                </div>
                </form>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveRenameDatabase('$databaseName');>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
    </div>";
    return $content;
  }

  function sqlPasswordEncode($passwordMysql){
      $encodePassword = $this->sqlArray($this->sqlQuery("select Password('$passwordMysql')"));
      $arrayPassword = array('password' => $passwordMysql,'hash' => $encodePassword["Password('$passwordMysql')"] );
      $query = $this->sqlInsert("mysql_password",$arrayPassword);
      if($this->sqlRowCount($this->sqlQuery("select * from mysql_password where password = '$passwordMysql'")) ==0){
          $this->sqlQuery($query);
      }
      return $query;
  }
  function sqlPasswordDecode($hashMysql){
      $getPassword = $this->sqlArray($this->sqlQuery("select * from mysql_password where hash = '$hashMysql'"));
      return $getPassword['password'];
  }

}
$manageServer = new manageServer();



 ?>
