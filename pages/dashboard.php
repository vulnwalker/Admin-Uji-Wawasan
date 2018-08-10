<?php
class dashboard extends baseObject{
  var $Prefix = "dashboard";
  var $formName = "dashboardForm";
  var $tableName = "ref_server";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'variable':{

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
    $checkList ='"glyph-icon icon-check"';
    return "
    <script type='text/javascript' src='js/dashboard.js'></script>
    <script type='text/javascript' src='assets/widgets/input-switch/inputswitch.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/daterangepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/moment.js'></script>
    <script type='text/javascript' src='assets/widgets/uniform/uniform.js'></script>
    <script type='text/javascript'>
      $( document ).ready(function() {
        $('.input-switch').bootstrapSwitch();
        $('.custom-checkbox').uniform();
        $('.checker span').append('<i class=$checkList></i>');
        $('.accordion').accordion({
            heightStyle: 'content'
        });
      });


    </script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-resize.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-stack.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-pie.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/flot/flot-tooltip.js'></script>
    ";
  }


  function pageContent(){
    $pageContent = "

      ".$this->dashBoardContent()."
      <div id='tempatLoading'></div>
    ";
    return $pageContent;
  }

  function setKolomHeader(){
    $kolomHeader = "
    <thead>
      <tr>
          <th>No</th>
          <th>Nama Server</th>
          <th>Alamat IP</th>
          <th>Status</th>
          <th>WEB</th>
          <th>OS</th>
          <th>RAM</th>
          <th>DISK</th>
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
    $rightClick = "
    <script>
    var menuOption = [
      {
        name: 'Status',
        img: 'assets/icons/status.png',
        title: 'Status',
        fun: function () {
            $this->Prefix.statusServer($id);
        }
      },
      {
          name: 'Reboot',
          img: 'assets/icons/reboot.png',
          title: 'Reboot',
          fun: function () {
              $this->Prefix.rebootServer('$id');
          }

      },
      {
          name: 'Off Website',
          img: 'assets/icons/shutdown.png',
          title: 'Off Website',
          fun: function () {
              $this->Prefix.killApache('$id');
          }

      },
      {
          name: 'On Website',
          img: 'assets/icons/on.png',
          title: 'On Website',
          fun: function () {
              $this->Prefix.lifeApache('$id');
          }

      },
      {
          name: 'Manage Server',
          img: 'assets/icons/server.png',
          title: 'Manage Server',
          fun: function () {
              $this->Prefix.manageServer('$id');
          }

      }
    ];
    var menuTrgr=$('#rightClick$id');
    menuTrgr.contextMenu(menuOption,{
         triggerOn :'contextmenu',
         mouseClick : 'right'
    });
    </script>
    ";
    //".$this->pingAddress($alamat_ip)."
    //".$this->apacheStatus($alamat_ip)."
    //".$this->osName($id)."
    //".$this->memorySize($id)."
    //".$this->diskSize($id)."
    $tableRow = "
    <tr class='$classRow' id ='rightClick$id' >
        <td>$no</td>
        <td>$nama_server</td>
        <td>$alamat_ip</td>
        <td><span id='statusPing$id'> </span></td>
        <td><span id='apacheStatus$id'> </span></td>
        <td align='right'><span id='osName$id'> </span></td>
        <td align='right'><span id='memorySize$id'> </span></td>
        <td align='right'><span id='diskSize$id'> </span></td>

    </tr>
    $rightClick
    ";
    return $tableRow;
  }
  function generateTable($kondisiTable){
    $no = 1;
    $getDataServer = $this->sqlQuery("select * from ref_server ".$kondisiTable." ");
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
  function dashBoardContent(){

      $content .= "
      <div class='panel'>
        <div class='panel-body'>
            <h3 class='title-hero'>
                Dashboard
            </h3>
            <div class='example-box-wrapper'>
                <ul class='list-group list-group-separator row list-group-icons'>
                    <li class='col-md-3 active'>
                        <a href='#tab-example-1' data-toggle='tab' class='list-group-item'>
                            <i class='glyph-icon font-red icon-dashboard'></i>
                            STATUS
                        </a>
                    </li>
                    <li class='col-md-3'>
                        <a href='#tab-example-2' data-toggle='tab' class='list-group-item'>
                            <i class='glyph-icon icon-dashboard'></i>
                            Top Score
                        </a>
                    </li>
                    <li class='col-md-3 '>
                        <a href='#tab-example-3' data-toggle='tab' class='list-group-item'>
                            <i class='glyph-icon font-primary icon-camera'></i>
                            Histori Jawaban
                        </a>
                    </li>
                    <li class='col-md-3'>
                        <a href='#tab-example-4' data-toggle='tab' class='list-group-item'>
                            <i class='glyph-icon font-blue-alt icon-globe'></i>
                            Histori Iklan
                        </a>
                    </li>
                </ul>
                <div class='tab-content'>
                    <div class='tab-pane fade active in' id='tab-example-1'>
                      <div class='content-box-wrapper'>
                        <div id='graphServer$id'>
                          ".$this->statistikServer($id,"hari ini")."
                        </div>
                      </div>
                    </div>
                    <div class='tab-pane fade ' id='tab-example-2'>
                        ".$this->contentTopScore()."
                    </div>
                    <div class='tab-pane fade' id='tab-example-3'>
                        ".$this->contentHistoriJawaban()."
                    </div>
                    <div class='tab-pane fade' id='tab-example-4'>
                        ".$this->contentHistoriIklan()."
                    </div>
                </div>
            </div>
        </div>
    </div>
      ";
    return $content;
  }
  function statistikServer($idServer,$option,$dateRange = "",$optionStatistik= ""){
    if(!$optionStatistik){
      $optionStatistik = array(
        'memoryUsage' => 'on',
        'cpuUsage' => 'on',
        'diskUsage' => 'on',
      );
    }
    $nomorUrut = 0;
    if($option == 'hari ini'){
      $getLogServer = $this->sqlQuery("select * from log_server where id_server = '$idServer' and tanggal = '".date("Y-m-d")."'");
      while ($dataLogServer = $this->sqlArray($getLogServer)) {
          $decodedLog = json_decode($dataLogServer['result']);
          $memoryUsage = str_replace("%","",$decodedLog->memoryUsage);
          $cpuUsage = str_replace("%","",$decodedLog->cpuUsage);
          $diskUsage = str_replace("%","",$decodedLog->diskUsage);
          $tanggalLog = $this->generateDate($dataLogServer['tanggal'])." ".$dataLogServer['jam'];
          $pushMemory = "memoryUsage.push([$nomorUrut, $memoryUsage]);";
          $pushCPU = "  cpuUsage.push([$nomorUrut, $cpuUsage]);";
          $pushDisk = "diskUsage.push([$nomorUrut, $diskUsage]);";
          if(empty($optionStatistik['memoryUsage'])){
            $pushMemory = "";
          }
          if(empty($optionStatistik['cpuUsage'])){
            $pushCPU = "";
          }
          if(empty($optionStatistik['diskUsage'])){
            $pushDisk = "";
          }
          $pushArrayStatistik .= "
          $pushMemory
          $pushCPU
          $pushDisk
          tanggalMemoryUsage.push(['$tanggalLog']);
          tanggalCpuUsage.push(['$tanggalLog']);
          tanggalDiskUsage.push(['$tanggalLog']);
          ";
          if($nomorUrut % 4 == 0){
            $arrayListJam[] = "[$nomorUrut,'".$dataLogServer['jam']."']";
          }
          $nomorUrut +=1;
      }
      $listJam = implode(",",$arrayListJam);
      if(!empty($optionStatistik['memoryUsage'])){
        $arrayFilterCheckBox[] = "{ data: memoryUsage, label: 'Memory Usage',color:'red', 'Tanggal' : tanggalMemoryUsage }";
      }
      if(!empty($optionStatistik['cpuUsage'])){
        $arrayFilterCheckBox[] = "{ data: cpuUsage, label: 'CPU Usage',color:'blue', 'Tanggal' : tanggalCpuUsage }";
      }
      if(!empty($optionStatistik['diskUsage'])){
        $arrayFilterCheckBox[] = "{ data: diskUsage, label: 'Disk Usage',color:'green', 'Tanggal' : tanggalDiskUsage }";
      }
      $implodeFilterCheckBox = implode(",",$arrayFilterCheckBox);
      $kamusData = "
                    xaxis: {
                      ticks: [$listJam]
                    },";
      $content = "<div class='panel'>
                  <div class='panel-body'>
                      <h3 class='title-hero'>
                      Graph Server Condition &nbsp<input class='btn btn-primary' type='button' value='Show Graph' onclick=$this->Prefix.showGraph($idServer) >
                      </h3>
                      <div class='example-box-wrapper'>
                          <div id='grapikServer$idServer' class='mrg20B' style='width: 100%; height: 300px; padding: 0px; position: relative;'>
                          </div>
                      </div>
                  </div>
              </div>
              <script type='text/javascript'>
              $(function() {
                  var memoryUsage = [], cpuUsage = [], diskUsage = [];
                  var tanggalMemoryUsage = [], tanggalCpuUsage = [], tanggalDiskUsage = [];

                  $pushArrayStatistik

                  var plot = $.plot($('#grapikServer$idServer'),
                      [
                        $implodeFilterCheckBox
                      ], {
                          series: {
                              shadowSize: 0,
                              lines: {
                                  show: true,
                                  lineWidth: 2
                              }
                          },
                          grid: {
                              labelMargin: 10,
                              hoverable: true,
                              clickable: true,
                              borderWidth: 1,
                              borderColor: 'rgba(82, 167, 224, 0.06)'
                          },
                          legend: {
                              backgroundColor: '#fff'
                          },
                          yaxis: { tickColor: 'rgba(0, 0, 0, 0.06)',  min : 0, max : 100, font: {color: 'rgba(0, 0, 0, 0.4)'}},
                          $kamusData
                          colors: [getUIColor('default'), getUIColor('gray'), getUIColor('blue')],
                          tooltip: true,
                      });

                  $('#grapikServer$idServer').bind('plotclick', function (event, pos, item) {
                  });
              });
              </script>


              ";
    }elseif($option == 'harian'){
      $arrayRangeDate = explode(" - ",$dateRange);
      $getLogServer = $this->sqlQuery("select * from log_server where id_server = '$idServer' and tanggal <= '".$this->generateDate($arrayRangeDate[1])."' and tanggal >= '".$this->generateDate($arrayRangeDate[0])."'");
      while ($dataLogServer = $this->sqlArray($getLogServer)) {
            $decodedLog = json_decode($dataLogServer['result']);
            $memoryUsage = str_replace("%","",$decodedLog->memoryUsage);
            $cpuUsage = str_replace("%","",$decodedLog->cpuUsage);
            $diskUsage = str_replace("%","",$decodedLog->diskUsage);
            $tanggalLog = $this->generateDate($dataLogServer['tanggal'])." ".$dataLogServer['jam'];
            $pushMemory = "memoryUsage.push([$nomorUrut, $memoryUsage]);";
            $pushCPU = "  cpuUsage.push([$nomorUrut, $cpuUsage]);";
            $pushDisk = "diskUsage.push([$nomorUrut, $diskUsage]);";
            if(empty($optionStatistik['memoryUsage'])){
              $pushMemory = "";
            }
            if(empty($optionStatistik['cpuUsage'])){
              $pushCPU = "";
            }
            if(empty($optionStatistik['diskUsage'])){
              $pushDisk = "";
            }
            $pushArrayStatistik .= "
            $pushMemory
            $pushCPU
            $pushDisk
            tanggalMemoryUsage.push(['$tanggalLog']);
            tanggalCpuUsage.push(['$tanggalLog']);
            tanggalDiskUsage.push(['$tanggalLog']);
            ";
            if($nomorUrut % 96 == 0){
              $arrayListTanggal[] = "[$nomorUrut,'".$this->generateDate($dataLogServer['tanggal'])."']";
            }
            $nomorUrut +=1;
      }
      if(!empty($optionStatistik['memoryUsage'])){
        $arrayFilterCheckBox[] = "{ data: memoryUsage, label: 'Memory Usage',color:'red', 'Tanggal' : tanggalMemoryUsage }";
      }
      if(!empty($optionStatistik['cpuUsage'])){
        $arrayFilterCheckBox[] = "{ data: cpuUsage, label: 'CPU Usage',color:'blue', 'Tanggal' : tanggalCpuUsage }";
      }
      if(!empty($optionStatistik['diskUsage'])){
        $arrayFilterCheckBox[] = "{ data: diskUsage, label: 'Disk Usage',color:'green', 'Tanggal' : tanggalDiskUsage }";
      }
      $implodeFilterCheckBox = implode(",",$arrayFilterCheckBox);
      $listTanggal = implode(",",$arrayListTanggal);
      $kamusData = "
                    xaxis: {
                      ticks: [$listTanggal]
                    },";

      $content = "<div class='panel'>
                  <div class='panel-body'>
                      <h3 class='title-hero'>
                      Graph Server Condition &nbsp<input class='btn btn-primary' type='button' value='Show Graph' onclick=$this->Prefix.showGraph($idServer) >
                      </h3>
                      <div class='example-box-wrapper'>
                          <div id='grapikServer$idServer' class='mrg20B' style='width: 100%; height: 300px; padding: 0px; position: relative;'>
                          </div>
                      </div>
                  </div>
              </div>
              <script type='text/javascript'>
              $(function() {
                  var memoryUsage = [], cpuUsage = [], diskUsage = [];
                  var tanggalMemoryUsage = [], tanggalCpuUsage = [], tanggalDiskUsage = [];

                  $pushArrayStatistik

                  var plot = $.plot($('#grapikServer$idServer'),
                      [
                        $implodeFilterCheckBox
                      ], {
                          series: {
                              shadowSize: 0,
                              lines: {
                                  show: true,
                                  lineWidth: 2
                              }
                          },
                          grid: {
                              labelMargin: 10,
                              hoverable: true,
                              clickable: true,
                              borderWidth: 1,
                              borderColor: 'rgba(82, 167, 224, 0.06)'
                          },
                          legend: {
                              backgroundColor: '#fff'
                          },
                          yaxis: { tickColor: 'rgba(0, 0, 0, 0.06)',  min : 0, max : 100, font: {color: 'rgba(0, 0, 0, 0.4)'}},
                          $kamusData

                          colors: [getUIColor('default'), getUIColor('gray'), getUIColor('blue')],
                          tooltip: true,

                      });

                  $('#grapikServer$idServer').bind('plotclick', function (event, pos, item) {

                  });


              });
              </script>


              ";
    }elseif($option == 'bulanan'){
      $arrayRangeDate = explode(" - ",$dateRange);
      $getLogServer = $this->sqlQuery("select * from log_server where id_server = '$idServer' and LEFT(tanggal,4) = '".date("Y")."' group by tanggal");
      while ($dataLogServer = $this->sqlArray($getLogServer)) {
            $getDataLogHarian = $this->sqlQuery("select * from log_server where id_server = '$idServer' and year(tanggal) = '".date("Y")."' and tanggal = '".$dataLogServer['tanggal']."'");
            $memoryUsage = '';
            $cpuUsage = '';
            $diskUsage = '';
            while ($dataLogHarian = $this->sqlArray($getDataLogHarian)) {
              $decodedLog = json_decode($dataLogServer['result']);
              $memoryUsage += str_replace("%","",$decodedLog->memoryUsage);
              $cpuUsage += str_replace("%","",$decodedLog->cpuUsage);
              $diskUsage += str_replace("%","",$decodedLog->diskUsage);
            }
            $jumlahDataHarian = $this->sqlRowCount($getDataLogHarian);
            $tanggalLog = $this->generateDate($dataLogServer['tanggal']);
            $sumMemoryUSage = $memoryUsage / $jumlahDataHarian;
            $sumCpuUsage = $cpuUsage / $jumlahDataHarian;
            $sumDiskUsage = $diskUsage / $jumlahDataHarian;
            $pushMemory = "memoryUsage.push([$nomorUrut, $sumMemoryUSage]);";
            $pushCPU = "  cpuUsage.push([$nomorUrut, $sumCpuUsage]);";
            $pushDisk = "diskUsage.push([$nomorUrut, $sumDiskUsage]);";
            if(empty($optionStatistik['memoryUsage'])){
              $pushMemory = "";
            }
            if(empty($optionStatistik['cpuUsage'])){
              $pushCPU = "";
            }
            if(empty($optionStatistik['diskUsage'])){
              $pushDisk = "";
            }
            $pushArrayStatistik .= "
            $pushMemory
            $pushCPU
            $pushDisk
            tanggalMemoryUsage.push(['$tanggalLog']);
            tanggalCpuUsage.push(['$tanggalLog']);
            tanggalDiskUsage.push(['$tanggalLog']);
            ";
            $getMaxIdForXaris = $this->sqlArray($this->sqlQuery("select min(tanggal) from log_server where year(tanggal) = '".date("Y")."' and month(tanggal) = '".$this->getMonth($dataLogServer['tanggal'])."'"));
            if($dataLogServer['tanggal'] == $getMaxIdForXaris['min(tanggal)']){
              $arrayListTanggal[] = "[$nomorUrut,'".$this->getNameMonth($this->getMonth($dataLogServer['tanggal']))."']";
            }
            $nomorUrut +=1;
      }
      if(!empty($optionStatistik['memoryUsage'])){
        $arrayFilterCheckBox[] = "{ data: memoryUsage, label: 'Memory Usage',color:'red', 'Tanggal' : tanggalMemoryUsage }";
      }
      if(!empty($optionStatistik['cpuUsage'])){
        $arrayFilterCheckBox[] = "{ data: cpuUsage, label: 'CPU Usage',color:'blue', 'Tanggal' : tanggalCpuUsage }";
      }
      if(!empty($optionStatistik['diskUsage'])){
        $arrayFilterCheckBox[] = "{ data: diskUsage, label: 'Disk Usage',color:'green', 'Tanggal' : tanggalDiskUsage }";
      }
      $implodeFilterCheckBox = implode(",",$arrayFilterCheckBox);
      $listTanggal = implode(",",$arrayListTanggal);
      $kamusData = "
                    xaxis: {
                      ticks: [$listTanggal]
                    },";

      $content = "<div class='panel'>
                  <div class='panel-body'>
                      <h3 class='title-hero'>
                      Graph Server Condition &nbsp<input class='btn btn-primary' type='button' value='Show Graph' onclick=$this->Prefix.showGraph($idServer) >
                      </h3>
                      <div class='example-box-wrapper'>
                          <div id='grapikServer$idServer' class='mrg20B' style='width: 100%; height: 300px; padding: 0px; position: relative;'>
                          </div>
                      </div>
                  </div>
              </div>
              <script type='text/javascript'>
              $(function() {
                  var memoryUsage = [], cpuUsage = [], diskUsage = [];
                  var tanggalMemoryUsage = [], tanggalCpuUsage = [], tanggalDiskUsage = [];

                  $pushArrayStatistik

                  var plot = $.plot($('#grapikServer$idServer'),
                      [
                        $implodeFilterCheckBox
                      ], {
                          series: {
                              shadowSize: 0,
                              lines: {
                                  show: true,
                                  lineWidth: 2
                              }
                          },
                          grid: {
                              labelMargin: 10,
                              hoverable: true,
                              clickable: true,
                              borderWidth: 1,
                              borderColor: 'rgba(82, 167, 224, 0.06)'
                          },
                          legend: {
                              backgroundColor: '#fff'
                          },
                          yaxis: { tickColor: 'rgba(0, 0, 0, 0.06)',  min : 0, max : 100, font: {color: 'rgba(0, 0, 0, 0.4)'}},
                          $kamusData

                          colors: [getUIColor('default'), getUIColor('gray'), getUIColor('blue')],
                          tooltip: true,

                      });

                  $('#grapikServer$idServer').bind('plotclick', function (event, pos, item) {

                  });


              });
              </script>


              ";
    }
    return $content;
  }

  function tableTopScore($dataSession){
    $no = 1;
    $getDataTopScore = $this->sqlQuery("select * from top_score where id_session = '".$dataSession['id']."' order by jumlah_point desc");
    while ($dataTopScore = $this->sqlArray($getDataTopScore)) {
      $getDataMember = $this->sqlArray($this->sqlQuery("select * from member where id = '".$dataTopScore['id_member']."'"));
      $listTopScore .= "
      <tr>
          <td> $no </td>
          <td>".$getDataMember['nama']."</td>
          <td style='text-align:right;'>".$this->numberFormat($dataTopScore['jumlah_point'])."</td>
      </tr>
      ";
      $no += 1;
    }
    $content = "
    <table class='table table-condensed'>
        <thead>
        <tr>
            <th style='width:20px !important;'>No</th>
            <th style='text-align:center;'>Nama</th>
            <th style='text-align:center;'>Score</th>
        </tr>
        </thead>
        <tbody>
        $listTopScore

        </tbody>
    </table>

    ";


    return $content ;
  }
  function contentTopScore(){

    $getDataSession = $this->sqlQuery("select * from sesion order by id desc");
    while ($dataSession = $this->sqlArray($getDataSession)) {
      $controllerSession = "controllerSession".$dataSession['id'];
      $clientSession = md5($controllerSession."_".md5($dataSession['judul']));
      $listTopScore .="
      <h3 class='ui-accordion-header ui-state-default ui-accordion-icons ui-corner-all' role='tab' id='$controllerSession' aria-controls='$clientSession' aria-selected='false' aria-expanded='false' tabindex='-1'><span class='ui-accordion-header-icon ui-icon ui-icon-triangle-1-e'></span>".$dataSession['judul']."</h3>
      <div class='ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom' id='$clientSession' aria-labelledby='$controllerSession' role='tabpanel' style='display: none;' aria-hidden='true'>
        ".$this->tableTopScore($dataSession)."
      </div>
      ";
    }

      $content = "
      <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <div class='accordion ui-accordion ui-widget ui-helper-reset' role='tablist'>
                  $listTopScore
                </div>
            </div>
        </div>
    </div>

      ";


    return $content;
  }
  function contentHistoriJawaban(){
    $content = "jawaban";

    return $content;
  }
  function contentHistoriIklan(){
    $content = "iklan";
    return $content;
  }
  function getMonth($tanggal){
      $explodeTanggal = explode("-",$tanggal);
      return $explodeTanggal[1];
  }
  function getNameMonth($bulan){
      $bulan = $bulan - 1;
      $arrayBulan = array(
        'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember'
      );
      return $arrayBulan[$bulan];
  }


}
$dashboard = new dashboard();


 ?>
