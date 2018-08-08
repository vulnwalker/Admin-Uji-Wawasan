<?php
class refDirCheck extends baseObject{
  var $Prefix = "refDirCheck";
  var $formName = "refDirCheckForm";
  var $tableName = "ref_dir_check";

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
        $content = $this->formEdit($refDirCheck_cb[0]);
  		break;
  		}
      case 'formBaru':{
        $content = $this->formBaru();
  		break;
  		}
      case 'ManageDisk':{
        $content = array("idEdit" => $refDirCheck_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaAplikasi)){
          $err = "Isi Nama Aplikasi";
        }elseif(empty($lokasiFolder)){
          $err = "Isi Lokasi Folder";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama' => $namaAplikasi,
            'directory' => $lokasiFolder,
          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($namaAplikasi)){
          $err = "Isi Nama Aplikasi";
        }elseif(empty($lokasiFolder)){
          $err = "Isi Lokasi Folder";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama' => $namaAplikasi,
            'directory' => $lokasiFolder,
          );
          $query = $this->sqlUpdate($this->tableName,$dataInsert,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refDirCheck_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refDirCheck_cb[$i]."'";
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
    <script type='text/javascript' src='js/bandingLocalFile/refDirCheck.js'></script>
    <script type='text/javascript'>
    $( document ).ready(function() {
          refDirCheck.createDialog();
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
    $pageContent = "
    <div id='page-title'>
      <h2>REFERENSI CHECK</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
                  ".$this->generateTable()."
          </div>
      </div>
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
          <th style='width:200px;'>Nama Aplikasi</th>
          <th style='width:800px;'>Directory</th>
      </tr>
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
        $$key = $value;
    }

    $tableRow = "
    <tr class='$classRow'>
        <td>$no</td>
        <td style='text-align:center;'>".$this->setCekBox($no - 1,$id,$this->Prefix)."</td>
        <td>$nama</td>
        <td>$directory</td>
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

  function formBaru(){
    $this->formCaption = "Folder Baru";
    $arrayStatus = array(
      array('1','LIVE'),
      array('2','ERROR'),
      array('3','DIE'),
    );
    $comboStatus = $this->cmbArray("statusServer","",$arrayStatus,"-- STATUS --","class='form-control' ");
    $formBaru = "
    <div class='modal fade bs-example-modal-lg' id='modalForm' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$this->formCaption</h4>
                </div>
                <div class='modal-body'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Nama</label>
                        <div class='col-sm-10'>
                            <input type='text' class='form-control'  placeholder='Nama' id='namaAplikasi' name='namaAplikasi'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Lokasi Folder</label>
                        <div class='col-sm-10'>
                            <input type='text' class='form-control'  placeholder='Lokasi Folder' id='lokasiFolder' name='lokasiFolder'>
                        </div>
                    </div>

                </form>
                </div>
                <div class='modal-footer'>
                  <div class='form-group' style='float:right;'>
                      <div class='col-sm-12'>
                      <button type='button' onclick=$this->Prefix.saveNew(); class='btn btn-alt btn-hover btn-success'>
                          <span>Simpan</span>
                          <i class='glyph-icon icon-save'></i>
                      </button>
                      <button type='button' class='btn btn-danger' data-dismiss='modal' id='closeModal'>Batal</button>
                      </div>
                  </div>
                </div>
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
    <div class='modal fade bs-example-modal-lg' id='modalForm' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                    <h4 class='modal-title'>$this->formCaption</h4>
                </div>
                <div class='modal-body'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Nama</label>
                        <div class='col-sm-10'>
                            <input type='text' class='form-control'  placeholder='Nama' id='namaAplikasi' name='namaAplikasi'  value='$nama'>
                        </div>
                    </div>
                    <div class='form-group'>
                        <label class='col-sm-2 control-label'>Lokasi Folder</label>
                        <div class='col-sm-10'>
                            <input type='text' class='form-control'  placeholder='Lokasi Folder' id='lokasiFolder' name='lokasiFolder' value='$directory'>
                        </div>
                    </div>
                </form>
                </div>
                <div class='modal-footer'>
                  <div class='form-group' style='float:right;'>
                      <div class='col-sm-12'>
                      <button type='button' onclick=$this->Prefix.saveEdit($idEdit); class='btn btn-alt btn-hover btn-success'>
                          <span>Simpan</span>
                          <i class='glyph-icon icon-save'></i>
                      </button>
                      <button type='button' class='btn btn-danger' data-dismiss='modal' id='closeModal'>Batal</button>
                      </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    ";
    return $formEdit;
  }


}
$refDirCheck = new refDirCheck();


 ?>
