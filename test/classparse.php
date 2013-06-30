<?php

class Mia {
    function msja() {
        return "EXITO A";
    }
    function msjb() {
        return "EXITO B";
    }
}


$content = file_get_contents('doc.txt');

echo $content;

/*

echo "<br>";
echo "<br>";

$obj = new Mia;

foreach($result[0] as $res) {
    $tx = $res;
    $or = $res;
    $tx = str_replace(' ', '', $tx); #quitar espacio en blanco
    $tx = str_replace('<#', '', $tx); #quitar prefijo
    $tx = str_replace('#>', '', $tx); #quitar posfijo
    $ar = explode('::', $tx);
    $val = $obj->$ar[1]();
    $content = str_replace($or, $val, $content);
}

echo $content;
*/
/*
$tx = $result[0][0][0];
$or = $result[0][0][0];
$tx = str_replace(' ', '', $tx); #quitar espacio en blanco
$tx = str_replace('<#', '', $tx); #quitar prefijo
$tx = str_replace('#>', '', $tx); #quitar posfijo
$ar=explode('::', $tx);
print_r($ar);
echo "<br>";echo "<br>";

$a = new Mia;
$b = $ar[1];
$t = $a->$b();
$content = str_replace($or, $t, $content);
echo $content;
*/
$obj = new Mia;

function obj_key_val($content, $result, $obj) {
    foreach($result as $res) {
        $tx = $res;
        $or = $res;
        $tx = str_replace(' ', '', $tx); #quitar espacio en blanco
        $tx = str_replace('<#', '', $tx); #quitar prefijo
        $tx = str_replace('#>', '', $tx); #quitar posfijo
        $ar = explode('::', $tx);
        $val = $obj->$ar[1]();
        $content = str_replace($or, $val, $content);
    }
    return $content;
}


if (preg_match_all('/<#(.+?)::(.+?)#>/', $content, $result) != 0) {
    echo obj_key_val($content, $result[0], $obj);//despues remplazar obj por array
}


 
?>



