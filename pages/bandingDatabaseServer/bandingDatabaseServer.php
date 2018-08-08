<?php
session_start();
class bandingDatabaseServer extends baseObject{
  var $Prefix = "bandingDatabaseServer";
  var $formName = "bandingDatabaseServerForm";
  var $tableName = "ref_server";
  var $idServer = 1000;
  var $username = "";
  var $blockListExtension = array(
      array('.bck'),
      array('bck'),
      array(',bck'),
      array('.BCK'),
      array('.backup'),
      array('.201'),
      array('201'),
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

      case 'checkStruktur':{
        $jsonFile = $this->listStruktur($this->idServer,$idRelease);
        $decodeJsonFile = json_decode($jsonFile);
        $arrayFile = array();
        for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
          if ($this->filterExtension($decodeJsonFile[$i]->tableName) == 0) {
            if(!empty($filterWord)){
                if($this->unFilterExtension($decodeJsonFile[$i]->tableName,$filterWord) != 0){
                    $arrayFile[] = array(
                            'tableName' => $decodeJsonFile[$i]->tableName,
                            'columnName' => $decodeJsonFile[$i]->columnName,
                            'typeData' => $decodeJsonFile[$i]->typeData,
                    );
                }
            }else{
              $arrayFile[] = array(
                      'tableName' => $decodeJsonFile[$i]->tableName,
                      'columnName' => $decodeJsonFile[$i]->columnName,
                      'typeData' => $decodeJsonFile[$i]->typeData,
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


  		break;
  		}
      case 'checkTriger':{
        $jsonFile = $this->listTriger($this->idServer,$idRelease);
        $decodeJsonFile = json_decode($jsonFile);
        $arrayFile = array();
        for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
          if ($this->filterExtension($decodeJsonFile[$i]->trigerName) == 0) {
            if(!empty($filterWord)){
                if($this->unFilterExtension($decodeJsonFile[$i]->trigerName,$filterWord) != 0){
                    $arrayFile[] = array(
                            'trigerName' => $decodeJsonFile[$i]->trigerName,
                            'actionTriger' => str_replace("\n","",$decodeJsonFile[$i]->actionTriger),
                            'actionTiming' => $decodeJsonFile[$i]->actionTiming,
                            'eventManipulation' => $decodeJsonFile[$i]->eventManipulation,
                            'tableName' => $decodeJsonFile[$i]->tableName,
                    );
                }
            }else{
              $arrayFile[] = array(
                      'trigerName' => $decodeJsonFile[$i]->trigerName,
                      'actionTriger' => str_replace("\n","",$decodeJsonFile[$i]->actionTriger),
                      'actionTiming' => $decodeJsonFile[$i]->actionTiming,
                      'eventManipulation' => $decodeJsonFile[$i]->eventManipulation,
                      'tableName' => $decodeJsonFile[$i]->tableName,
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


  		break;
  		}
      case 'checkRoutine':{
        $jsonFile = $this->listRoutine($this->idServer,$idRelease);
        $decodeJsonFile = json_decode($jsonFile);
        $arrayFile = array();
        for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
          $arrayRoutineParameters = array();
          if ($this->filterExtension($decodeJsonFile[$i]->routineName) == 0) {
            if(!empty($filterWord)){
                if($this->unFilterExtension($decodeJsonFile[$i]->routineName,$filterWord) != 0){
                    $jsonRoutineParameter = $this->listParameter($this->idServer,$idRelease,$decodeJsonFile[$i]->routineName,$decodeJsonFile[$i]->routineType);
                    $decodeJSONParameter = json_decode($jsonRoutineParameter);
                    for ($a=0; $a < sizeof($decodeJSONParameter); $a++) {
                      $arrayRoutineParameters[] = array(
                          'parameterName' => $decodeJSONParameter[$a]->parameterName,
                          'typeData' => $decodeJSONParameter[$a]->typeData,
                          'paramsPosition' => $decodeJSONParameter[$a]->paramsPosition,
                      );
                    }
                    $arrayFile[] = array(
                            'routineName' => $decodeJsonFile[$i]->routineName,
                            'actionRoutine' => $decodeJsonFile[$i]->actionRoutine,
                            'routineType' => $decodeJsonFile[$i]->routineType,
                            'routineParameters' => $arrayRoutineParameters ,
                    );
                }
            }else{
              $jsonRoutineParameter = $this->listParameter($this->idServer,$idRelease,$decodeJsonFile[$i]->routineName,$decodeJsonFile[$i]->routineType);
              $decodeJSONParameter = json_decode($jsonRoutineParameter);
              for ($a=0; $a < sizeof($decodeJSONParameter); $a++) {
                $arrayRoutineParameters[] = array(
                    'parameterName' => $decodeJSONParameter[$a]->parameterName,
                    'typeData' => $decodeJSONParameter[$a]->typeData,
                    'paramsPosition' => $decodeJSONParameter[$a]->paramsPosition,
                );
              }
              $arrayFile[] = array(
                      'routineName' => $decodeJsonFile[$i]->routineName,
                      'actionRoutine' => $decodeJsonFile[$i]->actionRoutine,
                      'routineType' => $decodeJsonFile[$i]->routineType,
                      'routineParameters' => $arrayRoutineParameters ,


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


  		break;
  		}
      case 'showListStruktur':{
          $jsonFile = $this->listStruktur($this->idServer,$idRelease);
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          $nomorUrut = 1;
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $tableName = $decodeJsonFile[$i]->tableName;
            $columnName = $decodeJsonFile[$i]->columnName;
            if($this->filterExtension($decodeJsonFile[$i]->tableName) == 0){
              $tableResult .= "
                <tr>
                  <td>$nomorUrut</td>
                  <td></td>
                  <td>".$tableName."</td>
                  <td>$columnName</td>
                </tr>
                ";
                $nomorUrut += 1;
            }
          }
          $jsonFileFilter = json_encode($arrayFile);
          $content = array("tableResult" => $tableResult);
          $cek = $jsonFile;

  		break;
  		}
      case 'showListTriger':{
         $jsonFile = $this->listTriger($this->idServer,$idRelease);
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          $nomorUrut = 1;
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $trigerName = $decodeJsonFile[$i]->trigerName;
            $actionTriger = $decodeJsonFile[$i]->actionTriger;
            if($this->filterExtension($decodeJsonFile[$i]->trigerName) == 0){
              $tableResult .= "
                <tr>
                  <td>$nomorUrut</td>
                  <td></td>
                  <td>".$trigerName."</td>
                  <td></td>
                </tr>
                ";
                $nomorUrut += 1;
            }
          }
          $jsonFileFilter = json_encode($arrayFile);
          $content = array("tableResult" => $tableResult);

  		break;
  		}
      case 'showListRoutine':{
         $jsonFile = $this->listRoutine($this->idServer,$idRelease);
          $decodeJsonFile = json_decode($jsonFile);
          $arrayFile = array();
          $nomorUrut = 1;
          for ($i=0; $i < sizeof($decodeJsonFile) ; $i++) {
            $routineName = $decodeJsonFile[$i]->routineName;
            $actionRoutine = $decodeJsonFile[$i]->actionRoutine;
            if($this->filterExtension($decodeJsonFile[$i]->$routineName) == 0){
              $tableResult .= "
                <tr>
                  <td>$nomorUrut</td>
                  <td></td>
                  <td>".$routineName."</td>
                  <td></td>
                </tr>
                ";
                $nomorUrut += 1;
            }
          }
          $jsonFileFilter = json_encode($arrayFile);
          $content = array("tableResult" => $tableResult);
          $cek = $jsonFile;

  		break;
  		}

      case 'prosesCheckStruktur':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $getDataFileCheck = $this->sqlArray($this->sqlQuery("select * from json_file_check where id ='$idFileCheck'"));
          $explodeListFile = json_decode($getDataFileCheck['isi']);
          $tableName = $explodeListFile[$nomorUrut - 1]->tableName;
          $columnName = $explodeListFile[$nomorUrut - 1]->columnName;
          $typeData = $explodeListFile[$nomorUrut - 1]->typeData;
          $jsonDatabaseTujuan = json_decode($this->listStrukturDatabaseTujuan($serverTarget,$idRelease,$tableName,$columnName));
          $tableNameTujuan = $jsonDatabaseTujuan[0]->tableName;
          $columnNameTujuan = $jsonDatabaseTujuan[0]->columnName;
          $typeDataTujuan = $jsonDatabaseTujuan[0]->typeData;
          $error = array();
          if($tableName != $tableNameTujuan){
            $error[]= "TABLE TIDAK ADA";
            $errorCode[]= "1";
          }
          if($columnName != $columnNameTujuan){
            $error[]= "KOLOM TIDAK ADA";
            $errorCode[]= "2";
          }
          if($typeData != $typeDataTujuan){
            $error[]= "TIPE DATA BERBEDA";
            $errorCode[]= "3";
          }
          if(sizeof($error) == 0){
            $error = "";
            $status = "OK";
          }else{
            $error = implode(", ",$error);
            $status = "ERROR";
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
        }
        $valueOfCheckbox = array(
            'tableName' => $tableName,
            'columnName' => $columnName,
            'typeData' => $typeData,
            'errorCode' => implode(";",$errorCode),
        );
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $error,
        'tableName' => $tableName,
        'columnName' => $columnName,
        'valueOfCheckbox' => json_encode($valueOfCheckbox),
        'reason' => $error,
        'status' => $status,

        );
  		break;
  		}
      case 'prosesCheckTriger':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $getDataFileCheck = $this->sqlArray($this->sqlQuery("select * from json_file_check where id ='$idFileCheck'"));
          $explodeListFile = json_decode($getDataFileCheck['isi']);
          $trigerName = $explodeListFile[$nomorUrut - 1]->trigerName;
          $actionTriger = $this->dropTrashString($this->hexDecode($explodeListFile[$nomorUrut - 1]->actionTriger));
          $actionTiming = $explodeListFile[$nomorUrut - 1]->actionTiming;
          $eventManipulation = $explodeListFile[$nomorUrut - 1]->eventManipulation;
          $tableName = $explodeListFile[$nomorUrut - 1]->tableName;
          $jsonDatabaseTujuan = json_decode($this->listTrigerDatabaseTujuan($serverTarget,$idRelease,$trigerName));
          $trigerNameTujuan = $jsonDatabaseTujuan[0]->trigerName;
          $actionTrigerTujuan = $this->dropTrashString($this->hexDecode($jsonDatabaseTujuan[0]->actionTriger));
          $actionTimingTujuan = $jsonDatabaseTujuan[0]->actionTiming;
          $eventManipulationTujuan = $jsonDatabaseTujuan[0]->eventManipulation;
          $tableNameTujuan = $jsonDatabaseTujuan[0]->tableName;
          $error = array();
          if($trigerName != $trigerNameTujuan){
            $error[]= "TRIGER TIDAK ADA";
            $errorCode[]= 1;
          }
          if($actionTriger != $actionTrigerTujuan){
            $error[]= "ISI TRIGER BEDA";
            $errorCode[]= 2;
          }
          if($actionTiming != $actionTimingTujuan){
            $error[]= "ACTION TIMING BEDA";
            $errorCode[]= 3;
          }
          if($eventManipulation != $eventManipulationTujuan){
            $error[]= "EVENT MANIPULATION BEDA";
            $errorCode[]= 4;
          }
          if($tableName != $tableNameTujuan){
            $error[]= "TABLE NAME BEDA";
            $errorCode[]= 5;
          }
          if(sizeof($error) == 0){
            $error = "";
            $status = "OK";
          }else{
            $error = implode(", ",$error);
            $status = "ERROR";
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
          $valueOfCheckbox = array(
              'isiTriger' => $explodeListFile[$nomorUrut - 1]->actionTriger,
              'actionTiming' => $actionTiming,
              'eventManipulation' => $eventManipulation,
              'namaTriger' => $trigerName,
              'tableName' => $tableName,
              'errorCode' => implode(";",$errorCode),
          );
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $error,
        'trigerName' => $trigerName,
        'reason' => $error,
        'status' => $status,
        'valueOfCheckbox' => json_encode($valueOfCheckbox),

        );
  		break;
  		}
      case 'prosesCheckRoutine':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $getDataFileCheck = $this->sqlArray($this->sqlQuery("select * from json_file_check where id ='$idFileCheck'"));
          $explodeListFile = json_decode($getDataFileCheck['isi']);
          $routineName = $explodeListFile[$nomorUrut - 1]->routineName;
          $routineType = $explodeListFile[$nomorUrut - 1]->routineType;
          $routineParameters = $explodeListFile[$nomorUrut - 1]->routineParameters;
          $actionRoutine = $this->dropTrashString($this->hexDecode($explodeListFile[$nomorUrut - 1]->actionRoutine));

          $jsonDatabaseTujuan = json_decode($this->listRoutineDatabaseTujuan($serverTarget,$idRelease,$routineName));
          $routineNameTujuan = $jsonDatabaseTujuan[0]->routineName;
          $actionRoutineTujuan = $this->dropTrashString($this->hexDecode($jsonDatabaseTujuan[0]->actionRoutine));
          $routineTypeTujuan = $jsonDatabaseTujuan[0]->routineType;
          $jsonRoutineParameter = $this->listParameter($serverTarget,$idRelease,$routineName,$routineType);
          $decodeJSONParameter = json_decode($jsonRoutineParameter);
          for ($a=0; $a < sizeof($decodeJSONParameter); $a++) {
            $arrayRoutineParameters[] = array(
                'parameterName' => $decodeJSONParameter[$a]->parameterName,
                'typeData' => $decodeJSONParameter[$a]->typeData,
                'paramsPosition' => $decodeJSONParameter[$a]->paramsPosition,
            );
          }



          $error = array();
          if($routineName != $routineNameTujuan){
            $error[]= "ROUTINE TIDAK ADA";
            $errorCode[]= 1;
          }
          if($actionRoutine != $actionRoutineTujuan){
            $error[]= "ISI ROUTINE BEDA";
            $errorCode[]= 2;
          }
          if($routineType != $routineTypeTujuan){
            $error[]= "TYPE ROUTINE BEDA";
            $errorCode[]= 3;
          }
          if(json_encode($routineParameters) != json_encode($arrayRoutineParameters)){
            $error[]= "PARAMETERS ROUTINE BEDA";
            $errorCode[]= 4;
          }
          if(sizeof($error) == 0){
            $error = "";
            $status = "OK";
          }else{
            $error = implode(", ",$error);
            $status = "ERROR";
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
          $arrayValueOfCheckBox= array(
                  'routineName' => $routineName,
                  'routineType' => $routineType,
                  'routineValue' => $explodeListFile[$nomorUrut - 1]->actionRoutine,
                  'routineParameters' => $routineParameters,
                  'errorCode' => implode(";",$errorCode),
          );
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $error,
        'routineName' => $routineName,
        'reason' => $error,
        'status' => $status,
        'valueOfCheckbox' => json_encode($arrayValueOfCheckBox),

        );
  		break;
  		}
      case 'pushStrukturSelected':{
          $content = array("valueStruktur" => $bandingDatabaseServer_cb[0] ,"jumlahData" => sizeof($bandingDatabaseServer_cb));
      break;
      }
      case 'pushTrigerSelected':{
          $content = array("valueTriger" => $bandingDatabaseServer_cb[0] ,"jumlahData" => sizeof($bandingDatabaseServer_cb));
      break;
      }
      case 'pushRoutineSelected':{
          $content = array("valueRoutine" => $bandingDatabaseServer_cb[0] ,"jumlahData" => sizeof($bandingDatabaseServer_cb));
      break;
      }
      case 'tableResultStruktur':{
          $content = $this->tableResultStruktur();
      break;
      }
      case 'tableResultTriger':{
          $content = $this->tableResultTriger();
      break;
      }
      case 'tableResultRoutine':{
          $content = $this->tableResultRoutine();
      break;
      }
      case 'prosesPushStruktur':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $valueStruktur = $bandingDatabaseServer_cb[$nomorUrut - 1];
          $decodeJSONCheckbox = json_decode($valueStruktur);
          $tableName = $decodeJSONCheckbox->tableName;
          $columnName = $decodeJSONCheckbox->columnName;
          $typeData = $decodeJSONCheckbox->typeData;
          $errorCode = $decodeJSONCheckbox->errorCode;
          if(strpos($errorCode, "1") !== false){
             $namaFile = "/tmp/".$tableName.".VulnWalker";
             file_put_contents($namaFile,$this->dumpTable($this->idServer,$idRelease,$tableName));
             $this->pushFileSelected($serverTarget,$namaFile);
             $this->createTable($serverTarget,$idRelease,str_replace(" ","",$namaFile));
          }
          if(strpos($errorCode, "2") !== false){
             $cek =  $this->addColumn($serverTarget,$idRelease,$tableName,$columnName,$typeData);
          }
          if(strpos($errorCode, "3") !== false){
             $cek =  $this->changeTypeData($serverTarget,$idRelease,$tableName,$columnName,$typeData);
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $cek,
        'valueStruktur' => $valueStruktur,
        'fileLocation' => $fileLocation,
        'reason' => $error,

        );
  		break;

  		}
      case 'prosesPushTriger':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $valueTriger = $bandingDatabaseServer_cb[$nomorUrut - 1];
          $decodeJSONCheckbox = json_decode($valueTriger);
          $namaTriger = $decodeJSONCheckbox->namaTriger;
          $isiTriger = $decodeJSONCheckbox->isiTriger;
          $actionTiming = $decodeJSONCheckbox->actionTiming;
          $eventManipulation = $decodeJSONCheckbox->eventManipulation;
          $tableName = $decodeJSONCheckbox->tableName;
          $errorCode = $decodeJSONCheckbox->errorCode;
          if(!empty($errorCode)){
            $cek = $this->replaceTriger($serverTarget,$idRelease,$namaTriger,$isiTriger,$actionTiming,$eventManipulation,$tableName);
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $cek,
        'valueStruktur' => $valueStruktur,
        'fileLocation' => $fileLocation,
        'reason' => $error,

        );
  		break;

  		}
      case 'prosesPushRoutine':{
        if($nomorUrut == ($jumlahData + 1)){
          $sukses = "OK";
        }else{
          $valueTriger = $bandingDatabaseServer_cb[$nomorUrut - 1];
          $decodeJSONCheckbox = json_decode($valueTriger);
          $routineName = $decodeJSONCheckbox->routineName;
          $routineType = $decodeJSONCheckbox->routineType;
          $routineValue = $decodeJSONCheckbox->routineValue;
          $routineParameters = $decodeJSONCheckbox->routineParameters;
          $errorCode = $decodeJSONCheckbox->errorCode;
          if(!empty($errorCode)){
              if($routineType == 'FUNCTION'){
                $cek = $this->createRoutineFunction($serverTarget,$idRelease,$routineName,$routineValue,json_encode($routineParameters));
              }else{
                $cek = $this->createRoutineStoreProcedur($serverTarget,$idRelease,$routineName,$routineValue,json_encode($routineParameters));
              }
          }
          $persen = ($nomorUrut / $jumlahData) * 100;
          $persenText = $persen."%";
          $sukses = "";
        }
        $content = array("sukses" => $sukses, "persen" => $persen, "persenText" => $persenText,"error" => $cek,
        'valueStruktur' => $valueTriger,
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
  function decodeBase64($string){
    return base64_decode($string);
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
    <script type='text/javascript' src='js/bandingDatabaseServer/bandingDatabaseServer.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/daterangepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/moment.js'></script>
    <script type='text/javascript'>
    $( document ).ready(function() {
          bandingDatabaseServer.createDialog();
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
    $arrayOptions = array(
      array("struktur","STRUKTUR"),
      array("triger","TRIGER"),
      array("routine","ROUTINE"),
    );
    $cmbOption  =  $this->cmbArray("optionBanding","",$arrayOptions,"-- JENIS --","class='form-control' onchange=$this->Prefix.optionChanged();");
    $pageContent = "
    <div id='page-title'>
      <h2>CHECK DATABASE SERVER</h2>
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
                        <textarea id='filterWord' name='filterWord' class='form-control'></textarea>
                    </div>
                </div>
                <div class='form-group'>
                    <label class='col-sm-1 control-label'>SERVER</label>
                    <div class='col-sm-11'>
                        $cmbTarget
                    </div>
                </div>

                <div class='form-group'>
                    <label class='col-sm-1 control-label'>OPTION</label>
                    <div class='col-sm-11'>
                        $cmbOption
                    </div>
                </div>
                <div class='form-group' style='float:right;'>
                    <div class='col-sm-12'>
                    <button type='button' onclick=$this->Prefix.showListStruktur(); class='btn btn-alt btn-hover btn-success' id='buttonShow'>
                        <span>Tampilkan</span>
                        <i class='glyph-icon icon-save'></i>
                    </button>
                    <button type='button' onclick=$this->Prefix.checkStruktur(); class='btn btn-alt btn-hover btn-success' id='buttonCheck'>
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
                <div class='form-group' id='tempatTableResult' >
                  ".$this->tableResultStruktur()."
                </div>

            </form>
          </div>
      </div>
    </div>

    ";
    return $pageContent;
  }

  function tableResultStruktur(){
    $tableContent = "
      <table class='table table-bordered table-striped table-condensed table-hover' style='font-size:11px;' id='tableResult'>
        <thead>
          <tr>
              <th width='20'>No</th>
              <th width='20'>".$this->checkAll(25,$this->Prefix)."</th>
              <th width='300'>Table Name</th>
              <th width='300'>Column Name</th>
              <th width='50'>Status</th>
              <th width='300'>Ket</th>
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
  function tableResultTriger(){
    $tableContent = "
      <table class='table table-bordered table-striped table-condensed table-hover' style='font-size:11px;' id='tableResult'>
        <thead>
          <tr>
              <th width='20'>No</th>
              <th width='20'>".$this->checkAll(25,$this->Prefix)."</th>
              <th width='300'>TRIGER NAME </th>
              <th width='50'>STATUS</th>
              <th width='300'>KET</th>
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
  function tableResultRoutine(){
    $tableContent = "
      <table class='table table-bordered table-striped table-condensed table-hover' style='font-size:11px;' id='tableResult'>
        <thead>
          <tr>
              <th width='20'>No</th>
              <th width='20'>".$this->checkAll(25,$this->Prefix)."</th>
              <th width='300'>Routine Name </th>
              <th width='50'>Status</th>
              <th width='300'>KET</th>
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
  function listDatabase($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $contentAWK = '{ print "{\"databasename\":\""$1"\",", "\"collation\":\""$2"\"} "}';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$userMysql." -p".$databasePassword." -s -e 'select SCHEMA_NAME,DEFAULT_COLLATION_NAME from information_schema.SCHEMATA;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function dumpTable($id,$idRelease,$tableName) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $comand = $this->sshCommand($sshConnection,"mysqldump -u".$userMysql." -p".$databasePassword." --no-data --skip-events --skip-routines --skip-triggers $databaseName $tableName");
    return $comand;
  }
  function createTable($id,$idRelease,$namaFile) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $databasePassword = $getDataServer["password_mysql"];
    $userMysql = $getDataServer["user_mysql"];
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
     $this->sshCommand($sshConnection,"mysql -u".$userMysql." -p".$databasePassword." $databaseName < $namaFile ");
    return "mysql -u".$userMysql." -p".$databasePassword." $databaseName < $namaFile " ;
  }
  function listStruktur($id,$idRelease) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
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
  function replaceTriger($id,$idRelease,$namaTriger,$isiTriger,$actionTiming,$eventManipulation,$tableName) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D $databaseName  -s -e 'DROP TRIGGER $namaTriger' ");
    file_put_contents("/tmp/$namaTriger.trigger","DELIMITER ;; \n CREATE TRIGGER `$namaTriger` $actionTiming $eventManipulation ON `$tableName` FOR EACH ROW \n".$this->hexDecode($isiTriger).";;");
    $this->pushFileSelected($id,"/tmp/$namaTriger.trigger");
    $command = "mysql -u".$usernameDatabase." -p".$passwordDatabase." -f -c $databaseName <  /tmp/$namaTriger.trigger";
    $this->sshCommand($sshConnection,$command);
    return $comand;
  }
  function createRoutineFunction($id,$idRelease,$routineName,$routineValue,$routineParameters) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D $databaseName  -s -e 'DROP FUNCTION IF EXISTS $routineName' ");
    $decodeJSONParameter = json_decode($routineParameters);
    for ($i=0; $i < sizeof($decodeJSONParameter); $i++) {
      if($decodeJSONParameter[$i]->paramsPosition == '0' ){
        $returnFunction = "RETURNS ".$decodeJSONParameter[$i]->typeData;
      }else{
        $arrayParameter[] =$decodeJSONParameter[$i]->parameterName." ".$decodeJSONParameter[$i]->typeData." ";
      }
    }
    file_put_contents("/tmp/$routineName.function","DELIMITER ;; \n CREATE FUNCTION `$routineName` (".implode(",",$arrayParameter).") $returnFunction \n".$this->hexDecode($routineValue).";;");
    $this->pushFileSelected($id,"/tmp/$routineName.function");
    $command = "mysql -u".$usernameDatabase." -p".$passwordDatabase." -f -c $databaseName <  /tmp/$routineName.function";
    $this->sshCommand($sshConnection,$command);
    return $comand;
  }
  function createRoutineStoreProcedur($id,$idRelease,$routineName,$routineValue,$routineParameters) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D $databaseName  -s -e 'DROP PROCEDURE IF EXISTS $routineName' ");
    $decodeJSONParameter = json_decode($routineParameters);
    for ($i=0; $i < sizeof($decodeJSONParameter); $i++) {
        $arrayParameter[] =$decodeJSONParameter[$i]->parameterName." ".$decodeJSONParameter[$i]->typeData." ";
    }
    file_put_contents("/tmp/$routineName.procedure","DELIMITER ;; \n CREATE PROCEDURE `$routineName` (".implode(",",$arrayParameter).")  \n".$this->hexDecode($routineValue).";;");
    $this->pushFileSelected($id,"/tmp/$routineName.procedure");
    $command = "mysql -u".$usernameDatabase." -p".$passwordDatabase." -f -c $databaseName <  /tmp/$routineName.procedure";
    $this->sshCommand($sshConnection,$command);
    return $comand;
  }
  function addColumn($id,$idRelease,$tableName,$columnName,$typeData) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $command = "ALTER TABLE $databaseName.$tableName ADD $columnName $typeData;";
    $this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -s -e '$command;' ");
    return $command;
  }
  function changeTypeData($id,$idRelease,$tableName,$columnName,$typeData) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $command = "ALTER TABLE $databaseName.$tableName MODIFY $columnName $typeData;";
    $this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -s -e '$command;' ");
    return $command;
  }
  function listStrukturDatabaseTujuan($id,$idRelease,$tableName,$columnName) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"tableName\":\""$1"\",", "\"columnName\":\""$2"\",", "\"typeData\":\""$3"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $tableName = '"'.$tableName.'"';
    $columnName = '"'.$columnName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -s -e 'select TABLE_NAME,COLUMN_NAME,COLUMN_TYPE from information_schema.COLUMNS where TABLE_SCHEMA= $stringDatabaseName and TABLE_NAME = $tableName and COLUMN_NAME = $columnName ;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listTrigerDatabaseTujuan($id,$idRelease,$trigerName) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"trigerName\":\""$1"\",", "\"actionTriger\":\""$2"\",", "\"actionTiming\":\""$3"\",", "\"eventManipulation\":\""$4"\",", "\"tableName\":\""$5"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $trigerName = '"'.$trigerName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D information_schema -s -e 'select TRIGGER_NAME,HEX(ACTION_STATEMENT),ACTION_TIMING,EVENT_MANIPULATION,EVENT_OBJECT_TABLE  from TRIGGERS where TRIGGER_SCHEMA=  $stringDatabaseName and TRIGGER_NAME = $trigerName  ;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listRoutineDatabaseTujuan($id,$idRelease,$routineName) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"routineName\":\""$1"\",", "\"actionRoutine\":\""$2"\",", "\"routineType\":\""$3"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $routineName = '"'.$routineName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D information_schema -s -e 'select ROUTINE_NAME,HEX(ROUTINE_DEFINITION),ROUTINE_TYPE from ROUTINES where ROUTINE_SCHEMA = $stringDatabaseName and ROUTINE_NAME = $routineName  ;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listTriger($id,$idRelease) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"trigerName\":\""$1"\",", "\"actionTriger\":\""$2"\",", "\"actionTiming\":\""$3"\",", "\"eventManipulation\":\""$4"\",", "\"tableName\":\""$5"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D information_schema -s -e 'select TRIGGER_NAME,HEX(ACTION_STATEMENT),ACTION_TIMING,EVENT_MANIPULATION,EVENT_OBJECT_TABLE  from TRIGGERS where TRIGGER_SCHEMA=  $stringDatabaseName;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listRoutine($id,$idRelease) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"routineName\":\""$1"\",", "\"actionRoutine\":\""$2"\",", "\"routineType\":\""$3"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D information_schema -s -e 'select ROUTINE_NAME,HEX(ROUTINE_DEFINITION),ROUTINE_TYPE from ROUTINES where ROUTINE_SCHEMA = $stringDatabaseName;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function listParameter($id,$idRelease,$routineName,$routineType) {
    $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$idRelease'"));
    $databaseName = $getDataRelease['nama_database'];
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $usernameDatabase = $getDataServer['user_mysql'];
    $passwordDatabase = $getDataServer['password_mysql'];
    $contentAWK = '{ print "{\"parameterName\":\""$1"\",", "\"typeData\":\""$2"\",", "\"paramsPosition\":\""$3"\"} "}';
    $stringDatabaseName = '"'.$databaseName.'"';
    $stringRoutineName = '"'.$routineName.'"';
    $comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"mysql -u".$usernameDatabase." -p".$passwordDatabase." -D information_schema -s -e 'select PARAMETER_NAME,DATA_TYPE,ORDINAL_POSITION from PARAMETERS where SPECIFIC_SCHEMA = $stringDatabaseName and SPECIFIC_NAME  = $stringRoutineName;' | awk '$contentAWK'"));
    return "[".str_replace(" ","",$comand)."]";
  }
  function dropTrashString($string){
    $string = str_replace("\t","",$string);
    $string = str_replace(" ","",$string);
    $string = str_replace("\r","",$string);
    $string = str_replace("&nbsp","",$string);
    $string = str_replace("\n","",$string);
    return $string;
  }

}
$bandingDatabaseServer = new bandingDatabaseServer();


 ?>
