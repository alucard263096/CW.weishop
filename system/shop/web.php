<?php
defined('SYSTEM_IN') or exit('Access Denied');
class shopAddons  extends BjModule {
		public function do_noticemail()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_adv()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_thirdlogin()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_zhifu()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_dispatch()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_config()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_orderbat()
	{	
		$this->__web(__FUNCTION__);
	}
	public function do_order()
	{
    $this->__web(__FUNCTION__);
	}
	public function do_goods()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_upload()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_category()
	{
		$this->__web(__FUNCTION__);
	}
	public function do_spec() {
		$this->__web(__FUNCTION__);
 	}
  public function do_picdelete() {
    $this->__web(__FUNCTION__);
  }
  public function do_specitem() {
		$this->__web(__FUNCTION__);
  }
  	public function setOrderCredit($openid,$id , $minus = true,$remark='') {
  	 			$order = mysqld_selectall("SELECT * FROM " . table('shop_order') . " WHERE id='{$id}'");
       		if(!empty($order['credit']))
       		{
            if ($minus) {
            	member_credit($openid,$order['credit'],'addcredit',$remark);
                
            } else {
               member_credit($openid,$order['credit'],'usecredit',$remark);
            }
          }
    }
    public function setOrderStock($id , $minus = true) {
        $ordergoods = mysqld_selectall("SELECT * FROM " . table('shop_order_goods') . " WHERE orderid='{$id}'");
        foreach ($ordergoods as $item) {
        	$goods = mysqld_select("SELECT * FROM " . table('shop_goods') . "  WHERE id='".$item['goodsid']."'");
        	if($goods['totalcnf']==1)
        	{
        	return;	
        	}
       
            if ($minus) {
                //属性
                if (!empty($item['optionid'])) {
                    mysqld_query("update " . table('shop_goods_option') . " set stock=stock-:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
                }
                $data = array();
                $data['sales'] = $goods['sales'] + $item['total'];
                mysqld_update('shop_goods', $data, array('id' => $item['goodsid']));
            } else {
                //属性
                if (!empty($item['optionid'])) {
                    mysqld_query("update " . table('shop_goods_option') . " set stock=stock+:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
                }
                $data = array();
                $data['sales'] = $goods['sales'] - $item['total'];
                mysqld_update('shop_goods', $data, array('id' => $item['goodsid']));
            }
        }
    }
}


