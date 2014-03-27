<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/26/14
 * Time: 2:40 PM
 */

include_once("config.php");

$_SESSION["user"]->db->connect('erp');
$loc = $_SESSION["user"]->getLocationProfile();

//----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
$sql = "select * from [order] o
  where o.status in (3,4)
  and o.id_location = ".$loc['id']."
  ";
$_SESSION["user"]->db->select($sql);
$orders = $_SESSION["user"]->db->fetchAllArrays();
foreach($orders as $k=>$order) {
    $_SESSION["user"]->db->select("select ou.id id, ou.name from [ord_unit] ou
        inner join [order] o on o.id = ou.id_order
        where o.id = ".$order['0']."
        ");

    $unit = $_SESSION["user"]->db->fetchAllArrays();
    if($_SESSION["user"]->db->numrows() > 0)
    {
        $orders[$k]['ord_unit'] = $unit;
    }
}

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
if ($insert) $smarty->assign('submenu',array(0=>array('name'=>'New unit status','link'=>'unit_statusedit.php')));

$smarty->assign('error',$error);
$smarty->assign('alastid',time());
$smarty->assign('id',$_GET['id']);
$smarty->assign('search',$_SESSION['UNIT_STATUS_SESSION']['SEARCH']);

//Privileges
$smarty->assign('update',$_SESSION["user"]->getPrivilege('update'));
$smarty->assign('delete',$_SESSION["user"]->getPrivilege('delete'));
$smarty->assign('hist',$_SESSION["user"]->getPrivilege('history'));
//$smarty->assign('insert',$_SESSION["user"]->getPrivilege('insert'));
$smarty->assign('insert',$insert);

$smarty->assign('description',$_POST['description']);
$smarty->assign('order',$orders);
$smarty->display('ord_unit_status.tpl');

?>