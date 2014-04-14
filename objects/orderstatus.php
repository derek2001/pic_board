<?
include_once ("object.user.php");

class OrderStatus
{
   //private:
   private $user;
   private $actDepartment;
   private $operation;
   private $unitoperation;
   private $advunitoperation;

   //---------------------------------------------------------------------------
   public function __construct(User $usr) {
       $this->user = & $usr;


       $dep = $this->user->getActiveDepartment();
       $deps = $this->user->getDepartment();
       if (isset($deps[$dep['id']]['did'])) $this->actDepartment = $deps[$dep['id']]['did']; else $this->actDepartment = -1;

       $this->user->db->connect("erp");
       $this->user->db->select("select [status_id], [status_name], [ord_value], [operation_id], [operation_name], [operation_disp_name], [department_id], [department_name], [department_color], [status_value] from  v_ord_operation_priv where department_id='".$this->actDepartment."'");
       $this->operation = array();
       $this->operation = $this->user->db->fetchAllArrays();

       $this->user->db->select("select [status_id], [status_name], [unit_value], [operation_id], [operation_name], [operation_disp_name], [department_id], [department_name], [department_color], [status_value] from  v_unit_operation_priv where department_id='".$this->actDepartment."'");
       $this->unitoperation = array();
       $this->unitoperation = $this->user->db->fetchAllArrays();
       
       $this->user->db->select("select [wo_status_id], [wo_status_name], [ord_value], [unit_status_id], [unit_status_name], [unit_value], [operation_id], [operation_name], [operation_disp_name], [department_id], [department_name], [department_color], [status_value] from v_depord_operation_priv where department_id='".$this->actDepartment."' and status_value IS NOT NULL");
       $this->advunitoperation = array();
       $this->advunitoperation = $this->user->db->fetchAllArrays();
       //if($_SERVER['REMOTE_ADDR'] == '192.168.1.121') print_r($this->operation);
   }

   //---------------------------------------------------------------------------
   public function getOperationStatus($name,$status)
   {
    if ($this->user->is_Admin() == true) return true;
    if (!is_numeric($status)) $status = 0;
    if (is_array($this->operation))
      foreach ($this->operation as $val) {
       if ($val['operation_name']==$name)
         if ($val['status_value']==$status) return true;
      }   
    return false;
   }

   //---------------------------------------------------------------------------
   public function generateOperationHTML($op_id)
   {
    include("lists/ord_status.inc.php");
    if (!is_array($ORD_STATUS_LIST)) return;
    include("lists/ord_operation.inc.php");
    if (!is_array($OPERATION_LIST) || count($OPERATION_LIST)<1) return;
    include("lists/department.inc.php");
    if (!is_array($DEPARTMENT_LIST)) return;

    $this->user->db->connect("erp");
    if (is_numeric($op_id)) $this->user->db->select("select * from  v_ord_operation_priv where operation_id='".$op_id."' order by status_value,status_id,department_id");
      else $this->user->db->select("select * from  v_ord_operation_priv where operation_id=(SELECT MIN(id) from ord_operation) order by status_value,status_id,department_id");
    $tab1 = $this->user->db->fetchAllArrays();

    $cnt = count($tab1); $k = 0;
    for ($i=0; $i<$cnt;)
      if (is_numeric($tab1[$i]['department_id']) || $tab1[$i]['status_value']!=$tab1[$i+1]['status_value'])
       {
         $tab[$k] = $tab1[$i];  $l = 0;
         while ($tab1[$i]['status_id'] == $tab[$k]['status_id'])
          if (is_numeric($tab1[$i]['department_id']))
           $tab[$k]['department'][$l++]['id'] = $tab1[$i++]['department_id'];
            else {  $i++; break; }
         $k++;
       } else $i++;
    $description = $OPERATION_LIST[0]['description'];
    $str = '
         <table width="100%"  border="0" cellpadding="0">
           <tr class="text_black">
             <form name="sel_operation" method="post" action=""><td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td width="40%" class="text_black" nowrap>
               Choose operation:&nbsp;<select name="id_operation" onChange="submit(this.form)">
               ';
                foreach ($OPERATION_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $op_id) { $str .= ' selected'; $description = $val['description'];}
                  $str .= '>'.$val['disp_name'].'</option>';
                }
    $str .= '
               </select>
             </td></form><td class="text_black" valign="top">'.$description.'</td></tr></table></td>
           </tr>
           <tr><td><hr size="1" noshade color="black"></td></tr>
           <tr class="text_black">
             <td><form name="statform" method="post" action="" onsubmit="document.statform.id_operation.value = document.sel_operation.id_operation.options.value">
             <input type="hidden" name="id_operation" value="">
             <input type="hidden" name="operation">
             <table width="100%"  border="0" cellpadding="0">
               <tr class="text_black">
                 <td>&nbsp;</td>';
                 foreach ($ORD_STATUS_LIST AS $val) $str .= ' <td><div align="center"><b>'.$val['name'].'</b></div></td>';
    $str .= '
               </tr>
               ';
     foreach($DEPARTMENT_LIST as $dval)
     {
       $str .= '<tr class="text_black">';
       $str .= ' <td><div align="right"><b>'.$dval['name'].'</b></div></td>';
       if (is_array($tab))
       //if (isset($tab[0]['status_id'])) $aux = $tab[0]['status_id']; else
       $aux = -1;
       foreach ($tab as $val)
          if ($val['status_id']!=$aux)
          {
               $str .=     '
                            <td><div align="center">
                                <input name="operation['.$dval['id'].']['.$val['status_id'].']" type="checkbox" value="1"';
               if (is_array($val['department']))
               foreach ($val['department'] as $ddval)
                 if ($ddval['id'] == $dval['id']) { $str .= ' checked'; break; }
               $str .= '>
                            </div></td>
                            ';
               $aux = $val['status_id'];
          }
       $str .= '</tr>';
     }
     $str .= '
             </table></td>
           </tr>
           <tr><td align="center"><input type="submit" name="save_stat_priv" value="Save priviledges" class="BUTTON_OK"></td></form></tr>
         </table>
    ';
    return $str;
   }

   //---------------------------------------------------------------------------
   public function saveOperations($op,$value)
   {
     //echo 'Dziala';
     if (!$_SESSION["user"]->db->delete('ord_operation_priv',$_GET['id'],'DELETE FROM ord_operation_priv WHERE id_ord_operation=\''.$op.'\''))
          return 'Saving settings failed.';

     if (is_array($value))
       foreach($value as $dep_key=>$dep_val)
         if (is_array($dep_val))
           foreach ($dep_val as $stat_key => $stat_val)
             $_SESSION["user"]->db->insert('INSERT INTO ord_operation_priv (id_ord_operation, id_ord_status, id_department) VALUES(\''.$op.'\',\''.$stat_key.'\',\''.$dep_key.'\')');
   }

   //---------------------------------------------------------------------------
   public function generateStatusFlowHTML($dep_id)
   {
    include("lists/ord_status.inc.php");
    if (!is_array($ORD_STATUS_LIST)) return;
    include("lists/ord_operation.inc.php");
    if (!is_array($OPERATION_LIST)) return;
    include("lists/department.inc.php");
    if (!is_array($DEPARTMENT_LIST) || count($DEPARTMENT_LIST)<1) return;

    $this->user->db->connect("erp");
    if (is_numeric($dep_id)) $this->user->db->select("select * from  v_ord_status_flow where id_department='".$dep_id."' order by id_department,status_value,id_ord_status");
      else $this->user->db->select("select * from  v_ord_status_flow where id_department=(SELECT MIN(id) from department) order by id_department,status_value,id_ord_status");
    $tab1 = $this->user->db->fetchAllArrays();

    $cnt = count($tab1);
    for ($i=0; $i<$cnt;)
    if (isset($tab1[$i]['id_ord_status']))
    {
      $k = $tab1[$i]['id_ord_status'];
      $tab[$k] = $tab1[$i];  $l = 0;
      while ($tab1[$i]['id_ord_status'] == $tab[$k]['id_ord_status'])
       if ($tab1[$i]['id_av_status'] != '')
        $tab[$k]['av_status'][$l++]['id'] = $tab1[$i++]['id_av_status'];
         else {  $i++; break; }

    } else $i++;
    //echo 'Tab: '; print_r($tab);
    $str = '
         <table width="100%"  border="0" cellpadding="0">
           <tr class="text_black">
             <form name="sel_department" method="post" action=""><td>
               Choose department:&nbsp;<select name="id_department" onChange="submit(this.form)">
               &nbsp;&nbsp;<font color="red">[Set status flow for chosen status in a column]</font>';
                foreach ($DEPARTMENT_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $dep_id) $str .= ' selected';
                  $str .= '>'.$val['name'].'</option>';
                }
    $str .= '
               </select>
             </td></form>
           </tr>
           <tr><td><hr size="1" noshade color="black"></td></tr>
           <tr class="text_black">
             <td><form name="depform" method="post" action="" onsubmit="document.depform.id_department.value = document.sel_department.id_department.options.value">
             <input type="hidden" name="id_department" value="">
             <input type="hidden" name="department">
             <table width="100%"  border="0" cellpadding="0">
               <tr class="text_black">
                 <td>&nbsp;</td>';
                 foreach ($ORD_STATUS_LIST AS $val) $str .= ' <td><div align="center"><b>'.$val['name'].'</b></div></td>';
    $str .= '
               </tr>
               ';
     foreach($ORD_STATUS_LIST as $val)
     {
       $str .= '<tr class="text_black">';
       $str .= ' <td><div align="right"><b>'.$val['name'].'</b></div></td>';
       foreach($ORD_STATUS_LIST as $dval)
       {
        $str .=     '
                        <td><div align="center">
                            ';
        if ($val['id']==$dval['id']) $str .= '-';
          else
          {
            $str .= '<input name="department['.$dval['id'].']['.$val['id'].']" type="checkbox" value="1"';
            if (isset($tab[$dval['id']]['av_status']) && is_array($tab[$dval['id']]['av_status']))
            foreach ($tab[$dval['id']]['av_status'] as $ddval)
              if ($ddval['id'] == $val['id']) { $str .= ' checked'; break; }
            $str .= '>';
          }
        $str .= '
                     </div></td>
                     ';
       }
       $str .= '</tr>';
     }
     $str .= '
             </table></td>
           </tr>
           <tr><td align="center"><input type="submit" name="save_stat_priv" value="Save Status Flow" class="BUTTON_OK"></td></form></tr>
         </table>
    ';
    return $str;
   }
   //---------------------------------------------------------------------------
   public function saveOrdStatusFlow($dep,$value)
   {
     if (!$_SESSION["user"]->db->delete('ord_status_flow',$_GET['id'],'DELETE FROM ord_status_flow WHERE id_department=\''.$dep.'\''))
          return 'Saving settings failed.';

     if (is_array($value))
      foreach($value as $stat_key=>$stat_val)
       if (is_array($stat_val))
        foreach ($stat_val as $av_key => $av_val)
         $_SESSION["user"]->db->insert('INSERT INTO ord_status_flow (id_ord_status, id_av_status, id_department) VALUES(\''.$stat_key.'\',\''.$av_key.'\',\''.$dep.'\')');
   }
   //---------------------------------------------------------------------------
   public function generateStatusHTML($act_ord)
   {
    if (!is_numeric($act_ord)) return;
    include("lists/ord_status.inc.php");
    if (!is_array($ORD_STATUS_LIST)) return;

    $this->user->db->connect("erp");
    $sql = "select os.id as status_id,os.name as status_name,os.value as status_value,osf.id_av_status,os1.value as av_value
from ord_status os left outer join ord_status_flow osf on os.id=osf.id_ord_status
inner join ord_status os1 on os1.id=osf.id_av_status where id_department='".$this->actDepartment."' and os.value='".$act_ord."'";
    $this->user->db->select($sql);
    $tab = $this->user->db->fetchAllArrays();
    if (is_array($tab))  foreach($tab as $val) $enabled[$val['av_value']] = true;
    $js = '';
    $str = '
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    ';
    $l = 0;
    foreach ($ORD_STATUS_LIST as $val)
    {
      $str .= '
          <tr class="text_black">
            <td class="row'.$l.'1"><label>';
      if($val['value'] == 4)
          $str .="<div id='unit_status'>";
      $str .='<input type="radio" name="status" value="'.$val['value'].'"';
      if (!isset($enabled[$val['value']]) && $this->actDepartment!=0 && $act_ord != $val['value']) $str .= ' disabled';
       else if ($act_ord == $val['value']) { $str .= ' checked'; $js = '<script language="javascript">document.getElementById(\'status_act\').innerHTML = \'&nbsp;('.$val['name'].')\';</script>'; }
      $str .= ' onClick="javascript:document.getElementById(\'save_button\').focus();">
              '.$val['name'].'</label>';
      if($val['value'] == 4)
          $str .='<div id="unitList"></div></div>';
      $str .='</td></tr>';
      $l = $l==0?$l=1:$l=0;
    }

    $str .= '
    </table>
    ';
    return $str.$js;
   }
   //---------------------------------------------------------------------------


   /****************************************************************************
                                       UNITs
   ****************************************************************************/
   //---------------------------------------------------------------------------
   public function getUnitOperationStatus($name,$status)
   {
    if ($this->user->is_Admin() == true) return true;
    if (!is_numeric($status)) $status = 0;
    if (is_array($this->unitoperation))
      foreach ($this->unitoperation as $val)
        if ($val['operation_name']==$name)
          if ($val['status_value']==$status) return true;
    return false;
   }

   //---------------------------------------------------------------------------
   public function generateUnitOperationHTML($op_id)
   {
    include("lists/unit_status.inc.php");
    if (!is_array($UNIT_STATUS_LIST)) return;
    include("lists/unit_operation.inc.php");
    if (!is_array($OPERATION_LIST) || count($OPERATION_LIST)<1) return;
    include("lists/department.inc.php");
    if (!is_array($DEPARTMENT_LIST)) return;

    $this->user->db->connect("erp");
    if (is_numeric($op_id)) $this->user->db->select("select * from  v_unit_operation_priv where operation_id='".$op_id."' order by unit_value,status_id,department_id");
      else $this->user->db->select("select * from  v_unit_operation_priv where operation_id=(SELECT MIN(id) from unit_operation) order by unit_value,status_id,department_id");
    $tab1 = $this->user->db->fetchAllArrays();

    $cnt = count($tab1); $k = 0;
    for ($i=0; $i<$cnt;)
    {
      $tab[$k] = $tab1[$i];  $l = 0;
      while ($tab1[$i]['status_id'] == $tab[$k]['status_id'])
       if ($tab1[$i]['department_id'] != '')
        $tab[$k]['department'][$l++]['id'] = $tab1[$i++]['department_id'];
         else {  $i++; break; }
      $k++;
    }
    $description = $OPERATION_LIST[0]['description'];
    $str = '
         <table width="100%"  border="0" cellpadding="0">
           <tr class="text_black">
             <form name="sel_operation" method="post" action=""><td>
               <table><tr><td class="text_black" width="40%">
               Choose operation:&nbsp;<select name="id_operation" onChange="submit(this.form)">
               ';
                foreach ($OPERATION_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $op_id) { $str .= ' selected'; $description = $val['description']; }
                  $str .= '>'.$val['disp_name'].'</option>';
                }
    $str .= '
               </select>
             </td><td class="text_black">'.$description.'</td></tr></table></td></form>
           </tr>
           <tr><td><hr size="1" noshade color="black"></td></tr>
           <tr class="text_black">
             <td><form name="statform" method="post" action="" onsubmit="document.statform.id_operation.value = document.sel_operation.id_operation.options.value">
             <input type="hidden" name="id_operation" value="">
             <input type="hidden" name="operation">
             <table width="100%"  border="0" cellpadding="0">
               <tr class="text_black">
                 <td>&nbsp;</td>';
                 foreach ($UNIT_STATUS_LIST AS $val) $str .= ' <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}"><div align="center"><b>'.$val['name'].'</b></div></td>';
    $str .= '
               </tr>
               ';

     foreach($DEPARTMENT_LIST as $dval)
     {
       $str .= '<tr class="text_black">';
       $str .= ' <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}"><div align="right"><b>'.$dval['name'].'</b></div></td>';
       foreach ($tab as $val)
       {

           $str .=     '
                        <td class="row{$_j_}{cycle name="parity" values="0,1"}" id="{$_i_}{cycle name="col_cycle" values="$columns"}"><div align="center">
                            <input name="operation['.$dval['id'].']['.$val['status_id'].']" type="checkbox" value="1"';
           if (is_array($val['department']))
           foreach ($val['department'] as $ddval)
             if ($ddval['id'] == $dval['id']) { $str .= ' checked'; break; }
           $str .= '>
                        </div></td>
                        ';
       }
       $str .= '</tr>';
     }
     $str .= '
             </table></td>
           </tr>
           <tr><td align="center"><input type="submit" name="save_stat_priv" value="Save priviledges" class="BUTTON_OK"></td></form></tr>
         </table>
    ';
    return $str;
   }

   //---------------------------------------------------------------------------
   public function saveUnitOperations($op,$value)
   {
     //echo 'Dziala';
     if (!$_SESSION["user"]->db->delete('unit_operation_priv',$_GET['id'],'DELETE FROM unit_operation_priv WHERE id_unit_operation=\''.$op.'\''))
          return 'Saving settings failed.';

     if (is_array($value))
       foreach($value as $dep_key=>$dep_val)
        if (is_array($dep_val))
          foreach ($dep_val as $stat_key => $stat_val)
            $_SESSION["user"]->db->insert('INSERT INTO unit_operation_priv (id_unit_operation, id_unit_status, id_department) VALUES(\''.$op.'\',\''.$stat_key.'\',\''.$dep_key.'\')');
   }

   //---------------------------------------------------------------------------
   public function generateUnitStatusFlowHTML($dep_id)
   {
    include("lists/unit_status.inc.php");
    if (!is_array($UNIT_STATUS_LIST)) return;
    include("lists/unit_operation.inc.php");
    if (!is_array($OPERATION_LIST)) return;
    include("lists/department.inc.php");
    if (!is_array($DEPARTMENT_LIST) || count($DEPARTMENT_LIST)<1) return;

    $this->user->db->connect("erp");
    if (is_numeric($dep_id)) $this->user->db->select("select * from  v_unit_status_flow where id_department='".$dep_id."' order by id_department,status_value,id_unit_status");
      else $this->user->db->select("select * from  v_unit_status_flow where id_department=(SELECT MIN(id) from department) order by id_department,status_value,id_unit_status");
    $tab1 = $this->user->db->fetchAllArrays();

    $cnt = count($tab1); //$k = $tab1[0]['id_ord_status'];
    for ($i=0; $i<$cnt;)
    if (isset($tab1[$i]['id_unit_status']))
    {
      $k = $tab1[$i]['id_unit_status'];
      $tab[$k] = $tab1[$i];  $l = 0;
      while ($tab1[$i]['id_unit_status'] == $tab[$k]['id_unit_status'])
       if ($tab1[$i]['id_av_status'] != '')
        $tab[$k]['av_status'][$l++]['id'] = $tab1[$i++]['id_av_status'];
         else {  $i++; break; }

    } else $i++;
    //echo 'Tab: '; print_r($tab);
    $str = '
         <table width="100%"  border="0" cellpadding="0">
           <tr class="text_black">
             <form name="sel_department" method="post" action=""><td>
               Choose department:&nbsp;<select name="id_department" onChange="submit(this.form)">
               &nbsp;&nbsp;<font color="red">[Set status flow for chosen status in a column]</font>';
                foreach ($DEPARTMENT_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $dep_id) $str .= ' selected';
                  $str .= '>'.$val['name'].'</option>';
                }
    $str .= '
               </select>
             </td></form>
           </tr>
           <tr><td><hr size="1" noshade color="black"></td></tr>
           <tr class="text_black">
             <td><form name="depform" method="post" action="" onsubmit="document.depform.id_department.value = document.sel_department.id_department.options.value">
             <input type="hidden" name="id_department" value="">
             <input type="hidden" name="department">
             <table width="100%"  border="0" cellpadding="0">
               <tr class="text_black">
                 <td>&nbsp;</td>';
                 foreach ($UNIT_STATUS_LIST AS $val) $str .= ' <td><div align="center"><b>'.$val['name'].'</b></div></td>';
    $str .= '
               </tr>
               ';

     foreach($UNIT_STATUS_LIST as $val)
     {
       $str .= '<tr class="text_black">';
       $str .= ' <td><div align="right"><b>'.$val['name'].'</b></div></td>';
       foreach($UNIT_STATUS_LIST as $dval)
       {
        $str .=     '
                        <td><div align="center">
                            ';
        if ($val['id']==$dval['id']) $str .= '-';
          else
          {
            $str .= '<input name="department['.$dval['id'].']['.$val['id'].']" type="checkbox" value="1"';
            if (isset($tab[$dval['id']]['av_status']) && is_array($tab[$dval['id']]['av_status']))
            foreach ($tab[$dval['id']]['av_status'] as $ddval)
              if ($ddval['id'] == $val['id']) { $str .= ' checked'; break; }
            $str .= '>';
          }
        $str .= '
                     </div></td>
                     ';
       }
       $str .= '</tr>';
     }
     $str .= '
             </table></td>
           </tr>
           <tr><td align="center"><input type="submit" name="save_stat_priv" value="Save Status Flow" class="BUTTON_OK"></td></form></tr>
         </table>
    ';
    return $str;
   }
   //---------------------------------------------------------------------------
   public function saveUnitStatusFlow($dep,$value)
   {
     if (!$_SESSION["user"]->db->delete('unit_status_flow',$_GET['id'],'DELETE FROM unit_status_flow WHERE id_department=\''.$dep.'\''))
          return 'Saving settings failed.';

     if (is_array($value))
       foreach($value as $stat_key=>$stat_val)
        if (is_array($stat_val))
          foreach ($stat_val as $av_key => $av_val)
            $_SESSION["user"]->db->insert('INSERT INTO unit_status_flow (id_unit_status, id_av_status, id_department) VALUES(\''.$stat_key.'\',\''.$av_key.'\',\''.$dep.'\')');
   }

   //---------------------------------------------------------------------------
   public function generateUnitStatusHTML($ord_status,$act_ord)
   {
    if (!is_numeric($act_ord)) return;
    include("lists/unit_status.inc.php");
    if (!is_array($UNIT_STATUS_LIST)) return;

    $this->user->db->connect("erp");
    
    //Wersja, gdy statusy unitow nie byly zalezne od statusow WO
    /* 
    $sql = "select os.id as status_id,os.name as status_name,os.value as status_value,osf.id_av_status,os1.value as av_value
from unit_status os left outer join unit_status_flow osf on os.id=osf.id_unit_status
inner join unit_status os1 on os1.id=osf.id_av_status where id_department='".$this->actDepartment."' and os.value='".$act_ord."'";
	*/
	//Wersja, gdy statusy unitow byly niezalezne od statusow WO oraz gdy przez dodatkowy widok byly uwzglednione powiazania
	$sql = 'select os.id as status_id,os.name as status_name,os.value as status_value,osf.id_av_status,os1.value as av_value,vf.status_value
from unit_status os 
left outer join unit_status_flow osf on os.id=osf.id_unit_status 
inner join unit_status os1 on os1.id=osf.id_av_status 
left join v_depord_status_flow vf on osf.id_av_status=vf.unit_status_id and vf.ord_value=\''.$ord_status.'\' and vf.id_department=\''.$this->actDepartment.'\'
where osf.id_department=\''.$this->actDepartment.'\' and os.value=\''.$act_ord.'\' and vf.status_value IS NOT NULL';

    $this->user->db->select($sql);
    $tab = $this->user->db->fetchAllArrays();
    if (is_array($tab))
      foreach($tab as $val) $enabled[$val['av_value']] = true;

    $str = '
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
    ';
    $l = 0; $js = '';
    foreach ($UNIT_STATUS_LIST as $val)
    {
      $str .= '
          <tr class="text_black">
            <td class="row'.$l.'1"><label>
              <input type="radio" name="status" value="'.$val['value'].'"';
      if (!$enabled[$val['value']] && $this->actDepartment!=0 && $act_ord != $val['value']) $str .= ' disabled';
       else if ($act_ord == $val['value']) { $str .= ' checked'; $js = '<script language="javascript">document.getElementById(\'ustatus_act\').innerHTML = \'&nbsp;('.$val['name'].')\';</script>'; }
      $str .= '>
              '.$val['name'].'</label></td>
          </tr>
      ';
      $l = $l==0?$l=1:$l=0;
    }

    $str .= '
    </table>
    ';
    return $str.$js;
   }








   //-------------------------Dependent operations------------------------------
   public function getAdvUnitOperationStatus($name,$wo_status,$status)
   {
    if ($this->user->is_Admin() == true) return false;
    if (!is_numeric($status)) $status = 0;
    if (is_array($this->advunitoperation))
      foreach ($this->advunitoperation as $val)
        if ($val['operation_name']==$name)
          if ($val['ord_value']==$wo_status) 
            if ($val['unit_value']==$status) return true;
    return false;
   }
   //---------------------------------------------------------------------------
   public function saveDepOperations($dep,$op,$value)
   {
     if (!$_SESSION["user"]->db->delete('unit_depoperation_priv',$_GET['id'],'DELETE FROM unit_depoperation_priv WHERE id_unit_depoperation=\''.$op.'\' and id_department=\''.$dep.'\''))
          return 'Saving settings failed.';

     if (is_array($value))
       foreach($value as $okey=>$oval)
         if (is_array($oval))
           foreach ($oval as $ukey => $uval)
             $_SESSION["user"]->db->insert('INSERT INTO unit_depoperation_priv (id_unit_depoperation, id_wo_status, id_unit_status, id_department) VALUES(\''.$op.'\',\''.$okey.'\',\''.$ukey.'\',\''.$dep.'\')');
   }
   //---------------------------------------------------------------------------
   public function generateDepStatusFlowHTML($dep_id)
   {
    include("lists/ord_status.inc.php");
    if (!is_array($ORD_STATUS_LIST)) return;
    include("lists/ord_operation.inc.php");
    if (!is_array($OPERATION_LIST)) return;
    include("lists/department.inc.php");
    if (!is_array($DEPARTMENT_LIST) || count($DEPARTMENT_LIST)<1) return;
    include("lists/unit_status.inc.php");
    if (!is_array($UNIT_STATUS_LIST)) return;

    $this->user->db->connect("erp");
    $dep_sql = is_numeric($dep_id)?'\''.$dep_id.'\'':'(SELECT MIN(id) from department)';
    $sql = "select * from  v_depord_status_flow where id_department=".$dep_sql." order by unit_value,ord_value,id_department";
    $this->user->db->select($sql);
    $tab = $this->user->db->fetchAllArrays();

    $str = '
         <table width="100%"  border="0" cellpadding="0">
           <tr class="text_black"><form name="sel_department" method="post" action="">
             <td>
               Choose department:&nbsp;<select name="id_department" onChange="submit(this.form)">
               &nbsp;&nbsp;<font color="red">[Set status flow for chosen status in a column]</font>';
                foreach ($DEPARTMENT_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $dep_id) $str .= ' selected';
                  $str .= '>'.$val['name'].'</option>';
                }
    $str .= '
               </select>
             </td></form>
           </tr>
           <tr><td><hr size="1" noshade color="black"></td></tr>
           <tr class="text_black">
             <td><form name="depform" method="post" action="" onsubmit="document.depform.id_department.value = document.sel_department.id_department.options.value">
             <input type="hidden" name="id_department" value="">
             <input type="hidden" name="department">
             <table width="100%"  border="0" cellpadding="0">
               <tr class="text_black">
                 <td>&nbsp;</td>';
                 foreach ($ORD_STATUS_LIST AS $val) $str .= ' <td><div align="center"><b>'.$val['name'].'</b></div></td>';
    $str .= '
               </tr>
               ';
               
     $line_cnt = is_array($ORD_STATUS_LIST) ? count($ORD_STATUS_LIST): 0; $list_pos = 0;          
     foreach($UNIT_STATUS_LIST as $val)
     {
       $str .= '<tr class="text_black">';
       $str .= ' <td><div align="right"><b>'.$val['name'].'</b></div></td>';
       for ($i=0;$i<$line_cnt;$i++)
       {
        $str .=     '
                        <td><div align="center">
                            ';
        $str .= '<input name="department['.$tab[$list_pos]['ord_status_id'].']['.$tab[$list_pos]['unit_status_id'].']" type="checkbox" value="1"';
        if (isset($tab[$list_pos]['status_value']) && is_numeric($tab[$list_pos]['status_value'])) $str .= ' checked';
        $str .= '>
                     </div></td>
                     ';
        $list_pos++;
       }
       $str .= '</tr>';
     }
     $str .= '
             </table></td>
           </tr>
           <tr><td align="center"><input type="submit" name="save_stat_priv" value="Save Status Flow" class="BUTTON_OK"></td></form></tr>
         </table>
    ';
    return $str;
   }
   //---------------------------------------------------------------------------
   public function saveDepOrdStatusFlow($dep,$value)
   {
     //echo 'Dep: '.$dep; print_r($value); return '';
     if (!$_SESSION["user"]->db->delete('wo_unit_status_flow',$_GET['id'],'DELETE FROM wo_unit_status_flow WHERE id_department=\''.$dep.'\''))
          return 'Saving settings failed.';

     if (is_array($value))
      foreach($value as $okey=>$oval)
       if (is_array($oval))
        foreach ($oval as $ukey => $uval)
         $_SESSION["user"]->db->insert('INSERT INTO wo_unit_status_flow (id_wo_status, id_unit_status, id_department) VALUES(\''.$okey.'\',\''.$ukey.'\',\''.$dep.'\')');
   }
   //---------------------------------------------------------------------------
   public function generateDepOperationHTML($dep,$op_id)
   {
    include("lists/ord_status.inc.php");
    if (!is_array($ORD_STATUS_LIST)) return;
    include("lists/dep_operation.inc.php");
    if (!is_array($DEP_OPERATION_LIST) || count($DEP_OPERATION_LIST)<1) return;
    include("lists/department.inc.php");
    if (!is_array($DEPARTMENT_LIST)) return;
    include("lists/unit_status.inc.php");
    if (!is_array($UNIT_STATUS_LIST)) return;

    $this->user->db->connect("erp");
    $op_sql = is_numeric($op_id)?'\''.$op_id.'\'':'(SELECT MIN(id) from unit_depoperation)';
    $dep_sql = is_numeric($dep)?'\''.$dep.'\'':'(SELECT MIN(id) from department)';
    $sql = "select * from  v_depord_operation_priv where operation_id=".$op_sql." and department_id=".$dep_sql." order by unit_value,ord_value,department_id";
    $this->user->db->select($sql);
    $tab = $this->user->db->fetchAllArrays();
 
    $description = $DEP_OPERATION_LIST[0]['description'];
    $str = '
         <table width="100%"  border="0" cellpadding="0">
           <tr>
             <td><table width="100%"><tr class="text_black"><form name="sel_operation" method="post" action=""><td>
               Choose department:&nbsp;<select name="id_odepartment" onChange="submit(this.form)">
               ';
                foreach ($DEPARTMENT_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $dep) $str .= ' selected';
                  $str .= '>'.$val['name'].'</option>';
                }
    $str .= '
               </select></td><td>Choose operation:&nbsp;<select name="id_operation" onChange="submit(this.form)">
               ';
                foreach ($DEP_OPERATION_LIST AS $val)
                {
                  $str .= ' <option value="'.$val['id'].'"';
                  if ($val['id'] == $op_id) { $str .= ' selected'; $description = $val['description'];}
                  $str .= '>'.$val['disp_name'].'</option>';
                }
    $str .= '
             </td></form></tr>
             <tr><td></td><td class="text_black">'.$description.'</td></tr>
             </table></td>
           </tr>
           <tr><td><hr size="1" noshade color="black"></td></tr>
           <tr class="text_black">
             <td><form name="statform" method="post" action="" onsubmit="document.statform.id_operation.value = document.sel_operation.id_operation.options.value; document.statform.id_odepartment.value = document.sel_operation.id_odepartment.options.value">
             <input type="hidden" name="id_operation" value="">
             <input type="hidden" name="id_odepartment" value="">
             <input type="hidden" name="operation">
             <table width="100%"  border="0" cellpadding="0">
               <tr class="text_black">
                 <td>&nbsp;</td>';
                 foreach ($ORD_STATUS_LIST AS $val) $str .= ' <td><div align="center"><b>'.$val['name'].'</b></div></td>';
    $str .= '
               </tr>
               ';
     $line_cnt = is_array($ORD_STATUS_LIST) ? count($ORD_STATUS_LIST): 0; $list_pos = 0;
     foreach($UNIT_STATUS_LIST as $dval)
     {
       $str .= '<tr class="text_black">';
       $str .= ' <td><div align="right"><b>'.$dval['name'].'</b></div></td>';
       if (is_array($tab))
          for ($i=0;$i<$line_cnt;$i++)
          {
          	$str .=     '
                            <td><div align="center">
                                <input name="operation['.$tab[$list_pos]['wo_status_id'].']['.$tab[$list_pos]['unit_status_id'].']" type="checkbox" value="1"';
            if (isset($tab[$list_pos]['status_value']) && is_numeric($tab[$list_pos]['status_value'])) $str .= ' checked';
            $str .= '>
                          </div></td>
                          ';
            $list_pos++;
          }
       $str .= '</tr>';
     }
     $str .= '
             </table></td>
           </tr>
           <tr><td align="center"><input type="submit" name="save_stat_priv" value="Save priviledges" class="BUTTON_OK"></td></form></tr>
         </table>
    ';
    return $str;
   }

   
}
?>