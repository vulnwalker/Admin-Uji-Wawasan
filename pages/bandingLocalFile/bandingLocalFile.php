<?php
session_start();
class bandingLocalFile extends baseObject{
  var $Prefix = "bandingLocalFile";
  var $formName = "bandingLocalFileForm";
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
  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){

      case 'submitCheck':{
        $jsonFile = $this->listFileLocal($this->idServer,$sumberDir);
        if(empty($kurunTanggal)){
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $dateModified = $this->dateConversion($decodeJsonFile[$i]->tanggal);
            if ($this->filterExtension($decodeJsonFile[$i]->file) == 0) {
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
          file_put_contents("/tmp/checkFile.txt",$jsonFileFilter);

          // $dataFileCheck = array(
          //   'isi' => $jsonFileFilter,
          //   'username' => $this->username,
          //   'tanggal' => date("Y-m-d"),
          //   'jam' => date("H:i"),
          // );
          // $this->sqlQuery($this->sqlInsert("json_file_check",$dataFileCheck));
          // $getIdFileChek = $this->sqlArray($this->sqlQuery("select max(id) from json_file_check where username = '$this->username'"));
          $decodeJSON = json_decode($jsonFileFilter);
          $content = array("jumlahData" => sizeof($decodeJSON),"idFileCheck" => 1,
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
            if($this->dateToNumber($dateModified) <= $tanggalSelesai &&  $this->dateToNumber($dateModified) >= $tanggalMulai && $this->filterExtension($decodeJsonFile[$i]->file) == 0){
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
        $jsonFile = $this->listFileLocal($this->idServer,$sumberDir);
        if(empty($kurunTanggal)){
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          $nomorUrut = 1;
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $dateModified = $this->dateConversion($decodeJsonFile[$i]->tanggal);
            if($this->filterExtension($decodeJsonFile[$i]->file) == 0){
              if(!empty($filterFolder)){
                  if($this->unFilterExtension($decodeJsonFile[$i]->file,$filterFolder) != 0){
                    $tableResult .= "
                      <tr>
                        <td>$nomorUrut</td>
                        <td></td>
                        <td>".$decodeJsonFile[$i]->file."</td>
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
          // $getDataFileCheck = $this->sqlArray($this->sqlQuery("select * from json_file_check where id ='$idFileCheck'"));
          $explodeListFile = json_decode(file_get_contents("/tmp/checkFile.txt"));
          $dateModified = $this->dateConversion($explodeListFile[$nomorUrut - 1]->tanggal);
          $fileLocation = $explodeListFile[$nomorUrut - 1]->file;
          $fileBanding = str_replace($getDataSumber['directory'],"",$explodeListFile[$nomorUrut - 1]->file);
          $fileTarget = str_replace($getDataSumber['directory'],$getDataTarget['directory']."/",$explodeListFile[$nomorUrut - 1]->file);
          $fileSize = $explodeListFile[$nomorUrut - 1]->size;
          $dateModifiedTarget = str_replace('.','',date("Y-m-d H:i.", filemtime($fileTarget)));
          $error = array();
          if($fileSize != filesize($fileTarget)){
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
        'reason' => $error,
        'status' => $status,

        );
  		break;
  		}
      case 'pushFileSelected':{
          $content = array("namaFile" => $bandingLocalFile_cb[0] ,"jumlahData" => sizeof($bandingLocalFile_cb));

      break;
      }
      case 'prosesPush':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $namaFile = $bandingLocalFile_cb[$nomorUrut - 1];
          $getDirSumber = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$sumberDir'"));
          $getDirTarget = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$targetDir'"));
          $directorySumber = $getDirSumber['directory'];
          $directoryTarget = $getDirTarget['directory'];
          $this->copyFile($directorySumber.$namaFile,$directoryTarget.$namaFile);
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
  function listFileLocal($id,$idSumber) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $getDataSumber = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$idSumber'"));
    $dirSumber = $getDataSumber['directory'];
    $contentAWK = '{ print "{\"file\":\""$4"\",\"tanggal\":\""$3"\",\"size\":\""$2"\"} "}';
    $prinst = '"%-8g %8s %-.22T+ %p\n"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"find $dirSumber -type f -printf $prinst | sort -r | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  // function listFileLocal($id,$idSumber) {
  //   $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
  //   $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
  //   $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
  //   $getDataSumber = $this->sqlArray($this->sqlQuery("select * from ref_dir_check where id = '$idSumber'"));
  //   $dirSumber = $getDataSumber['directory'];
  //   $contentAWK = '{ print "{\"file\":\""$8"\",\"tanggal\":\""$6"-"$5"-"$7"\",\"size\":\""$4"\"} "}';
  //   $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"find $dirSumber -type f | xargs ls --full-time -r -oAHd | awk '$contentAWK'"));
  //   return "[".str_replace(" ","",$comand)."]";
  // }
  function loadScript(){
    return "
    <script type='text/javascript' src='js/bandingLocalFile/bandingLocalFile.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/daterangepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/moment.js'></script>
    <script type='text/javascript'>
    $( document ).ready(function() {
          bandingLocalFile.createDialog();
          $('#kurunTanggal').daterangepicker({
              format: 'DD-MM-YYYY'
          });
    });

    </script>
    ";
  }
//   function sideBar(){
//     $sideBar = "
//     <div id='page-sidebar' style='height: 3195px;'>
//     <div class='scroll-sidebar' style='height: 3195px;'>
//       <ul id='sidebar-menu' class='sf-js-enabled sf-arrows'>
//       <li class='header'><span>Admin</span></li>
//       <li >
//           <a href='pages.php' title='Dashboard'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>Dashboard</span>
//           </a>
//       </li>
//       <li class='divider'></li>
//       <li class='header'><span>Server</span></li>
//       <li >
//           <a href='pages.php?page=bandingLocalFile' title='Referensi Server'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>Referensi Server</span>
//           </a>
//       </li>
//       <li>
//           <a href='pages.php?page=fileManager' title='fileManager'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>File Manager</span>
//           </a>
//       </li>
//       <li>
//           <a href='pages.php?page=refRelease' title='refRelease'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>Referensi Release</span>
//           </a>
//       </li>
//       <li>
//           <a href='pages.php?page=historyBackup' title='historyBackup'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>History Backup</span>
//           </a>
//       </li>
//       <li>
//           <a href='pages.php?page=refDirCheck' title='refDirCheck'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>Referensi Check File</span>
//           </a>
//       </li>
//       <li class='activeSidebar'>
//           <a href='pages.php?page=bandingLocalFile' title='bandingLocalFile'>
//               <i class='glyph-icon icon-linecons-tv'></i>
//               <span>Banding Local File</span>
//           </a>
//       </li>
//       </ul>
//     </div>
// </div>
//     ";
//     return $sideBar;
//   }
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
    $cmbSumber  =  $this->cmbQuery("sumberDir","","select id,nama from ref_dir_check ","onchange=$this->Prefix.diskChanged() class='form-control'","-- SUMBER --");
    $cmbTarget  =  $this->cmbQuery("targetDir","","select id,nama from ref_dir_check ","onchange=$this->Prefix.diskChanged() class='form-control'","-- TARGET --");
    $pageContent = "
    <div id='page-title'>
      <h2>CHECK FILE LOCAL</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <div class='example-box-wrapper'>
            <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>SUMBER</label>
                    <div class='col-sm-11'>
                        $cmbSumber
                    </div>
                </div>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>TARGET</label>
                    <div class='col-sm-11'>
                        $cmbTarget
                    </div>
                </div>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>FILTER WORD</label>
                    <div class='col-sm-11'>
                        <textarea id='filterFolder' name='filterFolder' class='form-control'></textarea>
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
              <th width='50'>STATUS</th>
              <th width='500'>Reason</th>
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
$bandingLocalFile = new bandingLocalFile();


 ?>
