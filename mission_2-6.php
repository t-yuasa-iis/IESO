<title>湯浅さんのアイスホッケーブログ</title>
<h1>湯浅さんのアイスホッケーブログ</h1>

<?php 
$filename = 'post.txt';

//削除対象された部分を削除
if(!empty($_POST['delete'])){
	$posts = file($filename);
	$fp = fopen($filename, 'w');
	foreach($posts as $post){
		$contents = explode('<>', $post);
		if($_POST['delete'] == $contents[0]){
			if(empty($_POST['deletePass']) || $_POST['deletePass'] != trim($contents[4])){ //パスワードが合ってない場合はスキップしない
				echo 'パスワードが不正です';
				fwrite($fp,$post);
			}
		}else{
			fwrite($fp,$post);
		}
	}
	fclose($fp);
}

//編集対象が指定されている場合の処理
if(!empty($_POST['edit'])){
	$posts = file($filename);
	foreach($posts as $post){
		$contents = explode('<>', $post);
		if($_POST['edit'] == $contents[0]){
			if(empty($_POST['editPass']) || $_POST['editPass'] != trim($contents[4])){ //パスワードが合ってない場合は編集しない
					echo 'パスワードが不正です';
					$editErrFlg = 1;
			}else{
				$editFlg = 1;
				$editName = $contents[1];
				$editComment = $contents[2];
				$editPassword = $contents[4];
			}
		}
	}
	if(empty($editFlg) && empty($editErrFlg)){
		echo '指定された番号の投稿は存在しません';
	}
}

//投稿された内容をテキストファイルに保存
if(!empty($_POST['name']) && !empty($_POST['comment'])){
	$name = $_POST['name'];
	$comment = $_POST['comment'];
	$comment = str_replace(array("\r\n", "\r", "\n"),'',$comment);
	$pass = $_POST['password'];
	$now = date('Y/m/d H:i:s');
	//編集モードの際は該当部修正のみ
	if(!empty($_POST['editNum'])){
		$posts = file($filename);
		$fp = fopen($filename, 'w');
		foreach($posts as $post){
			$contents = explode('<>', $post);
			if($_POST['editNum'] == $contents[0]){
				fwrite($fp,$contents[0].'<>'.$name.'<>'.$comment.'<>'.$now.'<>'.$pass.PHP_EOL);
			}else{
				fwrite($fp, $post);
			}
		}
		fclose($fp);
	}else{
		$filesize = filesize($filename);
		if(empty($filesize)){
			$num = 1;
		}else{
			$posts = file($filename);
			$last = explode('<>', end($posts));
			$num = $last[0] + 1;
		}
		$fp = fopen($filename,'a');
		fwrite($fp,$num.'<>'.$name.'<>'.$comment.'<>'.$now.'<>'.$pass.PHP_EOL);
		fclose($fp);
	}
}
?>	

<!--投稿フォーム-->
<h2>新規投稿する</h2>
<form method='post'>
	<div>
      名前<br/>
	  <input type='text' name='name'
	  <?php if(!empty($editName)): ?>value=<?php echo $editName; endif;?> 
	  />
	</div>
	<div>
	  コメント<br/>
	  <textarea name='comment' rows='4' cols='40'><?php if(!empty($editComment))echo $editComment; ?></textarea>
    </div>
	<div>
	  パスワード<br/>
	  <input type='password' name='password' 
	  <?php if(!empty($editPassword)): ?>value=<?php echo $editPassword; endif;?> 
	  />
    </div>
	<?php if(!empty($editFlg)): ?>
	<input type='hidden' name='editNum' value=<?php echo $_POST['edit']; ?> />
	<?php endif; ?>
	<br />
	<input type='submit' value='投稿' />
</form>
<?php
if(!empty($_POST['name']) && empty($_POST['comment'])){
	echo 'コメントが未入力です';
}
if(empty($_POST['name']) && !empty($_POST['comment'])){
	echo '名前が未入力です';
}
if(!empty($_POST['name']) && !empty($_POST['comment']) && empty($_POST['password'])){
	echo 'パスワードが未入力です';
}
?>

<!--削除フォーム-->
<h2>投稿を削除する</h2>
<form method='post' onsubmit='return submitChk()'>
	<div>
		削除対象番号
		<input type='number' name='delete' />
	</div>
	<div>
		パスワード
		<input type='password' name='deletePass' />
	</div>
	<input type='submit' value='削除する' />
</form>

<!--編集フォーム-->
<h2>投稿を編集する</h2>
<form method='post'>
	<div>
		編集対象番号
		<input type='number' name='edit' />		
	</div>
	<div>
		パスワード
		<input type='password' name='editPass' />
	</div>
	<input type='submit' value='編集する' />
</form>	

<!--ここまでの投稿内容を表示-->
<h2>投稿一覧</h2>
<table>
	<tr>
		<th>投稿番号</th>
		<th>名前</th>
		<th>コメント</th>
		<th>投稿日時</th>
	</tr>
	<?php
	$posts = file($filename);
	foreach($posts as $post):
	?>
		<tr>
			<?php 
			$contents = explode('<>', $post);
			for($i=0;$i<4;$i++): 
			?>
				<td><?php echo $contents[$i]; ?></td>
			<?php endfor; ?>
		</tr>
	<?php endforeach; ?>
</table>

<script>
    /**
     * 確認ダイアログの返り値によりフォーム送信
    */
    function submitChk () {
        /* 確認ダイアログ表示 */
        var flag = confirm ( "本当に削除しますか？");
        return flag;
    }
</script>