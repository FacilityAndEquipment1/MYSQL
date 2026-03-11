<?php
session_start();
include 'db.php';
if ($_SESSION['role'] != 'admin') { header("Location: index.php"); }

if (isset($_GET['id'])) {
    $id = $_GET['id']; $st = $_GET['st'];
    mysqli_query($conn, "UPDATE reservations SET status='$st' WHERE id=$id");
    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body>
    <h2>📋 Admin Management</h2>
    <table>
        <tr><th>User</th><th>Item</th><th>Status</th><th>Action</th></tr>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM reservations ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                <td>{$row['user_name']}</td>
                <td>{$row['item_name']}</td>
                <td class='status-{$row['status']}'>{$row['status']}</td>
                <td>
                    <a href='?id={$row['id']}&st=Approved'>Approve</a> | 
                    <a href='?id={$row['id']}&st=Rejected'>Reject</a> |
                    <a href='?id={$row['id']}&st=In-use'>In-use</a>
                </td>
            </tr>";
        }
        ?>
    </table>
    <br><a href="index.php">Logout</a>
</body>
</html>