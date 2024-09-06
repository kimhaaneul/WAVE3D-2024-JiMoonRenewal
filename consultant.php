<?php
session_start();
include_once("header2.php");
include_once("db.php");

$id = $_SESSION['id'];

// 유효한 세션이 없으면 로그인 페이지로 리디렉트
if (!isset($id)) {
    header("Location: adminLogin.php");
    exit;
}

$sql = "select * from select_field where id = '$id'";
$stmt = $con->prepare($sql);
$stmt->execute();
$board = $stmt->fetch();

$sql = "select * from select_mapping where id = '$id'";
$stmt = $con->prepare($sql);
$stmt->execute();
$board3 = $stmt->fetch();

$sql = "select * from result_approval where id = '$id'";
$stmt = $con->prepare($sql);
$stmt->execute();
$board4 = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">
    <link rel="stylesheet" href="css/consultant.css" />
    <title>CAP AI JIMOON CONSULTANT</title>

    <script type="text/javascript">
    var UserAgent = navigator.userAgent;
    if (UserAgent.match(
            /iPhone|ipad|Android|Windows CE|BlackBerry|Symbian|Windows Phone|webOS|Opera Mini|Opera Mobi|POLARIS|IEMobile|lgtelecom|nokia|SonyEricsson/i
        ) != null || UserAgent.match(/LG|SAMSUNG|Samsung/) != null) {
        location.href = "http://aitms.co.kr/capfingers/consultant.php"; /* 모바일 홈페이지 경로 */
    }
    </script>

</head>

<body>
    <div>
        <div class="cap-jm">
            <a href="adminLogin.php">
                <img src="/img/cap-jm.png"></a>
        </div>


        <?php if (!isset($_SESSION['id'])) { ?>
        <form action="login-checkBMS.php" method="post" encType="multiplart/form-data">
        </form>
        <?php } else { ?>

        <div class="consultant-container">
            <?php
                $name = $_SESSION['name'];
                echo "<div class='text-center'>";
                echo "<span>$name 님 환영합니다. </span>";
                echo "<span><a href=\"adminLogout.php\">[로그아웃]</a></span>";
                echo "</div>";
                ?>

            <div class="consultant-btnbox">
                <?php if ($_SESSION['id'] == $board['id']) { ?>
                <button class="btn btn-lg btn-primary btn-block" type="button"
                    onclick="location.href='BNS_contract.php'"
                    style="background-color: #FC4F52;border: 1px solid #FC4F52;">비밀유지계약서 (완료)</button>
                <?php } else { ?>
                <?php if ($board4['NDA'] != "") { ?>
                <button class="btn btn-lg btn-primary btn-block" type="button"
                    onclick="location.href='BNS_contract.php'"
                    style="background-color: #FC4F52;border: 1px solid #FC4F52;">비밀유지계약서 (완료)</button>
                <?php } else { ?>
                <button class="btn btn-lg btn-primary btn-block" type="button"
                    onclick="location.href='BNS_contract.php'">비밀유지계약서 (동의 필수)</button>
                <?php } ?>
                <?php } ?>
                <button id="reservation_list" class="btn btn-lg btn-primary btn-block" type="button"
                    onclick="location.href='reservation/reservation_form.php'">예약 관리</button>
            </div>
            <div
                style="<?php if (!isset($_SESSION['id'])) { ?>margin-left: 525px; position: absolute;margin-top: -334px;<?php } else { ?>  margin-left: -600px; position: absolute;margin-top: 150px;z-index:999;<?php } ?>">

            </div>
        </div>

    </div>
    <?php } ?>
    </div>

</body>

</html>