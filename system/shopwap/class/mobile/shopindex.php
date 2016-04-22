<?php
			$cfg=globaSetting();
			$advs = mysqld_selectall("select * from " . table('shop_adv') . " where enabled=1  order by displayorder desc");
     
			 $children_category=array();
	 $category = mysqld_selectall("SELECT *,'' as list FROM " . table('shop_category') . " WHERE isrecommand=1 and enabled=1 ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        foreach ($category as $index => $row) {
            if (!empty($row['parentid'])) {
                $children_category[$row['parentid']][$row['id']] = $row;
                unset($category[$index]);
            }
        }
 
        $recommandcategory = array();
        foreach ($category as &$c) {
            if ($c['isrecommand'] == 1) {
                $c['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}'  ORDER BY displayorder DESC, sales" );
                $recommandcategory[] = $c;
            }
            if (!empty($children_category[$c['id']])) {
                foreach ($children_category[$c['id']] as &$child) {
                    if ($child['isrecommand'] == 1) {
                        $child['list'] = mysqld_selectall("SELECT * FROM " . table('shop_goods') . " WHERE  isrecommand=1 and deleted=0 AND status = 1  and pcate='{$c['id']}' and ccate='{$child['id']}'  ORDER BY displayorder DESC, sales DESC " );
                        $recommandcategory[] = $child;
                    }
                }
                unset($child);
            }
        }
        
		include themePage('shopindex');