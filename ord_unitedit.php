<?php
include_once("config.php");
// error_reporting(2047);
if (!isset($_REQUEST['oid']) || !is_numeric($_REQUEST['oid'])) header('Location:index.php');
$userID = $_SESSION["user"]->getWorkerID();
$location = $_SESSION["user"]->getLocationProfile();
global $COUNT_FROM_CATEGORY;
$upd_lock = false;

include ('objects/orderstatus.php');
$ordstatus = new OrderStatus($_SESSION["user"]);
if(is_numeric($_REQUEST['oid'])) {
	$_SESSION['user']->db->select('select status from [order] where id=' . $_REQUEST['oid']);
	$order_status = $_SESSION['user']->db->fetchArray();
	$order_status = $order_status['status'];
	$unit_new = $ordstatus->getOperationStatus('unit_new', $order_status);
}
// Check privilage of creating new unit
if (!isset($_REQUEST['uid']) || !is_numeric($_REQUEST['uid'])) {
	if (!$unit_new) header('Location: orderedit.php?id=' . $_REQUEST['oid'] . '&id_account=' . $_REQUEST['id_account']);
} 
$uid = $_REQUEST['uid'];
$oid = $_REQUEST['oid'];
$id_account = $_REQUEST['id_account'];
$edit = $_REQUEST['edit'];
$edit['status'] = $_REQUEST['status'];
$table_r = "ord_rectangle"; 
// potrzebna data utworzenia orderu w celu wybrania odpowiednich cennikow
$_SESSION['user']->db->select('select Convert(varchar,cr_date,101) dd from [order] where id=' . $oid);
$crd = $_SESSION['user']->db->fetchArray(); 
// print_r($crd);
/*
if (isset($_REQUEST['uid']) && is_numeric($_REQUEST['uid']))
 if(!$_SESSION["user"]->db->prepareForUpdate('ord_unit',$uid))
 {
  $error="This record is locked for update in this moment.";
  //header('Location: ord_unit.php?error='.$error.'');
  $upd_lock = true;
 }
*/

if (isset($_GET['delete'])) {
	if (is_numeric($_GET['id'])) {
		$location = $_SESSION["user"]->getLocationProfile();
		if ($_SESSION["user"]->db->delete($table_r, $_GET['id'], 'DELETE FROM ' . $table_r . ' WHERE id IN (select t.id FROM ' . $table_r . ' t inner join ord_unit eu on eu.id=t.id_ord_unit inner join [order] e on e.id=eu.id_order WHERE t.id = \'' . $_GET['id'] . '\' and e.id_location = \'' . $location['id'] . '\')', true)) {
			$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
			$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);			
		} else $error = 'Deleting rectangle failed.';
		$ss = 'DELETE FROM ' . $table_r . ' WHERE id IN (select t.id FROM ' . $table_r . ' t inner join ord_unit eu on eu.id=t.id_ord_unit inner join [order] e on e.id=eu.id_order WHERE t.id = \'' . $_GET['id'] . '\' and e.id_location = \'' . $location['id'] . '\')';
	} 
} 

function getSlabData($slab_frame, $location, $xx, $crd)
{
	$_SESSION["user"]->db->select("select id_slab,id_frame from slab_frame where id='" . $slab_frame . "'");
	$sf = $_SESSION["user"]->db->fetchArray(); 
	// if ($xx == 1) //Get slab parameters from slab_category table
	// {
	$_SESSION["user"]->db->select($gg = "select sl.id,sl.id_source,sl.is_unknown,sl.id_location,sl.id_thickness,sl.id_stone,sl.sign,sl.length,sl.width,
                                       sc.sqft_price,sl.fragile,sl.polished,sl.slab_price,sc.profit,sc.pickup_profit,sc.m_quality,sc.cutting_risk,
                                       sc.severity,sl.slab_nbr,sl.active_nbr,sl.hold_nbr,sl.is_onyard,sl.description, sl.is_risky,sl.deleted,sl.cr_date,sl.cr_user,
                                       st.name as stone_name,us.name as us_name,us.t_cost,us.is_outsidestone, us.wherepay,us.sqft_price upra,slt.t_cost as trans_cost, sl.outside from Price_category('" . $crd['dd'] . "') sc,slab sl
                                       inner join stone st on sl.id_stone=st.id
                                       left join unknown_stone us on sl.id_source=us.id
                                       left join slab_transport slt on sl.id=slt.id_destslab
                                       where sl.active_nbr>'0' and sl.id_location='" . $location . "'
                                       and sc.id=dbo.categoryID(sl.sqft_price," . $location . ",'" . $crd['dd'] . "') and sl.id='" . $sf['id_slab'] . "'");
	/*
 }
  else   //Get slab parameters from slab table
  {
   $_SESSION["user"]->db->select("select sl.* ,st.name as stone_name,us.name as us_name,us.t_cost,us.is_outsidestone, slt.t_cost as trans_cost from slab sl
                                       inner join stone st on sl.id_stone=st.id
                                       left join unknown_stone us on sl.id_source=us.id
                                       left join slab_transport slt on sl.id=slt.id_destslab
                                       where sl.active_nbr>'0' and sl.id_location='".$location."' and sl.id='".$sf['id_slab']."'");
   }
   */
	$slab_data = $_SESSION["user"]->db->fetchArray();
	if (!is_array($slab_data) || count($slab_data) == 0) {
		// Get price for stone
		$_SESSION["user"]->db->select("select sl.* ,st.name as stone_name,us.name as us_name,us.t_cost,us.is_outsidestone, slt.t_cost as trans_cost, sl.outside from slab sl
                                       inner join stone st on sl.id_stone=st.id
                                       left join unknown_stone us on sl.id_source=us.id
                                       left join slab_transport slt on sl.id=slt.id_destslab
                                       where sl.active_nbr>'0' and sl.id_location='" . $location . "' and sl.id='" . $sf['id_slab'] . "'");
		$slab_data = $_SESSION["user"]->db->fetchArray();
	} 
	// get honing price
	$_SESSION['user']->db->select("select money_val as h_price from config_tab WHERE name='h_price'");
	$hprice = $_SESSION['user']->db->fetchArray();
	$slab_data['h_price'] = $hprice['h_price'];
	//print_r($hprice);

	$_SESSION["user"]->db->select("select sum(t_cost) as t_cost from slab_transport where id_destslab='" . $sf['id_slab'] . "'");
	$tmp = $_SESSION["user"]->db->fetchArray();
	$data['slab'] = $slab_data;
	$data['cost'] = $tmp['t_cost']; 
	// $data['sf']=$sf['id'];
	$data['sf'] = $slab_frame;
	return $data;
} 

if (!$upd_lock) {
	if ($_POST['act'] == "edit") {
		$edit = $_SESSION["user"]->cleansql($edit);
		$wynik = $edit; 
		// $dane=getSlabData($edit['slab_id'],$location['id'],$edit['frame_id'],$COUNT_FROM_CATEGORY);
		//if (is_numeric($edit['id_slab_frame'])) 
		$dane = getSlabData($edit['id_slab_frame'], $location['id'], $COUNT_FROM_CATEGORY,$crd);
		//else $dane = array();
		$slab_d = $dane['slab'];
		//print_r($dane);
		$waste = 0;
		if ($dane['cost'] == '') $dane['cost'] = 0;
		if ($slab_d['trans_cost'] == '') $slab_d['trans_cost'] = 0;
		if ($slab_d['is_outsidestone'] == 1 and $slab_d['wherepay'] == 1) {
			$coef = $slab_d['length'] * $slab_d['width'] / 144;
			$slab_area = $coef;
			while ($slab_area < $edit['area']) $slab_area += $coef;
			$waste = $slab_area - $edit['area']; 
			// print($slab_area.' - '.$edit['area']);
		} 
		if (is_numeric($edit['id_edge'])) $def_edge = '\'' . $edit['id_edge'] . '\'';
		else $def_edge = 'NULL';

		if (!$uid) { // INSERT                 !!!
			if (isset($_POST['alastid']) && lastid_check($_POST['alastid'])) {
				$_SESSION["user"]->db->BeginTransaction();
				if (!$_SESSION["user"]->db->insert($ssq = "INSERT INTO ord_unit (id_order,id_application,id_ord_unit,status,id_edge,name,h_price,honing,cust_price
     ,cust_description,tot_price,elem_nbr,is_rectangle,deleted,slab_nbr,id_salesman,sales_date,cr_date,cr_user)
     VALUES ('" . $oid . "','" . $edit['id_application'] . "','" . $edit['id_ord_unit'] . "','0'," . $def_edge . ",'" . $edit['name'] . "',
     CAST('" . $slab_d['h_price'] . "' AS MONEY),'" . $edit['honing'] . "',CAST ('" . $edit['cust_price'] . "' AS MONEY),'" . $edit['cust_description'] . "',
     CAST('" . $edit['tot_price'] . "' AS MONEY),'0',
     '" . $edit['is_rectangle'] . "','0','" . $edit['slab_nbr'] . "','" . $_SESSION["user"]->getWorkerID() . "',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'" . $_SESSION["user"]->getWorkerID() . "')")) {
					
					$error = "Inserting data failed.Name must be unique.Check if there are available stones for that project (consider area).";
					$_SESSION["user"]->db->Rollback();
				} else {
				//print($ssq);
					// dodawanie rekordow do tabeli ord_slab                  !!
					$uid = $_SESSION["user"]->db->insertID(); 
					$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
					$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
					
					// if(!$_SESSION["user"]->db->insert("INSERT INTO ord_slab (id_ord_unit,id_slab_frame,sqft_price,slab_price,profit,pickup_profit,m_quality,cutting_risk,severity,waste_sqft,t_price,o_price)
					// VALUES ('".$uid."','".$dane['sf']."',CAST('".$slab_d['sqft_price']."' AS MONEY),
					// CAST('".$slab_d['slab_price']."' AS MONEY),'".$slab_d['profit']."','".$slab_d['pickup_profit']."'
					// ,'".$slab_d['m_quality']."','".$slab_d['cutting_risk']."','".$slab_d['severity']."','".$waste."',
					// CAST('".$dane['cost']."' AS MONEY),CAST('".$slab_d['trans_cost']."' AS MONEY))"))
					/*
    if(!$_SESSION["user"]->db->insert("INSERT INTO ord_slab (id_ord_unit,id_slab_frame,sqft_price,slab_price,profit,pickup_profit,m_quality,cutting_risk,severity,waste_sqft,t_price,o_price)
                      VALUES ('".$uid."','".$dane['sf']."',CAST('0' AS MONEY),
                      CAST('0' AS MONEY),'0','0','0','0','0','".$waste."',
                      CAST('0' AS MONEY),CAST('0' AS MONEY))"))
                      */
					if (!$_SESSION["user"]->db->insert($ddc = "INSERT INTO ord_slab (id_ord_unit,id_slab_frame,sqft_price,slab_price,profit,pickup_profit,m_quality,cutting_risk,severity,waste_sqft,t_price,o_price)
                      VALUES ('" . $uid . "'," . $edit['id_slab_frame'] . ",0,0,'0','0','0','0','0','" . $waste . "',0,0)")) {
						// print($ddc); print($ssq);
						$error = "Some data can not be saved. No changes made.Check if there are available stones for that project (consider area).";
						$_SESSION["user"]->db->Rollback();
						$uid = '';
					} else {
						$_SESSION["user"]->db->Commit(); 
						$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
						$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
					}
					// dodawanie rekordow do tabeli ord_slab_end              !!
				} 
			} 
		} else { // UPDATE                 !!!!
			$_SESSION["user"]->db->BeginTransaction();
			if (!$_SESSION["user"]->db->update('ord_unit', $uid, $rr = "UPDATE ord_unit SET id_application='" . $edit['id_application'] . "',id_ord_unit='" . $edit['id_ord_unit'] . "',
                    status='" . $edit['status'] . "',id_edge=" . $def_edge . ",name='" . $edit['name'] . "',honing='" . $edit['honing'] . "',
                    cust_price=CAST('" . $edit['cust_price'] . "' AS MONEY),cust_description='" . $edit['cust_description'] . "',
                    is_rectangle='" . $edit['is_rectangle'] . "',slab_nbr='" . $edit['slab_nbr'] . "' WHERE id='" . $uid . "'")) {
				$error = "Changing data failed.Name must be unique." . $_SESSION['user']->error . ',' . $_SESSION['user']->db->error;
				$_SESSION["user"]->db->Rollback();
			} else {
                /*------------------------------------------------------------------------------------------------------------------------*/
                //if set the
                if($edit['status']=='6')
                {
                    $sql="select * from [ord_unit_status] ous where status=1 and ous.id_ord_unit = ".$uid."";
                    $_SESSION["user"]->db->select($sql);
                    if($_SESSION["user"]->db->numrows() > 0)
                    {
                        $unit = $_SESSION["user"]->db->fetchArray();
                        $update = "update [ord_unit_status] set i_status=2, i_fin_time=GETDATE(), status=2  where id_ord_unit=".$uid." and status=1";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $update))
                        {
                            $error = "Saving data failed.";
                        }

                    }
                }
                /*------------------------------------------------------------------------------------------------------------------------*/
			//echo $rr;
			    $_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
				$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
			   
				if ($edit['id_slab_frame'] != $edit['old_id_slab_frame']) {
                    $_SESSION["user"]->db->select("select id from ord_slab where id_ord_unit = ".$uid);
                    $ordslab_test = $_SESSION["user"]->db->fetchArray();
                    if(!is_numeric($ordslab_test[0])) $_SESSION["user"]->db->select("INSERT INTO [erp].[dbo].[ord_slab] ([id_ord_unit],[id_slab_frame],[sqft_price],[slab_price],[profit],[pickup_profit],[m_quality],[cutting_risk],[severity],[waste_sqft],[t_price],[o_price],[cena_zakupu]) VALUES (".$uid.",null,0,0,0,0,0,0,0,0,0,0,0)");
					if ($edit['id_slab_frame'] == 'NULL') $slab_sql = "UPDATE ord_slab SET id_slab_frame=" . $edit['id_slab_frame'] . ",waste_sqft=0,sqft_price=0,slab_price=0,profit=0,pickup_profit=0,m_quality=0,cutting_risk=0,severity=0,t_price=0,o_price=0 WHERE id_ord_unit='" . $uid . "'";
					else $slab_sql = "UPDATE ord_slab SET id_slab_frame=" . $edit['id_slab_frame'] . ",waste_sqft='" . $waste . "' WHERE id_ord_unit='" . $uid . "'"; 
					//echo $slab_sql;
					if (!$_SESSION["user"]->db->update('ord_slab', $edit['ord_slab_id'], $slab_sql)) {
						$error = "Some data can not be saved.";
						$_SESSION["user"]->db->Rollback();
					} else {
						 $_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
						 $_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
					} //$_SESSION["user"]->db->Commit();
				} else if (trim($edit['cust_description']) != trim($edit['old_cust_description']) && trim($edit['cust_description']) != '') {
					if (!$_SESSION['user']->db->insert('INSERT INTO ord_unit_log(id_src,description,cr_date,cr_user) values(' . $uid . ',\'Custom ($' . $edit['cust_price'] . '):' . trim($edit['cust_description']) . '\',CURRENT_TIMESTAMP,' . $_SESSION["user"]->getWorkerID() . ')')) {
						$error = "Changing custom description failed.";
						$_SESSION["user"]->db->Rollback();
					} //else $_SESSION["user"]->db->Commit();
				} else if ($order_status == 3 && $edit['status'] == 1) {
					$_SESSION['user']->db->select("select count(*) as dep_cnt from ord_payment where id_order=" . $_REQUEST['oid']);
					$cnt_req = $_SESSION['user']->db->fetchArray();
					if ($cnt_req['dep_cnt'] <= 0) {
						$error = "Changing data failed. There is no deposit." . $_SESSION['user']->error . ',' . $_SESSION['user']->db->error;
						$_SESSION["user"]->db->Rollback();
					} //else $_SESSION["user"]->db->Commit();
				} //else $_SESSION["user"]->db->Commit();
				//error_reporting(-1);
				if($edit['old_id_edge'] != $def_edge || (is_null($edit['old_id_edge']) && is_null($def_edge) and $def_edge>0)) {
					$ss = "select id from ord_rectangle where id_ord_unit=".$uid;
					$_SESSION['user']->db->select($ss);
					$rectangles = $_SESSION['user']->db->fetchAllArrays();
					if(is_array($rectangles))
					foreach($rectangles as $k=>$v) {
						$_SESSION['user']->db->select("UPDATE ord_rectangle set id_edge1=".$def_edge." where id=".$v['id']." and id_edge1 IS NOT NULL and id_edge1 = ".$edit['old_id_edge']);
						$_SESSION['user']->db->select("UPDATE ord_rectangle set id_edge2=".$def_edge." where id=".$v['id']." and id_edge2 IS NOT NULL and id_edge2 = ".$edit['old_id_edge']);
						$_SESSION['user']->db->select("UPDATE ord_rectangle set id_edge3=".$def_edge." where id=".$v['id']." and id_edge3 IS NOT NULL and id_edge3 = ".$edit['old_id_edge']);
						$_SESSION['user']->db->select("UPDATE ord_rectangle set id_edge4=".$def_edge." where id=".$v['id']." and id_edge4 IS NOT NULL and id_edge4 = ".$edit['old_id_edge']);	
					}
					$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
					$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
				}
		
				$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
				$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
				
		
				$_SESSION["user"]->db->Commit();
			} 
		} 
	} 
} 
if ($uid) {

	//$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$oid.",".$uid);
	//$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$oid);
	
	// wyci�gniecie danych do edycji
	$_SESSION["user"]->db->select("SELECT ou.*,a.name as a_name,ord.status as ord_status,o.name as o_name,os.id_slab_frame,os.id as ord_slab_id,s.id as slab_id,f.id as frame_id,
                                       f.sign as f_sign,st.name as stone_name,ord.id_location FROM ord_unit ou
                                       inner join application a on ou.id_application=a.id
                                       inner join [order] ord on ord.id=ou.id_order
                                       left join ord_unit o on ou.id_ord_unit=o.id
                                       left join ord_slab os on ou.id=os.id_ord_unit
                                       left join slab_frame sf on sf.id=os.id_slab_frame 
                                       left join slab s on s.id=sf.id_slab 
                                       left join frame f on f.id=sf.id_frame 
                                       left join stone st on s.id_stone=st.id WHERE ou.id='" . $uid . "'"); //bylo inner join
	$wynik = $_SESSION["user"]->db->fetchArray();
	$wynik = $_SESSION["user"]->cleanedit($wynik);
	$str = " AND id!='" . $uid . "'";
	$f_cnt = 1; 
	// echo "<pre>";
	// print_r($wynik);
	// echo "</pre>";
	// wyciagniecie danych koniec
	
} 

if ($_POST['act'] == "Saving")
	$wynik = $edit;
$sql = "";
if (is_numeric($edit['string']))
	$sql = " OR st_id='" . $edit['string'] . "'";
if (is_numeric($wynik['id_slab_frame'])) $sel_sf = $wynik['id_slab_frame'];
else $sel_sf = 1;
if ($edit['string']) {
	$pyt = "select * from getEstimateSlabFrame('" . $location['id'] . "'," . $sel_sf . ",'" . $crd['dd'] . "') where f_sign LIKE '%" . $edit['string'] . "%' OR st_name LIKE '%" . $edit['string'] . "%'" . $sql . " order by st_name";
} else {
	$pyt = "select * from getEstimateSlabFrame('" . $location['id'] . "'," . $sel_sf . ",'" . $crd['dd'] . "') order by st_name";
} 
$_SESSION["user"]->db->select($pyt); 
// print($pyt);
$slabs = $_SESSION["user"]->db->fetchAllArrays($pyt);
if (is_array($slabs))
	$f_cnt = count($slabs);
else
	$f_cnt = 0; 
// }
// wybranie pozosta�ych  unit�w do listy
if (isset($uid) && is_numeric($uid)) {
	$_SESSION["user"]->db->select("SELECT id,name from ord_unit where id_order='" . $oid . "'" . $str . "");
	$other_units = $_SESSION["user"]->db->fetchAllArrays();
	if (is_array($other_units))
		$cnt = count($other_units);
	else
		$cnt = 0;
} 
// koniec wybierania unit�w
// Get created estimates & other orders
$crestimates = '';
if (isset($uid) && is_numeric($uid)) {
	$_MAX_1 = 5;
	$_MAX_2 = 3; 
	// Search for created rectangles
	$_SESSION['user']->db->select($gg = 'select top ' . $_MAX_1 . ' id,height,length,quantity,(e1_length+e2_length+e3_length+e4_length+ b1_et_length+ b1_es_length +b2_et_length+ b2_es_length+b4_et_length+ b4_es_length)/12 as edge,
(b1_length*b1_height+b2_length*b2_height+b3_length*b3_height+b4_length*b4_height)/144 as bspl
 from ord_rectangle where id_ord_unit=' . $uid . ' order by cr_date desc');
	$crrectangles = $_SESSION['user']->db->fetchAllArrays();

	if (is_array($crrectangles)) {
		$suma_rec_quant = 0;
		foreach($crrectangles as $i)
		$suma_rec_quant = $suma_rec_quant + $i['quantity'];
		$cnt = count($crrectangles);
		if ($cnt >= $_MAX_1) {
			$_SESSION['user']->db->select('select count(*) as cnt from ord_rectangle where id_ord_unit=' . $uid);
			$cnt = $_SESSION['user']->db->fetchArray();
			$crrectangles_cnt = $cnt['cnt'];
		} else $crrectangles_cnt = $cnt;
	} 
	// Search for created shapes
	$_SESSION['user']->db->select('select top ' . $_MAX_2 . ' os.id,cs.name,cs.unit, os.shape_nbr,os.price from ord_shape os inner join custom_shape cs on os.id_custom_shape=cs.id where os.id_ord_unit=' . $uid . ' order by os.id desc');
	$crshapes = $_SESSION['user']->db->fetchAllArrays();
	if (is_array($crshapes)) {
		$cnt = count($crshapes);
		if ($cnt >= $_MAX_2) {
			$_SESSION['user']->db->select("select count(*) as cnt from ord_shape where id_ord_unit='$uid'");
			$cnt = $_SESSION['user']->db->fetchArray();
			$crshapes_cnt = $cnt['cnt'];
		} else $crshapes_cnt = $cnt;
	} 
	// Search for created cutouts
	$_SESSION['user']->db->select('select top ' . $_MAX_2 . ' oc.id,st.name,oc.cr_date,quantity from ord_cutout oc left join sink_type st on oc.id_sink_type=st.id where oc.id_ord_unit=' . $uid . ' order by oc.cr_date desc');
	$crcutouts = $_SESSION['user']->db->fetchAllArrays();
	$sum_cut = 0;
	if (is_array($crcutouts)) {
		foreach($crcutouts as $j)
		$sum_cut = $sum_cut + $j['quantity'];
		$cnt = count($crcutouts);
		if ($cnt >= $_MAX_2) {
			$_SESSION['user']->db->select("select count(*) as cnt from ord_cutout where id_ord_unit='" . $uid . "'");
			$cnt = $_SESSION['user']->db->fetchArray();
			$crcutouts_cnt = $cnt['cnt'];
		} else $crcutouts_cnt = $cnt;
	} 
	// Search for created units
	$_SESSION['user']->db->select("select top " . $_MAX_1 . " ou.id,ou.name,ou.id_application,a.name as app_name,ou.cr_date from [ord_unit] ou inner join application a on a.id=ou.id_application where id_order='" . $oid . "' AND ou.id!='" . $uid . "' order by cr_date desc");
	$crunits = $_SESSION['user']->db->fetchAllArrays();
	if (is_array($crunits)) {
		$cnt = count($crunits);
		if ($cnt >= $_MAX_1) {
			$_SESSION['user']->db->select('select count(*) as cnt from [ord_unit] where id_order=' . $oid);
			$cnt = $_SESSION['user']->db->fetchArray();
			$crunits_cnt = $cnt['cnt'];
		} else $crunits_cnt = $cnt + 1;
	} 
	// Search for cutters
	$_SESSION['user']->db->select("select top " . $_MAX_1 . " oc.id,w.fname,w.lname,oc.c_date  from ord_cutter oc
inner join pl_worker pw on pw.id = oc.id_cutter
inner join worker w on w.id= pw.id_worker 
where oc.id_ord_unit='" . $uid . "' order by oc.cr_date desc");
	$crcutters = $_SESSION['user']->db->fetchAllArrays();
	if (is_array($crcutters)) {
		$cnt = count($crcutters);
		if ($cnt >= $_MAX_1) {
			$_SESSION['user']->db->select('select count(*) as cnt from [ord_unit] where id_order=' . $oid);
			$cnt = $_SESSION['user']->db->fetchArray();
			$crcutters_cnt = $cnt['cnt'];
		} else $crcutters_cnt = $cnt;
	} 
	// Search for created installations
	$_SESSION["user"]->db->select($sql89 = "SELECT top 1 oi.id,oi.inst_date,w.fname,w.lname from ord_installation oi
                                 left join pl_worker on oi.id_installer=pl_worker.id
                                 left join worker w on pl_worker.id_worker=w.id 
                                 where id_order=$oid
                                 order by oi.cr_date desc");
	$crinstall = $_SESSION["user"]->db->fetchAllArrays(); 
	//print($sql89);
	// Search for created templates
	$_SESSION["user"]->db->select("SELECT top 1 ot.id,ot.temp_date,w.fname,w.lname from ord_template ot
                                 left join pl_worker on ot.id_templater=pl_worker.id
                                 left join worker w on pl_worker.id_worker=w.id
                                 where id_order=$oid order by ot.cr_date desc");
	$crtemplate = $_SESSION["user"]->db->fetchAllArrays();
} 
// Getting WO status
if (isset($wynik['ord_status']) || is_numeric($wynik['ord_status'])) {
	$_SESSION['user']->db->select('select status from [order] where id=' . $wynik['id_order']);
	$aux = $_SESSION['user']->db->fetchArray();
	$wynik['ord_status'] = $aux['status'];
} 
// Get WO LocationID
if (is_numeric($oid)) {
	$_SESSION['user']->db->select('select id_location FROM [order] WHERE id=' . $oid);
	$loc = $_SESSION['user']->db->fetchArray();
	if ($loc['id_location'] != $location['id']) $upd_lock = true;
} 
$unit_edit = $_SESSION['user']->is_Admin() | !$ordstatus->getAdvUnitOperationStatus('unit_edit', $order_status, $wynik['status']);
if ($unit_edit && !$upd_lock) {
	$lock_form = '';
	$lock_select = '';
} else {
	$lock_form = ' readonly';
	$lock_select = ' disabled';
} 
$new_element = !$ordstatus->getAdvUnitOperationStatus('new_element_lock', $order_status, $wynik['status']);
if (is_numeric($uid)) $_SESSION['ORD_SHAPE_SESSION']['UID'] = $uid;
if (is_numeric($oid)) $_SESSION['ORD_SHAPE_SESSION']['OID'] = $oid;
if (is_numeric($id_account)) $_SESSION['ORD_SHAPE_SESSION']['ID_ACCOUNT'] = $id_account;

@include_once('lists/application.inc.php');
@include_once('lists/edge.inc.php');

if(is_numeric($uid)) {
	$_SESSION["user"]->db->select("SELECT w.id,w.fname,w.lname from worker w , ord_unit ou where ou.cr_user = w.id and ou.id='" . $uid . "'");
	$name = $_SESSION["user"]->db->fetchArray(); 
}
else {
	$name = array();
}
// e-forms
if (isset($uid) && is_numeric($uid)) {
	if ($_SESSION["user"]->hasRestriction('e_forms')) {
		$_SESSION["user"]->db->select("SELECT count(*) from e_forms where id_order=" . $oid);
		$temp_counter = $_SESSION["user"]->db->fetchArray();
		$_SESSION["user"]->db->select("SELECT id,name from e_forms_list order by name");
		$e_forms = $_SESSION["user"]->db->fetchAllArrays();

		if ($temp_counter[0] > 0) {
			for($x = 0; $x < count($e_forms); $x++) {
				$_SESSION["user"]->db->select("SELECT signed,date_signed from e_forms where id_order=" . $oid . " and id_e_form_list=" . $e_forms[$x]['id']);
				$temp = $_SESSION["user"]->db->fetchArray();
				$e_forms[$x]['signed'] = $temp['signed'];
				$e_forms[$x]['date_signed'] = $temp['date_signed'];
			} 
		} 
		// print_r($e_forms);
	} 
} 
include('Smarty.class.php'); 
// print_r($slab_d);
$smarty = new Smarty; 
// H E A D E R - MENU  --------------------------------------------------
$smarty->assign('user_profile', $_SESSION['user']->getUserName());
global $css_color, $date_format;
$smarty->assign('css_color', $css_color);
$smarty->assign('time', date($date_format, time()));
$smarty->assign('menulink', $_SESSION['user']->getMenuLink()); 
// ----------------------------------------------------------------------
$smarty->assign('e_forms', $e_forms);
$smarty->assign('fragile', $slab_d['fragile']);
$smarty->assign('risky', $slab_d['cutting_risk']);
$smarty->assign('cnt', $cnt);
$smarty->assign('error', $error);
$smarty->assign('lock_form', $lock_form);
$smarty->assign('lock_select', $lock_select);
$smarty->assign('unit_edit', $unit_edit);
$smarty->assign('alastid', time());
$smarty->assign('ord_unit', $wynik);
$smarty->assign('other_units', $other_units);
$smarty->assign('uid', $uid);
$smarty->assign('oid', $oid);
$smarty->assign('id_account', $id_account);
$smarty->assign('application', $APPLICATION_LIST);
$smarty->assign('edge', $EDGE_LIST);
$smarty->assign('name', $name);
$smarty->assign('slab', $xxx);
$smarty->assign('slabs', $slabs);
$smarty->assign('frames', $frames);
$smarty->assign('f_cnt', $f_cnt);
$smarty->assign('rsq', $suma_rec_quant);
$smarty->assign('scut', $sum_cut);
$smarty->assign('wo_unit_lock', $ordstatus->getOperationStatus('wo_unit_lock', $wynik['ord_status']) &!$_SESSION['user']->is_Admin()); 
// print_r($wynik);
// $statuses = $ordstatus->generateStatusHTML('0');
$smarty->assign('stats', $ordstatus->generateUnitStatusHTML($wynik['ord_status'], $wynik['status']));
$insert = $_SESSION["user"]->getPrivilege('insert');
$submenu = array();
$submenu[] = array('name' => 'Go Back to WO', 'link' => 'orderedit.php?id=' . $oid . '&id_account=' . $id_account);
if (isset($uid) && is_numeric($uid) && !$upd_lock) {
	// $submenu[] = array('name'=>'Rectangles','link'=>'element_rect.php?uid='.$uid.'&oid='.$oid.'&id_account='.$id_account.'');
	if ($insert && $unit_edit && $new_element) $submenu[] = array('name' => 'Rectangles', 'link' => 'element_rectedit.php?uid=' . $uid . '&oid=' . $oid . '&ida=' . $id_account . ''); 
	// $submenu[] = array('name'=>'Custom charge','link'=>'ord_shape.php?uid='.$uid.'&oid='.$oid.'&id_account='.$id_account.'');
	if ($insert && $unit_edit) $submenu[] = array('name' => 'Custom charge', 'link' => 'ord_shapeedit.php?uid=' . $uid . '&oid=' . $oid); 
	// $submenu[] = array('name'=>'Cutouts','link'=>'ord_cutout.php?uid='.$uid.'&oid='.$oid.'&id_account='.$id_account.'');
	if ($_SESSION['user']->hasRestriction('polishing')) $submenu[] = array('name' => 'Polishing', 'link' => 'ord_polishing.php?uid=' . $uid . '&oid=' . $oid . '&id_account=' . $id_account . '');
	if ($insert && $unit_edit) $submenu[] = array('name' => 'Cutouts', 'link' => 'ord_cutoutedit.php?uid=' . $uid . '&oid=' . $oid . '&id_account=' . $id_account . ''); 
	// $submenu[] = array('name'=>'Cutters','link'=>'ord_cutter.php?uid='.$uid.'&oid='.$oid.'&id_account='.$id_account.'');
	if ($_SESSION['user']->hasRestriction('cutter')) $submenu[] = array('name' => 'Cutters', 'link' => 'ord_cutteredit.php?uid=' . $uid . '&oid=' . $oid . '&id_account=' . $id_account . '');
	if ($insert && $unit_edit) $submenu[] = array('name' => 'Products', 'link' => 'ord_productedit.php?uid=' . $uid . '&oid=' . $oid . '&id_account=' . $id_account . '');
} 
$wo_installation = $ordstatus->getOperationStatus('wo_installation', $order_status['status']);
if ($wo_installation) {
	// if ($_SESSION['user']->getScriptPrivilege('ord_installation.php','select')) $submenu[]=array('name'=>'Installations','link'=>'ord_installation.php?oid='.$oid.'&id_account='.$id_account);
	if ($_SESSION['user']->getScriptPrivilege('ord_installation.php', 'insert')) $submenu[] = array('name' => 'Installations', 'link' => 'ord_installationedit.php?uid=' . $uid . '&oid=' . $oid . '&id_account=' . $id_account);
} 
if (isset($uid) && is_numeric($uid) && !$upd_lock) {
	if (isset($uid) && is_numeric($uid)) $submenu[] = array('name' => 'Slab history', 'link' => '#" onClick="window.open(\'slab_history.php?id=' . $uid . '\',\'slab_hist\',\'width=800,height=600,toolbar=1,menu=1,scrollbars=1,resizable=1\');');
} 

$smarty->assign('submenu', $submenu); 
// Privileges
$smarty->assign('insert', $insert);
$smarty->assign('update', $_SESSION["user"]->getPrivilege('update'));
$smarty->assign('delete', $_SESSION["user"]->getPrivilege('delete'));
$smarty->assign('history', $_SESSION["user"]->getPrivilege('history'));

$smarty->assign('crrectangles', $crrectangles);
$smarty->assign('crrectangles_cnt', $crrectangles_cnt);
$smarty->assign('crshapes', $crshapes);
$smarty->assign('crshapes_cnt', $crshapes_cnt);
$smarty->assign('crcutouts', $crcutouts);
$smarty->assign('crcutouts_cnt', $crcutouts_cnt);
$smarty->assign('crunits', $crunits);
$smarty->assign('crunits_cnt', $crunits_cnt);
$smarty->assign('crinstall', $crinstall);
$smarty->assign('crtemplate', $crtemplate);
$smarty->assign('crcutters', $crcutters);
$smarty->assign('crcutters_cnt', $crcutters_cnt);
$smarty->assign('cutt_edit', $_SESSION['user']->getScriptPrivilege('ord_cutteredit.php', 'update'));

if ($_SESSION["user"]->hasRestriction('slab_nbr')) $smarty->assign('show_slab_nbr', true);
$smarty->assign('printouts', $_SESSION["user"]->hasRestriction('printouts'));
if ($_SESSION["user"]->hasRestriction('unit_print')) $smarty->assign('wo_unit_print', true);
$smarty->assign('show_wo_price', $_SESSION["user"]->hasRestriction('show_wo_price')); 
// obsluga logow
if (isset($uid) && is_numeric($uid)) {
	include 'objects/object.log.php';
	$log = new Log($_SESSION['user'], 'ord_unit', $uid, 'ord_unitedit', array(array('uid', $uid), array('id_account', $id_account), array('oid', $oid)));
	$log->logcmd = $_REQUEST['logcmd'];
	$smarty->assign('log', $log->Automate($_REQUEST, $_GET['logid'], 750)); 
	// obsluga obrazkow
	include_once("objects/object.picture.php");
	$pict = new Picture($_SESSION['user'], array('table' => 'ord_unit_drawing', 'field' => 'id_ord_unit', 'value' => $uid, 'dir' => 'ord_unit', 'link' => 'uid=' . $uid . '&oid=' . $oid . '&id_account=' . $id_account));
	$pict->AssignVar('id_order', $oid);
	$smarty->assign('pict', $pict->FetchPicture('picture.tpl', 750));
} 

$smarty->display('ord_unitedit.tpl');

?>
