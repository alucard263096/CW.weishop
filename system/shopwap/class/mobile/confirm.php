<?php

		if($_GP["follower"]!="nologinby")
		{
				if(is_login_account()==false)
				{
					if(empty($_SESSION["noneedlogin"]))
					{
					tosaveloginfrom();
					header("location:".create_url('mobile',array('name' => 'shopwap','do' => 'login','from'=>'confirm')));	
					}
				}
		
		}else
		{
			 $_SESSION["noneedlogin"]=true;
				clearloginfrom();	
		}
	
			$member=get_member_account();
				$openid =$member['openid'] ;
		$op = $_GP['op']?$_GP['op']:'display';
        $totalprice = 0;
        $allgoods = array();
        $id = intval($_GP['id']);
        $optionid = intval($_GP['optionid']);
        $total = intval($_GP['total']);
        if (empty($total)) {
            $total = 1;
        }
        $direct = false; //是否是直接购买
        $returnurl = ""; //当前连接
	$issendfree=0;
	 $defaultAddress = mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE isdefault = 1 and openid = :openid limit 1", array(':openid' => $openid));
        

        if (!empty($id)) {
        
            $item = mysqld_select("select * from " . table("shop_goods") . " where id=:id", array(":id" => $id));
			
				
					if($item['issendfree']==1)
                    		{
                    			$issendfree=1;	
                    		}
       
            if ($item['istime'] == 1) {
                if (time() > $item['timeend']) {
                    message('抱歉，商品限购时间已到，无法购买了！', refresh(), "error");
                }
            }

            if (!empty($optionid)) {
                $option = mysqld_select("select title,marketprice,weight,stock from " . table("shop_goods_option") . " where id=:id", array(":id" => $optionid));
                if ($option) {
                    $item['optionid'] = $optionid;
                    $item['title'] = $item['title'];
                    $item['optionname'] = $option['title'];
                    $item['marketprice'] = $option['marketprice'];
                    $item['weight'] = $option['weight'];
                }
            }
            $item['stock'] = $item['total'];
            $item['total'] = $total;
            $item['totalprice'] = $total * $item['marketprice'];
            $allgoods[] = $item;
            $totalprice+= $item['totalprice'];
            
            $direct = true;
            $returnurl = mobile_url("confirm", array("id" => $id, "optionid" => $optionid, "total" => $total));
        }
        if (!$direct) {
        
            //如果不是直接购买（从购物车购买）
            $list = mysqld_selectall("SELECT * FROM " . table('shop_cart') . " WHERE  session_id = '".$openid."'");
            if (!empty($list)) {
                foreach ($list as &$g) {
                    $item = mysqld_select("select * from " . table("shop_goods") . " where id=:id", array(":id" => $g['goodsid']));
                    //属性
                    $option = mysqld_select("select * from " . table("shop_goods_option") . " where id=:id ", array(":id" => $g['optionid']));
                    if ($option) {
                    		if($item['issendfree']==1)
                    		{
                    			$issendfree=1;	
                    		}
                        $item['optionid'] = $g['optionid'];
                        $item['title'] = $item['title'];
                        $item['optionname'] = $option['title'];
                        $item['marketprice'] = $option['marketprice'];
                        $item['weight'] = $option['weight'];
                    }
                    $item['stock'] = $item['total'];
                    $item['total'] = $g['total'];
                    $item['totalprice'] = $g['total'] * $item['marketprice'];
                    $allgoods[] = $item;
                    $totalprice+= $item['totalprice'];
                   
                }
                unset($g);
            }
            $returnurl = mobile_url("confirm");
        }

        if (count($allgoods) <= 0) {
            header("location: " . mobile_url('myorder'));
            exit();
        }
        //配送方式
       
       $dispatch=array();
        $dispatchcode=array();
     $addressdispatch1 = mysqld_selectall("select * from " . table("shop_dispatch") . " WHERE deleted=0 and  (provance!='' and provance=:provance) and (city!='' and city=:city) and (area!='' and area=:area)   order by displayorder desc",array(":provance"=>$defaultAddress['provance'],":city"=>$defaultAddress['city'],":area"=>$defaultAddress['area']));
     $addressdispatch2 = mysqld_selectall("select * from " . table("shop_dispatch") . " WHERE deleted=0 and  (provance!='' and provance=:provance) and (city!='' and city=:city) and area=''   order by displayorder desc",array(":provance"=>$defaultAddress['provance'],":city"=>$defaultAddress['city']));
     $addressdispatch3 = mysqld_selectall("select * from " . table("shop_dispatch") . " WHERE deleted=0 and  (provance!='' and provance=:provance) and city='' and area=''   order by displayorder desc",array(":provance"=>$defaultAddress['provance']));
 	$dispatchIndex=0;
 	$addressdispatchs=array($addressdispatch1,$addressdispatch2,$addressdispatch3);

 	 foreach ($addressdispatchs as &$addressdispatch) {
 	 	if(!empty($addressdispatch))
 	 	{
      foreach ($addressdispatch as &$d) {
      	if(!empty($d['express']))
      	{
      	$dispatch[$dispatchIndex]=&$d;
      	$dispatchcode[$dispatchIndex]=$d['express'];
      	$dispatchIndex=$dispatchIndex+1;
      	}
      }
    }
    }	 	
       $addressdispatch = mysqld_selectall("select * from " . table("shop_dispatch")." where deleted=0 and express not in (select express from " . table("shop_dispatch")." where   (provance!='' and provance=:provance) and (city!='' and city=:city) and (area!='' and area=:area) )"
    ." and express not in (select express from " . table("shop_dispatch")." where  (provance!='' and provance=:provance) and (city!='' and city=:city) and area='' ) "
    ." and express not in (select express from " . table("shop_dispatch")." where  (provance!='' and provance=:provance)  and city='' and area='' )   order by displayorder desc",array(":provance"=>$defaultAddress['provance'],":city"=>$defaultAddress['city'],":area"=>$defaultAddress['area']));
      foreach ($addressdispatch as &$d) {
      	if(!empty($d['express'])&&(empty($dispatchcode)||!in_array ($d['express'],$dispatchcode)))
      	{
      	$dispatch[$dispatchIndex]=&$d;
      	$dispatchcode[$dispatchIndex]=$d['express'];
      	$dispatchIndex=$dispatchIndex+1;
      	} 
      }
     
        foreach ($dispatch as &$d) {
            $weight = 0;

            foreach ($allgoods as $g) {
                $weight+=$g['weight'] * $g['total'];
                	if($g['issendfree']==1)
                    		{
                    			$issendfree=1;	
                    		}
            }

            $price = 0;
            if($issendfree!=1)
          	{
	            if ($weight <= $d['firstweight']) {
	                $price = $d['firstprice'];
	            } else {
	                $price = $d['firstprice'];
	                $secondweight = $weight - $d['firstweight'];
	                if ($secondweight % $d['secondweight'] == 0) {
	                    $price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
	                } else {
	                    $price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
	                }
	            }
         		}
            $d['price'] = $price;
        }
        unset($d);
		$paymentconfig="";
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
			$paymentconfig=" and code!='alipay'";
		}else
		{
			if (is_mobile_request()) {
					$paymentconfig=" and code!='weixin'";
				}	
		}
    $payments = mysqld_selectall("select * from " . table("payment")." where enabled=1 {$paymentconfig} order by `order` desc");
   
        if (checksubmit('submit')) {
            $address = mysqld_select("SELECT * FROM " . table('shop_address') . " WHERE id = :id", array(':id' => intval($_GP['address'])));
            if (empty($address)) {
                message('抱歉，请您填写收货地址！');
            }
             if (empty($_GP['dispatch'])) {
                message('请选择配送方式！');
            }
             if (empty($_GP['payment'])) {
                message('请选择支付方式！');
            }
            //商品价格
            $goodsprice = 0;
            $goodscredit=0;
            foreach ($allgoods as $row) {
                if ($item['stock'] != -1 && $row['total'] > $item['stock']) {
                    message('抱歉，“' . $row['title'] . '”此商品库存不足！', mobile_url('confirm'), 'error');
                }
                $goodsprice+= $row['totalprice'];
                if($row['issendfree']==1||$row['type']==1)
                    		{
                    			$issendfree=1;	
                    		}
                  $goodscredit+= intval($row['credit']);
            }
            //运费
        	
            $dispatchid = intval($_GP['dispatch']);
            $dispatchitem = mysqld_select("select sendtype,express from ".table('shop_dispatch')." where id=:id limit 1",array(":id"=>$dispatchid));
            $dispatchprice = 0;
            if($issendfree!=1)
          	{
	            foreach ($dispatch as $d) {
	                if ($d['id'] == $dispatchid) {
	                    $dispatchprice = $d['price'];
	                }
	            }
          	}				
					$ordersns= date('Ymd') . random(6, 1);
						$randomorder = mysqld_select("SELECT * FROM " . table('shop_order') . " WHERE  ordersn=:ordersn limit 1", array(':ordersn' =>$ordersns));
          	if(!empty($randomorder['ordersn']))
          	{
          				$ordersns= date('Ymd') . random(6, 1);
          		
          	}
          	$payment = mysqld_select("select * from " . table("payment")." where enabled=1 and code=:payment",array(':payment'=>$_GP['payment']));
   			if(empty($payment['id']))
   			{
   				message("没有获取到付款方式");	
   			}
   			
   			
   				$paytype=$this->getPaytypebycode($payment['code']);
            $data = array(
                'openid' => $openid,	
                'ordersn' => $ordersns,
                'price' => $goodsprice + $dispatchprice,
                'dispatchprice' => $dispatchprice,
                'goodsprice' => $goodsprice,
                'credit'=> $goodscredit,
                'status' => 0,
                'paytype'=> $paytype,
                'sendtype' => intval($dispatchitem['sendtype']),
                 'dispatchexpress' => $dispatchitem['express'],
                'dispatch' => $dispatchid,
                'paytypecode' => $payment['code'],
                 'paytypename' => $payment['name'],
                'remark' => $_GP['remark'],
                'addressid'=> $address['id'],
                  'address_mobile' => $address['mobile'],
                   'address_realname' => $address['realname'],
                    'address_province' => $address['province'],
                     'address_city' => $address['city'],
                      'address_area' => $address['area'],
                       'address_address' => $address['address'],
                'createtime' => time()		
            );
            mysqld_insert('shop_order', $data);
            $orderid = mysqld_insertid();
            //插入订单商品
            foreach ($allgoods as $row) {
                if (empty($row)) {
                    continue;
                }
                $d = array(
                    'goodsid' => $row['id'],
                    'orderid' => $orderid,
                    'total' => $row['total'],
                    'price' => $row['marketprice'],
                    'createtime' => time(),
                    'optionid' => $row['optionid']
                );
                $o = mysqld_select("select title from ".table('shop_goods_option')." where id=:id limit 1",array(":id"=>$row['optionid']));
                if(!empty($o)){
                    $d['optionname'] = $o['title'];
                }
							//获取商品id
							$ccate = $row['ccate'];
                mysqld_insert('shop_order_goods', $d);
            }
            //清空购物车
            if (!$direct) {
                mysqld_delete("shop_cart", array( "session_id" => $openid));
            }
            $this->setOrderStock($orderid);
            
            clearloginfrom(); 
            header("Location:".mobile_url('pay', array('orderid' => $orderid,'topay'=>'1')) );
        }
       include themePage('confirm');