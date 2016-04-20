<?php

 			$cfg = globaSetting();
			$district = mysqld_selectall("SELECT * FROM " . table('district'));
			
        $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
        
        if ($operation == 'post') {
            $id = intval($_GP['id']);
            if (!empty($id)) {
                $item = mysqld_select("SELECT * FROM " . table('merchant') . " WHERE id = :id", array(':id' => $id));
                if (empty($item)) {
                    message('抱歉，商家不存在或是已经删除！', '', 'error');
                }
                
			}
				if (checksubmit('submit')) {
					if (empty($_GP['merchantname'])) {
						message('请输入商家名称！');
					}
					if (empty($_GP['district'])) {
						message('请选择商家所在区！');
					}
					if (empty($_GP['address'])) {
						message('请输入地址！');
					}
					if (empty($_GP['contact'])) {
						message('请输入联系人！');
					}
					if (empty($_GP['mobile'])) {
						message('请输入联系电话！');
					}
					$data = array(
						'no' => $_GP['merchantno'],
						'name' => $_GP['merchantname'],
						'contact' => $_GP['contact'],
						'mobile' => $_GP['mobile'],
						'address' => $_GP['address'],
						'displayorder' => intval($_GP['displayorder']),
						'district' => intval($_GP['district']),
						'status' => intval($_GP['status']),
						'displayorder' => intval($_GP['displayorder']),
						'description' => $_GP['description'],
						'content' => htmlspecialchars_decode($_GP['content']),
						'createtime' => TIMESTAMP,
						'ishot' => intval($_GP['ishot']),
						'isdiscount' => intval($_GP['isdiscount']),
						'isrecommand' => intval($_GP['isrecommand'])
						);
					if (!empty($_FILES['thumb']['tmp_name'])) {
						$upload = file_upload($_FILES['thumb']);
						if (is_error($upload)) {
							message($upload['message'], '', 'error');
						}
						$data['thumb'] = $upload['path'];
					}
					if (empty($id)) {
						mysqld_insert('merchant', $data);
						$id = mysqld_insertid();
					} else {
						unset($data['createtime']);
						mysqld_update('merchant', $data, array('id' => $id));
					}
                
                  
                    
						$hsdata=array();
					  if (!empty($_GP['attachment-new'])) {
						foreach ($_GP['attachment-new'] as $index => $row) {
							if (empty($row)) {
								continue;
							}
							$hsdata[$index] = array(
								'attachment' => $_GP['attachment-new'][$index],
							);
						}
						$cur_index = $index + 1;
					}
					if (!empty($_GP['attachment'])) {
						foreach ($_GP['attachment'] as $index => $row) {
							if (empty($row)) {
								continue;
							}
							$hsdata[$cur_index + $index] = array(
								'attachment' => $_GP['attachment'][$index]
							);
						}
					}
					 mysqld_delete('merchant_piclist', array('merchantid' => $id));
					 foreach ($hsdata as $row) {
					$data = array(
							 'merchantid' => $id,
							 'picurl' =>$row['attachment']
		           				 );
						mysqld_insert('merchant_piclist', $data);
					}
                
					message('商品操作成功！', web_url('merchant', array('op' => 'post', 'id' => $id)), 'success');
				}
            
        		include page('merchant');
            
        } elseif ($operation == 'display') {

			$pindex = max(1, intval($_GP['page']));
            $psize = 10;
            $condition = '';
            if (!empty($_GP['keyword'])) {
                $condition .= " AND (main.no LIKE '%{$_GP['keyword']}%' 
				or main.name LIKE '%{$_GP['keyword']}%' 
				or main.contact LIKE '%{$_GP['keyword']}%'
				or main.mobile LIKE '%{$_GP['keyword']}%')";
            }

            if (!empty($_GP['area'])) {
                $did = intval($_GP['district']);
                $condition .= " AND main.district = '{$did}'";
            } 

            if (isset($_GP['status'])) {
                $condition .= " AND main.status = '" . intval($_GP['status']) . "'";
            }
			$sql="SELECT main.id, main.no,main.name,d.name district,main.contact,main.mobile,main.status FROM " . table('merchant') . " main
			inner join ".table('district')." d on main.district=d.id
			WHERE  main.deleted=0 $condition 
			ORDER BY main.status DESC, main.displayorder DESC, id DESC 
			LIMIT " . ($pindex - 1) * $psize . ',' . $psize;


            $list = mysqld_selectall($sql);
            $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('merchant') . " WHERE deleted=0 $condition");
            $pager = pagination($total, $pindex, $psize);


             include page('merchant_list');
        } elseif ($operation == 'delete') {

        }