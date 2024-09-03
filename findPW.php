<?php
include 'db.php';  // 데이터베이스 연결 파일 포함
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';  // PHPMailer autoload 파일 포함

$password_reset = false;  // 비밀번호 재설정 상태를 나타내는 변수 초기화
$password_reset_message = "";  // 사용자에게 표시할 메시지 변수 초기화

// 이메일 전송 함수 정의
function sendEmail($to, $subject, $body)
{
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.naver.com';  // 네이버 SMTP 서버
        $mail->SMTPAuth = true;
        $mail->Username = 'cap_edu@naver.com'; // 네이버 이메일 계정
        $mail->Password = 'wave3dcom*';        // 네이버 이메일 비밀번호
        $mail->SMTPSecure = 'ssl';  // SSL 보안 연결 사용
        $mail->Port = 465;  // SMTP 포트

        $mail->setFrom('cap_edu@naver.com', 'CAP Education');  // 발신자 정보 설정
        $mail->addAddress($to);  // 수신자 이메일 주소 설정

        $mail->isHTML(true);  // HTML 형식의 이메일 설정
        $mail->CharSet = 'UTF-8';  // 이메일 문자 인코딩 설정
        $mail->Subject = $subject;  // 이메일 제목 설정
        $mail->Body    = $body;  // 이메일 본문 설정

        $mail->send();  // 이메일 전송
    } catch (Exception $e) {
        echo "이메일 전송 실패. Error: {$mail->ErrorInfo}";  // 오류 메시지 출력
    }
}

// 사용자가 POST 요청을 보냈을 경우
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];  // 입력된 아이디
    $e_mail = $_POST['e_mail'];  // 입력된 이메일

    // 입력된 정보로 사용자가 존재하는지 확인
    $query = "SELECT id FROM consultant WHERE id = :id AND e_mail = :e_mail"; // SQL 쿼리 수정
    $stmt = $con->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':e_mail', $e_mail);
    $stmt->execute();

    $result = $stmt->fetch();  // 쿼리 실행 결과를 가져옴

    if ($result) {
        // 비밀번호 재설정 토큰 생성
        $token = bin2hex(random_bytes(50));  // 고유한 랜덤 토큰 생성
        $reset_link = "http://aitms.co.kr/capfingers/reset_password.php?token=" . $token;  // 재설정 링크 생성

        // 토큰을 데이터베이스에 저장
        $query = "UPDATE consultant SET reset_token = :token WHERE id = :id";
        $stmt = $con->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':id', $result['id']);
        $stmt->execute();

        // 이메일로 비밀번호 재설정 링크 전송
        $subject = "비밀번호 재설정 요청";
        $body = "비밀번호 재설정을 원하시면 아래 링크를 클릭하세요: <a href='$reset_link'>$reset_link</a>";

        sendEmail($e_mail, $subject, $body);  // 이메일 전송 함수 호출

        $password_reset = true;  // 비밀번호 재설정 상태를 true로 설정
        $password_reset_message = "비밀번호 재설정 링크가 이메일로 전송되었습니다.";  // 성공 메시지 설정
    } else {
        $password_reset_message = "입력하신 정보와 일치하는 사용자를 찾을 수 없습니다. 다시 확인해 주세요.";  // 실패 메시지 설정
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="/css/findpw.css" />
    <title>비밀번호 찾기</title>
</head>

<body>
    <?php if ($password_reset || $_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <div class="message-container">
            <p><?php echo $password_reset_message; ?></p>
        </div>

        <a href="adminLogin.php" class="confirm-button">확인</a>
    <?php else: ?>

        <div class="pw-logo">
            <a href="/adminLogin.php">
                <img src="/img/cap-jm.png">
            </a>
        </div>
        <h2 class="pw-title">비밀번호 찾기</h2>

        <div class="pw-container">
            <form method="post" action="">
                <div class="pw-form">
                    <label for="id">가입한 아이디</label>
                    <input type="text" id="id" name="id" required>
                </div>

                <div class="pw-form">
                    <label for="e_mail">가입한 이메일</label>
                    <input type="email" id="e_mail" name="e_mail" required>
                </div>

                <div class="button-container">
                    <input type="button" value="취소" onclick="goBack()" class="confirm-button">
                    <input type="submit" value="비밀번호 찾기" class="confirm-button">
                </div>
            </form>
            <div class="id-recovery">
                <a href="findID.php">아이디를 잃어버리셨나요?</a>
            </div>
        </div>

    <?php endif; ?>

    <script>
        function goBack() {
            window.history.back(); // 이전 페이지로 돌아가는 함수
        }
    </script>

</body>

</html>