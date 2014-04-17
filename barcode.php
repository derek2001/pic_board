<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/31/14
 * Time: 2:40 PM
 */

include_once("config.php");
$_SESSION["user"]->db->connect('erp');
$loc = $_SESSION["user"]->getLocationProfile();

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
                $insert = "INSERT INTO [ord_unit_status]
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
$msg = "PLEASE SCAN YOUR WORK ORDER UNIT BARCODE.&#13;&#10;PLEASE SCAN YOUR WORK ORDER UNIT BARCODE.";
$msg = "EMPLOYEE ID NOT ACCEPTED. CUTTING IN PROGRESS.&#13;&#10;DO YOU WANT ADD EXTRA HELP? FOR YES PRESS CONFIRM.";
$select = "select * from [order] where status = 4 ";
$_SESSION["user"]->db->select($select);
$order_init = $_SESSION["user"]->db->fetchAllArrays();
initUnitStatus($order_init);

$id_worker=isset($_GET['id_worker'])?(int)$_GET['id_worker']:0;
$id_unit=isset($_GET['id_unit'])?(int)$_GET['id_unit']:0;
if($id_worker!=0 && $id_unit!=0){
    $query = "select * from ord_unit_status where status=1 and id_ord_unit=".$id_unit."";
    $_SESSION["user"]->db->select($query);
    $obUnit = $_SESSION['user']->db->fetchArray();
    if($_SESSION['user']->db->numrows() > 0){
        $id_order = $obUnit['id_order'];
        $update = "update [ord_unit_status] set status=0 where id_ord_unit=".$id_unit."";
        if(!$_SESSION["user"]->db->update('ord_unit_status', $obUnit['id'], $update))
        {
            $msg = "Saving data failed.";
        }

        $insert = "INSERT INTO [ord_unit_status]
                 VALUES
                   (".$id_order."
                   ,".$id_unit."
                   ,1 ,".$id_worker." ,GETDATE() ,null
                   ,0 ,0 ,null ,null
                   ,0 ,0 ,null ,null,1
                   )";
        $_SESSION["user"]->db->insert($insert);
        $msg="Procedure restart for Unit: ".$id_unit."";
        $msg='';
    }

}
elseif( (strcmp(($_POST['workorder']), '')==0 || strcmp(($_POST['employee']), '')==0)
        && $_POST['confirm'] == 'CONFIRM'){
    $msg = "PLEASE SCAN THE BARCODE FOR UNIT AND WORKER.";
}
else if($_POST['confirm'] == 'CONFIRM'){
    $_SESSION['BARCODE_SESSION']['BARCODE'] = $_POST;
    $uid = $_SESSION['BARCODE_SESSION']['BARCODE']['workorder'];
    $eid = $_SESSION['BARCODE_SESSION']['BARCODE']['employee'];
    $stage = (int)$_SESSION['BARCODE_SESSION']['BARCODE']['stage'];
    unset ($_POST);
    $msg = '';
    $eid = substr($eid, 3, strlen($eid));
    $array = explode(' ', $uid);

    $sql = "select id, (fname + ' ' + lname) as name, id_punch from [worker] w
            where w.id_punch = ".$eid."
            ";
    //check to see if the user exist
    $_SESSION["user"]->db->select($sql);
    $worker = $_SESSION["user"]->db->fetchArray();

    if($_SESSION["user"]->db->numrows() > 0)
    {
        $sql="select * from [ord_unit_status] ous where status=1 and ous.id_ord_unit = ".$array[3]." and ous.id_order = ".$array[1]." ";
        $_SESSION["user"]->db->select($sql);
        if($_SESSION["user"]->db->numrows() > 0)
        {
            $sql_update = '';
            $sql_insert = '';
            if($stage == 0)
            {
                //for table ord_unit_helper stage column,
                // 1 for cutting stage, 2 for production stage, 3 for installation stage.
                $unit = $_SESSION["user"]->db->fetchArray();
                if($unit['i_status']==1){
                    $sql_update = "update [ord_unit_status] set i_status=2, i_fin_time = GETDATE() where status=1 and id_ord_unit = ".$array[3]."";
                    $msg = "INSTALLATION STAGE FINISHED.";
                }elseif($unit['i_status']==0 && $unit['p_status']==2){
                    $sql_update = "update [ord_unit_status] set i_status=1, i_start_time = GETDATE(), i_operator = '".$worker['id_punch']."' where status=1 and id_ord_unit = ".$array[3]."";
                    $msg = "PLEASE START INSTALLATION.";
                }elseif($unit['p_status']==1){
                    if($unit['p_operator'] == $worker['id_punch'])
                    {
                        $sql_update = "update [ord_unit_status] set p_status=2, p_fin_time = GETDATE() where status=1 and id_ord_unit = ".$array[3]."";
                        $msg = "FABRICATION/CNC STAGE FINISHED.";
                    }else{
                        $sql_insert = "INSERT INTO [ord_unit_helper] VALUES(".$unit['id_order']." ,".$unit['id_ord_unit']." ,2 ,".$worker['id_punch'].")";
                        $msg = "HELPER ACCEPTED. PLEASE START FABRICATION/CNC.";
                    }
                }elseif($unit['p_status']==0 && $unit['c_status']==2){
                    $sql_update = "update [ord_unit_status] set p_status=1, p_start_time = GETDATE(), p_operator = '".$worker['id_punch']."' where status=1 and id_ord_unit = ".$array[3]."";
                    $msg = "PLEASE START FABRICATION/CNC.";
                }elseif($unit['c_status']==1){
                    if($unit['c_operator'] == $worker['id_punch'])
                    {
                        $sql_update = "update [ord_unit_status] set c_status=2, c_fin_time = GETDATE() where status=1 and id_ord_unit = ".$array[3]."";
                        $msg = "CUTTING STAGE FINISHED.";
                    }else{

                        $sql_insert = "INSERT INTO [ord_unit_helper] VALUES(".$unit['id_order']." ,".$unit['id_ord_unit']." ,1 ,".$worker['id_punch'].")";
                        $msg = "HELPER ACCEPTED. PLEASE START CUTTING.";
                    }
                }elseif($unit['c_status']==0){
                    $sql_update = "update [ord_unit_status] set c_status=1, c_start_time = GETDATE(), c_operator = '".$worker['id_punch']."' where status=1 and id_ord_unit = ".$array[3]."";
                    $msg = "PLEASE START CUTTING.";
                }

                if(strcmp($sql_update, '') != 0){
                    if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                    {
                        $msg = "SAVING DATA FAILED.";
                    }
                }

                if(strcmp($sql_insert, '') != 0){
                    $_SESSION["user"]->db->insert($sql_insert);
                }
            }
            else{

            }
        }
        else{
            $msg = "WORK ORDER BARCODE NOT ACCEPTED.&#13;&#10;PLEASE SCAN THE CORRECT WORK ORDER BARCODE ID.";
        }
    }else{
        $msg = "ID BARCODE NUMBER NOT ACCEPTED.&#13;&#10;PLEASE SCAN THE CORRECT ID BARCODE NUMBER.";
    }

}

include('Smarty.class.php');
$smarty = new Smarty;
$smarty->assign('error',$msg);
$smarty->assign('msg', $msg);
$smarty->assign('alert', $alert);
$smarty->assign('uid', $array[3]);
$smarty->assign('eid', $eid);

$smarty->display('barcode.tpl');    



/*
 *
 * if((int)($array[1]) == 0)
                {
                    if((int)($unit['c_status']) == 0){
                        $sql_update = "update [ord_unit_status] set c_status=1, c_start_time = GETDATE(), c_operator = '".$worker['id_punch']."' where status=1 and id_ord_unit = ".$array[3]."";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                        {
                            $error = "Saving data failed.";
                        }
                        else
                        {
                            $msg = "Start template procedure of Unit: ". $array[0];
                        }
                    }
                    elseif((int)($unit['c_status']) == 1){
                        $sql_update = "update [ord_unit_status] set c_status=2, c_fin_time = GETDATE() where status=1 and id_ord_unit = ".$array[0]."";
                        if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                        {
                            $error = "Saving data failed.";
                        }
                        else
                        {
                            $msg = "Template procedure of Unit: ".$array[0]." finished";
                        }
                    }elseif((int)($unit['c_status']) == 2)
                    {
                        $alert = "This stage is already done. Do you want to redo it?";
                    }
                }elseif((int)($array[1]) == 1){
                    if((int)($unit['c_status']) == 2)
                    {
                        if((int)($unit['p_status']) == 0){
                            $sql_update = "update ord_unit_status set p_status=1, p_start_time = GETDATE(), p_operator = '".$worker['id_punch']."' where status=1 and id_ord_unit = ".$array[0]."";
                            if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                            {
                                $error = "Saving data failed.";
                            }
                            else
                            {
                                $msg = "Start production procedure of Unit: ". $array[0];
                            }
                        }
                        elseif((int)($unit['p_status']) == 1){
                            $sql_update = "update ord_unit_status set p_status=2, p_fin_time = GETDATE() where status=1 and id_ord_unit = ".$array[0]."";
                            if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                            {
                                $error = "Saving data failed.";
                            }
                            else
                            {
                                $msg = "Production procedure of Unit: ".$array[0]." finished";
                            }
                        }elseif((int)($unit['p_status']) == 2)
                        {
                            $alert = "This stage is already done. Do you want to redo it?";
                        }
                    }else{
                        $error = "Please finish template before start this stage.";
                    }
                }elseif((int)($array[1]) == 2){
                    if((int)($unit['p_status']) == 2)
                    {
                        if((int)($unit['i_status']) == 0){
                            $sql_update = "update [ord_unit_status] set i_status=1, i_start_time = GETDATE(), i_operator = '".$worker['id_punch']."' where status=1 and id_ord_unit = ".$array[0]."";
                            if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                            {
                                $error = "Saving data failed.";
                            }
                            else
                            {
                                $msg = "Start installation procedure of Unit: ". $array[0];
                            }
                        }
                        elseif((int)($unit['i_status']) == 1){
                            $sql_update = "update [ord_unit_status] set i_status=2, i_fin_time = GETDATE() where status=1 and id_ord_unit = ".$array[0]."";
                            if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                            {
                                $error = "Saving data failed.";
                            }
                            else
                            {
                                $msg = "Installation procedure of Unit: ".$array[0]." finished";
                            }
                        }elseif((int)($unit['i_status']) == 2)
                        {
                            $alert = "This stage is already done. Do you want to redo it?";
                        }
                    }else{
                        $error = "Please finish production before start this stage.";
                    }
                }


 * elseif ($_POST['cancel'] == 'CANCEL') {
    $_SESSION['BARCODE_SESSION']['BARCODE'] = $_POST;
    $uid = $_SESSION['BARCODE_SESSION']['BARCODE']['workorder'];
    $eid = $_SESSION['BARCODE_SESSION']['BARCODE']['employee'];
    unset ($_POST);
    $error = '';
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
        $sql="select * from [ord_unit_status] ous where status=1 and ous.id_ord_unit = ".$array[0]."";
        $_SESSION["user"]->db->select($sql);
        if($_SESSION["user"]->db->numrows() > 0)
        {
            $unit = $_SESSION["user"]->db->fetchArray();
            if((int)($array[1]) == 0)
            {
                if((int)($unit['c_status']) == 0){
                    $error = "The template procedure haven't start yet.";
                }
                elseif((int)($unit['c_status']) == 1){
                    $sql_update = "update [ord_unit_status] set c_status=0, c_start_time = NULL, c_fin_time = NULL, c_operator = NULL where status=1 and id_ord_unit = ".$array[0]."";
                    if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                    else
                    {
                        $msg = "Template procedure of Unit: ".$array[0]." canceled";
                    }
                }elseif((int)($unit['c_status']) == 2)
                {
                    $error = "This stage is already done. To redo it click the CONFIRM button.";
                }
            }elseif((int)($array[1]) == 1){
                if((int)($unit['p_status']) == 0){
                    $error = "The production procedure haven't start yet.";
                }
                elseif((int)($unit['p_status']) == 1){
                    $sql_update = "update [ord_unit_status] set p_status=0, p_start_time = NULL, p_fin_time = NULL, p_operator = NULL where status=1 and id_ord_unit = ".$array[0]."";
                    if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                    else
                    {
                        $msg = "Production procedure of Unit: ".$array[0]." canceled";
                    }
                }elseif((int)($unit['p_status']) == 2)
                {
                    $error = "This stage is already done. To redo it click the CONFIRM button.";
                }
            }elseif((int)($array[1]) == 2){
                if((int)($unit['i_status']) == 0){
                    $error = "The installation procedure haven't start yet.";
                }
                elseif((int)($unit['i_status']) == 1){
                    $sql_update = "update [ord_unit_status] set i_status=0, i_start_time = NULL, i_fin_time = NULL, i_operator = NULL where status=1 and id_ord_unit = ".$array[0]."";
                    if(!$_SESSION["user"]->db->update('ord_unit_status', $unit['id'], $sql_update))
                    {
                        $error = "Saving data failed.";
                    }
                    else
                    {
                        $msg = "Installation procedure of Unit: ".$array[0]." canceled";
                    }
                }elseif((int)($unit['i_status']) == 2)
                {
                    $error = "This stage is already done. To redo it click the CONFIRM button.";
                }
            }
        }
        else{
            $error = "This unit id doesn't exist.";
        }
    }else{
        $error = "This employee doesn't exist.";
    }
}
 */
?>
