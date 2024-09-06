<?php
include_once("db.php");

$response = ['status' => 'error', 'message' => '유효하지 않은 요청입니다.', 'name' => '', 'phone_number' => ''];

// 데이터베이스 연결 확인
if (!$con) {
    $response['message'] = '데이터베이스 연결에 실패했습니다.';
    echo json_encode($response);
    exit();
}

if (isset($_POST['event_code']) && isset($_POST['consultant_code'])) {
    $eventCode = $_POST['event_code'];
    $consultantCode = $_POST['consultant_code'];

    // 입력값 유효성 검사
    if (empty($eventCode) || empty($consultantCode)) {
        $response['message'] = '이벤트 코드와 컨설턴트 코드는 필수 입력 항목입니다.';
        echo json_encode($response);
        exit();
    }

    try {
        // 트랜잭션 시작
        $con->beginTransaction();

        // 컨설턴트 정보 조회
        $stmt = $con->prepare("SELECT name, phone_number FROM consultant WHERE consultant_code COLLATE utf8_unicode_ci = :consultant_code COLLATE utf8_unicode_ci");
        $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);
        $stmt->execute();

        $consultant = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($consultant) {
            // 이벤트에 이미 등록된 컨설턴트인지 확인
            $stmt = $con->prepare("SELECT COUNT(*) FROM event_list WHERE event_code COLLATE utf8_unicode_ci = :event_code COLLATE utf8_unicode_ci AND consultant_code COLLATE utf8_unicode_ci = :consultant_code COLLATE utf8_unicode_ci");
            $stmt->bindParam(':event_code', $eventCode, PDO::PARAM_STR);
            $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);
            $stmt->execute();

            $exists = $stmt->fetchColumn();

            if ($exists) {
                $response['message'] = '이미 등록된 컨설턴트입니다.';
            } else {
                // 컨설턴트 추가
                $stmt = $con->prepare("INSERT INTO event_list (event_code, consultant_code) VALUES (:event_code, :consultant_code)");
                $stmt->bindParam(':event_code', $eventCode, PDO::PARAM_STR);
                $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = '컨설턴트 코드가 성공적으로 추가되었습니다.';
                    $response['name'] = $consultant['name'];
                    $response['phone_number'] = $consultant['phone_number'];
                    $con->commit(); // 트랜잭션 커밋
                } else {
                    $response['message'] = '컨설턴트 코드 추가에 실패했습니다.';
                    $con->rollBack(); // 트랜잭션 롤백
                }
            }
        } else {
            $response['message'] = '유효하지 않은 컨설턴트 코드입니다.';
            $con->rollBack(); // 트랜잭션 롤백
        }
    } catch (PDOException $e) {
        $con->rollBack(); // 예외 발생 시 트랜잭션 롤백
        $response['message'] = '데이터베이스 오류: ' . $e->getMessage();
    }
}

echo json_encode($response);  // JSON 형식으로 응답
?>
