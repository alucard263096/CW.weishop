<?php
	if (!empty($_FILES['imgFile']['name'])) {
		if ($_FILES['imgFile']['error'] != 0) {
			$result['message'] = '上传失败，请重试！';
			exit(json_encode($result));
		}
		$file = file_upload($_FILES['imgFile'], 'image');
		if (is_error($file)) {
			$result['message'] = $file['message'];
			exit(json_encode($result));
		}
		$result['url'] = $file['url'];
		$result['error'] = 0;
		$result['filename'] = $file['path'];
		$result['url'] = WEBSITE_ROOT.'attachment/'.$result['filename'];
		$filename=basename($result['url']);
		mysqld_insert('attachment', array(
			'uid' => $_CMS['account']['id'],
			'filename' => $filename,
			'attachment' => $result['filename'],
			'type' => 1,
			'createtime' => TIMESTAMP,
		));
		exit(json_encode($result));
	} else {
		$result['message'] = '请选择要上传的图片！';
		exit(json_encode($result));
	}
		