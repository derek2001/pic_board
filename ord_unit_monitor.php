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

//--------------------------------------------------------------------------------------------------------------------------------------------

$select = "select ous.id_order, ous.id_ord_unit, ous.c_status, ous.c_operator, DATEDIFF(minute, ous.c_start_time, GETDATE()) as time , f.sign, UPPER(st.name) as sname, (fname+' '+lname) as name, o.id_location
    from ord_unit_status ous
    inner join ord_slab os on ous.id_ord_unit = os.id_ord_unit
    inner join slab_frame sf on os.id_slab_frame = sf.id
    inner join frame f on f.id = sf.id_frame
    inner join slab s on s.id = sf.id_slab
    inner join stone st on st.id = s.id_stone
    inner join worker w on w.id_punch = ous.c_operator
    inner join [order] o on o.id = ous.id_order
    where ous.status = 1 and c_status = 1
    order by id_order asc, id_ord_unit asc";
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
$smarty->display('ord_unit_monitor.tpl');

?>