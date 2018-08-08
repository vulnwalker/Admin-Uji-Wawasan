<?php
date_default_timezone_set('Asia/Jakarta');
error_reporting(0);
set_time_limit( 0 );
ini_set('max_execution_time',0);
class Config{
	 function connection(){
		return mysqli_connect("localhost", "root", "rf09thebye", "ujiwawasan");
	}
   function sqlQuery($script){
    return mysqli_query($this->connection(), $script);
  }
	function checkLogin(){
		if(isset($_SESSION['username'])){
			header('Location: pages.php');
		}
	}
  function sqlInsert($table, $data){
  	    if (is_array($data)) {
  	        $key   = array_keys($data);
  	        $kolom = implode(',', $key);
  	        $v     = array();
  	        for ($i = 0; $i < count($data); $i++) {
  	            array_push($v, "'" . $data[$key[$i]] . "'");
  	        }
  	        $values = implode(',', $v);
  	        $query  = "INSERT INTO $table ($kolom) VALUES ($values)";
  	    } else {
  	        $query = "INSERT INTO $table $data";
  	    }
  		  return $query;

  	}

  function sqlUpdate($table, $data, $where){
      if (is_array($data)) {
          $key   = array_keys($data);
          $kolom = implode(',', $key);
          $v     = array();
          for ($i = 0; $i < count($data); $i++) {
              array_push($v, $key[$i] . " = '" . $data[$key[$i]] . "'");
          }
          $values = implode(',', $v);
          $query  = "UPDATE $table SET $values WHERE $where";
      } else {
          $query = "UPDATE $table SET $data WHERE $where";
      }

     return $query;
  }

	function sqlArray($sqlQuery){
			return mysqli_fetch_assoc($sqlQuery);
	}

	function sqlRowCount($sqlQuery){
			return mysqli_num_rows($sqlQuery);
	}
	function generateDate($tanggal){
			$tanggal = explode("-",$tanggal);
			return $tanggal[2]."-".$tanggal[1]."-".$tanggal[0];
	}
	function w($dir,$perm) {
		if(!is_writable($dir)) {
			return "<font color=red>".$perm."</font>";
		} else {
			return "<font color=lime>".$perm."</font>";
		}
	}
	function perms($file){
		$perms = fileperms($file);
		if (($perms & 0xC000) == 0xC000) {
		// Socket
		$info = 's';
		} elseif (($perms & 0xA000) == 0xA000) {
		// Symbolic Link
		$info = 'l';
		} elseif (($perms & 0x8000) == 0x8000) {
		// Regular
		$info = '-';
		} elseif (($perms & 0x6000) == 0x6000) {
		// Block special
		$info = 'b';
		} elseif (($perms & 0x4000) == 0x4000) {
		// Directory
		$info = 'd';
		} elseif (($perms & 0x2000) == 0x2000) {
		// Character special
		$info = 'c';
		} elseif (($perms & 0x1000) == 0x1000) {
		// FIFO pipe
		$info = 'p';
		} else {
		// Unknown
		$info = 'u';
		}
			// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		(($perms & 0x0800) ? 's' : 'x' ) :
		(($perms & 0x0800) ? 'S' : '-'));
		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		(($perms & 0x0400) ? 's' : 'x' ) :
		(($perms & 0x0400) ? 'S' : '-'));
		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		(($perms & 0x0200) ? 't' : 'x' ) :
		(($perms & 0x0200) ? 'T' : '-'));
		return $info;
	}
	function checkAllFile($jumlahCheck,$Prefix){
		return "
							<input type='checkbox' class='custom-checkbox' name='".$Prefix."_toogle' id='".$Prefix."_toogle' onclick=$Prefix.checkSemua($jumlahCheck,'".$Prefix."_cb','$Prefix_toogle','".$Prefix."_jmlcek',this)>

						";
	}
	function setCekBoxFile($cb, $KeyValueStr, $Prefix){
    $hsl = "
      <input class='custom-checkbox' type='checkbox' id='".$Prefix."_cb$cb' name='".$Prefix."_cb[]' value='".$KeyValueStr."' onchange = $Prefix.thisChecked('".$Prefix."_cb$cb','".$Prefix."_jmlcek'); >
    ";
    return $hsl;
  }
	function unlinkDir($dir){
      $dirs = array($dir);
      $files = array() ;
      for($i=0;;$i++)
      {
          if(isset($dirs[$i]))
              $dir =  $dirs[$i];
          else
              break ;
          if($openDir = opendir($dir))
          {
              while($readDir = @readdir($openDir))
              {
                  if($readDir != "." && $readDir != "..")
                  {
                      if(is_dir($dir."/".$readDir))
                      {
                          $dirs[] = $dir."/".$readDir ;
                      }
                      else
                      {
                          $files[] = $dir."/".$readDir ;
                      }
                  }
              }
          }
      }
      foreach($files as $file)
      {
          unlink($file) ;
      }
      $dirs = array_reverse($dirs) ;
      foreach($dirs as $dir)
      {
          rmdir($dir) ;
      }
  }

	function copyDir( $source, $target ) {
	    if ( is_dir( $source ) ) {
	        @mkdir( $target );
	        $d = dir( $source );
	        while ( FALSE !== ( $entry = $d->read() ) ) {
	            if ( $entry == '.' || $entry == '..' ) {
	                continue;
	            }
	            $Entry = $source . '/' . $entry;
	            if ( is_dir( $Entry ) ) {
	                $this->copyDir( $Entry, $target . '/' . $entry );
	                continue;
	            }
	            copy( $Entry, $target . '/' . $entry );
	        }

	        $d->close();
	    }else {
	        copy( $source, $target );
	    }
	}
	function copyFile($sumber, $tujuan ) {
		$explodeDirTujuan = explode("/",$tujuan);
		$indexFile = sizeof($explodeDirTujuan);
		$directoryName = str_replace($explodeDirTujuan[$indexFile - 1],"",$tujuan);
		if(!is_dir($directoryName)){
		    mkdir($directoryName, 0777, true);
		}
		unlink($tujuan);
		$this->kembarSiam($sumber,$tujuan);
	}
	function kembarSiam($pathSource, $pathDest) {
    copy($pathSource, $pathDest) ;
    $dt = filemtime($pathSource);
    if ($dt === FALSE) return FALSE;
    return touch($pathDest, $dt);
	}


	function xSFTP( $source, $target,$connection ) {
	    if ( is_dir( $source ) ) {
	        $this->sshCommand($connection,"mkdir $target");
	        $d = dir( $source );
	        while ( FALSE !== ( $entry = $d->read() ) ) {
	            if ( $entry == '.' || $entry == '..' ) {
	                continue;
	            }
	            $Entry = $source . '/' . $entry;
	            if ( is_dir( $Entry ) ) {
	               $this->xSFTP( $Entry, $target . '/' . $entry,$connection );
	                continue;
	            }
	             ssh2_scp_send( $connection,$Entry, $target . '/' . $entry );
	        }
	        $d->close();
	    }else {
	        ssh2_scp_send( $connection,$source, $target );
	    }
	}

	function sshCommand($connection,$command){
	  $stream = ssh2_exec($connection, $command);
	  stream_set_blocking($stream, true);
	  $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
	  return stream_get_contents($stream_out);
	}

	function sshConnect($server,$ftpPort){
		return ssh2_connect($server, $ftpPort);
	}

	function sshLogin($sshConnection,$username,$password){
		return ssh2_auth_password($sshConnection, $username, $password);
	}

	function hexEncode($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}
	function hexDecode($hex){
	    $string='';
	    for ($i=0; $i < strlen($hex)-1; $i+=2){
	        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
	    }
	    return $string;
	}

	function numberFormat($angka,$jumlahNol = 0){
		return number_format($angka,$jumlahNol,',','.');
	}
	function getDetailDisk($id) {
		$getDataServer = $this->sqlArray($this->sqlQuery("select * from ref_server where id = '$id'"));
		$databasePassword = $getDataServer["password_mysql"];
		$userMysql = $getDataServer["user_mysql"];
		$sshConnection = $this->sshConnect($getDataServer['alamat_ip'],$getDataServer['port_ftp']);
		$this->sshLogin($sshConnection,$getDataServer['user_ftp'],$getDataServer['password_ftp']);
		$contentAWK = '{ print "{\"FileSystem\":\""$1"\",", "\"Size\":\""$2"\",", "\"Used\":\""$3"\",", "\"Free\":\""$4"\",", "\"Persen\":\""$5"\",", "\"Mounted\":\""$6"\"} "}';
		$comand =  str_replace("} \n{","},{",$this->sshCommand($sshConnection,"df -h | awk '$contentAWK'"));
		return "[".str_replace(" ","",$comand)."]";
	}
	function formGenerator($formFields){
		foreach ($formFields as $key=>$formComponent){
			if ($formComponent['type'] == ''){
				$val = $formComponent['value'];
				$marginLabel = "";

			}else{
				$val = $this->Entry($formComponent['value'],$key,$formComponent['parrams'],$formComponent['type']);
				$marginLabel = "margin-top:6px;";

			}
			$labelWidth = $formComponent['labelWidth'];
			$componentWidth = 12 - $labelWidth;
			$content .=
			"
				<div class='form-group'>
          <div class='row'>
              <label class='col-sm-$labelWidth control-label ' style='$marginLabel' >".$formComponent['label']."</label>
              <div class='col-sm-$componentWidth'>
                ".$val."
              </div>
          </div>
        </div>"
				;
		}
		return $content;
	}
	function Entry($val,$name='entry1',$param='', $EntryType=0){
			$hsl ='';
			switch($EntryType){
				//case 0: case '': $hsl=''; break;
				case 1: case 'hidden': $hsl = "<input type='hidden' id='$name' name='$name' value='$val' $param>" ;break;
				case 2: case 'text': $hsl = "<input type='text' id='$name' name='$name' value='$val' $param>" ;break;
				case 4: case 'number':
					$hsl =
						"<input type='text'
							onkeypress='return isNumberKey(event)'
							value='$val'
							name='$name'
							id='$name'
							$param
						>";
					break;
				case 5: case 'textarea':
					$hsl = "<textarea $param id='$name' name='$name' >".$val."</textarea>";
					break;
				default:
					//$hsl='';
					$hsl = "<input type='text' id='$name' name='$name' value='$val' $param>" ;
					break;
			}
			return $hsl;
	}
}
?>
