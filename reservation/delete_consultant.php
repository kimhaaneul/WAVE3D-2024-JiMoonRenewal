<?php
header('Content-Type: application/json'); // JSON 응답 헤더 설정

include_once('db.php');

// 데이터베이스 연결 확인
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => '데이터베이스 연결에 실패했습니다.']);
    exit();
}

if (isset($_POST['event_code']) && isset($_POST['consultant_code'])) {
    $eventCode = $_POST['event_code'];
    $consultantCode = $_POST['consultant_code'];

    // 입력값 유효성 검사
    if (empty($eventCode) || empty($consultantCode)) {
        echo json_encode(['status' => 'error', 'message' => '이벤트 코드와 컨설턴트 코드는 필수 입력 항목입니다.']);
        exit();
    }

    try {
        // 삭제 쿼리 실행, collation 명시적으로 지정
        $stmt = $con->prepare("DELETE FROM event_list WHERE event_code COLLATE utf8_unicode_ci = :event_code COLLATE utf8_unicode_ci AND consultant_code COLLATE utf8_unicode_ci = :consultant_code COLLATE utf8_unicode_ci");
        $stmt->bindParam(':event_code', $eventCode, PDO::PARAM_STR);
        $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => '삭제가 완료되었습니다.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => '삭제에 실패했습니다.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '유효하지 않은 요청입니다.']);
}
exit();
?>
