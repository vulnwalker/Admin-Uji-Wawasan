<?php
session_start();
class bandingServerFile extends baseObject{
  var $Prefix = "bandingServerFile";
  var $formName = "bandingServerFileForm";
  var $tableName = "ref_server";
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
  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){

      case 'submitCheck':{
        $jsonFile = $this->listFileRelease($this->idServer,$idRelease);
        if(empty($kurunTanggal)){
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
          $getIdFileChek = $this->sqlArray($this->sqlQuery("select max(id) from json_file_check where username = '$this->username'"));
          $decodeJSON = json_decode($jsonFileFilter);
          $content = array("jumlahData" => sizeof($decodeJSON),"idFileCheck" => $getIdFileChek['max(id)'],
          'tableResult' => $this->tableResultBody($getIdFileChek['max(id)'])
        );
        }else{
          $explodeKurunTanggal = explode(" - ",$kurunTanggal);
          $tanggalMulai = $this->dateToNumber($this->generateDate($explodeKurunTanggal[0])."0000");
          $tanggalSelesai = $this->dateToNumber($this->generateDate($explodeKurunTanggal[1])."2359");
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $dateModified = $this->dateConversion($decodeJsonFile[$i]->tanggal);
            if($this->dateToNumber($dateModified) <= $tanggalSelesai &&  $this->dateToNumber($dateModified) >= $tanggalMulai && $this->filterExtension($decodeJsonFile[$i]->file) == 0 ){
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
          $getIdFileChek = $this->sqlArray($this->sqlQuery("select max(id) from json_file_check where username = '$this->username'"));
          $decodeJSON = json_decode($jsonFileFilter);
          $content = array("jumlahData" => sizeof($decodeJSON),"idFileCheck" => $getIdFileChek['max(id)'],
          'tableResult' => $this->tableResultBody($getIdFileChek['max(id)'])
        );

        }

  		break;
  		}
      case 'showList':{
        $jsonFile = $this->listFileRelease($this->idServer,$idRelease);
        if(empty($kurunTanggal)){
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          $nomorUrut = 1;
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $dateModified = $this->dateConversion($decodeJsonFile[$i]->tanggal);
            if($this->filterExtension($decodeJsonFile[$i]->file) == 0 ){
              if(!empty($filterFolder)){
                  $cek.= $decodeJsonFile[$i]->file."=>".$this->unFilterExtension($decodeJsonFile[$i]->file,$filterFolder)."\n";
                  if($this->unFilterExtension($decodeJsonFile[$i]->file,$filterFolder) != 0){
                    $tableResult .= "
                      <tr>
                        <td>$nomorUrut</td>
                        <td></td>
                        <td>".$decodeJsonFile[$i]->file."</td>
                        <td>$dateModified</td>
                        <td></td>
                        <td></td>
                      </tr>
                      ";
                      $nomorUrut += 1;
                  }
              }else{
                $tableResult .= "
                  <tr>
                    <td>$nomorUrut</td>
                    <td></td>
                    <td>".$decodeJsonFile[$i]->file."</td>
                    <td>$dateModified</td>
                    <td></td>
                    <td></td>
                  </tr>
                  ";
                  $nomorUrut += 1;
              }

            }
          }
          $jsonFileFilter = json_encode($arrayFile);
          $content = array("tableResult" => $tableResult);
        }else{
          $explodeKurunTanggal = explode(" - ",$kurunTanggal);
          $tanggalMulai = $this->dateToNumber($this->generateDate($explodeKurunTanggal[0])."0000");
          $tanggalSelesai = $this->dateToNumber($this->generateDate($explodeKurunTanggal[1])."2359");
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          $nomorUrut = 1;
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $dateModified = $this->dateConversion($decodeJsonFile[$i]->tanggal);
            if($this->dateToNumber($dateModified) <= $tanggalSelesai &&  $this->dateToNumber($dateModified) >= $tanggalMulai && $this->filterExtension($decodeJsonFile[$i]->file) == 0  ){
              if(!empty($filterFolder)){
                  if($this->unFilterExtension($decodeJsonFile[$i]->file,$filterFolder) != 0){
                    $tableResult .= "
                      <tr>
                        <td>$nomorUrut</td>
                        <td></td>
                        <td>".$decodeJsonFile[$i]->file."</td>
                        <td>$dateModified</td>
                        <td></td>
                        <td></td>
                      </tr>
                      ";
                      $nomorUrut += 1;
                  }
              }else{
                $tableResult .= "
                  <tr>
                    <td>$nomorUrut</td>
                    <td></td>
                    <td>".$decodeJsonFile[$i]->file."</td>
                    <td>$dateModified</td>
                    <td></td>
                    <td></td>
                  </tr>
                  ";
                  $nomorUrut += 1;
              }
            }
          }
          $jsonFileFilter = json_encode($arrayFile);
          $content = array("tableResult" => $tableResult);

        }

  		break;
  		}

      case 'prosesCheck':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $getDataTarget = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$targetDir'"));
          $getDataSumber = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$sumberDir'"));
          $getDataFileCheck = $this->sqlArray($this->sqlQuery("select * from json_file_check where id ='$idFileCheck'"));
          $explodeListFile = json_decode($getDataFileCheck['isi']);
          $dateModified = $this->dateConversion($explodeListFile[$nomorUrut - 1]->tanggal);
          $fileLocation = $explodeListFile[$nomorUrut - 1]->file;
          $fileBanding = str_replace($getDataSumber['directory'],"",$explodeListFile[$nomorUrut - 1]->file);
          $fileTarget = str_replace($getDataSumber['directory'],$getDataTarget['directory']."/",$explodeListFile[$nomorUrut - 1]->file);
          $fileSize = $explodeListFile[$nomorUrut - 1]->size;
          $dateModifiedTarget = str_replace('.','',date("Y-m-d H:i.", filemtime($fileTarget)));

          $jsonFileTarget = $this->getJsonFileTarget($explodeListFile[$nomorUrut - 1]->file,$serverTarget);
          $decodeJSONFileTarget = json_decode($jsonFileTarget);

          $error = array();
          if($fileSize != $decodeJSONFileTarget[0]->size){
            $error[]= " SIZE FILE BEDA ";
          }
          if($dateModified != $dateModifiedTarget){
            $error[]= "TANGGAL BEDA ";
          }
          if(sizeof($error) == 0){
            $cek =  " $fileBanding => OK ";
            $error = "";
            $status = "OK";
          }else{
            $error = implode(", ",$error);
            $cek =  " $fileBanding =>  $error";
            $status = "ERROR";
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $cek,
        'namaFile' => $fileBanding,
        'fileLocation' => $fileLocation,
        'tanggalDiServer' => $dateModified,
        'reason' => $error,
        'status' => $status,

        );
  		break;
  		}
      case 'pushFileSelected':{
          $content = array("namaFile" => $bandingServerFile_cb[0] ,"jumlahData" => sizeof($bandingServerFile_cb));

      break;
      }
      case 'prosesPush':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $namaFile = $bandingServerFile_cb[$nomorUrut - 1];
          $this->pushFileSelected($serverTarget,$namaFile);
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $cek,
        'namaFile' => $namaFile,
        'fileLocation' => $fileLocation,
        'reason' => $error,

        );
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
    $this->username = $_SESSION['username'];
    if(!isset($_GET['API'])){
          echo $this->pageShow();
    }else{
       echo $this->jsonGenerator();
    }
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
  function loadScript(){
    return "
    <script type='text/javascript' src='js/bandingServerFile/bandingServerFile.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/daterangepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/moment.js'></script>
    <script type='text/javascript'>
    $( document ).ready(function() {
          bandingServerFile.createDialog();
          $('#kurunTanggal').daterangepicker({
              format: 'DD-MM-YYYY'
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
    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $cmbRelease  =  $this->cmbQuery("idRelease","","select id,nama_release from ref_release "," class='form-control'","-- RELEASE --");
    $cmbTarget  =  $this->cmbQuery("serverTarget","","select id,nama_server from ref_server "," class='form-control'","-- TARGET SERVER --");
    $pageContent = "
    <div id='page-title'>
      <h2>CHECK SERVER FILE</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
            <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>RELEASE</label>
                    <div class='col-sm-11'>
                        $cmbRelease
                    </div>
                </div>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>FILTER WORD</label>
                    <div class='col-sm-11'>
                        <textarea id='filterFolder' name='filterFolder' class='form-control'></textarea>
                    </div>
                </div>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>SERVER</label>
                    <div class='col-sm-11'>
                        $cmbTarget
                    </div>
                </div>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>Tanggal</label>
                    <div class='col-sm-11'>
                        <input type='text' name='kurunTanggal' id='kurunTanggal' placeholder='Kurun Tanggal' class='float-left mrg10R form-control hasDatepicker'>
                    </div>
                </div>

                <div class='form-group' style='float:right;'>
                    <div class='col-sm-12'>
                    <button type='button' onclick=$this->Prefix.showList(); class='btn btn-alt btn-hover btn-success'>
                        <span>Tampilkan</span>
                        <i class='glyph-icon icon-save'></i>
                    </button>
                    <button type='button' onclick=$this->Prefix.submitCheck(); class='btn btn-alt btn-hover btn-success'>
                        <span>Check</span>
                        <i class='glyph-icon icon-save'></i>
                    </button>

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
                <div class='form-group' >
                  ".$this->tableResult()."
                </div>



            </form>
          </div>
      </div>
    </div>

    ";
    return $pageContent;
  }

  function tableResult(){
    $tableContent = "
      <table class='table table-bordered table-striped table-condensed table-hover' style='font-size:11px;' id='tableResult'>
        <thead>
          <tr>
              <th width='20'>No</th>
              <th width='20'>".$this->checkAll(25,$this->Prefix)."</th>
              <th width='300'>File</th>
              <th width='100'>Tanggal Sumber</th>
              <th width='50'>Status</th>
              <th width='500'>Keterangan</th>
          </tr>
        </thead>
          <tbody>

          </tbody>
      </table>
      <div class='form-group' style='float:right;'>
          <div class='col-sm-12'>
          <span id='tempatButtonPush'></span>

          </div>
      </div>
    ";
    return $tableContent;
  }
  function tableResultBody($idFileCheck){
    $tableContent = "

    ";
    return $tableContent;
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
$bandingServerFile = new bandingServerFile();


 ?>
