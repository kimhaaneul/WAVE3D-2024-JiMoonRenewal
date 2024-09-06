<?php
session_start(); // 세션을 시작합니다.

// 모든 세션 변수를 제거합니다.
session_unset();

// 세션을 완전히 종료합니다.
session_destroy();

// capfingers/adminLogin.php로 리디렉션합니다.
header("Location: /capfingers/adminLogin.php");
exit();
?>
