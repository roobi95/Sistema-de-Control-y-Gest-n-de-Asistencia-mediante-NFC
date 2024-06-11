<?php
$command = 'ssh -i /home/servidorweb/.ssh/id_rsa rcasir@192.168.1.155 ls -la 2>&1';
$output = shell_exec($command);
echo "<pre>$output</pre>";
?>
