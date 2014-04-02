<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/26/14
 * Time: 10:14 AM
 */

include_once("config.php");
include("Barcode39.php");

    $id = $_GET['uid'];
    $code_length = (int)$_GET['width'];
    $bc = new Barcode39($id);
    $bc->draw();
?>