<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 4/9/14
 * Time: 12:09 PM
 */

function smarty_modifier_location($id){
    $loc = array(
        '2' => 'RP',
        '11' => 'PA',
        '12' => 'SP',
        '13' => 'CT',
        '15' => 'VT',
        '16' => 'FA'
    );
    if(is_numeric($id)){
        return $loc[$id];
    }

    return null;
}