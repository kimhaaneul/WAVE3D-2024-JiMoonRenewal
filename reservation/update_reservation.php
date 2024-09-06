<?php
session_start();
include_once ("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Unknown';
$id = isset($_SESSION['id']) ? $_SESSION['id'] : '0';
$code = isset($_SESSION['code']) ? $_SESSION['code'] : 'Unknown';

function sendUpdateEmail($toEmail, $subject, $body)
{
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
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "이메일 전송 실패 Mailer Error: {$mail->ErrorInfo}";
    }
}

if (isset($_POST['idx']) && isset($_POST['location']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['people_num']) && isset($_POST['con_num'])) {
    $idx = $_POST['idx'];
    $location = $_POST['location'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $people_num = $_POST['people_num'];
    $con_num = $_POST['con_num'];

    try {
        $stmt = $con->prepare("UPDATE reservation_support SET location = :location, date = :date, time = :time, people_num = :people_num, con_num = :con_num WHERE idx = :idx");
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->bindParam(':people_num', $people_num, PDO::PARAM_INT);
        $stmt->bindParam(':con_num', $con_num, PDO::PARAM_INT);
        $stmt->bindParam(':idx', $idx, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $stmt = $con->prepare("SELECT consultant_code, consultant_name FROM reservation_support WHERE idx = :idx");
            $stmt->bindParam(':idx', $idx, PDO::PARAM_INT);
            $stmt->execute();
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reservation) {
                $consultantCode = $reservation['consultant_code'];
                $consultantName = $reservation['consultant_name'];

                $stmt = $con->prepare("SELECT e_mail FROM consultant WHERE consultant_code = :consultant_code");
                $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);
                $stmt->execute();
                $consultant = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($consultant) {
                    $toEmail = $consultant['e_mail'];
                    $subject = "예약 정보 수정 알림";
                    $body = "안녕하세요, {$consultantName}님. 예약 정보가 다음과 같이 수정되었습니다.<br><br>
                             장소: {$location}<br>
                             날짜: {$date}<br>
                             시간: {$time}<br>
                             예상 인원: {$people_num}<br>
                             컨설턴트 인원: {$con_num}";

                    sendUpdateEmail($toEmail, $subject, $body);
                } else {
                    echo "컨설턴트 이메일을 찾을 수 없습니다.";
                }
            } else {
                echo "예약 정보를 찾을 수 없습니다.";
            }

            echo "예약 정보가 성공적으로 수정되었습니다.";
        } else {
            echo "예약 정보 수정에 실패했습니다.";
        }
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
} else {
    echo "유효하지 않은 요청입니다.";
}
?>