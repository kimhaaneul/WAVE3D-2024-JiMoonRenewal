<?php
session_start();
$event_code = isset($_GET['event_code']) ? $_GET['event_code'] : '';
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>캡티칭 지문 예약 시스템</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }

    header,
    footer {
        background-color: #343a40;
        color: #ffffff;
        text-align: center;
        padding: 1rem 0;
    }

    header h1,
    footer p {
        margin: 0;
    }

    .container {
        max-width: 600px;
        margin: 2rem auto;
        background-color: #ffffff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        border-radius: 8px;
    }

    .form-box {
        margin-bottom: 1.5rem;
    }

    .form-box h3 {
        margin-bottom: 1rem;
        color: #007bff;
    }

    .form-group label {
        font-weight: bold;
    }

    .form-inline input[type="radio"] {
        margin-right: 0.5rem;
    }

    .btn-outline-info {
        width: 100%;
    }
    </style>
</head>

<body>
    <header>
        <h1>캡티칭 지문 예약 시스템</h1>
    </header>

    <main class="container mt-4">
        <form name="reservationForm" action="submit_reservation.php" method="post"
            onsubmit="return openReservationCompleteWindow()">
            <div class="form-box">
                <h3>예약 정보</h3>
                <div class="form-group">
                    <label for="event_code">코드:</label>
                    <input type="text" id="event_code" name="event_code" class="form-control"
                        value="<?= htmlspecialchars($event_code) ?>" readonly>
                </div>
            </div>

            <div class="form-box">
                <h3>예약자 정보</h3>
                <div class="form-group">
                    <label for="name">예약자 성함:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
    <label for="phone">전화번호:</label>
    <input type="tel" id="phone" name="phone" class="form-control" required pattern="\d+" title="숫자만 입력해주세요.">
</div>


                <div class="form-group">
                    <label>주 손잡이:</label>
                    <div class="form-inline">
                        <input type="radio" id="right_hand" name="hand_s" value="오른손" required><label
                            for="right_hand">오른손</label>
                        <input type="radio" id="left_hand" name="hand_s" value="왼손" required><label
                            for="left_hand">왼손</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="gender">성별</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="남성">남성</option>
                        <option value="여성">여성</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birth_date">생년월일</label>
                    <input type="text" id="birth_date" name="birth_date" class="form-control" maxlength="10" required>
                </div>
                <div class="form-group">
                    <label for="mbti">MBTI 타입:</label>
                    <select id="mbti" name="mbti" class="form-control">
                        <option value="">선택 안함</option>
                        <option value="INTJ">INTJ</option>
                        <option value="INTP">INTP</option>
                        <option value="INFJ">INFJ</option>
                        <option value="INFP">INFP</option>
                        <option value="ISTJ">ISTJ</option>
                        <option value="ISTP">ISTP</option>
                        <option value="ISFJ">ISFJ</option>
                        <option value="ISFP">ISFP</option>
                        <option value="ENTJ">ENTJ</option>
                        <option value="ENTP">ENTP</option>
                        <option value="ENFJ">ENFJ</option>
                        <option value="ENFP">ENFP</option>
                        <option value="ESTJ">ESTJ</option>
                        <option value="ESTP">ESTP</option>
                        <option value="ESFJ">ESFJ</option>
                        <option value="ESFP">ESFP</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-info">예약하기</button>
        </form>
    </main>

    <footer>
        <p>© 2024 예약 시스템</p>
    </footer>

    <script>
    $(document).ready(function() {
        $('#birth_date').datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: "1900:2024",
            beforeShow: function(input, inst) {
                $(input).attr("maxlength", "10");
            },
            onClose: function(dateText, inst) {
                const datePattern = /^\d{4}-\d{2}-\d{2}$/;
                if (!datePattern.test(dateText)) {
                    alert("날짜 형식이 잘못되었습니다. YYYY-MM-DD 형식으로 입력해주세요.");
                    $(this).val('');
                }
            }
        });

        $('#birth_date').on('input', function() {
            this.value = this.value.replace(/[^0-9\-]/g, '');
        });
    });


    function validateForm() {
        const form = document.forms['reservationForm'];
        const name = form['name'].value;
        const phone = form['phone'].value;
        const hand_s = form['hand_s'].value;
        const gender = form['gender'].value;
        const birth_date = form['birth_date'].value;

        if (!name || !phone || !hand_s || !gender || !birth_date) {
            alert('모든 필수 항목을 입력해 주세요.');
            return false;
        }
        return true;
    }
    </script>
</body>

</html>