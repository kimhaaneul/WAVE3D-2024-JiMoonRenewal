<?php
session_start();
include_once("db.php");
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>CAP TEACHING BNS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <noscript>
        <link rel="stylesheet" href="assets/css/noscript.css" />
    </noscript>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/gh/moonspam/NanumSquare@1.0/nanumsquare.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style>
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

    .btn-custom {
        background-color: #FF6700;
        color: #fff;
        border: 1px solid #FF6700;
        padding: 8px 20px;
        font-size: 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s;
        margin: 10px;
    }

    .btn-custom:hover {
        background-color: #e55d00;
        color: #ffffff;
    }

    .custom-checkbox {
        width: 14px;
        height: 14px;
        transform: scale(1.5);
        vertical-align: middle;
    }
    </style>
</head>

<body class="is-preload">

    <div id="main" style="text-align: center;">
        <br><br><br>
        <h1 style="font-size: 2em;">BNS 계약서</h1><br>

        <div class="container border py-4 mb-5">
            <div class="col-12 pb-3 text-left">
                비밀유지계약서 <small class="text-info">(필수)</small>
            </div>

            <?php include_once('join-tos-box.php'); ?>

            <hr>
            <form id="agreementForm" method="post" action="BNS_agreeDB.php" onsubmit="return handleFormSubmit();">
                <input type="hidden" name="NDA" value="동의확인">

                <!-- 동의 체크박스 -->
                <div class="form-check mb-3 text-center">
                    <input type="checkbox" class="form-check-input custom-checkbox" id="agreeCheckbox" required>
                    <label class="form-check-label" for="agreeCheckbox">이용약관에 동의합니다.</label>
                </div>

                <div class="col-12 text-center">
                    <a href="adminLogin.php" class="btn btn-lg btn-danger ml-2">취소하기</a>
                    <button id="target_btn" type="submit" class="btn btn-custom">다음으로</button>

                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
    function handleFormSubmit() {
        const checkbox = document.getElementById('agreeCheckbox');

        if (!checkbox.checked) {
            alert("이용약관에 동의하셔야 합니다.");
            return false; // 동의하지 않은 경우 폼 제출을 막음
        }

        // 동의한 경우 다른 페이지로 이동하도록 action 설정
        const form = document.getElementById('agreementForm');
        form.action = "CMS_register_1.php";

        return true; // 폼 제출 허용
    }
    </script>

    <!-- Scripts -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>