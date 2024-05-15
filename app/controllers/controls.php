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

        \models\students::setdown();
        \models\students::setup();

        \models\record::setdown();
        \models\record::setup();

        echo "Nainstalováno!";
    }

}