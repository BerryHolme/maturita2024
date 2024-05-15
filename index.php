<?php
require 'vendor/autoload.php';
$f3 = \Base::instance();
$f3->config("./app/configs/config.ini");
$f3->set('DB', new \DB\SQL('mysql:host=localhost;dbname=maturita2024','root', ''));
$f3->run();