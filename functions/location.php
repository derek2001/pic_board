<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 4/9/14
 * Time: 11:35 AM
 */

function location($id){
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

