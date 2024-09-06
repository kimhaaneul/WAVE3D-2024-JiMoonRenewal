<?php
session_start();
include_once("db.php");

function generateMemberCode($event_code, $con) {
    // 이벤트 코드에서 앞 8자리 추출
    $prefix = substr($event_code, 0, 8);

    // 현재 년월 구하기
    $yearMonth = date('ym');

    // 순차 증가 번호 생성
    $stmt = $con->prepare("SELECT MAX(SUBSTR(member_code, -4)) AS max_code FROM finger_reservation WHERE member_code LIKE ? COLLATE utf8_general_ci");
    $stmt->execute([$prefix . '%']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $sequence = str_pad((int)$result['max_code'] + 1, 4, '0', STR_PAD_LEFT);

    // 회원 코드 생성 (유니코드 4바이트 문자가 없도록)
    return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $prefix . 'JM' . $yearMonth . $sequence);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_code = $_POST['event_code'];  // 여기에 문제가 있을 가능성 점검
    $reservation_time = date('Y-m-d H:i:s');

    try {
        $con->beginTransaction();

        // 이벤트 코드로 예약 정보를 가져오기
        $stmt = $con->prepare("SELECT location FROM reservation_support WHERE event_code = ? COLLATE utf8_general_ci");
        $stmt->execute([$event_code]);
        $reservation_support = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reservation_support) {
            throw new Exception("유효하지 않은 이벤트 코드입니다.");
        }

        $location = $reservation_support['location'];

        // 회원 코드 생성
        $member_code = generateMemberCode($event_code, $con);

        // 예약자 정보 삽입
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $hand_s = $_POST['hand_s'];
        $gender = $_POST['gender'];
        $birth_date = $_POST['birth_date'];
        $mbti = $_POST['mbti'];

        $stmt = $con->prepare("INSERT INTO finger_reservation (event_code, member_code, reserver_name, phone, hand_s, gender, birth_date, mbti, reservation_time, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$event_code, $member_code, $name, $phone, $hand_s, $gender, $birth_date, $mbti, $reservation_time, $location]);
        $lastInsertedId = $con->lastInsertId();

        $con->commit();
        $_SESSION['reservation_id'] = $lastInsertedId;
        header('Location: reservation_complete.php');
        exit;
    } catch (Exception $e) {
        $con->rollBack();
        echo "예약 중 오류가 발생했습니다: " . $e->getMessage();
    }
}
?>
