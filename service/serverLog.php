<?php
include "../base/config.php";
class serverLog extends Config{
  var $Prefix = "serverLog";
  var $formName = "serverLogForm";
  var $tableName = "ref_server";
  function __construct(){
      $getDataServer = $this->sqlQuery("select * from ref_server");
      while ($dataServer = $this->sqlArray($getDataServer)) {
          $this->getInfoServer($dataServer['id']);
          $arrayResultLog = array(
              'memoryUsage' => $this->memoryUsage($dataServer['id']),
              'cpuUsage' => $this->cpuUsage($dataServer['id']),
              'diskUsage' => $this->diskUsage($dataServer['id']),
          );
          $dataLogServer =array(
              'id_server' => $dataServer['id'],
              'tanggal' => date("Y-m-d"),
              'jam' => date("H:i"),
              'result' => json_encode($arrayResultLog),
          );
          $query = $this->sqlInsert("log_server",$dataLogServer);
          $this->sqlQuery($query);
          echo $dataServer['nama_server'] ." => ".json_encode($arrayResultLog)." \n";
      }
  }
  function memoryUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $memUsage = '"%u%%"';
    return $this->sshCommand($sshConnection,"awk '/^Mem/ {printf($memUsage, 100*$3/$2);}' <(free -m)");
  }
  function cpuUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return str_replace("\n","",$this->sshCommand($sshConnection,"grep 'cpu ' /proc/stat | awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'"));
  }
  function diskUsage($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $diskUsg = '"%s"';
    $varNF = "$"."NF";
    $bagi = '"/"';
    return strstr($this->sshCommand($sshConnection,"df -h | awk '$varNF==$bagi{printf $diskUsg, $5}'"), '%', true);
  }
  function getInfoServer($idServer){
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$idServer'"));
    $arrayFileSystem = array();
    $getPartisi = str_replace('{"FileSystem":"Filesystem","Size":"Size","Used":"Used","Free":"Avail","Persen":"Use%","Mounted":"Mounted"},',"",$this->getDetailDisk($idServer));
    $arrayDisk = json_decode($getPartisi);
    for ($i=0; $i < sizeof($arrayDisk) ; $i++) {
        $arrayFileSystem[] = array($arrayDisk[$i]->FileSystem,$arrayDisk[$i]->FileSystem." | ".$arrayDisk[$i]->Size);
    }
    $processor = $this->getProcessor($idServer);
    $kernel = $this->getKernel($idServer);
    $operationSystem = $this->getOperationSystem($idServer);
    $RAM = $this->getRamSize($idServer);
    $diskSize = $this->getDiskSize($idServer);
    $statusPing = $this->pingAddress($getDataServer['alamat_ip']);
    $apacheStatus = $this->apacheStatus($getDataServer['alamat_ip']);
    if(!empty($processor) && $kernel && !empty($operationSystem) && !empty($diskSize) && !empty($statusPing) && !empty($apacheStatus)){
      $content = array(
                          'processor' => $processor,
                          'kernel' => $kernel,
                          'operationSystem' => $operationSystem,
                          'RAM' => $RAM,
                          'diskSize' => $diskSize,
                          'statusPing' => $statusPing,
                          'apacheStatus' => $apacheStatus,
                       );
       if($this->sqlRowCount($this->sqlQuery("select * from info_server where id_server = '$idServer'")) == 0){
          $dataInfoServer = array(
                                    'id_server' => $idServer,
                                    'processor' => $content['processor'],
                                    'kernel' => $content['kernel'],
                                    'os' => $content['operationSystem'],
                                    'ram' => $content['RAM'],
                                    'harddisk' => $getPartisi,
                                    'server_status' => $content['statusPing'],
                                    'web_status' => $content['apacheStatus'],
                                );
          $query = $this->sqlInsert("info_server",$dataInfoServer);
          $this->sqlQuery($query);
       }else{
         $dataInfoServer = array(
                                   'processor' => $content['processor'],
                                   'kernel' => $content['kernel'],
                                   'os' => $content['operationSystem'],
                                   'ram' => $content['RAM'],
                                   'harddisk' => $getPartisi,
                                   'server_status' => $content['statusPing'],
                                   'web_status' => $content['apacheStatus'],
                               );
          $query = $this->sqlUpdate("info_server",$dataInfoServer,"id_server = '$idServer'");
          $this->sqlQuery($query);
       }
    }



  }

  function getProcessor($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $modelName = '"MODELNAME=\"';
    $kutip = '"';
    $modelNameKutip = '"model name"';
    return $this->sshCommand($sshConnection,"echo -n $modelName $kutip
    grep -m 1 $modelNameKutip /proc/cpuinfo | cut -d: -f2 | sed -e 's/^ *//' | sed -e 's/$/$kutip/'");
  }
  function getOperationSystem($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"cat /etc/issue");
  }
  function getRamSize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->numberFormat($this->sshCommand($sshConnection,"echo $(awk '/^MemTotal:/{print $2}' /proc/meminfo)"))." Byte";
  }
  function getDiskSize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->numberFormat($this->sshCommand($sshConnection,"blockdev --getsize64 /dev/sda"))." Byte";
  }
  function getKernel($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"uname -a");
  }
  function pingAddress($ip) {
      $pingresult = exec("/bin/ping -c2 -w2 $ip", $outcome, $status);
      if ($status==0) {
      $status = "LIVE";
      } else {
      $status = "DEAD";
      }
      $message = $status;
      return $message;
  }
  function apacheStatus($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $rt = curl_exec($ch);
    $info = curl_getinfo($ch);
    $info = $info["http_code"];
    // if($info == 200 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    // if($info == 504 ){
    //   $status = "<span style='text-color:red;'>DIE</span>";
    // }
    // if($info == 500 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    // if($info == 302 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    // if($info == 404 ){
    //   $status = "<span style='text-color:green;'>ACTIVE</span>";
    // }
    $status = "DIE";
    if($info == 200 ){
      $status = "ACTIVE";
    }
    if($info == 504 ){
      $status = "DIE";
    }
    if($info == 500 ){
      $status = "ACTIVE";
    }
    if($info == 302 ){
      $status = "ACTIVE";
    }
    if($info == 404 ){
      $status = "ACTIVE";
    }
    return $status;
  }



}
$serverLog = new serverLog();

 ?>
