<?php

namespace controllers;

class gradebook
{
    public function getGradebook()
    {
        echo \Template::instance()->render("gradebook.html");
    }

}