<?php

/**
 * AnyBase - create a base numbering system to meet your needs.  
 * Designed to create short urls based on a decimal id number - the default
 * charset contains the unreserved URL characters and is good up to base65.  
 *
 * @author Michael Richey
 */
class AnyBase {
    private $charset;
    private $base;
    function __construct($charset="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_~") {
        $this->charset = $charset;
        $this->base = strlen($charset);
    }
    function encode($n) {
        $converted = "";
        while ($n > 0) {
            $converted = substr($this->charset, ($n % $this->base), 1) . $converted;
            $n = floor($n / $this->base);
        }
        return $converted;        
    }
    function decode($n) {
        $c = 0;
        for ($i = strlen($n); $i; $i--) {
            $c += strpos($this->charset, substr($n, (-1 * ( $i - strlen($n) )), 1)) * pow($this->base, $i - 1);
        }
        return $c;        
    }
}
