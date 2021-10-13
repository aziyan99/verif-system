<?php
header("Access-Control-Allow-Origin: *");
require_once('dbconfig.php');
session_start();
function utf8ize($d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string($d)) {
        return utf8_encode($d);
    }
    return $d;
}

// 1 = address document, 2 = idcard, 3 = passport

$json = file_get_contents('php://input');
$jsonObjc = json_decode($json);
$file = uniqid() . ".jpeg";
file_put_contents("uploads/" . $file, file_get_contents($jsonObjc->data));
$data = [];
$sql = "";
if ($jsonObjc->documentType == "1") {
    $data = [
        'user_id' => $_SESSION['userId'],
        'address_document' => $file
    ];
    $sql = "UPDATE documents_verifications SET address_document=:address_document WHERE user_id=:user_id";
} else if ($jsonObjc->documentType == "2") {
    $data = [
        'user_id' => $_SESSION['userId'],
        'idcard_document' => $file
    ];
    $sql = "UPDATE documents_verifications SET idcard_document=:idcard_document WHERE user_id=:user_id";
} else {
    $data = [
        'user_id' => $_SESSION['userId'],
        'passport_document' => $file
    ];
    $sql = "UPDATE documents_verifications SET passport_document=:passport_document WHERE user_id=:user_id";
}
$stmt = $conn->prepare($sql);
$res = $stmt->execute($data);

// header('location: verification.php');

$res = [
    'data' => $jsonObjc->data,
    'documentType' => $jsonObjc->documentType
];



echo json_encode(utf8ize($res));
