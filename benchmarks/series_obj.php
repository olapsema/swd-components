<?php

class TestX {

    protected $id;
    public $name;

    function __construct($id,$name){
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}

$a1000 = [];
$a10000 = [];
$a100000 = [];

for($i=0;$i<1000;$i++){
    $a1000[] = new TestX($i,'name_'.$i);
}

for($i=0;$i<10000;$i++){
    $a10000[] =  new TestX($i,'name_'.$i);
}
for($i=0;$i<100000;$i++){
    $a100000[] =  new TestX($i,'name_'.$i);
}


return [
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
];
