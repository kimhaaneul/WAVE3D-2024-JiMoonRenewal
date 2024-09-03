<?php 
@session_start();
include_once("db.php");

if(!isset($_SESSION['id'])){
    echo "<script>
    alert('로그인이 필요합니다.');
    window.location.href='adminLogin.php';
    </script>";
    exit();
} else if($_SESSION['position'] == 1 || $_SESSION['position'] == 2){
    $id = $_SESSION['id'];
} else {
    echo "<script>
    alert('접근권한이 없습니다.');
    window.location.href='adminLogin.php';
    </script>";
    exit();
}

// 지문 검사자 수 카운트
$sql2 = "SELECT count(*) as acount FROM finger_info";
$stmt2 = $con->prepare($sql2);
$stmt2->execute();
$result10 = $stmt2->fetch();
$acounter = $result10['acount']; 

// 사용자 정보 가져오기
$sql2 = "SELECT * FROM consultant WHERE id = :id";
$stmt = $con->prepare($sql2);
$stmt->bindParam(':id', $id);
$stmt->execute();
$result5 = $stmt->fetch();

$position = $result5['position'];
$job = "";
$img = "";

// 직책 및 이미지 설정
function getSupportImage($position) {
    switch ($position) {
        case 1:
        case 2:
            return "support3.png";
        case 3:
            return "support6.png";
        case 4:
        case 5:
            return "support4.png";
        default:
            return "user.jpg";
    }
}

switch ($position) {
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
    case 5:
        $job = ($position == 4) ? "연구원" : "분석사";
        break;
    default:
        $job = "안녕하세요";
        break;
}

$img = getSupportImage($position);

// 지원팀 이미지 로드
$supportPositions = ['support', 'support2', 'support3'];
$supportImages = [];

foreach ($supportPositions as $support) {
    $sql2 = "SELECT position FROM consultant WHERE support = :support";
    $stmt = $con->prepare($sql2);
    $stmt->bindParam(':support', $support);
    $stmt->execute();
    $row = $stmt->fetch();
    $supportImages[$support] = getSupportImage($row['position']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CAP AIFingers</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js"></script>
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3 pl-2"
            style="border-radius: 0 0 25px 0; background-color: #EBEBEB; height: 658px;">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary" style="color: #585858; font-family: Arial Black;">
                        <span style="color: #FD4827;">CAP</span> AI Fingers
                    </h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/<?php echo $img ?>" alt=""
                            style="width: 40px; height: 40px;">
                        <div
                            class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo $result5['name'] ?> <?php echo $job ?>님</h6>
                        <span>반갑습니다.</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="index_admin.html" class="nav-item nav-link active"><i class="fa fa-home me-2"></i>첫 화면</a>
                    <div class="navbar-nav w-100 mb-1" style="border-bottom-style: ridge;"></div>
                    <a href="employee_admin.php" class="nav-item nav-link"><i class="fa fa-user me-2"></i>컨설턴트 분석사
                        관리</a>
                    <div class="navbar-nav w-100 mb-1" style="border-bottom-style: ridge;"></div>
                    <a href="reservation/reservation_list_admin.php" class="nav-item nav-link"><i
                            class="fa fa-list-alt me-2"></i>예약 리스트 관리</a>
                    <div class="navbar-nav w-100 mb-1" style="border-bottom-style: ridge;"></div>
                    <a href="#" class="nav-item nav-link"><i class="fa fa-cogs me-2"></i>회원 관리</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4">
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="검사지 및 보고서 검색">
                </form>

                <!-- 지원팀 및 오류지원 관련 코드 -->
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle navstlye" data-bs-toggle="dropdown">
                        <div class="position-relative" id="divReloadLayer9">
                            <img class="rounded-circle" src="img/<?php echo $supportImages['support'] ?>" alt=""
                                style="width: 40px; height: 40px; margin-right: 10px;">
                            <div
                                class="<?php echo ($supportImages['support'] == 'user.jpg') ? 'bg-danger' : 'bg-success' ?> rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                            </div>
                        </div>
                        <span class="d-none d-lg-inline-flex" style="margin-left: 10px;">지원팀1</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <a href="supportDB.php?support=support" class="dropdown-item">지원하기</a>
                        <a href="support_removeDB.php?support=support" class="dropdown-item">취소하기</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle navstlye" data-bs-toggle="dropdown">
                        <div class="position-relative" id="divReloadLayer8">
                            <img class="rounded-circle" src="img/<?php echo $supportImages['support2'] ?>" alt=""
                                style="width: 40px; height: 40px; margin-right: 10px;">
                            <div
                                class="<?php echo ($supportImages['support2'] == 'user.jpg') ? 'bg-danger' : 'bg-success' ?> rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                            </div>
                        </div>
                        <span class="d-none d-lg-inline-flex" style="margin-left: 10px;">지원팀2</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <a href="supportDB.php?support=support2" class="dropdown-item">지원하기</a>
                        <a href="support_removeDB.php?support=support2" class="dropdown-item">취소하기</a>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle navstlye" data-bs-toggle="dropdown">
                        <div class="position-relative" id="divReloadLayer7">
                            <img class="rounded-circle" src="img/<?php echo $supportImages['support3'] ?>" alt=""
                                style="width: 40px; height: 40px; margin-right: 10px;">
                            <div
                                class="<?php echo ($supportImages['support3'] == 'user.jpg') ? 'bg-danger' : 'bg-success' ?> rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                            </div>
                        </div>
                        <span class="d-none d-lg-inline-flex" style="margin-left: 10px;">지원팀3</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                        <a href="supportDB.php?support=support3" class="dropdown-item">지원하기</a>
                        <a href="support_removeDB.php?support=support3" class="dropdown-item">취소하기</a>
                    </div>
                </div>


                <!-- 오류지원 -->
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <?php
                            $sql2 = "SELECT count(DISTINCT user_code) as comment_counter FROM error_support";
                            $stmt = $con->prepare($sql2);
                            $stmt->execute();
                            $result2 = $stmt->fetch();
                            ?>
                            <i class="fa me-lg-2"
                                style="background: radial-gradient(#beebfd 10%, red 100px, brown 99%);position: absolute;width: 18px;height: 18px;margin: 22px 0 0 -23px;box-shadow: 1px 1px gray;color: #ed4d4d;">
                                <span id="divReloadLayer5"><?php echo $result2['comment_counter'] ?></span>
                            </i>
                            <span class="d-none d-lg-inline-flex">오류지원 &nbsp;<span
                                    style="color: red;"><?php echo $result2['comment_counter'] ?></span>명 </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0"
                            style="height: 300px; overflow: auto">
                            <?php
                            $sql = "SELECT * FROM error_support GROUP BY user_code ORDER BY idx DESC";
                            $stmt = $con->prepare($sql);
                            $stmt->execute();
                            while ($result = $stmt->fetch()) {
                                $sql2 = "SELECT * FROM finger_info WHERE user_code = :user_code";
                                $stmt2 = $con->prepare($sql2);
                                $stmt2->bindParam(':user_code', $result['user_code']);
                                $stmt2->execute();
                                $result1 = $stmt2->fetch();
                                
                                $sql3 = "SELECT count(*) as counter FROM error_support WHERE user_code = :user_code";
                                $stmt3 = $con->prepare($sql3);
                                $stmt3->bindParam(':user_code', $result['user_code']);
                                $stmt3->execute();
                                $result3 = $stmt3->fetch();
                            ?>
                            <a href="fp_err.html?user_code=<?php echo $result1['user_code'] ?>&user_name=<?php echo $result1['user_name'] ?>&birth=<?php echo $result1['birth'] ?>&handle=<?php echo $result1['handle'] ?>&gender=<?php echo $result1['gender'] ?>"
                                class="dropdown-item">
                                <h6 class="fw-normal mb-0"><?php echo $result1['user_name'] ?>님</h6>
                                <small>오류지원 <?php echo $result3['counter'] ?> 건</small>
                            </a>
                            <hr class="dropdown-divider">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/<?php echo $img ?>" alt=""
                                style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo $result5['name'] ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item"><i class="fa fa-user-circle"
                                    style="padding-right: 8px;"></i>프로필</a>
                            <a href="adminLogout.php" class="dropdown-item"><i class="fa fa-sign-out"
                                    style="padding-right: 8px;"></i>로그아웃</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- 검사 오류 지원 테이블 -->
            <div class="container-fluid pt-4 px-4">
                <form onsubmit="search_(event)">
                    <input type="text" name="keyword" id="keyword" placeholder="검색어를 입력하세요">
                    <input type="submit" value="검색">
                </form>
                <div class="bg-light text-center rounded p-1">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">검사 오류 지원</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-center align-middle table-bordered table-hover mb-0">
                            <thead style="background-color: #A6E5E5;">
                                <tr class="text-center">
                                    <th scope="col-1"><input class="form-check-input" type="checkbox"></th>
                                    <th scope="col-2">검사 날짜</th>
                                    <th scope="col-1">유형</th>
                                    <th scope="col-1">검사자</th>
                                    <th scope="col-1">주손잡이</th>
                                    <th scope="col-2">연락처</th>
                                    <th scope="col-2">보고서 번호</th>
                                    <th scope="col-1">분석체크</th>
                                    <th scope="col-1">지원팀1</th>
                                    <th scope="col-1">지원팀2</th>
                                    <th scope="col-1">지원팀3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Pagination variables
                                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                $sql = "SELECT * FROM finger_info;";
                                $stmt = $con->prepare($sql);
                                $stmt->execute();
                                $row_num = $stmt->rowCount();
                                $list = 5;
                                $block_ct = 5;

                                $block_num = ceil($page / $block_ct);
                                $block_start = (($block_num - 1) * $block_ct) + 1;
                                $block_end = $block_start + $block_ct - 1;

                                $total_page = ceil($row_num / $list);
                                if ($block_end > $total_page) $block_end = $total_page;
                                $total_block = ceil($total_page / $block_ct);
                                $start_num = ($page - 1) * $list;

                                if (empty($search_word)) {
                                    $sql2 = "SELECT * FROM finger_info ORDER BY idx DESC LIMIT $start_num, $list";
                                    $stmt = $con->prepare($sql2);
                                    $stmt->execute();
                                    while ($board = $stmt->fetch()) {
                                        $sql2 = "SELECT * FROM error_support WHERE user_code = :user_code";
                                        $stmt2 = $con->prepare($sql2);
                                        $stmt2->bindParam(':user_code', $board['user_code']);
                                        $stmt2->execute();
                                        $result7 = $stmt2->fetch();
                                ?>
                                <tr
                                    style="<?php if ($result7['user_code'] == $board['user_code']) { ?> background-color: #8B98BD; color: white;<?php } else if ($board['finger_type'] == "") { ?> background-color: #D3D3D3; <?php } ?>">
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td><?php echo $board['date'] ?></td>
                                    <td><?php echo $board['finger_type'] ?></td>
                                    <td><?php echo $board['user_name'] ?></td>
                                    <td><?php echo $board['handle'] ?></td>
                                    <td><?php echo $board['user_code'] ?></td>
                                    <td><?php echo $board['member_code'] ?></td>
                                    <td>
                                        <a class="btn btn-sm <?php echo ($board['pass'] == 'confirm') ? 'btn-danger' : 'btn-primary'; ?>"
                                            href="fp_err.html?user_code=<?php echo $board['user_code']; ?>&user_name=<?php echo $board['user_name']; ?>&birth=<?php echo $board['birth']; ?>&handle=<?php echo $board['handle']; ?>&gender=<?php echo $board['gender']; ?>&member_code=<?php echo $board['member_code']; ?>">
                                            <?php echo ($board['pass'] == 'confirm') ? '분석 완료' : '분석전'; ?>
                                        </a>
                                    </td>
                                    <td
                                        style="<?php if ($board['support'] != "") { if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <?php } else { ?>background-color: #3ED730; color: white;<?php }} ?>">
                                        <?php echo $board['support'] ?>
                                    </td>
                                    <td
                                        style="<?php if ($board['support2'] != "") { if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <?php } else { ?>background-color: #3ED730; color: white;<?php }} ?>">
                                        <?php echo $board['support2'] ?>
                                    </td>
                                    <td
                                        style="<?php if ($board['support3'] != "") { if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <?php } else { ?>background-color: #3ED730; color: white;<?php }} ?>">
                                        <?php echo $board['support3'] ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } else {
                                    $sql2 = "SELECT * FROM finger_info WHERE 
                                    date LIKE :search_word OR
                                    finger_type LIKE :search_word OR
                                    user_name LIKE :search_word OR
                                    handle LIKE :search_word OR
                                    user_code LIKE :search_word
                                    ORDER BY idx DESC
                                    LIMIT $start_num, $list";
                                    $stmt = $con->prepare($sql2);
                                    $likeSearchWord = '%' . $search_word . '%';
                                    $stmt->bindParam(':search_word', $likeSearchWord);
                                    $stmt->execute();
                                    while ($board = $stmt->fetch()) {
                                        $sql2 = "SELECT * FROM error_support WHERE user_code = :user_code";
                                        $stmt2 = $con->prepare($sql2);
                                        $stmt2->bindParam(':user_code', $board['user_code']);
                                        $stmt2->execute();
                                        $result7 = $stmt2->fetch();
                                ?>
                                <tr
                                    style="<?php if ($result7['user_code'] == $board['user_code']) { ?> background-color: #8B98BD; color: white;<?php } else if ($board['finger_type'] == "") { ?> background-color: #D3D3D3; <?php } ?>">
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td><?php echo $board['date'] ?></td>
                                    <td><?php echo $board['finger_type'] ?></td>
                                    <td><?php echo $board['user_name'] ?></td>
                                    <td><?php echo $board['handle'] ?></td>
                                    <td><?php echo $board['user_code'] ?></td>
                                    <td><a class="btn btn-sm btn-primary"
                                            href="fp_err.html?user_code=<?php echo $board['user_code'] ?>&user_name=<?php echo $board['user_name'] ?>&birth=<?php echo $board['birth'] ?>&handle=<?php echo $board['handle'] ?>&gender=<?php echo $board['gender'] ?>&member_code=<?php echo $board['member_code'] ?>">확인</a>
                                    </td>
                                    <td
                                        style="<?php if ($board['support'] != "") { if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <?php } else { ?>background-color: #3ED730; color: white;<?php }} ?>">
                                        <?php echo $board['support'] ?>
                                    </td>
                                    <td
                                        style="<?php if ($board['support2'] != "") { if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <?php } else { ?>background-color: #3ED730; color: white;<?php }} ?>">
                                        <?php echo $board['support2'] ?>
                                    </td>
                                    <td
                                        style="<?php if ($board['support3'] != "") { if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <?php } else { ?>background-color: #3ED730; color: white;<?php }} ?>">
                                        <?php echo $board['support3'] ?>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- 페이지 네비게이션 -->
                    <div class="container">
                        <div class="row">
                            <div class="col-4"></div>
                            <div class="col-4">
                                <ul class="pagination" style="place-content: center;margin: 15px;">
                                    <?php
                                    if ($page > 1) {
                                        echo "<li class='page-item'><a class='page-link' href='?page=1'> ◀◀ </a></li>";
                                        $pre = $page - 1;
                                        echo "<li class='page-item'><a class='page-link' href='?page=$pre'>◀</a></li>";
                                    }
                                    
                                    for ($i = $block_start; $i <= $block_end; $i++) {
                                        if ($page == $i) {
                                            echo "<li class='page-item this_page'><a class='page-link this_page' style='background-color:#dce8ff;'>$i</a></li>";
                                        } else {
                                            echo "<li><a class='page-link' href='?page=$i'>$i</a></li>";
                                        }
                                    }

                                    if ($page < $total_page) {
                                        $next = $page + 1;
                                        echo "<li class='page-item'><a class='page-link' href='?page=$next'> ▶ </a></li>";
                                        echo "<li class='page-item'><a class='page-link' href='?page=$total_page'> ▶▶ </a></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 최근 보고서 테이블 -->
            <div class="container-fluid pt-4 px-4 mb-5">
                <form onsubmit="search_(event)">
                    <input type="text" name="keyword_" id="keyword_" placeholder="검색어를 입력하세요">
                    <input type="submit" value="검색">
                </form>
                <div class="bg-light text-center rounded p-1" id="divReloadLayer3"
                    style="background-color: white !important;">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">최근 보고서</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table text-center align-middle table-bordered table-hover mb-0">
                            <thead style="background-color: #C1BEEB;">
                                <tr class="text-center">
                                    <th scope="col"><input class="form-check-input" type="checkbox"></th>
                                    <th scope="col">검사 날짜</th>
                                    <th scope="col">유형</th>
                                    <th scope="col">검사자</th>
                                    <th scope="col">주손잡이</th>
                                    <th scope="col">연락처</th>
                                    <th scope="col-1">보고서 번호</th>
                                    <th scope="col" style="background-color: #FFD97C;">인쇄</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $page2 = isset($_GET['page2']) ? $_GET['page2'] : 1;
                                    $start_num = ($page2 - 1) * $list;
                                    $search_word_ = isset($search_word_) ? $search_word_ : '';
                                    if ($search_word_ == "") {
                                        $sql2 = "SELECT * FROM finger_info ORDER BY idx DESC LIMIT $start_num, $list";
                                    } else {
                                        $sql2 = "SELECT * FROM finger_info WHERE 
                                        date LIKE :search_word OR
                                        finger_type LIKE :search_word OR
                                        user_name LIKE :search_word OR
                                        handle LIKE :search_word OR
                                        user_code LIKE :search_word
                                        ORDER BY idx DESC
                                        LIMIT $start_num, $list";
                                        $stmt = $con->prepare($sql2);
                                        $likeSearchWord = '%' . $search_word_ . '%';
                                        $stmt->bindParam(':search_word', $likeSearchWord);
                                        $stmt->execute();
                                    }

                                    $stmt = $con->prepare($sql2);
                                    $stmt->execute();
                                    while ($board = $stmt->fetch()) {
                                ?>
                                <tr>
                                    <td><input class="form-check-input" type="checkbox"></td>
                                    <td><?php echo $board['date'] ?></td>
                                    <td><?php echo $board['finger_type'] ?></td>
                                    <td><?php echo $board['user_name'] ?></td>
                                    <td><?php echo $board['handle'] ?></td>
                                    <td><?php echo $board['user_code'] ?></td>
                                    <td><?php echo $board['member_code'] ?></td>
                                    <?php if ($board['finger_type'] == "심화") { ?>
                                    <td><a href="javascript:void(0);"
                                            onclick="openPrintWindow('http://aitms.co.kr/capfingers/result_private_final.php?member_code=<?php echo $board['member_code'] ?>')"><i
                                                class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>
                                    <?php } else { ?>
                                    <td><a href="javascript:void(0);"
                                            onclick="openPrintWindow('http://aitms.co.kr/capfingers/<?php echo $board['base_url'] ?>')"><i
                                                class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <div class="container">
                            <div class="row">
                                <div class="col-4"></div>
                                <div class="col-4">
                                    <ul class="pagination" style="place-content: center;margin: 15px;">
                                        <?php
                                            if ($page2 <= 1) { 
                                                echo "<li class='page-item'><a class='page-link' href='#'> ◀◀ </a></li>"; 
                                            } else {
                                                echo "<li class='page-item'><a class='page-link' href='?page2=1'> ◀◀ </a></li>"; 
                                            }
                                            if ($page2 <= 1) { 
                                                // 빈값
                                            } else {
                                                $pre = $page2 - 1; 
                                                echo "<li class='page-item'><a class='page-link' href='?page2=$pre'>◀</a></li>"; 
                                            }                
                                            for ($i = $block_start; $i <= $block_end; $i++) {
                                                if ($page2 == $i) { 
                                                    echo "<li class='page-item this_page' ><a class='page-link this_page' style='background-color:#dce8ff;'> $i </a> </li>"; 
                                                } else {
                                                    echo "<li><a class='page-link' href='?page2=$i'>$i </a></li>"; 
                                                }
                                            }
                                            if ($page2 >= $total_page) { 
                                                // 빈값
                                            } else {
                                                $next = $page2 + 1; 
                                                echo "<li class='page-item'><a class='page-link' href='?page2=$next'> ▶ </a></li>";
                                            }
                                            if ($page2 >= $total_page) { 
                                                // 빈값
                                            } else {
                                                echo "<li class='page-item' style='letter-spacing:0.1px;'><a class='page-link' href='?page2=$total_page'> ▶▶ </a></li>"; 
                                            }
                                        ?>
                                    </ul>
                                </div>
                                <div class="col-4 text-right"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4" style="position: absolute;bottom: -10%;">
                <div class="bg-light rounded-top p-4 mt-5">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a href="#">Copyright © 2012-2020 캡티칭</a>, All Right Reserved.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    <script>
    function openPrintWindow(url) {
        var printWindow = window.open(url, 'PrintWindow', 'width=800,height=600');
        printWindow.addEventListener('load', function() {
            printWindow.print();
        }, true);
    }
    </script>

</body>

</html>