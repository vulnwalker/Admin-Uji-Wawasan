<?php
class refRelease extends baseObject{
  var $Prefix = "refRelease";
  var $formName = "refReleaseForm";
  var $tableName = "ref_release";
  var $idServer = 1000;
  var $username = "";
  var $blockListExtension = array(
      array('.bck'),
      array(',bck'),
      array('.BCK'),
      array('.backup'),
      array('.201'),
      array('.php_'),
      array('.php_'),
      array('.bck'),
      array('.git'),
      array('_DOC'),
      array('.doc'),
      array('.xls'),
  );

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'Release':{
        $getData = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id = '".$refRelease_cb[0]."'"));
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
                          <label class='col-sm-3 control-label' style='margin-top:6px;'>Option</label>
                          <div class='col-sm-3'>
                            <input type='checkbox' name='optionExecuteSql' id='optionExecuteSql' value='databaseOnly'> Push Database ( KOSONGAN )
                          </div>
                          <div class='col-sm-3'>
                            <input type='checkbox' name='optionPushFile' id='optionPushFile' value='optionPushFile'> Push Files
                          </div>
                        </div>
                      </div>
                      <div class='form-group' >
                          <div class='col-sm-12' >
                            <div class='progressbar' data-value='0' id='prosesBarValue'>
                              <div class='progressbar-value bg-primary' id='prosesBarWarna' >
                                <div class='progress-overlay'></div>
                                <div class='progress-label' id='prosesBarText'>0%</div>
                              </div>
                            </div>
                          </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                          <div class='col-sm-12'>
                            <textarea class='form-control' id='logPush' readonly style='margin-top:10px;'></textarea>
                          </div>

                        </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <input type='hidden' name='idRelease' id='idRelease' value='".$refRelease_cb[0]."'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.executeRelease();>Push</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
          </form>
        </div>";
  		break;
  		}
      case 'prosesPush':{
            $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '".$listServer[$urutanServer - 1]."'"));
            $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
            $logPush .= $getDataServer['alias']." =>  ";
            if($nomorUrut == 1){
              if(!empty($optionExecuteSql)){
                $databasePassword = $getDataServer["password_mysql"];
                $userMysql = $getDataServer["user_mysql"];
                $databaseName = $getDataRelease['nama_database'];
                $sqlLocation = $getDataRelease['mysql_file'];
                $getDataServerLocal = $this->sqlArray($this->sqlQuery("select * from  ref_server where id = '$this->idServer'"));
                $sshConnectionLocal = $this->sshConnect($getDataServerLocal['alamat_ip'],$getDataServerLocal['port_ftp']);
                $this->sshLogin($sshConnectionLocal,$getDataServerLocal['user_ftp'],$getDataServerLocal['password_ftp']);
                $namaFileStruktur = "/tmp/".$databaseName.".struktur.sql";
                $namaFileFunction = "/tmp/".$databaseName.".function.sql";
                $this->sshCommand($sshConnectionLocal,"mysqldump -u ".$getDataServerLocal['user_mysql']." -p".$getDataServerLocal['password_mysql']." -f --no-data --skip-events --skip-routines --skip-triggers $databaseName > $namaFileStruktur");
                $this->sshCommand($sshConnectionLocal,"mysqldump -u ".$getDataServerLocal['user_mysql']." -p".$getDataServerLocal['password_mysql']." -f --routines --triggers --no-create-info --no-data --no-create-db --skip-opt $databaseName > $namaFileFunction");
                $this->pushFileSelected($getDataServer['id'],$namaFileStruktur);
                $this->pushFileSelected($getDataServer['id'],$namaFileFunction);

                $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
                $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
                $createDatabase = "mysql -u".$userMysql." -p".$databasePassword." -e 'CREATE DATABASE ".$databaseName." CHARACTER SET latin1 COLLATE latin1_general_ci '";
                $this->sshCommand($sshConnection,$createDatabase);
                $this->sshCommand($sshConnection,"mysql -u $userMysql -p$databasePassword -f -c $databaseName < $namaFileStruktur");
                $this->sshCommand($sshConnection,"mysql -u $userMysql -p$databasePassword -f -c $databaseName < $namaFileFunction");
                $cek = $createDatabase;
                $logPush .= "CREATE DATABASE => $databaseName , ";
              }
            }

            if($nomorUrut == ($jumlahData + 1)){
              $sukses = "OK";
              $nextServer = "1";
            }else{
              if(!empty($optionPushFile)){
                $getDataFileCheck = $this->sqlArray($this->sqlQuery("select * from json_file_check where id ='$idFileCheck'"));
                $explodeListFile = json_decode($getDataFileCheck['isi']);
                $fileLocation = $explodeListFile[$nomorUrut - 1]->file;
                $logPush .= "push file => $fileLocation";
                $this->pushFileSelected($getDataServer['id'],$namaFile);

              }
              if($nomorUrut == ($jumlahData)){
                $sukses = "OK";
                $nextServer = "1";
              }
              $persen = ($nomorUrut / $jumlahData) * 100;
              $persenText = $persen."%";
              $sukses = "";
            }
            if($urutanServer == sizeof($listServer)){
              $sukses = "OK";
              $nextServer = "0";
            }

            $content = array(
                        'urutanKe' => $urutanKe + 1,
                        'nextServer' => $nextServer,
                        "sukses" => $sukses,
                        "persen" => $persen,
                        "persenText" => $persenText,
                        "error" => $cek,
                        'namaFile' => $fileLocation,
                        'fileLocation' => $fileLocation,
                        'logPush' => $logPush
                      );
  		break;
  		}
      case 'executeRelease':{
            if(sizeof($listServer) == 0){
              $err = "Pilih Server Tujuan";
            }
            if(empty($optionPushFile) &&  empty($optionExecuteSql)){
              $err = "Centang Salah Satu";
            }


            if($err == ""){
              if(!empty($optionPushFile)){
                  $jsonFile = $this->listFileRelease($this->idServer,$idRelease);
                  $decodeJsonFile = json_decode($jsonFile);
                  $arrayFile = array();
                  for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
                    $dateModified = $this->dateConversion($decodeJsonFile[$i]->tanggal);
                    if($this->filterExtension($decodeJsonFile[$i]->file) == 0 ){
                      if(!empty($filterFolder)){
                          if($this->unFilterExtension($decodeJsonFile[$i]->file,$filterFolder) != 0){
                              $arrayFile[] = array(
                                      'file' => $decodeJsonFile[$i]->file,
                                      'tanggal' => $decodeJsonFile[$i]->tanggal,
                                      'size' => $decodeJsonFile[$i]->size,
                              );
                          }
                      }else{
                        $arrayFile[] = array(
                                'file' => $decodeJsonFile[$i]->file,
                                'tanggal' => $decodeJsonFile[$i]->tanggal,
                                'size' => $decodeJsonFile[$i]->size,
                        );
                      }
                    }
                  }
                  $jsonFileFilter = json_encode($arrayFile);
                  $dataFileCheck = array(
                    'isi' => $jsonFileFilter,
                    'username' => $this->username,
                    'tanggal' => date("Y-m-d"),
                    'jam' => date("H:i"),
                  );
                  $this->sqlQuery($this->sqlInsert("json_file_check",$dataFileCheck));
                  $getIdFileCheck = $this->sqlArray($this->sqlQuery("select max(id) from json_file_check where username = '$this->username'"));
                  $decodeJSON = json_decode($jsonFileFilter);


                  $content = array(
                    "jumlahData" => sizeof($decodeJSON),
                    "idFileCheck" => $getIdFileCheck['max(id)'],
                    "idDataBaseCheck" => $getIdDataBaseCheck['max(id)'],
                    'urutanKe' =>  1,
                );
              }else{
                $content = array(
                  "jumlahData" => 1,
                  "idFileCheck" => 0,
                  'urutanKe' =>  1,
              );
              }


            }
  		break;
  		}
      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "nama_release like '%$filterCari%' ";
          $arrKondisi[] = "tanggal_release like '%".$this->generateDate($filterCari)."%' ";
          $arrKondisi[] = "directory_location like '%$filterCari%' ";
          $kondisi = join(" or ",$arrKondisi);
          $kondisi = " where $kondisi ";
        }
        $cek = "select * from $this->tableName $kondisi";
        $content=array('tableContent' => $this->generateTable($kondisi));
  		break;
  		}
      case 'Edit':{
        $content = array("idEdit" => $refRelease_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaRelease)){
          $err = "Isi Nama Release";
        }elseif(empty($tanggalRelease)){
          $err = "Isi Tanggal Release";
        }elseif(empty($directoryLocation)){
          $err = "Isi Directory Location";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama_release' => $namaRelease,
            'tanggal_release' => $this->generateDate($tanggalRelease),
            'directory_location' => $directoryLocation,
            'nama_database' => $namaDatabase,
            'last_modified' => date("Y-m-d H:i:s"),
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          mkdir($directoryLocation);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($namaRelease)){
          $err = "Isi Nama Release";
        }elseif(empty($tanggalRelease)){
          $err = "Isi Tanggal Release";
        }elseif(empty($directoryLocation)){
          $err = "Isi Directory Location";
        }
        if(empty($err)){
          $getDataSebelumnya = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id ='$idEdit'"));
          $dataUpdate = array(
            'nama_release' => $namaRelease,
            'tanggal_release' => $this->generateDate($tanggalRelease),
            'directory_location' => $directoryLocation,
            'nama_database' => $namaDatabase,
            'last_modified' => date("Y-m-d H:i:s"),
          );
          $query = $this->sqlUpdate($this->tableName,$dataUpdate,"id = '$idEdit'");
          $this->sqlQuery($query);
          rename($getDataSebelumnya['directory_location'],$directoryLocation);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refRelease_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refRelease_cb[$i]."'";
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
    <script type='text/javascript' src='js/refRelease/refRelease.js'></script>
    <script type='text/javascript' src='assets/widgets/datepicker/datepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/multi-select/multiselect.js'></script>
    <style>
      .dataTables_filter{
        display:none;
      }
    </style>
    <link rel='stylesheet' type='text/css' href='assets/widgets/datatable/datatable.css'>
    <script type='text/javascript' src='assets/widgets/datatable/datatable.js'></script>
    <script type='text/javascript' src='assets/widgets/datatable/datatable-bootstrap.js'></script>
    <script type='text/javascript' src='assets/widgets/datatable/datatable-tabletools.js'></script>
    <script type='text/javascript'>
    $(document).ready(function() {
      var table = $('#dataServer').DataTable({
           lengthMenu: [
               [ 1, 2, 4, 8, 16, 32, 64, 128, -1 ],
               [ '1', '2', '4', '8', '16', '32', '64', '128', 'Show all' ]
           ]
      });
    $('#dataServer tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('tr-selected');
    } );
    $('#dataTables_filter').attr('style','display:none;');
        $('.bootstrap-datepicker').bsdatepicker({
            format: 'dd-mm-yyyy'
        });
    });
    </script>
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
              <span class='input-group-btn' onclick=$this->Prefix.setValueFilter(document.getElementById('filterCari'));>
                  <a class='btn btn-primary' >Cari</a>
              </span>
          </div>
      </div>
    </div>

    <a class='header-btn' style='cursor:pointer;'  onclick=$this->Prefix.Release(); title='Push To Release'>
      <i class='glyph-icon icon-rss'></i>
    </a>
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
    $pageContent = "
    <div id='page-title'>
      <h2>Referensi Release</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable()."
          </div>
      </div>
      <div id='tempatModal'> </div>
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
          <th>Nama Release</th>
          <th>Tanggal Release</th>
          <th>Directory Location</th>
          <th>Nama Database</th>
          <th>Last Modified</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }
    if($status == '1'){
      $statusServer = "LIVE";
    }elseif($status == '2'){
      $statusServer = "ERROR";
    }elseif($status == '3'){
      $statusServer = "DIE";
    }
    $tableRow = "
    <tr class='$classRow'>
        <td>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td><span style='cursor:pointer;' onclick=$this->Prefix.detailRelease($id)>$nama_release</span?</td>
        <td>".$this->generateDate($tanggal_release)."</td>
        <td>$directory_location</td>
        <td>$nama_database</td>
        <td>$last_modified</td>
    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ref_release ".$kondisiTable);
    while ($dataServer = $this->sqlArray($getDataServer)) {
      $kolomData.= $this->setKolomData($no,$dataServer);
      $no++;
    }
    $htmlTable = "
      <form name='$this->formName' id='$this->formName'>
        <table class='table table-bordered table-striped table-condensed table-hover'  role='grid' aria-describedby='dataServer_info' style='width: 100%;font-size:12px;' id='dataServer' >
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

  function formBaru(){
    $arrayStatus = array(
      array('1','LIVE'),
      array('2','ERROR'),
      array('3','DIE'),
    );
    $comboStatus = $this->cmbArray("statusServer","",$arrayStatus,"-- STATUS --","class='form-control' ");
    $formBaru = "
    <div id='page-title'>
      <h2>REFERENSI RELEASE BARU</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Release</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Release' id='namaRelease' name='namaRelease'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Tanggal Relase</label>
                        <div class='col-sm-11'>
                            <input type='text' class='bootstrap-datepicker form-control'  placeholder='Tanggal Release' id='tanggalRelease' name='tanggalRelease'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Directory Location</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Directory Location' id='directoryLocation' name='directoryLocation'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Database</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Database' id='namaDatabase' name='namaDatabase'>
                        </div>
                    </div>
                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12' >
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
    $arrayStatus = array(
      array('1','LIVE'),
      array('2','ERROR'),
      array('3','DIE'),
    );
    $comboStatus = $this->cmbArray("statusServer",$status,$arrayStatus,"-- STATUS --","class='form-control' ");
    $formEdit = "
    <div id='page-title'>
      <h2>EDIT RELEASE</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Nama Release</label>
                      <div class='col-sm-11'>
                          <input type='text' class='form-control'  placeholder='Nama Release' id='namaRelease' name='namaRelease' value='$nama_release'>
                      </div>
                  </div>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Tanggal Relase</label>
                      <div class='col-sm-11'>
                          <input type='text' class='bootstrap-datepicker form-control'  placeholder='Tanggal Release' id='tanggalRelease' name='tanggalRelease' value='".$this->generateDate($tanggal_release)."'>
                      </div>
                  </div>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Directory Location</label>
                      <div class='col-sm-11'>
                          <input type='text' class='form-control'  placeholder='Directory Location' id='directoryLocation' name='directoryLocation' value='$directory_location'>
                      </div>
                  </div>
                  <div class='form-group'>
                      <label class='col-sm-1 control-label'>Nama Database</label>
                      <div class='col-sm-11'>
                          <input type='text' class='form-control'  placeholder='Nama Database' id='namaDatabase' name='namaDatabase' value='$nama_database'>
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

  function dateConversion($tanggal){
    // $arrayTanggal = explode("/",$tanggal);
    // $getJam = explode("-",$tanggal);
    // $arrayJam = explode(":",$getJam[1]);
    // return "20".str_replace($arrayJam[0].":".$arrayJam[1].":".$arrayJam[2],"",$arrayTanggal[2])."".$this->genNumber($arrayTanggal[1])."-".$this->genNumber($arrayTanggal[0])." ".$arrayJam[0].":".$arrayJam[1];
    $arrayTanggal =  explode("+",$tanggal);
    $explodeJam = explode(":",$arrayTanggal[1]);
    return $arrayTanggal[0]." ".$explodeJam[0].":".$explodeJam[1];
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
  function listFileRelease($id,$idRelease) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $dirSumber = $getDataRelease['directory_location'];
    $contentAWK = '{ print "{\"file\":\""$4"\",\"tanggal\":\""$3"\",\"size\":\""$2"\"} "}';
    $prinst = '"%-8g %8s %-.22T+ %p\n"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"find $dirSumber -type f -printf $prinst | sort -r | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function getJsonFileTarget($fileTarget,$id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"file\":\""$4"\",\"tanggal\":\""$3"\",\"size\":\""$2"\"} "}';
    $prinst = '"%-8g %8s %-.22T+ %p\n"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"find $fileTarget -type f -printf $prinst | sort -r | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function pushFileSelected($idServer,$fileLocation){
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$idServer'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $arrayFolder = explode("/",$fileLocation);
    $arrayLong = sizeof($arrayFolder)- 1;
    $target = strstr($fileLocation, $arrayFolder[$arrayLong], true);
    $this->sshCommand($sshConnection,"mkdir -p $target");
    ssh2_scp_send($sshConnection,$fileLocation,$fileLocation);
  }
  function filterExtension($word){
      $result = 0;
      for ($i=0; $i < sizeof($this->blockListExtension); $i++) {
        if(strpos($word, $this->blockListExtension[$i][0]) !== false){
          $result += 1;
        }
      }
      return $result;
  }
  function unFilterExtension($word,$arrayAllowedExtension){
      $arrayAllowedExtension = explode("\n",$arrayAllowedExtension);
      $result = 0;
      for ($i=0; $i < sizeof($arrayAllowedExtension); $i++) {
        $filterWord = str_replace("\r","",$arrayAllowedExtension[$i]);
        $filterWord = str_replace("//","/",$filterWord);
        $filterWord = str_replace("\t","",$filterWord);
        $filterWord = str_replace(" ","",$filterWord);
        if(strpos($word, $filterWord) !== false){
          $result += 1;
        }
      }
      return $result;
  }
}
$refRelease = new refRelease();


 ?>
