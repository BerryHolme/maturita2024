<?php

namespace models;

use DB\Cortex;

class students extends Cortex
{
    protected $db='DB', $table ='students';

    protected $fieldConf=[
        'id' =>['type'=>'INT', 'nullable'=>false],
        'name' =>['type'=>'VARCHAR256','nullable' =>false],
        'surname' =>['type'=>'VARCHAR256','nullable' =>false],
        'phone'=>['type'=>'INT8','nullable' =>false],
        'commute'=>['type'=>'BOOLEAN','nullable' =>false],
    ];

}