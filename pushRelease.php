<?php
// set_time_limit(0);
// max_execution_time(0);
function xSFTP( $source, $target,$connection ) {
    if ( is_dir( $source ) ) {
        executeShell($connection,"mkdir $target");
        $d = dir( $source );
        while ( FALSE !== ( $entry = $d->read() ) ) {
            if ( $entry == '.' || $entry == '..' ) {
                continue;
            }
            $Entry = $source . '/' . $entry;
            if ( is_dir( $Entry ) ) {
                xSFTP( $Entry, $target . '/' . $entry,$connection );
                continue;
            }
             ssh2_scp_send( $connection,$Entry, $target . '/' . $entry );
        }
        $d->close();
    }else {
        ssh2_scp_send( $connection,$source, $target );
    }
}

function executeShell($connection,$command){
  $stream = ssh2_exec($connection, $command);
  stream_set_blocking($stream, true);
  $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
  return stream_get_contents($stream_out);
}

  $server = "bonus-pulsa.com";
  $ftpUser = "root";
  $ftpPassword = "Hash2856";
  $ftpPort = "22";

  $connection = ssh2_connect($server, $ftpPort);
  if(ssh2_auth_password($connection, $ftpUser, $ftpPassword)){
    // $command = "
    // if [ -d '/var/www/html/atisiskada/base' ]; then
    //   echo 1
    // else
    //   echo 0
    // fi";
    // echo executeShell($connection,$command);
    xSFTP("/var/www/html/atisiskada/","/var/www/html/atisiskada/",$connection);
    echo "success";
    //  echo "login success";
      // ssh2_exec($connection,"echo hubla >> /var/www/html/atisiskada/test.txt");

      // ssh2_scp_send($connection, "/var/www/html/atisiskada/index.php", "/var/www/html/atisiskada/index.php");
  } else {
      echo "Login Failed";
  }

 ?>
