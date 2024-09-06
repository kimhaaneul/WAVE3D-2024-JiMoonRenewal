<?php
session_start();
include 'db.php';

$consultant_id = $_SESSION['id']; // 세션에서 로그인한 사용자 ID를 가져옵니다.

// 페이지 번호와 페이지당 표시할 항목 수를 가져옵니다.
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 5; // 한 페이지에 표시할 항목 수
$offset = ($page - 1) * $items_per_page; // 시작 인덱스 계산

// 해당 페이지에 맞는 이벤트 정보 가져오기, 날짜를 기준으로 내림차순 정렬
$stmt = $con->prepare("SELECT * FROM cms.reservation_support 
                       WHERE consultant_id = :consultant_id 
                       ORDER BY date DESC 
                       LIMIT :offset, :items_per_page");
$stmt->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 총 이벤트 개수 가져오기
$countStmt = $con->prepare("SELECT COUNT(*) FROM cms.reservation_support WHERE consultant_id = :consultant_id");
$countStmt->bindParam(':consultant_id', $consultant_id, PDO::PARAM_INT);
$countStmt->execute();
$total_items = $countStmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

 // 이벤트 목록에서 event_code가 null일 경우 "승인 대기"로 처리
 foreach ($events as &$event) {
    if (is_null($event['event_code'])) {
        $event['event_code'] = "승인 대기";
    }
}
// 결과를 JSON 형식으로 반환
echo json_encode([
    'status' => 'success',
    'data' => $events,
    'total_pages' => $total_pages
]);
