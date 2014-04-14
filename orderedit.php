<?
include_once("config.php");

//---------------------FUNKCJE --------------------------------------

function rest($id_acc)
{
$sql = 'select dbo.rest_acc('.$id_acc.') val';
$_SESSION["user"]->db->select($sql);
$wyn = $_SESSION["user"]->db->fetchArray();
return $wyn['val'];
}



// -----------------------------------------------------------------------------BLEDY 

if (isset($_GET['c']) && is_numeric($_GET['c']))
 switch($_GET['c'])
 {
    case 1: $error = '<font color="green">Status was not changed becouse templater is required.Set template and templater before.</font>'; break;
    case 2: $error = '<font color="green">Status was not changed becouse installation is required.Set installation for all units before.</font>'; break;    
    case 3: $error = '<font color="green">You can not change that status as long as you won\'t close all units.</font>'; break;
    case 4: $error = '<font color="green">There is no total payment or payment is unconfirmed.Status was not changed.</font>'; break;
	case 5: $error = '<font color="red"><b>Templater is not working on that day.</b></font>'; break;
	case 6: $error = '<font color="red"><b>Deposit is required.</b></font>'; break;
	case 7: $error = '<font color="red"><b>You may not change order name after a payment was made. Please contact your local accountant.</b></font>'; break;
 }
//-----------------------------------------------------------------------------------------------------------------------------------------------------------


//save form
if(isset($_GET['formval'])) {
	$_SESSION["user"]->db->select("update [order] set form='".$_GET['formval']."' where id=".$_GET['id']);
	echo "update [order] set form='".$_GET['formval']."' where id=".$_GET['id'];
	exit;
}

//generate pick up delivery
if(isset($_GET['generate_delivery'])) {
	$_SESSION["user"]->db->select("select address, town, state, zip from account where id = ". $_GET['aid']);
	$account = $_SESSION["user"]->db->fetchArray();	
	include_once("geocode_googleapi.php");
	$g = find_geo ($account['address'], $account['town'], $account['state'], $account['zip']);
	$_SESSION["user"]->db->insert("INSERT INTO add_task (id_order,id_location,name,description,t_date,price,address,town,state,zip,phone,cr_date,
								   cr_user,status,lat,lon) VALUES (".$_GET['oid'].",'".$_GET['lid']."','Pickup Delivery',' ',".$_GET['t_date'].",'0',
								   '".$account['address']."','".$account['town']."','".$account['state']."','".$account['zip']."','".$_GET['phone']."',
								   CURRENT_TIMESTAMP,'".$_SESSION["user"]->getWorkerID()."',0,'".$g['lat']."','".$g['lon']."')");	 
}

//update geocode manually
if($_GET['geo'] == 1) {
    include_once("geocode_googleapi.php");
    $_SESSION["user"]->db->select("select address, town, state, zip from [order] where id = ". $_GET['id']);
    $temp_order_data = $_SESSION["user"]->db->fetchArray();    
   
    $geotag = find_geo($temp_order_data['address'], $temp_order_data['town'], $temp_order_data['_state'], $temp_order_data['zip']);
    print_r($geotag);
    if(is_array($geotag)) {
        if((float)$geotag['lat'] < 49 && (float)$geotag['lat'] > 23 && (float)$geotag['lon'] < -63 && (float)$geotag['lon'] > -125) {
            $_SESSION["user"]->db->select("update [order] set lat='".substr($geotag['lat'],0,10)."',lon='".substr($geotag['lon'],0,10)."' where id=".intval($_GET['id']));    
        }
    }
    else {
        $geotag = find_geo("", $temp_order_data['town'], $temp_order_data['_state'], $temp_order_data['zip']);
        if(is_array($geotag)) {
            $geotag['lon'] = (float)$geotag['lon']+rand(1,19)/1000;
            $geotag['lat'] = (float)$geotag['lat']+rand(1,19)/1000;
            $_SESSION["user"]->db->select("update [order] set lat='".substr($geotag['lat'],0,10)."',lon='".substr($geotag['lon'],0,10)."' where id=".intval($_GET['id']));    
        }
    }                                                                                  
}


// --------------------------------------------------------------------------MUSI BYC ID ACCOUNT inaczej przenosi do account w celu wybrania customera

if (!is_numeric($_REQUEST['id_account'])) header('Location: account.php');


//-----------------------------------------------------------------------------------------------






if (isset($_POST['alastid2']))
	{
		$_GET['id']=$_POST['id'];
		$_GET['id_account']=$_POST['id_account'];
	}




$_SESSION["user"]->db->connect('erp');
$editform = true; 
$error .= ''; 
$loc = $_SESSION["user"]->getLocationProfile();
$dmy = date("Y-m-d"); 

if($_GET['id'])
	{
		$_SESSION["user"]->db->select($ff='select convert(varchar,cr_date,101) dd from [order] where id ='.$_GET['id']);
		$dd = $_SESSION["user"]->db->fetchArray();
		$dmy= $dd['dd'];
	}

 $_SESSION["user"]->db->select($dd="SELECT * FROM Price_Options('".$dmy."')");
 while ($tab = $_SESSION["user"]->db->fetchArray()) 
 $conf[$tab['name']] = $tab;


//print($dmy);

//Init WO/Unit status & privileges object
include ('objects/orderstatus.php');
$ordstatus = new OrderStatus($_SESSION["user"]);
$templater_in_wo = $_SESSION['user']->hasRestriction('templater_in_wo');
if($_REQUEST['id'] > 0) {
    $_SESSION["user"]->db->select("select status from [order] where id = ".$_REQUEST['id']);
    $order_status = $_SESSION['user']->db->fetchArray();
}
$unit_status = $order_status['unit_status'];
$order_status = $order_status['status'];
$unit_delete = $ordstatus->getOperationStatus('unit_delete',$order_status);
$del_perm = !$ordstatus->getAdvUnitOperationStatus('unit_delete',$order_status,$unit_status);
$ttt=0;
$zunitu = $_GET['zu'];

if(is_numeric($_GET['id'])) {
    $_SESSION["user"]->db->select($ff='select distance d from [order] where id ='.$_GET['id']);
    $dist = $_SESSION["user"]->db->fetchArray();
}
//print($ff);



   //var_dump($unit_delete);
   //var_dump($del_perm);
//print_r($_POST);
//print_r($_GET);


// ----------------------------------------------------------------------------------------------------------------USUNIECIE UNITU Z ORDERU  

switch ($_GET['act'])
{
case "del" : if ($unit_delete)
                if (!$ordstatus->getAdvUnitOperationStatus('unit_delete',$order_status,$unit_status))
                	{	
                       $location = $_SESSION["user"]->getLocationProfile();
					   //$_SESSION["user"]->db->select("DELETE FROM ord_unit where id=".$_GET['uid']);
                       $_SESSION["user"]->db->select("DELETE FROM ord_unit where id=".$_GET['uid']);
					   sleep(2);
					   $_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$_GET['id']);
					   sleep(2);
					   header('Location: orderedit.php?id='.$_GET['id'].'&id_account='.$_GET['id_account']);
					   $ttt=1;
                    }
             break;
}

//----------------------------------------------------------------------------------------------------------------------------------------------------





//Assigning yard salesman
if (is_numeric($_GET['id']) && (isset($_GET['assignme']) || isset($_GET['assign'])))
{   
       //Clear assignement or Assign user who is logged in
       $sql = '';
       if ($_SESSION["user"]->hasRestriction('ysalesman_clear') && (isset($_GET['clear']) || isset($_GET['assign']))) 
       {
         if (isset($_GET['clear'])) $sql = 'update [order] set yard_salesman=NULL,yard_date=NULL WHERE id='.$_GET['id'].' and yard_salesman IS NOT NULL and id_location='.$loc['id'];
          else if (isset($_GET['assign']) && is_numeric($_GET['assign'])) $sql = 'update [order] set yard_salesman='.$_GET['assign'].',yard_date=CURRENT_TIMESTAMP WHERE id='.$_GET['id'].' and id_location='.$loc['id'];
       }  else $sql = 'update [order] set yard_salesman='.$_SESSION['user']->getWorkerID().',yard_date=CURRENT_TIMESTAMP WHERE id='.$_GET['id'].' and yard_salesman IS NULL and id_location='.$loc['id'];
       
       if ($sql != '')
       {
              //echo 'SQL: '.$sql;
              $_SESSION['user']->db->update('[order]',$_GET['id'],$sql);
			  
			  $_SESSION["user"]->db->select('SELECT id FROM ord_unit WHERE id_order='.$_GET['id']);
	  		  $temp_un = $_SESSION["user"]->db->fetchAllArrays();
			  foreach($temp_un as $k => $v) 
			  	$_SESSION["user"]->db->select("exec dbo.ord_unitprice_new ".$_GET['id'].",".$v['id']);
			  
			  $_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$_GET['id']);
              header('Location: orderedit.php?id='.$_GET['id'].'&id_account='.$_GET['id_account']); //die;
       }
}

if(is_numeric($_GET['id_account'])) {
	if (rest($_GET['id_account'])==30 && !is_numeric($_GET['id']))
	{
	 header('Location: accountedit.php?error=1&id='.$_GET['id_account']);
	}
}


//Remember search parameters       
if (is_numeric($_GET['id']))
	{
	  //$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$_GET['id']);
	  $_SESSION["user"]->db->select('SELECT * FROM v_order WHERE id=\''.$_GET['id'].'\'');
	  $data = $_SESSION["user"]->db->fetchArray();
	  $tmp_ext = explode(' ext.',$data['phone']); $data['phone'] = trim($tmp_ext[0]);  $data['phone4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['phone']);
	  $data['phone1'] = trim($tmp[0]);  $data['phone2'] = trim($tmp[1]);  $data['phone3'] = trim($tmp[2]);
	  $tmp_ext = explode(' ext.',$data['w_phone']); $data['w_phone'] = trim($tmp_ext[0]);  $data['w_phone4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['w_phone']);
	  $data['w_phone1'] = trim($tmp[0]);  $data['w_phone2'] = trim($tmp[1]);  $data['w_phone3'] = trim($tmp[2]);
	  $tmp_ext = explode(' ext.',$data['fax']); $data['fax'] = trim($tmp_ext[0]);  $data['fax4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['fax']);
	  $data['fax1'] = trim($tmp[0]);  $data['fax2'] = trim($tmp[1]);  $data['fax3'] = trim($tmp[2]);
	  $tmp_ext = explode(' ext.',$data['cell']); $data['cell'] = trim($tmp_ext[0]);  $data['cell4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['cell']);
	  $data['cell1'] = trim($tmp[0]);  $data['cell2'] = trim($tmp[1]);  $data['cell3'] = trim($tmp[2]);
	  $tmp_ext = explode(' ext.',$data['c_phone']); $data['c_phone'] = trim($tmp_ext[0]);  $data['c_phone4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['c_phone']);
	  $data['c_phone1'] = trim($tmp[0]);  $data['c_phone2'] = trim($tmp[1]);  $data['c_phone3'] = trim($tmp[2]);
	  $tmp_ext = explode(' ext.',$data['c_fax']); $data['c_fax'] = trim($tmp_ext[0]);  $data['c_fax4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['c_fax']);
	  $data['c_fax1'] = trim($tmp[0]);  $data['c_fax2'] = trim($tmp[1]);  $data['c_fax3'] = trim($tmp[2]);
	  $tmp_ext = explode(' ext.',$data['c_cell']); $data['c_cell'] = trim($tmp_ext[0]);  $data['c_cell4'] = trim($tmp_ext[1]); 
	  $tmp = explode('-',$data['c_cell']);
	  $data['c_cell1'] = trim($tmp[0]);  $data['c_cell2'] = trim($tmp[1]);  $data['c_cell3'] = trim($tmp[2]);
	  if ($data['temp_date']!='') 
	  	$dt = strtotime($data['temp_date']); 
	  else 
	  	$dt=0;
	  if (is_numeric($data['floor_nbr'])) 
	  	$data['floor_nbr']++;
  	  if (!is_numeric($data['addition'])) 
	  	$data['addition']=0.00;
  	  if ($dt>0) 
	    $data['temp_date'] =  date($date_format,strtotime($data['temp_date']));
   	  else  
	    $data['temp_date'] = '';
   
 	  if (!$_SESSION["user"]->db->prepareForUpdate('[order]', $_GET['id']))
  	  	{ 
			$lworker = $_SESSION['user']->db->getLockUser(); 
			$error .= "Another user is currently editing this record (".$lworker['worker']."). You can not edit it at this time."; 
			$editform = false;
		}
	} 
else if (is_numeric($_GET['id_account']))
    {
    	$_SESSION["user"]->db->select('select id as id_account,name as a_name,address,town,state as _state,zip,phone, substring(phone, 1, 3) AS phone1, substring(phone, 5, 3) AS phone2,
					     			   substring(phone, 9, 4) AS phone3, fax, substring(fax, 1, 3) AS fax1, substring(fax, 5, 3) AS fax2, substring(fax, 9, 4) AS fax3, cell, substring(cell,
									   1, 3) AS cell1, substring(cell, 5, 3) AS cell2, substring(cell, 9, 4) AS cell3, tax_level,floor_nbr,commition,is_contractor from account 
									   where id=\''.$_GET['id_account'].'\'');
    	$data = $_SESSION["user"]->db->fetchArray();
    	
		if ($data['is_contractor']=='1') 
			{
        		if ($zunitu > 0)
		    		{
		      			$ssq ='select address, town, zip,state, name ,substring(phone, 1, 3) AS phone1, substring(phone, 5, 3) AS phone2, substring(phone, 9, 4) AS phone3, fax, 
						   substring(fax, 1, 3) AS fax1, substring(fax, 5, 3) AS fax2, substring(fax, 9, 4) AS fax3, cell, substring(cell, 1, 3) AS cell1, substring(cell, 5, 3) AS cell2,
						   substring(cell, 9, 4) AS cell3 from estimate where id='.$zunitu;

			  			$_SESSION["user"]->db->select($ssq);
			   			$k = $_SESSION["user"]->db->fetchArray();
						$data['name']= $k['name'];   $data['address'] = $k['address'];  $data['town'] = $k['town']; $data['zip'] = $k['zip'];  $data['phone1'] = $k['phone1'];
						$data['phone2'] = $k['phone2']; $data['phone3'] = $k['phone3']; $data['fax1'] = $k['fax1']; $data['fax2'] = $k['fax2']; $data['fax3'] = $k['fax3']; 
						$data['cell1'] = $k['cell1'];  $data['cell2'] =  $k['cell2']; $data['cell3'] =  $k['cell3'];
	 		   		}
	        
				else  // jak zmienna zunitu est 0 to znaczy ze to jest kontraktor
					{
						$error .= 'This job goes throught a contractor. Please check a jobside addres.';
						$data['address'] = ''; 
						$data['town'] = ''; 
						$data['zip'] = ''; 
						$data['phone1'] = ''; 
						$data['phone2'] = ''; 
						$data['phone3'] = '';
						$data['fax1'] = ''; 
						$data['fax2'] = ''; 
						$data['fax3'] = '';  
						$data['cell1'] = ''; 
						$data['cell2'] = ''; 
						$data['cell3'] = '';
						$data['floor_nbr'] = 1; 
						unset($data['tax_level']);
					}
           } 
	    else 
	      $data['name'] = $data['a_name'];
   }
else if (isset($_POST['alastid']) && lastid_check($_POST['alastid']) && !isset($_REQUEST['logcmd']))
   	 if ((isset($_POST['x']) && isset($_POST['y'])) || (isset($_POST['pickup']) && $_POST['pickup'] == 1))
   		{
        	$data = $_POST;
			$_SESSION["user"]->db->select('select *,substring(phone, 1, 3) AS phone1, substring(phone, 5, 3) AS phone2, substring(phone, 9, 4) AS phone3, substring(fax, 1, 3) AS fax1,
										   substring(fax, 5, 3) AS fax2, substring(fax, 9, 4) AS fax3, substring(cell, 1, 3) AS cell1, substring(cell, 5, 3) AS cell2, 
										   substring(cell, 9, 4) AS cell3 from location where id=\''.$loc['id'].'\'');
        								   $aux = $_SESSION["user"]->db->fetchArray();
        								   $data['address'] = $aux['address']; $data['town'] = $aux['town']; $data['zip'] = $aux['zip']; $data['state'] = $aux['state'];
        								   $data['tax_level'] = '';
        }
   else {
         $data = $_POST;
		 if(is_numeric($_REQUEST['id'])) { 
			 $_SESSION['user']->db->select('select sum(total) as sum from ord_payment where id_order=\''.$_REQUEST['id'].'\'');
			 $sm = $_SESSION['user']->db->fetchArray();
			 $data['deposit'] = $sm['sum'];
		}
		else $data['deposit'] = 0;
         if (!is_numeric($data['message'])) $data['message'] = 0;
         	if (isset($_POST['gen_inv']) && !isset($data['inv_number']))       //Refresh list of used invoices
         		{
            		$_SESSION['user']->db->select('select dbo.get_invoice() as inv');
          			$inv=$_SESSION['user']->db->fetchArray();
          			$data['inv_number'] = $inv['inv'];
         		}
         	$data['name'] = stripslashes($data['name']); $data['address'] = stripslashes($data['address']);  $data['town'] = stripslashes($data['town']);
         	if ($data['h_improvement']=='') 
				$data['h_improvement'] = '0';
         	if ($_POST['name']!='' && $_POST['zip']!='')
         		{
          			if ($_POST['phone1']!='' && $_POST['phone2']!='' && $_POST['phone3']!='')  
						{ 
							$data['phone'] = $_POST['phone1'].'-'.$_POST['phone2'].'-'.$_POST['phone3']; 
							if ($_POST['phone4']!='') 
								$data['phone'] .= ' ext.'.$_POST['phone4'];
						}
          			if ($_POST['w_phone1']!='' && $_POST['w_phone2']!='' && $_POST['w_phone3']!='')  
						{ 
							$data['w_phone'] = $_POST['w_phone1'].'-'.$_POST['w_phone2'].'-'.$_POST['w_phone3']; 
							if ($_POST['w_phone4']!='') 
								$data['w_phone'] .= ' ext.'.$_POST['w_phone4']; 
						}
          			if ($_POST['fax1']!='' && $_POST['fax2']!='' && $_POST['fax3']!='') 
						{ 
							$data['fax'] = $_POST['fax1'].'-'.$_POST['fax2'].'-'.$_POST['fax3']; 
							if ($_POST['fax4']!='') 
								$data['fax'] .= ' ext.'.$_POST['fax4']; 
						}
          			if ($_POST['cell1']!='' && $_POST['cell2']!='' && $_POST['cell3']!='') 
						{ 
							$data['cell'] = $_POST['cell1'].'-'.$_POST['cell2'].'-'.$_POST['cell3']; 
							if ($_POST['cell4']!='') 
								$data['cell'] .= ' ext.'.$_POST['cell4']; 
						}
          			if ($_POST['c_phone1']!='' && $_POST['c_phone2']!='' && $_POST['c_phone3']!='') 
						{ 
							$data['c_phone'] = $_POST['c_phone1'].'-'.$_POST['c_phone2'].'-'.$_POST['c_phone3']; 
							if ($_POST['c_phone4']!='') 
								$data['c_phone'] .= ' ext.'.$_POST['c_phone4']; 
						}
          			if ($_POST['c_fax1']!='' && $_POST['c_fax2']!='' && $_POST['c_fax3']!='') 
						{ 
							$data['c_fax'] = $_POST['c_fax1'].'-'.$_POST['c_fax2'].'-'.$_POST['c_fax3']; 
							if ($_POST['c_fax4']!='') 
								$data['c_fax'] .= ' ext.'.$_POST['c_fax4']; 
						}
          			if ($_POST['c_cell1']!='' && $_POST['c_cell2']!='' && $_POST['c_cell3']!='') 
						{ 
							$data['c_cell'] = $_POST['c_cell1'].'-'.$_POST['c_cell2'].'-'.$_POST['c_cell3']; 
							if ($_POST['c_cell4']!='') 
								$data['c_cell'] .= ' ext.'.$_POST['c_cell4']; 
						}
          
		  			if ($data['resale']!='1') 
						$data['resale'] = 0;
          			if ($data['credit_app']!='1') 
						$data['credit_app'] = 0;
          			if ($data['discount']=='' || $data['commition']=='')
          				{
            				$_SESSION["user"]->db->select('select commition, discount from account where id=\''.$data['id_account'].'\'');
							$tab = $_SESSION["user"]->db->fetchArray();
							if (is_array($tab)) 
								{ 
									$data['discount'] = $tab['discount'];  
									$data['commition'] = $tab['commition']; 
								}
						}
					if ($data['discount']=='') 
						$data['discount'] = 0;
					if ($data['commition']=='') 
						$data['commition'] = 0;
					if ($data['no_template'] == '') 
						$data['no_template'] = 0;
					if ($data['pick_up'] == '') 
						$data['pick_up'] = 0;
					if ($data['check_discount'] == 1) 
						$data['discount'] = $conf['discount']['price']*100;   
					else  
						$data['discount']=0; 

          
          			$data['id_location']=$loc['id'];     //Get information about user's location
					$data['loc_name']=$loc['name'];

          
          			if ($data['pick_up']==1) //If pickup, set zip, tax_level and zone from given location
						{	
							$_SESSION["user"]->db->select('select zip FROM location where id=\''.$data['id_location'].'\'');
							$lzip = $_SESSION["user"]->db->fetchArray();
							$data['zip'] = $lzip['zip'];
					    }
					// ---------------------------------------Check if zip/city is correct
          			$sql = "SELECT vz.zip_code,vz.city,vz.d_charge,vz.m_price,pt.id,pt.tax_level from v_zip vz 
							inner join tax_district td on td.id=vz.id_tax_district
							inner join Price_TaxDist('".$dmy."') pt ON pt.name = td.name
							WHERE id_location='".$data['id_location']."' AND zip_code='".$data['zip']."' and city LIKE '".$data['town']."' and state = '".$data['_state']."'";
         			$_SESSION["user"]->db->select($sql);
          			if ($_SESSION["user"]->db->numRows()==0) 
						$error .= 'Given zip doesn\'t exist or doesn\'t match given town. Correct that field.';
           			else
           				{   //Check if City is correct
          					$zip_found = true;
						  	if ($_POST['old_zip']!=$_POST['zip'] || $_POST['old_town']!=$_POST['town'] || !is_numeric($_POST['id']) || !isset($_POST['tax_level']) || !is_numeric($_POST['tax_level']))
						  		{
						   			$zip_found = false;
									$data['tax_level'] = '';
						   			while($tab=$_SESSION['user']->db->fetchArray())
						   				{
											$data['zone_coef'] = $tab['m_price'];   
											$data['zone_charge'] = $tab['d_charge']; 
											$data['id_tax_district'] = $tab['id_tax_district']; 
											$data['tax_level'] = $tab['tax_level']*100;
            								if (strtoupper($tab['city'])==strtoupper($data['town'])) 
												{ 
													$zip_found=true; 
													break;
												}
           								}
          						}
          					if (!$zip_found)
            					if ($_POST['old_zip']==$_POST['zip'])
              						if ($_POST['old_town']==$_POST['town'])
                						if (!$_SESSION['ORDER_SESSION']['zip_error']) 
											$zip_found=true;
          									$_SESSION['ORDER_SESSION']['zip_error'] = $zip_found;
          									//Check if user can change status of WO (templates or installations completed)
											$status_req = false; 
											$err_mode = 0;  
          									if (!$_SESSION['user']->is_Admin())
												if ($data['status']!=$data['ord_status'])
													{//Templater required
														if ($ordstatus->getOperationStatus('templater_required',$data['status']))
															{
													  			$_SESSION['user']->db->select('select count(*) as cnt from ord_template WHERE id_order='.$_POST['id'].
																							  ' AND deleted=\'0\' and id_templater IS NOT NULL');
													  			$cnt_req = $_SESSION['user']->db->fetchArray();
													  			if (!isset($cnt_req['cnt']) || !is_numeric($cnt_req['cnt']) || $cnt_req['cnt']<=0) 
																	{
																		$status_req = true; 
																		$data['status'] = $data['old_status']; 
																		$err_mode = 1; 
																	}
													        }
															 //Deposit required
														if ($ordstatus->getOperationStatus('deposit_required',$data['status']))
															{
																$_SESSION['user']->db->select("select count(*) as dep_cnt from ord_payment where id_order=".$_POST['id']);
																$cnt_req = $_SESSION['user']->db->fetchArray();
																if ($cnt_req['dep_cnt']<=0) 
																	{
																		$status_req = true; 
																		$data['status'] = $data['old_status']; 
																		$err_mode = 6; 
																	}
															}
													     //Installation required
														if ($ordstatus->getOperationStatus('installation_required',$data['status']))
															{
																$_SESSION['user']->db->select('select count(*) as inst_cnt,(select count(*) from ord_unit where 
																							   id_order=\''.$_POST['id'].'\') as unit_cnt from ord_installation 
																							   where id_order=\''.$_POST['id'].'\' and deleted=0 and id_installer IS NOT NULL');
																$cnt_req = $_SESSION['user']->db->fetchArray();
																if (is_numeric($cnt_req['unit_cnt']) && $cnt_req['unit_cnt']>0)
																	if (!isset($cnt_req['inst_cnt']) || !is_numeric($cnt_req['inst_cnt']) || $cnt_req['inst_cnt']<=0) 
																		{
																			$status_req = true; 
																			$data['status'] = $data['old_status']; 
																			$err_mode = 2; 
																		}
															}
														 //Close status only if payment done and confirmed
														if ($ordstatus->getOperationStatus('payment_required',$data['status']))
															{
																$_SESSION['user']->db->select('select sum(total)-o.tot_price as balance from [order] o 
																							   inner join ord_payment op on o.id=op.id_order inner join [check] ch on ch.id=op.id_check 
																							   where id_order=\''.$_POST['id'].'\' and ch.v_date IS NOT NULL group by o.tot_price');
																$balanceSt = $_SESSION['user']->db->fetchArray();
																if ($balanceSt['balance']<0) 
																	{
																		$status_req = true; 
																		$data['status'] = $data['old_status']; 
																		$err_mode = 4; 
																	}
															}
														//WO unit installed
														if ($ordstatus->getOperationStatus('wo_unit_installed',$data['status']))
															{//Get USER DEPARTMENT
																$dep = $_SESSION['user']->getActiveDepartment();
																$_SESSION['user']->db->select('select count(*) as inst_cnt,(select count(*) from ord_unit 
																							   where id_order=\''.$_POST['id'].'\') as unit_cnt from ord_unit ou 
																							   where ou.id_order=\''.$_POST['id'].'\' and ou.deleted=0 and ou.status IN 
																							   (select top 1 value from unit_operation_priv up inner join unit_status us 
																							   on up.id_unit_status=us.id where id_unit_operation=
																							   (select id from unit_operation where name=\'unit_closed\'))');
																$cnt_req = $_SESSION['user']->db->fetchArray();
																//print_r($cnt_req);
																if (!isset($cnt_req['inst_cnt']) || !is_numeric($cnt_req['inst_cnt']) || $cnt_req['inst_cnt']<=0 || 
																	!isset($cnt_req['unit_cnt'])|| !is_numeric($cnt_req['unit_cnt']) || $cnt_req['unit_cnt']<=0 
																	|| $cnt_req['inst_cnt']<$cnt_req['unit_cnt']) 
																	{
																		$status_req = true; 
																		$data['status'] = $data['old_status']; 
																		$err_mode = 3; 
																	}
															}   
													}
													
												$temp_date = 'NULL'; $temp_error = '';	
													
												if($data['status'] == 11) {
													$_SESSION['user']->db->select("select count(*) as dep_cnt from ord_payment where id_order=".$_POST['id']);
													$cnt_req = $_SESSION['user']->db->fetchArray();
													if ($cnt_req['dep_cnt'] > 0) 
														{
															$temp_error = "You may not cancel the order because there is a deposit made on the order.";
															$data['status'] = $data['old_status']; 
														}
												}
												
         										 //Implement template date check
          										
         										if (trim($data['temp_date'])!='')
         											{ 
														$loc = $_SESSION["user"]->getLocationProfile();
														$_SESSION['user']->db->select('select block_date from block_template where id_location='.$loc['id']);
														$btemp = $_SESSION['user']->db->fetchAllArrays();
														if (isset($btemp) and is_array($btemp) && !isset($_POST['gen_inv']))
																{
																	foreach($btemp as $key=>$val)
																   		{
																	 		if (date("m/d/Y",strtotime($val[0])) == $data['temp_date'] 
																					&& date("m/d/Y") < $data['temp_date'])
																		  		$temp_error = 'Templating is blocked for this date.';
																   		}
																}
												  	    if ($data['temp_date']!=$data['old_temp_date'])
															if (!$_SESSION['user']->hasRestriction('free_td_mod') && strtotime($data['temp_date'])<time()-86400)
																{
																	$temp_error .= 'Template date must be equal or greater than today. Changes were not saved.';
																	$data['temp_date'] = $data['old_temp_date']; 
																	
																	if($data['status'] == 11 && $data['old_status'] == 11) {
																		$temp_error .= 'You may not set a new date for templating while the order is Canceled. Change the status to New Before Template.'; $data['temp_date'] = $data['old_temp_date']; 
																	}
																} 
															else 
                          										{
                                     								//Check if template date<installation date
                                     								$_SESSION['user']->db->select('select top 1 inst_date from ord_installation 
																								   where id_order='.$_POST['id'].' order by inst_date desc');
                                     								$inst_date = $_SESSION['user']->db->fetchArray(); 
																	$inst_date = $inst_date['inst_date'];
                                    								if (is_null($inst_date) || $inst_date=='' || strtotime($data['temp_date'])<strtotime($inst_date)) 
																		{//Allow to change template date
																			$temp_date = $data['temp_date'];
																			//Check if changing status is necessary
																			$_SESSION['user']->db->select('select top 1 value from ord_status where break_point=1 
																										   and \''.$data['status'].'\' IN (select value from ord_status 
																										   WHERE break_point=0) order by value asc');
																			$ord_status = $_SESSION['user']->db->fetchArray();
																			if (!is_null($ord_status['value']) && is_numeric($ord_status['value'])) 
																				$data['status'] = $ord_status['value']; 
																	 	}       
																	else 
																		{ 
																			$temp_error .= 'Template date must be lower than installation date ('.date($date_format,strtotime($inst_date)).'). 	
																						   Changes were not saved.'; $data['temp_date'] = $data['old_temp_date']; } 
                        												}
																	
																	
																 } 
														else if (trim($data['old_temp_date'])!='') 
															{ 
																$temp_error .= 'Template date is required (you can not blank it when it has been set once).'; 
																$data['temp_date'] = $data['old_temp_date']; 
															}
            
          //Check if all fields are correct
														if (!$zip_found) $error .= 'That city has different zip code or doesn\'t exist. Check it or chose \'Save\' to ignore that error.';
														else if ($_POST['name']=='') $error .= 'Name must be set. Changes were not saved.';
														else if ($_POST['address']=='') $error .= 'Address must be set. Changes were not saved.';
														else if ($_POST['town']=='') $error .= 'Town must be set. Changes were not saved.';
														else if ($_POST['zip']=='') $error .= 'Zip must be set. Changes were not saved.';
														else if (!is_numeric($data['tax_level'])) $error .= 'Taxation level must be set.'; 
														else if ($_POST['floor_nbr']=='') $error .= 'Floor number must be set (default: 1). Changes were not saved.';
														else if ($_POST['floor_nbr']==0) $data['floor_nbr'] = 1;
														else if ($_POST['name'] != $_POST['old_name'] && $data['deposit'] > 0 && !$_SESSION["user"]->hasRestriction('change_name_payment')) { header('Location: orderedit.php?id='.$_POST['id'].'&id_account='.$data['id_account'].'&c=7');	}
														else if ($temp_error!='') $error .= $temp_error;
														else if (is_numeric($_POST['id']))
															{ 
																//form bit ublocked
																$ublock_sql = '';
																if ($_SESSION["user"]->hasRestriction('can_unblock_wo')) 
																	$unblock_sql = ($data['unblocked']==1)? ',unblocked=1' : ',unblocked=0';
																//update
																$_SESSION["user"]->db->BeginTransaction();
																$sql = "UPDATE [order] SET name='".str_replace("'", "''", $data['name']);
																if (isset($data['inv_number'])) 
																	$sql .= "',inv_number='".$data['inv_number'];
																$sql.= "',address='".str_replace("'", "''", $data['address'])."',town='".str_replace("'", "''", $data['town'])."',
																	   state='".$data['_state']."', zip='".$data['zip'];
																if ($_SESSION["user"]->hasRestriction('edit_phone_no')) 
																	$sql .= "', phone='".$data['phone']."', w_phone='".$data['w_phone']."',fax='".$data['fax']."', cell='".$data['cell'];
																$sql.= "',cust_price=CAST('".$data['cust_price']."' AS MONEY)".",cust_description='".$data['cust_description']."',
																		 discount=CAST('".$data['discount']."' AS MONEY),status='".$data['status']."',h_improvement='".
																		 $data['h_improvement']."',form='".$data['form']."',tax_level='".$data['tax_level']."',message='".$data['message'].
                       													 "',floor_nbr='".($data['floor_nbr']-1)."',add_sales_tax_ny='".$data['add_sales_tax']."',no_template='".$data['pick_up']."',
																		 pick_up='".$data['pick_up']."'".$unblock_sql;
																if ($data['message']!=$data['oldmessage']) 
																	$sql .= ",message_date=CURRENT_TIMESTAMP,message_user='".$_SESSION["user"]->getWorkerID()."'";
																if ($temp_date!='NULL') $sql .= ",temp_date='".$temp_date."'";
																if (isset($data['addition'])) $sql .= ",addition='".$data['addition']."'";
																if (isset($data['temp_extrahelp']) && is_numeric($data['temp_extrahelp'])) {
																	$data['temp_extrahelp'] = ($data['temp_extrahelp'] < 0 ? 0 : $data['temp_extrahelp']);
																	$sql .= ",temp_extrahelp='".$data['temp_extrahelp']."'";
																}
																if (isset($data['inst_extrahelp']) && is_numeric($data['inst_extrahelp']))  {
																	$data['inst_extrahelp'] = ($data['inst_extrahelp'] < 0 ? 0 : $data['inst_extrahelp']);
																	$sql .= ",inst_extrahelp='".$data['inst_extrahelp']."'";  
																}
																if ($_REQUEST['sel_est']==0) 
																	$sql .= ", id_estimate=NULL "; 
																else 
																	$sql.= ", id_estimate=".$_REQUEST['sel_est']." ";
																if ($_POST['old_zip']!=$_POST['zip'] || $_POST['old_town']!=$_POST['town'] || !is_numeric($_POST['id']) || 
																	!isset($_POST['tax_level']) || !is_numeric($_POST['tax_level']))
																 	$sql .= ",zone_coef='".$data['zone_coef']."',zone_charge='".$data['zone_charge']."'";
																if (isset($_POST['only_template']) && is_numeric($_POST['only_template']))
																	 $sql .= ',only_template=\''.$_POST['only_template'].'\'';
																$sql.= " WHERE id='".$_POST['id']."'";
															   echo $sql;
												                // die;
																if ($_SESSION["user"]->db->update('[order]',$_POST['id'],$sql))
																	{
																		$_SESSION["user"]->db->Commit();
																  		
																		$_SESSION["user"]->db->select('SELECT id FROM ord_unit WHERE id_order='.$_POST['id']);
																		  $temp_un222 = $_SESSION["user"]->db->fetchAllArrays();
																		 // print_r($temp_un);
																		  foreach($temp_un222 as $t22 => $v222) { 
																			$_SESSION["user"]->db->select($s="exec dbo.ord_unitprice_new ".$_POST['id'].",".$v222['id']);
																		  	//echo $s."<br>";
																			//print_r($aaa);
																		  }
																	    $_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$_POST['id']);
																		
																		//usuwanie z windykacji po zaplacie
																  		if($data['status']=='10' || $data['status']=='11') $_SESSION['user']->db->select("update due_days set paid=1 where id_order=".$_POST['id']);
																		
																		//geocoding
																		if($zip_found) {
																			include_once("geocode_googleapi.php");
																			$geotag = find_geo($data['address'], $data['town'], $data['_state'], $data['zip']);
																			if(is_array($geotag)) {
																				if((float)$geotag['lat'] < 49 && (float)$geotag['lat'] > 23 && (float)$geotag['lon'] < -63 && (float)$geotag['lon'] > -125) {
																					$_SESSION["user"]->db->select("update [order] set lat='".substr($geotag['lat'],0,10)."',lon='".substr($geotag['lon'],0,10)."' where id=".intval($_POST['id']));	
																				}																	
																			}
                                                                            else {
                                                                                $geotag = find_geo("", $data['town'], $data['_state'], $data['zip']);
                                                                                if(is_array($geotag)) {
                                                                                    $geotag['lon'] = (float)$geotag['lon']+rand(1,19)/1000;
                                                                                    $geotag['lat'] = (float)$geotag['lat']+rand(1,19)/1000;
                                                                                    $_SESSION["user"]->db->select("update [order] set lat='".substr($geotag['lat'],0,10)."',lon='".substr($geotag['lon'],0,10)."' where id=".intval($_POST['id']));    
                                                                                }
                                                                            }            
																		}
																		//end geocoding
																		
																		//canceling order reason
																		if(trim($_POST['cancel_order_reason']) != "") {
																			$_SESSION['user']->db->insert("INSERT INTO order_log(id_src,description,cr_date,cr_user) values(".$_POST['id'].",'REASON FOR CANCELING: ".$_POST['cancel_order_reason']."',CURRENT_TIMESTAMP,".$_SESSION["user"]->getWorkerID().")");
																		}
																		
																		
																		//translate additional price to polish
																		if (strpos($data['addition'],".") === FALSE)
																			$data['addition'] = ".00";
																		if (($data['addition']!=$data['old_addition'])) {
																			include_once("lists/translate.inc.php");
																			$data['addition_string'] = "";
																			$addition_split = str_split($data['addition']);
																			//print_r($addition_split);
																			for($x = 0; $x < count($addition_split); $x++) {
																				$data['addition_string'] .= $TRANSLATE_LIST[$addition_split[$x]]." ";
																			}
																			$_SESSION['user']->db->insert('INSERT INTO order_log(id_src,description,cr_date,cr_user) 
																										values('.$_POST['id'].',\'ap...'.$data['addition_string'].
																										'\',CURRENT_TIMESTAMP,'.$_SESSION["user"]->getWorkerID().')');
																		}
																		//print_r($data);
																  		//Insert log when custom description has changed
																  		if ((trim($data['cust_price'])!=trim($data['old_cust_price']) || trim($data['cust_description'])!=trim($data['old_cust_description'])) && trim($data['cust_description'])!='')
																				$_SESSION['user']->db->insert('INSERT INTO order_log(id_src,description,cr_date,cr_user) 
																												values('.$_POST['id'].',\'Custom ($'.$data['cust_price'].'):'.
																												trim($data['cust_description']).'\',CURRENT_TIMESTAMP,'.
																												$_SESSION["user"]->getWorkerID().')');
																				//Assign templater if set
																		if (($templater_in_wo && isset($_POST['templater_in_wo_worker']) && $_POST['templater_in_wo_worker']!=
																			$_POST['old_templater_in_wo_worker']) || $_POST['temp_daytime_req'] != $_POST['old_temp_daytime_req'])       
																			{       
																				//unassigned templator
																				if($_POST['templater_in_wo_worker']==0)	$templater='NULL'; else $templater=$_POST['templater_in_wo_worker'];
																				$_SESSION["user"]->db->select("select count(*) as cnt from workeroff where id_worker = (select id_worker from pl_worker where id=".$templater.") and dzien = '".$_POST['temp_date']."' and id_location = ".$loc['id']);
																				$worker_off = $_SESSION["user"]->db->fetchArray();
																				//print_r($worker_off);
																				
																				//get order for task assigned for new templator          
																				include_once("functions/task_order.php");
																				$task_ord = get_task_order($_POST['templater_in_wo_worker'],$_POST['old_templater_in_wo_worker'],
																											$_POST['temp_date'],$_POST['ord_template_order']);
                                                                                if(!is_numeric($task_ord)) $task_ord = 0;                            
																				if($worker_off['cnt'] == 0) { 
																					$_SESSION['user']->db->update('ord_template',0,'update ord_template set id_templater='.$templater.',[order]='.
																												$task_ord.', daytime='.$_POST['temp_daytime_req'].' where id_order='.$_POST['id'].' and temp_date IS NOT NULL and 
																												temp_date=(select max(temp_date) from ord_template where 
																												id_order='.$_POST['id'].')');
																					$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$_POST['id']);
																				}
																				else {
																					$err_mode = 5;
																				}
																			}
																		
																 		header('Location: orderedit.php?id='.$_POST['id'].'&id_account='.$data['id_account'].'&c='.$err_mode);
																	 } 
																 else { $error .= 'Saving data failed5.'.$sql; $_SESSION["user"]->db->Rollback();}
											  			} 
											  		else //insert
												  		if (is_numeric($data['zone_coef']) && is_numeric($data['tax_level']))
												  			{
													  //Get config parameters
																  $sql  = "SET IDENTITY_INSERT [order] ON\n";
																  $sql .= "INSERT INTO [order](id,id_account, id_location,id_estimate,inv_number, address, town, state, zip,phone,w_phone,
																  		   fax, cell, status, backsplash, edge, area, name, distance, cust_price, cust_description, tot_price, discount,
																		   d_price, tax_level, h_improvement, xml, form, temp_date,temp_time,templated, commition, worker_cost, zone_coef, 
																		   zone_charge,cost_per_mile, floor_nbr, id_confirm, confirm_date, close_date, no_template, pick_up, deleted,
																		   cr_date, cr_user) VALUES([dbo].[generateWOID](),'".$data['id_account']."','".$data['id_location']."',NULL,'"
																		   .$data['inv_number']."','".str_replace("'", "''", $data['address'])."', '".str_replace("'", "''", $data['town']).
																		   "','".$data['_state']."', '".$data['zip']."','".$data['phone']."','".$data['w_phone']."','".$data['fax']."','".
																		   $data['cell']."','".$data['status']."',0,0,0,'".str_replace("'", "''", $data['name'])."','0',CAST('"
																		   .$data['cust_price']."' AS MONEY),'".$data['cust_description']."',0,CAST('".$data['discount']."' AS MONEY),
																		   CAST('".$data['d_price']."' AS MONEY), '".$data['tax_level']."','".$data['h_improvement']."','','".$data['form'].
																		   "',".$temp_date.",'".$data['temp_time']."',0,'".$data['commition']."',CAST('".$conf['worker_cost']['price']."' 
																		   AS money),'".$data['zone_coef']."','".$data['zone_charge']."',CAST('".$conf['cost_per_mile']['price']."' 
																		   AS money),'".($data['floor_nbr']-1)."',NULL,NULL,NULL,'".$data['no_template']."','".$data['pick_up'].
																		   "',0,CURRENT_TIMESTAMP,'".$_SESSION["user"]->getWorkerID()."')";
																  $sql .= "\nSET IDENTITY_INSERT [order] OFF\n";
                      //echo $sql;
																  if ($_SESSION["user"]->db->insert($sql)) 
																  		{	
																			$_SESSION['user']->db->select('select num_val as id from config_tab where name=\'inserted_WO_id\'');
																		 	$insert = $_SESSION['user']->db->fetchArray();
																			$_SESSION['ORDER_SESSION']['CNT'] = '';
																			
																			//geocoding
																			if($zip_found) {
																				include_once("geocode_googleapi.php");
																				$geotag = find_geo($data['address'].",", $data['town'], $data['_state'], $data['zip']);
																				//print_r($geotag);
																				if(is_array($geotag)) {
																					if($geotag['lat'] < 49 && $geotag['lat'] > 23 && $geotag['lon'] < -63 && $geotag['lon'] > -125)
																						$_SESSION["user"]->db->select("update [order] set lat='".substr($geotag['lat'],0,10)."',lon='".substr($geotag['lon'],0,10)."' where id=".intval($insert['id']));																				
																				}
                                                                                else {
                                                                                    $geotag = find_geo("", $data['town'], $data['_state'], $data['zip']);
                                                                                    //print_r($geotag);
                                                                                    if(is_array($geotag)) {
                                                                                        $geotag['lon'] = (float)$geotag['lon']+rand(1,19)/1000;
                                                                                        $geotag['lat'] = (float)$geotag['lat']+rand(1,19)/1000;
                                                                                        $_SESSION["user"]->db->select("update [order] set lat='".substr($geotag['lat'],0,10)."',lon='".substr($geotag['lon'],0,10)."' where id=".intval($insert['id']));
                                                                                    }
                                                                                }
																			}
																			//end geocoding
																			
																			$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".((int)$insert['id']));
																			
																		 	header('Location: orderedit.php?id='.((int)$insert['id']).'&id_account='.$data['id_account'].'&c='.$err_mode); 
																  		} 
																  else  $error .= 'Saving data failed.';
                  											} 
													   else 
													   		{
																   $error .= 'Getting zone price failed (city or zip are incorrect). Check it or chose \'Save\' to ignore that error.';
																   $data['tax_level']=0; 
																   $_SESSION["user"]->db->select("SELECT max(d_charge) as max from zone where id_location='".$data['id_location']."'");
																   $tab=$_SESSION['user']->db->fetchArray(); $data['zone_coef'] = $tab['max'];
															}
           								}
         			}
       }

if (!isset($data)) 
$data = $_POST;
$data['cust_description'] = trim($data['cust_description']);
//print_r($_POST);
if(isset($_POST['inv_number']))
	if ($error!='' && trim($data['inv_number'])!=trim($data['oldinv_number']) && trim($data['inv_number'])!='0') 
		{ 
			$data['inv_number']=$data['oldinv_number']; 
			$error.='Invoice must be unique in a system.';
		}

if (!isset($data['id_location']))
	{
 		$data['id_location']=$loc['id'];
 		$data['loc_name']=$loc['name'];
	}

for($i=1;$i<4;$i++)
	{
		 $data['phone'.$i] = trim($data['phone'.$i]);
		 $data['fax'.$i] = trim($data['fax'.$i]);
		 $data['cell'.$i] = trim($data['cell'.$i]);
	}

unset($state);
include_once('lists/state.php');
unset($contact);
$contact['0']='Phone'; $contact['1']='Fax';

if ($data['cust_price']=='') $data['cust_price'] = 0;
if ($data['floor_nbr']=='') $data['floor_nbr'] = 1;
if (isset($data['state'])) $data['_state'] = $data['state'];



//-------------------------------------------------------------------------Get balance

if(is_numeric($_REQUEST['id'])) {
	$_SESSION['user']->db->select('select sum(total) as sum from ord_payment where id_order=\''.$_REQUEST['id'].'\'');
	$sm = $_SESSION['user']->db->fetchArray();
	$data['deposit'] = $sm['sum'];
}
else $data['deposit'] = 0;
$crestimates = '';
if (isset($data['id']) && is_numeric($data['id']))
	{
  		$_MAX_ESTORD = 5;
  		//Search for created estimates
		$_SESSION['user']->db->select('select top '.$_MAX_ESTORD.' id,name,cr_date from estimate where id_account='.$data['id_account'].' order by cr_date desc');
		$crestimates = $_SESSION['user']->db->fetchAllArrays();
		if (is_array($crestimates))
			{
		    	$cnt = count($crestimates);
		    	if ($cnt>=$_MAX_ESTORD)
		    		{
		      			$_SESSION['user']->db->select('select count(*) as cnt from estimate where id_account='.$data['id_account']);
		      			$cnt = $_SESSION['user']->db->fetchArray();
		      			$crestimates_cnt = $cnt['cnt'];
		    		} 
		    	else $crestimates_cnt = $cnt;
		  	}
  			//Search for created orders
  			$_SESSION['user']->db->select('select top '.$_MAX_ESTORD.' o.id,o.name,o.cr_date,case when LEN(os.name)<10 THEN os.name ELSE SUBSTRING(os.name,0,10)+\'...\' END as status
  			from [order] o
  			left join ord_status os on o.status=os.value
  			where o.id_account='.$data['id_account'].'
  			and o.id<>\''.$data['id'].'\'
  			order by o.cr_date desc');

  			$crorders = $_SESSION['user']->db->fetchAllArrays();
  			if (is_array($crorders))
  				{
				    $cnt = count($crorders);
				    if ($cnt>=$_MAX_ESTORD)
				    	{
					    	$_SESSION['user']->db->select('select count(*) as cnt from [order] where id_account='.$data['id_account']);
					    	$cnt = $_SESSION['user']->db->fetchArray();
					    	$crorders_cnt = $cnt['cnt'];
				    	} 
				    else $crorders_cnt = $cnt+1;
				}
				
  				//Search for created units
  				$_SESSION['user']->db->select('select ou.id,ou.name,ou.id_application,a.name as app_name,ou.cr_date from [ord_unit] ou inner join application a on a.id=ou.id_application where id_order='.$data['id'].' order by cr_date desc');
				$crunits = $_SESSION['user']->db->fetchAllArrays();
				if (is_array($crunits))
					{
				    	$crunits_cnt = count($crunits);
				    }
	}

//Check if printing is available
$can_print = false;
if (is_numeric($data['id']))
	{
	   $_SESSION['user']->db->select('select dbo.canPrintOrder('.$data['id'].',\''.date($date_format,time()).'\') as can');
	   $canp = $_SESSION['user']->db->fetchArray();
	   $can_print = $canp['can'];
	}

//Encrypt phones
//Get phone prefix
$_SESSION['user']->db->select("select num_val FROM config_tab where name='ph_prefix'");
$prefix = $_SESSION['user']->db->fetchArray();
$prefix['num_val'] = sprintf("%d",$prefix['num_val']);
include('functions/encrypt.php');
$prefix = code($prefix['num_val']);
$data['call_phone'] = code(trim(str_replace('-','', $data['phone'])));
if ($data['call_phone'] != '') $data['call_phone'] = $prefix.$data['call_phone'];
$data['call_w_phone'] = code(trim(str_replace('-','', $data['w_phone'])));
if ($data['call_w_phone'] != '') $data['call_w_phone'] = $prefix.$data['call_w_phone'];
$data['call_fax'] = code(trim(str_replace('-','', $data['fax'])));
if ($data['call_fax'] != '') $data['call_fax'] = $prefix.$data['call_fax'];
$data['call_cell'] = code(trim(str_replace('-','', $data['cell'])));
if ($data['call_cell'] != '') $data['call_cell'] = $prefix.$data['call_cell'];

//Get information about types of payments
if (isset($data['id']) && is_numeric($data['id']))
	{
	    $_SESSION['user']->db->select("select dbo.scanRequired(".$data['id'].") as sr");
	    $sr = $_SESSION['user']->db->fetchArray(); $sr = $sr['sr'];
	} 
else $sr = 1;
if ($can_print==1)
	if ($sr==1)
		if(is_null($data['inv_number']) || $data['inv_number']<1) $can_print=0;

// checking assign/able estimates
if(is_numeric($data['id'])) {
	$est_sql = "SELECT o.id as oid, o.id_estimate as oest,e.id id_est,e.name as namees,eu.name FROM [order] as o
				inner JOIN account as a ON o.id_account=a.id 
				inner JOIN estimate as e ON e.id_account=a.id 
				inner join est_unit as eu ON eu.id_estimate = e.id
				WHERE e.id not in 
				(select id_estimate from [order] where id_estimate>0 and id_estimate<>o.id_estimate)
				and o.id=".$data['id'];
	
	$_SESSION['user']->db->select($est_sql);
	$est_list = $_SESSION['user']->db->fetchAllArrays();
}
else $est_list = array();

$temp ='';
$licz =-1;
$es = array();
if (is_array($est_list))
	{
		foreach($est_list as $i=>$k)
			{
		  		if($k['id_est']!=$temp)
		      		{
		        		$temp =$k['id_est'];
		        		$es[++$licz] = $k;
		      		}
		       else
		      		{
		        		$es[$licz]['name'].=', '.$k['name'];
		
		      		}
			}
		$est_list =$es;
	 }
//print($est_sql);
if (!is_numeric($data['floor_nbr']) || $data['floor_nbr']<1) $data['floor_nbr']=1;
//Lock edit if user is from other location
if (is_numeric($data['id_location']) && $loc['id']!=$data['id_location']) $editform = false;

//Get information about services
$data['service_cnt'] = 0;
if ($crunits_cnt>0 && is_numeric($data['id']))
	{
		$sql = 'SELECT count(*) as cnt FROM service WHERE id_order='.$data['id'];
		$_SESSION['user']->db->select($sql);
		$serv_cnt = $_SESSION['user']->db->fetchArray();
		$data['service_cnt'] = $serv_cnt['cnt'];       
	}       

if(is_numeric($_GET['id'])) {
	$s='select z.value, ct.[name] from 
		(select isnull(sum(op.total),0) as value, max(op.id_check) as idcheck from ord_payment as op
		where id_order='.$_GET['id'].'    ) as z
		left join [check] as c on c.id=z.idcheck 
		left join check_type as ct on ct.id= c.id_check_type';
	
	$_SESSION["user"]->db->select($s);
	$balance = $_SESSION["user"]->db->fetchArray();
}
else $balance = 0;

if ($data['rownaj']==1)
	{
		$sql='select  o.id,o.tot_price-op.wpl from [order] o 
			  inner join ord_installation oi ON oi.id_order=o.id
			  inner join ( select id_order, sum(total) wpl from ord_payment group by id_order )op ON op.id_order=o.id 
			  where o.id='.$_POST['id'];  
  
  		$_SESSION['user']->db->select($sql);
 		$rownanie = $_SESSION['user']->db->fetchArray();
		// print_r($rownanie); 
 		if ($rownanie[1]!=0)
  			{
  				$tx=($data['tax_level'] +100)/100;
 				// print($tx);
   				$kwota = $rownanie[1]/$tx;
  				$sql = 'update [order] set cust_price=cust_price-('.$kwota.') where id='.$_POST['id'];
     			if ($_SESSION["user"]->db->update('[order]',$_POST['id'],$sql))
	 				{ 
						$_SESSION["user"]->db->select("exec dbo.ord_orderprice_new ".$_POST['id']);
	 					header('Location: orderedit.php?id='.$_POST['id'].'&id_account='.$data['id_account']);
	 				}
 			}
  	}
  

$_SESSION["user"]->db->select('select * FROM location where id=\''.$data['id_location'].'\'');
$lzip = $_SESSION["user"]->db->fetchArray();

if(is_numeric($data['id_account'])) {	  
	$_SESSION["user"]->db->select('select address,town,state,zip FROM account where id=\''.$data['id_account'].'\'');
	$dacc = $_SESSION["user"]->db->fetchArray();
} else $dacc = array();		  
		  
$js = 'var Pick = new Array();'."\n";
$js.= ' Pick[1] =\''.$dacc['address']."'\n" ; //address estimate
$js.= ' Pick[2] =\''.$dacc['town']."'\n" ;//address estimate
$js.= ' Pick[3] =\''.$dacc['state']."'\n";//address estimate
$js.= ' Pick[4] =\''.$dacc['zip']."'\n" ;//address estimate
	
	
$js.= ' Pick[5] =\''.$lzip['address']."'\n" ; //address estimate
$js.= ' Pick[6] =\''.$lzip['town']."'\n" ;//address estimate
$js.= ' Pick[7] =\''.$lzip['state']."'\n";//address estimate
$js.= ' Pick[8] =\''.$lzip['zip']."'\n"; //address estimate

	

include('Smarty.class.php');
$smarty = new Smarty;
        //H E A D E R - MENU  ----------------prnt----------------------------------
        $smarty->assign('user_profile',$_SESSION['user']->getUserName());
        global $css_color,$date_format;
        $smarty->assign('css_color',$css_color);
        $smarty->assign('time',date($date_format,time()));
        $smarty->assign('menulink',$_SESSION['user']->getMenuLink());
        //----------------------------------------------------------------------

$smarty->assign('unit_delete',$unit_delete);        //$delete eq true && $unit_delete && $dp && $wo_unit_lock
$smarty->assign('dp',$del_perm);
$smarty->assign('delete_unit',$ttt);
$smarty->assign('bal',$balance);
$smarty->assign('discount',$conf['discount']['price']*100);
$smarty->assign('data',$data);
//print_r($data);
$smarty->assign('estimates',$est_list);
$smarty->assign('state',$state);
$smarty->assign('contact_method',$contact);
$smarty->assign('id',$_REQUEST['id']);
$smarty->assign('forms',array('Installation Release Form','Confirmation Form'));

$unit_new = $ordstatus->getOperationStatus('unit_new',$data['status']);
$payment = $ordstatus->getOperationStatus('payment',$data['status']);
$wo_template = $ordstatus->getOperationStatus('wo_template',$data['status']);
$wo_installation = $ordstatus->getOperationStatus('wo_installation',$data['status']);

//$statuses = $ordstatus->generateStatusHTML('0');
if ($crunits_cnt>0) $smarty->assign('stats',$ordstatus->generateStatusHTML($data['status']));
$smarty->assign('payment',$payment);

$smarty->assign('home_improvement',$ordstatus->getOperationStatus('home_improvement',$data['status']));
$smarty->assign('inv_nbr',$ordstatus->getOperationStatus('inv_nbr',$data['status']));
$smarty->assign('new_unit',$unit_new);
$smarty->assign('loc_price',$ordstatus->getOperationStatus('loc_price',$data['status']));
$smarty->assign('distance',$dist['d']);
$smarty->assign('alastid',time());
$smarty->assign('can_print',$can_print);
$smarty->assign('sr',$sr);
$smarty->assign('js',$js);
$smarty->assign('editform',$editform);
$smarty->assign('script','order');
$smarty->assign('error',$error);
$smarty->assign('zunitu',$zunitu);
$smarty->assign('edit_unit',$_SESSION["user"]->hasRestriction('edit_unit') & !$_SESSION['user']->is_Admin());
$smarty->assign('show_wo_price',$_SESSION["user"]->hasRestriction('show_wo_price'));
$smarty->assign('calculate',$_SESSION["user"]->hasRestriction('calculate'));
$smarty->assign('assign_acc',$_SESSION["user"]->hasRestriction('assign_acc'));
$smarty->assign('addition_priv',$_SESSION["user"]->hasRestriction('addition'));
$smarty->assign('extrahelp_priv',$_SESSION["user"]->hasRestriction('extra_help')); 
$smarty->assign('can_always_print',$_SESSION["user"]->hasRestriction('can_always_print'));
$smarty->assign('can_unblock_wo',$_SESSION["user"]->hasRestriction('can_unblock_wo'));
$smarty->assign('edit_phone_no',$_SESSION["user"]->hasRestriction('edit_phone_no'));
$smarty->assign('only_template',$_SESSION["user"]->hasRestriction('only_template'));
$smarty->assign('home_improv',$_SESSION["user"]->hasRestriction('home_improv'));
$smarty->assign('print_inv',$_SESSION["user"]->hasRestriction('print_inv'));
$temp_cr_date = strtotime($data['cr_date']);
$temp_to_date = strtotime('08/31/2011');
if($temp_cr_date >= $temp_to_date)
	$print_inv_cr_date = true; 
else 
	$print_inv_cr_date = false;
$smarty->assign('print_inv_cr_date',$print_inv_cr_date);
$smarty->assign('workerid',$_SESSION["user"]->getWorkerID());

$show_gps_wo_stops = $_SESSION['user']->hasRestriction('show_gps_wo_stops');
$smarty->assign('show_gps_wo_stops',$show_gps_wo_stops);

$show_slab_on_hold = $_SESSION['user']->hasRestriction('show_slab_on_hold');
$smarty->assign('show_slab_on_hold',$show_slab_on_hold);

$ysalesman_clear = $_SESSION["user"]->hasRestriction('ysalesman_clear');
$smarty->assign('ysalesman_clear',$ysalesman_clear);
$smarty->assign('message',$_SESSION["user"]->hasRestriction('message'));
$smarty->assign('new_check_link',$_SESSION["user"]->hasRestriction('new_check_link'));
$smarty->assign('wo_unit_lock',$ordstatus->getOperationStatus('wo_unit_lock',$data['status']) & !$_SESSION['user']->is_Admin());
$smarty->assign('bool',array('No','Yes'));

$smarty->assign('crestimates',$crestimates);
$smarty->assign('crestimates_cnt',$crestimates_cnt);
$smarty->assign('crorders',$crorders);
$smarty->assign('crorders_cnt',$crorders_cnt);
$smarty->assign('crunits',$crunits);
$smarty->assign('crunits_cnt',$crunits_cnt);
$show_template = ($crunits_cnt>0 & $_SESSION['user']->hasRestriction('template_date'))?true:false;
$smarty->assign('show_temp_date',$show_template);
/*---------------------------------------------------------------------------------------------*/
/*
 *Load all the units status for the order
 */
$str='';
foreach($crunits as $k=>$unit){
    $_SESSION['user']->db->select("select * from dbo.unit_status_select() where u_status=1 and id_ord_unit=".$unit['id']."");
    $oun = $_SESSION['user']->db->fetchArray();
    if($_SESSION["user"]->db->numrows() > 0){
        $str .="<tr><td>&nbsp;&nbsp;&nbsp;<input type='radio' name='status' value=".$k."\>Unit: ".$oun['id_ord_unit']."</td></tr>";
        if((int)($oun['c_status'])==1){
            $str.= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Template:&nbsp;&nbsp; </td><td  align='right'>".$oun['c_name']."</td><td> START ".$oun['c_start_time']." </td></tr>";
        }elseif((int)($oun['c_status'])==2){
            $str.= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Template:&nbsp;&nbsp; </td><td  align='right'>".$oun['c_name']."</td><td> START ".$oun['c_start_time']." -END- ".$oun['c_fin_time']."</td></tr>";
        }

        if((int)($oun['p_status'])==1){
            $str.= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Production: </td><td  align='right'>".$oun['p_name']."</td><td> START ".$oun['p_start_time']."</td></tr>";
        }elseif((int)($oun['p_status'])==2){
            $str.= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Production: </td><td  align='right'>".$oun['p_name']."</td><td> START ".$oun['p_start_time']." -END- ".$oun['p_fin_time']."</td></tr>";
        }

        if((int)($oun['i_status'])==1){
            $str.= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Installation: </td><td  align='right'>".$oun['i_name']."</td><td> START ".$oun['i_start_time']."</td></tr>";
        }elseif((int)($oun['i_status'])==2){
            $str.= "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Installation: </td><td  align='right'>".$oun['i_name']."</td><td> START ".$oun['i_start_time']." -END- ".$oun['i_fin_time']."</td></tr>";
        }
    }
}

$smarty->assign('unit_status', $str);
/*---------------------------------------------------------------------------------------------*/

if ($ysalesman_clear && $crunits_cnt>0)
	{   //Get list of workers
		$_SESSION['user']->db->select('select w.id, w.fname+\' \'+w.lname as worker from worker w ' .
										'inner join worker_dep wd on wd.id_worker=w.id ' .
										'left join department d on wd.id_department=d.id  ' .
										'inner join [user] u on w.id=u.id_worker ' .
										'where wd.id_dep_location='.$loc['id'].' and d.yard_assign=1 and u.activated=1 order by worker');
       
       //$_SESSION['user']->db->select('select w.id, w.fname+\' \'+w.lname as worker from worker w inner join worker_dep wd on wd.id_worker=w.id where wd.id_dep_location='.$loc['id']);
       $worker_list = array('name'=>array(),'value'=>array());
       while($tab = $_SESSION['user']->db->fetchArray())
       		{
            	$worker_list['name'][] = $tab['worker'];        $worker_list['value'][] = $tab['id'];
              	$smarty->assign('worker_list',$worker_list);
       		}       
	}     
//print($ordstatus->getOperationStatus('loc_price',$data['status']));
//Getting information about templater
if ($templater_in_wo)
	{
       if(is_numeric($_REQUEST['id'])) {
	   		$_SESSION['user']->db->select('select ot.[order],pw.id,ot.daytime from ord_template ot left join pl_worker pw on pw.id=ot.id_templater where id_order='.$_REQUEST['id']);
       		$wo_info = $_SESSION['user']->db->fetchArray();
	   }
       //Get list of templaters
       $_SESSION['user']->db->select('select pw.id,w.lname+\' \'+w.fname as templater,REPLACE(w.cell,\'-\',\'\') as cell from pl_worker pw inner join worker w on w.id=pw.id_worker where position = 1 and (pw.active=\'1\' or pw.id=\''.(is_numeric($wo_info['id'])?$wo_info['id']:0).'\') and id_location='.$loc['id'].' order by w.lname');
       $templater_list = $_SESSION['user']->db->fetchAllArrays();

       //print_r($wo_info);
       $smarty->assign('templater_in_wo',true);
       $smarty->assign('templater_list',$templater_list);
       $smarty->assign('templater_in_wo_worker',$wo_info['id']);
       $smarty->assign('ord_template_order',$wo_info['order']);
	   $smarty->assign('ord_template_daytime',$wo_info['daytime']);
	}
//Build submenu
if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $editform)
	{ 
    	  $submenu = array();
		  $submenu[] = array('name'=>'Get directions','link'=>'#" onClick="javascript:window.open(\'print_directions.php?id=0&oid='.$_REQUEST['id'].'&worker=-1&pid=-1&t=0&o=0&np=0&dt=\',\'wo_history\',\'toolbar=1,menu=0,scrollbars=1,resizable=0\');');
          $submenu[]=array('name'=>'Scan Files','link'=>'ord_drawing.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account'].'&new=1');
          if ($unit_new)  $submenu[]=array('name'=>'New unit','link'=>'ord_unitedit.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
          $submenu[]=array('name'=>'Units','link'=>'ord_unit.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
          if ($ordstatus->getOperationStatus('wo_template',$data['status']) && $_SESSION['user']->getScriptPrivilege('ord_template.php','select') && $show_template) $submenu[]=array('name'=>'Templates','link'=>'ord_template.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
          if ($wo_installation && $crunits_cnt>0)
          	{
             	// if ($_SESSION['user']->getScriptPrivilege('ord_installation.php','select')) $submenu[]=array('name'=>'Installations','link'=>'ord_installation.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
              	if ($_SESSION['user']->getScriptPrivilege('ord_installation.php','insert')) $submenu[]=array('name'=>'Installations','link'=>'ord_installationedit.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
           	}  
          if ($payment) $submenu[]=array('name'=>'Payments','link'=>'opaymentlist.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
          if ($crunits_cnt>0)
          	{
               //if ($_SESSION['user']->getScriptPrivilege('service.php','select')) $submenu[] = array('name'=>'Services','link'=>'service.php?oid='.$_REQUEST['id'].'&id_account='.$data['id_account']);
               if ($_SESSION['user']->getScriptPrivilege('service.php','insert')) $submenu[] = array('name'=>'Services','link'=>'serviceedit.php?oid='.$_REQUEST['id'].'&aid='.$data['id_account']);
           	}
          $submenu[]=array('name'=>'Follow up','link'=>'ord_followupedit.php?oid='.$_REQUEST['id'].'&aid='.$data['id_account']);
          $c = $ordstatus->getOperationStatus('loc_price',$data['status']);
          if (is_numeric($data['id_estimate'])) $c = false;
          if($c) $submenu[]=array('name'=>'Prices on different locations',link=>'#" onClick="javascript:window.open(\'ord_locprice.php?oid='.$data['id'].'\',\'price_show\',\'width=600,height=200,left=100,top=100,scrollbars=1,toolbar=no,menu=no\');');
          if ($_SESSION["user"]->getPrivilege('history'))
          $submenu[] = array('name'=>'WO history','link'=>'#" onClick="javascript:window.open(\'history_v2.php?tab=order&id='.$_REQUEST['id'].'&n=1\',\'wo_history\',\'toolbar=1,menu=0,scrollbars=1,resizable=1\');');
          $submenu[] = array('name'=>'Make new WO','link'=>'orderedit.php?zu='.$_GET['id'].'&id_account='.$data['id_account']);
	      $submenu[]=array('name'=>'Unit Details',link=>'#" onClick="javascript:window.open(\'print_unit_forman.php?id='.$data['id'].'\',\'price_show\',\'width=600,height=500,left=100,top=100,scrollbars=1,toolbar=no,menu=no\');');
		  $submenu[]=array('name'=>'Email Pictures',link=>'#" onClick="javascript:window.open(\'send_pictures.php?oid='.$data['id'].'\',\'send_pics\',\'width=320,height=360,left=100,top=100,scrollbars=1,toolbar=no,menu=no\');');
    	  $smarty->assign('submenu',$submenu);
	}
// obsluga logow
if (isset($_GET['id']) && is_numeric($_GET['id']))
	{
		include 'objects/object.log.php';
		$log = new Log($_SESSION['user'],'order',$_REQUEST['id'],'orderedit',array(array('id',$_REQUEST["id"]),array('id_account',$_REQUEST["id_account"])));
		$log->logcmd = $_REQUEST['logcmd'];
		$log->SetTitle('Work Order logs');
		$smarty->assign('log',$log->Automate($_REQUEST,$_REQUEST['logid'],920));
		// obsluga logow
		
		// logi dodatkowe
		if(is_numeric($_REQUEST['id_account'])) {
			$log2 = new Log($_SESSION['user'],'account',0,'orderedit',array(array('id',$_REQUEST["id"]),array('id_account',$_REQUEST["id_account"])),'log_ACC');
			$log2->SetTitle('Account logs');
			$log2->SetInsertPermission(0);
			$log2->SetParentName('Account');
			$log2->actionLog = $_REQUEST['actionlog'];
			$log2->SetAddSQL("AND o.id=".$_REQUEST['id_account']."");
			$log2->logcmd = $_REQUEST['logcmd'];
			$smarty->assign('log_ACC',$log2->Automate($_REQUEST,$_GET['logid'],920));
		}
		
		if(is_numeric($_REQUEST['id_account']) && is_numeric($_REQUEST['id'])) {
			$log3 = new Log($_SESSION['user'],'estimate',0,'orderedit',array(array('id',$_REQUEST["id"]),array('id_account',$_REQUEST["id_account"])),'log_EST');
			$log3->SetTitle('Assigned Estimate logs');
			$log3->SetInsertPermission(0);
			$log3->SetParentName('Estimate');
			$log3->actionLog = $_REQUEST['actionlog'];
			$log3->SetAddCOLS("o.id_account");
			$log3->SetAddSQL("AND id_account=".$_REQUEST['id_account']." AND o.id IN (SELECT id_estimate from [order] WHERE [order].id=".$_REQUEST['id'].")");
			$log3->logcmd = $_REQUEST['logcmd'];
			$smarty->assign('log_EST',$log3->Automate($_REQUEST,$_GET['logid'],920));
		}
		
		if(is_numeric($_REQUEST['id'])) {
			$log4 = new Log($_SESSION['user'],'ord_unit',0,'orderedit',array(array('id',$_REQUEST["id"]),array('id_account',$_REQUEST["id_account"])),'log_UNIT');
			$log4->SetTitle('Unit logs');
			$log4->SetInsertPermission(0);
			$log4->SetParentName('Unit');
			$log3->SetAddCOLS("o.id_order");
			$log4->SetAddSQL("AND o.id_order=".$_REQUEST["id"]."");
			$log4->actionLog = $_REQUEST['actionlog'];
			$log4->logcmd = $_REQUEST['logcmd'];
			$smarty->assign('log_UNIT',$log4->Automate($_REQUEST,$_GET['logid'],920));
		}
		// logi dodatkowe
	}
//Flags
if (isset($data['id_account']) && is_numeric($data['id_account']))
	{
    	include('objects/object.flag.php');
    	$flag = new Flag($_SESSION['user'],$data['id_account']);
    	$smarty->assign('flag',$flag->DisplayFlag());
	}
	
//Schedule Info for Install and Template
if(is_numeric($_REQUEST['id'])) {
	$_SESSION['user']->db->select("select [order],temp_time,temp_time_to from ord_template where id_order=".$_REQUEST['id']);
	$temp_schedule_info = $_SESSION['user']->db->fetchArray();
	$_SESSION['user']->db->select("select [order],inst_time,inst_time_to from ord_installation where id_order=".$_REQUEST['id']);
	$inst_schedule_info = $_SESSION['user']->db->fetchArray();
}
$smarty->assign('temp_schedule_info',$temp_schedule_info);
$smarty->assign('inst_schedule_info',$inst_schedule_info);
//End Schedule Info

//Schedule Info for Install and Template
  //Search for created installations
if(is_numeric($_REQUEST['id'])) {
  $_SESSION["user"]->db->select("SELECT top 1 oi.id, oi.inst_date,oi.message,oi.m_voicemail from ord_installation oi
                                 left join pl_worker on oi.id_installer=pl_worker.id
                                 left join worker w on pl_worker.id_worker=w.id 
                                 where id_order=".$_REQUEST['id']."
                                 order by oi.cr_date desc");
  $crinstall = $_SESSION["user"]->db->fetchArray();
  
  //Search for created templates
  $_SESSION["user"]->db->select("SELECT top 1 ot.id, ot.temp_date,ot.message,ot.m_voicemail from ord_template ot
                                 left join pl_worker on ot.id_templater=pl_worker.id
                                 left join worker w on pl_worker.id_worker=w.id
                                 where id_order=".$_REQUEST['id']." 
								 order by ot.cr_date desc");
  $crtemplate = $_SESSION["user"]->db->fetchArray();
}
  $smarty->assign('crtemplate',$crtemplate);
  $smarty->assign('crinstall',$crinstall);
  $smarty->assign('today',date("m/d/Y"));
//End Schedule Info

//get in info about stops
if($show_gps_wo_stops) {
	if(is_numeric($_REQUEST['id'])) {
		$_SESSION['user']->db->select("select * from gps_wo_stops where id_order=".$_REQUEST['id']." order by stop_date,cast(stop_time as datetime)");
		$gps_stops = $_SESSION['user']->db->fetchAllArrays();
	}
	$smarty->assign('gps_stops',$gps_stops);
}
//end info

//get in info about slabs on hold
if(is_numeric($_REQUEST['id'])) {
	$_SESSION['user']->db->select("select * from ord_slab_onhold where id_order=".$_REQUEST['id']);
	$slab_on_hold = $_SESSION['user']->db->fetchAllArrays();
}
$smarty->assign('slab_on_hold_data',$slab_on_hold);
//end info

//get sch_notes
$_SESSION['user']->db->select("select note from schedule_notes where day='".date("m/d/Y")."' and id_location = ".$loc['id']);
$sch_notes = $_SESSION['user']->db->fetchArray();
$_SESSION['user']->db->select("select note from schedule_notes where day='".date("m/d/Y", strtotime("+1 day"))."' and id_location = ".$loc['id']);
$sch_notes_tomorrow = $_SESSION['user']->db->fetchArray();
$smarty->assign('sch_notes', trim($sch_notes['note']));
$smarty->assign('sch_notes_tomorrow', trim($sch_notes_tomorrow['note']));
//end sch_notes

$comp_name = explode(".", gethostbyaddr($_SERVER['REMOTE_ADDR']));
$comp_name = strtolower($comp_name[0]);
$smarty->assign('comp_name',$comp_name);

$smarty->display('orderedit.tpl');
?>