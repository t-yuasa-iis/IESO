<title>湯浅さんのアイスホッケーブログ</title>
<meta charset="UTF-8">

<?php 
//データベース接続設定
/*
アカウント情報を含むため省略
*/

//テーブル作成(初回のみ実行)
$sql = 'CREATE TABLE posts(id INT,name char(32),comment TEXT, modified DATETIME, password char(32));';
$statement = $pdo->query($sql);
?>


<h1>湯浅さんのアイスホッケーブログ</h1>

<?php

//削除対象された部分を削除
if(!empty($_POST['delete'])){
	$sql = "SELECT * FROM posts WHERE id=".$_POST['delete'];
	$deletePost = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
	if(empty($deletePost)){
		echo "該当する投稿は存在しません";
	}else if(empty($_POST['deletePass']) || $_POST['deletePass'] != $deletePost['password']){
		echo "パスワードが不正です。";
	}else{
		$sql = "DELETE FROM posts WHERE id=".$deletePost['id'];
		$result = $pdo->query($sql);
		if(!empty($result)){
			echo "正常に削除されました";
		}else{
			echo "削除に失敗しました";
		}
	}
}

//編集対象が指定されている場合の処理
if(!empty($_POST['edit'])){
	$postEdit = $pdo->query("SELECT * FROM posts WHERE id=".$_POST['edit'])->fetch(PDO::FETCH_ASSOC);
	if(empty($postEdit)){
		echo "該当する投稿は存在しません。";
	}else if(empty($_POST['editPass']) || $_POST['editPass'] != $postEdit['password']){
		echo "パスワードが不正です。";
	}else{
		echo "編集モード";
		$editFlg = 1;
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
		$sql = $pdo->prepare("UPDATE posts SET name=:name, comment=:comment, password=:pass, modified=:now WHERE id=:id");
		$sql->bindParam(':id',$_POST['editNum'],PDO::PARAM_INT);
		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
		$sql->bindParam(':now',$now,PDO::PARAM_STR);
		$sql->execute();
	}else{
		$results = $pdo->query('SELECT * FROM posts');
		$num = 0;
		foreach($results as $row){
			if($row['id'] > $num){
				$num = $row['id'];
			}
		}
		if(empty($num)){
			$num = 1;
		}else{
			$num = $num + 1;
		}
		$sql = $pdo->prepare('INSERT INTO posts(id,name,comment,modified,password) VALUE(:id,:name,:comment,:now,:pass)');
		$sql->bindParam(':id',$num,PDO::PARAM_INT);
		$sql->bindParam(':name',$name,PDO::PARAM_STR);
		$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
		$sql->bindParam(':now',$now,PDO::PARAM_STR);
		$sql->bindParam(':pass',$pass,PDO::PARAM_STR);
		$sql->execute();
	}
}

//投稿内容を取得
$sql = "SELECT * FROM posts ORDER BY id ASC";
$posts = $pdo->query($sql);
?>	

<!--投稿フォーム-->
<h2>新規投稿する</h2>
<form method='post'>
	<div>
      名前<br/>
	  <input type='text' name='name'
	  <?php if(!empty($editFlg)): ?>value=<?php echo $postEdit['name']; endif;?> 
	  />
	</div>
	<div>
	  コメント<br/>
	  <textarea name='comment' rows='4' cols='40'><?php if(!empty($editFlg))echo $postEdit['comment']; ?></textarea>
    </div>
	<div>
	  パスワード<br/>
	  <input type='password' name='password' 
	  <?php if(!empty($editFlg)): ?>value=<?php echo $postEdit['password']; endif;?> 
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
	<?php foreach($posts as $post): ?>
		<tr>
			<td><?php echo $post['id']; ?></td>
			<td><?php echo $post['name']; ?></td>
			<td><?php echo $post['comment']; ?></td>
			<td><?php echo $post['modified']; ?></td>
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