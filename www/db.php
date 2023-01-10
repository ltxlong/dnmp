<?php

try {
    $con =new PDO("mysql:host=mysql;dbname=test","root","123456");
    echo"ok...";
} catch(PDOException $e) {
    echo $e->getMessage();
}
