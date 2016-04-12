<?php
      $delurl = $_GP['pic'];
        
        if (file_delete($delurl)) {
        
         $filename=basename(SYSTEM_WEBROOT . '/attachment/' . $delurl);
        		mysqld_delete('attachment', array(
			'uid' => $_CMS['account']['id'],
			'filename' => $filename
		));
            echo 1;
        } else {
            echo 0;
        }