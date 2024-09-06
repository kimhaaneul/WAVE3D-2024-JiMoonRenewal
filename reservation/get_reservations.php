<?php
include_once("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $event_code = $_GET['event_code'] ?? null;
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $records_per_page = 5; // 페이지당 표시할 레코드 수
    $offset = ($page - 1) * $records_per_page;

    if ($event_code) {
        try {
            // 총 예약자 수를 가져옴
            $count_stmt = $con->prepare("SELECT COUNT(*) FROM finger_reservation WHERE event_code = ?");
            $count_stmt->execute([$event_code]);
            $total_records = $count_stmt->fetchColumn();

            // 예약자 목록을 페이징 처리하여 가져옴
            $stmt = $con->prepare("SELECT * FROM finger_reservation WHERE event_code = ? ORDER BY idx ASC LIMIT ? OFFSET ?");
            $stmt->bindParam(1, $event_code, PDO::PARAM_STR);
            $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
            $stmt->bindParam(3, $offset, PDO::PARAM_INT);
            $stmt->execute();
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 데이터와 페이지 정보를 JSON으로 반환
            $response = [
                'reservations' => $reservations,
                'total_pages' => ceil($total_records / $records_per_page),
                'current_page' => $page
            ];
            echo json_encode($response);

        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request.']);
    }
}
