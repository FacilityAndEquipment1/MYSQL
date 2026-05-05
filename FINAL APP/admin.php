<?php
session_start();
include 'db.php';


if (isset($_GET['id']) && isset($_GET['tbl'])) {
    $id = $_GET['id']; $st = $_GET['st']; $tbl = $_GET['tbl'];
    $table_name = ($tbl == 'fac') ? 'reserve_facilities' : 'reserve_equipment';
    mysqli_query($conn, "UPDATE $table_name SET status='$st' WHERE id=$id");
    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="admin-wrapper">
    <h2 style="color: white; margin-bottom: 20px;">📅 Reservation Management</h2>

    <div class="table-container">
        <div class="table-header">🏢 FACILITY RESERVATIONS</div>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Facility Name</th>
                    <th>Schedule</th> <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM reserve_facilities ORDER BY reserve_date ASC");
                while($row = mysqli_fetch_assoc($res)) {
                  
                    $formattedDate = date('M d, Y - h:i A', strtotime($row['reserve_date']));
                    echo "<tr>
                        <td>{$row['user_name']}</td>
                        <td>{$row['facility_name']}</td>
                        <td><strong>$formattedDate</strong></td>
                        <td><span class='status {$row['status']}'>{$row['status']}</span></td>
                        <td>
                            <a href='?id={$row['id']}&st=Approved&tbl=fac' class='btn-approve'>Approve</a>
                            <a href='?id={$row['id']}&st=Rejected&tbl=fac' class='btn-reject'>Reject</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <div class="table-header">🛠️ EQUIPMENT RESERVATIONS</div>
        <table>
            <thead>
                <tr>
                    <th>User</th>
                    <th>Equipment Name</th>
                    <th>Borrow Date (Kanus-a)</th> <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM reserve_equipment ORDER BY borrow_date ASC");
                while($row = mysqli_fetch_assoc($res)) {
                    $formattedDate = date('M d, Y - h:i A', strtotime($row['borrow_date']));
                    echo "<tr>
                        <td>{$row['user_name']}</td>
                        <td>{$row['equipment_name']}</td>
                        <td><strong>$formattedDate</strong></td>
                        <td><span class='status {$row['status']}'>{$row['status']}</span></td>
                        <td>
                            <a href='?id={$row['id']}&st=Approved&tbl=eq' class='btn-approve'>Approve</a>
                            <a href='?id={$row['id']}&st=Rejected&tbl=eq' class='btn-reject'>Reject</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <a href="index.php?logout=1" class="logout-btn">Logout Admin</a>
</div>

</body>
</html>