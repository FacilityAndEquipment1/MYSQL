<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    header("Location: index.php");
    exit();
}


if (isset($_GET['return_id'])) {
    $id = $_GET['return_id'];
    mysqli_query($conn, "UPDATE reserve_equipment SET status='Returned' WHERE id=$id");
    header("Location: manager.php?msg=Returned");
    exit();
}


if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: manager.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager - Inventory & Live Tracking</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .nav-links { margin: 20px 0; display: flex; gap: 10px; }
        .nav-links a { 
            padding: 10px 20px; 
            background: #4b5563; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px;
            transition: 0.3s;
        }
        .nav-links a:hover { opacity: 0.8; }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .stat-card h3 { margin: 0; color: #1f2937; font-size: 16px; }
        .stat-card p { font-size: 32px; font-weight: bold; color: #3b82f6; margin: 5px 0 0; }
    
        #map-container {
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        #manager-map { 
            height: 400px; 
            width: 100%; 
            border-radius: 8px; 
            border: 1px solid #ddd;
        }

        .track-out { color: #ef4444; font-weight: bold; }
        mark { background: #fef08a; padding: 2px 5px; border-radius: 3px; }
        .btn-return {
            background: #10b981;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-returned { color: #6b7280; font-style: italic; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <h2 style="color: white; border-bottom: 2px solid rgba(255,255,255,0.2); padding-bottom: 10px;">
        🛡️ NBSC System & Inventory Manager
    </h2>
    
    <div class="nav-links">
        <a href="manager.php" style="background:#3b82f6;">📊 Tracking & Users</a>
        <a href="index.php?logout=1" style="background:#ef4444;">Logout</a>
    </div>

    <div class="stats-container">
        <div class="stat-card">
            <h3>📦 Equipment Out</h3>
            <?php 
            $count_eq = mysqli_query($conn, "SELECT id FROM reserve_equipment WHERE status='Approved'");
            echo "<p>".mysqli_num_rows($count_eq)."</p>";
            ?>
        </div>
        <div class="stat-card">
            <h3>🏢 Facilities Occupied</h3>
            <?php 
            $count_fac = mysqli_query($conn, "SELECT id FROM reserve_facilities WHERE status='Approved'");
            echo "<p>".mysqli_num_rows($count_fac)."</p>";
            ?>
        </div>
        <div class="stat-card">
            <h3>👥 Total Users</h3>
            <?php 
            $count_users = mysqli_query($conn, "SELECT id FROM users");
            echo "<p>".mysqli_num_rows($count_users)."</p>";
            ?>
        </div>
    </div>

    <div id="map-container">
        <h3 style="margin-top:0; color: #1e293b; font-size: 16px;">📍 NBSC Campus Live Equipment Locator</h3>
        <div id="manager-map"></div>
    </div>

    <div class="table-container" style="margin-bottom: 40px;">
        <div class="table-header" style="background: #059669;">🛰️ LIVE EQUIPMENT LOGISTICS</div>
        <table>
            <thead>
                <tr>
                    <th>Item Name</th>
                    <th>User / Borrower</th>
                    <th>Destination/Purpose</th>
                    <th>Schedule</th>
                    <th>Live Status</th>
                    <th>Action</th> </tr>
            </thead>
            <tbody>
                <?php
                $tracking = mysqli_query($conn, "SELECT * FROM reserve_equipment WHERE status IN ('Approved', 'Returned') ORDER BY status ASC, borrow_date DESC");
                if(mysqli_num_rows($tracking) > 0) {
                    while($row = mysqli_fetch_assoc($tracking)) {
                        $date = date('M d', strtotime($row['borrow_date']));
                        $time = date('h:i A', strtotime($row['time_in'])) . " - " . date('h:i A', strtotime($row['time_out']));
                        
                        if($row['status'] == 'Approved') {
                            $status_label = "🟢 DEPLOYED";
                            $class = "track-out";
                        } else {
                            $status_label = "⚪ RETURNED";
                            $class = "status-returned";
                        }
                        
                        echo "<tr>
                            <td><strong>{$row['equipment_name']}</strong></td>
                            <td>{$row['user_name']}</td>
                            <td><mark>{$row['purpose']}</mark></td>
                            <td>$date ($time)</td>
                            <td class='$class'>$status_label</td>
                            <td>";
                                if($row['status'] == 'Approved') {
                                    echo "<a href='manager.php?return_id={$row['id']}' class='btn-return' onclick='return confirm(\"Confirm return?\")'>Mark Returned</a>";
                                } else {
                                    echo "<small>Closed</small>";
                                }
                        echo "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No active equipment logs found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <div class="table-header" style="background: #1e293b;">👥 USER ACCOUNTS MANAGEMENT</div>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = mysqli_query($conn, "SELECT * FROM users ORDER BY role ASC");
                while($u = mysqli_fetch_assoc($users)) {
                    $roleColor = ($u['role'] == 'admin' || $u['role'] == 'manager') ? 'color:#ef4444; font-weight:bold;' : '';
                    echo "<tr>
                        <td>{$u['username']}</td>
                        <td style='$roleColor'>".strtoupper($u['role'])."</td>
                        <td>";
                        if($u['username'] != $_SESSION['username']) {
                            echo "<a href='manager.php?delete_user={$u['id']}' 
                                     style='padding:5px 10px; font-size:12px; background:#ef4444; color:white; text-decoration:none; border-radius:3px;' 
                                     onclick='return confirm(\"Sigurado ka nga papason kini nga user?\")'>Remove</a>";
                        } else {
                            echo "<small style='color:gray;'>Current Session</small>";
                        }
                    echo "</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    
    var map = L.map('manager-map').setView([8.3615, 124.8585], 18);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© NBSC Logistics Tracker'
    }).addTo(map);

    function getPos(purpose) {
        let p = purpose.toLowerCase();
        if (p.includes('admin')) return [8.36162, 124.85865];
        if (p.includes('gym') || p.includes('court')) return [8.36115, 124.85888];
        if (p.includes('library')) return [8.36185, 124.85842];
        if (p.includes('ict') || p.includes('lab')) return [8.36145, 124.85815];
        return [8.3615 + (Math.random() - 0.5) * 0.0005, 124.8585 + (Math.random() - 0.5) * 0.0005];
    }

    <?php
   
    $map_data = mysqli_query($conn, "SELECT user_name, equipment_name, purpose FROM reserve_equipment WHERE status='Approved'");
    while($m = mysqli_fetch_assoc($map_data)) {
        $u = addslashes($m['user_name']);
        $i = addslashes($m['equipment_name']);
        $p = addslashes($m['purpose']);
        echo "
            (function(){
                var pos = getPos('$p');
                L.marker(pos).addTo(map)
                 .bindPopup('<b>$u</b><br>Item: $i<br>Loc: $p');
            })();
        ";
    }
    ?>
</script>

</body>
</html>