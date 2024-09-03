<?php session_start();
include_once("header2.php");
?>


<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>wave3d</title>

    <link href="signin.css" rel="stylesheet">
    <style>
        body {
            background-color: #282f39;
        }

        @media (min-width: 992px) {
            #login {
                margin-top: -450px;
                margin-left: 83px;
            }
        }

        @media (max-width: 1200px) {
            #login {
                margin-top: -445px;
                margin-left: 91px;
            }
        }
    </style>
</head>

<body>
    <div style="width: 100%;">
        <div>
            <img src="img/메인수정.png" style="width: 1920px;">
        </div>
        <div class="container" id="login">
            <?php
            if (!isset($_SESSION['id'])) { ?>
                <form action="login-check.php" method="post" encType="multiplart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="radio" id="consultant" name="role" value="consultant" required>
                            <label for="consultant">컨설턴트</label>
                            <input type="radio" id="analyst" name="role" value="analyst" required>
                            <label for="analyst">지문분석전문가</label>
                        </div>
                        <div class="col-md-8"></div>
                        <div class="col-md-4" style="margin-top: 5px;">
                            <label for="id" class="sr-only">ID</label>
                            <input type="text" id="id" name="id" class="form-control" placeholder="ID" required autofocus>
                        </div>
                        <div class="col-md-8"></div>
                        <div class="col-md-4" style="margin-top: 5px;">
                            <label for="pw" class="sr-only">Password</label>
                            <input type="password" id="pw" name="pw" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-lg btn-primary btn-block" type="submit">로그인</button>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <a href="BNS_contract.php">회원가입</a> |
                            <a href="findID.php">아이디 찾기</a> |
                            <a href="findPW.php">비밀번호 찾기</a>
                        </div>
                    </div>
                </form>
            <?php } else {
                $id = $_SESSION['id'];
                echo "<div class='text-left'>";
                echo "<p style='color:white'>($id)님은 이미 로그인하고 있습니다. ";
                echo "<a href='javascript:history.back()'>[돌아가기]</a> ";
                echo "<a href=\"adminLogout.php\">[로그아웃]</a></p>";
                echo "</div>";
            } ?>
        </div>
    </div>
</body>

</html>