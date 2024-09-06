<?php
include 'db.php';  // 데이터베이스 연결 파일 포함

$password_reset = false;  // 비밀번호 재설정 상태를 나타내는 변수 초기화
$token_error = false;  // 토큰 오류 상태를 나타내는 변수 초기화
$password_reset_message = "";  // 사용자에게 표시할 메시지 변수 초기화

// 사용자가 비밀번호 재설정 페이지에 접근했을 때
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['token']) && !empty($_GET['token'])) {
        $token = $_GET['token'];  // URL에서 토큰 가져오기

        // 토큰 유효성 검사
        $query = "SELECT id FROM consultant WHERE reset_token = :token";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch();  // 쿼리 실행 결과를 가져옴

        if ($result) {
            // 유효한 토큰일 경우
            $password_reset = true;  // 비밀번호 재설정 상태를 true로 설정
            $user_id = $result['id'];  // 사용자 ID를 저장
        } else {
            // 유효하지 않은 토큰일 경우
            $token_error = true;  // 토큰 오류 상태를 true로 설정
            $password_reset_message = "비밀번호 찾기 중 유효하지 않은 토큰입니다. 다시 시도해 주세요.";  // 오류 메시지 설정
        }
    } else {
        // 토큰이 전혀 없는 경우
        $token_error = true;  // 토큰 오류 상태를 true로 설정
        $password_reset_message = "비밀번호 재설정 링크가 올바르지 않습니다. 다시 시도해 주세요.";  // 오류 메시지 설정
    }
}

// 사용자가 POST 요청으로 새 비밀번호를 제출했을 경우
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $token = $_POST['token'];  // 제출된 토큰
    $new_password = $_POST['new_password'];  // 새 비밀번호
    $confirm_password = $_POST['confirm_password'];  // 비밀번호 확인

    if ($new_password === $confirm_password) {
        // 토큰 유효성 검사
        $query = "SELECT id FROM consultant WHERE reset_token = :token";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch();  // 쿼리 실행 결과를 가져옴

        if ($result) {
            // 유효한 토큰일 경우 비밀번호 업데이트
            $query = "UPDATE consultant SET password = :password, reset_token = NULL WHERE id = :id";
            $stmt = $con->prepare($query);
            $stmt->bindParam(':password', $new_password);  // 해시화 없이 비밀번호 바로 저장
            $stmt->bindParam(':id', $result['id']);
            if ($stmt->execute()) {
                $password_reset_message = "비밀번호가 성공적으로 재설정되었습니다.";  // 성공 메시지 설정
            } else {
                $password_reset_message = "비밀번호 재설정 중 오류가 발생했습니다. 다시 시도해 주세요.";  // 오류 메시지 설정
            }
        } else {
            $password_reset_message = "비밀번호 찾기 중 유효하지 않은 토큰입니다. 다시 시도해 주세요.";  // 오류 메시지 설정
        }
    } else {
        $password_reset_message = "비밀번호가 일치하지 않습니다. 다시 확인해 주세요.";  // 비밀번호 불일치 메시지 설정
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/reset_password.css" />
    <title>비밀번호 재설정</title>
</head>

<body>
    <?php if ($token_error): ?>
        <div class="message-container">
            <p><?php echo $password_reset_message; ?></p>
        </div>
        <a href="adminLogin.php" class="confirm-button">확인</a>
    <?php elseif ($password_reset): ?>

        <div class="pw-logo">
            <a href ="adminLogin.php">
            <img src="img/cap-jm.png"></a>
        </div>

        <div class="pw-container">
            <h2 class="pw-title">새 비밀번호 설정</h2>
            <form method="post" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

                <div class="pw-form">
                    <label for="new_password">새 비밀번호</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                
                <div class="pw-form">
                    <label for="confirm_password">새 비밀번호 재입력</label>
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
            window.history.back();  // 이전 페이지로 돌아가는 함수
        }
    </script>

</body>

</html>
