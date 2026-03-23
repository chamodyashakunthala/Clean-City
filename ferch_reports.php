<?php
include("../citizen-app/db.php");

$sql = "SELECT * FROM issue_reports ORDER BY id DESC";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['location']."</td>";
    echo "<td>".$row['phone']."</td>";
    echo "<td>".$row['issue_type']."</td>";
    echo "<td>".$row['description']."</td>";
    echo "<td>".$row['status']."</td>";
    echo "<td><img src='../citizen-app/uploads/".$row['photo']."' width='80'></td>";

    // ACTION BUTTONS
    echo "<td>
        <a href='update_status.php?id=".$row['id']."' style='color:green;'>Resolve</a> |
        <a href='delete.php?id=".$row['id']."' style='color:red;'>Delete</a>
    </td>";

    echo "</tr>";
}
?>