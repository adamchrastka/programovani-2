<?php
$dsn = "mysql:host=localhost;dbname=chrastka;charset=utf8";
$username="testuser";
$password="testpass";
try{
    $db=new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    echo "uspesne pripojeni22";
}


catch (PDOException $e){
    echo"nelze se pripojit k db:22".$e->getmessage();
    
}