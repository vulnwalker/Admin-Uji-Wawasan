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
                            <i class='glyph-icon icon-glass'></i>
                            Top Score
                        </a>
                    </li>
                    <li class='col-md-3 '>
                        <a href='#tab-example-3' data-toggle='tab' class='list-group-item'>
                            <i class='glyph-icon font-primary icon-elusive-doc'></i>
                            Histori Jawaban
                        </a>
                    </li>
                    <li class='col-md-3'>
                        <a href='#tab-example-4' data-toggle='tab' class='list-group-item'>
                            <i class='glyph-icon font-blue-alt icon-iconic-video'></i>
                            Histori Iklan
                        </a>
                    </li>
                </ul>
                <div class='tab-content'>
                    <div class='tab-pane fade active in' id='tab-example-1'>
                      <div class='content-box-wrapper'>
                        <div id='graphServer$id'>
                          ".$this->grafikIklan()."
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
  function grafikIklan(){
    $content = "
    <div class='panel-body'>
        <h3 class='title-hero'>
            Graph Ads Loaded
        </h3>
        <div class='example-box-wrapper'>
          <div id='data-example-1' class='mrg20B' style='width: 100%; height: 300px;'></div>
        </div>
    </div>


    <script type='text/javascript'>
    $(function() {
        var sin = [], cos = [];
        for (var i = 0; i < 354; i += 31) {
            sin.push([i, Math.random(i)]);
            cos.push([i, Math.random(i)]);
        }

        var plot = $.plot($('#data-example-1'),
            [{ data: sin, label: 'Ads Showed' }, { data: cos, label: 'Ads Loaded' }], {
                series: {

                    shadowSize: 0,
                    lines: {
                        show: true,
                        lineWidth: 2
                    },
                    points: { show: true }
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
                yaxis: { tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
                xaxis: { tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
                colors: [getUIColor('success'), getUIColor('gray')],
                tooltip: true,
                tooltipOpts: {
                    content: 'x: %x, y: %y'
                }
            });

        var previousPoint = null;
        $('#data-example-1').bind('plothover', function (event, pos, item) {
            $('#x').text(pos.x.toFixed(2));
            $('#y').text(pos.y.toFixed(2));
        });

        $('#data-example-1').bind('plotclick', function (event, pos, item) {
            if (item) {
                $('#clickdata').text('You clicked point ' + item.dataIndex + ' in ' + item.series.label + '.');
                plot.highlight(item.series, item.datapoint);
            }
        });


    });


    $(function() {
        var data = [],
            totalPoints = 300;
        function getRandomData() {
            if (data.length > 0)
                data = data.slice(1);
            // Do a random walk
            while (data.length < totalPoints) {
                var prev = data.length > 0 ? data[data.length - 1] : 50,
                    y = prev + Math.random() * 10 - 5;
                if (y < 0) {
                    y = 0;
                } else if (y > 100) {
                    y = 100;
                }
                data.push(y);
            }
            // Zip the generated y values with the x values
            var res = [];
            for (var i = 0; i < data.length; ++i) {
                res.push([i, data[i]])
            }
            return res;
        }
        // Set up the control widget
        var updateInterval = 30;
        var plot = $.plot('#data-example-3', [ getRandomData() ], {
            series: {
                lines: {
                    show: true,
                    lineWidth: 2,
                    fill: 0.5,
                    fillColor: { colors: [ { opacity: 0.01 }, { opacity: 0.08 } ] }
                },
                shadowSize: 0   // Drawing is faster without shadows
            },
            grid: {
                labelMargin: 10,
                hoverable: true,
                clickable: true,
                borderWidth: 1,
                borderColor: 'rgba(82, 167, 224, 0.06)'
            },
            yaxis: {
                min: 0,
                max: 120,
                tickColor: 'rgba(0, 0, 0, 0.06)', font: {color: 'rgba(0, 0, 0, 0.4)'}},
            xaxis: { show: false },
            colors: [getUIColor('default'),getUIColor('gray')]
        });

        function update() {
            plot.setData([getRandomData()]);
            plot.draw();
            setTimeout(update, updateInterval);
        }
        update();
    });


    </script>
    ";
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
    $no = 1;
    $getDataAchievement = $this->sqlQuery("select * from achievement  order by id desc");
    while ($dataAchievement = $this->sqlArray($getDataAchievement)) {
      $getDataMember = $this->sqlArray($this->sqlQuery("select * from member where id = '".$dataAchievement['id_member']."'"));
      $getDataKategori = $this->sqlArray($this->sqlQuery("select * from kategori where id = '".$dataAchievement['id_kategori']."'"));
      $getDataSoal = $this->sqlArray($this->sqlQuery("select * from soal where id = '".$dataAchievement['id_soal']."'"));
      $listTopScore .= "
      <tr>
          <td> $no </td>
          <td>".$getDataKategori['nama_kategori']."</td>
          <td>".$getDataSoal['pertanyaan']."</td>
          <td>".$getDataMember['nama']."</td>
          <td >".$this->generateDate($dataAchievement['tanggal']). " ".$dataAchievement['jam']."</td>
      </tr>
      ";
      $no += 1;
    }
    $content = "
    <table class='table table-condensed'>
        <thead>
        <tr>
            <th style='width:20px !important;'>No</th>
            <th style='text-align:center;'>Kategori</th>
            <th style='text-align:center;'>Soal</th>
            <th style='text-align:center;'>Nama Member</th>
            <th style='text-align:center;'>Tanggal</th>
        </tr>
        </thead>
        <tbody>
        $listTopScore

        </tbody>
    </table>

    ";

    return $content;
  }
  function contentHistoriIklan(){
    $no = 1;
    $getDataChargeEnergi = $this->sqlQuery("select * from charge_energi  order by id desc");
    while ($dataChargeEnergi = $this->sqlArray($getDataChargeEnergi)) {
      $getDataMember = $this->sqlArray($this->sqlQuery("select * from member where id = '".$dataChargeEnergi['id_member']."'"));
      $getDataIklan = $this->sqlArray($this->sqlQuery("select * from type_iklan where id = '".$dataChargeEnergi['type_iklan']."' "));
      $listTopScore .= "
      <tr>
          <td> $no </td>
          <td>".$getDataMember['nama']."</td>
          <td style='text-align:center;'>".$getDataIklan['jenis_iklan']."</td>
          <td  style='text-align:center;'>".$this->generateDate($dataChargeEnergi['tanggal']). " ".$dataChargeEnergi['jam']."</td>
          <td style='text-align:right;'>".$dataChargeEnergi['total_energi']."</td>
      </tr>
      ";
      $no += 1;
    }
    $content = "
    <table class='table table-condensed'>
        <thead>
        <tr>
            <th style='width:20px !important;'>No</th>
            <th style='text-align:center;'>Nama Member</th>
            <th style='text-align:center;'>Jenis Iklan</th>
            <th style='text-align:center;'>Tanggal</th>
            <th style='text-align:center;'>Energi</th>
        </tr>
        </thead>
        <tbody>
        $listTopScore

        </tbody>
    </table>

    ";
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
