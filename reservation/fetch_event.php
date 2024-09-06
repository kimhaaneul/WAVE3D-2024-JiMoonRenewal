<?php
session_start();
include 'db.php';

// 현재 로그인한 사용자의 ID를 가져옵니다.
$consultant_id = $_SESSION['id'];

try {
    // 이벤트 정보를 가져옵니다.
    $stmt = $con->prepare("SELECT * FROM cms.reservation_support WHERE consultant_id = :consultant_id ORDER BY date DESC");
    $stmt->bindParam(':consultant_id', $consultant_id);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $eventArray = [];

    foreach ($events as $event) {
        $eventArray[] = [
            'title' => $event['location'],
            'start' => $event['date'] . 'T' . $event['time'], // FullCalendar가 인식할 수 있는 날짜 및 시간 형식
            'url'   => 'reservation.php?event_code=' . $event['event_code'] // 이벤트 클릭 시 이동할 URL
        ];
    }

    // JSON 형식으로 데이터를 반환합니다.
    echo json_encode($eventArray);

} catch (PDOException $e) {
    echo json_encode(['error' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>
