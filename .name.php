<?php
include '/home/bitrix/ServerIP.php';
echo '<h1>Server: '.$ipi.'</h1> <br>';
echo '<b>Host: '.gethostname().'</b><br>'; 
echo 'Current file location: '. __FILE__.'<br>' ;

$v = disk_free_space(".");
echo 'Free disk space (#df) = '.$v.' bytes ('.round($v/1024/1024).' Mb)<br>';

$load = sys_getloadavg();
echo 'System load average (#top -b -n 1) = '.$load[0].'  '.$load[1].'  '.$load[2].'<br>' ;

echo 'Current server time: '.date('d.m.Y H:i:s',$_SERVER['REQUEST_TIME']).' <br>' ;

?>