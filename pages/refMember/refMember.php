<?php
class refMember extends baseObject{
  var $Prefix = "refMember";
  var $formName = "refMemberForm";
  var $tableName = "member";
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
          // $arrKondisi[] = "tanggal_release like '%".$this->generateDate($filterCari)."%' ";
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
        $content = $this->Edit($refMember_cb[0]);
  		break;
  		}
      case 'saveNew':{
  			if(empty($namaMember)){
          $err = "Isi Nama Member";
        }elseif(empty($emailMember)){
          $err = "Isi Email Member";
        }elseif(empty($passwordMember)){
          $err = "Isi Password Member";
        }elseif(empty($alamatMember)){
          $err = "Isi Alamat Member";
        }elseif(empty($nomorTelepon)){
          $err = "Isi Nomor Telepon";
        }elseif(empty($pointMember)){
          $err = "Isi Point";
        }elseif(empty($energiMember)){
          $err = "Isi Energi";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama' => $namaMember,
            'email' => $emaiMember,
            'password' => $passwordMember,
            'alamat' => $alamatMember,
            'no_telpn' => $nomorTelepon,
            'point' => $pointMember,
            'akses' => $statusMember,
            'energi' => $energiMember,

          );
          $query = $this->sqlInsert($this->tableName,$dataInsert);
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($namaMember)){
          $err = "Isi Nama Member";
        }elseif(empty($emailMember)){
          $err = "Isi Email Member";
        }elseif(empty($passwordMember)){
          $err = "Isi Password Member";
        }elseif(empty($alamatMember)){
          $err = "Isi Alamat Member";
        }elseif(empty($nomorTelepon)){
          $err = "Isi Nomor Telepon";
        }elseif(empty($pointMember)){
          $err = "Isi Point";
        }elseif(empty($energiMember)){
          $err = "Isi Energi";
        }
        if(empty($err)){
          $dataInsert = array(
            'nama' => $namaMember,
            'email' => $emaiMember,
            'password' => $passwordMember,
            'alamat' => $alamatMember,
            'no_telpn' => $nomorTelepon,
            'point' => $pointMember,
            'akses' => $statusMember,
            'energi' => $energiMember,

          );
          $query = $this->sqlUpdate($this->tableName,$dataInsert,"id = '$idEdit'");
          $this->sqlQuery($query);
          $cek = $query;
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($refMember_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$refMember_cb[$i]."'";
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
    <script type='text/javascript' src='js/refMember/refMember.js'></script>
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
      <h2>MEMBER</h2>
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
          <th width='200'>Nama </th>
          <th width='100'>Email</th>
          <th width='100'>Password</th>
          <th width='100'>Alamat</th>
          <th width='100'>Telepon</th>
          <th width='100'>Point</th>
          <th width='100'>Energi</th>
          <th width='100'>Status</th>
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
        <td>$nama</td>
        <td>$email</td>
        <td>$password</td>
        <td>$alamat</td>
        <td>$no_telpn</td>
        <td>$point</td>
        <td>$energi</td>
        <td>$akses</td>

    </tr>
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from member ".$kondisiTable);
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
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Member</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'namaMember',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Email</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'emailMember',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Password</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'passwordMember',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Alamat</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'alamatMember',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Nomor Telepon</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'nomorTelepon',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Point</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'pointMember',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Energi</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'energiMember',
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Status</label>
                      <div class='col-sm-9'>
                        ".$this->cmbArray("statusMember","ACTIVE",array(array("ACTIVE","ACTIVE"),array("BLOCK","BLOCK")),"-- STATUS --","class='form-control'")."
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
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Nama Member</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'namaMember',
                          "value" => $getDataEdit['nama'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Email</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'emailMember',
                          "value" => $getDataEdit['email'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Password</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'passwordMember',
                          "value" => $getDataEdit['password'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Alamat</label>
                      <div class='col-sm-9'>
                        ".$this->textBox(array(
                          "id" => 'alamatMember',
                          "value" => $getDataEdit['alamat'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Nomor Telepon</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'nomorTelepon',
                          "value" => $getDataEdit['no_telpn'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Point</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'pointMember',
                          "value" => $getDataEdit['point'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Energi</label>
                      <div class='col-sm-9'>
                        ".$this->numberText(array(
                          "id" => 'energiMember',
                          "value" => $getDataEdit['energi'],
                        ))."
                      </div>
                    </div>
                  </div>
                  <div class='form-group'>
                    <div class='row'>
                      <label class='col-sm-3 control-label' style='margin-top:6px;'>Status</label>
                      <div class='col-sm-9'>
                        ".$this->cmbArray("statusMember",$getDataEdit['akses'],array(array("ACTIVE","ACTIVE"),array("BLOCK","BLOCK")),"-- STATUS --","class='form-control'")."
                      </div>
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
$refMember = new refMember();


 ?>
