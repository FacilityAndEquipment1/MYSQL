<?php
session_start();
date_default_timezone_set('Asia/Manila'); 
include 'db.php';

if (!isset($_SESSION['username'])) { 
    header("Location: index.php"); 
    exit(); 
}

$u = $_SESSION['username'];


if (isset($_GET['delete_fac'])) {
    $id = $_GET['delete_fac'];
    mysqli_query($conn, "DELETE FROM reserve_facilities WHERE id=$id AND user_name='$u'");
    header("Location: user.php");
}

if (isset($_GET['delete_eq'])) {
    $id = $_GET['delete_eq'];
    mysqli_query($conn, "DELETE FROM reserve_equipment WHERE id=$id AND user_name='$u'");
    header("Location: user.php");
}


if (isset($_POST['submitRes'])) {
    $purpose = mysqli_real_escape_string($conn, $_POST['event_title']);
    $type = $_POST['res_type'];
    $item = $_POST['item_name'];
    $date = $_POST['event_date'];
    $t_in = $_POST['time_in'];
    $t_out = $_POST['time_out'];

    if ($type == "Facility") {
        $sql = "INSERT INTO reserve_facilities 
                (user_name, facility_name, reserve_date, purpose, time_in, time_out, status) 
                VALUES ('$u', '$item', '$date', '$purpose', '$t_in', '$t_out', 'Pending')";
    } else {
        $sql = "INSERT INTO reserve_equipment 
                (user_name, equipment_name, borrow_date, purpose, time_in, time_out, status) 
                VALUES ('$u', '$item', '$date', '$purpose', '$t_in', '$t_out', 'Pending')";
    }

    if (mysqli_query($conn, $sql)) { 
        echo "<script>alert('Reservation Sent! Manager can now track this location.'); window.location='user.php';</script>"; 
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - ICT Help Desk</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-group { margin-bottom: 15px; }
        .row { display: flex; gap: 10px; }
        .row input { flex: 1; }
        .loc-search { border: 2px solid #3b82f6 !important; background: #f0f7ff; font-weight: bold; }
    </style>
</head>
<body onload="updateItems()">

<div class="top-nav">
    <div class="logo">ICT Help Desk</div>
    <a href="index.php?logout=1" class="btn-logout">LOGOUT</a>
</div>

<div class="dashboard-content">
    <div class="card">
        <h3>📍 Reserve & Share Location</h3>
        <form method="POST">
            <div class="form-group">
                <label style="font-size: 12px; color: #3b82f6;">Search Location (Para ma-track sa Mapa):</label>
                <input type="text" name="event_title" list="campussites" class="loc-search" placeholder="e.g. Gym, ICT Lab, Admin..." required>
                <datalist id="campussites">
                    <option value="ICT Lab 1">
                    <option value="ICT Lab 2">
                    <option value="Gymnasium">
                    <option value="Admin Building">
                    <option value="Library">
                    <option value="AVR Room">
                    <option value="Canteen">
                </datalist>
            </div>

            <select name="res_type" id="type" onchange="updateItems()" required>
                <option value="">Select TYPE</option>
                <option value="Facility">Facility</option>
                <option value="Equipment">Equipment</option>
            </select>

            <select name="item_name" id="item" required>
                <option value="">Select First</option>
            </select>

            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="event_date" required>
            </div>

            <div class="row">
                <div class="form-group">
                    <label>Time In:</label>
                    <input type="time" name="time_in" required>
                </div>
                <div class="form-group">
                    <label>Time Out:</label>
                    <input type="time" name="time_out" required>
                </div>
            </div>

            <button type="submit" name="submitRes" class="btn-submit">Submit Reservation</button>
        </form>
    </div>

    <div class="table-container">
        <h3>📋 Your Reservations</h3>
        <h4>🛠️ Equipment Tracking Status</h4>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Destination (Tracked)</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res_eq = mysqli_query($conn, "SELECT * FROM reserve_equipment WHERE user_name='$u' ORDER BY id DESC");
                while($row = mysqli_fetch_assoc($res_eq)) {
                    $statusColor = ($row['status'] == 'Approved') ? '#22c55e' : (($row['status'] == 'Rejected') ? '#ef4444' : '#f59e0b');
                    echo "<tr>
                        <td><b>{$row['equipment_name']}</b></td>
                        <td><mark>📍 {$row['purpose']}</mark></td>
                        <td>".date('M d, Y', strtotime($row['borrow_date']))."</td>
                        <td style='color:$statusColor;font-weight:bold'>{$row['status']}</td>
                        <td><a href='user.php?delete_eq={$row['id']}' class='btn-delete' onclick='return confirm(\"Delete?\")'>🗑️</a></td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateItems(){
    var type = document.getElementById('type').value;
    var item = document.getElementById('item');
    item.innerHTML = "";
    var list = (type == "Facility") ? ["ICT Lab 1", "ICT Lab 2", "AVR"] : ["Projector", "Laptop", "Speaker", "Microphone"];
    list.forEach(opt => {
        var el = document.createElement("option");
        el.value = opt; el.textContent = opt; item.appendChild(el);
    });
}
</script>
</body>
</html>