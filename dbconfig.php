<?php
$user  = 'uxmt5ayudvbi2bwl';
$pass = 'pOvggjud5fzSCUU32hm8';
try {
    $conn = new PDO('mysql:host=btsj8anrhqktyxzqv0jx-mysql.services.clever-cloud.com;port:3306;dbname=btsj8anrhqktyxzqv0jx;', $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "error : " . $e->getMessage() . "<br/>";
    die();
}
