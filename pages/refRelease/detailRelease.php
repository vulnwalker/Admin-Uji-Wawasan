<?php
class detailRelease extends baseObject{
  var $Prefix = "detailRelease";
  var $formName = "detailReleaseForm";
  var $tableName = "ref_server";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'Release':{

        for ($i=0; $i < sizeof($detailRelease_cb); $i++) {
            $explodeSelected = explode(";",$detailRelease_cb[$i]);
            $arrayPush[] = array(
              'nama'=>$explodeSelected[0],
              'type'=>$explodeSelected[1],
            );
        }
        $getData = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
        $formCaption = "Push Release To Server";
        $comboRelease = $this->cmbQuery("cmbRelease","","select id,nama_release from ref_release","onchange=$this->Prefix.releaseChanged() class='form-control'","-- RELEASE --");
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalRelease' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
          <form name='".$this->formName."_release' id='".$this->formName."_release'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>$formCaption</h4>
                    </div>
                    <div class='modal-body'>

                      <div class='form-group'>
                        <div class='row'>
                        <label class='col-sm-3 control-label'>Server Tujuan</label>
                          <div class='col-sm-9'>
                              ".$this->cmbQueryEmpty("listServer[]",$listServer,"select id,nama_server from ref_server","multiple class='multi-select'")."
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Release</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' name='namaRelease' id='namaRelease' value='".$getData['nama_release']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Database</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' value='".$getData['nama_database']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Sql File</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' value='".$getData['mysql_file']."' readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <input type='hidden' name='listDataPush' id='listDataPush' value='".json_encode($arrayPush)."'>
                        <input type='hidden' name='dirLocation' id='dirLocation' value='".$currentLocation."'>
                        <input type='hidden' name='locationRelease' id='locationRelease' value='".$getData['directory_location']."'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.executeRelease();>Push</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
          </form>
        </div>";
  		break;
  		}
      case 'pushAndExecute':{
        $getData = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
        $formCaption = "Push And Execute Sql";
        $comboRelease = $this->cmbQuery("cmbRelease","","select id,nama_release from ref_release","onchange=$this->Prefix.releaseChanged() class='form-control'","-- RELEASE --");
        $sqlFile = $this->hexDecode($namaSql);
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalRelease' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
          <form name='".$this->formName."_release' id='".$this->formName."_release'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>$formCaption</h4>
                    </div>
                    <div class='modal-body'>

                      <div class='form-group'>
                        <div class='row'>
                        <label class='col-sm-3 control-label'>Server Tujuan</label>
                          <div class='col-sm-9'>
                              ".$this->cmbQueryEmpty("listServer[]",$listServer,"select id,nama_server from ref_server","multiple class='multi-select'")."
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Release</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' name='namaRelease' id='namaRelease' value='".$getData['nama_release']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Database</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' value='".$getData['nama_database']."' readonly>
                          </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Sql File</label>
                          <div class='col-sm-9'>
                            <input type='text' class='form-control' value='".$getData['directory_location']."/".$currentLocation."/".$sqlFile."' readonly>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <input type='hidden' name='listDataPush' id='listDataPush' value='$sqlFile'>
                        <input type='hidden' name='dirLocation' id='dirLocation' value='".$currentLocation."'>
                        <input type='hidden' name='locationRelease' id='locationRelease' value='".$getData['directory_location']."'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.sendSqlFile();>Push</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
          </form>
        </div>";
  		break;
  		}
      case 'formNewFolder':{
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalFolder' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>Folder Baru</h4>
                    </div>
                    <div class='modal-body'>
                      <div class='row'>
                          <label class='col-sm-2 control-label' style='margin-top:6px;'>Nama Folder</label>
                          <div class='col-sm-10'>
                              <input type='text' class='form-control'  placeholder='Nama Folder' id='namaFolder' name='namaFolder'>
                          </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveNewDir();>Simpan</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
        </div>";
  		break;
  		}
      case 'formRenameFolder':{
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalFolder' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>Folder Baru</h4>
                    </div>
                    <div class='modal-body'>
                      <div class='row'>
                          <label class='col-sm-2 control-label' style='margin-top:6px;'>Nama Folder</label>
                          <div class='col-sm-10'>
                              <input type='text' class='form-control'  placeholder='Nama Folder' id='namaFolder' name='namaFolder' value='$namaFolder'>
                              <input type='hidden' class='form-control' id='hiddenNamaFolder' name='hiddenNamaFolder' value='$namaFolder'>
                          </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveRenameFolder();>Simpan</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
        </div>";
  		break;
  		}
      case 'executeRelease':{
        if(sizeof($listServer) == 0){
          $err = "Pilih Server Tujuan";
        }
        $content = array('urutanKe' =>  1);
  		break;
  		}
      case 'sendSqlFile':{
        if(sizeof($listServer) == 0){
          $err = "Pilih Server Tujuan";
        }
        $content = array('urutanKe' =>  1);
  		break;
  		}
      case 'setAsDatabaseFile':{
        $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
        $databaseFile = $this->hexDecode($databaseFile);
        if(!empty($currentLocation)){
            $sqlLocation = $getDataRelease['directory_location']."/".$currentLocation."/".$databaseFile;
        }else{
            $sqlLocation = $getDataRelease['directory_location']."/".$currentLocation."/".$databaseFile;
        }
        $data = array('mysql_file' => str_replace("//","/",$sqlLocation));
        $query = $this->sqlUpdate("ref_release",$data,"id = '$idRelease'");
        $this->sqlQuery($query);
        $cek = $query;
  		break;
  		}
      case 'pushRelease':{
            $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '".$listServer[$urutanKe - 1]."'"));
            $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
            $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
            $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
            $arrayData = json_decode($listDataPush);
            for ($f=0; $f < sizeof($arrayData) ; $f++) {
                if($arrayData[$f]->type == 'file'){
                  $cek = "tercopy file ".$getDataRelease['directory_location']."/".$dirLocation."/".$arrayData[$f]->nama;
                  ssh2_scp_send($sshConnection,$getDataRelease['directory_location']."/".$dirLocation."/".$arrayData[$f]->nama,$getDataRelease['directory_location']."/".$dirLocation."/".$arrayData[$f]->nama);
                }elseif($arrayData[$f]->type == 'dir'){
                  $this->xSFTP($getDataRelease['directory_location']."/".$dirLocation."/".$arrayData[$f]->nama,$getDataRelease['directory_location']."/".$dirLocation."/".$arrayData[$f]->nama,$sshConnection);
                }
            }
            if($urutanKe == sizeof($listServer)){
              $status = "OK";
            }
            $content = array('urutanKe' => $urutanKe + 1, 'status' => $status);
  		break;
  		}
      case 'executeSqlFile':{
            $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '".$listServer[$urutanKe - 1]."'"));
            $databasePassword = $getDataServer["password_mysql"];
            $userMysql = $getDataServer["user_mysql"];
            $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
            $databaseName = $getDataRelease['nama_database'];
            $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
            $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
            ssh2_scp_send($sshConnection,$getDataRelease['directory_location']."/".$dirLocation."/".$listDataPush,$getDataRelease['directory_location']."/".$dirLocation."/".$listDataPush);
            $createDatabase = "mysql -u".$userMysql." -p".$databasePassword." -e 'CREATE DATABASE ".$databaseName." CHARACTER SET latin1 COLLATE latin1_general_ci '";
            $this->sshCommand($sshConnection,$createDatabase);
            $sqlLocation = $getDataRelease['directory_location']."/".$dirLocation."/".$listDataPush;
            $executeSqlFile = "mysql -u".$userMysql." -p".$databasePassword." $databaseName < $sqlLocation";
            $this->sshCommand($sshConnection,$executeSqlFile);
            if($urutanKe == sizeof($listServer)){
              $status = "OK";
            }
            $cek = $executeSqlFile;
            $content = array('urutanKe' => $urutanKe + 1, 'status' => $status);
  		break;
  		}
      case 'refreshList':{

        $content=array('tableContent' => $this->generateTable($currentLocation));

  		break;
  		}
      case 'releaseChanged':{
        $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$cmbRelease'"));
        $content = array('folderRelease' => $getDataRelease['directory_location']);
  		break;
  		}
      case 'Edit':{
        $explodeSelected = explode(';',$detailRelease_cb[0]);
        $namaFile = $explodeSelected[0];
        if($explodeSelected[1] == 'dir')$err = "Folder tidak dapat diubah";
        $content = array("namaFile" => $namaFile);
  		break;
  		}
      case 'saveNew':{
  			if(empty($fileName)){
          $err = "Isi Nama File";
        }
        if(empty($err)){
          file_put_contents($location."/".$fileName,$isiFile);
        }
  		break;
  		}
      case 'saveNewDir':{
  			if(empty($namaFolder)){
          $err = "Isi Nama Folder";
        }elseif (is_dir($location."/".$namaFolder)) {
            $err = "Folder sudah ada";
        }
        if(empty($err)){
            mkdir($location."/".$namaFolder);
        }
  		break;
  		}
      case 'saveRenameFolder':{
  			if(empty($namaFolder)){
          $err = "Isi Nama Folder";
        }
        if(empty($err)){
            rename($location."/".$hiddenNamaFolder,$location."/".$namaFolder);
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($fileName)){
          $err = "Isi Nama File";
        }
        if(empty($err)){
          if($hiddenNamaFile != $fileName){
            unlink($location."/".$hiddenNamaFile);
          }
          file_put_contents($location."/".$fileName,$isiFile);
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($detailRelease_cb) ; $i++) {
          $explodeSelected = explode(';',$detailRelease_cb[$i]);
          if($explodeSelected[1] == 'file'){
            unlink($currentLocation."/".$explodeSelected[0]);
          }else{
            $this->unlinkDir($currentLocation."/".$explodeSelected[0]);
          }

        }
        $cek = $query;
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
    if(!isset($_GET['API'])){
        if(empty($_GET['action'])){
          echo $this->pageShow();
        }else{
          if($_GET['action'] == 'new'){
            echo $this->pageShowNew();
          }else{
            echo $this->pageShowEdit($_GET['idEdit']);
          }
        }
    }else{
       echo $this->jsonGenerator();
    }
  }
  function loadScript(){
    return "

    <script type='text/javascript' src='js/refRelease/detailRelease.js'></script>
    <script type='text/javascript' src='assets/widgets/multi-select/multiselect.js'></script>
    <script src='plugins/contextMenu/contextMenu.min.js'></script>
    <link rel='stylesheet' type='text/css' href='plugins/contextMenu/contextMenu.min.css'>
    <script type='text/javascript'>
      $( document ).ready(function() {
        detailRelease.refreshList();
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
      <li >
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
          <a href='pages.php?page=fileManager' title='detailRelease'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>File Manager</span>
          </a>
      </li>
      <li class='activeSidebar'>
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
    <a class='header-btn' style='cursor:pointer;'  onclick=$this->Prefix.Release(); title='Push To Release'>
      <i class='glyph-icon icon-linecons-attach'></i>
    </a>

    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '".$_GET['id']."'"));
    $this->idRelease = $getDataRelease['id'];
    $this->namaRelease = $getDataRelease['nama_release'];
    $this->locationRelease = $getDataRelease['directory_location'];
    $pageContent = "
    <div id='page-title' >
      <h2>Detail Release $this->namaRelease</h2>
      <input type='hidden' value='".$_GET['id']."' id='idRelease' name='idRelease'>
    </div>
    <div class='panel'>
      <div class='panel-body'>
        <form name='$this->formName' id='$this->formName'>
              ".$this->generateTable()."

        </form>
      </div>
      <div id='tempatModal'> </div>
    </div>

    ";
    return $pageContent;
  }

  function setKolomHeader(){

    $kolomHeader = "
    <thead>
      <tr id='rowHeader'>
          <th style='text-align:center;'>".$this->checkAll(1000,$this->Prefix)."</th>
          <th>Nama</th>
          <th>Type</th>
          <th>Size</th>
          <th>Modified</th>
          <th>Owner</th>
          <th>Permisson</th>
      </tr>
      $rightClick
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
       $$key = $value;
    }
    if($jenisDir == '.' || $jenisDir=='..'){
      $checkBox = "<td style='text-align:center;'></td>";
    }else{
      $checkBox = "<td style='text-align:center;'>".$this->setCekBoxFile($no - 3,"$jenisDir;$typeFile",$this->Prefix)."</td>";
    }
    if($typeFile == 'dir'){
      $action = "onclick=$this->Prefix.refreshList('$dirLocation');";
    }
    if($jenisDir != '.'){
      if($jenisDir !='..' ){

      }
      if($typeFile == 'dir'){
        $tableRow = "
        <tr id='$jenisDir'>
        $checkBox
        <td><span style='cursor:pointer;'   $action>".$namaFile."</span>

        </td>
        <td>".$typeFile."</td>
        <td>".$sizeFile."</td>
        <td>".$last_modified."</td>
        <td>".$owner."</td>
        <td>".$permission."</td>
        </tr>
        ";
      }else{
        $tableRow = "
        <tr id='$jenisDir'>
          $checkBox
          <td><span style='cursor:pointer;' $action>".$namaFile."</span>
          </td>
          <td>".$typeFile."</td>
          <td>".$sizeFile."</td>
          <td>".$last_modified."</td>
          <td>".$owner."</td>
          <td>".$permission."</td>
        </tr>
        ";
        $explodeExtension = substr($jenisDir, -3);;
        if($explodeExtension == "sql"){
          $idSql = $this->hexEncode($jenisDir);
          $rightClick = "
          <script>
          var menuOption = [
            {
              name: 'Set As Database File',
              img: 'assets/icons/database.png',
              title: 'Se As Database File',
              fun: function () {
                  $this->Prefix.setAsDatabaseFile('$idSql');
              }
            },
            {
              name: 'Push And Execute Sql File',
              img: 'assets/icons/rename_database.png',
              title: 'Push And Execute Sql File',
              fun: function () {
                  $this->Prefix.pushAndExecute('$idSql');
              }
            }
          ];
          var menuTrgr=$('#$idSql');
          menuTrgr.contextMenu(menuOption,{
               triggerOn :'contextmenu',
               mouseClick : 'right'
          });
          </script>
          ";
          $tableRow = "
          <tr id='$idSql'>
            $checkBox
            <td><span style='cursor:pointer;' $action>".$namaFile."</span>
            </td>
            <td>".$typeFile."</td>
            <td>".$sizeFile."</td>
            <td>".$last_modified."</td>
            <td>".$owner."</td>
            <td>".$permission."</td>
          </tr>
          $rightClick
          ";
        }
      }
    }
    return $tableRow;
  }
  function generateTable($locationDirectory){
    $no = 1;
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '".$_POST['idRelease']."'"));
    // $this->idRelease = $getDataRelease['id'];
    // $this->namaRelease = $getDataRelease['nama_release'];
    // $this->locationRelease = $getDataRelease['directory_location'];
    $locationDirectory = str_replace('//','/',$locationDirectory);
    $this->locationRelease =$getDataRelease['directory_location'];
    if(empty($locationDirectory)){
      $dir = $this->locationRelease;
    }else{
      $dir = $this->locationRelease.$locationDirectory;
    }
    $scandir = scandir($dir);
    foreach($scandir as $dirx) {
      $dtype = filetype("$dir/$dirx");
      $dtime = date("F d Y g:i:s", filemtime("$dir/$dirx"));
      if(function_exists('posix_getpwuid')) {
        $downer = @posix_getpwuid(fileowner("$dir/$dirx"));
        $downer = $downer['name'];
      } else {
        //$downer = $uid;
        $downer = fileowner("$dir/$dirx");
      }
      if(function_exists('posix_getgrgid')) {
        $dgrp = @posix_getgrgid(filegroup("$dir/$dirx"));
        $dgrp = $dgrp['name'];
      } else {
        $dgrp = filegroup("$dir/$dirx");
      }
      if(!is_dir("$dir/$dirx")) continue;
      if($dirx === '..') {
        $explodeSlash = explode("/",$locationDirectory);
        if(sizeof($explodeSlash) == 2 || $locationDirectory == ''){
          $href = "/";
        }else{
          $href = "".dirname($dir)."";
        }

      } elseif($dirx === '.') {
        $href = "$dirx";
      } else {
        $href = "$dir/$dirx";
      }
      $arrayFile = array(
        'dirLocation' => str_replace($this->locationRelease,'',$href),
        'jenisDir' => $dirx,
        'namaFile' => "<img src='data:image/png;base64,R0lGODlhEwAQALMAAAAAAP///5ycAM7OY///nP//zv/OnPf39////wAAAAAAAAAAAAAAAAAAAAAA"."AAAAACH5BAEAAAgALAAAAAATABAAAARREMlJq7046yp6BxsiHEVBEAKYCUPrDp7HlXRdEoMqCebp"."/4YchffzGQhH4YRYPB2DOlHPiKwqd1Pq8yrVVg3QYeH5RYK5rJfaFUUA3vB4fBIBADs='>$dirx",
        'typeFile' => $dtype,
        'sizeFile' => "-",
        'last_modified' => $dtime,
        'owner' => $downer,
        'permission' => $this->w("$dir/$dirx",$this->perms("$dir/$dirx")),
      );
      $kolomData.=$this->setKolomData($no,$arrayFile);
      $no++;
    }
    foreach($scandir as $file) {
			$ftype = filetype("$dir/$file");
			$ftime = date("F d Y g:i:s", filemtime("$dir/$file"));
			$size = filesize("$dir/$file")/1024;
			$size = round($size,3);
			if(function_exists('posix_getpwuid')) {
				$fowner = @posix_getpwuid(fileowner("$dir/$file"));
				$fowner = $fowner['name'];
			} else {
				//$downer = $uid;
				$fowner = fileowner("$dir/$file");
			}
			if(function_exists('posix_getgrgid')) {
				$fgrp = @posix_getgrgid(filegroup("$dir/$file"));
				$fgrp = $fgrp['name'];
			} else {
				$fgrp = filegroup("$dir/$file");
			}
			if($size > 1024) {
				$size = round($size/1024,2). 'MB';
			} else {
				$size = $size. 'KB';
			}
			if(!is_file("$dir/$file")) continue;
      $arrayFile = array(
        'jenisDir' => $file,
        'namaFile' => "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9oJBhcTJv2B2d4AAAJMSURBVDjLbZO9ThxZEIW/qlvdtM38BNgJQmQgJGd+A/MQBLwGjiwH3nwdkSLtO2xERG5LqxXRSIR2YDfD4GkGM0P3rb4b9PAz0l7pSlWlW0fnnLolAIPB4PXh4eFunucAIILwdESeZyAifnp6+u9oNLo3gM3NzTdHR+//zvJMzSyJKKodiIg8AXaxeIz1bDZ7MxqNftgSURDWy7LUnZ0dYmxAFAVElI6AECygIsQQsizLBOABADOjKApqh7u7GoCUWiwYbetoUHrrPcwCqoF2KUeXLzEzBv0+uQmSHMEZ9F6SZcr6i4IsBOa/b7HQMaHtIAwgLdHalDA1ev0eQbSjrErQwJpqF4eAx/hoqD132mMkJri5uSOlFhEhpUQIiojwamODNsljfUWCqpLnOaaCSKJtnaBCsZYjAllmXI4vaeoaVX0cbSdhmUR3zAKvNjY6Vioo0tWzgEonKbW+KkGWt3Unt0CeGfJs9g+UU0rEGHH/Hw/MjH6/T+POdFoRNKChM22xmOPespjPGQ6HpNQ27t6sACDSNanyoljDLEdVaFOLe8ZkUjK5ukq3t79lPC7/ODk5Ga+Y6O5MqymNw3V1y3hyzfX0hqvJLybXFd++f2d3d0dms+qvg4ODz8fHx0/Lsbe3964sS7+4uEjunpqmSe6e3D3N5/N0WZbtly9f09nZ2Z/b29v2fLEevvK9qv7c2toKi8UiiQiqHbm6riW6a13fn+zv73+oqorhcLgKUFXVP+fn52+Lonj8ILJ0P8ZICCF9/PTpClhpBvgPeloL9U55NIAAAAAASUVORK5CYII='>$file",
        'typeFile' => $ftype,
        'sizeFile' => $size,
        'last_modified' => $ftime,
        'owner' => $fowner,
        'permission' => $this->w("$dir/$file",$this->perms("$dir/$file")),
      );
      $kolomData.=$this->setKolomData($no,$arrayFile);
      $no++;
		}

    $htmlTable = "

        <div class='row'>
          <div class='form-group'>
              <label class='col-sm-2 control-label' style='margin-top:6px;'>Current Location</label>
              <div class='col-sm-7'>
                  <input type='hidden' id='locationRelease' name='locationRelease' value='$this->locationRelease'>
                  <input type='text' class='form-control' placeholder='Current Location' id='currentLocation' name='currentLocation' value='$locationDirectory'>
              </div>
              <div class='col-sm-1'>
                <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
                    <a class='btn btn-primary' >Tampilkan</a>
                </span>
              </div>
          </div>
        </div>
        <br>

        <table class='table table-bordered table-striped table-hover table-condensed'>
            ".$this->setKolomHeader()."
            <tbody>
              $kolomData
            </tbody>
        </table>
        <input type='hidden' name='".$this->Prefix."_jmlcek' id='".$this->Prefix."_jmlcek' value='0'>
    ";
    return $htmlTable;
  }

}
$detailRelease = new detailRelease();


 ?>
