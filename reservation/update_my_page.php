<?php
session_start();
include_once("db.php");

$id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? null;
    $phone_number = $_POST['phone_number'] ?? null;
    $e_mail = $_POST['e_mail'] ?? null;
    $address = $_POST['address'] ?? null;

    try {
        $stmt = $con->prepare("UPDATE consultant SET name = ?, phone_number = ?, e_mail = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone_number, $e_mail, $address, $id]);

        echo "정보가 성공적으로 수정되었습니다.";
    } catch (PDOException $e) {
        die("정보 수정 중 오류 발생: " . $e->getMessage());
    }
}
?>
