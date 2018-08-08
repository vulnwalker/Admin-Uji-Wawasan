<?php
class fileManager extends baseObject{
  var $Prefix = "fileManager";
  var $formName = "fileManagerForm";
  var $tableName = "ref_server";

  function jsonGenerator(){
    $cek = ''; $err=''; $content=''; $json=TRUE;
    foreach ($_POST as $key => $value) {
       $$key = $value;
    }
	  switch($_GET['API']){
      case 'Release':{
        for ($i=0; $i < sizeof($fileManager_cb); $i++) {
            $explodeSelected = explode(";",$fileManager_cb[$i]);
            $arrayPush[] = array(
              'nama'=>$explodeSelected[0],
              'type'=>$explodeSelected[1],
            );
        }
        $formCaption = "Push To Release";
        $comboRelease = $this->cmbQuery("cmbRelease","","select id,nama_release from ref_release","onchange=$this->Prefix.releaseChanged() class='form-control'","-- RELEASE --");
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalRelease' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
          <form name='".$this->formName."_release' id='".$this->formName."_release'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>$formCaption</h4>
                    </div>
                    <div class='modal-body'>
                      <div class='form-group'>
                        <div class='row'>
                            <label class='col-sm-2 control-label' style='margin-top:6px;'>Release</label>
                            <div class='col-sm-10'>
                                $comboRelease
                            </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                            <label class='col-sm-2 control-label' style='margin-top:6px;'>Folder Release</label>
                            <div class='col-sm-10'>
                                <input type='text' class='form-control'  placeholder='Folder Release' id='folderRelease' name='folderRelease' readonly>
                            </div>
                        </div>
                      </div>
                      <div class='form-group'>
                        <div class='row'>
                            <label class='col-sm-2 control-label' style='margin-top:6px;'>Sub Folder</label>
                            <div class='col-sm-10'>
                                <input type='text' class='form-control'  placeholder='Sub Folder' id='subFolder' name='subFolder'>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <input type='hidden' name='listDataPush' id='listDataPush' value='".json_encode($arrayPush)."'>
                        <input type='hidden' name='dirLocation' id='dirLocation' value='".$currentLocation."'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.pushRelease();>Push</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
          </form>
        </div>";
  		break;
  		}
      case 'formNewFolder':{
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalFolder' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>Folder Baru</h4>
                    </div>
                    <div class='modal-body'>
                      <div class='row'>
                          <label class='col-sm-2 control-label' style='margin-top:6px;'>Nama Folder</label>
                          <div class='col-sm-10'>
                              <input type='text' class='form-control'  placeholder='Nama Folder' id='namaFolder' name='namaFolder'>
                          </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveNewDir();>Simpan</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
        </div>";
  		break;
  		}
      case 'formRenameFolder':{
        $content="
        <div class='modal fade bs-example-modal-lg' id='modalFolder' tabindex='-1' role='dialog' aria-labelledby='myLargeModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                        <h4 class='modal-title'>Folder Baru</h4>
                    </div>
                    <div class='modal-body'>
                      <div class='row'>
                          <label class='col-sm-2 control-label' style='margin-top:6px;'>Nama Folder</label>
                          <div class='col-sm-10'>
                              <input type='text' class='form-control'  placeholder='Nama Folder' id='namaFolder' name='namaFolder' value='$namaFolder'>
                              <input type='hidden' class='form-control' id='hiddenNamaFolder' name='hiddenNamaFolder' value='$namaFolder'>
                          </div>
                      </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-primary' onclick=$this->Prefix.saveRenameFolder();>Simpan</button>
                        <button type='button' class='btn btn-default' data-dismiss='modal' id='closeModal'>Batal</button>
                    </div>
                </div>
            </div>
        </div>";
  		break;
  		}
      case 'pushRelease':{
          $arrayData = json_decode($listDataPush);
          if (!is_dir($folderRelease)) {
              mkdir($folderRelease);
          }
          if (!is_dir($folderRelease."/".$subFolder) && !empty($subFolder)) {
            $explodeSubFolder = explode("/",$subFolder);
            if(!is_dir($folderRelease."/".$explodeSubFolder[0])){
              for ($i=0; $i < sizeof($explodeSubFolder) ; $i++) {
                mkdir($folderRelease."/".$folderKu.$explodeSubFolder[$i]);
                $folderKu.="/".$$explodeSubFolder[$i];
              }
            }
          }
          for ($i=0; $i < sizeof($arrayData) ; $i++) {
              if($arrayData[$i]->type == 'file'){
                $cek .= $arrayData[$i]->nama.":".$arrayData[$i]->type.";";
                copy($dirLocation."/".$arrayData[$i]->nama, $folderRelease."/".$subFolder."/".$arrayData[$i]->nama);
              }elseif($arrayData[$i]->type == 'dir'){
                $this->copyDir($dirLocation."/".$arrayData[$i]->nama,$folderRelease."/".$subFolder."/".$arrayData[$i]->nama);
                $cek .= $arrayData[$i]->nama.":".$arrayData[$i]->type.";";
              }
          }


  		break;
  		}
      case 'refreshList':{

        $content=array('tableContent' => $this->generateTable($currentLocation));

  		break;
  		}
      case 'releaseChanged':{
        $getDataRelease = $this->sqlArray($this->sqlQuery("select * from ref_release where id = '$cmbRelease'"));
        $content = array('folderRelease' => $getDataRelease['directory_location']);
  		break;
  		}
      case 'Edit':{
        $explodeSelected = explode(';',$fileManager_cb[0]);
        $namaFile = $explodeSelected[0];
        if($explodeSelected[1] == 'dir')$err = "Folder tidak dapat diubah";
        $content = array("namaFile" => $namaFile);
  		break;
  		}
      case 'saveNew':{
  			if(empty($fileName)){
          $err = "Isi Nama File";
        }
        if(empty($err)){
          file_put_contents($location."/".$fileName,$isiFile);
        }
  		break;
  		}
      case 'saveNewDir':{
  			if(empty($namaFolder)){
          $err = "Isi Nama Folder";
        }elseif (is_dir($location."/".$namaFolder)) {
            $err = "Folder sudah ada";
        }
        if(empty($err)){
            mkdir($location."/".$namaFolder);
        }
  		break;
  		}
      case 'saveRenameFolder':{
  			if(empty($namaFolder)){
          $err = "Isi Nama Folder";
        }
        if(empty($err)){
            rename($location."/".$hiddenNamaFolder,$location."/".$namaFolder);
        }
  		break;
  		}
      case 'saveEdit':{
        if(empty($fileName)){
          $err = "Isi Nama File";
        }
        if(empty($err)){
          if($hiddenNamaFile != $fileName){
            unlink($location."/".$hiddenNamaFile);
          }
          file_put_contents($location."/".$fileName,$isiFile);
        }
  		break;
  		}
      case 'Hapus':{
        for ($i=0; $i < sizeof($fileManager_cb) ; $i++) {
          $explodeSelected = explode(';',$fileManager_cb[$i]);
          if($explodeSelected[1] == 'file'){
            unlink($currentLocation."/".$explodeSelected[0]);
          }else{
            $this->unlinkDir($currentLocation."/".$explodeSelected[0]);
          }

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
    return "

    <script type='text/javascript' src='js/fileManager/fileManager.js'></script>
    <script src='plugins/monaco-editor/min/vs/loader.js'></script>
    <script src='plugins/contextMenu/contextMenu.min.js'></script>
    <link rel='stylesheet' type='text/css' href='plugins/contextMenu/contextMenu.min.css'>
    ";
  }
  
  function setMenuEdit(){
    $setMenuEdit = "
    <div id='header-nav-right'>
    <a href='#' class='hdr-btn popover-button' title='Search' data-placement='bottom' data-id='#popover-search'>
      <i class='glyph-icon icon-search'></i>
    </a>
    <div class='hide' id='popover-search'>
      <div class='pad5A '>
          <div class='input-group'>
              <input type='text' class='form-control' id='filterCari' name='filterCari' onkeyup=$this->Prefix.setValueFilter(this) placeholder='Cari data'>
              <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
                  <a class='btn btn-primary' >Cari</a>
              </span>
          </div>
      </div>
    </div>
    <a class='header-btn' style='cursor:pointer;'  onclick=$this->Prefix.Release(); title='Push To Release'>
      <i class='glyph-icon icon-linecons-attach'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Baru(); title='Baru'>
      <i class='glyph-icon icon-plus'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Edit(); title='Edit'>
      <i class='glyph-icon icon-pencil'></i>
    </a>
    <a class='header-btn' style='cursor:pointer;' id='logout-btn' onclick=$this->Prefix.Hapus(); title='Hapus'>
      <i class='glyph-icon icon-trash'></i>
    </a>

    </div>

    ";
    return $setMenuEdit;
  }
  function pageContent(){
    $pageContent = "
    <div id='page-title'>
      <h2>File Manager</h2>
    </div>
    <div class='panel'>
      <div class='panel-body'>
        <form name='$this->formName' id='$this->formName'>
              ".$this->generateTable($_GET['location'])."
        </form>
      </div>
      <div id='tempatModal'> </div>
    </div>

    ";
    return $pageContent;
  }

  function setKolomHeader(){
    $rightClick = "
    <script>
    var menuOption = [
      {
        name: 'New Folder',
        img: 'http://ignitersworld.com/lab/assets/images/create.png',
        title: 'New Directory',
        fun: function () {
            $this->Prefix.formNewFolder();
        }
      }
    ];
    var menuTrgr=$('#rowHeader');
    menuTrgr.contextMenu(menuOption,{
         triggerOn :'contextmenu',
         mouseClick : 'right'
    });
    </script>
    ";
    $kolomHeader = "
    <thead>
      <tr id='rowHeader'>
          <th style='text-align:center;'>".$this->checkAll(1000,$this->Prefix)."</th>
          <th>Nama</th>
          <th>Type</th>
          <th>Size</th>
          <th>Modified</th>
          <th>Owner</th>
          <th>Permisson</th>
      </tr>
      $rightClick
    </thead>";
    return $kolomHeader;
  }
  function setKolomData($no,$arrayData){
    foreach ($arrayData as $key => $value) {
       $$key = $value;
    }
    if($jenisDir == '.' || $jenisDir=='..'){
      $checkBox = "<td style='text-align:center;'></td>";
    }else{
      $checkBox = "<td style='text-align:center;'>".$this->setCekBoxFile($no - 3,"$jenisDir;$typeFile",$this->Prefix)."</td>";
    }
    if($typeFile == 'dir'){
      $action = "onclick=$this->Prefix.refreshList('$dirLocation');";
    }
    if($jenisDir != '.'){
      if($jenisDir !='..' ){
        $jenisDir = $this->hexEncode($jenisDir);
        $rightClick = "
        <script>
        var menuOption = [
          {
            name: 'New Folder',
            img: 'http://ignitersworld.com/lab/assets/images/create.png',
            title: 'New Directory',
            fun: function () {
                $this->Prefix.formNewFolder();
            }
          },
          {
              name: 'Rename',
              img: 'http://ignitersworld.com/lab/assets/images/update.png',
              title: 'Rename',
              fun: function () {
                  $this->Prefix.formRenameFolder('$jenisDir');
              }

          }
        ];
        var menuTrgr=$('#$jenisDir');
        menuTrgr.contextMenu(menuOption,{
             triggerOn :'contextmenu',
        	   mouseClick : 'right'
        });
        </script>
        ";
      }

      $tableRow = "
      <tr id='$jenisDir'>
          $checkBox
          <td><span style='cursor:pointer;'   $action>".$namaFile."</span>
              $rightClick
          </td>
          <td>".$typeFile."</td>
          <td>".$sizeFile."</td>
          <td>".$last_modified."</td>
          <td>".$owner."</td>
          <td>".$permission."</td>

      </tr>
      ";
    }
    return $tableRow;
  }
  function generateTable($locationDirectory){
    $no = 1;
    if(empty($locationDirectory)){
      $dir = getcwd();
    }else{
      $dir = $locationDirectory;
    }
    $scandir = scandir($dir);
    foreach($scandir as $dirx) {
      $dtype = filetype("$dir/$dirx");
      $dtime = date("F d Y g:i:s", filemtime("$dir/$dirx"));
      if(function_exists('posix_getpwuid')) {
        $downer = @posix_getpwuid(fileowner("$dir/$dirx"));
        $downer = $downer['name'];
      } else {
        //$downer = $uid;
        $downer = fileowner("$dir/$dirx");
      }
      if(function_exists('posix_getgrgid')) {
        $dgrp = @posix_getgrgid(filegroup("$dir/$dirx"));
        $dgrp = $dgrp['name'];
      } else {
        $dgrp = filegroup("$dir/$dirx");
      }
      if(!is_dir("$dir/$dirx")) continue;
      if($dirx === '..') {
        $href = "".dirname($dir)."";
      } elseif($dirx === '.') {
        $href = "$dirx";
      } else {
        $href = "$dir/$dirx";
      }
      if($dirx === '.' || $dirx === '..') {
        $act_dir = "<a href='?act=newfile&dir=$dir'>newfile</a> | <a href='?act=newfolder&dir=$dir'>newfolder</a>";
        } else {
        $act_dir = "<a href='?act=rename_dir&dir=$dir/$dirx'>rename</a> | <a href='?act=delete_dir&dir=$dir/$dirx'>delete</a>";
      }
      $arrayFile = array(
        'dirLocation' => $href,
        'jenisDir' => $dirx,
        'namaFile' => "<img src='data:image/png;base64,R0lGODlhEwAQALMAAAAAAP///5ycAM7OY///nP//zv/OnPf39////wAAAAAAAAAAAAAAAAAAAAAA"."AAAAACH5BAEAAAgALAAAAAATABAAAARREMlJq7046yp6BxsiHEVBEAKYCUPrDp7HlXRdEoMqCebp"."/4YchffzGQhH4YRYPB2DOlHPiKwqd1Pq8yrVVg3QYeH5RYK5rJfaFUUA3vB4fBIBADs='>$dirx",
        'typeFile' => $dtype,
        'sizeFile' => "-",
        'last_modified' => $dtime,
        'owner' => $downer,
        'permission' => $this->w("$dir/$dirx",$this->perms("$dir/$dirx")),
      );
      $kolomData.=$this->setKolomData($no,$arrayFile);
      $no++;
    }
    foreach($scandir as $file) {
			$ftype = filetype("$dir/$file");
			$ftime = date("F d Y g:i:s", filemtime("$dir/$file"));
			$size = filesize("$dir/$file")/1024;
			$size = round($size,3);
			if(function_exists('posix_getpwuid')) {
				$fowner = @posix_getpwuid(fileowner("$dir/$file"));
				$fowner = $fowner['name'];
			} else {
				//$downer = $uid;
				$fowner = fileowner("$dir/$file");
			}
			if(function_exists('posix_getgrgid')) {
				$fgrp = @posix_getgrgid(filegroup("$dir/$file"));
				$fgrp = $fgrp['name'];
			} else {
				$fgrp = filegroup("$dir/$file");
			}
			if($size > 1024) {
				$size = round($size/1024,2). 'MB';
			} else {
				$size = $size. 'KB';
			}
			if(!is_file("$dir/$file")) continue;
      $arrayFile = array(
        'jenisDir' => $file,
        'namaFile' => "<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9oJBhcTJv2B2d4AAAJMSURBVDjLbZO9ThxZEIW/qlvdtM38BNgJQmQgJGd+A/MQBLwGjiwH3nwdkSLtO2xERG5LqxXRSIR2YDfD4GkGM0P3rb4b9PAz0l7pSlWlW0fnnLolAIPB4PXh4eFunucAIILwdESeZyAifnp6+u9oNLo3gM3NzTdHR+//zvJMzSyJKKodiIg8AXaxeIz1bDZ7MxqNftgSURDWy7LUnZ0dYmxAFAVElI6AECygIsQQsizLBOABADOjKApqh7u7GoCUWiwYbetoUHrrPcwCqoF2KUeXLzEzBv0+uQmSHMEZ9F6SZcr6i4IsBOa/b7HQMaHtIAwgLdHalDA1ev0eQbSjrErQwJpqF4eAx/hoqD132mMkJri5uSOlFhEhpUQIiojwamODNsljfUWCqpLnOaaCSKJtnaBCsZYjAllmXI4vaeoaVX0cbSdhmUR3zAKvNjY6Vioo0tWzgEonKbW+KkGWt3Unt0CeGfJs9g+UU0rEGHH/Hw/MjH6/T+POdFoRNKChM22xmOPespjPGQ6HpNQ27t6sACDSNanyoljDLEdVaFOLe8ZkUjK5ukq3t79lPC7/ODk5Ga+Y6O5MqymNw3V1y3hyzfX0hqvJLybXFd++f2d3d0dms+qvg4ODz8fHx0/Lsbe3964sS7+4uEjunpqmSe6e3D3N5/N0WZbtly9f09nZ2Z/b29v2fLEevvK9qv7c2toKi8UiiQiqHbm6riW6a13fn+zv73+oqorhcLgKUFXVP+fn52+Lonj8ILJ0P8ZICCF9/PTpClhpBvgPeloL9U55NIAAAAAASUVORK5CYII='>$file",
        'typeFile' => $ftype,
        'sizeFile' => $size,
        'last_modified' => $ftime,
        'owner' => $fowner,
        'permission' => $this->w("$dir/$file",$this->perms("$dir/$file")),
      );
      $kolomData.=$this->setKolomData($no,$arrayFile);
      $no++;
		}

    $htmlTable = "

        <div class='row'>
          <div class='form-group'>
              <label class='col-sm-2 control-label' style='margin-top:6px;'>Current Location</label>
              <div class='col-sm-7'>
                  <input type='text' class='form-control' placeholder='Current Location' id='currentLocation' name='currentLocation' value='$dir'>
              </div>
              <div class='col-sm-1'>
                <span class='input-group-btn' onclick=$this->Prefix.refreshList();>
                    <a class='btn btn-primary' >Tampilkan</a>
                </span>
              </div>
          </div>
        </div>
        <br>

        <table class='table table-bordered table-striped table-hover table-condensed'>
            ".$this->setKolomHeader()."
            <tbody>
              $kolomData
            </tbody>
        </table>
        <input type='hidden' name='".$this->Prefix."_jmlcek' id='".$this->Prefix."_jmlcek' value='0'>
    ";
    return $htmlTable;
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
  function pageShowEdit($idEdit){
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
                ".$this->formEdit($idEdit)."
              </div>
            </div>
        </div>
      </div>
      </body>
      </html>
          ";

    return $pageShow;
  }

  function formBaru(){
    $arrayProgramingLanguage = array(
      array('php','PHP'),
      array('javascript','Java Script'),
      array('css','CSS'),
      array('html','HTML'),
      array('sql','SQL'),

    );
    $comboProgramingLanguage = $this->cmbArray("programingLanguage","",$arrayProgramingLanguage,"-- PROGRAMING LANGUAGE --","class='form-control' onchange=$this->Prefix.changeLanguage(); ");
    // setTimeout(function(){
    //   $('#codeEditor').attr('class','col-sm-12');
    //   $('.monaco-editor').attr('class','monaco-editor vs-dark col-sm-12');
    //   $('.monaco-editor').attr('style','height:800px;');
    //   $('.overflow-guard').attr('class','overflow-guard col-sm-12');
    //   $('.overflow-guard').attr('style','height:800px;');
    // },3000);
    $formBaru = "
    <script>
      var editor;
    	$this->Prefix.setCodeEditor('','php');

    </script>
    <div id='page-title'>
      <h2>File Baru</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_new' id='".$this->formName."_new'>
                <input type='hidden' name='location' id='location' value='".$_GET['location']."'>
                    <div class='form-group'>
                        <div class='col-sm-2'>
                          <input type='text' name='fileName' id='fileName' placeholder='Nama File' class='form-control'>
                        </div>
                        <div class='col-sm-2'>
                          $comboProgramingLanguage
                        </div>
                    </div>
                    <div class='form-group' class='col-sm-12'>
                      <div class='row'>
                        <div class='col-sm-12' id='codeEditor' style='height:800px;'></div>
                      </div>
                    </div>

                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12'>
                        <button type='button' onclick=$this->Prefix.saveNew(); class='btn btn-alt btn-hover btn-success'>
                            <span>Simpan</span>
                            <i class='glyph-icon icon-save'></i>
                        </button>
                        <button type='button' onclick=$this->Prefix.afterSave(); class='btn btn-alt btn-hover btn-danger'>
                            <span>Batal</span>
                            <i class='glyph-icon icon-times'></i>
                        </button>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    ";
    return $formBaru;
  }
  function formEdit($idEdit){
    foreach ($_GET as $key => $value) {
       $$key = $value;
    }
    $explodeExtension = explode('.',$namaFile);
    $arrayProgramingLanguage = array(
      array('php','PHP'),
      array('javascript','Java Script'),
      array('css','CSS'),
      array('html','HTML'),
      array('sql','SQL'),

    );
    $comboProgramingLanguage = $this->cmbArray("programingLanguage",$explodeExtension[1],$arrayProgramingLanguage,"-- PROGRAMING LANGUAGE --","class='form-control' onchange=$this->Prefix.changeLanguage(); ");
    $formEdit = "
    <script>
      var editor;
    	$this->Prefix.setCodeEditor('".base64_encode(file_get_contents($location."/".$namaFile))."','".$explodeExtension[1]."');
      setTimeout(function(){
        $('#codeEditor').attr('class','col-sm-12');
        $('.monaco-editor').attr('class','monaco-editor vs-dark col-sm-12');
        $('.monaco-editor').attr('style','height:800px;');
        $('.overflow-guard').attr('class','overflow-guard col-sm-12');
        $('.overflow-guard').attr('style','height:800px;');
      },3000);

    </script>
    <div id='page-title'>
      <h2>File Edit</h2>
    </div>
    <div class='panel'>
        <div class='panel-body'>
            <div class='example-box-wrapper'>
                <form class='form-horizontal bordered-row' name='".$this->formName."_edit' id='".$this->formName."_edit'>
                <input type='hidden' name='location' id='location' value='".$_GET['location']."'>
                <input type='hidden' name='hiddenNamaFile' id='hiddenNamaFile' value='$namaFile'>
                    <div class='form-group'>
                        <div class='col-sm-2'>
                          <input type='text' name='fileName' id='fileName' placeholder='Nama File' class='form-control' value='$namaFile'>
                        </div>
                        <div class='col-sm-2'>
                          $comboProgramingLanguage
                        </div>
                    </div>
                    <div class='form-group' class='col-sm-12'>
                      <div class='row'>
                        <div  id='codeEditor' style='height:800px;'></div>
                      </div>
                    </div>

                    <div class='form-group' style='float:right;'>
                        <div class='col-sm-12'>
                        <button type='button' onclick=$this->Prefix.saveEdit(); class='btn btn-alt btn-hover btn-success'>
                            <span>Simpan</span>
                            <i class='glyph-icon icon-save'></i>
                        </button>
                        <button type='button' onclick=$this->Prefix.afterSave(); class='btn btn-alt btn-hover btn-danger'>
                            <span>Batal</span>
                            <i class='glyph-icon icon-times'></i>
                        </button>
                        </div>
                    </div>



                </form>
            </div>
        </div>
    </div>
    ";
    return $formEdit;
  }


}
$fileManager = new fileManager();


 ?>
