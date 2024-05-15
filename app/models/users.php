<?php

namespace models;

use DB\Cortex;
class users extends Cortex
{
    protected $db='DB', $table ='users';
    protected $fieldConf=[
        'id' =>['type'=>'INT', 'nullable'=>false],
        'name' =>['type'=>'VARCHAR256','nullable' =>false],
        'surname' =>['type'=>'VARCHAR256','nullable' =>false],
        'email'=>['type'=>'VARCHAR256','nullable' =>false, 'index'=>true, 'unique'=>true, 'default'=>true],
        'password'=>['type'=>'VARCHAR256','nullable' =>false],
    ];

    protected function set_password($value)
    {
        return password_hash($value,PASSWORD_DEFAULT);
    }

}