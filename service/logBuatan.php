<?php
include "../base/config.php";
class serverLog extends Config{
  function __construct(){
      echo date('w');
      // for ($hariTanggal=1; $hariTanggal < 27 ; $hariTanggal++) {
      //   $tanggalLooping = "2018-03-".$this->genNumber($hariTanggal);
      //   for ($jamLooping=0; $jamLooping < 24 ; $jamLooping++) {
      //     for ($menitLooping=0; $menitLooping < 4 ; $menitLooping++) {
      //       $getDataServer = $this->sqlQuery("select * from ref_server");
      //       while ($dataServer = $this->sqlArray($getDataServer)) {
      //         $arrayResultLog = array(
      //             'memoryUsage' => $this->randomNumber(),
      //             'cpuUsage' => $this->randomNumber(),
      //             'diskUsage' => $this->randomNumber(),
      //         );
      //         $dataLogServer =array(
      //             'id_server' => $dataServer['id'],
      //             'tanggal' => $tanggalLooping,
      //             'jam' => $this->genNumber($jamLooping).":".$this->genNumber($menitLooping * 15),
      //             'result' => json_encode($arrayResultLog),
      //         );
      //         $query = $this->sqlInsert("log_server",$dataLogServer);
      //         $this->sqlQuery($query);
      //         echo $dataServer['nama_server'] ." => ".json_encode($dataLogServer)." \n";
      //       }
      //     }
      //
      //   }
      //
      // }

  }



function randomNumber(){
  return rand ( 0 , 99 );
}
function genNumber($num, $dig=2){
	$tambah = pow(10,$dig);//100000;
	$tmp = ($num + $tambah).'';
	return substr($tmp,1,$dig);
}
}

$serverLog = new serverLog();

 ?>
