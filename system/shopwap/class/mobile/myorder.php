<?php
				$member=get_member_account();
					$openid =$member['openid'] ;
				$id = $profile['id'];
        $op = $_GP['op'];
         if ($op == 'cancelsend') {
       	   $orderid = intval($_GP['orderid']);
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid )); 	
	         	if (empty($item)) {
                message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
            }
	         	if(($item['paytype']==3&&$item['status']==1)||$item['status']==0)
	         	{
         		mysqld_update('shop_order', array('status' => -1,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
 						message('订单已关闭！', mobile_url('myorder'), 'success');
           
         		}
         			if($item['status']==2)
	         	{
	         			message('商家已发货无法修改订单');
          
	         	}
	         		message('该订单不可取消');
          
       	} 
         if ($op == 'returngood') {
            $orderid = intval($_GP['orderid']);
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
             $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
       
            if (empty($item)) {
                message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
            }
                $opname="退货";
              if (checksubmit("submit")) {
            mysqld_update('shop_order', array('status' => -4,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));
						
            message('申请退货成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
                  }
          include themePage('order_detail_return');
           exit;
        } if ($op == 'resendgood') {
            $orderid = intval($_GP['orderid']);
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
            $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
           
            if (empty($item)) {
                message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
            }
            $opname="换货";
        if (checksubmit("submit")) {
            mysqld_update('shop_order', array('status' =>  -3,'isrest'=>1,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));
						
            message('申请换货成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
             }
          include themePage('order_detail_return');
           exit;
        } if ($op == 'returnpay') {
            $orderid = intval($_GP['orderid']);
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
            $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
           
            if (empty($item)) {
                message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
            }
             $opname="退款";
        if (checksubmit("submit")) {
        	    	if($order['paytype']==3)
           	{
           		 message('货到付款订单不能进行退款操作!', refresh(), 'error');
           	}
            mysqld_update('shop_order', array('status' => -2,'rsreson' => $_GP['rsreson']), array('id' => $orderid, 'openid' => $openid ));
						
            message('申请退款成功，请等待审核！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
          }
             include themePage('order_detail_return');
              exit;
        } elseif ($op == 'confirm') {
            $orderid = intval($_GP['orderid']);
            $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id AND openid = :openid", array(':id' => $orderid, ':openid' => $openid ));
            if (empty($order)) {
                message('抱歉，您的订单不存在或是已经被取消！', mobile_url('myorder'), 'error');
            }
            
             if (empty($order['isrest'])) {//不是换货
            	$this->setOrderCredit($openid,$order['id'],true,'订单:'.$order['ordersn'].'收货新增积分');
            }
            mysqld_update('shop_order', array('status' => 3,'updatetime'=>time()), array('id' => $orderid, 'openid' => $openid ));
						
            message('确认收货完成！', mobile_url('myorder',array('status' => intval($_GP['fromstatus']))), 'success');
        } else if ($op == 'detail') {

            $orderid = intval($_GP['orderid']);
            $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
            if (empty($item)) {
                message('抱歉，您的订单不存或是已经被取消！', mobile_url('myorder'), 'error');
            }
            		if($item['paytype']!=$this->getPaytypebycode($item['paytypecode']))
  					{
  						 mysqld_update('shop_order', array('paytype' => $this->getPaytypebycode($item['paytypecode'])), array('id' => $orderid, 'openid' => $openid ));
						 $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE openid = '".$openid."' and id='{$orderid}' limit 1");
           
  					}
  
            $goodsid = mysqld_selectall("SELECT goodsid,total FROM " . table('shop_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');

            $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice,o.total,o.optionid FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                    . " WHERE o.orderid='{$orderid}'");
            foreach ($goods as &$g) {
                //属性
                $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
                if ($option) {
                    $g['title'] = "[" . $option['title'] . "]" . $g['title'];
                    $g['marketprice'] = $option['marketprice'];
                }
            }
            unset($g);

            $dispatch = mysqld_select("select id,dispatchname,sendtype from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
            $payments = mysqld_selectall("select * from " . table("payment")." where enabled=1 order by `order` desc");
  
  			
  
            include themePage('order_detail');
            exit;
        } else {
            $pindex = max(1, intval($_GP['page']));
            $psize = 20;

            $status = intval($_GP['status']);
            $where = "openid = '".$openid."'";
            ;
            if ($status == -5) {
                $where.=" and ( status=-2 or status=-3 or status=-4 )";
            }if ($status == 99) {
               // $where.=" and ( status=-5 or status=-6 or status=3 )";
            }  else {
                $where.=" and status=$status";
            }
            $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE $where ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, array(), 'id');
            $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE  openid = '".$openid."'");
            $pager = pagination($total, $pindex, $psize);

            if (!empty($list)) {
                foreach ($list as &$row) {
                    $goods = mysqld_selectall("SELECT g.id, g.title, g.thumb, g.marketprice,o.total,o.optionid FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                            . " WHERE o.orderid='{$row['id']}'");
                    foreach ($goods as &$item) {
                        //属性
                        $option = mysqld_select("select title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id limit 1", array(":id" => $item['optionid']));
                        if ($option) {
                            $item['title'] = "[" . $option['title'] . "]" . $item['title'];
                            $item['marketprice'] = $option['marketprice'];
                        }
                    }
                    unset($item);
                    $row['goods'] = $goods;
                    $row['total'] = $goodsid;
                    $row['dispatch'] = mysqld_select("select id,dispatchname from " . table('shop_dispatch') . " where id=:id limit 1", array(":id" => $row['dispatch']));
                }
            }
		        include themePage('order');
		         exit;
        }