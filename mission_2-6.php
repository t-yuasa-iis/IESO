<title>���󂳂�̃A�C�X�z�b�P�[�u���O</title>
<h1>���󂳂�̃A�C�X�z�b�P�[�u���O</h1>

<?php 
$filename = 'post.txt';

//�폜�Ώۂ��ꂽ�������폜
if(!empty($_POST['delete'])){
	$posts = file($filename);
	$fp = fopen($filename, 'w');
	foreach($posts as $post){
		$contents = explode('<>', $post);
		if($_POST['delete'] == $contents[0]){
			if(empty($_POST['deletePass']) || $_POST['deletePass'] != trim($contents[4])){ //�p�X���[�h�������ĂȂ��ꍇ�̓X�L�b�v���Ȃ�
				echo '�p�X���[�h���s���ł�';
				fwrite($fp,$post);
			}
		}else{
			fwrite($fp,$post);
		}
	}
	fclose($fp);
}

//�ҏW�Ώۂ��w�肳��Ă���ꍇ�̏���
if(!empty($_POST['edit'])){
	$posts = file($filename);
	foreach($posts as $post){
		$contents = explode('<>', $post);
		if($_POST['edit'] == $contents[0]){
			if(empty($_POST['editPass']) || $_POST['editPass'] != trim($contents[4])){ //�p�X���[�h�������ĂȂ��ꍇ�͕ҏW���Ȃ�
					echo '�p�X���[�h���s���ł�';
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
		echo '�w�肳�ꂽ�ԍ��̓��e�͑��݂��܂���';
	}
}

//���e���ꂽ���e���e�L�X�g�t�@�C���ɕۑ�
if(!empty($_POST['name']) && !empty($_POST['comment'])){
	$name = $_POST['name'];
	$comment = $_POST['comment'];
	$comment = str_replace(array("\r\n", "\r", "\n"),'',$comment);
	$pass = $_POST['password'];
	$now = date('Y/m/d H:i:s');
	//�ҏW���[�h�̍ۂ͊Y�����C���̂�
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

<!--���e�t�H�[��-->
<h2>�V�K���e����</h2>
<form method='post'>
	<div>
      ���O<br/>
	  <input type='text' name='name'
	  <?php if(!empty($editName)): ?>value=<?php echo $editName; endif;?> 
	  />
	</div>
	<div>
	  �R�����g<br/>
	  <textarea name='comment' rows='4' cols='40'><?php if(!empty($editComment))echo $editComment; ?></textarea>
    </div>
	<div>
	  �p�X���[�h<br/>
	  <input type='password' name='password' 
	  <?php if(!empty($editPassword)): ?>value=<?php echo $editPassword; endif;?> 
	  />
    </div>
	<?php if(!empty($editFlg)): ?>
	<input type='hidden' name='editNum' value=<?php echo $_POST['edit']; ?> />
	<?php endif; ?>
	<br />
	<input type='submit' value='���e' />
</form>
<?php
if(!empty($_POST['name']) && empty($_POST['comment'])){
	echo '�R�����g�������͂ł�';
}
if(empty($_POST['name']) && !empty($_POST['comment'])){
	echo '���O�������͂ł�';
}
if(!empty($_POST['name']) && !empty($_POST['comment']) && empty($_POST['password'])){
	echo '�p�X���[�h�������͂ł�';
}
?>

<!--�폜�t�H�[��-->
<h2>���e���폜����</h2>
<form method='post' onsubmit='return submitChk()'>
	<div>
		�폜�Ώ۔ԍ�
		<input type='number' name='delete' />
	</div>
	<div>
		�p�X���[�h
		<input type='password' name='deletePass' />
	</div>
	<input type='submit' value='�폜����' />
</form>

<!--�ҏW�t�H�[��-->
<h2>���e��ҏW����</h2>
<form method='post'>
	<div>
		�ҏW�Ώ۔ԍ�
		<input type='number' name='edit' />		
	</div>
	<div>
		�p�X���[�h
		<input type='password' name='editPass' />
	</div>
	<input type='submit' value='�ҏW����' />
</form>	

<!--�����܂ł̓��e���e��\��-->
<h2>���e�ꗗ</h2>
<table>
	<tr>
		<th>���e�ԍ�</th>
		<th>���O</th>
		<th>�R�����g</th>
		<th>���e����</th>
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
     * �m�F�_�C�A���O�̕Ԃ�l�ɂ��t�H�[�����M
    */
    function submitChk () {
        /* �m�F�_�C�A���O�\�� */
        var flag = confirm ( "�{���ɍ폜���܂����H");
        return flag;
    }
</script>