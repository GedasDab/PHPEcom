<?php
    ob_start();
    session_start();
    //session_destroy();
    defined("DS") ? null : define("DS", DIRECTORY_SEPARATOR);
    
    // echo __DIR__ . '</br>';
    // echo __FILE__;
    
    defined("TEMPLATE_FRONT") ? null : define("TEMPLATE_FRONT", __DIR__ . DS . "templates".DS."front");
    defined("TEMPLATE_BACK") ? null : define("TEMPLATE_BACK", __DIR__ . DS . "templates".DS."back");
    //Send photos
    defined("UPLOAD_DIRECTORY") ? null : define("UPLOAD_DIRECTORY", __DIR__ . DS . "images");
    //C:\xampp\htdocs\Ecom_Check_it\resources 
    //echo __DIR__;
    //echo UPLOAD_DIRECTORY;
    //C:\xampp\htdocs\Ecom_Check_it\resources\uploads 
    //C:\xampp\htdocs\Ecom_Check_it\resources\templates\back 
    //echo TEMPLATE_BACK;

    //echo dirname(__FILE__);
    //C:\xampp\htdocs\Ecom_Check_it\resources 
    
    defined("DB_HOST") ? null : define("DB_HOST", "localhost");
    defined("DB_USER") ? null : define("DB_USER","root");
    defined("DB_PASS") ? null : define("DB_PASS", "");
    defined("DB_NAME") ? null : define("DB_NAME",  "ecom_db");

    $connection = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

    require_once("functions.php");
    require_once("cart.php");
?>