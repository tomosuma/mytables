<?php
// ƒtƒ@ƒCƒ‹•Û‘¶
$filename = './save_data.dat';
$data = $_POST['data'];
$fp = fopen($filename, "w");
fwrite($fp, $data);
fclose($fp);
