<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 4/11/14
 * Time: 4:07 PM
 */
include_once("config.php");
$_SESSION["user"]->db->connect('erp');
$loc = $_SESSION["user"]->getLocationProfile();

$uid = isset($_GET['uid'])?$_GET['uid']:0;
$eid = isset($_GET['eid'])?$_GET['eid']:0;
$response="";
if($uid!=0){
    $query = "select * from ord_unit_status where status=1 and id_ord_unit=".$uid."";
    $_SESSION["user"]->db->select($query);
    $unit = $_SESSION['user']->db->fetchArray();
    if($_SESSION['user']->db->numrows() > 0){
        $response = "c_status=".$unit['c_status']."&p_status=".$unit['p_status'].
            "&i_status=".$unit['i_status']."&id_order=".$unit['id_order'];
    }
    if($response!=="")
        echo $response;
}elseif($eid!=0){
    $query = "select * from worker where id_punch=".$eid."";
    $_SESSION["user"]->db->select($query);
    $worker = $_SESSION['user']->db->fetchArray();
    if($_SESSION['user']->db->numrows() > 0){
        $response = $worker['fname']." ".$worker['lname'] ;
    }
    if($response!=="")
        echo $response;
}

