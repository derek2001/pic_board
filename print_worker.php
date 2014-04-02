<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 4/1/14
 * Time: 9:18 AM
 */
include_once("config.php");
$_SESSION["user"]->db->connect('erp');

$sql = 'select * from [worker]';
$_SESSION["user"]->db->select($sql);
$worker = $_SESSION["user"]->db->fetchAllArrays();

include ('Smarty.class.php');
$smarty = new Smarty;

$smarty->assign('data', $worker);
$smarty->assign('css_color', $css_color);
$smarty->display('print/print_worker.tpl');
?>