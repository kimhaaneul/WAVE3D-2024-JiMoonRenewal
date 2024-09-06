<?php
session_start();
include_once("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// 세션에서 사용자 정보를 가져옴
$name = $_SESSION['name'] ?? 'Unknown';
$id = $_SESSION['id'] ?? '0';
$code = $_SESSION['code'] ?? 'Unknown';

$location = $_POST['location'] ?? 'Unknown';
$date = $_POST['date'] ?? '0000-00-00';
$time = $_POST['time'] ?? '00:00';
$country = $_POST['country'] ?? 'Unknown';
$mc = $_POST['city'] ?? 'Unknown';
$cc = $_POST['district'] ?? 'Unknown';
$people_num = $_POST['num_people'] ?? 0;
$con_num = $_POST['additional_consultants'] ?? 0;



try {
    $query = $con->prepare("INSERT INTO reservation_support (consultant_id, consultant_name, location, date, time, country, MC, CC, people_num, con_num, consultant_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bindParam(1, $id);
    $query->bindParam(2, $name);
    $query->bindParam(3, $location);
    $query->bindParam(4, $date);
    $query->bindParam(5, $time);
    $query->bindParam(6, $country);
    $query->bindParam(7, $mc);
    $query->bindParam(8, $cc);
    $query->bindParam(9, $people_num, PDO::PARAM_INT);
    $query->bindParam(10, $con_num, PDO::PARAM_INT);
    $query->bindParam(11, $code);

    if ($query->execute()) {
        $response = ["status" => "success", "message" => "예약폼신청이 성공적으로 완료되었습니다."];
        
        $emailQuery = $con->prepare("SELECT e_mail FROM consultant WHERE consultant_code = ?");
        $emailQuery->bindParam(1, $code);
        $emailQuery->execute();
        $consultant = $emailQuery->fetch(PDO::FETCH_ASSOC);
        $toEmail = $consultant['e_mail'];

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
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = '예약폼 신청 확인';
            $mail->Body = "<p>예약폼 신청이 성공적으로 완료되었습니다.</p>
                           <p>장소: $location</p>
                           <p>날짜: $date</p>
                           <p>시간: $time</p>
                           <p>권리지역: $country, $mc, $cc</p>
                           <p>예상 예약 인원: $people_num</p>
                           <p>추가 컨설턴트 인원: $con_num</p>";

            $mail->send();
        } catch (Exception $e) {
            $response["mail_error"] = "메일 전송 실패: {$mail->ErrorInfo}";
        }
    } else {
        $response = ["status" => "error", "message" => "예약 실패: " . $query->errorInfo()[2]];
    }
} catch (Exception $e) {
    $response = ["status" => "error", "message" => "오류 발생: " . $e->getMessage()];
}

echo json_encode($response);
?>
