<?php

 $operation = !empty($_GP['op']) ? $_GP['op'] : 'display';
		 if ($operation == 'display') {
            $pindex = max(1, intval($_GP['page']));
            $psize = 20;
            $status = !isset($_GP['status']) ? 1 : $_GP['status'];
            $sendtype = !isset($_GP['sendtype']) ? 0 : $_GP['sendtype'];
            $condition = '';
            $param_ordersn=$_GP['ordersn'];
 						if (!empty($_GP['ordersn'])) {
                $condition .= " AND ordersn LIKE '%{$_GP['ordersn']}%'";
            }

            	if (!empty($_GP['paytype'])) {
                $condition .= " AND paytypecode ='".$_GP['paytype']."'";
            }
            	if (!empty($_GP['dispatch'])) {
                $condition .= " AND dispatch =".intval($_GP['dispatch']);
            }
              if (!empty($_GP['endtime'])) {
                $condition .= " AND createtime  <= ". strtotime($_GP['endtime']);
            }
               if (!empty($_GP['begintime'])) {
                $condition .= " AND createtime  >= ". strtotime($_GP['begintime']);
            }
           
            if (!empty($_GP['address_realname'])) {
                $condition .= " AND address_realname  LIKE '%{$_GP['address_realname']}%'";
            }
                if (!empty($_GP['address_mobile'])) {
                $condition .= " AND address_mobile  LIKE '%{$_GP['address_mobile']}%'";
            }
           
            if ($status != '-99') {
		                $condition .= " AND status = '" . intval($status) . "'";
            }
            if ($status == '3') {
		                $condition .= " and ( status = 3 or status = -5 or status = -6)";
            }
						$dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
						$dispatchdata=array();
					  if(is_array($dispatchs)) { foreach($dispatchs as $disitem) { 
					  	$dispatchdata[$disitem['id']]=$disitem;
							 } }
							 $selectCondition="LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
						  if (!empty($_GP['report'])) {
						  	$selectCondition="";
						  }
					
            $list = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE 1=1 $condition ORDER BY  createtime DESC ".$selectCondition);
            $total = mysqld_selectcolumn('SELECT COUNT(*) FROM ' . table('shop_order') . " WHERE 1=1  $condition");
            $pager = pagination($total, $pindex, $psize);
				 if (!empty($_GP['report'])) {
				
				 	 foreach ( $list as $id => $item) {
			             	 	$list[$id]['ordergoods']=mysqld_selectall("SELECT (select category.name	from" . table('shop_category') . " category where (0=goods.ccate and category.id=goods.pcate) or (0!=goods.ccate and category.id=goods.ccate) ) as categoryname,goods.thumb,ordersgoods.price,ordersgoods.total,goods.title,ordersgoods.optionname from " . table('shop_order_goods') . " ordersgoods left join " . table('shop_goods') . " goods on goods.id=ordersgoods.goodsid  where  ordersgoods.orderid=:oid order by ordersgoods.createtime  desc ",array(':oid' => $item['id']));;
                }
                	 $report='orderreport';
                	require_once 'report.php';
             					exit;
					}
                  	$payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
             include page('order_list');
        }
        if ($operation == 'detail') {
        	$orderid=intval($_GP['id']);
        	   $order = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id=:id",array(":id"=>$orderid));
         	$payments = mysqld_selectall("SELECT * FROM " . table('payment') . " WHERE enabled = 1");
							$dispatchs = mysqld_selectall("SELECT * FROM " . table('shop_dispatch') );
						$dispatchdata=array();
					  if(is_array($dispatchs)) { foreach($dispatchs as $disitem) { 
					  	$dispatchdata[$disitem['id']]=$disitem;
							 } }
			 $goods = mysqld_selectall("SELECT g.id,o.total, g.title, g.status,g.thumb, g.goodssn,g.productsn,g.marketprice,o.total,g.type,o.optionname,o.optionid,o.price as orderprice FROM " . table('shop_order_goods') . " o left join " . table('shop_goods') . " g on o.goodsid=g.id "
                    . " WHERE o.orderid='{$orderid}'");
            $order['goods'] = $goods;
            
            
              if (checksubmit('confrimpay')) {
                mysqld_update('shop_order', array('status' => 1,'remark'=>$_GP['remark']), array('id' => $orderid));
                message('确认订单付款操作成功！', refresh(), 'success');
            }
            if (checksubmit('confirmsend')) {
                if ($_GP['express']!="-1" && empty($_GP['expresssn'])) {
                    message('请输入快递单号！');
                }        
                $express=$_GP['express'];
                 if ($express=="-1")
                 {
                 	$express="";
                 	}
                mysqld_update('shop_order', array(
                    'status' => 2,
                    'express' => $express,
                    'expresscom' => $_GP['expresscom'],
                    'expresssn' => $_GP['expresssn'],'remark'=>$_GP['remark']), array('id' => $orderid));
                message('发货操作成功！', refresh(), 'success');
            }
            if (checksubmit('cancelsend')) {
                mysqld_update('shop_order', array(
                    'status' => 1,'remark'=>$_GP['remark']
                        ), array('id' => $orderid));
                message('取消发货操作成功！', refresh(), 'success');
            }       
             if (checksubmit('cancelreturn')) {
                $item = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE id = :id", array(':id' => $orderid));
               	$ostatus=3;
               	if($item['status']==-2)
               	{
               		$ostatus=1;
               	}
               		if($item['status']==-3)
               	{
               		$ostatus=3;
               	}
               		if($item['status']==-4)
               	{
               		$ostatus=3;
               	}
                mysqld_update('shop_order', array(
                    'status' => $ostatus,
                        ), array('id' => $orderid));
                message('退回操作成功！', refresh(), 'success');
            }
              if (checksubmit('open')) {
                mysqld_update('shop_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $orderid));
                message('开启订单操作成功！', referer(), 'success');
            }
              
               if (checksubmit('close')) {
                mysqld_update('shop_order', array('status' => -1,'remark'=>$_GP['remark']), array('id' => $orderid));
                message('订单关闭操作成功！', refresh(), 'success');
            }
        
            if (checksubmit('finish')) {
            	        if (empty($order['isrest'])) {
           		 $this->setOrderCredit($order['openid'],$orderid,true,'订单:'.$order['ordersn'].'完成新增积分');
           		}
                mysqld_update('shop_order', array('status' => 3, 'remark' => $_GP['remark']), array('id' => $orderid));
                message('订单操作成功！', refresh(), 'success');
            }
        
          
           if (checksubmit('returnpay')) {
           	
           	if($order['paytype']==3)
           	{
           		 message('货到付款订单不能进行退款操作!', refresh(), 'error');
           	}
                mysqld_update('shop_order', array('status' => -6,'remark'=>$_GP['remark']), array('id' => $orderid));
            
                $this->setOrderStock($orderid, false);
							
								member_gold($order['openid'],$order['price'],'addgold','订单:'.$order['ordersn'].'退款返还余额');
                message('退款操作成功！', refresh(), 'success');
            }
            
               if (checksubmit('returngood')) {
                mysqld_update('shop_order', array('status' => -5,'remark'=>$_GP['remark']), array('id' => $orderid));
                $this->setOrderStock($orderid, false);
								$this->setOrderCredit($order['openid'],$orderid,false,'订单:'.$order['ordersn'].'退货扣除积分');
								member_gold($order['openid'],$order['price'],'addgold','订单:'.$order['ordersn'].'退货返还余额');
                message('退货操作成功！', refresh(), 'success');
            }
           
        	  include page('order');
        }