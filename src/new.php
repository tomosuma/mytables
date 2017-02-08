<?php
// �X�L�[�}�_���v�ǂݍ���
$schema = file('./n.s');

// ���e���
$schemaA = array();
$lineA = array();
$act = 0;
$tablename = '';
foreach ($schema as $key => $line) {
  // �e�[�u�����A��������
  if(preg_match("/CREATE TABLE/", $line)){
    $line = preg_replace('/\n|\r|\r\n/', '', $line);
    $tablename = preg_replace('/CREATE TABLE `/', '', $line);
    $tablename = preg_replace('/`.*/', '', $tablename);
    $act = 1;
    $lineA = ['tablename' => $tablename];
    continue;
  }
  // �s�v�Ȃ��̂̓X���[
  if(preg_match("/PRIMARY KEY|UNIQUE KEY/", $line)){
    continue;
  }
  // �����܂�
  if(preg_match("/\) ENGINE/", $line)){
    array_push($schemaA, $lineA);
    $act = 0;
  }
  // ���
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
// JSON��
$schemaJ = json_encode($schemaA);
echo $schemaJ;
