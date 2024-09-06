<?php
include 'db.php';

$id_found = false;
$id_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $e_mail = $_POST['e_mail'];

    $query = "SELECT id FROM consultant WHERE name = :name AND phone_number = :phone_number AND e_mail = :e_mail";
    $stmt = $con->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':e_mail', $e_mail);
    $stmt->execute();

    $result = $stmt->fetch();

    if ($result) {
        $id_found = true;
        $id_message = "가입한 아이디는 " . $result['id'] . " 입니다.";
    } else {
        $id_message = "가입된 아이디가 없습니다. 다시 확인해 주세요.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/css/findid.css" />
    <title>아이디찾기</title>
</head>

<body>
    <?php if ($id_found || $_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <div class="message-container">
            <p><?php echo $id_message; ?></p>

        </div>
        <a href="adminLogin.php" class="confirm-button">확인</a>

    <?php else: ?>

        <div class="id-logo">
            <a href="/adminLogin.php">
                <img src="/img/cap-jm.png">
            </a>
        </div>
        <h2 class="id-title">아이디 찾기</h2>

        <div class="id-container">
            <form method="post" action="">
                <div class="id-form">
                    <label for="name">이름</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="id-form">
                    <label for="phone_number">가입한 휴대폰 번호</label>
                    <input type="text" id="phone_number" name="phone_number" required>
                </div>

                <div class="id-form">
                    <label for="e_mail">가입한 이메일</label>
                    <input type="email" id="e_mail" name="e_mail" required>
                </div>

                <div class="button-container">
                    <input type="button" value="취소" onclick="goBack()" class="confirm-button">
                    <input type="submit" value="아이디 찾기" class="confirm-button">
                </div>
            </form>
            <div class="password-recovery">
                <a href="find_password.php">비밀번호를 잃어버리셨나요?</a>
            </div>
        </div>

    <?php endif; ?>


    <script>
        function goBack() {
            window.history.back();
        }
    </script>

</body>

</html>