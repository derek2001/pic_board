<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 4/8/14
 * Time: 4:12 PM
 */

include_once("config.php");
include_once("functions/paging.php");

$_SESSION["user"]->db->connect('erp');
$loc = $_SESSION["user"]->getLocationProfile();

function imageSelect()
{
    return array($_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER']=>'<img src=\'gfx/'.$_SESSION['ORD_UNIT_STATUS_SESSION']['ORDER_DIR'].'.gif\'>');
}
//--------------------------------------------------------------------------------------------------------------------------------------------

$select = "select * from dbo.unit_status_select() where status = 4 and u_status = 1
    order by c_status desc, p_status desc, i_status desc, id_order asc, id_ord_unit asc";
$_SESSION["user"]->db->select($select);
$orders = $_SESSION["user"]->db->fetchAllArrays();

//select all unit not done from table

if (isset($_GET['ord'])) { setOrder($_GET['ord']);   header('Location: ord_unit_monitor.php'); }

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
$smarty->assign('error',$error);
$smarty->assign('alastid',time());
$smarty->assign('id',$_GET['id']);

//Privileges
$smarty->assign('update',$_SESSION["user"]->getPrivilege('update'));
$smarty->assign('delete',$_SESSION["user"]->getPrivilege('delete'));
$smarty->assign('hist',$_SESSION["user"]->getPrivilege('history'));
//$smarty->assign('insert',$_SESSION["user"]->getPrivilege('insert'));
$smarty->assign('insert',$insert);

$smarty->assign('description',$_POST['description']);
$smarty->assign('order',$orders);
$smarty->assign('img',imageSelect());
$smarty->display('ord_unit_monitor.tpl');

?>