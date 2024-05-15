<?php

namespace controllers;

class users
{
    public function getRegister(\Base $base)
    {
        echo \Template::instance()->render("register.html");
    }

    public function postRegister(\Base $base)
    {
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $base->clear("SESSION.user");
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
        $user = $base->get("SESSION.user[id]");
        if ($user) {
            $base->clear("SESSION.user");
        }
        $base->reroute('/');

    }

}