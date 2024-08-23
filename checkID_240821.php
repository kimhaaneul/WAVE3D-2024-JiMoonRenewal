<?php
include_once("db_240821.php");

// 데이터가 전달되었는지 확인
if (isset($_POST['memberId'])) {
    $memberId = $_POST['memberId'];

    // 기본적인 유효성 검증
    if (empty($memberId) || strlen($memberId) < 4 || strlen($memberId) > 12) {
        echo json_encode(['res' => 'invalid', 'message' => '아이디는 4~12자여야 합니다.']);
        exit;
    }

    $sql = "select * from consultant where id='".$memberId."'";
    $stmt = $con->prepare($sql);
    $stmt->bindParam(':id', $memberId, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch result
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    // 결과에 따라 응답 반환
    if ($member) {
        echo json_encode(['res' => 'bad', 'message' => '이미 존재하는 아이디입니다.']);
    } else {
        echo json_encode(['res' => 'good', 'message' => '사용 가능한 아이디입니다.']);
    }
} else {
    echo json_encode(['res' => 'error', 'message' => '데이터가 전송되지 않았습니다.']);
}