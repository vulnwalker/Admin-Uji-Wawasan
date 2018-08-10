<?php

class baseObject extends Config{

  function setCekBox($cb, $KeyValueStr, $Prefix){
    $hsl = "
      <input class='custom-checkbox' type='checkbox' id='".$Prefix."_cb$cb' name='".$Prefix."_cb[]' value='".$KeyValueStr."' onchange = $Prefix.thisChecked('".$Prefix."_cb$cb','".$Prefix."_jmlcek'); >
    ";
    return $hsl;
  }
  function checkAll($jumlahCheck,$Prefix){
    return "
              <input type='checkbox' class='custom-checkbox' name='".$Prefix."_toogle' id='".$Prefix."_toogle' onclick=$Prefix.checkSemua($jumlahCheck,'".$Prefix."_cb','$Prefix_toogle','".$Prefix."_jmlcek',this)>

            ";
  }
  function cmbArray($name='txtField',$value='',$arrList = '',$default='Pilih', $param='') {
     	$isi = $value;
    	for($i=0;$i<count($arrList);$i++) {
    		$Sel = $isi==$arrList[$i][0]?" selected ":"";
    		$Input .= "<option $Sel value='{$arrList[$i][0]}'>{$arrList[$i][1]}</option>";
    	}
    	$Input  = "<select $param name='$name'  id='$name' >$Input</select>";
    	return $Input;
  }
  function getBeetwen($var1="",$var2="",$pool){
    $temp1 = strpos($pool,$var1)+strlen($var1);
    $result = substr($pool,$temp1,strlen($pool));
    $dd=strpos($result,$var2);
    if($dd == 0){
        $dd = strlen($result);
    }

    return substr($result,0,$dd);
}
  function cmbQuery($name='txtField', $value='', $query='', $param='', $Atas='Pilih', $vAtas='') {
    $queryKolom = $this->getBeetwen('select','from',$query);
    $explodeQuery = explode(",",$queryKolom);
    $value1 = str_replace(" ","",$explodeQuery[0]);
    $value2= str_replace(" ","",$explodeQuery[1]);
      $Input = "<option value='$vAtas'>$Atas</option>";
      $Query = $this->sqlQuery($query);
      while ($Hasil = $this->sqlArray($Query)) {
          $Sel = $Hasil[$value1] == $value ? "selected" : "";
          $Input .= "<option $Sel value='".$Hasil[$value1]."'>".$Hasil[$value2]."</option> $queryKolom";
      }
      $Input = "<select $param name='$name' id='$name'>$Input</select>";
      return $Input;
  }
  function cmbQueryEmpty($name='txtField', $value='', $query='', $param='', $Atas='Pilih', $vAtas='') {
    $queryKolom = $this->getBeetwen('select','from',$query);
    $explodeQuery = explode(",",$queryKolom);
    $value1 = str_replace(" ","",$explodeQuery[0]);
    $value2= str_replace(" ","",$explodeQuery[1]);
      $Query = $this->sqlQuery($query);
      while ($Hasil = $this->sqlArray($Query)) {
          $Sel = $Hasil[$value1] == $value ? "selected" : "";
          $Input .= "<option $Sel value='".$Hasil[$value1]."'>".$Hasil[$value2]."</option> $queryKolom";
      }
      $Input = "<select $param name='$name' id='$name'>$Input</select>";
      return $Input;
  }
  function textBox($arrayOption){
    if(!isset($arrayOption['class'])){
      $className = "form-control";
    }else{
      $className = $arrayOption['class'];
    }
    return "<input type='text' name='".$arrayOption['id']."' id = '".$arrayOption['id']."' value='".$arrayOption['value']."' class='$className' ".$arrayOption['params']." >";
  }
  function textArea($arrayOption){
    if(!isset($arrayOption['class'])){
      $className = "form-control";
    }else{
      $className = $arrayOption['class'];
    }
    return "<textarea  name='".$arrayOption['id']."' id = '".$arrayOption['id']."' class='$className' ".$arrayOption['params']." >".$arrayOption['value']."</textarea>";
  }
  function numberText($arrayOption){
    if(!isset($arrayOption['class'])){
      $className = "form-control";
    }else{
      $className = $arrayOption['class'];
    }
    return "<input type='text' name='".$arrayOption['id']."' id = '".$arrayOption['id']."' value='".$arrayOption['value']."' class='$className' ".$arrayOption['params']." onkeypress='return event.charCode >= 48 && event.charCode <= 57' onkeyup=$this->Prefix.numberMark(this); >";
  }
  function button($arrayOption){
    if(!isset($arrayOption['class'])){
      $className = "form-control";
    }else{
      $className = $arrayOption['class'];
    }
    return "<input type='button' name='".$arrayOption['id']."' id = '".$arrayOption['id']."' value='".$arrayOption['value']."' class='$className' ".$arrayOption['params']." >";
  }
  function setMenuEdit(){
    $setMenuEdit = "
    <div id='header-nav-right'>
    </div>

    ";
    return $setMenuEdit;
  }
  function loadJSandCSS(){
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <title>UJI WAWASAN</title>
    <head>
    <style>
        .spinner{margin:0;width:70px;height:18px;margin:-35px 0 0 -9px;position:absolute;top:50%;left:50%;text-align:center}.spinner > div{width:18px;height:18px;background-color:#333;border-radius:100%;display:inline-block;-webkit-animation:bouncedelay 1.4s infinite ease-in-out;animation:bouncedelay 1.4s infinite ease-in-out;-webkit-animation-fill-mode:both;animation-fill-mode:both}.spinner .bounce1{-webkit-animation-delay:-.32s;animation-delay:-.32s}.spinner .bounce2{-webkit-animation-delay:-.16s;animation-delay:-.16s}@-webkit-keyframes bouncedelay{0%,80%,100%{-webkit-transform:scale(0.0)}40%{-webkit-transform:scale(1.0)}}@keyframes bouncedelay{0%,80%,100%{transform:scale(0.0);-webkit-transform:scale(0.0)}40%{transform:scale(1.0);-webkit-transform:scale(1.0)}}
        .activeSidebar{
            border: 1px solid #cecece;
            border-radius: 4px;
        }
    </style>
    <meta name='description' content=''>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no'>
    <link rel='apple-touch-icon-precomposed' sizes='144x144' href='assets/images/icons/apple-touch-icon-144-precomposed.png'>
    <link rel='apple-touch-icon-precomposed' sizes='114x114' href='assets/images/icons/apple-touch-icon-114-precomposed.png'>
    <link rel='apple-touch-icon-precomposed' sizes='72x72' href='assets/images/icons/apple-touch-icon-72-precomposed.png'>
    <link rel='apple-touch-icon-precomposed' href='assets/images/icons/apple-touch-icon-57-precomposed.png'>
    <link rel='shortcut icon' href='assets/images/icons/favicon.png'>

    <link rel='stylesheet' type='text/css' href='assets/bootstrap/css/bootstrap.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/animate.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/backgrounds.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/boilerplate.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/border-radius.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/grid.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/page-transitions.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/spacing.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/typography.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/utils.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/colors.css'>

    <link rel='stylesheet' type='text/css' href='assets/elements/badges.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/buttons.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/content-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/dashboard-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/forms.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/images.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/info-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/invoice.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/loading-indicators.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/menus.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/panel-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/response-messages.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/responsive-tables.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/ribbon.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/social-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/tables.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/tile-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/elements/timeline.css'>

    <link rel='stylesheet' type='text/css' href='assets/icons/fontawesome/fontawesome.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/linecons/linecons.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/spinnericon/spinnericon.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/iconic/iconic.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/elusive/elusive.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/meteocons/meteocons.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/spinnericon/spinnericon.css'>
    <link rel='stylesheet' type='text/css' href='assets/icons/typicons/typicons.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/accordion-ui/accordion.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/calendar/calendar.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/carousel/carousel.css'>

    <link rel='stylesheet' type='text/css' href='assets/widgets/charts/justgage/justgage.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/charts/morris/morris.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/charts/piegage/piegage.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/charts/xcharts/xcharts.css'>

    <link rel='stylesheet' type='text/css' href='assets/widgets/chosen/chosen.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/colorpicker/colorpicker.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/datatable/datatable.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/datepicker/datepicker.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/datepicker-ui/datepicker.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/daterangepicker/daterangepicker.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/dialog/dialog.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/dropdown/dropdown.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/dropzone/dropzone.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/file-input/fileinput.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/input-switch/inputswitch.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/input-switch/inputswitch-alt.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/ionrangeslider/ionrangeslider.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/jcrop/jcrop.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/jgrowl-notifications/jgrowl.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/loading-bar/loadingbar.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/maps/vector-maps/vectormaps.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/markdown/markdown.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/modal/modal.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/multi-select/multiselect.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/multi-upload/fileupload.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/nestable/nestable.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/noty-notifications/noty.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/popover/popover.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/pretty-photo/prettyphoto.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/progressbar/progressbar.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/range-slider/rangeslider.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/slidebars/slidebars.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/slider-ui/slider.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/summernote-wysiwyg/summernote-wysiwyg.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/tabs-ui/tabs.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/theme-switcher/themeswitcher.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/timepicker/timepicker.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/tocify/tocify.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/tooltip/tooltip.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/touchspin/touchspin.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/uniform/uniform.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/wizard/wizard.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/xeditable/xeditable.css'>

    <link rel='stylesheet' type='text/css' href='assets/snippets/chat.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/files-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/login-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/notification-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/progress-box.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/todo.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/user-profile.css'>
    <link rel='stylesheet' type='text/css' href='assets/snippets/mobile-navigation.css'>
    <link rel='stylesheet' type='text/css' href='assets/widgets/accordion-ui/accordion.css'>
    <link rel='stylesheet' type='text/css' href='https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.css'>



    <!-- APPLICATIONS -->

    <link rel='stylesheet' type='text/css' href='assets/applications/mailbox.css'>

    <!-- Admin theme -->

    <link rel='stylesheet' type='text/css' href='assets/themes/admin/layout.css'>
    <link rel='stylesheet' type='text/css' href='assets/themes/admin/color-schemes/default.css'>

    <!-- Components theme -->

    <link rel='stylesheet' type='text/css' href='assets/themes/components/default.css'>
    <link rel='stylesheet' type='text/css' href='assets/themes/components/border-radius.css'>

    <!-- Admin responsive -->

    <link rel='stylesheet' type='text/css' href='assets/helpers/responsive-elements.css'>
    <link rel='stylesheet' type='text/css' href='assets/helpers/admin-responsive.css'>

    <!-- JS Core -->

    <script type='text/javascript' src='assets/js-core/jquery-core.js'></script>
    <script type='text/javascript' src='assets/js-core/jquery-ui-core.js'></script>
    <script type='text/javascript' src='assets/js-core/jquery-ui-widget.js'></script>
    <script type='text/javascript' src='assets/js-core/jquery-ui-mouse.js'></script>
    <script type='text/javascript' src='assets/js-core/jquery-ui-position.js'></script>
    <!--<script type='text/javascript' src='assets/js-core/transition.js'></script>-->
    <script type='text/javascript' src='assets/js-core/modernizr.js'></script>
    <script type='text/javascript' src='assets/js-core/jquery-cookie.js'></script>
    <script type='text/javascript' src='assets/bootstrap/js/bootstrap.js'></script>
    <script type='text/javascript' src='assets/widgets/progressbar/progressbar.js'></script>
    <script type='text/javascript' src='assets/widgets/superclick/superclick.js'></script>
    <script type='text/javascript' src='assets/widgets/input-switch/inputswitch-alt.js'></script>
    <script type='text/javascript' src='assets/widgets/slimscroll/slimscroll.js'></script>
    <script type='text/javascript' src='assets/widgets/slidebars/slidebars.js'></script>
    <script type='text/javascript' src='assets/widgets/slidebars/slidebars-demo.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/piegage/piegage.js'></script>
    <script type='text/javascript' src='assets/widgets/charts/piegage/piegage-demo.js'></script>
    <script type='text/javascript' src='assets/widgets/screenfull/screenfull.js'></script>
    <script type='text/javascript' src='assets/widgets/content-box/contentbox.js'></script>
    <script type='text/javascript' src='assets/widgets/overlay/overlay.js'></script>
    <script type='text/javascript' src='assets/js-init/widgets-init.js'></script>
    <script type='text/javascript' src='assets/themes/admin/layout.js'></script>
    <script type='text/javascript' src='assets/widgets/theme-switcher/themeswitcher.js'></script>
    <script type='text/javascript' src='assets/widgets/tabs-ui/tabs.js'></script>
    <script type='text/javascript' src='assets/widgets/tabs/tabs.js'></script>
    <script type='text/javascript' src='assets/widgets/accordion-ui/accordion.js'></script>
    <script type='text/javascript' src='js/baseObject.js'></script>
    <script type='text/javascript' src='js/base64.js'></script>
    <script type='text/javascript' src='https://lipis.github.io/bootstrap-sweetalert/dist/sweetalert.js'></script>

    ".$this->loadScript()."
    <script type='text/javascript'>
        $(window).load(function(){
            setTimeout(function() {
                $('#loading').fadeOut( 400, 'linear' );
            }, 300);
        });
    </script>

    </head>
    ";

  }

  function loadScript(){
    return "";
  }
  function menuBar(){
    $getUser = $this->sqlArray($this->sqlQuery("select * from admin where username = '".$_SESSION['username']."'"));
    $menuBar = "
    <div id='page-header' class='bg-gradient-9'>
        <div id='mobile-navigation'>
        <button id='nav-toggle' class='collapsed' data-toggle='collapse' data-target='#page-sidebar'><span></span></button>
        <a href='pages.php' class='logo-content-small' title='MonarchUI'></a>
        </div>
        <div id='header-logo' class='logo-bg'>
        <a href='pages.php' class='logo-content-big' >
          Monarch <i>UI</i>
          <span>The perfect solution for user interfaces</span>
        </a>
        <a href='pages.php' class='logo-content-small' title='MonarchUI'>
          Monarch <i>UI</i>
          <span>The perfect solution for user interfaces</span>
        </a>
        <a id='close-sidebar' href='#' title='Close sidebar'>
          <i class='glyph-icon icon-angle-left'></i>
        </a>
        </div>
        <div id='header-nav-left'>

        </div><!-- #header-nav-left -->

        ".$this->setMenuEdit()."

</div>
    ";
    return $menuBar;
  }
  function emptyMenuBar(){
    $getUser = $this->sqlArray($this->sqlQuery("select * from admin where username = '".$_SESSION['username']."'"));
    $menuBar = "
    <div id='page-header' class='bg-gradient-9'>
        <div id='mobile-navigation'>
        <button id='nav-toggle' class='collapsed' data-toggle='collapse' data-target='#page-sidebar'><span></span></button>
        <a href='pages.php' class='logo-content-small' title='MonarchUI'></a>
        </div>
        <div id='header-logo' class='logo-bg'>
        <a href='pages.php' class='logo-content-big' >
          Monarch <i>UI</i>
          <span>The perfect solution for user interfaces</span>
        </a>
        <a href='pages.php' class='logo-content-small' title='MonarchUI'>
          Monarch <i>UI</i>
          <span>The perfect solution for user interfaces</span>
        </a>
        <a id='close-sidebar' href='#' title='Close sidebar'>
          <i class='glyph-icon icon-angle-left'></i>
        </a>
        </div>
        <div id='header-nav-left'>
        <div class='user-account-btn dropdown'>
          <a href='#' title='My Account' class='user-profile clearfix' data-toggle='dropdown'>
              <img width='28' src='assets/image-resources/gravatar.jpg' alt='Profile image'>
              <span>".$getUser['nama']."</span>
              <i class='glyph-icon icon-angle-down'></i>
          </a>
          <div class='dropdown-menu float-left'>
              <div class='box-sm'>
                  <div class='login-box clearfix'>
                      <div class='user-img'>
                          <a href='#' title='' class='change-img'>Change photo</a>
                          <img src='assets/image-resources/gravatar.jpg' alt=''>
                      </div>
                      <div class='user-info'>
                          <span>
                              ".$getUser['nama']."
                              <i>UX/UI developer</i>
                          </span>
                          <a href='#' title='Edit profile'>Edit profile</a>
                          <a href='#' title='View notifications'>View notifications</a>
                      </div>
                  </div>
                  <div class='pad5A button-pane button-pane-alt text-center'>
                      <a href='#' class='btn display-block font-normal btn-danger'>
                          <i class='glyph-icon icon-power-off'></i>
                          Logout
                      </a>
                  </div>
              </div>
          </div>
        </div>
        </div><!-- #header-nav-left -->

</div>
    ";
    return $menuBar;
  }
  function sideBar(){
    $arraySideBar = array(
        array("dashboard","
            <a href='pages.php' title='Dashboard'>
                <i class='glyph-icon icon-area-chart'></i>
                <span>Dashboard</span>
            </a>
            "),
        array("refMember","
            <a href='pages.php?page=refMember' title='Member'>
                <i class='glyph-icon icon-elusive-user'></i>
                <span>Member</span>
            </a>
            "),
        array("refSession","
            <a href='pages.php?page=refSession' title='Session'>
                <i class='glyph-icon icon-circle-o-notch'></i>
                <span>Session</span>
            </a>
            "),
        array("refKategori","
            <a href='pages.php?page=refKategori' title='Kategori'>
                <i class='glyph-icon icon-list-ul'></i>
                <span>Kategori</span>
            </a>
            "),
        array("refSoal","
            <a href='pages.php?page=refSoal' title='Soal'>
                <i class='glyph-icon icon-lightbulb-o'></i>
                <span>Soal</span>
            </a>
            "),
        array("refHadiah","
            <a href='pages.php?page=refHadiah' title='Hadiah'>
                <i class='glyph-icon icon-typicons-gift'></i>
                <span>Hadiah</span>
            </a>
            "),
        array("refAds","
            <a href='pages.php?page=refAds' title='Ads'>
                <i class='glyph-icon icon-bolt'></i>
                <span>Ads</span>
            </a>
            "),
        array("refBerita","
            <a href='pages.php?page=refBerita' title='Ads'>
                <i class='glyph-icon icon-typicons-news'></i>
                <span>Berita</span>
            </a>
            "),

        array("logout","
            <a href='pages.php?page=logout' title='bandingDatabaseServer'>
                <i class='glyph-icon icon-power-off'></i>
                <span>Logout</span>
            </a>
            "),

    );

    $isi = $_GET['page'];
    for($i=0;$i<count($arraySideBar);$i++) {
      if(!isset($isi)){
        if($arraySideBar[$i][0] == 'dashboard'){
          $Sel = 'activeSidebar';
        }else{
          $Sel = "";
        }
      }else{
        $Sel = $isi==$arraySideBar[$i][0]?" activeSidebar ":"";
      }
      $listSideBar .= "<li class='$Sel'>{$arraySideBar[$i][1]}</li>";
    }

    $sideBar = "
    <div id='page-sidebar' style='height: 3195px;' class='bg-black font-inverse'>
    <div class='scroll-sidebar' style='height: 3195px;'>
      <ul id='sidebar-menu' class='sf-js-enabled sf-arrows'>
        $listSideBar
      </ul>
    </div>
</div>
    ";
    return $sideBar;
  }

  function pageContent(){
    $pageContent = "
    <div id='page-title'>
      <h2>Responsive tables</h2>
      <p>MonarchUI comes packed with out of the box responsive tables.</p>
    </div>
    <div class='panel'>
      <div class='panel-body'>
          <h3 class='title-hero'>
              Hide columns
          </h3>
          <div class='example-box-wrapper'>
              <div class='hide-columns'>
                  <table class='table table-bordered table-striped table-condensed'>
                      <thead>
                      <tr>
                          <th>Code</th>
                          <th>Company</th>
                          <th class='numeric'>Price</th>
                          <th class='numeric'>Change</th>
                          <th class='numeric'>Change %</th>
                          <th class='numeric'>Open</th>
                          <th class='numeric'>High</th>
                          <th class='numeric'>Low</th>
                          <th class='numeric'>Volume</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                          <td>AAC</td>
                          <td>AUSTRALIAN AGRICULTURAL COMPANY LIMITED.</td>
                          <td class='numeric'>$1.38</td>
                          <td class='numeric'>-0.01</td>
                          <td class='numeric'>-0.36%</td>
                          <td class='numeric'>$1.39</td>
                          <td class='numeric'>$1.39</td>
                          <td class='numeric'>$1.38</td>
                          <td class='numeric'>9,395</td>
                      </tr>
                      <tr>
                          <td>AAD</td>
                          <td>ARDENT LEISURE GROUP</td>
                          <td class='numeric'>$1.15</td>
                          <td class='numeric'>  +0.02</td>
                          <td class='numeric'>1.32%</td>
                          <td class='numeric'>$1.14</td>
                          <td class='numeric'>$1.15</td>
                          <td class='numeric'>$1.13</td>
                          <td class='numeric'>56,431</td>
                      </tr>
                      <tr>
                          <td>AAX</td>
                          <td>AUSENCO LIMITED</td>
                          <td class='numeric'>$4.00</td>
                          <td class='numeric'>-0.04</td>
                          <td class='numeric'>-0.99%</td>
                          <td class='numeric'>$4.01</td>
                          <td class='numeric'>$4.05</td>
                          <td class='numeric'>$4.00</td>
                          <td class='numeric'>90,641</td>
                      </tr>
                      <tr>
                          <td>ABC</td>
                          <td>ADELAIDE BRIGHTON LIMITED</td>
                          <td class='numeric'>$3.00</td>
                          <td class='numeric'>  +0.06</td>
                          <td class='numeric'>2.04%</td>
                          <td class='numeric'>$2.98</td>
                          <td class='numeric'>$3.00</td>
                          <td class='numeric'>$2.96</td>
                          <td class='numeric'>862,518</td>
                      </tr>
                      <tr>
                          <td>ABP</td>
                          <td>ABACUS PROPERTY GROUP</td>
                          <td class='numeric'>$1.91</td>
                          <td class='numeric'>0.00</td>
                          <td class='numeric'>0.00%</td>
                          <td class='numeric'>$1.92</td>
                          <td class='numeric'>$1.93</td>
                          <td class='numeric'>$1.90</td>
                          <td class='numeric'>595,701</td>
                      </tr>
                      <tr>
                          <td>ABY</td>
                          <td>ADITYA BIRLA MINERALS LIMITED</td>
                          <td class='numeric'>$0.77</td>
                          <td class='numeric'>  +0.02</td>
                          <td class='numeric'>2.00%</td>
                          <td class='numeric'>$0.76</td>
                          <td class='numeric'>$0.77</td>
                          <td class='numeric'>$0.76</td>
                          <td class='numeric'>54,567</td>
                      </tr>
                      <tr>
                          <td>ACR</td>
                          <td>ACRUX LIMITED</td>
                          <td class='numeric'>$3.71</td>
                          <td class='numeric'>  +0.01</td>
                          <td class='numeric'>0.14%</td>
                          <td class='numeric'>$3.70</td>
                          <td class='numeric'>$3.72</td>
                          <td class='numeric'>$3.68</td>
                          <td class='numeric'>191,373</td>
                      </tr>
                      <tr>
                          <td>ADU</td>
                          <td>ADAMUS RESOURCES LIMITED</td>
                          <td class='numeric'>$0.72</td>
                          <td class='numeric'>0.00</td>
                          <td class='numeric'>0.00%</td>
                          <td class='numeric'>$0.73</td>
                          <td class='numeric'>$0.74</td>
                          <td class='numeric'>$0.72</td>
                          <td class='numeric'>8,602,291</td>
                      </tr>
                      <tr>
                          <td>AGG</td>
                          <td>ANGLOGOLD ASHANTI LIMITED</td>
                          <td class='numeric'>$7.81</td>
                          <td class='numeric'>-0.22</td>
                          <td class='numeric'>-2.74%</td>
                          <td class='numeric'>$7.82</td>
                          <td class='numeric'>$7.82</td>
                          <td class='numeric'>$7.81</td>
                          <td class='numeric'>148</td>
                      </tr>
                      <tr>
                          <td>AGK</td>
                          <td>AGL ENERGY LIMITED</td>
                          <td class='numeric'>$13.82</td>
                          <td class='numeric'>  +0.02</td>
                          <td class='numeric'>0.14%</td>
                          <td class='numeric'>$13.83</td>
                          <td class='numeric'>$13.83</td>
                          <td class='numeric'>$13.67</td>
                          <td class='numeric'>846,403</td>
                      </tr>
                      <tr>
                          <td>AGO</td>
                          <td>ATLAS IRON LIMITED</td>
                          <td class='numeric'>$3.17</td>
                          <td class='numeric'>-0.02</td>
                          <td class='numeric'>-0.47%</td>
                          <td class='numeric'>$3.11</td>
                          <td class='numeric'>$3.22</td>
                          <td class='numeric'>$3.10</td>
                          <td class='numeric'>5,416,303</td>
                      </tr>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
    </div>

    ";
    return $pageContent;
  }
  function pageShow(){
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
  ".$this->menuBar()."
  ".$this->sidebar()."
  <div id='page-content-wrapper'>
      <div id='page-content'>
        <div class='container'>
          ".$this->pageContent()."
        </div>
      </div>
  </div>
</div>
</body>
</html>
    ";

    return $pageShow;
  }

  function switchOption($id,$value,$params){
    if($value == 'LIVE' || $value = 'ACTIVE'){
      $value = "on";
    }else{
      $value = "off";
    }
    $html =
    "<div class='bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-medium bootstrap-switch-animate bootstrap-switch-$value'>
      <div class='bootstrap-switch-container'>
        <input type='checkbox' data-on-color='primary' name='$id' id='$id'  class='input-switch' checked='' $params data-size='medium' data-on-text='On' data-off-text='Off'>
      </div>
    </div>";
    return $html;
  }

  function checkBox($id,$value,$checked,$text){
    // $arrayWarna = array(
    //                 array('blue','info'),
    //                 array('green','success'),
    //                 array('red','danger'),
    //               );
    // for($i=0;$i<count($arrayWarna);$i++) {
    //   $Sel = $warna==$arrayWarna[$i][0]?"".$arrayWarna[$i][1]."":"";
    // }
    $html = "
    <div class='col-md-3'>
        <div class='checkbox checkbox-info'>
            <label style='padding-left:0px;'>
                <div class='checker' id='uniform-inlineCheckbox114'><span class=''><input type='checkbox' id='$id' name='$id'  $checked class='custom-checkbox'>
                <i class='glyph-icon icon-check'></i></span></div>$text
            </label>
        </div>
    </div>";
    return $html;
  }
  function generateDate($tanggal){
        $tanggal = explode('-',$tanggal);
        $tanggal = $tanggal[2]."-".$tanggal[1]."-".$tanggal[0];
        return $tanggal;
  }
  function putImage($baseImage,$imageLocation){
    $this->baseToImage($baseImage,$imageLocation);
    return $imageLocation;
  }
  function baseToImage($base64_string, $output_file) {
		$ifp = fopen( $output_file, 'wb' );
		$data = explode( ',', $base64_string );
		fwrite( $ifp, base64_decode( $data[ 1 ] ) );
		fclose( $ifp );
		return str_replace("../",$output_file);
  }
  function imageToBase($path){
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
  }



}



 ?>
