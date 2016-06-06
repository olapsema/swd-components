<?php
require __DIR__.'/../tests/bootstrap_autoload.php';

use Swd\Component\Utils\Arrays\ArrayMolder as AM;

//$series =  include_once(__DIR__.'/series_array.php');
$series =  include_once(__DIR__.'/series_obj.php');

$res = [];
foreach($series as $test){
    $res[$test['name']] = [];
    printf("run %s\n",$test['name']);

    for($i=0;$i<$test['times'];$i++){
        $s = microtime(true);
        AM::index($test['data']);
        $res[$test['name']][] = microtime(true) - $s;
    }
}

foreach($res as $name => $timers){
    $avg = array_sum($timers)/count($timers);
    $min = array_reduce($timers,'min',0);
    $max = array_reduce($timers,'max',0);

    printf("result  %s: %.4f / %.4f / %.4f \n",$name,$min,$avg,$max);
}

