<?php
include_once("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendApprovalEmail($toEmail, $subject, $body) {
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
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "이메일 전송 실패. Error: {$mail->ErrorInfo}";
    }
}

function generateEventCode($con, $country, $MC, $CC) {
    $approvalYear = date('y'); // 현재 연도에서 마지막 두 자리 추출
    $baseCode = $country . $MC . $CC . $approvalYear;
    $stmt = $con->prepare("SELECT event_code FROM reservation_support WHERE event_code LIKE :baseCode ORDER BY event_code DESC LIMIT 1");
    $stmt->execute(['baseCode' => $baseCode . '%']);
    $lastCode = $stmt->fetchColumn();

    if ($lastCode) {
        $number = (int)substr($lastCode, strlen($baseCode)) + 1;
    } else {
        $number = 1;
    }

    return $baseCode . str_pad($number, 3, '0', STR_PAD_LEFT);
}

if (isset($_POST['idx']) && isset($_POST['pass'])) {
    $idx = $_POST['idx'];
    $pass = $_POST['pass'];

    try {
        // reservation_support 테이블에서 컨설턴트 코드와 이름을 가져옴
        $stmt = $con->prepare("SELECT consultant_code, consultant_name FROM reservation_support WHERE idx = :idx");
        $stmt->bindParam(':idx', $idx, PDO::PARAM_INT);
        $stmt->execute();
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            $consultantCode = $reservation['consultant_code'];
            $consultantName = $reservation['consultant_name'];

            // consultant 테이블에서 이메일을 가져옴
            $stmt = $con->prepare("SELECT e_mail FROM consultant WHERE consultant_code = :consultant_code");
            $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);
            $stmt->execute();
            $consultant = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($consultant) {
                $toEmail = $consultant['e_mail'];
                $subject = '';
                $body = '';

                if ($pass === 'true') {
                    // 1차 승인 시 이벤트 코드 생성
                    $stmt = $con->prepare("SELECT country, MC, CC FROM reservation_support WHERE idx = :idx");
                    $stmt->bindParam(':idx', $idx, PDO::PARAM_INT);
                    $stmt->execute();
                    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($reservation) {
                        $eventCode = generateEventCode($con, $reservation['country'], $reservation['MC'], $reservation['CC']);

                        // reservation_support 테이블 업데이트
                        $stmt = $con->prepare("UPDATE reservation_support SET pass = :pass, event_code = :event_code WHERE idx = :idx");
                        $stmt->bindParam(':event_code', $eventCode, PDO::PARAM_STR);
                        $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                        $stmt->bindParam(':idx', $idx, PDO::PARAM_INT);

                        if ($stmt->execute()) {
                            // event_list 테이블에 이벤트 코드와 컨설턴트 코드 추가
                            $stmt = $con->prepare("INSERT INTO event_list (event_code, consultant_code) VALUES (:event_code, :consultant_code)");
                            $stmt->bindParam(':event_code', $eventCode, PDO::PARAM_STR);
                            $stmt->bindParam(':consultant_code', $consultantCode, PDO::PARAM_STR);
                            $stmt->execute();

                            $subject = "1차 승인 완료";
                            $body = "안녕하세요, {$consultantName}님. 이벤트 코드 {$eventCode}가 1차 승인이 완료되었습니다.";

                            sendApprovalEmail($toEmail, $subject, $body);

                            echo "성공적으로 업데이트되었습니다.";
                        } else {
                            echo "업데이트에 실패했습니다.";
                        }
                    } else {
                        echo "예약 정보를 찾을 수 없습니다.";
                    }
                } else {
                    // 1차 승인 해제 또는 최종 승인 시
                    $stmt = $con->prepare("UPDATE reservation_support SET pass = :pass WHERE idx = :idx");
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                    $stmt->bindParam(':idx', $idx, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        if ($pass === 'complete') {
                            $subject = "최종 승인 완료";
                            $body = "안녕하세요, {$consultantName}님. 이벤트가 최종 승인되었습니다.";
                        } elseif ($pass === 'true') {
                            $subject = "최종 승인 취소";
                            $body = "안녕하세요, {$consultantName}님. 이벤트가 최종 승인에서 1차 승인 상태로 변경되었습니다.";
                        } else {
                            $subject = "1차 승인 취소";
                            $body = "안녕하세요, {$consultantName}님. 이벤트가 1차 승인 상태에서 취소되었습니다.";
                        }

                        sendApprovalEmail($toEmail, $subject, $body);

                        echo "성공적으로 업데이트되었습니다.";
                    } else {
                        echo "업데이트에 실패했습니다.";
                    }
                }
            } else {
                echo "컨설턴트 이메일을 찾을 수 없습니다.";
            }
        } else {
            echo "예약 정보를 찾을 수 없습니다.";
        }
    } catch (PDOException $e) {
        echo "데이터베이스 오류: " . $e->getMessage();
    }
} else {
    echo "유효하지 않은 요청입니다.";
}

?>
