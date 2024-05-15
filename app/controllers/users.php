<?php

namespace controllers;

class users
{
    public function getRegister(\Base $base)
    {
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $base->reroute("board/");
        }else{
            echo \Template::instance()->render("register.html");
        }

    }

    public function postRegister(\Base $base)
    {
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $base->clear("SESSION.user");
        }
        if (!$base->get('POST.name')) {
            echo "Jméno je prázdné";
            return;
        }
        if (!$base->get('POST.surname')) {
            echo "Příjmení je prázdné";
            return;
        }
        if (!$base->get('POST.email')) {
            echo "Email je prázný";
            return;
        }
        if (!$base->get('POST.password')) {
            echo "Heslo je prázdné";
            return;
        }

        $user = new \models\users();

        $existingUser = $user->findone(["email=?", $base->get('POST.email')]);

        if ($existingUser) {
            echo "Uživatel už existuje!";
            return;
        }
        $user->copyfrom($base->get('POST'));

        $user->save();
        $base->reroute("/");


    }

    public function postLogin(\Base $base)
    {
        if (!$base->get('POST.email')) {
            echo "Email je prázný";
            return;
        }
        if (!$base->get('POST.password')) {
            echo "Heslo je prázdné";
            return;
        }

        $email = $base->get("POST.email");
        $user = new \models\users();
        $base->clear("SESSION.user");
        $u = $user->findone(["email=?", $email]);

        if ($u) {
            if (password_verify($base->get('POST.password'), $u->password)) {
                $base->set("SESSION.user[id]", $u->id);
                $base->set("SESSION.user[name]", $u->name);
                $base->set("SESSION.user[surname]", $u->surname);
                $base->set("SESSION.user[email]", $u->email);

                $base->reroute("board/");
            } else {
                echo "Špatné heslo";
            }
        } else {
            echo "Uživatel neexistuje";
        }

    }

    public function logout(\Base $base)
    {

        $base->clear("SESSION.user");

        $base->reroute('/');

    }

}