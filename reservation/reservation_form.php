<?php

session_start();
include_once("db.php");

// 데이터베이스 연결 확인
if (!$con) {
    die("데이터베이스 연결 실패");
}

// 세션에서 사용자 정보를 가져옴
$name = $_SESSION['name'] ?? null;
$phone_number = $_SESSION['phone_number'] ?? null;
$email = $_SESSION['email'] ?? null;
$address = $_SESSION['address'] ?? null;
$code = $_SESSION['code'] ?? null;
$position = $_SESSION['position'] ?? null; // position 값 가져오기

// 세션 정보가 없으면 로그인 페이지로 리디렉션
if (!$name || !$code) {
    echo "<script>
            alert('로그인하지 않으셨습니다. 로그인 페이지로 이동합니다.');
            window.location.href = '../adminLogin.php';
          </script>";
    exit();
}

// 관리자인지 확인하여 세션에 저장
if ($position == 1 || $position == 2) { // position이 1 또는 2이면 관리자
    $_SESSION['is_admin'] = true;
} else {
    $_SESSION['is_admin'] = false;
}

// POST 요청 처리 (사용자 정보 수정)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $new_name = $_POST['name'] ?? null;
    $phone_number = $_POST['phone_number'] ?? null;
    $email = $_POST['e_mail'] ?? null;
    $address = $_POST['address'] ?? null;

    try {
        $stmt = $con->prepare("UPDATE consultant SET name = ?, phone_number = ?, e_mail = ?, address = ? WHERE consultant_code = ?");
        $stmt->execute([$new_name, $phone_number, $email, $address, $code]);

        // 세션 업데이트
        $_SESSION['name'] = $new_name;
        $_SESSION['phone_number'] = $phone_number;
        $_SESSION['email'] = $email;
        $_SESSION['address'] = $address;

        // 성공 시 리다이렉트
        echo "<script>
                alert('정보가 성공적으로 수정되었습니다.');
                window.location.href = '/reservation/reservation_form.php';
              </script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>
                alert('데이터베이스 업데이트 중 오류 발생: " . htmlspecialchars($e->getMessage()) . "');
              </script>";
        exit();
    }
}

// 페이지당 표시할 레코드 수 설정
$records_per_page = 5;

// 현재 페이지 번호 가져오기 (기본값은 1)
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// 총 레코드 수 가져오기
$total_records_query = $con->prepare("SELECT COUNT(*) FROM reservation_support WHERE consultant_code COLLATE utf8_unicode_ci = ?");
$total_records_query->execute([$code]);
$total_records = $total_records_query->fetchColumn();
$total_pages = ceil($total_records / $records_per_page);

// 예약 정보 가져오기 (LIMIT 적용)
$stmt = $con->prepare("SELECT idx, location, date, time, submitted_at, pass, event_code, people_num, con_num 
                       FROM reservation_support 
                       WHERE consultant_code COLLATE utf8_unicode_ci = ? 
                       ORDER BY date DESC 
                       LIMIT ? OFFSET ?");
$stmt->bindParam(1, $code, PDO::PARAM_STR);
$stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$userReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 참여 이벤트 목록을 페이지 단위로 조회
$records_per_page_events = 5;
$current_page_events = isset($_GET['event_page']) ? intval($_GET['event_page']) : 1;
$offset_events = ($current_page_events - 1) * $records_per_page_events;

// 총 이벤트 수 가져오기
$total_event_records_query = $con->prepare("SELECT COUNT(*) FROM reservation_support e JOIN event_list l ON e.event_code COLLATE utf8_unicode_ci = l.event_code WHERE l.consultant_code COLLATE utf8_unicode_ci = ?");
$total_event_records_query->execute([$code]);
$total_event_records = $total_event_records_query->fetchColumn();
$total_event_pages = ceil($total_event_records / $records_per_page_events);

try {
    $stmt2 = $con->prepare("SELECT e.idx, e.location, e.date, e.time, e.event_code 
                            FROM reservation_support e
                            JOIN event_list l ON e.event_code COLLATE utf8_unicode_ci = l.event_code
                            WHERE l.consultant_code COLLATE utf8_unicode_ci = ?
                            LIMIT ? OFFSET ?");
    $stmt2->bindParam(1, $code, PDO::PARAM_STR);
    $stmt2->bindParam(2, $records_per_page_events, PDO::PARAM_INT);
    $stmt2->bindParam(3, $offset_events, PDO::PARAM_INT);
    $stmt2->execute();
    $participatedEvents = $stmt2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("데이터베이스 조회 중 오류 발생: " . htmlspecialchars($e->getMessage()));
}

// 마이페이지에서 사용할 사용자 정보 조회
try {
    $stmt3 = $con->prepare("SELECT name, phone_number, e_mail, address FROM consultant WHERE consultant_code COLLATE utf8_unicode_ci = ?");
    $stmt3->execute([$code]);
    $consultantInfo = $stmt3->fetch(PDO::FETCH_ASSOC);

    if ($consultantInfo) {
        $name = htmlspecialchars($consultantInfo['name']);
        $phone_number = htmlspecialchars($consultantInfo['phone_number']);
        $email = htmlspecialchars($consultantInfo['e_mail']);
        $address = htmlspecialchars($consultantInfo['address']);
    }
} catch (PDOException $e) {
    die("데이터베이스 조회 중 오류 발생: " . htmlspecialchars($e->getMessage()));
}

?>


<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예약 폼 신청 페이지</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/reservation_form.css" />

    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />

</head>


<body>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- jQuery UI JS -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <!-- FullCalendar JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <div class="sidebar">
        <div class="sidebar-jm-box">
            <img src="/img/cap-jm.png">
        </div>
        <div class="profile">
            <a href="#"> <?= htmlspecialchars($name) ?>님</a><br>
            <span>반갑습니다.</span>
        </div>
        <div class="sidebar-btn-box">
            <a href="#" id="myPageBtn" class="my-btn">마이페이지</a>
            <a href="../adminLogout.php" class="sidebar-logout">로그아웃</a>
        </div>

        <h2 class="text-center">메뉴</h2>
        <a href="#" id="myEventsBtn">내 이벤트 정보</a>
        <a href="#" id="applyEventBtn">이벤트 신청</a>
        <a href="#" id="participateEventsBtn">참여 이벤트 목록</a>

        <!-- 관리자일 경우에만 관리자 페이지로 돌아가기 버튼 표시 -->
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <a href="../employee_admin.php" id="adminPageBtn">관리자 페이지로 이동</a>
        <?php endif; ?>
    </div>


    <!-- 마이 페이지 -->
    <div class="container-box active" id="myPageContainer">
        <div class="profile-container">
            <h2>마이페이지</h2>
            <hr class="profile-line" />
            <form class="profile-form" action="reservation_form.php" method="POST">
                <label for="name">이름</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

                <label for="phone_number">전화번호</label>
                <input type="text" id="phone_number" name="phone_number"
                    value="<?php echo htmlspecialchars($phone_number); ?>" required>

                <label for="e_mail">이메일</label>
                <input type="email" id="e_mail" name="e_mail" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="address">주소</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>"
                    required>

                <div class="submit-container">
                    <input type="submit" value="정보 수정">
                </div>
            </form>
        </div>
    </div>

    <!-- 내 이벤트 정보 컨테이너 -->
    <div class="container-box" id="myEventsContainer">
        <h2>내 이벤트 정보</h2>

        <table class="table text-center align-middle table-bordered table-hover mb-0">
            <thead style="background-color: #FFE0D2;">
                <tr>
                    <th>예약 장소</th>
                    <th>예약 날짜</th>
                    <th>예약 시간</th>
                    <th>요청일시</th>
                    <th>이벤트 코드</th>
                    <th>컨설턴트 추가</th>
                    <th>상태</th>
                    <th>수정</th>
                    <th>URL</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($userReservations)): ?>
                    <?php foreach ($userReservations as $reservation): ?>
                        <tr>
                            <td><?= htmlspecialchars($reservation['location']) ?></td>
                            <td><?= htmlspecialchars($reservation['date']) ?></td>
                            <td><?= htmlspecialchars($reservation['time']) ?></td>
                            <td><?= htmlspecialchars($reservation['submitted_at']) ?></td>
                            <td><?= htmlspecialchars($reservation['event_code']) ?></td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <button class="btn btn-primary" data-toggle="modal" data-target="#addConsultantModal"
                                        data-idx="<?= $reservation['idx'] ?>"
                                        data-event-code="<?= htmlspecialchars($reservation['event_code']) ?>"
                                        <?= $reservation['pass'] === 'true' ? '' : 'disabled' ?>>등록</button>
                                </div>
                            </td>
                            <td><?= $reservation['pass'] === 'true' ? '최종 승인 대기' : ($reservation['pass'] === 'complete' ? '최종 승인 완료' : '신청 완료') ?>
                            </td>
                            <td>
                                <button class="btn btn-secondary" data-toggle="modal" data-target="#consultantModal"
                                    data-idx="<?= $reservation['idx'] ?>" data-location="<?= $reservation['location'] ?>"
                                    data-date="<?= $reservation['date'] ?>" data-time="<?= $reservation['time'] ?>"
                                    data-people_num="<?= $reservation['people_num'] ?>"
                                    data-con_num="<?= $reservation['con_num'] ?>"
                                    <?= $reservation['pass'] === 'true' ? '' : 'disabled' ?>>수정</button>
                            </td>
                            <td>
                                <?php if ($reservation['event_code']): ?>
                                    <a href="reservation.php?event_code=<?= htmlspecialchars($reservation['event_code']) ?>"
                                        target="_blank">예약자 정보폼</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">등록된 예약 정보가 없습니다.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- 페이지 네비게이션 -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- 이전 페이지 링크 -->
                <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <!-- 다음 페이지 링크 -->
                <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- 컨설턴트 추가 모달 -->
    <div class="modal fade" id="addConsultantModal" tabindex="-1" role="dialog"
        aria-labelledby="addConsultantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addConsultantModalLabel">컨설턴트 추가</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control" id="newConsultantCode" placeholder="컨설턴트 코드"
                                style="flex: 1;">
                            <button type="button" class="btn btn-primary ml-2" id="addConsultantButton"
                                style="height: 38px;">추가</button>
                        </div>
                    </div>
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>이름</th>
                                <th>전화번호</th>
                                <th>컨설턴트 코드</th>
                                <th>삭제</th>
                            </tr>
                        </thead>
                        <tbody id="addConsultantList">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 이벤트 신청 컨테이너 -->
    <div class="container-box" id="applyEventContainer">
        <h2>이벤트 신청</h2>

        <form id="reservationForm" method="post">
            <table class="table table-bordered fixed-table">
                <tbody>
                    <tr>
                        <th rowspan="3">지역</th>
                        <th><label for="country">국가 선택</label></th>
                        <td>
                            <select id="country" name="country" class="form-control">
                                <option value="">국가 선택</option>
                                <option value="KOR" selected>한국</option>
                                <option value="AUS">호주</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="city">MC(Mega Center) 광역</label></th>
                        <td>
                            <select id="city" name="city" class="form-control" disabled>
                                <option value="">MC 선택</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="district">CC(Core Center) 지역</label></th>
                        <td>
                            <select id="district" name="district" class="form-control" disabled>
                                <option value="">CC 선택</option>
                            </select>
                        </td>
                    </tr>
                    <th rowspan="4">일정</th>
                    <tr>
                        <th><label for="location">장소</label></th>
                        <td>
                            <input type="text" id="location" name="location" class="form-control" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="date">날짜</label></th>
                        <td>
                            <input type="text" id="date" name="date" class="form-control datepicker" required>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="time">시간</label></th>

                        <td>
                            <div class="d-flex">
                                <select id="ampm" name="ampm" class="form-control ml-2" required>
                                    <option value="AM">AM</option>
                                    <option value="PM">PM</option>
                                </select>
                                <select id="hour" name="hour" class="form-control" required>
                                    <option value="12">12</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                </select>

                                <select id="minute" name="minute" class="form-control" required>
                                    <option value="00">00</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="40">40</option>
                                    <option value="50">50</option>
                                </select>

                            </div>
                        </td>
                    </tr>

                    <th rowspan="2">인원</th>
                    <th><label for="num_people">예약 인원 (명)</label></th>
                    <td>
                        <input type="number" id="num_people" name="num_people" class="form-control" min="1" required>
                    </td>
                    </tr>
                    <tr>
                        <th><label for="additional_consultants">추가 컨설턴트 인원 (명)</label></th>
                        <td>
                            <input type="number" id="additional_consultants" name="additional_consultants"
                                class="form-control" min="0" required value="0">
                        </td>
                    </tr>

                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">신청</button>
        </form>
    </div>

    <!-- 예약 수정 모달 -->
    <div class="modal fade" id="consultantModal" tabindex="-1" role="dialog" aria-labelledby="consultantModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="consultantModalLabel">예약 정보 수정</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateReservationForm">
                        <input type="hidden" id="reservationIdx" name="idx">
                        <div class="form-group">
                            <label for="update_location">장소</label>
                            <input type="text" id="update_location" class="form-control" name="location" required>
                        </div>
                        <div class="form-group">
                            <label for="update_date">날짜</label>
                            <input type="text" id="update_date" class="form-control datepicker" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="update_time">시간</label>
                            <input type="text" id="update_time" class="form-control timepicker" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="update_people_num">예약 인원 (명)</label>
                            <input type="number" id="update_people_num" class="form-control" name="people_num" min="0"
                                required value="0">
                        </div>
                        <div class="form-group">
                            <label for="update_con_num">추가 컨설턴트 인원 (명)</label>
                            <input type="number" id="update_con_num" class="form-control" name="con_num" min="0"
                                required value="0">
                        </div>
                        <button type="submit" class="btn btn-primary">수정</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 참여 이벤트 목록 컨테이너 -->
    <div class="container-box" id="participateEventsContainer">
        <h2>참여 이벤트 목록</h2>
        <table class="table text-center align-middle table-bordered table-hover mb-0">
            <thead style="background-color: #FFE0D2;">
                <tr>
                    <th>장소</th>
                    <th>날짜</th>
                    <th>시간</th>
                    <th>이벤트 코드</th>
                    <th>예약자 조회</th>
                </tr>
            </thead>
            <tbody>
                <!-- AJAX로 데이터가 로드됩니다. -->
            </tbody>
        </table>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <!-- AJAX로 페이지 네비게이션이 동적으로 추가됩니다. -->
            </ul>
        </nav>
    </div>

    <!-- 예약자 조회 모달 -->
    <div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-labelledby="reservationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">예약자 정보</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>예약 번호</th>
                                <th>예약자 이름</th>
                                <th>전화번호</th>
                                <th>주 손잡이</th>
                                <th>성별</th>
                                <th>생년월일</th>
                                <th>MBTI</th>
                                <th>예약 시간</th>
                                <th>검사 유무</th>
                            </tr>
                        </thead>
                        <tbody id="reservationList">
                            <!-- AJAX로 데이터가 로드됩니다. -->
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center" id="reservationPagination">
                            <!-- AJAX로 페이지 네비게이션이 동적으로 추가됩니다. -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS -->
    <script src="reservation_form.js"></script>
</body>

</html>