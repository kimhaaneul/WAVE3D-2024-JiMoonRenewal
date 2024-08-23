<?php
include "db_240821.php";

$id = $_POST['id'];
$password = $_POST['pw'];
$name = $_POST['name'];
$ph = $_POST['ph'];
$address = $_POST['address'];
$job = $_POST['job'];
$email1 = $_POST['email']; 
$position = $_POST['position']; 

// 이메일 합치기
if (empty($_POST['str_email02'])){
    $email2 = $_POST['selectEmail'];
} else {
    $email2 = $_POST['str_email02'];
}
$e_mail = $email1 . "@" . $email2;

// 사용자 입력한 인증 코드
$input_verification_code = $_POST['verify-code'];

// 세션에 저장된 인증 코드와 비교
session_start();
if ($_SESSION['verification_code'] !== $input_verification_code) {
    echo "<script>
        alert('인증 코드가 일치하지 않습니다.');
        history.back();
    </script>";
    exit;
}

try {
    $sql = "INSERT INTO `consultant` (id, password, name, phone_number, position, e_mail, address, job) VALUES ('$id', '$password', '$name', '$ph', '$position', '$e_mail', '$address', '$job')";

    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt === false) {
        echo "<script>
            alert('error');
            history.back();
        </script>";
    } else {
        echo "<script>
            window.location.href='BNS_register_complete.php?id=$id';
        </script>";
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}