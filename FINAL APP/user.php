<?php
session_start();
// 1. TIMEZONE SETTING (Importante para sa saktong oras sa Pinas)
date_default_timezone_set('Asia/Manila'); 

include 'db.php';

if (!isset($_SESSION['username'])) { 
    header("Location: index.php"); 
    exit(); 
}

$u = $_SESSION['username'];

// 2. DELETE LOGIC (Kini ang mofunction inig click sa Delete)
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

// 3. SUBMIT RESERVATION LOGIC
if (isset($_POST['submitRes'])) {
    $t = mysqli_real_escape_string($conn, $_POST['event_title']);
    $type = $_POST['res_type'];
    $item = $_POST['item_name'];
    $date = $_POST['event_date'];

    if ($type == "Facility") {
        $sql = "INSERT INTO reserve_facilities (user_name, facility_name, reserve_date, status) 
                VALUES ('$u', '$item', '$date', 'Pending')";
    } else {
        $sql = "INSERT INTO reserve_equipment (user_name, equipment_name, borrow_date, status) 
                VALUES ('$u', '$item', '$date', 'Pending')";
    }
    
    if (mysqli_query($conn, $sql)) { 
        echo "<script>alert('Sent!'); window.location='user.php';</script>"; 
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>User Dashboard</title>
</head>
<body>
    
    <div class="top-nav">
        <div class="logo">ICT Help Desk</div>
        <a href="index.php?logout=1" class="btn-logout">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
            </svg>
            LOGOUT
        </a>
    </div>

    <div class="dashboard-content">
        <div class="card">
            <h3> Select Reservation</h3>
            <form method="POST">
                <input type="text" name="event_title" placeholder="Purpose/Event" required>
                <select name="res_type" id="type" onchange="updateItems()" required>
                    <option value="">Select TYPE</option>
                    <option value="Facility">Facility</option>
                    <option value="Equipment">Equipment</option>
                </select>
                <select name="item_name" id="item" required>
                    <option value="">Select First</option>
                </select>
                <input type="datetime-local" name="event_date" required>
                <button type="submit" name="submitRes" class="btn-submit">Submit</button>
            </form>
        </div>

        <div class="table-container">
            <h3>📋 Your Reservation</h3>
            
            <h4>🏢 Facilities Status</h4>
            <table>
                <thead>
                    <tr>
                        <th>Facility</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res_fac = mysqli_query($conn, "SELECT * FROM reserve_facilities WHERE user_name='$u' ORDER BY created_at DESC");
                    while($row = mysqli_fetch_assoc($res_fac)) {
                        $statusColor = ($row['status'] == 'Approved') ? '#22c55e' : (($row['status'] == 'Rejected') ? '#ef4444' : '#f59e0b');
                        $formattedDate = date('M d, Y - h:i A', strtotime($row['reserve_date']));
                        echo "<tr>
                            <td>{$row['facility_name']}</td>
                            <td>$formattedDate</td>
                            <td style='color: $statusColor; font-weight: bold;'>{$row['status']}</td>
                            <td>
                                <a href='user.php?delete_fac={$row['id']}' class='btn-delete' onclick='return confirm(\"Are You Sure To delete This Status?\")'>🗑️</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>

            <h4 style="margin-top:20px;">🛠️ Equipment Status</h4>
            <table>
                <thead>
                    <tr>
                        <th>Equipment</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res_eq = mysqli_query($conn, "SELECT * FROM reserve_equipment WHERE user_name='$u' ORDER BY created_at DESC");
                    while($row = mysqli_fetch_assoc($res_eq)) {
                        $statusColor = ($row['status'] == 'Approved') ? '#22c55e' : (($row['status'] == 'Rejected') ? '#ef4444' : '#f59e0b');
                        $formattedDate = date('M d, Y - h:i A', strtotime($row['borrow_date']));
                        echo "<tr>
                            <td>{$row['equipment_name']}</td>
                            <td>$formattedDate</td>
                            <td style='color: $statusColor; font-weight: bold;'>{$row['status']}</td>
                            <td>
                                <a href='user.php?delete_eq={$row['id']}' class='btn-delete' onclick='return confirm(\"Sigurado ka mo-delete?\")'>🗑️</a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function updateItems() {
        var type = document.getElementById('type').value;
        var item = document.getElementById('item');
        item.innerHTML = "";
        var list = (type == "Facility") ? ["ICT Lab 1", "ICT Lab 2", "AVR"] : ["Projector", "Laptop", "Speaker"];
        list.forEach(opt => { 
            var el = document.createElement("option"); 
            el.value = opt; 
            el.textContent = opt; 
            item.appendChild(el); 
        });
    }
    </script>
</body>
</html>