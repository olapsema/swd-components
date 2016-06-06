<?php

$a100 = [];
$a1000 = [];
$a10000 = [];

for($i=0;$i<100;$i++){
    $a100[] = array(
        'id' => $i,
        'name' => 'name_'.$i
    );
}

for($i=0;$i<1000;$i++){
    $a1000[] = array(
        'id' => $i,
        'name' => 'name_'.$i
    );
}

for($i=0;$i<10000;$i++){
    $a10000[] = array(
        'id' => $i,
        'name' => 'name_'.$i
    );
}
for($i=0;$i<100000;$i++){
    $a100000[] = array(
        'id' => $i,
        'name' => 'name_'.$i
    );
}
//for($i=0;$i<1000000;$i++){
    //$a1000000[] = array(
        //'id' => $i,
        //'name' => 'name_'.$i
    //);
//}


return [
    //[   'name' => '100',
        //'data' => $a100,
        //'times' => 100000,
    //],
    [   'name' => '1000',
        'data' => $a1000,
        'times' => 10000,
    ],
    [   'name' => '10000',
        'data' => $a10000,
        'times' => 1000,
    ],
    [   'name' => '100000',
        'data' => $a100000,
        'times' => 1000,
    ],
    //[   'name' => '1000000',
        //'data' => $a1000000,
        //'times' => 1000,
    //],
];
