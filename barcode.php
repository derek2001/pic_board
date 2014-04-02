<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/31/14
 * Time: 2:40 PM
 */

include_once("config.php");

abstract class UnitStatus{
    const notStart = 0;
    const inProcess = 1;
    const Finish = 2;
}

$_SESSION["user"]->db->connect('erp');
$loc = $_SESSION["user"]->getLocationProfile();

if ($_POST['cancel'] == 'CANCEL') {
    $_SESSION['BARCODE_SESSION']['BARCODE'] = $_POST;
    $uid = $_SESSION['BARCODE_SESSION']['BARCODE']['workorder'];
    $eid = $_SESSION['BARCODE_SESSION']['BARCODE']['employee'];
    $uid = substr($uid, 1, strlen($uid));
    $eid = substr($eid, 1, strlen($eid));

    $error = "The work order is not start yet. $uid. $eid";
    unset ($_POST);
}
else if($_POST['confirm'] == 'CONFIRM'){
    $_SESSION['BARCODE_SESSION']['BARCODE'] = $_POST;
    $uid = $_SESSION['BARCODE_SESSION']['BARCODE']['workorder'];
    $eid = $_SESSION['BARCODE_SESSION']['BARCODE']['employee'];
    unset ($_POST);
    $uid = substr($uid, 1, strlen($uid));
    $eid = substr($eid, 1, strlen($eid));
    $array = explode('-', $uid);

    $sql = "select id, (fname + ' ' + lname) as name, id_punch from [worker] w
            where w.id_punch = ".$eid."
            ";
    //check to see if the user exist
    $_SESSION["user"]->db->select($sql);
    $worker = $_SESSION["user"]->db->fetchArray();
    if($_SESSION["user"]->db->numrows() > 0)
    {
        $sql="select * from [ord_unit_status] ous where  ous.id_ord_unit = ".$array[0]."";
        $_SESSION["user"]->db->select($sql);
        if($_SESSION["user"]->db->numrows() > 0)
        {
            $unit = $_SESSION["user"]->db->fetchArray();
            if((int)($array[1]) == 0)
            {
                if((int)($unit['c_status']) == 0){
                    $sql_update = "update [ord_unit_status] set c_status=1, c_start_time = GETDATE(), c_operator = '".$worker['name']."' where id_ord_unit = ".$array[0]."";
                    if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                }
                elseif((int)($unit['c_status']) == 1){
                    $sql_update = "update [ord_unit_status] set c_status=2, c_fin_time = GETDATE() where id_ord_unit = ".$array[0]."";
                    if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                }elseif((int)($unit['c_status']) == 2)
                {
                    $error = "This stage is already done.";
                }
            }elseif((int)($array[1]) == 1){
                if((int)($unit['c_status']) == 2)
                {
                    if((int)($unit['p_status']) == 0){
                        $sql_update = "update ord_unit_status set p_status=1, p_start_time = GETDATE(), p_operator = '".$worker['name']."' where id_ord_unit = ".$array[0]."";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                        {
                            $error = "Saving data failed.";
                        }
                    }
                    elseif((int)($unit['p_status']) == 1){
                        $sql_update = "update ord_unit_status set p_status=2, p_fin_time = GETDATE() where id_ord_unit = ".$array[0]."";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                        {
                            $error = "Saving data failed.";
                        }
                    }elseif((int)($unit['p_status']) == 2)
                    {
                        $error = "This stage is already done.";
                    }
                }else{
                    $error = "Please finish template before start this stage.";
                }
            }elseif((int)($array[1]) == 2){
                if((int)($unit['p_status']) == 2)
                {
                    if((int)($unit['i_status']) == 0){
                        $sql_update = "update [ord_unit_status] set i_status=1, i_start_time = GETDATE(), i_operator = '".$worker['name']."' where id_ord_unit = ".$array[0]."";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                        {
                            $error = "Saving data failed.";
                        }
                    }
                    elseif((int)($unit['i_status']) == 1){
                        $sql_update = "update [ord_unit_status] set i_status=2, i_fin_time = GETDATE() where id_ord_unit = ".$array[0]."";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                        {
                            $error = "Saving data failed.";
                        }
                    }elseif((int)($unit['i_status']) == 2)
                    {
                        $error = "This stage is already done.";
                    }
                }else{
                    $error = "Please finish production before start this stage.";
                }
            }
        }
        else{
            $error = "This unit id doesn't exist.";
        }
    }
    else{
        $error = "This employee doesn't exist.";
    }

}

include('Smarty.class.php');
$smarty = new Smarty;
$smarty->assign('error',$error);

$smarty->display('barcode.tpl');    
   
?>
