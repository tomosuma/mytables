<?php
// スキーマダンプ読み込み
$schema = file('./n.s');

// 内容解析
$schemaA = array();
$lineA = array();
$act = 0;
$tablename = '';
foreach ($schema as $key => $line) {
  // テーブル名、ここから
  if(preg_match("/CREATE TABLE/", $line)){
    $line = preg_replace('/\n|\r|\r\n/', '', $line);
    $tablename = preg_replace('/CREATE TABLE `/', '', $line);
    $tablename = preg_replace('/`.*/', '', $tablename);
    $act = 1;
    $lineA = ['tablename' => $tablename];
    continue;
  }
  // 不要なものはスルー
  if(preg_match("/PRIMARY KEY|UNIQUE KEY/", $line)){
    continue;
  }
  // ここまで
  if(preg_match("/\) ENGINE/", $line)){
    array_push($schemaA, $lineA);
    $act = 0;
  }
  // 解析
  if($act){
    $line = preg_replace('/\n|\r|\r\n/', '', $line);
    $Field = preg_replace('/.* `/', '', $line);
    $Field = preg_replace('/` .*/', '', $Field);
    $lineA[$Field] = array('Field' => $Field);
    $Type = preg_replace('/.*` /', '', $line);
    $Type = preg_replace('/ .*/', '', $Type);
    $lineA[$Field]['Type'] = $Type;
    if(preg_match("/NOT NULL/", $line)){
      $lineA[$Field]['Null'] = 'NOT NULL';
    }else{
      $lineA[$Field]['Null'] = '';
    }
    if(preg_match("/DEFAULT/", $line)){
      if(preg_match("/DEFAULT NULL/", $line)){
        $lineA[$Field]['Default'] = 'NULL';
      }else{
        $Default = preg_replace('/.*DEFAULT \'/', '', $line);
        $Default = preg_replace('/\'.*/', '', $Default);
        $lineA[$Field]['Default'] = $Default;
      }
    }else{
      $lineA[$Field]['Default'] = '';
    }
  }
}
// JSON化
$schemaJ = json_encode($schemaA);
echo $schemaJ;
