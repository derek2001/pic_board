<?php
include_once ("object.user.php");

class PrintOrder
{
 //private:
   private $owner;
   private $PREFIX;
   private $error;
   private $user;
   private $uid;                     //Unit ID
   private $iid;                     //Installation ID
   private $location;
   private $printID;
 //public:
   public function PrintOrder(User &$usr,$printID)
   {
          $this->PREFIX = 'ord';   $this->user = $usr; $this->printID = is_numeric($printID)?$printID:0;
          $this->location = $this->user->getLocationProfile();  $this->uid = 0;  $this->iid = 0;
   }
   //----------------------------------------------------------------------------------------------------------------------------------
   public function PrintOnlyOneUnit($uID)
   {
          if (is_numeric($uID)) $this->uid = $uID;
   }
   //----------------------------------------------------------------------------------------------------------------------------------
   public function PrintForOneInstallation($iID)
   {
          if (is_numeric($iID)) $this->iid = $iID;
   }
   //----------------------------------------------------------------------------------------------------------------------------------
   public function GetDataService($id)
    {
       if (!isset($id) || !is_numeric($id)) { $this->error = 'Service ID must be specified.'; return false; }
       $sql = ' select s.*,sc.name as car_name,w.fname+\' \'+w.lname as s_name from service s left join subcategory sc on sc.id=s.id_subcategory
                 left join pl_worker pl on pl.id=s.id_servicer left join worker w on w.id=pl.id_worker WHERE s.id ='.$id;
                 
       $_SESSION['user']->db->select($sql);
       $tab = $_SESSION['user']->db->fetchArray();
   
       $sql = ' select sd.* from service s inner join service_drawing sd ON s.id = sd.id_service where s.id='.$id;
       $_SESSION['user']->db->select($sql);
       $draw = $_SESSION['user']->db->fetchAllArrays();
       $tab['drawing']  = $draw;
       $tab['draw_count'] = is_array($draw)?count($draw):0;
      
    return $tab;
    }


  //----------------------------------------------------------------------------------------------------------------------------------
   public function GetData($oid /*order ID*/, $only_templates = false,$pictures = false,$force_pictures = false, $is_service = false)
   {
//print('asdfasdf');
   	if (!isset($oid) || !is_numeric($oid)) { $this->error = 'Work Order ID must be specified.'; return false; }
        
    $close_check = "select status from [order] where id = ".$oid;
    $_SESSION['user']->db->select($close_check);
    $close_check = $_SESSION['user']->db->fetchArray();
    if(($close_check[0] == 10 && !$_SESSION['user']->is_Admin() && !$is_service)) { 
        if ($_SESSION['user']->getWorkerID() == 394 || $_SESSION['user']->getWorkerID() == 275 || $_SESSION['user']->getWorkerID() == 568
            || $_SESSION['user']->getWorkerID() == 433 || $_SESSION['user']->getWorkerID() == 82) $donothing = null;
        else { $this->error = 'Word Order closed. Please contact the administrator.'; return false; }
    }
          
       //Note printing
       if ($this->printID>0)
       {
           include_once('object.printsave.php');
           $note = new PrintSave($_SESSION['user']);
           $note->SaveUser($_GET['oid'],$this->printID);
       }
    
          $data = array(); 
          //Get information about checks
       $_SESSION['user']->db->select('select op.*,c.*,wrf.fname+\' \'+wrf.lname as refund_worker,
       a.name as a_name,a.company_name,a.address,a.town,a.state,a.zip,a.phone
       from ord_payment op
       inner join [check] c on c.id=op.id_check
       inner join account a on a.id=c.id_account
       left join worker wrf on wrf.id=c.refund_user
       where op.id_order='.$oid.'
       ORDER BY op.p_date');

       $data['check'] = $_SESSION['user']->db->fetchAllArrays();
       if (!is_array($data['check'])) $data['check']=array();//die(set_error('There were no payments made for that order.'));
     // var_dump($data);  
       //Get WO
       $_SESSION['user']->db->select('select o.*,o.cust_price as custom,ws.fname+\' \'+ws.lname as ysalesman,a.name as a_name, a.company_name, a.address as a_address,a.town as a_town,
       a.state as a_state, a.zip as a_zip,a.phone as a_phone, a.fax as a_fax,a.is_contractor,a.resale,a.resale_date,pl.name as pay_level,pl.dep_required,
       w.fname+\' \'+w.lname as cr_name from [order] o inner join account a on a.id=o.id_account
       inner join pay_level pl on pl.id=a.id_pay_level inner join worker w on w.id=w.cr_user left join worker ws on ws.id=o.yard_salesman where o.id=\''.$oid.'\'');
       $data['order'] = $_SESSION['user']->db->fetchArray();
       if (is_array($data['order'])) {
         $data['order']['cr_date'] = date('m/d/Y',strtotime($data['order']['cr_date']));
		 if($data['order']['resale'] != '0') $data['order']['resale_date'] = date('m/d/Y',strtotime($data['order']['resale_date']));
         //get information about last update
         $_SESSION['user']->db->select('select top 1 * from order_history ('.$oid.') order by change_date desc');
         $hist=$_SESSION['user']->db->fetchArray();
         if (is_array($hist)) { $data['order']['lm_date'] = @date('m/d/Y',strtotime($hist['lm_date'])); $data['order']['lm_name'] = $hist['lm_name']; }
       
         if ($data['order']['pick_up'] == 1)
         {
          // $_SESSION['user']->db->select('select zip from location where id=\''.$data['order']['id_location'].'\'');
         //  $lzip = $_SESSION['user']->db->fetchArray();
        //   $_SESSION['user']->db->select('select tax_level*100 as tax_level from zip z inner join tax_district t on t.id=z.id_tax_district where zip_code = \''.$lzip['zip'].'\'');
           //$tlevel = $_SESSION['user']->db->fetchArray();
          // $data['order']['tax_level'] = $tlevel['tax_level']; 
		    $data['order']['distance'] = 0;
          
		 //  if ($data['order']['close_date']!=NULL and date('m/d/Y',strtotime($data['order']['close_date'])) <'07/15/2006')  
		//   { $data['order']['tax_level']= 6.0; }
		 }
       }

       //get units, edges, slabs & cutouts
       $_SESSION['user']->db->select('select ou.*,us.name as status_name,w.fname+\' \'+w.lname as cr_name,a.name as app_name
       from [ord_unit] ou
       inner join application a on ou.id_application=a.id
       left join unit_status us on us.value=ou.status
       left join worker w on w.id=ou.cr_user
       where ou.id_order=\''.$oid.'\'');

       $data['unit'] = $_SESSION['user']->db->fetchAllArrays(); $unit_list = ''; 
       
       if (is_array($data['unit'])) //die(set_error('No units were defined for specified order.'));
       {
         //Add aditional price
         $oucnt = count($data['unit']);
         if ($oucnt>0) $data['order']['additional_per_unit'] = is_numeric($data['order']['addition'])?$data['order']['addition']/$oucnt:0; else $data['order']['additional_per_unit'] = 0;
         
         $unit_pos = array(); 
         foreach($data['unit'] as $k=>$v) 
         {
                if($k>0) $unit_list.=','; $unit_list.=$v['id']; $unit_pos[$v['id']] = $k; $data['unit'][$k]['l_edge'] = $v['edge']; $data['unit'][$k]['edge'] = array();
                $data['unit'][$k]['shape'] = array();$data['unit'][$k]['cutout'] = array(); $data['unit'][$k]['slab'] = array();$data['unit'][$k]['inst'] = array();
                $data['unit'][$k]['temp'] = array(); $data['unit'][$k]['drawing_cnt']=0;
                $data['unit'][$k]['inst_price'] = 0; $data['unit'][$k]['temp_price'] = 0; 
         }

         //Read backsplash height
         $_SESSION['user']->db->select('select id_ord_unit,max(b1_height) as max1,max(b2_height) as max2,max(b3_height) as max3,max(b4_height) as max4,sum(r41p+r12p+r23p+r34p) as radius
         from ord_rectangle
         where id_ord_unit IN ('.$unit_list.')
         group by id_ord_unit');
         $b_height = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($b_height))
           foreach($b_height as $v) 
           {
                  $data['unit'][$unit_pos[$v['id_ord_unit']]]['backsplash_height'] = max($v['max1'],$v['max2'],$v['max3'],$v['max4']);
                  $data['unit'][$unit_pos[$v['id_ord_unit']]]['custom'] = $v['radius'];
           }
        
         //slabs
         $_SESSION['user']->db->select('select os.*,sl.length,sl.width,st.name as stone_type,f.sign,s.id as stone_id,s.name as st_name,t.word as thickness,sf.slab_sample
         from [ord_slab] os
         inner join slab_frame sf on sf.id=os.id_slab_frame 
         inner join frame f on f.id=sf.id_frame
         inner join slab sl on sf.id_slab=sl.id
         inner join thickness t on t.id=sl.id_thickness
         inner join stone s on s.id=sl.id_stone 
         inner join stone_type st on st.id=s.id_stone_type where id_ord_unit IN ('.$unit_list.')');
         $ord_slab = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_slab))
           foreach($ord_slab as $v) $data['unit'][$unit_pos[$v['id_ord_unit']]]['slab'] = $v;

         //unit status
         $_SESSION['user']->db->select('select os.id_ord_unit, o.status as status
         from [ord_slab] os
         inner join [ord_unit] ou on ou.id = os.id_ord_unit
         inner join [order] o on ou.id_order = o.id
         left join [ord_status] osts on o.status=osts.value
         where os.id_ord_unit IN ('.$unit_list.')');
         $ord_slab = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_slab))
            foreach($ord_slab as $v) $data['unit'][$unit_pos[$v['id_ord_unit']]]['status'] = $v;

         //picture status
         $_SESSION['user']->db->select('select os.id_ord_unit, sf.id_slab as id, s.id_stone,
         (select count(sia.type) from [stone_image] sia
			where sia.id_slab = sf.id_slab and sia.id_stone = s.id_stone and sia.type = 0)as cnt_raw,
         (select count(sib.type) from [stone_image] sib
			where sib.id_slab = sf.id_slab and sib.id_stone = s.id_stone and sib.type = 2)as cnt_erp,
         (select count(sic.type) from [stone_image] sic
			where sic.id_slab = sf.id_slab and sic.id_stone = s.id_stone and sic.type = 3)as cnt_www,
	     (select count(sid.type) from [stone_image] sid
			where sid.id_slab = sf.id_slab and sid.id_stone = s.id_stone and sid.type = 1)as cnt_jpg,
		 (select pict1 from [stone_drawing] sda
			where sda.id_slab = sf.id_slab and sda.id_stone = s.id_stone )as www_pc1,
		 (select pict2 from [stone_drawing] sdb
			where sdb.id_slab = sf.id_slab and sdb.id_stone = s.id_stone )as www_pc2,
		 (select pict3 from [stone_drawing] sdc
			where sdc.id_slab = sf.id_slab and sdc.id_stone = s.id_stone )as www_pc3
         from [ord_slab] os
         inner join [slab_frame] sf on sf.id = os.id_slab_frame
         inner join [stone_image] si on si.id_slab_frame = sf.id
		 inner join [slab] s on s.id = sf.id_slab
         where os.id_ord_unit IN ('.$unit_list.')
         group by sf.id_slab, os.id_ord_unit, s.id_stone');
         $cnt_raw = $_SESSION['user']->db->fetchAllArrays();
         if(is_array($cnt_raw))
            foreach($cnt_raw as $v) $data['unit'][$unit_pos[$v['id_ord_unit']]]['cnt_pic'] = $v;

         //edges
         $_SESSION['user']->db->select('select oe.*,0 as e_status,e.name,e.picture from [ord_edge] oe inner join edge e on e.id=oe.id_edge where oe.id_ord_unit IN ('.$unit_list.') and oe.length>0');
         $ord_edge = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_edge))
          foreach($ord_edge as $v) $data['unit'][$unit_pos[$v['id_ord_unit']]]['edge'][count($data['unit'][$unit_pos[$v['id_ord_unit']]]['edge'])] = $v;
         //cutouts
         $_SESSION['user']->db->select('select ec.*,st.name from [ord_cutout] ec inner join sink_type st on st.id=ec.id_sink_type where ec.id_ord_unit IN ('.$unit_list.')');
         $ord_cutout = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_cutout))
          foreach($ord_cutout as $v) { $v['cprice']=$v['price']*$v['quantity']; $data['unit'][$unit_pos[$v['id_ord_unit']]]['cutout'][count($data['unit'][$unit_pos[$v['id_ord_unit']]]['cutout'])] = $v; }
         //rectangles
         $_SESSION['user']->db->select('select * from [ord_rectangle] where id_ord_unit IN ('.$unit_list.')');
         $ord_rect = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_rect))
         {
          $fieldScan = array(array('id'=>'id_edge1','value'=>0x01),array('id'=>'id_b1_et','value'=>0x02),array('id'=>'id_b1_es','value'=>0x02),
                             array('id'=>'id_edge2','value'=>0x01),array('id'=>'id_b2_et','value'=>0x02),array('id'=>'id_b2_es','value'=>0x02),
                             array('id'=>'id_edge3','value'=>0x01),
                             array('id'=>'id_edge4','value'=>0x01),array('id'=>'id_b4_et','value'=>0x02),array('id'=>'id_b4_es','value'=>0x02)
                            );
          foreach($ord_rect as $v) 
          {
                   //Match edges (assign status)
                   foreach($data['unit'][$unit_pos[$v['id_ord_unit']]]['edge'] as $kedge=>$vedge)
                     foreach($fieldScan as $vs)
                      if (is_numeric($v[$vs['id']]) && $v[$vs['id']]!=0) 
                         if ($vedge['id_edge']==$v[$vs['id']]) { $data['unit'][$unit_pos[$v['id_ord_unit']]]['edge'][$kedge]['e_status'] |= $vs['value'];  }
  
                   $data['unit'][$unit_pos[$v['id_ord_unit']]]['rect'][count($data['unit'][$unit_pos[$v['id_ord_unit']]]['rect'])] = $v;
          }
         }        
         //cust shapes
         $_SESSION['user']->db->select('select os.*,cs.name from [ord_shape] os inner join custom_shape cs on cs.id=os.id_custom_shape where os.id_ord_unit IN ('.$unit_list.')');
         $ord_shape = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_shape))
          foreach($ord_shape as $v) { $v['sprice']=$v['price']*$v['shape_nbr']; $data['unit'][$unit_pos[$v['id_ord_unit']]]['shape'][count($data['unit'][$unit_pos[$v['id_ord_unit']]]['shape'])] = $v; }
         
         //Cutters
         $_SESSION['user']->db->select('select oc.*,oc.id_cutter,w.fname+\' \'+w.lname as c_name from [ord_cutter] oc inner join pl_worker pw on pw.id=oc.id_cutter inner join worker w on w.id=pw.id_worker where oc.id_ord_unit IN ('.$unit_list.') order by oc.id_ord_unit');
         $ord_cutter = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_cutter))
          foreach($ord_cutter as $v) { $data['unit'][$unit_pos[$v['id_ord_unit']]]['cutter'][] = $v; }
         //transactions
         $_SESSION['user']->db->select('select * from [ord_payment] where id_order=\''.$oid.'\'');
         $data['payment'] = $_SESSION['user']->db->fetchAllArrays();
         //installations
         $_SESSION['user']->db->select('select oi.*,ou.id as id_unit,w.fname+\' \'+w.lname as installer from ord_installation oi 
         inner join ord_unit ou on ou.id_ord_installation = oi.id left join pl_worker pw on pw.id=oi.id_installer
         left join worker w on w.id=pw.id_worker where oi.deleted=0 and oi.id_order='.$oid);
         $ord_inst = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_inst))
          foreach ($data['unit'] as $key => $value) 
            foreach($ord_inst as $v)
              if ($value['id'] == $v['id_unit'])
                      $data['unit'][$key]['inst'][count($data['unit'][$key]['inst'])] = $v;
         //template
         $_SESSION['user']->db->select('select oi.*,w.fname+\' \'+w.lname as templater from ord_template oi 
         left join pl_worker pw on pw.id=oi.id_templater left join worker w on w.id=pw.id_worker where oi.deleted=0 and oi.id_order='.$oid);
         $ord_temp = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_temp))
          foreach ($data['unit'] as $key => $value)
            foreach($ord_temp as $v) $data['unit'][$key]['temp'][count($data['unit'][$key]['temp'])] = $v;
       } else $data['order']['additional_per_unit'] = 0;
       //get information about current location
       //$_SESSION['user']->db->select('select * from location where id=\''.$this->location['id'].'\'');
       $_SESSION['user']->db->select('select * from location where id=\''.$data['order']['id_location'].'\'');
       $location = $_SESSION['user']->db->fetchArray();
       $data['location'] = $location;
       
       //products
       $_SESSION['user']->db->select($gg='select op.*,ou.name u_name,c.name c_name,pd.p1+\'-\'+ pit.item  p_name, cast(op.price as numeric)p from ord_product op 
        inner join ord_unit ou ON op.id_ord_unit=ou.id
        inner join product_item pit ON pit.id = op.idp
        inner join m_product mp ON mp.id =pit.id_product
        left join coupon c ON c.id=op.id_coupon
        inner join product_data pd ON pd.id = mp.id_dane  where op.id_order=\''.$oid.'\'');
		//print($gg);
		$data['product'] = $_SESSION['user']->db->fetchAllArrays();
       
       //count prices
       $data['order']['subtotal_price'] = $data['order']['additional_per_unit']>0?$data['order']['addition']:0; $c_price = 0;
       if (is_numeric($data['order']['custom'])) $data['order']['subtotal_price'] += $data['order']['custom'];
       if ($data['order']['h_improvement'] == 1) $data['order']['tax_level']=0;
       
       if (is_array($data['unit']))
          foreach($data['unit'] as $k=>$val)
          {
           $iprice = 0; $tprice=0;
           //price for edges
           $eprice = 0; if (is_array($val['edge']))foreach($val['edge'] as $ev) $eprice += $ev['price']*$ev['length'];
           $data['unit'][$k]['eprice'] = $eprice;
           $data['order']['subtotal_price'] += $eprice;
           //price for custom shapes
           $sprice = 0; if (is_array($val['shape'])) foreach($val['shape'] as $ev) $sprice += $ev['sprice'];
           //$data['unit'][$k]['sprice'] = $sprice;
           //$data['order']['subtotal_price'] += $sprice;
           //price for custom & honing
           if (!is_numeric($data['unit'][$k]['custom'])) $data['unit'][$k]['custom']=0;
           $data['unit'][$k]['custom'] += $sprice+$val['cust_price']+$val['h_price']*$val['honing']*$val['area'];
		   
		   $honing[$data['unit'][$k]['id']]=$val['h_price']*$val['honing']*$val['area'];
           $data['order']['subtotal_price'] += $data['unit'][$k]['custom'];
           $c_price += $data['unit'][$k]['custom'];
           //price for cutout
           $cprice = 0; if (is_array($val['cutout'])) foreach($val['cutout'] as $ev) $cprice += $ev['price']*$ev['quantity'];
           $data['unit'][$k]['cutout_price'] = $cprice; 
           $data['order']['subtotal_price'] += $cprice;
             
           //print($data['order']['subtotal_price'].'---')  ;
	       //price for template and installation
           
           if ($data['order']['no_template']==0 && $val['area']>0)
           {
             $_SESSION['user']->db->select('SELECT top 1 e_nbr*t_time as tprice FROM ord_template where id_order='.$oid.' order by id desc');
             $temp = $_SESSION['user']->db->fetchArray();
             if (is_numeric($temp['tprice'])) { $data['unit'][$k]['temp_price'] = $data['order']['worker_cost']*$temp['tprice']; $tprice += $data['unit'][$k]['temp_price']; if ($data['order']['area']>0) if ($k==0) $data['order']['subtotal_price']+=$data['unit'][$k]['temp_price']; }
           } else $data['unit'][$k]['temp_price'] = 0;
           //Material price
           if ($data['order']['pick_up'] == 1)
           {
             $mprice = 0; 
             $mprice += $data['order']['area_coef']*$val['area']*$val['slab']['sqft_price']*(100+$val['slab']['pickup_profit']+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100+$val['slab']['t_price']+$val['slab']['o_price']+$val['slab']['waste_sqft']*$val['slab']['sqft_price']*(100+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100;
             $data['unit'][$k]['mprice'] = $mprice;
             $data['order']['subtotal_price'] += $mprice+$data['unit'][$k]['bsp_price'];
             //Backsplash price
             $data['unit'][$k]['price']['backsplash_price'] = $data['order']['area_coef']*$val['backsplash']*$val['slab']['sqft_price']*(100+$val['slab']['pickup_profit']+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100+$val['slab']['t_price']+$val['slab']['o_price']+$val['slab']['waste_sqft']*$val['slab']['sqft_price']*(100+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100;
             $data['unit'][$k]['inst_price'] = 0;
           } else
             {
                $mprice = 0;
                $mprice += $data['order']['area_coef']*$val['area']*$val['slab']['sqft_price']*(100+$val['slab']['profit']+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100+$val['slab']['t_price']+$val['slab']['o_price']+$val['slab']['waste_sqft']*$val['slab']['sqft_price']*(100+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100;
                $data['unit'][$k]['mprice'] = $mprice;
                $data['order']['subtotal_price'] += $mprice+$data['unit'][$k]['bsp_price'];
               // print($data['order']['subtotal_price'].'---') ;
                $data['unit'][$k]['price']['backsplash_price'] = $data['order']['area_coef']*$val['backsplash']*$val['slab']['sqft_price']*(100+$val['slab']['profit']+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100+$val['slab']['t_price']+$val['slab']['o_price']+$val['slab']['waste_sqft']*$val['slab']['sqft_price']*(100+$val['slab']['m_quality']+$val['slab']['cutting_risk']+$val['slab']['severity'])/100; 
       
                if ($val['area']>0)
                {
                   $_SESSION['user']->db->select('SELECT '.$data['order']['worker_cost'].'*sum((e_nbr+extra_help)*i_time) as iprice FROM ord_installation where id_order='.$oid);
                   $temp = $_SESSION['user']->db->fetchArray();
                   if (is_numeric($temp['iprice'])) 
                   { $data['unit'][$k]['inst_price'] = $temp['iprice']; 
                   	 $iprice += $data['unit'][$k]['inst_price']; 
                   	 if ($data['order']['area']>0) if ($k==0) $data['order']['subtotal_price']+=$data['unit'][$k]['inst_price']; }
                }
             }
           $data['unit'][$k]['uprice'] = $data['unit'][$k]['eprice']+/*$data['unit'][$k]['csprice']+*/$data['unit'][$k]['mprice']+$data['unit'][$k]['cprice']+$data['unit'][$k]['sprice']+$data['order']['additional_per_unit'];
          }
       $c_price += $data['order']['cust_price'];
       //Get cost for floors
       $_SESSION['user']->db->select('SELECT sum(floor_cost) as f_cost FROM ord_installation WHERE id_order=\''.$oid.'\'');
       $f_cost = $_SESSION['user']->db->fetchArray();
       if (is_numeric($f_cost['f_cost'])) { $iprice += $f_cost['f_cost']; $data['order']['subtotal_price'] += $f_cost['f_cost']; }

       //load disclaimers
       if (isset($_GET['disc']) && $_GET['disc']=='1')
       {
         $_SESSION['user']->db->select('select name,content from disclaimer where name=\'order\'');
         $discl = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($discl)) foreach($discl as $v) { $disclaimer[$v['name']] = $v['content']; }
       }
       //print_r($data['product']);
       $data['product_price'] = 0;
       if (is_array($data['product']))
	    foreach ($data['product'] as $pval) 
		{
		$data['product_price'] += $pval['price']*$pval['quantity'];
        }
	   $data['order']['subtotal_price'] += $data['product_price'];
       
       $zone_charge = is_numeric($data['order']['zone_charge'])?$data['order']['zone_charge']/2.0:0;
       $iiprice = 0;
       if ($data['order']['pick_up'] == 0)
       {
         $_SESSION['user']->db->select('SELECT count(*) as ocnt FROM ord_installation WHERE id_order='.$oid);
         $cnt = $_SESSION['user']->db->fetchArray();
         if (is_numeric($cnt['ocnt'])) 
         	{ 
                  	$iiprice = 2*$cnt['ocnt']*$data['order']['distance']*$data['order']['cost_per_mile']*$data['order']['zone_coef']+$zone_charge; 
                  	$iprice += $iiprice; 
                  	if ($data['order']['area']>0) $data['order']['subtotal_price'] += $iiprice;
            }
       }
       
       $ttprice = 0;
       if ($data['order']['no_template'] == 0)
       {
         $_SESSION['user']->db->select('SELECT count(*) as tcnt FROM ord_template WHERE id_order='.$oid);
         $cnt = $_SESSION['user']->db->fetchArray();
         if (is_numeric($cnt['tcnt'])) 
         	{ 
         		$ttprice = 2*$cnt['tcnt']*$data['order']['distance']*$data['order']['cost_per_mile']*$data['order']['zone_coef']+$zone_charge; 
         		$tprice += $ttprice; 
         		if ($data['order']['area']>0) $data['order']['subtotal_price'] += $ttprice;
         	}
       }
      
       //Consider resale
       if ($data['order']['resale']==1 && strtotime($data['order']['resale_date']) <= strtotime($data['order']['cr_date']))	$TaxLevel = 0; 	
	   else $TaxLevel = $data['order']['tax_level'];
      
       //$data['order']['subtotal_price_print']=$data['order']['subtotal_price'];
//------------------------------------------------------------       //poprawka podatk�w na NY i poprawka dla PA
      if ($data['order']['area']==0)
       {
       	$tprice=0;$iprice=0;
       }
       
       if ( ($data['order']['pick_up'] == 0)  &&(($data['order']['state']=='NY' && $data['order']['add_sales_tax_ny']==0)  || ($data['order']['state']=='PA')))
       $TaxLevel=0;
       //if ($data['order']['state']=='PA') 
       	//{
       	//	$data['order']['subtotal_price_tax']=$data['order']['subtotal_price']-$tprice-$iprice;
      // print($data['order']['subtotal_price'].'---');
       	//}
//----------------------------------------------------------------------------------------------------------------
       //Get discount mode
       //$data['order']['discount'] = 0;  
       if (is_numeric($data['order']['discount'])) 
       {
           $_SESSION['user']->db->select('select num_val as mode from config_tab where name=\'show_discount\'');
           $data['order']['discount_mode'] = $_SESSION['user']->db->fetchArray();
		   $data['order']['discount_mode'] = is_numeric($data['order']['discount_mode']['mode'])?$data['order']['discount_mode']['mode']:0;
           $data['order']['discount_val'] = $data['order']['discount']*$data['order']['subtotal_price']/100; 
           if ($data['order']['discount_mode']==1) 
		   { 
		     $data['order']['subtotal_price'] -= $data['order']['discount_val']; 
		    
		         /*if ($data['order']['state']=='PA') 
			       	{
			       		$tax_price=$TaxLevel/100*($data['order']['subtotal_price']-$tprice-$iprice);
			       	   
			       	}
	           else
		           */
		         $tax_price=$TaxLevel/100*$data['order']['subtotal_price'];
		   }	
           else 
		     /* if ($data['order']['state']=='PA') 
			       	{
			       		$tax_price=$TaxLevel/100*($data['order']['subtotal_price']-$tprice-$iprice-$data['order']['discount_val']);
			       	   
			       	}
	           else
	    */
	                   $tax_price=$TaxLevel/100*($data['order']['subtotal_price']-$data['order']['discount_val']);
       } 
	   else 
	   {
	    $data['order']['discount_mode'] = 0; 
	    
	         /*  if ($data['order']['state']=='PA') 
			       	{
			       		$tax_price=$TaxLevel/100*($data['order']['subtotal_price']-$tprice-$iprice);
			       	   
			       	}
	           else
	    */
	              $tax_price=$TaxLevel/100*$data['order']['subtotal_price'];
	   }
       
	    $data['order']['tax_price'] = $tax_price;
       
       //Get unit price
       if (is_array($data['unit']))
       {
        $data['unit_cnt'] = count($data['unit']);
        if ($data['unit_cnt']==0) die('There need to be at least one unit defined.');
        
		foreach($data['unit'] as $k=>$v)
        {
            $data['unit'][$k]['price']['prod_price']=0;
			
			if (is_array($data['product'])) 
			foreach ($data['product'] as $pval) 
			 {
			   if ($pval['id_ord_unit']==$v['id'])
			   {
			      $data['unit'][$k]['price']['prod_price'] += $pval['price']*$pval['quantity'];
               }
       		 } 
			 
			 
			  if ($data['order']['area']==0)
              {
                $iiprice = 0; $ttprice = 0; $iprice=0;$tprice = 0;
                $data['unit'][$k]['inst_price'] = 0; $data['unit'][$k]['temp_price'] = 0;       
              }
              
              $data['unit'][$k]['default_edge']='';
              if (is_numeric($v['id_edge'])) foreach ($v['edge'] as $kedge=>$vedge) if ($vedge['id_edge']==$v['id_edge']) {$data['unit'][$k]['default_edge']=$kedge; break;}
              
              $coef = $v['area']>0?$v['backsplash']/$v['area']:0;
              $area_C = $data['order']['area']>0?round($v['area']/$data['order']['area'],4):0; 
              $data['unit'][$k]['price']['backsplash_price'] += $data['unit'][$k]['bsp_price'];
              $addition_price = $data['order']['addition']/$data['unit_cnt'];
              $data['unit'][$k]['price']['distance_price'] = ($iiprice+$ttprice)*$area_C;
              $data['unit'][$k]['price']['custom'] = $data['unit'][$k]['custom']+$data['order']['custom']/$data['unit_cnt'] -$honing[$data['unit'][$k]['id']];
             
             
            //  if ($data['order']['state']=='PA') 
            //  $data['unit'][$k]['price']['subtotal_price'] = $data['unit'][$k]['tot_price']+$data['order']['custom']/$data['unit_cnt']+$addition_price;
			//   else
			  $data['unit'][$k]['price']['subtotal_price'] = $data['unit'][$k]['tot_price']+$data['order']['custom']/$data['unit_cnt']+$addition_price+ $tprice*$area_C*($data['order']['no_template']==0?1:0)+$iprice*$area_C ;
			   //print($tprice.'----'.$area_C.'---------'.$iprice);
			//  print($area_C * ($tprice+$iprice).' areac<br> ');
			  $data['unit'][$k]['price']['subtotal_price_inv'] = $data['unit'][$k]['tot_price']+$data['order']['custom']/$data['unit_cnt']+$addition_price;
             // print($data['unit'][$k]['price']['subtotal_price_inv'].'--');
              if ($data['order']['state']=='PA') 
                 $data['unit'][$k]['price']['tax_price']=$TaxLevel/100*($data['unit'][$k]['price']['subtotal_price'] - ($tprice*$area_C*($data['order']['no_template']==0?1:0)+$iprice*$area_C )) ;
               else
              $data['unit'][$k]['price']['tax_price']=$TaxLevel/100*$data['unit'][$k]['price']['subtotal_price'];
           	  $data['unit'][$k]['price']['total_price'] = $data['unit'][$k]['price']['subtotal_price']+$data['unit'][$k]['price']['tax_price'];
              $data['unit'][$k]['price']['edge_price'] = 0;
              foreach($v['edge'] as $vv) $data['unit'][$k]['price']['edge_price'] += $vv['price']*$vv['length'];
              $data['unit'][$k]['price']['cutout_price'] = 0;
              foreach($v['cutout'] as $vv) $data['unit'][$k]['price']['cutout_price'] += $vv['price']*$vv['quantity'];
              $data['unit'][$k]['price']['surface_price'] = $data['unit'][$k]['price']['subtotal_price'] - $data['unit'][$k]['price']['backsplash_price'] - $data['unit'][$k]['price']['edge_price']-$data['unit'][$k]['price']['cutout_price']-$data['unit'][$k]['price']['custom']-$data['unit'][$k]['price']['prod_price'];
              
			  //Count total price for unit
                //  if ($data['order']['state']=='PA') 
                //$data['unit'][$k]['price']['total_invprice'] = $data['unit'][$k]['price']['subtotal_price'];
       			 // else
       			$data['unit'][$k]['price']['total_invprice'] = $data['unit'][$k]['price']['subtotal_price'];//-($data['unit'][$k]['inst_price'] + $data['unit'][$k]['temp_price'])/$data['unit_cnt'] ;//;- $data['unit'][$k]['price']['distance_price'] ;  //print('<br>'. $data['unit'][$k]['price']['subtotal_price']);
       			
       		   // $data['unit'][$k]['price']['total_invprice_print']	 = $data['unit'][$k]['price']['subtotal_price'];//$data['unit'][$k]['price']['total_invprice']+($data['unit'][$k]['inst_price'] + $data['unit'][$k]['temp_price'])/$data['unit_cnt'];
       		 		
       
     // print($data['unit'][$k]['price']['subtotal_price_inv'].'<br>');
     // print($iprice.'<br>');
     // print($tprice.'<br>');
	  //print( $data['unit'][$k]['price']['subtotal_price'].'sprice<br>');
	    } 
       }
         //  print($data['unit'][$k]['price']['total_invprice'].'----'.$data['unit'][$k]['price']['prod_price']);
       $data['order']['temp_price'] = $tprice;  $data['order']['inst_price'] = $iprice;
       
       //Don't show pictures if deposit is not present
       $show_pict = $force_pictures;
       if (is_numeric($data['order']['id']) && !$force_pictures)
       {
          $_SESSION['user']->db->select($yy='select dbo.canPrintOrder('.$data['order']['id'].',\''.date('m/d/Y',time()).'\') as can');
          //print($yy);
          $canp = $_SESSION['user']->db->fetchArray();
          $show_pict = $canp['can'];
       }
       
       //-----------------------------------Logs----------------------------------------
       $data['log'] = array(); 
       $_SESSION['user']->db->select('select l.*,fname+\' \'+lname as w_name FROM account_log l left join [worker] w on w.id=l.cr_user WHERE id_src=\''.$data['order']['id_account'].'\' order by l.cr_date desc');
       $data['log']['account']=$_SESSION['user']->db->fetchAllArrays();
       
       if(is_numeric($data['order']['id_estimate'])) {
           $_SESSION['user']->db->select('select l.*,fname+\' \'+lname as w_name FROM estimate_log l left join [worker] w on w.id=l.cr_user WHERE id_src=\''.$data['order']['id_estimate'].'\' order by l.cr_date desc');
           $data['log']['estimate']=$_SESSION['user']->db->fetchAllArrays();
       }
       if(is_numeric($data['order']['id_estimate'])) {
           $_SESSION['user']->db->select('select l.*,fname+\' \'+lname as w_name FROM est_unit_log l left join [worker] w on w.id=l.cr_user WHERE id_src IN (select id FROM est_unit WHERE id_estimate = \''.$data['order']['id_estimate'].'\') order by l.cr_date desc');
           $data['log']['est_unit']=$_SESSION['user']->db->fetchAllArrays();
       }
       $_SESSION['user']->db->select('select l.*,fname+\' \'+lname as w_name FROM order_log l left join [worker] w on w.id=l.cr_user WHERE id_src=\''.$data['order']['id'].'\' order by l.cr_date desc');
       $data['log']['wo'] = $_SESSION['user']->db->fetchAllArrays();
       
       $_SESSION['user']->db->select('select l.*,fname+\' \'+lname as w_name FROM ord_unit_log l left join [worker] w on w.id=l.cr_user WHERE id_src IN ('.$unit_list.') order by l.cr_date desc');
       $uulog = $_SESSION['user']->db->fetchAllArrays();
       if (is_array($uulog))
          foreach($uulog as $v) 
           foreach($data['unit'] as $kk => $vv)
             if ($vv['id']==$v['id_src']) { $data['unit'][$kk]['log'][] = $v; break; }

       //---------------------------------Pictures--------------------------------------
       if ($pictures && $show_pict)
       {
	   	 //Get order's pictures (after and before install)
         //$WIDTH = 480; $HEIGHT=336;
         $_SESSION['user']->db->select($dd='select * from ord_picture where id_order='.$data['order']['id']);
         //print($dd);
         $ord_pictures = $_SESSION['user']->db->fetchAllArrays();
		 if (is_array($ord_pictures))
          foreach($ord_pictures as $k=>$v)
           if (file_exists('upload/ord_picture/small/'.$v['id'].'_'.$v['picture']))
           {
             $size = getimagesize('upload/ord_picture/small/'.$v['id'].'_'.$v['picture']);
             if ($size[1]>$size[0])
             	$ord_pictures[$k]['format'] = "p";
			 else
			 	$ord_pictures[$k]['format'] = "l";
           }
         $data['order']['pictures'] = $ord_pictures; $data['order']['pictures_cnt'] = is_array($ord_pictures)?count($ord_pictures):0;
		 
		 
         //Get order's pictures
         $WIDTH = 670; $HEIGHT=830;
         $_SESSION['user']->db->select($dd='select * from ord_drawing where id_order='.$data['order']['id']);
         //print($dd);
         $ord_drawings = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($ord_drawings))
          foreach($ord_drawings as $k=>$v)
           if (file_exists('upload/order/'.$v['id'].'_'.$v['picture']))
           {
             $size = getimagesize('upload/order/'.$v['id'].'_'.$v['picture']);
             //if ($size[0]>$WIDTH) {$ord_drawings[$k]['w'] = $WIDTH; $aratio = $WIDTH/$size[0]; $ord_drawings[$k]['h'] = intval($size[1]*$aratio); }
             // else { 
             if ($size[0]>$WIDTH || $size[1]>$HEIGHT)
                  if ($size[0]>$WIDTH && $size[0]>$size[1])
                  {
                     $size[1] = intval($size[1]*$WIDTH/$size[0]); $size[0] = $WIDTH; 
                  }  else { $size[0] = intval($size[0]*$HEIGHT/$size[1]); $size[1] = $HEIGHT; }
             $ord_drawings[$k]['w'] = $size[0]; $ord_drawings[$k]['h'] = $size[1];
             //        }
           }
           
         $data['order']['drawing'] = $ord_drawings; $data['order']['drawing_cnt'] = is_array($ord_drawings)?count($ord_drawings):0;
         //Get estimate's pictures
         $_SESSION['user']->db->select('select * from est_drawing where id_estimate=(select id_estimate FROM [order] WHERE id = \''.$data['order']['id'].'\')');
         $est_drawings = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($est_drawings))
          foreach($est_drawings as $k=>$v)
           if (file_exists('upload/estimate/'.$v['id'].'_'.$v['picture']))
           {
             $size = getimagesize('upload/estimate/'.$v['id'].'_'.$v['picture']);
             if ($size[0]>$WIDTH || $size[1]>$HEIGHT)
                  if ($size[0]>$WIDTH && $size[0]>$size[1])
                  {
                     $size[1] = intval($size[1]*$WIDTH/$size[0]); $size[0] = $WIDTH; 
                  }  else { $size[0] = intval($size[0]*$HEIGHT/$size[1]); $size[1] = $HEIGHT; }
             //if ($size[0]>$WIDTH) {$est_drawings[$k]['w'] = $WIDTH; $aratio = $WIDTH/$size[0]; $est_drawings[$k]['h'] = intval($size[1]*$aratio); }
             // else { 
                     $est_drawings[$k]['w'] = $size[0]; $est_drawings[$k]['h'] = $size[1];
                     //}
           }
         $data['estimate']['drawing'] = $est_drawings; $data['estimate']['drawing_cnt'] = is_array($est_drawings)?count($est_drawings):0;
         //Get pictures from estimate unit's pictures
         $_SESSION['user']->db->select('select * from est_unit_drawing where id_est_unit IN (select id FROM est_unit WHERE id_estimate = (select id_estimate FROM [order] WHERE id = \''.$data['order']['id'].'\'))');
         $est_drawings = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($est_drawings))
          foreach($est_drawings as $k=>$v)
           if (file_exists('upload/est_unit/'.$v['id'].'_'.$v['picture']))
           {
             $size = getimagesize('upload/est_unit/'.$v['id'].'_'.$v['picture']);
             if ($size[0]>$WIDTH || $size[1]>$HEIGHT)
                  if ($size[0]>$WIDTH && $size[0]>$size[1])
                  {
                     $size[1] = intval($size[1]*$WIDTH/$size[0]); $size[0] = $WIDTH; 
                  }  else { $size[0] = intval($size[0]*$HEIGHT/$size[1]); $size[1] = $HEIGHT; }
             //if ($size[0]>$WIDTH) {$est_drawings[$k]['w'] = $WIDTH; $aratio = $WIDTH/$size[0]; $est_drawings[$k]['h'] = intval($size[1]*$aratio); }
             // else { 
             $est_drawings[$k]['w'] = $size[0]; $est_drawings[$k]['h'] = $size[1];
             //}
           }
         $data['estimate']['unit_drawing'] = $est_drawings; $data['estimate']['unit_drawing_cnt'] = is_array($est_drawings)?count($est_drawings):0;
         //Get unit's pictures
         $_SESSION['user']->db->select('select * from ord_unit_drawing where id_ord_unit IN ('.$unit_list.') ORDER BY id');
         $u_drawings = $_SESSION['user']->db->fetchAllArrays();
         if (is_array($u_drawings))
          foreach($u_drawings as $k=>$v)
           if (file_exists('upload/ord_unit/'.$v['id'].'_'.$v['picture']))
           {
             $size = getimagesize('upload/ord_unit/'.$v['id'].'_'.$v['picture']);
             if ($size[0]>$WIDTH || $size[1]>$HEIGHT)
                  if ($size[0]>$WIDTH && $size[0]>$size[1])
                  {
                     $size[1] = intval($size[1]*$WIDTH/$size[0]); $size[0] = $WIDTH; 
                  }  else { $size[0] = intval($size[0]*$HEIGHT/$size[1]); $size[1] = $HEIGHT; }
             //if ($size[0]>$WIDTH) {$u_drawings[$k]['w'] = $WIDTH; $aratio = $WIDTH/$size[0]; $u_drawings[$k]['h'] = intval($size[1]*$aratio); }
             // else { 
             $u_drawings[$k]['w'] = $size[0]; $u_drawings[$k]['h'] = $size[1];
             //}
             foreach ($data['unit'] as $kk=>$vv) if ($vv['id'] == $v['id_ord_unit']) { $data['unit'][$kk]['drawing'][] = $u_drawings[$k]; $data['unit'][$kk]['drawing_cnt'] = is_numeric($data['unit'][$kk]['drawing_cnt'])?++$data['unit'][$kk]['drawing_cnt']:1; break; }
           }
       }
       
       //Print only templates [ot]
       if ($only_templates) 
       {
       	 $data['order']['subtotal_price'] = sprintf('%.2f',$tprice);
       	 if ($data['order']['tax_price']>=0.01) $data['order']['tax_price'] = $data['order']['subtotal_price']*$data['order']['tax_level']/100.0;  
       } else $data['order']['subtotal_price'] = sprintf('%.2f',$data['order']['subtotal_price']);
       $data['order']['tax_price'] = sprintf('%.2f',$data['order']['tax_price']); 
       
       if ($data['order']['state']=='PA') 
       		$data['order']['total_price'] = $data['order']['subtotal_price'] + $data['order']['tax_price'];//+$tprice+$iprice;
       
        else 
        $data['order']['total_price'] = $data['order']['subtotal_price'] + $data['order']['tax_price'];
    
    
    
    
    
	 //  print('------'.$data['order']['subtotal_price']);
	   //Count balance
       $data['order']['balance'] = $data['order']['total_price'];
       if (is_array($data['payment'])) foreach($data['payment'] as $pay) $data['order']['balance'] -= $pay['total'];
       //Usun zera z podatku (na koncu)
       $data['order']['tax_level'] = preg_replace("/(\.\d+?)0+$/", "$1", $data['order']['tax_level'])*1;



       //Wyrownaj ceny (oszukaj zaokraglenia)
       if (!$only_templates)
         if ($data['order']['subtotal_price']>0)
         {
              //Zaokraglenie do dwoch miejsc po przecinku
              $unity =0;
			  for ($p=0;$p<$data['unit_cnt'];$p++)
			 { 
			 $data['unit'][$p]['price']['total_invprice'] = round($data['unit'][$p]['price']['total_invprice'],2);
			 // print($data['unit'][$p]['price']['total_invprice'].'<br>');
			    $unity+=$data['unit'][$p]['price']['total_invprice'];
			 }
   			  //print($unity.'--');
			  $data['order']['subtotal_price'] = round($data['order']['subtotal_price'],2);
              $data['order']['product_price'] = round($data['order']['product_price'],2);
              $data['order']['inst_price'] = round($data['order']['inst_price'],2);
              $data['order']['temp_price'] = round($data['order']['temp_price'],2);
            //  print( $data['order']['subtotal_price'].'stotal<br>');
              //Uwzglednij roznice
           
              $difference = $data['order']['subtotal_price']+$data['order']['discount_val']-$data['order']['product_price'];//-$data['order']['inst_price']-$data['order']['temp_price'];
        
          //  print($difference.'--dd-<br>');
			  if ($data['unit_cnt']>0 && abs($difference)>0)
              {
            //       print($data['unit'][$k]['price']['total_invprice'].'----------<br>');
                     for ($k=0;$k<$data['unit_cnt'];$k++) 
					 $difference -= $data['unit'][$k]['price']['total_invprice'];
                     
                     
                     
                     $difference /= $data['unit_cnt'];
                      $difference=round($difference,2);
              //        print($difference.'<br>');
					 if (abs($difference)>0.01) 
                       for ($k=0;$k<$data['unit_cnt'];$k++) $data['unit'][$k]['price']['total_invprice'] += $difference;
              }
             // print( $data['order']['subtotal_price'].'subtotal<br>'); 
             
			 
			 
			 
			  if (abs(round($data['order']['tot_price']-$data['order']['total_price'],2))>=0.01)
              {
                     $difference = sprintf("%.2f",$data['order']['tot_price']-$data['order']['total_price']);
                    // print($data['order']['tot_price'].'---'.$data['order']['total_price'] );
					 if ($data['unit_cnt']>0) $data['unit'][0]['price']['total_invprice']+=$difference;
                     $data['order']['subtotal_price']+=$difference;
                     $data['order']['total_price'] += $difference;
              }
    		  
			// print( $data['order']['subtotal_price'].'subtotal<br>'); 
				  //Niweluj roznice $0.01 pomiedzy sub_price a skladnikami       
	              $ctotal = 0;
	              for ($p=0;$p<$data['unit_cnt'];$p++) $ctotal += $data['unit'][$p]['price']['total_invprice'];
	              $ctotal += $data['order']['product_price'];
	              //print($ctotal.'--');
	             	              
	              if (abs(round($ctotal-$data['order']['subtotal_price'],2))>=0.01) ;
	              { 
	                      $difference = round($data['order']['subtotal_price']-$ctotal,2);
	                      if ($data['unit_cnt']>0) $data['unit'][0]['price']['total_invprice']+=$difference;
	                       else $data['order']['inst_price']+=$difference;
	              }
			
        

            //niweluj r�nice dla PA z grand total bo to popirdzielic sie moze
            
             if ($data['order']['state']=='PA')   
				{
					$gtotal = 0;
	               	for ($p=0;$p<$data['unit_cnt'];$p++) $gtotal += $data['unit'][$p]['price']['total_invprice'];
	              	$gtotal += $data['order']['product_price'];
	              	//print($ctotal.'--');
	              	           
	                 	$gtotal += $data['order']['inst_price']; 
	                 	$gtotal += $data['order']['temp_price'];
	                   
	                    $gtotal+= $data['order']['tax_price'];
	              //print(abs(round($gtotal-$data['order']['total_price'],2)));
	              if (abs(round($gtotal-$data['order']['total_price'],2))>=0.01) ;
	              { 
	                      $difference = round($data['order']['total_price']-$gtotal,2);
	                      
	                      $data['order']['inst_price']+=$difference;
	              }
					
					
					
				}

		 }
    



       if ($this->iid>0)
         for ($i=0;$i<$data['unit_cnt'];$i++)
         {
           $inst_cnt = is_array($data['unit'][$i]['inst'])?count($data['unit'][$i]['inst']):0;
           $in_inst = false;
           for($j=0;$j<$inst_cnt;$j++)
                if ($data['unit'][$i]['inst'][$j]['id'] == $this->iid) { $in_inst = true; break; }
              //Remove unit from the list
              if (!$in_inst)
              {
                 for ($j=$i;$j<$data['unit_cnt']-1;$j++) $data['unit'][$j] = $data['unit'][$j+1];  
                 unset($data['unit'][$j]);
                 $i--; $data['unit_cnt']--;
              }
         }
       if ($this->uid>0)
         for ($i=0;$i<$data['unit_cnt'];$i++)
           if ($data['unit'][$i]['id'] == $this->uid)
           {
             $data['unit'][0] = $data['unit'][$i];
             for ($j=1;$j<$data['unit_cnt'];$j++) unset($data['unit'][$j]);
             break;
           }
     
   //print_r($data);
       return $data;
   }
   //----------------------------------------------------------------------------------------------------------------------------------
   public function GetError() { return '<font color="red" ><span style="font-family: Arial, Helvetica, sans-serif">'.$this->error.'</span></font>'; }
   //----------------------------------------------------------------------------------------------------------------------------------
   //----------------------------------------------------------------------------------------------------------------------------------
   //----------------------------------------------------------------------------------------------------------------------------------
}
?>
