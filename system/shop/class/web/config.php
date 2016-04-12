<?php
		
			$settings=globaSetting();
			if (checksubmit("submit")) {
            $cfg = array(
                'shop_openreg' => intval($_GP['shop_openreg']),
                 'shop_regcredit' => intval($_GP['shop_regcredit']),
						  //	'shop_keyword' => $_GP['shop_keyword'],
				   		//  'shop_description' => $_GP['shop_description'],
				   		  'shop_title' => $_GP['shop_title'],
				   		    'shop_icp' => $_GP['shop_icp'],
				   		  'help' =>   htmlspecialchars_decode($_GP['help'])
            );
      
          	if (!empty($_FILES['shop_logo']['tmp_name'])) {
                    $upload = file_upload($_FILES['shop_logo']);
                    if (is_error($upload)) {
                        message($upload['message'], '', 'error');
                    }
                    $shoplogo = $upload['path'];
                }
                if(!empty($shoplogo))
                {
                	$cfg['shop_logo']=$shoplogo;
                }
                
          	refreshSetting($cfg);
            message('保存成功', 'refresh', 'success');
        }
		include page('setting');