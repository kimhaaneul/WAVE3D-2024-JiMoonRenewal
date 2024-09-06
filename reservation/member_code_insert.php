<?php

$host = "localhost";
$username = "root";
$password = "0000"; # MySQL 계정 패스워드
// $password = "1234"; # MySQL 계정 패스워드
$dbname = "cms"; # DATABASE 이름

$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

try {
    $con = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Failed to connect to the database: " . $e->getMessage());
}

$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
    function undo_magic_quotes_gpc(&$array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                undo_magic_quotes_gpc($value);
            } else {
                $value = stripslashes($value);
            }
        }
    }

    undo_magic_quotes_gpc($_POST);
    undo_magic_quotes_gpc($_GET);
    undo_magic_quotes_gpc($_COOKIE);
}

// finger_info 테이블의 모든 행을 선택
$query = 'SELECT idx FROM finger_info';
$stmt = $con->query($query);
$rows = $stmt->fetchAll();

$base_code = 'KORSUA01JM0025';

// 각 행에 대해 member_code 업데이트
$con->beginTransaction();
try {
    foreach ($rows as $index => $row) {
        $member_code = $base_code . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
        $updateQuery = 'UPDATE finger_info SET member_code = :member_code WHERE idx = :id';
        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->execute([':member_code' => $member_code, ':id' => $row['idx']]);
    }
    $con->commit();
    echo "All member_code values have been updated successfully.";
} catch (Exception $e) {
    $con->rollBack();
    echo "Failed to update member_code values: " . $e->getMessage();
}
?>
