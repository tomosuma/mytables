<?php
// ファイル保存
$filename = './save_data.dat';
$data = $_POST['data'];
$fp = fopen($filename, "w");
fwrite($fp, $data);
fclose($fp);
