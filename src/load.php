<?php
// 保存データ読み込み
$data = file('./save_data.dat');
$fataS = implode("", $data);
echo $fataS;
