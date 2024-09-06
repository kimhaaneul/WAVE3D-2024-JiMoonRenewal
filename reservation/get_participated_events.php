<?php
session_start(); // 세션 시작

include_once("db.php");

header('Content-Type: application/json');

// 세션에서 코드 가져오기
$code = $_SESSION['code'] ?? null;

if (!$code) {
    echo json_encode([
        'status' => 'error',
        'message' => '유효하지 않은 사용자입니다.'
    ]);
    exit;
}

try {
    $records_per_page_events = 5;
    $current_page_events = isset($_GET['event_page']) ? intval($_GET['event_page']) : 1;
    $offset_events = ($current_page_events - 1) * $records_per_page_events;

    // 총 이벤트 수 가져오기
    $total_event_records_query = $con->prepare("SELECT COUNT(*) FROM reservation_support WHERE consultant_code = ?");
    $total_event_records_query->execute([$code]);
    $total_event_records = $total_event_records_query->fetchColumn();

    // 페이지 수 계산
    $total_event_pages = ceil($total_event_records / $records_per_page_events);

    // 이벤트 목록 가져오기, 날짜 기준 내림차순 정렬
    $stmt = $con->prepare("SELECT location, date, time, event_code 
                           FROM reservation_support 
                           WHERE consultant_code = ? 
                           ORDER BY date DESC 
                           LIMIT ? OFFSET ?");
    $stmt->bindParam(1, $code, PDO::PARAM_STR);
    $stmt->bindParam(2, $records_per_page_events, PDO::PARAM_INT);
    $stmt->bindParam(3, $offset_events, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 이벤트 목록에서 event_code가 null일 경우 "승인 대기"로 처리
    foreach ($events as &$event) {
        if (is_null($event['event_code'])) {
            $event['event_code'] = "승인 대기";
        }
    }

    // JSON 응답 생성
    $response = [
        'events' => $events,
        'total_event_pages' => $total_event_pages,
        'current_page_events' => $current_page_events,
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => '데이터를 가져오는 중 오류가 발생했습니다: ' . $e->getMessage(),
    ]);
}
?>
