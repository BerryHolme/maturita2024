<?php

namespace controllers;

class controls
{
    public function index(\Base $base)
    {
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $base->reroute("board/");
        }else{
            echo \Template::instance()->render("index.html");
        }
    }

    public function install(\Base $base)
    {
        $password = "$2y$10$7ny2BYRHjB4o56ty2Ji4GeBdDLpWFBQ1td/ZHChnM9Tfkh7UcJAv2";


        if (password_verify($base->get('POST.password'), $password)) {
            \models\users::setdown();
            \models\users::setup();

            \models\students::setdown();
            \models\students::setup();

            \models\record::setdown();
            \models\record::setup();

            echo "Nainstalováno!";
        }else{
            echo "Špatné heslo";
        }


    }

    public function getInstall()
    {
        echo \Template::instance()->render("install.html");
    }

}