<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>My Tables</title>
<meta name="keywords" content="">
<meta name="description" content="">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<style>
body{
  background: #f0f0f0;
  font-family: 'MS PGothic', arial, helvetica, sans-serif; sans-serif;
}
.mytable{
  border: solid 1px #95c6fc;
  border-radius: 5px;
  background: #ffffff;
  position: absolute;
}
.tablename{
  background: #dcdcff;
  border-radius: 5px;
  padding: 2px 5px 2px 5px;
  min-width: 230px;
}
.tablenametext{
  font-weight: bold;
}
.mytabledetail{
  border-collapse: collapse;
  border-spacing: 0;
  background: #ffffff;
  width: 100%;
}
.mytabledetail td, .mytabledetail th{
  padding: 1px 3px;
  font-size: 13px;
  border: solid 1px #acdccc;
}
.mytabledetail th{
  background: #f0f0ff;
  font-size: 11px;
  color: #a056f0;
  padding: 3px 3px 1px 3px;
}

.tablename{
  cursor: move;
}
.tgbtn{
  float: right;
  color: #ffffff;
  cursor: pointer;
}

#controller{
  padding: 2px 2px 10px 2px;
}
.ctrlbtn{
  border: solid 1px #95c6fc;
  border-radius: 5px;
  padding: 1px 10px 1px 10px;
  margin: 1px 2px;
  cursor: pointer;
  background: #ffffff;
}
.memo{
  width: 100%;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
  box-sizing: border-box;
  font-size: 13px;
  display: none;
}
.memot{
  padding: 4px 4px 0 4px;;
  font-size: 13px;
  min-height: 16px;
}
.schemaBox{
  padding: 0 0 3px 0;
}
#messageBox{
  display:none;
}
#message{
  color: #ff00fe;
  font-weight: bold;
}
.clickme{
  font-size: 10px;
  color: #ff8902;
  font-weight: bold;
  cursor: pointer;
}


</style>
<script>
$(document).ready(function() {
  // 準備
  var zValue = 1;

  // 読込（スキーマ）
  $(document).on('click', '#newfile', function(){
		$.ajax({
			type: "POST",
			url: "newfile.php",
			success: function(backdata, dataType){
/*
        var tablesA = JSON.parse(backdata);
        // HTML作成
        var box = '';
        var myX = 0;
        var myY = 0;
        // テーブルのループ
        for(var i in tablesA){
          box = _makeHtml(tablesA[i], box, myX, myY, i, null);
          myX = myX + 0;
          myY = myY + 23;
        }
        $('#tables').html(box);
        _draggable(); // ドラッグ関数セット
*/

console.log(backdata);

			}
		});
  });

  // 詳細表示
  $(document).on('click', '#show', function(){
    $('.mytabledetail').show('slow');
    $('.tgbtn').show();
  });

  // 詳細非表示
  $(document).on('click', '#hide', function(){
    $('.mytabledetail').hide('slow');
    $('.tgbtn').hide();
  });

  // 保存
  $(document).on('click', '#save', function(){
		var html_text = $('#tables').html();
		var data = {
			"data" : html_text
		};
		$.ajax({
			type: "POST",
			url: "save.php",
			data: data,
			success: function(msg){
        $('#message').text('save is success.');
        $('#messageBox').show();
			}
		});
  });

  // 復帰
  $(document).on('click', '#load', function(){
		$.ajax({
			type: "POST",
			url: "load.php",
			success: function(backdata, dataType){
        $('#tables').html(backdata);
        // メモ復帰
        $('.memoh').each(function(){
          var myVal = $(this).val();
          if(myVal){
            var myId = $(this).attr('id');
            var myBrother = myId.replace(/h$/ , '') ;
            $('#' + myBrother).val(myVal);
          }
        });
        _draggable(); // ドラッグ関数セット
			}
		});
  });

  // 保存データーに最新スキーマを反映
  $(document).on('click', '#loadnew', function(){
		$.ajax({
			type: "POST",
			url: "new.php",
			success: function(backdata, dataType){
        var tablesA = JSON.parse(backdata);
        // HTML作成
        var box = '';
        var myX = 0;
        var myY = 0;
        // テーブルのループ
        var tablenamesA = [];
        for(var i in tablesA){
          tablenamesA.push(tablesA[i]['tablename']);
          if($('#' + tablesA[i]['tablename']).html()){
            // 有ればスキーマだけ書き換え
            var schemaBox = '';
            schemaBox = _makeHtml(tablesA[i], box, myX, myY, i, 'schemaOnly');
            $('#' + tablesA[i]['tablename'] + ' .schemaBox').html(schemaBox);
          }else{
            // 無ければ追加
            box = _makeHtml(tablesA[i], box, myX, myY, i, null);
            myX = myX + 0;
            myY = myY + 23;
          }
        }
        // スキーマに無いものは削除
        $('.mytable').each(function(){
          var myId = $(this).attr('id');
            if (tablenamesA.indexOf(myId) == -1){
              $('#' + myId).remove();
            }
        });
        // 追加分を書き込み
        if(box){$('#tables').append(box);}
        _draggable(); // ドラッグ関数セット
      }
    });
  });

  // ドラッグ関数セット
  function _draggable(){
    $(".mytable").draggable({
      start : function (event , ui){
        zValue ++;
        $(this).css('z-index', zValue);
        $('#message').text($(this).attr('id'));
        $('#messageBox').show();
      } ,
      stop : function (event , ui){
        var grid = 20;
        var myX = (Math.round(ui.position.left / grid)) * grid + 'px';
        var myY = (Math.round(ui.position.top / grid)) * grid + 'px';
        $(this).css('left', myX);
        $(this).css('top', myY);
      }
    });
  }

  // html 作成
  function _makeHtml(tables, box, myX, myY, i, schemaOnly){
    var columnsA = tables;
    // 上書きならスキーマだけ
    if(!schemaOnly){
      box += '<table id="' + columnsA['tablename'] + '" class="mytable" style="z-index: 1; left: ' + myX + 'px; top: ' + myY + 'px;">';
      box += '<tr><td>';
      box += '<div class="tablename"><span class="tablenametext">' + columnsA['tablename'] + '</span><span class="tgbtn">◆</span>';
      box += '<div class="schemaBox">';
    }
    box += '<table class="mytabledetail"><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>';
    // カラムのループ
    for(var j in columnsA){
      if(typeof columnsA[j] == 'string'){continue;}
      box += '<tr><td>' + columnsA[j]['Field'] + '</td>';
      box += '<td>' + columnsA[j]['Type'] + '</td>';
      box += '<td>' + (columnsA[j]['Null'] ? 'o' : 'x') + '</td>';
      box += '<td>' + columnsA[j]['Default'] + '</td></tr>';
    }
    box += '</table>';
    // 上書きならスキーマだけ
    if(!schemaOnly){
      box += '</div></div><div class="memoBox">';
      box += '<textarea type="text" id="memo' + i + '" class="memo"></textarea>';
      box += '<div id="memo' + i + 't" class="memot"></div>';
      box += '<input type="hidden" id="memo' + i + 'h" class="memoh" />';
      box += '</div></td></tr></table>';
    }
    return box;
  }

  // テキストエリア → テキスト
  $(document).on('blur', '.memo', function(){
    self = $(this);
    var myId = self.attr('id');
    self.hide();
    var memo = self.val();
    $('#' + myId + 't').html(memo.replace(/[\n\r]/g, "<br />"));
    $('#' + myId + 't').show();
  });

  // テキスト → テキストエリア
  $(document).on('click', '.memot', function(){
    self = $(this);
    var myId = self.attr('id');
    var myBrother = myId.replace(/t$/ , '') ;
    self.hide();
    $('#' + myBrother).show();
    $('#' + myBrother).focus();
  });

  // 画面クリア
  $(document).on('click', '#clear', function(){
    $('#tables').html('');
  });

  // トグル
  $(document).on('click', '.tgbtn', function(){
    $(this).next().toggle('fold');
  });

  // メモ
  $(document).on('change', '.memo', function(){
    var myId = $(this).attr('id');
    $('#' + myId + 'h').val($(this).val());
  });

  // メッセージクリア
  $(document).on('click', '.messageclear', function(){
    $('#message').text('');
    $('#messageBox').hide();
  });


});
</script>
</head>
<body>
<div id="controller">
  <span class="messageclear">

<input type="file" name="newfile"></input> 

  <span id="newfile" class="ctrlbtn">send new file</span>
  <span id="show" class="ctrlbtn">show</span>
  <span id="hide" class="ctrlbtn">hide</span>
  <span id="save" class="ctrlbtn">save</span>
  <span id="load" class="ctrlbtn">load</span>
  <span id="loadnew" class="ctrlbtn">load + new</span>
  <span id="clear" class="ctrlbtn">clear</span>
  </span>
  <span id="messageBox">&nbsp;<span id="message"></span>&nbsp;<span class="clickme messageclear">&nbsp;&nbsp;[x]</span></span>
</div>
<div id="tables" style="position: relative;">
</div>
</body>
</html>
