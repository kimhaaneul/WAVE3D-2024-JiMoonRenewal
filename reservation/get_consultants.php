<?php
include_once("db.php");

header('Content-Type: application/json'); // JSON 응답으로 설정

// 데이터베이스 연결 확인
if (!$con) {
    echo json_encode(['status' => 'error', 'message' => '데이터베이스 연결에 실패했습니다.']);
    exit();
}

if (isset($_POST['event_code'])) {
    try {
        $eventCode = $_POST['event_code'];

        // 입력값 유효성 검사
        if (empty($eventCode)) {
            echo json_encode(['status' => 'error', 'message' => '이벤트 코드는 필수 입력 항목입니다.']);
            exit();
        }

        // 쿼리 실행 시 collation을 명시적으로 지정
        $query = "SELECT c.name, c.phone_number, e.consultant_code 
                  FROM event_list e 
                  JOIN consultant c ON e.consultant_code COLLATE utf8_unicode_ci = c.consultant_code COLLATE utf8_unicode_ci 
                  WHERE e.event_code COLLATE utf8_unicode_ci = :event_code COLLATE utf8_unicode_ci";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':event_code', $eventCode, PDO::PARAM_STR);
        $stmt->execute();
        $consultants = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'consultants' => $consultants]);
        exit();

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => '데이터베이스 오류: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => '유효하지 않은 요청입니다.']);
    exit();
}
?>
