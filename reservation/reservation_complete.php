<?php
session_start();
include_once("db.php");

if (!isset($_SESSION['reservation_id'])) {
    echo "잘못된 접근입니다.";
    exit;
}

$reservation_id = $_SESSION['reservation_id'];

try {
    // 예약자 정보를 가져오는 쿼리
    $stmt = $con->prepare("SELECT reserver_name, member_code, location, event_date, event_time, event_code FROM finger_reservation WHERE idx = ?");
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        echo "예약 정보를 찾을 수 없습니다.";
        exit;
    }

    // 예약 번호와 예상 대기 시간을 계산하는 쿼리
    $stmt2 = $con->prepare("SELECT idx FROM finger_reservation WHERE location = ? AND event_date = ? AND event_time = ? ORDER BY reservation_time ASC");
    $stmt2->execute([$reservation['location'], $reservation['event_date'], $reservation['event_time']]);
    $reservations = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    $reservation_number = 0;
    $waiting_time = 0;
    foreach ($reservations as $index => $res) {
        if ($res['idx'] == $reservation_id) {
            $reservation_number = $index + 1;
            $waiting_time = ($index) * 5;
            break;
        }
    }

    // event_code를 세션에 저장
    $_SESSION['event_code'] = $reservation['event_code'];
} catch (PDOException $e) {
    die("데이터베이스 조회 중 오류 발생: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CAP TEACHING BNS - 예약 완료</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/moonspam/NanumSquare@1.0/nanumsquare.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <style>
        html {
            height: 100%;
        }
        .capteaching {
            font-weight: 900;
            font-size: 40px;
            color: #e85305;
        }
        .intro {
            font-weight: 750;
            font-size: 35px;
            color: black;
        }
        .middle {
            font-weight: 650;
            font-size: 20px;
            color: #0B155C;
        }
        .content {
            font-weight: 680;
            color: #9092a2;
        }
        .table_border {
            border-top: 2px solid;
            border-bottom: 2px solid;
        }
        @media (min-width: 992px) {
            .box_label {
                height: 367px;
                font-size: 1.5em;
                text-align: left;
                padding: 44px 0 0 363px;
                font-weight: 900;
            }
        }
        @media (max-width: 1200px) { 
            .box_label {
                height: 227px;
                font-size: 1em;
                text-align: left;
                padding: 24px 0 0 114px;
                font-weight: 900;
            }
        }
    </style>
    <script>
        function showCloseMessage() {
            alert("예약이 완료되었습니다. 창이 닫힙니다.");
            window.close(); // 현재 창 닫기
        }
    </script>
</head>
<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Header -->
        <header id="header" style="padding: 0 0 0 0;">
        </header>
        <!-- Main -->
        <div id="main" style="text-align: center;">
            <br>
            <div class="inner">
                <div class="container mt-5 py-4 mb-1">
                    <div class="row">
                        <div class="col-12 text-center mb-3"></div>
                        <div class="col-12 text-center mb-3">
                            <h2 style="color: #FF4F00;">지문 예약이 성공적으로 완료 되었습니다. 아래 정보를 확인하세요.</h2>
                        </div>
                        <div class="col-2 text-center"></div>
                        <div class="col-12 mt-5 text-left box_label" style="background: url('img/box.png');background-size: 100% 100%;">
                            · &nbsp;<strong>예약자 이름:</strong> <?= htmlspecialchars($reservation['reserver_name']) ?><br>
                            · &nbsp;<strong>예약자 코드:</strong> <?= htmlspecialchars($reservation['member_code']) ?><br>
                            · &nbsp;<strong>장소:</strong> <?= htmlspecialchars($reservation['location']) ?><br>
                            · &nbsp;<strong>날짜:</strong> <?= htmlspecialchars($reservation['event_date']) ?><br>
                            · &nbsp;<strong>시간:</strong> <?= htmlspecialchars($reservation['event_time']) ?><br>
                            · &nbsp;<strong>예약 번호:</strong> <?= $reservation_number ?><br>
                            · &nbsp;<strong>예상 대기 시간:</strong> <?= $waiting_time ?>분<br>
                        </div>
                        <div class="col-12 text-center mt-4">
                            <form action="reservation.php" method="get" style="display: inline;">
                                <input type="hidden" name="event_code" value="<?= htmlspecialchars($reservation['event_code']) ?>">
                                <button type="submit" class="btn btn-primary">추가 예약</button>
                            </form>
                            <button class="btn btn-danger" onclick="showCloseMessage()">완료</button>
                        </div>
                        <div class="col-2 text-center"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-1CmrxMRARb6aLqgBO7yyAxTOQE2AKb9GfXnEaCZm49j+6sXniPhlgmZq5jGAm0bJ" crossorigin="anonymous"></script>
</body>
</html>
