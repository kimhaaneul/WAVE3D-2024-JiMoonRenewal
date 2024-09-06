<?php
session_start();
include_once("db.php");

if (!isset($_POST['name']) || empty(trim($_POST['name'])) || !isset($_POST['phone']) || empty(trim($_POST['phone']))) {
    header("Content-Type: text/html; charset=UTF-8");
    echo "<script>alert('이름 또는 전화번호가 빠졌거나 잘못된 접근입니다.');";
    echo "window.location.replace('adminLogin.php');</script>";
    exit;
}

$name = trim($_POST['name']);
$phone = trim($_POST['phone']);

$sql = "SELECT * FROM finger_reservation WHERE reserver_name = :name AND phone = :phone";
$stmt = $con->prepare($sql);
$stmt->bindParam(':name', $name, PDO::PARAM_STR);
$stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
$stmt->execute();
$count = $stmt->fetch(PDO::FETCH_BOTH);

if (!$count) {
    header("Content-Type: text/html; charset=UTF-8");
    echo "<script>alert('이름 또는 전화번호가 잘못되었습니다.');";
    echo "window.location.replace('adminLogin.php');</script>";
    exit;
} else {
    $_SESSION['idx'] = $count['idx'];
    $_SESSION['reserver_name'] = $count['reserver_name'];
    $_SESSION['phone'] = $count['phone'];
    $_SESSION['hand_s'] = $count['hand_s'];
    $_SESSION['gender'] = $count['gender'];
    $_SESSION['event_date'] = $count['event_date'];

    echo "<script>document.location.href='index.html';</script>";
    exit;
}
?>
