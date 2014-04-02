<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/28/14
 * Time: 4:33 PM
 */

function smarty_modifier_barcode($id, $width=40){
    return "<img src=\"/erp_old/a_workbarcode.php?uid={$id}&width={$width}\">";
}