<?php
@session_start();
include_once("db.php");

if (!isset($_SESSION['id'])) {
    echo "<script>
    alert('로그인이 필요합니다.');
    window.location.href='adminLogin.php';
    </script>";
} else if ($_SESSION['position'] == 1 || $_SESSION['position'] == 2) {
    $id = $_SESSION['id'];
} else {
    echo "<script>
    alert('접근권한이 없습니다.');
    window.location.href='adminLogin.php';
    </script>";
}

try {
    $query = $con->query("SELECT * FROM reservation_support ORDER BY idx ASC");  // 번호를 오름차순으로 정렬
    $reservations = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("데이터베이스 조회 중 오류 발생: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예약 관리 페이지</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>예약 폼 승인 관리</h2>
        <div class="table-responsive">
            <table class="table text-center align-middle table-bordered table-hover mb-0">
                <thead style="background-color: #A6E5E5;">
                    <tr class="text-center">
                        <th>컨설턴트 이름</th>
                        <th>장소</th>
                        <th>날짜</th>
                        <th>시간</th>
                        <th>국가</th>
                        <th>MC</th>
                        <th>CC</th>
                        <th>예상 인원</th>
                        <th>컨설턴트 인원</th>
                        <th>제출 시각</th>
                        <th>1차 승인</th>
                        <th>최종 승인</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reservations)): ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['consultant_name']) ?></td>
                                <td><?= htmlspecialchars($reservation['location']) ?></td>
                                <td><?= htmlspecialchars($reservation['date']) ?></td>
                                <td><?= htmlspecialchars($reservation['time']) ?></td>
                                <td><?= htmlspecialchars($reservation['country']) ?></td>
                                <td><?= htmlspecialchars($reservation['MC']) ?></td>
                                <td><?= htmlspecialchars($reservation['CC']) ?></td>
                                <td><?= htmlspecialchars($reservation['people_num']) ?></td>
                                <td><?= htmlspecialchars($reservation['con_num']) ?></td>
                                <td><?= htmlspecialchars($reservation['submitted_at']) ?></td>
                                <td>
                                    <?php if (empty($reservation['pass']) || $reservation['pass'] === 'false'): ?>
                                        <button onclick="toggleApproval(<?= $reservation['idx'] ?>, 'true')"
                                            class="btn btn-success">1차 승인</button>
                                    <?php elseif ($reservation['pass'] === 'true'): ?>
                                        <button onclick="toggleApproval(<?= $reservation['idx'] ?>, 'false')"
                                            class="btn btn-danger">1차 승인 취소</button>
                                    <?php else: ?>
                                        <span>승인 완료</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($reservation['pass'] === 'true'): ?>
                                        <button onclick="toggleApproval(<?= $reservation['idx'] ?>, 'complete')"
                                            class="btn btn-success">최종 승인</button>
                                    <?php elseif ($reservation['pass'] === 'complete'): ?>
                                        <button onclick="toggleApproval(<?= $reservation['idx'] ?>, 'true')"
                                            class="btn btn-danger">최종 승인 취소</button>
                                    <?php else: ?>
                                        <span>승인 대기</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14">등록된 예약 정보가 없습니다.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleApproval(idx, action) {
            $.ajax({
                url: 'approve_reservation.php',
                type: 'POST',
                data: { idx: idx, pass: action },
                success: function (response) {
                    alert(response);
                    location.reload();  // 페이지 새로고침
                }
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" async></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" defer></script>
</body>

</html>
