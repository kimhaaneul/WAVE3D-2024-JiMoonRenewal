<?php
@session_start();
include_once("db.php");

if (!isset($_SESSION['id'])) {
    echo "<script>
		   alert('로그인이 필요합니다.');
		   window.location.href='adminLogin.php';
	   </script>";
} else if ($_SESSION['position'] == 1 || $_SESSION['position'] == 2 || $_SESSION['position'] == 4) {
    $id = $_SESSION['id'];
} else {
    echo "<script>
    alert('접근권한이 없습니다.');
     window.location.href='adminLogin.php';
    </script>";
}

$sql2 = "SELECT count(*) as acount FROM finger_info";
$stmt2 = $con->prepare($sql2);
$stmt2->execute();
$result10 = $stmt2->fetch();
$acounter = $result10['acount'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CAP AIFingers</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <!--    <link href="img/favicon.ico" rel="icon">-->

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
    <link href="css/index_analyst.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.slim.js"
        integrity="sha256-HwWONEZrpuoh951cQD1ov2HUK5zA5DwJ1DNUXaM6FsY=" crossorigin="anonymous"></script>
</head>
<?php
$sql2 = "SELECT * FROM consultant where id = '$id'";
$stmt = $con->prepare($sql2);
$stmt->execute();
$result5 = $stmt->fetch();

if ($result5['position'] == 1) {
    $job = "대표";
    $img = "support3";
} else if ($result5['position'] == 2) {
    $job = "이사";
    $img = "support3";
} else if ($result5['position'] == 3) {
    $job = "팀장";
    $img = "support6";
} else if ($result5['position'] == 4) {
    $job = "연구원";
    $img = "support4";
} else if ($result5['position'] == 5) {
    $job = "분석사";
    $img = "support4";
} else {
    $job = "안녕하세요";
    $img = "support5";
}
?>
<script>
Notification.requestPermission().then(function(result) {
    console.log(result);
});
let permissionCheck = Notification.permission;
//		getNotificationPermission();
//		alert(permissionCheck);
//		console.log(permission);
//알림 권한 요청
function getNotificationPermission() {
    // 브라우저 지원 여부 체크
    if (!("Notification" in window)) {
        alert("데스크톱 알림을 지원하지 않는 브라우저입니다.");
    }
    // 데스크탑 알림 권한 요청
    Notification.requestPermission(function(result) {
        // 권한 거절
        if (result == 'denied') {
            Notification.requestPermission();
            alert('알림을 차단하셨습니다.\n브라우저의 사이트 설정에서 변경하실 수 있습니다.');
            return false;
        } else if (result == 'granted') {
            alert('알림을 허용하셨습니다.');
        }
    });
}

function notify(msg) {
    var options = {
        body: msg
    }



    // 3초뒤 알람 닫기
    setTimeout(function() {
        notification.close();
    }, 3000);
}
</script>

<body>
    <!-- Sidebar Start -->
    <div class="sidebar">
        <div class="sidebar-jm-box">
            <img src="/img/cap-jm.png">
        </div>
        <nav class="navbar">
            <div class="d-flex align-items-center">
                <div class="position-relative">
                </div>
                <div class="ms-3">
                    <h6 class="mb-0">
                        <? echo $result5['name'] ?>&nbsp;
                        <? echo $job ?>님
                    </h6>
                    <div class="logout-box">
                        <span>반갑습니다.</span>
                        <span><a href="../adminLogout.php" class="sidebar-logout">x</a></span>
                    </div>
                </div>
            </div>

            <div class="navbar-nav w-100">
                <a href="index.php" class="nav-item nav-link active" style="padding: 15px 20px;"><i
                        class="fa fa-home me-2"></i>첫
                    화면</a>
                <div class="navbar-nav w-100 mb-1" style="border-bottom-style: ridge;"></div>
        </nav>
    </div>
    <!-- Sidebar End -->

    <div class="container">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Content Start -->
        <div class="content">
            <div style="background-color: #f5f5f5;">
                <script>
                function reloadDivArea9() {
                    var currentLocation = window.location;
                    $("#divReloadLayer9").load(currentLocation + ' #divReloadLayer9');
                }

                setInterval('reloadDivArea9()', 1000); //30초 후 새로고침

                function reloadDivArea8() {
                    var currentLocation = window.location;
                    $("#divReloadLayer8").load(currentLocation + ' #divReloadLayer8');
                }

                setInterval('reloadDivArea8()', 1000); //30초 후 새로고침

                function reloadDivArea7() {
                    var currentLocation = window.location;
                    $("#divReloadLayer7").load(currentLocation + ' #divReloadLayer7');
                }

                setInterval('reloadDivArea7()', 1000); //30초 후 새로고침

                //						setTimeout('reloadDivArea5()', 1000); //30초 후 새로고침	
                </script>
                <?php

                $search_word = isset($_GET["keyword"]) ? $_GET["keyword"] : '';
                $search_word_ = isset($_GET["keyword_"]) ? $_GET["keyword_"] : '';


                $sql2 = "SELECT * FROM consultant where support = 'support'";
                $stmt = $con->prepare($sql2);
                $stmt->execute();
                $r1 = $stmt->fetch();

                if ($r1['position'] == 1) {
                    $img2 = "support3.png";
                } else if ($r1['position'] == 2) {
                    $img2 = "support3.png";
                } else if ($r1['position'] == 3) {
                    $img2 = "support6.png";
                } else if ($r1['position'] == 4) {
                    $img2 = "support4.png";
                } else if ($r1['position'] == 5) {
                    $img2 = "support5.png";
                } else {
                    $img2 = "user.jpg";
                }

                $sql2 = "SELECT * FROM consultant where support = 'support2'";
                $stmt = $con->prepare($sql2);
                $stmt->execute();
                $r2 = $stmt->fetch();

                if ($r2['position'] == 1) {
                    $img3 = "support3.png";
                } else if ($r2['position'] == 2) {
                    $img3 = "support3.png";
                } else if ($r2['position'] == 3) {
                    $img3 = "support6.png";
                } else if ($r2['position'] == 4) {
                    $img3 = "support4.png";
                } else if ($r2['position'] == 5) {
                    $img3 = "support5.png";
                } else {
                    $img3 = "user.jpg";
                }

                $sql2 = "SELECT * FROM consultant where support = 'support3'";
                $stmt = $con->prepare($sql2);
                $stmt->execute();
                $r3 = $stmt->fetch();

                if ($r3['position'] == 1) {
                    $img4 = "support3.png";
                } else if ($r3['position'] == 2) {
                    $img4 = "support3.png";
                } else if ($r3['position'] == 3) {
                    $img4 = "support6.png";
                } else if ($r3['position'] == 4) {
                    $img4 = "support4.png";
                } else if ($r3['position'] == 5) {
                    $img4 = "support5.png";
                } else {
                    $img4 = "user.jpg";
                }
                ?>



                <!-- Sales Chart End -->
                <script>
                function reloadDivArea() {
                    var currentLocation = window.location;
                    $("#divReloadLayer").load(currentLocation + ' #divReloadLayer');
                }

                setInterval('reloadDivArea()', 1000); //30초 후 새로고침
                </script>


                <!-- Recent Sales Start -->
                <div class="analyst-content">
                    <div class="analyst-content-box">
                        <h3 class="mb-0">검사 오류 지원</h3>
                        <form onsubmit="search_(event)">
                            <input type="text" name="keyword" id="keyword" placeholder="검색어를 입력하세요">
                            <input type="submit" value="검색">
                        </form>
                    </div>
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
                            <?
                            if (isset($_GET['page'])) {
                                $page = $_GET['page'];
                            } else {
                                $page = 1;
                            }
                            $sql = "select * from finger_info;";
                            $stmt = $con->prepare($sql);
                            $stmt->execute();
                            $row_num = $stmt->rowCount();
                            $list = 10; //한 페이지에 보여줄 개수
                            $block_ct = 5; //블록당 보여줄 페이지 개수

                            $block_num = ceil($page / $block_ct); // 현재 페이지 블록 구하기
                            $block_start = (($block_num - 1) * $block_ct) + 1; // 블록의 시작번호
                            $block_end = $block_start + $block_ct - 1; //블록 마지막 번호

                            $total_page = ceil($row_num / $list); // 페이징한 페이지 수 구하기
                            if ($block_end > $total_page) $block_end = $total_page; //만약 블록의 마지박 번호가 페이지수보다 많다면 마지박번호는 페이지 수
                            $total_block = ceil($total_page / $block_ct); //블럭 총 개수
                            $start_num = ($page - 1) * $list; //시작번호 (page-1)에서 $list를 곱한다.

                            if ($search_word == "") {
                                $sql2 = "select * from finger_info order by idx desc limit $start_num, $list";

                                $stmt = $con->prepare($sql2);
                                $stmt->execute();
                                while ($board = $stmt->fetch()) {
                                    $sql2 = "SELECT * FROM error_support where user_code = '$board[user_code]'";
                                    $stmt2 = $con->prepare($sql2);
                                    $stmt2->execute();
                                    $result7 = $stmt2->fetch(); ?>

                            <tr
                                style="<?php if ($result7['user_code'] == $board['user_code']) { ?> background-color: #8B98BD; color: white;<? } else if ($board['finger_type'] == "") { ?> background-color: #D3D3D3; <? } ?>">
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <? echo $board['date'] ?>
                                </td>
                                <td>
                                    <? echo $board['finger_type'] ?>
                                </td>
                                <td>
                                    <? echo $board['user_name'] ?>
                                </td>
                                <td>
                                    <? echo $board['handle'] ?>
                                </td>
                                <td>
                                    <? echo $board['user_code'] ?>
                                </td>
                                <td>
                                    <? echo $board['member_code'] ?>
                                </td>
                                <td>
                                    <a class="btn btn-sm <?php echo ($board['pass'] == 'confirm') ? 'btn-danger' : 'btn-primary'; ?>"
                                        href="fp_err.html?user_code=<?php echo $board['user_code']; ?>&user_name=<?php echo $board['user_name']; ?>&birth=<?php echo $board['birth']; ?>&handle=<?php echo $board['handle']; ?>&gender=<?php echo $board['gender']; ?>&member_code=<?php echo $board['member_code']; ?>">
                                        <?php echo ($board['pass'] == 'confirm') ? '분석 완료' : '분석전'; ?>
                                    </a>
                                </td>

                                <!--
                                    <td style="<?php if ($board['support'] != "") {
                                                    if ($result7['user_code'] != "") { ?>background-color: #EE643C; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                    } ?>"><? echo $board['support'] ?></td>
                                    <td style="<?php if ($board['support2'] != "") {
                                                    if ($result7['user_code'] != "") { ?>background-color: #EE643C; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                    } ?>"><? echo $board['support2'] ?></td>
                                    <td style="<?php if ($board['support3'] != "") {
                                                    if ($result7['user_code'] != "") { ?>background-color: #EE643C; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                    } ?>"><? echo $board['support3'] ?></td>
-->

                                <td
                                    style="<?php if ($board['support'] != "") {
                                                        if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                                                                                        } ?>">
                                    <? echo $board['support'] ?>
                                </td>
                                <td
                                    style="<?php if ($board['support2'] != "") {
                                                        if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                                                                                        } ?>">
                                    <? echo $board['support2'] ?>
                                </td>
                                <td
                                    style="<?php if ($board['support3'] != "") {
                                                        if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                                                                                        } ?>">
                                    <? echo $board['support3'] ?>
                                </td>
                            </tr>

                            <? } ?>
                            <? } else { ?>
                            <?
                                $sql2 = $sql = "SELECT * FROM finger_info WHERE 
                                date LIKE '%$search_word%' OR
                                finger_type LIKE '%$search_word%' OR
                                user_name LIKE '%$search_word%' OR
                                handle LIKE '%$search_word%' OR
                                user_code LIKE '%$search_word%'
                               ORDER BY idx DESC
                               LIMIT $start_num, $list";
                                $stmt = $con->prepare($sql2);
                                $stmt->execute();
                                while ($board = $stmt->fetch()) {
                                    $sql2 = "SELECT * FROM error_support where user_code = '$board[user_code]'";
                                    $stmt2 = $con->prepare($sql2);
                                    $stmt2->execute();
                                    $result7 = $stmt2->fetch(); ?>

                            <tr
                                style="<?php if ($result7['user_code'] == $board['user_code']) { ?> background-color: #8B98BD; color: white;<? } else if ($board['finger_type'] == "") { ?> background-color: #D3D3D3; <? } ?>">
                                <td><input class="form-check-input" type="checkbox"></td>
                                <td>
                                    <? echo $board['date'] ?>
                                </td>
                                <td>
                                    <? echo $board['finger_type'] ?>
                                </td>
                                <td>
                                    <? echo $board['user_name'] ?>
                                </td>
                                <td>
                                    <? echo $board['handle'] ?>
                                </td>
                                <td>
                                    <? echo $board['user_code'] ?>
                                </td>
                                <td>
                                    <? echo $board['member_code'] ?>
                                </td>
                                <td><a class="btn btn-sm btn-primary"
                                        href="fp_err.html?user_code=<? echo $board['user_code'] ?>&user_name=<? echo $board['user_name'] ?>&birth=<? echo $board['birth'] ?>&handle=<? echo $board['handle'] ?>&gender=<? echo $board['gender'] ?>&member_code=<? echo $board['member_code'] ?>">확인</a>
                                </td>

                                <!-- <td style="<?php if ($board['support'] != "") {
                                                            if ($result7['user_code'] != "") { ?>background-color: #EE643C; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                            } ?>"><? echo $board['support'] ?></td>
                                            <td style="<?php if ($board['support2'] != "") {
                                                            if ($result7['user_code'] != "") { ?>background-color: #EE643C; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                            } ?>"><? echo $board['support2'] ?></td>
                                            <td style="<?php if ($board['support3'] != "") {
                                                            if ($result7['user_code'] != "") { ?>background-color: #EE643C; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                            } ?>"><? echo $board['support3'] ?></td>
        -->

                                <td
                                    style="<?php if ($board['support'] != "") {
                                                        if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                                                                                        } ?>">
                                    <? echo $board['support'] ?>
                                </td>
                                <td
                                    style="<?php if ($board['support2'] != "") {
                                                        if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                                                                                        } ?>">
                                    <? echo $board['support2'] ?>
                                </td>
                                <td
                                    style="<?php if ($board['support3'] != "") {
                                                        if ($board['support'] == "" || $board['support2'] == "" || $board['support3'] == "") { ?>background-color: #EE643C;color: white; <? } else { ?>background-color: #3ED730; color: white;<? }
                                                                                                                                                                                                                                        } ?>">
                                    <? echo $board['support3'] ?>
                                </td>
                            </tr>

                            <? } ?>
                            <? } ?>
                        </tbody>
                    </table>

                    <div class="container">
                        <div class="page-num">
                            <div class="col-4">
                                <ul class="pagination" style="place-content: center;margin: 15px;">
                                    <?
                                    if ($page <= 1) { //만약 page가 1보다 크거나 같다면
                                        //  echo "<li class='page-item'><a class = 'page-link' href='#'> << </a></li>"; //처음이라는 글자에 빨간색 표시 
                                    } else {
                                        echo "<li class='page-item'><a class = 'page-link' href='?page=1'> ◀◀ </a></li>"; //알니라면 처음글자에 1번페이지로 갈 수있게 링크
                                    }
                                    if ($page <= 1) { //만약 page가 1보다 크거나 같다면 빈값
                                    } else {
                                        $pre = $page - 1; //pre변수에 page-1을 해준다 만약 현재 페이지가 3인데 이전버튼을 누르면 2번페이지로 갈 수 있게 함
                                        echo "<li class='page-item'><a class = 'page-link' href='?page=$pre'>◀</a></li>"; //이전글자에 pre변수를 링크한다. 이러면 이전버튼을 누를때마다 현재 페이지에서 -1하게 된다.
                                    }
                                    for ($i = $block_start; $i <= $block_end; $i++) {
                                        //for문 반복문을 사용하여, 초기값을 블록의 시작번호를 조건으로 블록시작번호가 마지박블록보다 작거나 같을 때까지 $i를 반복시킨다
                                        if ($page == $i) { //만약 page가 $i와 같다면 
                                            echo "<li class='page-item this_page' ><a class = 'page-link this_page' style ='background-color:#dce8ff;'> $i </a> </li>"; //현재 페이지에 해당하는 번호에 굵은 빨간색을 적용한다
                                        } else {
                                            echo "<li><a class = 'page-link' href='?page=$i'>$i </a></li>"; //아니라면 $i
                                        }
                                    }

                                    if ($page >= $total_page) { //만약 현재 블록이 블록 총개수보다 크거나 같다면 빈 값
                                    } else {
                                        $next = $page + 1; //next변수에 page + 1을 해준다.
                                        echo "<li class='page-item'><a class = 'page-link' href='?page=$next'> ▶ </a></li>"; //다음글자에 next변수를 링크한다. 현재 4페이지에 있다면 +1하여 5페이지로 이동하게 된다.
                                    }
                                    if ($page >= $total_page) { //만약 page가 페이지수보다 크거나 같다면
                                    } else {
                                        echo "<li class='page-item' style='letter-spacing:0.1px;'><a class = 'page-link' href='?page=$total_page'> ▶▶ </a></li>"; //아니라면 마지막글자에 total_page를 링크한다.
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <script>
            function search(event) {
                event.preventDefault(); // 폼 제출 이벤트 막기

                var keyword = document.getElementById("keyword").value;
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "index_search_test.php?keyword=" + encodeURIComponent(keyword), true);
                xhr.send();
            }
            </script>
            <!--
	<script>

		function reloadDivArea6() {
		var currentLocation = window.location;	
		$("#divReloadLayer6").load(currentLocation + ' #divReloadLayer6');
	   var audio = document.getElementById("audio");
       <?php
        $sql2 = "SELECT count(*) as counta FROM finger_info";
        $stmt2 = $con->prepare($sql2);
        $stmt2->execute();
        $result11 = $stmt2->fetch();
        if ($acounter != $result11['counta']) { ?>
			audio.play();	
		   // 데스크탑 알림 요청
    		new Notification("새로운 지문 검사자가 있습니다.", options);
			<?
            $acounter = $acounter + 1;
        } else { ?>
			  audio.stop();	
		  <? } ?>
		}

		setInterval('reloadDivArea6()', 4000); //30초 후 새로고침


	</script>
	
			<div id="divReloadLayer6">

			<audio id="audio" autoplay>
				<source src="mp3/a_mp3.MP3" type="audio/MP3">
				
			</audio>
-->

            <!-- Recent Sales End -->
            <script>
            function reloadDivArea3() {
                var currentLocation = window.location;
                $("#divReloadLayer3").load(currentLocation + ' #divReloadLayer3');
            }

            setInterval('reloadDivArea3()', 1000); //30초 후 새로고침
            </script>

            <!-- Recent Sales Start -->
            <div class="analyst-content">
                <div class="analyst-content-box">
                    <h3 class="mb-0">최근 보고서</h3>
                    <form onsubmit="search_(event)">
                        <input type="text" name='keyword_' id='keyword_' placeholder="검색어를 입력하세요">
                        <input type="submit" value="검색">
                    </form>
                </div>
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
                        <?
                        if (isset($_GET['page'])) {
                            $page = $_GET['page'];
                        } else {
                            $page = 1;
                        }
                        $sql = "select * from finger_info;";
                        $stmt = $con->prepare($sql);
                        $stmt->execute();
                        $row_num = $stmt->rowCount();
                        $list = 10; //한 페이지에 보여줄 개수
                        $block_ct = 5; //블록당 보여줄 페이지 개수

                        $block_num = ceil($page / $block_ct); // 현재 페이지 블록 구하기
                        $block_start = (($block_num - 1) * $block_ct) + 1; // 블록의 시작번호
                        $block_end = $block_start + $block_ct - 1; //블록 마지막 번호

                        $total_page = ceil($row_num / $list); // 페이징한 페이지 수 구하기
                        if ($block_end > $total_page) $block_end = $total_page; //만약 블록의 마지박 번호가 페이지수보다 많다면 마지박번호는 페이지 수
                        $total_block = ceil($total_page / $block_ct); //블럭 총 개수
                        $start_num = ($page - 1) * $list; //시작번호 (page-1)에서 $list를 곱한다.
                        if ($search_word_ == "") {

                            $sql2 = "select * from finger_info order by idx desc limit $start_num, $list";

                            $stmt = $con->prepare($sql2);
                            $stmt->execute();
                            while ($board = $stmt->fetch()) {
                        ?>
                        <tr>
                            <td><input class="form-check-input" type="checkbox"></td>
                            <td>
                                <? echo $board['date'] ?>
                            </td>
                            <td>
                                <? echo $board['finger_type'] ?>
                            </td>
                            <td>
                                <? echo $board['user_name'] ?>
                            </td>
                            <td>
                                <? echo $board['handle'] ?>
                            </td>
                            <td>
                                <? echo $board['user_code'] ?>
                            </td>
                            <td>
                                <? echo $board['member_code'] ?>
                            </td>

                            <?php if ($board['finger_type'] == "심화") { ?>
                            <td><a
                                    href="http://aitms.co.kr/capfingers/result_private_final.php?member_code=<?php echo $board['member_code'] ?>"><i
                                        class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>

                            <?php } else { ?>
                            <?php if ($board['handle'] == "오른손") {
                                        ?>
                            <td>
                                <a href="http://aitms.co.kr/capfingers/<?php echo $board['base_url'] ?>">
                                    <i class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a>
                            </td>
                            <? } else { ?>
                            <td><a href="http://aitms.co.kr/capfingers/<?php echo $board['base_url'] ?>"><i
                                        class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>
                            <? } ?>

                        </tr>
                        <? } ?>
                        <? } ?>
                        <? } else { ?>
                        <?
                            $sql2 = $sql = "SELECT * FROM finger_info WHERE 
                                                            date LIKE '%$search_word_%' OR
                                                            finger_type LIKE '%$search_word_%' OR
                                                            user_name LIKE '%$search_word_%' OR
                                                            handle LIKE '%$search_word_%' OR
                                                            user_code LIKE '%$search_word_%'
                                                        ORDER BY idx DESC
                                                        LIMIT $start_num, $list";

                            $stmt = $con->prepare($sql2);
                            $stmt->execute();
                            while ($board = $stmt->fetch()) {
                        ?>
                        <tr>
                            <td><input class="form-check-input" type="checkbox"></td>
                            <td>
                                <? echo $board['date'] ?>
                            </td>
                            <td>
                                <? echo $board['finger_type'] ?>
                            </td>
                            <td>
                                <? echo $board['user_name'] ?>
                            </td>
                            <td>
                                <? echo $board['handle'] ?>
                            </td>
                            <td>
                                <? echo $board['user_code'] ?>
                            </td>
                            <td>
                                <? echo $board['member_code'] ?>
                            </td>
                            <?php if ($board['finger_type'] == "심화") { ?>
                            <td><a
                                    href="http://aitms.co.kr/capfingers/result_private_final.php?member_code=<?php echo $board['member_code'] ?>"><i
                                        class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>

                            <?php } else { ?>
                            <?php
                                    if ($board['handle'] == "오른손") {
                                    ?>
                            <td><a
                                    href="http://aitms.co.kr/cms/result_private.php?member_code=<?php echo $board['member_code'] ?>"><i
                                        class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>
                            <? } else { ?>
                            <td><a
                                    href="http://aitms.co.kr/cms/result_private.php?member_code=<?php echo $board['member_code'] ?>"><i
                                        class="fa fa-print fa-2x" style="color: #FFAE00;"></i></a></td>
                            <? } ?>
                        </tr>
                        <? } ?>
                        <? } ?>
                        <? } ?>

                    </tbody>
                </table>
            </div>
        </div>
        <!-- 나중에 여기에 page 넘어가는거 달면 돼요. 이 주석 라인에-->
        <footer>
            <div class="container-footer">
                <div>
                    <p>Copyright ©2012 캡티칭 All Right Reserved. </p>
                </div>
            </div>
        </footer>
    </div>
    <!-- Recent Sales End -->
    <script>
    function search_(event)
    event.preventDefault(); // 폼 제출 이벤트 막기

    var keyword_ = document.getElementById("keyword_").value;
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "index_search_test.php?keyword_=" + encodeURIComponent(keyword_), true);
    xhr.send();
    </script>


    </div>
    <!-- Content End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>


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
</body>
<script>
window.addEventListener('load', function() {
    if (performance.navigation.type === 1) {
        // Type 1 means the page was refreshed
        window.location.href = 'index.php';
    }
});
</script>

</html>
</php>