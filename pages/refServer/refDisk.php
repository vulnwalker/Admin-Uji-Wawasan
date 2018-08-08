<?php
class refDisk extends baseObject{
  var $Prefix = "refDisk";
  var $formName = "refDiskForm";
  var $tableName = "ref_disk";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "nama_server like '%$filterCari%' ";
          $arrKondisi[] = "alias like '%$filterCari%' ";
          $arrKondisi[] = "alamat_ip like '%$filterCari%' ";
          $arrKondisi[] = "user_ftp like '%$filterCari%' ";
          $arrKondisi[] = "password_ftp like '%$filterCari%' ";
          $arrKondisi[] = "port_ftp like '%$filterCari%' ";
          $arrKondisi[] = "status like '%$filterCari%' ";
          $kondisi = join(" or ",$arrKondisi);
          $kondisi = " and ( $kondisi ) ";
        }
        // if(!empty($limitTable)){
        //     if($pageKe == 1){
        //        $queryLimit  = " limit 0,$limitTable";
        //     }else{
        //        $dataMulai = ($pageKe - 1)  * $limitTable;
        //        $dataMulai +=1;
        //        $queryLimit  = " limit $dataMulai,$limitTable";
        //     }
        // }
        // if (!empty($sorter)) {
        //   $kondisiSort = "ORDER BY $sorter $ascending";
        // }
        $cek = "select * from $this->tableName where id_server = '".$_POST['idServer']."' $kondisi";
        $this->idServer = $_POST['idServer'];
        $content=array('tableContent' => $this->generateTable($kondisi));
        // $getData = $this->sqlQuery("select * from $tableName $kondisi $kondisiSort $queryLimit");

  		break;
  		}
      case 'Edit':{
        $content = array("idEdit" => $refDisk_cb[0]);
  		break;
  		}
      case 'ManageDisk':{
        $content = array("idEdit" => $refDisk_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaHardDisk)){
          $err = "Isi Nama Harddisk";
        }elseif(empty($fileSystem)){
          $err = "Isi File System";
        }elseif(empty($backupLocation)){
          $err = "Isi Backup Location";
        }
        if(empty($err)){
          $dataInsert = array(
            'id_server' => $idServer,
            'nama_harddisk' => $namaHardDisk,
            'file_system' => $fileSystem,
            'backup_location' => $backupLocation,
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($namaHardDisk)){
          $err = "Isi Nama Harddisk";
        }elseif(empty($fileSystem)){
          $err = "Isi File System";
        }elseif(empty($backupLocation)){
          $err = "Isi Backup Location";
        }
        if(empty($err)){
          $dataUpdate = array(
            'nama_harddisk' => $namaHardDisk,
            'file_system' => $fileSystem,
            'backup_location' => $backupLocation,
          );
          $query = $this->sqlUpdate($this->tableName,$dataUpdate,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refDisk_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refDisk_cb[$i]."'";
          $this->sqlQuery($query);
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
    <script type='text/javascript' src='js/refServer/refDisk.js'></script>
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
      <li class='activeSidebar'>
          <a href='pages.php?page=refServer' title='Referensi Server'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>Referensi Server</span>
          </a>
      </li>
      <li>
          <a href='pages.php?page=fileManager' title='fileManager'>
              <i class='glyph-icon icon-linecons-tv'></i>
              <span>File Manager</span>
          </a>
      </li>
      <li>
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
    <a href='#' class='hdr-btn popover-button' title='Search' data-placement='bottom' data-id='#popover-search'>
      <i class='glyph-icon icon-search'></i>
    </a>
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
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Baru(); title='Baru'>
      <i class='glyph-icon icon-plus'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Edit(); title='Edit'>
      <i class='glyph-icon icon-pencil'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Hapus(); title='Hapus'>
      <i class='glyph-icon icon-trash'></i>
    </a>

    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '".$_GET['idServer']."'"));
    $alias = $getDataServer['alias'];
    $this->idServer = $_GET['idServer'];
    $pageContent = "
    <div id='page-title'>
      <h2>LIST HARDDISK $alias</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable(" where id_server = '$this->idServer'")."
          </div>
      </div>
      <input type='hidden' name='idServer' id='idServer' value='".$_GET['idServer']."'>
    </div>

    ";
    return $pageContent;
  }

  function setKolomHeader(){
    $kolomHeader = "
    <thead>
      <tr>
          <th>No</th>
          <th style='text-align:center;'>".$this->checkAll(25,$this->Prefix)."</th>
          <th>Nama Harddisk</th>
          <th>File System</th>
          <th>Mount Location</th>
          <th>Backup Location</th>
          <th>Size</th>
          <th>Used</th>
          <th>Free</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData,$jsonDisk){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }
    $this->file_system = $file_system;
    $arrayDisk = json_decode($jsonDisk);
    $diskObject = array_filter(  $arrayDisk,
        function ($e) use (&$searchedValue) {
            return $e->FileSystem == $this->file_system;
        }
    );
    $sizeDisk = $diskObject[max(array_keys($diskObject))]->Size."\n";
    $usedDisk = $diskObject[max(array_keys($diskObject))]->Used."\n";
    $freeDisk =  $diskObject[max(array_keys($diskObject))]->Free."\n";
    $mountedDisk =  $diskObject[max(array_keys($diskObject))]->Mounted."\n";
    $tableRow = "
    <tr class='$classRow'>
        <td>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td>$nama_harddisk</td>
        <td>$file_system</td>
        <td>$mountedDisk</td>
        <td>$backup_location</td>
        <td>".$sizeDisk."</td>
        <td>".$usedDisk."</td>
        <td>".$freeDisk."</td>
    </tr>
    ";
    return $tableRow;
  }
  function getDetailDisk($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"FileSystem\":\""$1"\",", "\"Size\":\""$2"\",", "\"Used\":\""$3"\",", "\"Free\":\""$4"\",", "\"Persen\":\""$5"\",", "\"Mounted\":\""$6"\"} "}';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"df -h | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ref_disk ".$kondisiTable);
    $jsonDisk = $this->getDetailDisk($this->idServer);
    while ($dataServer = $this->sqlArray($getDataServer)) {
      $kolomData.= $this->setKolomData($no,$dataServer,$jsonDisk);
      $no++;
    }
    $htmlTable = "
      <form name='$this->formName' id='$this->formName'>
        <table class='table table-bordered table-striped table-condensed table-hover'>
            ".$this->setKolomHeader()."
            <tbody>
              $kolomData
            </tbody>
        </table>
        <input type='hidden' name='".$this->Prefix."_jmlcek' id='".$this->Prefix."_jmlcek' value='0'>
      </form>
    ";
    return $htmlTable;
  }

  function pageShowNew(){
    $pageShow = "
    ".$this->loadJSandCSS()."
        <body class='fixed-header'>
        <div id='loading'>
            <div class='spinner'>
                <div class='bounce1'></div>
                <div class='bounce2'></div>
                <div class='bounce3'></div>
            </div>
        </div>
        <div id='page-wrapper'>
        ".$this->emptyMenuBar()."
        ".$this->sidebar()."
        <div id='page-content-wrapper'>
            <div id='page-content'>
              <div class='container'>
                ".$this->formBaru($_GET['idServer'])."
              </div>
            </div>
        </div>
      </div>
      <input type='hidden' name='idServer' id='idServer' value='".$_GET['idServer']."'>
      </body>
      </html>
          ";

    return $pageShow;
  }
  function pageShowEdit($idEdit){
    $pageShow = "
    ".$this->loadJSandCSS()."
        <body class='fixed-header'>
        <div id='loading'>
            <div class='spinner'>
                <div class='bounce1'></div>
                <div class='bounce2'></div>
                <div class='bounce3'></div>
            </div>
        </div>
        <div id='page-wrapper'>
        ".$this->emptyMenuBar()."
        ".$this->sidebar()."
        <div id='page-content-wrapper'>
            <div id='page-content'>
              <div class='container'>
                ".$this->formEdit($idEdit)."
              </div>
            </div>
        </div>
      </div>
      <input type='hidden' name='idServer' id='idServer' value='".$_GET['idServer']."'>
      </body>
      </html>
          ";

    return $pageShow;
  }

  function formBaru($idServer){
    $arrayFileSystem = array();
    $getPartisi = str_replace('{"FileSystem":"Filesystem","Size":"Size","Used":"Used","Free":"Avail","Persen":"Use%","Mounted":"Mounted"},',"",$this->getDetailDisk($idServer));
    $arrayDisk = json_decode($getPartisi);
    for ($i=0; $i < sizeof($arrayDisk) ; $i++) {
        $arrayFileSystem[] = array($arrayDisk[$i]->FileSystem,$arrayDisk[$i]->FileSystem);
    }

    $comboDisk = $this->cmbArray("fileSystem",$fileSystem,$arrayFileSystem,"-- File System --","class='form-control' ");
    $formBaru = "
    <div id='page-title'>
      <h2>Haddisk Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Nama Hard Disk</label>
                        <div class='col-sm-10'>
                            <input type='text' class='form-control'  placeholder='Nama Harddisk' id='namaHardDisk' name='namaHardDisk'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>File System</label>
                        <div class='col-sm-10'>
                            $comboDisk
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Backup Location</label>
                        <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='Backup Location' id='backupLocation' name='backupLocation'>
                        </div>
                    </div>
                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12'>
                        <button type='button' onclick=$this->Prefix.saveNew(); class='btn btn-alt btn-hover btn-success'>
                            <span>Simpan</span>
                            <i class='glyph-icon icon-save'></i>
                        </button>
                        <button type='button' onclick=$this->Prefix.homePage(); class='btn btn-alt btn-hover btn-danger'>
                            <span>Batal</span>
                            <i class='glyph-icon icon-times'></i>
                        </button>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    ";
    return $formBaru;
  }
  function formEdit($idEdit){
    $getData = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id='$idEdit'"));
    foreach ($getData as $key => $value) {
       $$key = $value;
    }
    $arrayFileSystem = array();
    $getPartisi = str_replace('{"FileSystem":"Filesystem","Size":"Size","Used":"Used","Free":"Avail","Persen":"Use%","Mounted":"Mounted"},',"",$this->getDetailDisk($id_server));
    $arrayDisk = json_decode($getPartisi);
    for ($i=0; $i < sizeof($arrayDisk) ; $i++) {
        $arrayFileSystem[] = array($arrayDisk[$i]->FileSystem,$arrayDisk[$i]->FileSystem);
    }
    $comboDisk = $this->cmbArray("fileSystem",$file_system,$arrayFileSystem,"-- File System --","class='form-control' ");
    $formEdit = "
    <div id='page-title'>
      <h2>Referensi Server Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Nama Hard Disk</label>
                        <div class='col-sm-10'>
                            <input type='text' class='form-control'  placeholder='Nama Harddisk' id='namaHardDisk' name='namaHardDisk' value='$nama_harddisk'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>File System</label>
                        <div class='col-sm-10'>
                            $comboDisk
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Backup Location</label>
                        <div class='col-sm-10'>
                          <input type='text' class='form-control'  placeholder='Backup Location' id='backupLocation' name='backupLocation' value='$backup_location'>
                        </div>
                    </div>
                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12'>
                        <button type='button' onclick=$this->Prefix.saveEdit($idEdit); class='btn btn-alt btn-hover btn-success'>
                            <span>Simpan</span>
                            <i class='glyph-icon icon-save'></i>
                        </button>
                        <button type='button' onclick=$this->Prefix.homePage(); class='btn btn-alt btn-hover btn-danger'>
                            <span>Batal</span>
                            <i class='glyph-icon icon-times'></i>
                        </button>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    ";
    return $formEdit;
  }


}
$refDisk = new refDisk();


 ?>
