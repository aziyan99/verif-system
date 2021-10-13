<?php
$user  = 'root-local';
$pass = 'root-local';
try {
    $conn = new PDO('mysql:host=localhost;dbname=verif_system;', $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "error : " . $e->getMessage() . "<br/>";
    die();
}
