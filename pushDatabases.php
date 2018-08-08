<?php
include "base/config.php";
include "base/baseObject.php";
class pushDatabases extends baseObject{
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
        $content = array("idEdit" => $pushDatabases_cb[0]);
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
        for ($i=0; $i < sizeof($pushDatabases_cb) ; $i++) {
          $query = "delete from $this->tableName where id = '".$pushDatabases_cb[$i]."'";
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
    $optionSourceDB = getopt(null, ["sourceDB:"]);
    $sourceDB = $optionSourceDB["sourceDB"];
    $listIDServer = getopt(null, ["idServer:"]);
    $listIDServer = explode(",",$listIDServer['idServer']);
    $idRelease = getopt(null, ["idRelease:"]);
    $idRelease = $idRelease['idRelease'];
    for ($i=0; $i < sizeof($listIDServer) ; $i++) {
      if(empty($sourceDB)){
        $this->pushRelease($idRelease,$listIDServer[$i]);
      }else{
        echo "source DB = ".$sourceDB." | ID SERVER = ".implode(",",$listIDServer)." | DATABASE NAME = ".$databaseName ." | ID RELEASE = ".$idRelease;
      }
    }

  }

  function pushRelease($idRelease,$idServerTujuan){
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $getDataServerTujuan = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$idServerTujuan'"));
    $getDataServerLocal = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$this->idServer'"));


  }
  function listStruktur($id,$databaseName) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"tableName\":\""$1"\",", "\"columnName\":\""$2"\",", "\"typeData\":\""$3"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -s -e 'select TABLE_NAME,COLUMN_NAME,COLUMN_TYPE from information_schema.COLUMNS where TABLE_SCHEMA= $stringDatabaseName;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
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
$pushDatabases = new pushDatabases();


 ?>
