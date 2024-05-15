<?php

namespace models;

use DB\Cortex;

class record extends Cortex
{
    protected $db='DB', $table ='record';

    protected $fieldConf=[
        'id' =>['type'=>'INT', 'nullable'=>false],
        'date' =>['type'=>'DATE','nullable' =>false],
        'student' =>['belongs-to-one'=>'\models\students'],
        'hours'=>['type'=>'INT8','nullable' =>false],
        'excused'=>['type'=>'BOOLEAN','nullable' =>false],
    ];

}