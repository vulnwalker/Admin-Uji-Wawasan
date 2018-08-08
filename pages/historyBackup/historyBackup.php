<?php
class historyBackup extends baseObject{
  var $Prefix = "historyBackup";
  var $formName = "historyBackupForm";
  var $tableName = "history_backup";
  var $idServerBackup = 10;

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'sendTo':{
        $content = $this->sendTo();
  		break;
  		}
      case 'hardDiskChanged':{
            $jsonDisk = $this->getDetailDisk($idServer);
            $getDataDisk = $this->sqlArray($this->sqlQuery("select * from ref_disk where id = '$hardDiskTujuan'"));
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
      case 'saveSendTo':{
            if(empty($serverTujuan)){
              $err = "Pilih Server Tujuan";
            }
            $content = array('urutanKe' =>  1);
      break;
      }
      case 'copyDatabase':{
            $listHistoryBackup = explode(";",$listIdBackup);
            $getDataBackup = $this->sqlArray($this->sqlQuery("select * from history_backup where id = '".$listHistoryBackup[$urutanKe - 1]."'"));
            $idServer = $getDataBackup['id_server'];
            $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$idServer'"));
            $getDataServerBackUp = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$this->idServerBackup'"));
            $sshConnectionBackupServer = $this->sshConnect($getDataServerBackUp['alamat_ip'],$getDataServerBackUp['port_ftp']);
            $this->sshLogin($sshConnectionBackupServer,$getDataServerBackUp['user_ftp'],$getDataServerBackUp['password_ftp']);
            $this->sshCommand($sshConnectionBackupServer,"mkdir -p $backupLocation/".$getDataServer['alias']);
            $arrayBackup = json_decode($getDataBackup['file_backup']);
            if(!empty($arrayBackup->struktur)){
              $this->sshCommand($sshConnectionBackupServer,"sshpass -p '".$getDataServer['password_ftp']."' scp -P ".$getDataServer['port_ftp']." ".$getDataServer['user_ftp']."@".$getDataServer['alamat_ip'].":$arrayBackup->struktur $backupLocation/".$getDataServer['alias']."/");
              $cek = "sshpass -p '".$getDataServer['password_ftp']."' scp -P ".$getDataServer['port_ftp']." ".$getDataServer['user_ftp']."@".$getDataServer['alamat_ip'].":$arrayBackup->struktur $backupLocation/".$getDataServer['alias']."/";
            }
            if(!empty($arrayBackup->data)){
              $this->sshCommand($sshConnectionBackupServer,"sshpass -p '".$getDataServer['password_ftp']."' scp -P ".$getDataServer['port_ftp']." ".$getDataServer['user_ftp']."@".$getDataServer['alamat_ip'].":$arrayBackup->data $backupLocation/".$getDataServer['alias']."/");
            }
            if(!empty($arrayBackup->triger)){
              $this->sshCommand($sshConnectionBackupServer,"sshpass -p '".$getDataServer['password_ftp']."' scp -P ".$getDataServer['port_ftp']." ".$getDataServer['user_ftp']."@".$getDataServer['alamat_ip'].":$arrayBackup->triger $backupLocation/".$getDataServer['alias']."/");
            }
            if($urutanKe == sizeof($listHistoryBackup)){
              $status = "OK";
            }elseif($urutanKe > sizeof($listHistoryBackup)){
              $status = "OK";
            }
            $content = array('urutanKe' => $urutanKe + 1, 'status' => $status);
  		break;
  		}
      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "tanggal like '%".$this->generateDate($filterCari)."%' ";
          $arrKondisi[] = "id_server like '%$filterCari%' ";
          $arrKondisi[] = "nama_database like '%$filterCari%' ";
          $kondisi = join(" or ",$arrKondisi);
          $kondisi = " where $kondisi ";
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
        $cek = "select * from $this->tableName $kondisi";
        $content=array('tableContent' => $this->generateTable($kondisi));
        // $getData = $this->sqlQuery("select * from $tableName $kondisi $kondisiSort $queryLimit");

  		break;
  		}
      case 'Edit':{
        $content = array("idEdit" => $historyBackup_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaServer)){
          $err = "Isi Nama Server";
        }elseif(empty($alamatIP)){
          $err = "Isi Alamat Server";
        }elseif(empty($userFTP)){
          $err = "Isi User FTP";
        }elseif(empty($passwordFTP)){
          $err = "Isi Password FTP";
        }elseif(empty($ftpPort)){
          $err = "Isi PORT FTP";
        }elseif(empty($userMysql)){
          $err = "Isi User FTP";
        }elseif(empty($passwordMysql)){
          $err = "Isi Password FTP";
        }elseif(empty($portMysql)){
          $err = "Isi PORT FTP";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama_server' => $namaServer,
            'alias' => $aliasServer,
            'alamat_ip' => $alamatIP,
            'user_ftp' => $userFTP,
            'password_ftp' => $passwordFTP,
            'port_ftp' => $ftpPort,
            'user_mysql' => $userMysql,
            'password_mysql' => $passwordMysql,
            'port_mysql' => $portMysql,
            // 'status' => $statusServer,
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
  			if(empty($namaServer)){
          $err = "Isi Nama Server";
        }elseif(empty($alamatIP)){
          $err = "Isi Alamat Server";
        }elseif(empty($userFTP)){
          $err = "Isi User FTP";
        }elseif(empty($passwordFTP)){
          $err = "Isi Password FTP";
        }elseif(empty($ftpPort)){
          $err = "Isi PORT FTP";
        }elseif(empty($userMysql)){
          $err = "Isi User FTP";
        }elseif(empty($passwordMysql)){
          $err = "Isi Password FTP";
        }elseif(empty($portMysql)){
          $err = "Isi PORT FTP";
        }
        if(empty($err)){
          $dataUpdate = array(
            'nama_server' => $namaServer,
            'alias' => $aliasServer,
            'alamat_ip' => $alamatIP,
            'user_ftp' => $userFTP,
            'password_ftp' => $passwordFTP,
            'port_ftp' => $ftpPort,
            'user_mysql' => $userMysql,
            'password_mysql' => $passwordMysql,
            'port_mysql' => $portMysql,
            // 'status' => $statusServer,
          );
          $query = $this->sqlUpdate($this->tableName,$dataUpdate,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($historyBackup_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$historyBackup_cb[$i]."'";
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
      apache_setenv("KeepAlive", "On");
      apache_setenv("KeepAliveTimeout", "360000");
      apache_setenv("MaxKeepAliveRequests", "360000");
      header("Connection: keep-alive");
      header("Keep-Alive: timeout=360000, max=360000");
       echo $this->jsonGenerator();
    }
  }
  function loadScript(){
    return "
    <script type='text/javascript' src='js/historyBackup/historyBackup.js'></script>
    ";
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
    <a class='header-btn' style='cursor:pointer;'  onclick=$this->Prefix.sendTo(); title='Send To'>
      <i class='glyph-icon icon-rss'></i>
    </a>


    </div>

    ";
    // <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Baru(); title='Baru'>
    //   <i class='glyph-icon icon-plus'></i>
    // </a>
    // <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Edit(); title='Edit'>
    //   <i class='glyph-icon icon-pencil'></i>
    // </a>
    // <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Hapus(); title='Hapus'>
    //   <i class='glyph-icon icon-trash'></i>
    // </a>
    return $setMenuEdit;
  }
  function pageContent(){
    $pageContent = "
    <div id='page-title'>
      <h2>History Backup</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable()."
          </div>
      </div>
    </div>
    <div id='tempatModal'> </div>

    ";
    return $pageContent;
  }

  function setKolomHeader(){
    $kolomHeader = "
    <thead>
      <tr>
          <th>No</th>
          <th style='text-align:center;'>".$this->checkAll(25,$this->Prefix)."</th>
          <th>Tanggal</th>
          <th>Alias</th>
          <th>Nama Database</th>
          <th>Struktur</th>
          <th>Data</th>
          <th>Triger</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id_server'"));
    $arrayBackup = json_decode($file_backup);
    $tableRow = "
    <tr class='$classRow'>
        <td>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td>".$this->generateDate($tanggal)."</td>
        <td>".$getDataServer['alias']."</td>
        <td>$nama_database</td>
        <td>".$arrayBackup->struktur."</td>
        <td>".$arrayBackup->data."</td>
        <td>".$arrayBackup->triger."</td>
    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from history_backup ".$kondisiTable);
    while ($dataServer = $this->sqlArray($getDataServer)) {
      $kolomData.= $this->setKolomData($no,$dataServer);
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
                ".$this->formBaru()."
              </div>
            </div>
        </div>
      </div>
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
      </body>
      </html>
          ";

    return $pageShow;
  }

  function sendTo(){
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
    for ($i=0; $i < sizeof($historyBackup_cb) ; $i++) {
      $listIdBackup[] = $historyBackup_cb[$i];
    }
    $listIdBackup = implode(";",$listIdBackup);
     $comboHardDisk = $this->cmbQuery("idDisk","","select id,nama_harddisk from ref_disk where id_server = '$this->idServerBackup'","onchange=$this->Prefix.diskChanged() class='form-control'","-- HARD DISK --");
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalSendTo' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
      <form name='".$this->formName."_sendTo' id='".$this->formName."_sendTo'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$formCaption</h4>
                </div>
                <div class='modal-body'>

                  <div class='form-group'>
                    <div class='row'>
                    <label class='col-sm-2 control-label'>Server Tujuan</label>
                      <div class='col-sm-10'>
                          ".$this->cmbQueryEmpty("serverTujuan",$serverTujuan,"select id,nama_server from ref_server where id ='$this->idServerBackup'"," class='form-control'")."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                    <label class='col-sm-2 control-label'>Harddisk</label>
                      <div class='col-sm-10'>
                          ".$this->cmbQuery("hardDiskTujuan",$hardDiskTujuan,"select id,nama_harddisk from ref_disk where id_server ='$this->idServerBackup'"," class='form-control' onchange=$this->Prefix.hardDiskChanged()")."
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
                              <input type='hidden' class='form-control' id='backupLocation' name='backupLocation' readonly>
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
                <div class='modal-footer'>
                    <input type='hidden' name='listIdBackup' id='listIdBackup' value='".$listIdBackup."'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveSendTo();>Copy</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
      </form>
    </div>";
    return $content;
  }


}
$historyBackup = new historyBackup();


 ?>
