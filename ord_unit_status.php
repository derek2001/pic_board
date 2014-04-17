<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/26/14
 * Time: 2:40 PM
 */

include_once("config.php");
include_once("functions/paging.php");

$_SESSION["user"]->db->connect('erp');
$loc = $_SESSION["user"]->getLocationProfile();

abstract class UnitStatus{
    const notStart = 0;
    const inProcess = 1;
    const overwrite = 2;
    const finish = 3;
}

//initiate all the unit that not cut yet.
function initUnitStatus($orders){
    foreach($orders as $order){
        $_SESSION["user"]->db->select("select ou.id id, ou.name from [ord_unit] ou
            inner join [order] o on o.id = ou.id_order
            where o.id = ".$order['0']."
            ");

        $units = $_SESSION["user"]->db->fetchAllArrays();

        foreach($units as $k=>$unit){
            $sql = "select * from [ord_unit_status] ous
            where ous.id_order = ".$order['id']."
            and ous.id_ord_unit = ".$unit['id']."
            ";
            $_SESSION["user"]->db->select($sql);
            $mem = $_SESSION["user"]->db->fetchArray();
            if($_SESSION["user"]->db->numrows() < 1)
            {
                $insert = "INSERT INTO [SlabImgDB].[dbo].[ord_unit_status]
                 VALUES
                   (".$order['id']."
                   ,".$unit['id']."
                   ,0 ,0 ,null ,null
                   ,0 ,0 ,null ,null
                   ,0 ,0 ,null ,null,1
                   )";

                $_SESSION["user"]->db->insert($insert);
            }
        }
    }
}

function setOrder($op)
{
    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER'] == $op)
    {
        if ($_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'] == 'ASC') $_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'] = 'DESC';
        else $_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'] = 'ASC';
    } else
    {
        $_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER'] = $op;
        $_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'] = 'ASC';
    }
    $_SESSION['ORD_UNIT_STATUS_SESSION']['CNT'] = '';
}
function imageSelect()
{
   return array($_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER']=>'<img src=\'gfx/'.$_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'].'.gif\'>');
}
//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
if ($_POST['submit'] == 'Search') {
    $_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH'] = $_POST;
    unset ($_POST);
}
$query = "";
$proj = "";
$status = " and u_status = 1 ";
if (isset ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH'])) {
    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword'] != '') {
        $query .= " AND (";
        if (is_numeric($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword']))
        {
            $query .= " id_ord_unit='".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword']."' or ";
            $query .= "id_order = '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword']."' or ";
        }
        $query .= " c_name like '%"
            .$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword']."%' or p_name like '%"
            .$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword']."%' or i_name like '%"
            .$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['keyword']."%' )";
    }

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['ord'] != '')
        $query .= " AND id_order = '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['ord']."'";

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['unt'] != '')
        $query .= " AND id_ord_unit = '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['unt']."'";

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['operator'] != '')
        $query .= " AND (c_name = '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['operator']."'
            or p_name = '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['operator']."'
            or i_name = '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['operator']."' )";

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['cut_from'] != '')
        $query .= " AND c_start_time >= '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['cut_from']."'";
    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['cut_to'] != '')
        $query .= " AND c_fin_time <= '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['cut_to']."'";

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['pro_from'] != '')
        $query .= " AND p_start_time >= '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['pro_from']."'";
    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['pro_to'] != '')
        $query .= " AND p_fin_time <= '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['pro_to']."'";

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['ins_from'] != '')
        $query .= " AND i_start_time >= '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['ins_from']."'";
    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['ins_to'] != '')
        $query .= " AND i_fin_time <= '".$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['ins_to']."'";
    $_SESSION['ORD_UNIT_STATUS_SESSION']['CNT'] = '';

    if ($_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']['in_obsolete'] != '')
        $status = " and u_status in (0,1)";
}

$script='ord_unit_status';
$page = 10;
function getCnt($loc, $proj, $query){
    $_SESSION["user"]->db->select("select count(*) as cnt from dbo.unit_status_select()
        where status = 4
        $query
        ");
    $cnt = $_SESSION["user"]->db->fetchArray();
    /*$_SESSION["user"]->db->select("select * from [order] o where o.status in (3,4) and o.id_location = ".$loc['id']."  $query");
    $data = $_SESSION['user']->db->fetchAllArrays();
    $num=1;
    $flag=0;
    foreach($data as $k=>$order) {
        if(strcmp($query, ' AND ') == 0 || $query=='')
        {
            $rows = "select * from [ord_unit_status] ous
            where ous.id_order = ".$order['id']."
            and ous.i_status < 2
            ";
        }
        else
        {
            $rows = "select * from [ord_unit_status] ous
                    where ous.id_order = ".$order['id']."
                    $query
                    ";
            $flag=1;
        }
        $_SESSION["user"]->db->select($rows);
        $unit = $_SESSION["user"]->db->fetchAllArrays();
        if($_SESSION["user"]->db->numrows() > 0)
        {
            $orders[$k]['ord_unit'] = $unit;
            $num++;
        }
    }
    if($flag==1)
        $cnt = $num;*/

return $cnt;
}
$select = "select * from [order] where status = 4 ";
$_SESSION["user"]->db->select($select);
$order_init = $_SESSION["user"]->db->fetchAllArrays();
initUnitStatus($order_init);

$data = "and status = 4
  $query
  $status
  ";
$_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER'] = ' c_status desc, p_status desc, i_status desc, id_order asc, id_ord_unit ';
$_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'] = 'ASC';
$cfgArr = array(
    'table'=>(isset($_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER']) && $_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER']!='')?$_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER']:'id',
    'dir'=>$_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'],
    'col_name' =>"*"
);
$sql = page_query_cur($data,'dbo.unit_status_select()',$cfgArr,isset($_GET['pg'])?($_GET['pg'])*10:null,$page);
$_SESSION["user"]->db->select($sql);
$orders = $_SESSION["user"]->db->fetchAllArrays();


//select all unit not done from table
/*$num=0;
$flag=0;
foreach($orders as $k=>$order) {
    if(strcmp($query, ' AND ') == 0 || $query=='')
    {
        $rows = "select * from [ord_unit_status] ous
            where ous.id_order = ".$order['id']."
            and ous.i_status < 2
            ";
    }
    else
    {
        $rows = "select * from [ord_unit_status] ous
            where ous.id_order = ".$order['id']."
            $query
            ";
        $flag=1;
    }
    $_SESSION["user"]->db->select($rows);
    $unit = $_SESSION["user"]->db->fetchAllArrays();
    if($_SESSION["user"]->db->numrows() > 0)
    {
        $orders[$k]['ord_unit'] = $unit;
        $num++;
    }
}*/
$cnt = getCnt($loc, $proj, $query);
if (isset($_GET['pg'])) $arrow = page_selector($page, $_GET['pg'], $cnt['cnt'], $script);
else $arrow = page_selector($page, '', $cnt['cnt'], $script);

if (isset($_GET['ord'])) { setOrder($_GET['ord']);   header('Location: ord_unit_status.php'); }

include('Smarty.class.php');
$smarty = new Smarty;
//H E A D E R - MENU  --------------------------------------------------
    $smarty->assign('user_profile',$_SESSION['user']->getUserName());
    global $css_color,$date_format;
    $smarty->assign('css_color',$css_color);
    $smarty->assign('time',date($date_format,time()));
    $smarty->assign('menulink',$_SESSION['user']->getMenuLink());
//----------------------------------------------------------------------
$insert = $_SESSION["user"]->getPrivilege('insert');
if ($insert) $smarty->assign('submenu',array(0=>array('name'=>'Unit Status Monitor','link'=>'ord_unit_monitor.php')));

$smarty->assign('cnt',$cnt);
$smarty->assign('paging',$arrow);

$smarty->assign('search',$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']);
$smarty->assign('error',$error);
$smarty->assign('alastid',time());
$smarty->assign('id',$_GET['id']);
$smarty->assign('search',$_SESSION['ORD_UNIT_STATUS_SESSION']['SEARCH']);

//Privileges
$smarty->assign('update',$_SESSION["user"]->getPrivilege('update'));
$smarty->assign('delete',$_SESSION["user"]->getPrivilege('delete'));
$smarty->assign('hist',$_SESSION["user"]->getPrivilege('history'));
//$smarty->assign('insert',$_SESSION["user"]->getPrivilege('insert'));
$smarty->assign('insert',$insert);

$smarty->assign('description',$_POST['description']);
$smarty->assign('order',$orders);
$smarty->assign('img',imageSelect());
$smarty->display('ord_unit_status.tpl');

?>