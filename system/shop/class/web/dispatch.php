<?php
		
		      $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
		 if ($operation == 'display') {
		 	  $list = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') . " where deleted=0 ORDER BY displayorder desc");
       	     include page('dispatch_list');
			}
			if ($operation == 'post') {
				    $id = intval($_GP['id']);
            if (!empty($id)) {
                $item = mysqld_select("SELECT * FROM " . table('shop_dispatch') . " WHERE id = :id", array(':id' => $id));
            }
            if (checksubmit('submit')) {
            	 $data = array(
                    'displayorder' => intval($_GP['displayorder']),
                    'dispatchname' => $_GP['dispatchname'],
                    'firstprice' => $_GP['firstprice'],
                    'secondprice' => $_GP['secondprice'],
                     'provance' => $_GP['sel-provance'],
                      'city' => $_GP['sel-city'],
                       'area' => $_GP['sel-area'],
                     'firstweight' => intval($_GP['firstweight']),
              			  'secondweight' => intval($_GP['secondweight']),
                         'express' => $_GP['express'],
                          'deleted' => 0,
                    'sendtype' => intval($_GP['sendtype'])
                );
                if (empty($id)) {
                    mysqld_insert("shop_dispatch", $data);
                    $id = mysqld_insertid();
                } else {
                    mysqld_update("shop_dispatch", $data, array('id' => $id));
                }
                message('配送方式操作成功！', web_url('dispatch', array('op' => 'post', 'id' => $id)), 'success');
            }
					include page('dispatch');
				}elseif ($operation == 'delete') {
            $id = intval($_GP['id']);
            $row = mysqld_select("SELECT id FROM " . table('shop_dispatch') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，配送方式不存在或是已经被删除！');
            }
            //修改成不直接删除
            mysqld_update("shop_dispatch",array('deleted'=>1), array('id' => $id));
            message('删除成功！', 'refresh', 'success');
        }