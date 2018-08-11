<?php
class refAds extends baseObject{
  var $Prefix = "refAds";
  var $formName = "refAdsForm";
  var $tableName = "type_iklan";
  var $username = "";
  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){

      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "nama like '%$filterCari%' ";
          $arrKondisi[] = "directory_location like '%$filterCari%' ";
          $kondisi = join(" or ",$arrKondisi);
          $kondisi = " where $kondisi ";
        }
        $cek = "select * from $this->tableName $kondisi";
        $content=array('tableContent' => $this->generateTable($kondisi));
  		break;
  		}
      case 'Baru':{
        $content = $this->Baru();
  		break;
  		}
      case 'Edit':{
        $content = $this->Edit($refAds_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($idSession)){
          $err = "Pilih Session";
        }elseif(empty($namaHadiah)){
          $err = "Isi Nama Hadiah";
        }elseif(empty($deskripsiHadiah)){
          $err = "Isi Deskripsi";
        }
        if(empty($err)){
          $dataInsert = array(
            'id_session' => $idSession,
            'nama' => $namaHadiah,
            'deskripsi' => $deskripsiHadiah,
            'gambar' => $this->putImage($gambarHadiah,"images/hadiah/".md5($namaHadiah).".jpg"),
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($jenisIklan)){
          $err = "Pilih Jenis Iklan";
        }elseif(empty($energiIklan)){
          $err = "Isi Energi";
        }elseif(empty($delayIklan)){
          $err = "Isi Delay";
        }elseif(empty($adUnit)){
          $err = "Isi Ad Unit";
        }
        if(empty($err)){
          $dataInsert = array(
            'jenis_iklan' => $jenisIklan,
            'energi' => $energiIklan,
            'delay' => $delayIklan,
            'ad_unit' => $adUnit,
          );
          $query = $this->sqlUpdate($this->tableName,$dataInsert,"id ='$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refAds_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refAds_cb[$i]."'";
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
    <script type='text/javascript' src='js/refAds/refAds.js'></script>
    <script type='text/javascript' src='https://cdn.jsdelivr.net/momentjs/latest/moment.min.js'></script>
    <script type='text/javascript' src='https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js'></script>
    <link rel='stylesheet' type='text/css' href='https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' />
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
      <h2>HADIAH</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable("")."
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
          <th style='width:20px !important;'>No</th>
          <th width='20' style='text-align:center;'>".$this->checkAll(25,$this->Prefix)."</th>
          <th width='400'>Jenis Iklan </th>
          <th width='100'Energi </th>
          <th width='100'>Delay </th>
          <th width='200'>Ad Unit </th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }
    $getDataSession = $this->sqlArray($this->sqlQuery("select * from sesion where id = '$id_session'"));
    $namaHadiah = $getDataSession['judul'];
    $tableRow = "
    <tr class='$classRow'>
        <td style='vertical-align:middle;text-align:center;'>$no</td>
        <td style='text-align:center;vertical-align:middle;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td style='vertical-align:middle;'>$jenis_iklan</td>
        <td style='vertical-align:middle;'>$energi</td>
        <td style='vertical-align:middle;'>$delay</td>
        <td style='vertical-align:middle;'>$ad_unit</td>

    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from $this->tableName ".$kondisiTable);
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

  function Baru(){
    $formCaption = "Baru";
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalForm' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
      <form name='".$this->formName."_input' id='".$this->formName."_input'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$formCaption</h4>
                </div>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Session</label>
                      <div class='col-sm-9'>
                        ".$this->cmbArray("jenisIklan","",array(
                          array("BANNER","BANNER"),
                          array("POPUP","POPUP"),
                          array("POPUP","POPUP"),
                        ),"-- JENIS IKLAN --")."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Hadiah</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'namaHadiah',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Deskripsi</label>
                      <div class='col-sm-9'>
                        ".$this->textArea(array(
                          "id" => 'deskripsiHadiah',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Gambar</label>
                      <div class='col-sm-9'>
                        <div class='fileinput fileinput-new' data-provides='fileinput'>
                          <div class='fileinput-preview thumbnail' data-trigger='fileinput' style='width: 200px; height: 150px;'><img id='thumbnailImages'></img></div>
                            <div>
                                <span class='btn btn-default btn-file'>
                                    <span class='fileinput-image'>Select image</span>
                                    <span class='fileinput-exists'>Change</span>
                                    <input type='hidden' name='gambarHadiah' id='gambarHadiah' >
                                    <input type='file' id='fileInputHadiah' onChange=$this->Prefix.imageChanged(); accept='image/x-png,image/gif,image/jpeg'>
                                </span>
                                <a href='#' class='btn btn-default fileinput-exists' data-dismiss='fileinput'>Remove</a>
                            </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <div class='modal-footer'>
                    <input type='hidden' name='idEdit' id='idEdit' value=''>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveNew();>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
      </form>
    </div>";

    return $content;
  }
  function Edit($idEdit){
    $formCaption = "Edit";
    $getDataEdit = $this->sqlArray($this->sqlQuery("select * from $this->tableName where id = '$idEdit'"));
    $content="
    <div class='modal fade bs-example-modal-lg' id='modalForm' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
      <form name='".$this->formName."_input' id='".$this->formName."_input'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$formCaption</h4>
                </div>
                <div class='modal-body'>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Jenis Iklan</label>
                      <div class='col-sm-9'>
                        ".$this->cmbArray("jenisIklan","",array(
                          array("BANNER","BANNER"),
                          array("POPUP","POPUP"),
                          array("POPUP","POPUP"),
                        ),"-- JENIS IKLAN --","class='form-control'")."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Energi</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'energiIklan',
                          "value" => $getDataEdit['energi'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Delay</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'delayIklan',
                          "value" => $getDataEdit['delay'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Ad Unit</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'adUnit',
                          "value" => $getDataEdit['ad_unit'],
                        ))."
                      </div>
                    </div>
                  </div>
                <div class='modal-footer'>
                    <input type='hidden' name='idEdit' id='idEdit' value='$idEdit'>
                    <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveEdit();>Simpan</button>
                    <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                </div>
            </div>
        </div>
      </form>
    </div>";

    return $content;
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

}
$refAds = new refAds();


 ?>
