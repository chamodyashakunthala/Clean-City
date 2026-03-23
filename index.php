<?php
include("../citizen-app/db.php"); // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Dark Mode</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">

<style>
    /* ---------- RESET ---------- */
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'DM Sans', sans-serif; }
    body { background: #121212; color: #eee; padding: 20px; }

    /* ---------- HEADER ---------- */
    h2 { text-align: center; font-family: 'Syne', sans-serif; margin-bottom: 30px; color: #fff; }

    /* ---------- STATS CARDS ---------- */
    .stats { display: flex; gap: 20px; justify-content: center; margin-bottom: 30px; flex-wrap: wrap; }
    .card { background: #1f1f1f; padding: 20px; border-radius: 12px; min-width: 150px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.5); }
    .card h3 { font-family: 'Syne', sans-serif; font-weight: 700; margin-bottom: 10px; color: #ffb400; }
    .card p { font-size: 1.8rem; font-weight: 700; color: #00ff94; }

    /* ---------- TABLE ---------- */
    table { width: 100%; border-collapse: collapse; background: #1f1f1f; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.5); }
    th, td { padding: 12px 15px; text-align: center; }
    th { background: #292929; color: #fff; font-weight: 700; }
    tr:nth-child(even) { background: #222; }
    tr:hover { background: #333; }

    .status { padding: 5px 10px; border-radius: 12px; font-weight: 600; color: #fff; font-size: 0.85rem; }
    .pending { background: #f59e0b; }
    .in-progress { background: #3b82f6; }
    .resolved { background: #10b981; }

    /* ---------- BUTTONS ---------- */
    .btn { padding: 6px 12px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: 0.2s; }
    .btn-delete { background: #ef4444; color: #fff; }
    .btn-delete:hover { background: #cc1f1f; }
    .btn-resolve { background: #10b981; color: #fff; }
    .btn-resolve:hover { background: #0f9c6b; }

    /* ---------- IMAGE ---------- */
    .img-thumb { max-width: 60px; border-radius: 8px; }

    /* ---------- RESPONSIVE ---------- */
    @media (max-width: 768px) {
        .stats { flex-direction: column; align-items: center; }
        table { font-size: 0.85rem; }
        .img-thumb { max-width: 40px; }
    }
</style>
</head>
<body>

<h2>📊 CleanCity Admin Dashboard</h2>

<div class="stats">
    <?php
    $total = $conn->query("SELECT COUNT(*) as total FROM issue_reports")->fetch_assoc()['total'];
    $resolved = $conn->query("SELECT COUNT(*) as total FROM issue_reports WHERE status='Resolved'")->fetch_assoc()['total'];
    $pending = $conn->query("SELECT COUNT(*) as total FROM issue_reports WHERE status='Pending'")->fetch_assoc()['total'];
    ?>
    <div class="card">
        <h3>Total Reports</h3>
        <p><?php echo $total; ?></p>
    </div>
    <div class="card">
        <h3>Resolved</h3>
        <p><?php echo $resolved; ?></p>
    </div>
    <div class="card">
        <h3>Pending</h3>
        <p><?php echo $pending; ?></p>
    </div>
</div>

<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Location</th>
    <th>Phone</th>
    <th>Issue</th>
    <th>Description</th>
    <th>Photo</th>
    <th>Status</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php
$reports = $conn->query("SELECT * FROM issue_reports ORDER BY id DESC");
while($row = $reports->fetch_assoc()) {
    echo "<tr>";
    echo "<td>".$row['id']."</td>";
    echo "<td>".$row['name']."</td>";
    echo "<td>".$row['location']."</td>";
    echo "<td>".$row['phone']."</td>";
    echo "<td>".$row['issue_type']."</td>";
    echo "<td>".substr($row['description'],0,50)."...</td>";
    echo "<td><img src='../citizen-app/uploads/".$row['photo']."' class='img-thumb'></td>";
    echo "<td><span class='status ".strtolower(str_replace(' ','-',$row['status']))."'>".$row['status']."</span></td>";
    echo "<td>
        <form style='display:inline;' method='POST'>
            <input type='hidden' name='resolve_id' value='".$row['id']."'>
            <button type='submit' name='resolve' class='btn btn-resolve'>Resolve</button>
        </form>
        <form style='display:inline;' method='POST'>
            <input type='hidden' name='delete_id' value='".$row['id']."'>
            <button type='submit' name='delete' class='btn btn-delete'>Delete</button>
        </form>
    </td>";
    echo "</tr>";
}
?>
</tbody>
</table>

<?php
if(isset($_POST['resolve'])) {
    $id = $_POST['resolve_id'];
    $conn->query("UPDATE issue_reports SET status='Resolved' WHERE id=$id");
    echo "<script>window.location='index.php';</script>";
}

if(isset($_POST['delete'])) {
    $id = $_POST['delete_id'];
    $conn->query("DELETE FROM issue_reports WHERE id=$id");
    echo "<script>window.location='index.php';</script>";
}
?>

</body>
</html>