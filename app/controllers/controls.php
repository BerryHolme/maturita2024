<?php

namespace controllers;

class controls
{
    public function index(\Base $base)
    {
        echo \Template::instance()->render("index.html");
    }

    public function install()
    {
        \models\users::setdown();
        \models\users::setup();

        echo "Nainstalováno!";
    }

}