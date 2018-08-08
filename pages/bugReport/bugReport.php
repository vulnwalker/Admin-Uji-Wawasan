<?php
class bugReport extends baseObject{
  var $Prefix = "bugReport";
  var $formName = "bugReportForm";
  var $tableName = "ref_server";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'refreshList':{


        $cek = "select * from $this->tableName $kondisi";
        $content=array('tableContent' => $this->generateTable($kondisi));

  		break;
  		}
        case 'Edit':{
          $dataBug = json_decode($this->getDataBug(" where id = '".$bugReport_cb[0]."'"));
          $formCaption = "UPDATE STATUS";
          $arrayStatus = array(
              array("0","BELUM DITANGANI"),
              array("1","PROSES"),
              array("2","SELESAI"),
          );
          $comboStatus = $this->cmbArray("statusBug",$dataBug[0]->status,$arrayStatus," ","class='form-control'");
          $content="
          <div class='modal fade bs-example-modal-lg' id='modalRelease' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
            <form name='".$this->formName."_edit' id='".$this->formName."_edit'>
              <div class='modal-dialog modal-lg'>
                  <div class='modal-content'>
                      <div class='modal-header'>
                          <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                          <h4 class='modal-title'>$formCaption</h4>
                      </div>
                      <div class='modal-body'>
                        <div class='form-group'>
                          <div class='row'>
                            <label class='col-sm-3 control-label' style='margin-top:6px;'>STATUS</label>
                            <div class='col-sm-9'>
                              $comboStatus
                            </div>
                          </div>
                        </div>
                        <div class='form-group'>
                          <div class='row'>
                            <label class='col-sm-3 control-label' style='margin-top:6px;'>NOTE</label>
                            <div class='col-sm-9'>
                              <textarea class='form-control' id='noteReport' name='noteReport'  style='margin-top:10px;'>".$dataBug[0]->note."</textarea>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class='modal-footer'>
                          <input type='hidden' name='idEdit' id='idEdit' value='".$bugReport_cb[0]."'>
                          <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveEdit(".$bugReport_cb[0].");>Simpan</button>
                          <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                      </div>
                  </div>
              </div>
            </form>
          </div>";
    		break;
    		}
      case 'ManageDisk':{
        $content = array("idEdit" => $bugReport_cb[0]);
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
            $dataUpdate = [
              'note' => $noteReport,
              'status' => $statusBug,
              'idEdit' => $idEdit,
            ];
            $ch = curl_init('http://cs.pilar.web.id/apiBug/update.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataUpdate);
            $response = json_decode(curl_exec($ch));
            $content = $response->content;
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($bugReport_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$bugReport_cb[$i]."'";
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
    <script type='text/javascript' src='js/bugReport/bugReport.js'></script>

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
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Edit(); title='Edit'>
      <i class='glyph-icon icon-pencil'></i>
    </a>
    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $pageContent = "


    <div id='page-title'>
        <h2>Bug Report</h2>
    </div>
    <div class='row'>
        <div class='col-md-12'>
            ".$this->generateTable($idBug)."
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
          <th style='width:20px !important;'>No</th>
          <th width='10'></th>
          <th width='200'>PEMDA</th>
          <th width='100'>APLIKASI</th>
          <th width='100'>DESKRIPSI</th>
          <th width='100'>TANGGAL</th>
          <th width='100'>FILE</th>
          <th width='100'>STATUS</th>
          <th width='100'>ACTION</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }

  function generateTable($kondisiTable){
    $no = 1;
      $jsonData =$this->getDataBug();
      $deocdedJson = json_decode($jsonData);
      for ($i=0; $i < sizeof($deocdedJson) ; $i++) {
        $kolomData.= $this->listBug($deocdedJson[$i],$no);
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
  function listBug($dataBug,$no){
    if($dataBug->status == '0'){
      $status = "Belum ditangani";
    }elseif($dataBug->status == '1'){
      $status = "Dalam Proses";
    }elseif($dataBug->status == '2'){
      $status = "Perbaikan Selesai";
    }
    $isi = strip_tags(base64_decode($dataBug->deskripsi));

    if (strlen($isi) > 250) {

     $stringCut = substr($isi, 0, 250);

     $isi = substr($stringCut, 0, strrpos($stringCut, ' ')).'...';
    }
    $arrayFile= $dataBug->file;
    for ($i=0; $i < sizeof($arrayFile); $i++) {
      $listFile[]= "<a href='".$arrayFile[$i]->fileLocation."' target='_blank'>DOWNLOAD</a>";
    }


    $tableRow = "
    <tr class='$classRow'>
        <td align='center'>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$dataBug->id,$this->Prefix)."</td>
        <td>".$dataBug->namaPemda."</td>
        <td>".$dataBug->namaProduk."</td>
        <td>".$isi."</td>
        <td>".$this->generateDate($dataBug->tanggal)."</td>
        <td>".implode(" | ",$listFile)."</td>
        <td>$status</td>
        <td align='center'><button class='btn btn-info'><i class='fa fa-eye'></i> LIHAT</button></td>
    </tr>
    ";
    return $tableRow;
  }
  function getDataBug($kondisiTable){
    $post = [
            'kondisi' => $kondisiTable,
          ];
      $ch = curl_init('http://cs.pilar.web.id/apiBug/list.php');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
      $response = curl_exec($ch);
      return $response;
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
      <h2>Referensi Server Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Server</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Server' id='namaServer' name='namaServer'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Alias</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Alias' id='aliasServer' name='aliasServer'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Alamat IP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Alamat IP' id='alamatIP' name='alamatIP'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>User FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='User FTP' id='userFTP' name='userFTP'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Password FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Password FTP' id='passwordFTP' name='passwordFTP'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>FTP PORT</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PORT FTP' id='ftpPort' name='ftpPort'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>USER MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='USER MYSQL' id='userMysql' name='userMysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PASSWORD MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PASSWORD MYSQL' id='passwordMysql' name='passwordMysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PORT MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='POT MYSQL' id='portMysql' name='portMysql'>
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
    $arrayStatus = array(
      array('1','LIVE'),
      array('2','ERROR'),
      array('3','DIE'),
    );
    $comboStatus = $this->cmbArray("statusServer",$status,$arrayStatus,"-- STATUS --","class='form-control' ");
    $formEdit = "
    <div id='page-title'>
      <h2>Referensi Server Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Server</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Server' id='namaServer' name='namaServer' value='$nama_server'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Nama Alias</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Nama Alias' id='aliasServer' name='aliasServer' value='$alias'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Alamat IP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Alamat IP' id='alamatIP' name='alamatIP' value='$alamat_ip'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>User FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='User FTP' id='userFTP' name='userFTP' value='$user_ftp'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>Password FTP</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='Password FTP' id='passwordFTP' name='passwordFTP' value='$password_ftp'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>FTP PORT</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PORT FTP' id='ftpPort' name='ftpPort' value='$port_ftp'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>USER MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='USER MYSQL' id='userMysql' name='userMysql' value='$user_mysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PASSWORD MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='PASSWORD MYSQL' id='passwordMysql' name='passwordMysql' value='$password_mysql'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-1 control-label'>PORT MYSQL</label>
                        <div class='col-sm-11'>
                            <input type='text' class='form-control'  placeholder='POT MYSQL' id='portMysql' name='portMysql' value='$port_mysql'>
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
$bugReport = new bugReport();


 ?>
