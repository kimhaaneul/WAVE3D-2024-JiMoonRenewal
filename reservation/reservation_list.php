<?php
// 데이터베이스 연결 설정
$host = 'localhost'; // 또는 서버의 IP 주소
$user = 'root'; // MySQL 사용자 이름
$password = '0000'; // MySQL 비밀번호
$dbname = 'cms'; // 데이터베이스 이름


$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_GET['delete'])) {
    $deleteId = $_GET['delete'];
    $deleteQuery = "DELETE FROM finger_reservation WHERE idx = $deleteId";
    if ($conn->query($deleteQuery) === TRUE) {
        echo "삭제되었습니다.";
    } else {
        echo "Error: " . $conn->error;
    }
}

$query = "SELECT * FROM finger_reservation ORDER BY creation_date ASC";
$result = $conn->query($query);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>예약 목록</title>
</head>

<body>
    <table>
        <tr>
            <th>번호</th>
            <th>예약 날짜</th>
            <th>검사 유형</th>
            <th>예약자</th>
            <th>주 사용 손</th>
            <th>연락처</th>
            <th>삭제</th>
        </tr>
        <?php
        $counter = 1; 
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $counter . "</td>"; 
                echo "<td>" . $row["creation_date"] . "</td>";
                echo "<td>" . (isset($row["finger_type"]) ? $row["finger_type"] : "null") . "</td>";
                echo "<td>" . $row["rsv_name"] . "</td>";
                echo "<td>" . $row["hand_s"] . "</td>";
                echo "<td>" . $row["rsv_phone"] . "</td>";
                echo "<td><a href='reservation_list.php?delete=" . $row["idx"] . "' onclick='return confirm(\"정말 삭제하시겠습니까?\");'>Delete</a></td>";
                echo "</tr>";
                $counter++;
            }
        } else {
            echo "<tr><td colspan='7'>목록 없음</td></tr>";
        }
        ?>
    </table>
</body>

</html>

<?php
$conn->close();
?>