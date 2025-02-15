<?php
require_once '../../vendor/autoload.php';
require_once '../DB.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if ($_SERVER['REQUEST_METHOD'] === 'GET' &&
isset($_GET['id'])
) {
    $id = $_GET['id'];
    $productInfo = $DB->query(
        "SELECT * FROM products WHERE id = '$id'"
    )->fetchAll()[0];

    //записать в qr id товара, название, описание , цену
    $productName = $productInfo['name'];
    $productDesc = $productInfo['description'];
    $productPrice = $productInfo['price'];
    $qrText = "
    Ид товара : $id
    Название товар : $productName
    Описание товара : $productDesc
    Цена товара : $productPrice
    ";


    $qrCode = new QrCode($qrText);
    $writer = new PngWriter();
    $result = $writer->write($qrCode);
    header('Content-Type: '.$result->getMimeType());
    echo $result->getString();



    echo json_encode($productInfo);
}

?>