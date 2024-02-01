<?php

/*  @copyright Copyright (C) 2013 - 2018 Michael Richey. All rights reserved.
 *  @license GNU General Public License version 3 or later
 */
abstract class decodeStateData {
 
    public static function slides($data) {
        $matches = array();
        $items = array();
        $dataregex = '/100(?P<end>[0-9a-zA-Z_$]{4,5}(?=[0-9a-zA-Z_$~]+010))(.*?)(?=010)(?P<data>(.*?)\k<end>)/';
        $itemregex = '/0(?=[0-9a-zA-Z_$])(?P<item>[0-9a-zA-Z_$]{4,}?(?=(0|$)))/';
        preg_match($dataregex, $data, $matches);
        preg_match_all($itemregex, $matches['data'], $items);
        return $items['item'];
    }
    
    public static function translateSlides($slides) {
        require_once(__DIR__ . '/anyBase.php');
        $a64 = new AnyBase('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_$');
        $r = array();
        foreach($slides as $slide) {
        switch(strlen($slide)) {
            case 4:
            list($page64,$section64) = str_split($slide,2);
            break;
            default: // anything else
            $section64 = substr($slide,-2);
            $page64 = substr($slide,0,2);
            break;
        }
        $r[$slide] = array('section'=>($a64->decode($section64)-65),'page'=>($a64->decode($page64)-63));
    }
        return $r;
    }
}
