<?php
session_start();
include_once("db.php");

if (!isset($_SESSION['id'])) {
    echo "<script>
           alert('로그인이 필요합니다.');
           window.location.href='../adminLogin.php';
          </script>";
    exit;
} else if ($_SESSION['position'] == 1 || $_SESSION['position'] == 2) {
    $id = $_SESSION['id'];
} else {
    echo "<script>
    alert('접근권한이 없습니다.');
    window.location.href='../adminLogin.php';
    </script>";
    exit;
}

// 사용자의 정보 가져오기
$stmt = $con->prepare("SELECT * FROM consultant WHERE id = ?");
$result5 = $stmt->fetch(PDO::FETCH_ASSOC);

// $result5에서 가져온 정보로 $job 변수 설정
if ($result5) {
    switch ($result5['position']) {
        case 1:
            $job = "대표";
            break;
        case 2:
            $job = "이사";
            break;
        case 3:
            $job = "팀장";
            break;
        case 4:
            $job = "연구원";
            break;
        case 5:
            $job = "분석사";
            break;
        default:
            $job = "";
            break;
    }
} else {
    $job = "관리자";
}

$items_per_page = 15;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$search_query_sql = '%' . $search_query . '%';

try {
    $reservations = [];
    $total_reservations = 0;
    $total_pages = 1;

    // 데이터 검색 및 시간 내림차순 정렬
    $stmt = $con->prepare("
    SELECT * FROM finger_reservation
    WHERE location LIKE :search_query OR reserver_name LIKE :search_query
    ORDER BY reservation_time DESC
    LIMIT :limit OFFSET :offset
");

    // 검색어 및 페이지네이션 매개변수 바인딩
    $stmt->bindValue(':search_query', $search_query_sql, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 총 예약 수 계산
    $total_stmt = $con->prepare("
        SELECT COUNT(*) FROM finger_reservation
        WHERE location LIKE :search_query OR reserver_name LIKE :search_query
    ");
    $total_stmt->bindValue(':search_query', $search_query_sql, PDO::PARAM_STR);
    $total_stmt->execute();
    $total_reservations = $total_stmt->fetchColumn();

    // 총 페이지 수 계산
    $total_pages = ceil($total_reservations / $items_per_page);

} catch (PDOException $e) {
    die("데이터베이스 조회 중 오류 발생: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예약 리스트 관리 페이지</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <style>
    body {
        display: flex;
    }

    .sidebar {
        width: 240px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #343a40;
        padding-top: 20px;
        padding-left: 10px;
        padding-right: 10px;
    }

    .sidebar .nav-item {
        padding: 10px;
        font-size: 16px;
        color: white;
        display: block;
        text-decoration: none;
    }

    .sidebar .nav-item:hover {
        background-color: #495057;
    }

    .sidebar .nav-item.active {
        background-color: #007bff;
        color: white;
    }

    .content {
        margin-left: 260px;
        padding: 20px;
        width: calc(100% - 260px);
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        margin-bottom: 1.5rem;
    }

    .table-fixed {
        min-width: 100%;
        table-layout: auto;
        word-wrap: break-word;
        font-size: 14px;
    }

    .table-fixed th,
    .table-fixed td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 8px;
        vertical-align: middle;
    }

    .table-fixed thead {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 1;
    }

    .table-fixed th:nth-child(1),
    .table-fixed td:nth-child(1) {
        width: 5%;
    }

    .table-fixed th:nth-child(2),
    .table-fixed td:nth-child(2) {
        width: 10%;
    }

    .table-fixed th:nth-child(3),
    .table-fixed td:nth-child(3) {
        width: 10%;
    }

    .table-fixed th:nth-child(4),
    .table-fixed td:nth-child(4) {
        width: 8%;
    }

    .table-fixed th:nth-child(5),
    .table-fixed td:nth-child(5) {
        width: 10%;
    }

    .table-fixed th:nth-child(6),
    .table-fixed td:nth-child(6) {
        width: 12%;
    }

    .table-fixed th:nth-child(7),
    .table-fixed td:nth-child(7) {
        width: 10%;
    }

    .table-fixed th:nth-child(8),
    .table-fixed td:nth-child(8) {
        width: 6%;
    }

    .table-fixed th:nth-child(9),
    .table-fixed td:nth-child(9) {
        width: 7%;
    }

    .table-fixed th:nth-child(10),
    .table-fixed td:nth-child(10) {
        width: 7%;
    }

    .table-fixed th:nth-child(11),
    .table-fixed td:nth-child(11) {
        width: 10%;
    }

    .table-fixed th:nth-child(12),
    .table-fixed td:nth-child(12) {
        width: 15%;
    }
    </style>
</head>

<body>

    <!-- 사이드바 -->
    <div class="sidebar pe-4 pb-3 pl-2" style="border-radius: 0 0 25px 0; background-color: #EBEBEB; height: 658px;">
        <nav class="navbar bg-light navbar-light" style="background-color: #EBEBEB !important;">
            <!-- 제목 -->
            <h2 class="text-center" style="color: #585858; font-family: Arial Black; font-size: 24px;">예약 리스트 관리</h2>
            <div class="d-flex align-items-center ms-4 mb-4">
                <div class="position-relative">
                    <!-- <img class="rounded-circle" src="img/<?php echo $img ?>.png" alt="" style="width: 40px; height: 40px;"> -->
                    <div class="">
                    </div>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0"><?php echo $result5['name'] ?>&nbsp;<?php echo $job ?>님</h6>
                    <span>반갑습니다.</span>
                </div>
            </div>

            <div class="navbar-nav w-100 mb-1" style="border-bottom-style: ridge;"></div>

            <div class="navbar-nav w-100">
                <!-- 관리자 화면 버튼 -->
                <a href="../index_admin.php" class="nav-item nav-link"><i class="fa fa-cogs me-2"></i>관리자 화면</a>
                <div class="navbar-nav w-100 mb-1" style="border-bottom-style: ridge;"></div>
                <!-- 다른 메뉴 항목을 여기에 추가할 수 있습니다 -->
            </div>
        </nav>
    </div>


    <!-- 메인 콘텐츠 -->
    <div class="content">
        <!-- 검색 폼을 테이블 상단으로 이동 -->
        <div class="content-header">
            <div></div> <!-- 왼쪽 공간 비워두기 -->
            <form method="GET" action="" class="form-inline">
                <input type="text" name="search_query" class="form-control mr-2" placeholder="장소 또는 예약자 이름 검색"
                    value="<?= htmlspecialchars($search_query) ?>">
                <button type="submit" class="btn btn-primary">검색</button>
            </form>
        </div>

        <!-- 예약 리스트 테이블 -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-fixed">
                        <thead>
                            <tr>
                                <th>예약 번호</th>
                                <th>장소</th>
                                <th>날짜</th>
                                <th>시간</th>
                                <th>컨설턴트 이름</th>
                                <th>예약자 이름</th>
                                <th>전화번호</th>
                                <th>성별</th>
                                <th>주 손잡이</th>
                                <th>MBTI</th>
                                <th>생년월일</th>
                                <th>예약 시간</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reservations)): ?>
                            <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['idx']) ?></td>
                                <td><?= htmlspecialchars($reservation['location']) ?></td>
                                <td><?= htmlspecialchars($reservation['event_date']) ?></td>
                                <td><?= htmlspecialchars($reservation['event_time']) ?></td>
                                <td>-</td>
                                <td><?= htmlspecialchars($reservation['reserver_name']) ?></td>
                                <td><?= htmlspecialchars($reservation['phone']) ?></td>
                                <td><?= htmlspecialchars($reservation['gender']) ?></td>
                                <td><?= htmlspecialchars($reservation['hand_s']) ?></td>
                                <td><?= htmlspecialchars($reservation['mbti']) ?></td>
                                <td><?= htmlspecialchars($reservation['birth_date']) ?></td>
                                <td><?= htmlspecialchars($reservation['reservation_time']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="12" class="text-center">등록된 예약 정보가 없습니다.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 페이지네이션 -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $current_page - 1 ?>&search_query=<?= htmlspecialchars($search_query) ?>"
                        aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $i ?>&search_query=<?= htmlspecialchars($search_query) ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $current_page + 1 ?>&search_query=<?= htmlspecialchars($search_query) ?>"
                        aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</body>

</html>