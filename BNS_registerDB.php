<?php
session_start();
include_once("header2.php");
include_once("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];
    // Your code here
} else {
    // Handle the case where 'id' is not set
    echo "ID is not set.";
}


$sql = "select * from consultant where id = '$id'";
$stmt = $con->prepare($sql);
$stmt->execute();
$board = $stmt->fetch();

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
    $mail->addAddress('cap_edu@naver.com');
    $mail->addAddress($board['e_mail']);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = '가입 신청 완료 알림';
    $mail->Body    = "
        <h2>가입 신청이 성공적으로 완료되었습니다.</h2>
        <p>관리자 승인 대기 중입니다.</p>
        <p>아래는 가입 정보입니다:</p>
        <ul>
            <li>아이디: {$board['id']}</li>
            <li>이름: {$board['name']}</li>
            <li>연락처: {$board['phone_number']}</li>
            <li>주소: {$board['address']}</li>
            <li>회원 구분: {$board['job']}</li>
        </ul>
    ";

    $mail->send();
} catch (Exception $e) {
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>CAP TEACHING BNS</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/moonspam/NanumSquare@1.0/nanumsquare.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>
    <script src="http://code.jquery.com/jquery-latest.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script
        src="https://www.jqueryscript.net/demo/jQuery-Plugin-For-Fixed-Table-Header-Footer-Columns-TableHeadFixer/tableHeadFixer.js"></script>
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>
</head>
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
        color: #9092a2
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
                            <h2 style="color: #FF4F00;"> <?php echo $board['job']; ?> 가입신청이 성공적으로 완료 되었습니다.</h2>
                            <h2 style="color: #FF4F00;">관리자 승인 대기 중입니다.</h2>
                        </div>
                        <div class="col-2 text-center"></div>
                        <div class="col-12 mt-5 text-left box_label"
                            style="background: url('img/box.png');background-size: 100% 100%;">
                            · &nbsp;아이디 : <?php echo $board['id']; ?> <br>
                            · &nbsp;이름 : <?php echo $board['name']; ?> <br>
                            · &nbsp;연락처 : <?php echo $board['phone_number']; ?> <br>
                            · &nbsp;주소 : <?php echo $board['address']; ?> <br>
                            · &nbsp;회원 구분 : <?php echo $board['job']; ?> <br>
                            · &nbsp;E-MAIL : <?php echo $board['e_mail']; ?> <br>
                        </div>
                        <div class="col-2 text-center"></div>
                        <script>
                            $(document).ready(function () {
                                $('input[name=reg_id]').val('<?php echo $board['ID']; ?>');
                            });
                        </script>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline-info" style="margin-top: 40px;"
                onclick="location.href='http://aitms.co.kr/capfingers/adminLogin.php'">로그인</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
</body>

</html>
