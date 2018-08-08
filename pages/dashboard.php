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
      case 'refreshList':{
        if(!empty($filterCari)){
          $arrKondisi[] = "nama_server like '%$filterCari%' ";
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
      case 'statusServer':{
        $getInfoServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
        foreach ($getInfoServer as $key => $value) {
            $$key = $value;
        }
        $content = array(
                          'statusPing' => $this->pingAddress($alamat_ip),
                          'apacheStatus' => $this->apacheStatus($alamat_ip),
                          'osName' => $this->osName($id),
                          'memorySize' => $this->memorySize($id),
                          'diskSize' => $this->diskSize($id),
                        );
  		break;
  		}
      case 'graphServerChanged':{
        $arrayOptions = array(
          'memoryUsage' => $checkBoxMemoryUsage,
          'cpuUsage' => $checkBoxCpuUsage,
          'diskUsage' => $checkBoxDiskUsage,
        );
        $cek = $this->loadScript().$this->statistikServer($idServer,$optionStatistik,$dateRange,$arrayOptions);
  		break;
  		}
      case 'optionsGraphChanged':{
        $dateRange = "<input type='text' name='kurunWaktu$idServer' id='kurunWaktu$idServer' placeholder='Filter Tanggal' class='float-left mrg10R form-control hasDatepicker' >";
        $loadScript = "<script type='text/javascript' src='assets/widgets/daterangepicker/daterangepicker.js'></script>
        <script type='text/javascript' src='assets/widgets/daterangepicker/moment.js'></script>
        <script type='text/javascript'>
            $('#kurunWaktu$idServer').daterangepicker({
                format: 'DD-MM-YYYY'
            });
        </script>
        ";
        if($optionStatistik =='hari ini' || $optionStatistik =='bulanan'){
          $loadScript == "";
          $dateRange = "<input type='text' name='kurunWaktu$idServer' id='kurunWaktu$idServer' placeholder='Filter Tanggal' class='float-left mrg10R form-control hasDatepicker' disabled >";
        }
        $content = $loadScript.$dateRange;
  		break;
  		}
      case 'rebootServer':{
        $this->rebootServer($id);
  		break;
  		}
      case 'baseBigFile':{
        $path = 'release.zip';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $content = $base64;
  		break;
  		}
      case 'killApache':{
        $this->killApache($id);
  		break;
  		}
      case 'lifeApache':{
        $this->lifeApache($id);
  		break;
  		}
      case 'getInfoServer':{
          $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$idServer'"));
          $arrayFileSystem = array();
          $getPartisi = str_replace('{"FileSystem":"Filesystem","Size":"Size","Used":"Used","Free":"Avail","Persen":"Use%","Mounted":"Mounted"},',"",$this->getDetailDisk($idServer));
          $arrayDisk = json_decode($getPartisi);
          for ($i=0; $i < sizeof($arrayDisk) ; $i++) {
              $arrayFileSystem[] = array($arrayDisk[$i]->FileSystem,$arrayDisk[$i]->FileSystem." | ".$arrayDisk[$i]->Size);
          }

          $comboDisk = $this->cmbArray("fileSystem",$fileSystem,$arrayFileSystem,"-- File System --","class='form-control' ");
          $content = array(
                              'processor' => $this->getProcessor($idServer),
                              'kernel' => $this->getKernel($idServer),
                              'operationSystem' => $this->getOperationSystem($idServer),
                              'RAM' => $this->getRamSize($idServer),
                              'diskSize' => $this->getDiskSize($idServer),
                              'statusPing' => $this->pingAddress($getDataServer['alamat_ip']),
                              'apacheStatus' => $this->apacheStatus($getDataServer['alamat_ip']),
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
           $arrayOptions = array(
             array('hari ini','HARI INI'),
             array('harian','HARIAN'),
             array('mingguan','MINGGUAN'),
             array('bulanan','BULANAN'),
           );
           $getInfoServer = $this->sqlArray($this->sqlQuery("select * from info_server where id_server = '$idServer'"));
           $formFields = array(
       			'serverName' => array(
       						'label'=>"Server Alias",
       						'labelWidth'=>3,
       						'value'=>$getDataServer['alias'],
       						 ),
       			'ipServer' => array(
       						'label'=>"IP",
       						'labelWidth'=>3,
       						'value'=>$getDataServer['alamat_ip'],
       						 ),
       			'kernelSever' => array(
       						'label'=>"Kernel",
       						'labelWidth'=>3,
       						'value'=>$getInfoServer['kernel'],
       						 ),
       			'osServer' => array(
       						'label'=>"Operation System",
       						'labelWidth'=>3,
       						'value'=>$getInfoServer['os'],
       						 ),
       			'processorServer' => array(
       						'label'=>"Processor",
       						'labelWidth'=>3,
       						'value'=>$getInfoServer['processor'],
       						 ),
       			'diskSize' => array(
       						'label'=>"Disk Size",
       						'labelWidth'=>3,
       						'value'=>$comboDisk,
       						 ),
       			'memorySizeServer' => array(
       						'label'=>"RAM",
       						'labelWidth'=>3,
       						'value'=>$getInfoServer['ram'],
       						 ),
             'serverStatus' => array(
       						'label'=>"Server Status",
       						'labelWidth'=>3,
       						'value'=> $this->switchOption("serverStatus$id",$getInfoServer['server_status'],"disabled"),
       						 ),
       			'webStatus' => array(
       						'label'=>"Web Status",
       						'labelWidth'=>3,
       						'value'=>$this->switchOption("webStatus$id",$getInfoServer['web_status'],"onchange=$this->Prefix.webStatusChanged($id)"),
       						 ),
             'optionStatistik' => array(
                   'label'=>"Graph Server",
                   'labelWidth'=>3,
                   'value'=> $this->cmbArray("optionStatistik$id",$optionStatistik,$arrayOptions,"-- OPTION --","class='form-control' onchange=$this->Prefix.optionsGraphChanged($id);  ")
                    ),
            'filterTanggal' => array(
      						'label'=>"Filter Tanggal",
      						'labelWidth'=>3,
                  'value'=> "<span id='spanKurunWaktu$id'> <input type='text' name='kurunWaktu$id' id='kurunWaktu$id' placeholder='Filter Tanggal' class='float-left mrg10R form-control hasDatepicker' > </span>"
      						 ),
             'optionData' => array(
                   'label'=>"",
                   'labelWidth'=>3,
                   'value'=> $this->checkBox("checkBoxMemoryUsage$id","true","checked","Memory")."&nbsp".$this->checkBox("checkBoxCpuUsage$id","true","checked","CPU")."&nbsp".$this->checkBox("checkBoxDiskUsage$id","true","checked","DISK")
                    ),
       			);

           $cek = "
               <div class='content-box'>
                   <h3 class='content-box-header bg-primary'>
                       <span class=' pull-right' style='cursor:pointer;' onclick=$this->Prefix.getInfoServer($idServer);>
                           <i class='glyph-icon icon-refresh'></i>
                       </span>".$getDataServer['nama_server']."
                       </h3>

                   <div class='content-box-wrapper'>
                     ".$this->formGenerator($formFields)."
                     ".$this->loadScript($formFields)."
                     <div id='graphServer$id'>
                      ".$this->statistikServer($idServer,$optionStatistik)."
                     </div>
                   </div>
               </div>
           ";
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
    <script src='plugins/contextMenu/contextMenu.min.js'></script>
    <link rel='stylesheet' type='text/css' href='plugins/contextMenu/contextMenu.min.css'>
    <script type='text/javascript' src='assets/widgets/input-switch/inputswitch.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/daterangepicker.js'></script>
    <script type='text/javascript' src='assets/widgets/daterangepicker/moment.js'></script>
    <script type='text/javascript' src='assets/widgets/uniform/uniform.js'></script>
    <script type='text/javascript'>
      $( document ).ready(function() {
        $('.input-switch').bootstrapSwitch();
        $('.custom-checkbox').uniform();
        $('.checker span').append('<i class=$checkList></i>');
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

      ".$this->listPanelServer()."
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
  function listPanelServer(){
    $getDataServer = $this->sqlQuery("select * from ref_server  ");
    while ($dataServer = $this->sqlArray($getDataServer)) {
      foreach ($dataServer as $key => $value) {
         $$key = $value;
      }
      $getInfoServer = $this->sqlArray($this->sqlQuery("select * from info_server where id_server = '$id'"));
      $arrayFileSystem = array();
      $getPartisi = $getInfoServer['harddisk'];
      $arrayDisk = json_decode($getPartisi);
      for ($i=0; $i < sizeof($arrayDisk) ; $i++) {
          $arrayFileSystem[] = array($arrayDisk[$i]->FileSystem,$arrayDisk[$i]->FileSystem." | ".$arrayDisk[$i]->Size);
      }
      $arrayOptions = array(
        array('hari ini','HARI INI'),
        array('harian','HARIAN'),
        array('bulanan','BULANAN'),
      );
      $comboDisk = $this->cmbArray("fileSystem",$fileSystem,$arrayFileSystem,"-- File System --","class='form-control' ");
      $formFields = array(
  			'serverName' => array(
  						'label'=>"Server Alias",
  						'labelWidth'=>3,
  						'value'=>$alias,
  						 ),
  			'ipServer' => array(
  						'label'=>"IP",
  						'labelWidth'=>3,
  						'value'=>$alamat_ip,
  						 ),
  			'kernelSever' => array(
  						'label'=>"Kernel",
  						'labelWidth'=>3,
  						'value'=>$getInfoServer['kernel'],
  						 ),
  			'osServer' => array(
  						'label'=>"Operation System",
  						'labelWidth'=>3,
  						'value'=>$getInfoServer['os'],
  						 ),
  			'processorServer' => array(
  						'label'=>"Processor",
  						'labelWidth'=>3,
  						'value'=>$getInfoServer['processor'],
  						 ),
  			'diskSize' => array(
  						'label'=>"Disk Size",
  						'labelWidth'=>3,
  						'value'=>$comboDisk,
  						 ),
  			'memorySizeServer' => array(
  						'label'=>"RAM",
  						'labelWidth'=>3,
  						'value'=>$getInfoServer['ram'],
  						 ),
  			'serverStatus' => array(
  						'label'=>"Server Status",
  						'labelWidth'=>3,
  						'value'=> $this->switchOption("serverStatus$id",$getInfoServer['server_status'],"disabled")."&nbsp <button type='button' class='btn btn-primary' onclick=$this->Prefix.rebootServer($id);>Reboot</button> ",
  						 ),
  			'webStatus' => array(
  						'label'=>"Web Status",
  						'labelWidth'=>3,
  						'value'=>$this->switchOption("webStatus$id",$getInfoServer['web_status'],"onchange$this->Prefix.webStatusChanged($id,this.checked) "),
  						 ),
  			'optionStatistik' => array(
  						'label'=>"Graph Server",
  						'labelWidth'=>3,
  						'value'=> $this->cmbArray("optionStatistik$id",$optionStatistik,$arrayOptions,"-- OPTION --","class='form-control'  onchange=$this->Prefix.optionsGraphChanged($id); ")
  						 ),
  			'filterTanggal' => array(
  						'label'=>"Filter Tanggal",
  						'labelWidth'=>3,
  						'value'=> "<span id='spanKurunWaktu$id'> <input type='text' name='kurunWaktu$id' id='kurunWaktu$id' placeholder='Filter Tanggal' class='float-left mrg10R form-control hasDatepicker' disabled > </span>"
  						 ),
  			'optionData' => array(
  						'label'=>"",
  						'labelWidth'=>3,
  						'value'=> $this->checkBox("checkBoxMemoryUsage$id","true","checked","Memory")."&nbsp".$this->checkBox("checkBoxCpuUsage$id","true","checked","CPU")."&nbsp".$this->checkBox("checkBoxDiskUsage$id","true","checked","DISK")
  						 ),
  			);
      $content .= "
      <div class='col-md-6' id='panelServer$id'>
          <div class='content-box'>
              <h3 class='content-box-header bg-primary'>
                  <span class=' pull-right' style='cursor:pointer;' onclick=$this->Prefix.getInfoServer($id);>
                      <i class='glyph-icon icon-refresh'></i>
                  </span>$nama_server
                  </h3>

              <div class='content-box-wrapper'>
                ".$this->formGenerator($formFields)."
                <div id='graphServer$id'>
                  ".$this->statistikServer($id,"hari ini")."
                </div>
              </div>
          </div>
      </div>";
    }
    return $content;
  }
  function statistikServer($idServer,$option,$dateRange,$optionStatistik){
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
  function killApache($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $this->sqlQuery("update info_server set web_status = 'DIE' where id_server = '$id'");
    return $this->sshCommand($sshConnection,"service apache2 stop");
  }
  function lifeApache($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    $this->sqlQuery("update info_server set web_status = 'ACTIVE' where id_server = '$id'");
    return $this->sshCommand($sshConnection,"service apache2 start");
  }
  function rebootServer($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"reboot");
  }
  function memorySize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->numberFormat($this->sshCommand($sshConnection,"echo $(awk '/^MemTotal:/{print $2}' /proc/meminfo)"))." Byte";
  }
  function diskSize($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    // return $this->numberFormat(str_replace("(","",$this->sshCommand($sshConnection,"dmesg | grep blocks | grep GB | grep -o -P '(?<=blocks:).*(?=GB)'")))." GB";
    return $this->numberFormat($this->sshCommand($sshConnection,"blockdev --getsize64 /dev/sda"))." Byte";
  }
  function osName($id) {
    $getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
    $sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
    $this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
    return $this->sshCommand($sshConnection,"cat /etc/issue");
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
