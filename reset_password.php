<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/css/resetpw.css" />
    <title>비밀번호 재설정</title>
</head>

<body>
    <?php if ($token_error): ?>
    <div class="message-container">
        <p><?php echo $password_reset_message; ?></p>
    </div>
    <a href="adminLogin.php" class="confirm-button">확인</a>
    <?php elseif ($password_reset): ?>
    <div class="pw-container">
        <h2 class="pw-title">새 비밀번호 설정</h2>
        <form method="post" action="">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

            <div class="pw-form">
                <label for="new_password">새 비밀번호</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>

            <div class="pw-form">
                <label for="confirm_password">비밀번호 확인</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="button-container">
                <input type="button" value="취소" onclick="goBack()" class="confirm-button">
                <input type="submit" value="비밀번호 재설정" class="confirm-button">
            </div>
        </form>
    </div>
    <?php else: ?>
    <div class="message-container">
        <p><?php echo $password_reset_message; ?></p>
    </div>
    <a href="adminLogin.php" class="confirm-button">확인</a>
    <?php endif; ?>

    <script>
    function goBack() {
        window.history.back(); // 이전 페이지로 돌아가는 함수
    }
    </script>

</body>

</html>