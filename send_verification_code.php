<?php
if (isset($_POST['email1']) && isset($_POST['str_email02'])) {
    $email1 = $_POST['email1'];
    $email2 = $_POST['str_email02'];
    $full_email = $email1 . "@" . $email2;

    // 인증 코드 생성
    $verification_code = rand(100000, 999999);  // 6자리 인증 코드 생성
    session_start();  // 세션 시작
    $_SESSION['verification_code'] = $verification_code;  // 세션에 인증 코드 저장

    // 이메일 제목과 내용
    $subject = "이메일 인증 코드입니다.";
    $message = "인증 코드는 " . $verification_code . "입니다.";

    // 이메일 전송 (mail 함수 사용 예시)
    $headers = 'From: your-email@example.com' . "\r\n" .
               'Reply-To: your-email@example.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    if (mail($full_email, $subject, $message, $headers)) {
        echo json_encode(['success' => true, 'message' => '이메일 인증번호를 전송하였습니다.']);
    } else {
        echo json_encode(['success' => false, 'message' => '이메일 전송에 실패했습니다.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '이메일 정보가 없습니다.']);
}