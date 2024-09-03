<?php
@session_start();
include_once("db.php");

if (!isset($_SESSION['id'])) {
    echo "<script>
           alert('로그인이 필요합니다.');
           window.location.href='adminLogin.php';
          </script>";
    exit;
} else if ($_SESSION['position'] == 1 || $_SESSION['position'] == 2) {
    $id = $_SESSION['id'];
} else {
    echo "<script>
    alert('접근권한이 없습니다.');
    window.location.href='adminLogin.php';
    </script>";
    exit;
}
// 사용자의 정보 가져오기
$stmt = $con->prepare("SELECT * FROM consultant WHERE id = ?");
$stmt->execute([$id]);
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
            $job = "직원";
            break;
    }
} else {
    $job = "직원";
}

require 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.naver.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cap_edu@naver.com';
        $mail->Password = 'wave3dcom*';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('cap_edu@naver.com', 'CAP Education');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "이메일 전송 실패. Error: {$mail->ErrorInfo}";
    }
}

// 페이지네이션을 위한 설정
$items_per_page = 10;  
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// 컨설턴트 데이터 가져오기
$consultantQuery = "SELECT id, name, phone_number, country, MC, CC, consultant_code, consultant, analyst 
          FROM consultant 
          LIMIT :offset, :items_per_page";
$stmt = $con->prepare($consultantQuery);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$consultants = $stmt->fetchAll();

// 총 컨설턴트 수 계산
$total_consultants_query = "SELECT COUNT(*) FROM consultant";
$total_stmt = $con->prepare($total_consultants_query);
$total_stmt->execute();
$total_consultants = $total_stmt->fetchColumn();
$total_pages = ceil($total_consultants / $items_per_page);

// 예약 승인 관리 데이터 가져오기
$reservationQuery = "SELECT * FROM reservation_support ORDER BY idx ASC LIMIT :offset, :items_per_page";
$reservationStmt = $con->prepare($reservationQuery);
$reservationStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$reservationStmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);
$reservationStmt->execute();
$reservations = $reservationStmt->fetchAll();

// 총 예약 승인 수 계산
$total_reservation_query = "SELECT COUNT(*) FROM reservation_support";
$total_reservation_stmt = $con->prepare($total_reservation_query);
$total_reservation_stmt->execute();
$total_reservations = $total_reservation_stmt->fetchColumn();
$total_reservation_pages = ceil($total_reservations / $items_per_page);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alertMessage = "";  // 알림 메시지를 저장할 변수

    if (isset($_POST['consultant_toggle'])) {
        $id = $_POST['consultant_toggle'];

        $query = "SELECT consultant, e_mail FROM consultant WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $newConsultantValue = $result['consultant'] === 'pass' ? NULL : 'pass';
            $solution = $newConsultantValue ? 'pass' : NULL;
            $support = $newConsultantValue ? 'support3' : NULL;

            $updateQuery = "UPDATE consultant SET consultant = :newConsultantValue, solution = :solution, support = :support WHERE id = :id";
            $stmt = $con->prepare($updateQuery);
            $stmt->bindParam(':newConsultantValue', $newConsultantValue, PDO::PARAM_STR);
            $stmt->bindParam(':solution', $solution, PDO::PARAM_STR);
            $stmt->bindParam(':support', $support, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $status = $newConsultantValue ? '승인하기' : '해지하기';
            $alertMessage = "컨설턴트 권한이 {$status}되었습니다.";

            if (!empty($result['e_mail'])) {
                sendEmail($result['e_mail'], "컨설턴트 권한 {$status} 안내", $emailBody);
            } else {
                $alertMessage .= "\n이메일 주소를 찾을 수 없습니다. 승인 처리는 계속됩니다.";
            }
        }
    } elseif (isset($_POST['analyst_toggle'])) {
        $id = $_POST['analyst_toggle'];

        $query = "SELECT analyst, e_mail FROM consultant WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $newAnalystValue = $result['analyst'] === 'pass' ? NULL : 'pass';
            $solution = $newAnalystValue ? 'pass' : NULL;
            $support = $newAnalystValue ? 'support3' : NULL;

            $updateQuery = "UPDATE consultant SET analyst = :newAnalystValue, solution = :solution, support = :support WHERE id = :id";
            $stmt = $con->prepare($updateQuery);
            $stmt->bindParam(':newAnalystValue', $newAnalystValue, PDO::PARAM_STR);
            $stmt->bindParam(':solution', $solution, PDO::PARAM_STR);
            $stmt->bindParam(':support', $support, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $status = $newAnalystValue ? '승인하기' : '해지하기';
            $alertMessage = "지문 분석 전문가 권한이 {$status}되었습니다.";

            if (!empty($result['e_mail'])) {
                sendEmail($result['e_mail'], "지문 분석 전문가 권한 {$status} 안내", $emailBody);
            } else {
                $alertMessage .= "\n이메일 주소를 찾을 수 없습니다. 승인 처리는 계속됩니다.";
            }
        }
    }

    // JavaScript를 사용하여 alert 메시지 출력 후 리디렉션
    if ($alertMessage) {
        echo "<script type='text/javascript'>
                alert('$alertMessage');
                window.location.href='employee_admin.php';
              </script>";
    } else {
        header('Location: employee_admin.php');
    }
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>관리자 예약 승인 폼</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
    .sidebar {
        height: 100%;
        width: 220px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #343a40;
        padding-top: 20px;
        font-family: 'Arial', sans-serif;
    }

    .sidebar a {
        padding: 15px;
        text-decoration: none;
        font-size: 18px;
        color: white;
        display: block;
        border-bottom: 1px solid #474f54;
    }

    .sidebar a:hover {
        background-color: #495057;
        color: white;
    }

    .content {
        margin-left: 240px;
        padding: 20px;
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    .btn-approve {
        background-color: #28a745;
        color: white;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .btn-approve:hover {
        background-color: #218838;
    }

    .btn-revoke {
        background-color: #dc3545;
        color: white;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .btn-revoke:hover {
        background-color: #c82333;
    }

    table.table {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    table.table th,
    table.table td {
        vertical-align: middle;
        text-align: center;
    }

    h2 {
        font-family: 'Arial', sans-serif;
        color: #343a40;
        border-bottom: 2px solid #343a40;
        padding-bottom: 10px;
    }

    .section {
        display: none;
    }

    .active {
        display: block;
    }
    </style>
    <script>
    function showSection(sectionId) {
        document.getElementById('consultantSection').classList.remove('active');
        document.getElementById('analystSection').classList.remove('active');
        document.getElementById('reservationApprovalSection').classList.remove('active');

        document.getElementById(sectionId).classList.add('active');
    }

    window.onload = function() {
        showSection('reservationApprovalSection');
    }

    function toggleApproval(idx, action) {
        $.ajax({
            url: 'reservation/approve_reservation.php',
            type: 'POST',
            data: {
                idx: idx,
                pass: action
            },
            success: function(response) {
                alert(response);
                $('#reservationApprovalSection').load(window.location.href +
                ' #reservationApprovalSection');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error during AJAX request:', textStatus, errorThrown);
                alert('승인 처리 중 오류가 발생했습니다.');
            }
        });
    }
    </script>
</head>

<body>

    <!-- sidebar.html -->
    <div class="sidebar pe-4 pb-3 pl-2" style="border-radius: 0 0 25px 0; background-color: #EBEBEB; height: 658px;">
        <nav class="navbar bg-light navbar-light" style="background-color: #EBEBEB !important;">
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

                <a href="index_admin.php" class="nav-item nav-link"><i class="fas fa-home me-2"></i>홈 화면</a>
                <a href="reservation/reservation_form.php" class="nav-item nav-link"><i
                        class="fas fa-calendar-alt me-2"></i> 컨설턴트 페이지</a>
                <a href="#" class="nav-item nav-link" onclick="showSection('consultantSection')"><i
                        class="fas fa-user-tie me-2"></i> 컨설턴트 권한 승인</a>
                <a href="#" class="nav-item nav-link" onclick="showSection('analystSection')"><i
                        class="fas fa-search me-2"></i> 분석가 권한 승인</a>
                <a href="#" class="nav-item nav-link" onclick="showSection('reservationApprovalSection')"><i
                        class="fas fa-check me-2"></i> 예약 승인 관리</a>
            </div>
        </nav>
    </div>


    <div class="content">
        <div id="consultantSection" class="section mt-4">
            <h2>컨설턴트</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>이름</th>
                        <th>전화 번호</th>
                        <th>권리 지역</th>
                        <th>컨설턴트 코드</th>
                        <th>컨설턴트 권한</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultants as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td><?= htmlspecialchars($row['country'] . ' ' . $row['MC'] . ' ' . $row['CC']) ?></td>
                        <td><?= htmlspecialchars($row['consultant_code']) ?></td>
                        <td>
                            <form method="post">
                                <button type="submit" name="consultant_toggle" value="<?= $row['id'] ?>"
                                    class="btn <?= $row['consultant'] === 'pass' ? 'btn-revoke' : 'btn-approve' ?>">
                                    <?= $row['consultant'] === 'pass' ? '해지하기' : '승인하기' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

        <div id="analystSection" class="section mt-4">
            <h2>지문분석 전문가</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>이름</th>
                        <th>전화 번호</th>
                        <th>권리 지역</th>
                        <th>컨설턴트 코드</th>
                        <th>애널리스트 권한</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultants as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['phone_number']) ?></td>
                        <td><?= htmlspecialchars($row['country'] . ' ' . $row['MC'] . ' ' . $row['CC']) ?></td>
                        <td><?= htmlspecialchars($row['consultant_code']) ?></td>
                        <td>
                            <form method="post">
                                <button type="submit" name="analyst_toggle" value="<?= $row['id'] ?>"
                                    class="btn <?= $row['analyst'] === 'pass' ? 'btn-revoke' : 'btn-approve' ?>">
                                    <?= $row['analyst'] === 'pass' ? '해지하기' : '승인하기' ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

        <div id="reservationApprovalSection" class="section mt-4 active">
            <h2>예약 승인 관리</h2>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
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
                        <td colspan="12">등록된 예약 정보가 없습니다.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <nav>
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_reservation_pages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>

</body>

</html>