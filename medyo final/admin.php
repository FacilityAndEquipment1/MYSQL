<?php
session_start();
include 'db.php';

// Proteksyon: Admin lang ang makasulod
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Logic para sa Approve/Reject
if (isset($_GET['id']) && isset($_GET['tbl'])) {
    $id = $_GET['id']; 
    $st = $_GET['st']; 
    $tbl = $_GET['tbl'];
    $table_name = ($tbl == 'fac') ? 'reserve_facilities' : 'reserve_equipment';
    
    $update = mysqli_query($conn, "UPDATE $table_name SET status='$st' WHERE id=$id");
    header("Location: admin.php");
    exit();
}

// Pag-ihap sa Pending Requests
$count_fac = mysqli_query($conn, "SELECT id FROM reserve_facilities WHERE status='Pending'");
$pending_fac = mysqli_num_rows($count_fac);

$count_eq = mysqli_query($conn, "SELECT id FROM reserve_equipment WHERE status='Pending'");
$pending_eq = mysqli_num_rows($count_eq);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - ICT Help Desk</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg: #0f172a;
        }

        body {
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
            font-family: 'Poppins', sans-serif;
            color: #f1f5f9;
            margin: 0;
        }

        .admin-wrapper {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        /* Modern Header */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--glass);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            margin-bottom: 30px;
        }

        .admin-header h1 { margin: 0; font-size: 28px; letter-spacing: -1px; }

        /* Card Design */
        .stat-card {
            background: var(--glass);
            padding: 25px;
            border-radius: 15px;
            border: 1px solid var(--glass-border);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
            border-color: var(--primary);
        }

        /* Table Design */
        .table-container {
            background: var(--glass);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(5px);
            margin-bottom: 40px;
        }

        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(0,0,0,0.2); padding: 15px; text-align: left; font-size: 13px; color: #94a3b8; }
        td { padding: 18px 15px; border-bottom: 1px solid var(--glass-border); font-size: 14px; }
        tr:hover { background: rgba(255,255,255,0.02); }

        /* Status Badges */
        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .bg-approved { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .bg-returned { background: rgba(148, 163, 184, 0.2); color: #94a3b8; }
        .bg-rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

        /* Glass Buttons */
        .btn-approve, .btn-reject {
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.3s;
            display: inline-block;
        }
        .btn-approve { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); }
        .btn-reject { background: transparent; color: #f1f5f9; border: 1px solid var(--glass-border); margin-left: 5px; }
        .btn-approve:hover { background: #2563eb; transform: scale(1.05); }
        .btn-reject:hover { background: var(--danger); border-color: var(--danger); }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <div class="admin-header">
        <div>
            <h1>🛡️ Administrator <span style="color: var(--primary);">Panel</span></h1>
            <p style="color: #94a3b8; margin-top: 5px;">You have <strong style="color: var(--warning);"><?php echo $pending_fac + $pending_eq; ?></strong> requests waiting for your action.</p>
        </div>
        <a href="index.php?logout=1" class="btn-logout" style="background: var(--danger); padding: 10px 20px; border-radius: 10px; color: white; text-decoration: none; font-weight: 600; font-size: 14px;">LOGOUT</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="stat-card" style="border-top: 4px solid var(--warning);">
            <small style="color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Facilities Pending</small>
            <h2 style="margin: 10px 0 0; font-size: 36px;"><?php echo $pending_fac; ?></h2>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--primary);">
            <small style="color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">Equipment Pending</small>
            <h2 style="margin: 10px 0 0; font-size: 36px;"><?php echo $pending_eq; ?></h2>
        </div>
        <div class="stat-card" style="border-top: 4px solid var(--success);">
            <small style="color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">System Health</small>
            <h2 style="margin: 10px 0 0; font-size: 36px;">100%</h2>
        </div>
    </div>

    <div class="section-title">🏢 Facility Requests</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Borrower</th>
                    <th>Facility</th>
                    <th>Schedule</th>
                    <th>Purpose</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM reserve_facilities WHERE status='Pending' ORDER BY reserve_date ASC");
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                            <td>
                                <div style='font-weight: 600;'>{$row['user_name']}</div>
                                <div style='font-size: 11px; color: #64748b;'>#F-{$row['id']}</div>
                            </td>
                            <td><span style='color: var(--primary); font-weight: 500;'>{$row['facility_name']}</span></td>
                            <td>
                                <div>".date('M d, Y', strtotime($row['reserve_date']))."</div>
                                <div style='font-size: 11px; color: #64748b;'>{$row['time_in']} - {$row['time_out']}</div>
                            </td>
                            <td style='color: #94a3b8;'><i>\"{$row['purpose']}\"</i></td>
                            <td style='text-align: right;'>
                                <a href='?id={$row['id']}&st=Approved&tbl=fac' class='btn-approve'>Approve</a>
                                <a href='?id={$row['id']}&st=Rejected&tbl=fac' class='btn-reject' onclick='return confirm(\"Reject?\")'>Reject</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #64748b;'>🎉 All caught up! No pending facilities.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="section-title">🛠️ Equipment Requests</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Borrower</th>
                    <th>Item</th>
                    <th>Schedule</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $res = mysqli_query($conn, "SELECT * FROM reserve_equipment WHERE status='Pending' ORDER BY borrow_date ASC");
                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                            <td><div style='font-weight: 600;'>{$row['user_name']}</div></td>
                            <td><span style='color: var(--success); font-weight: 500;'>{$row['equipment_name']}</span></td>
                            <td>".date('M d', strtotime($row['borrow_date']))." | {$row['time_in']}</td>
                            <td style='text-align: right;'>
                                <a href='?id={$row['id']}&st=Approved&tbl=eq' class='btn-approve'>Approve</a>
                                <a href='?id={$row['id']}&st=Rejected&tbl=eq' class='btn-reject' onclick='return confirm(\"Reject?\")'>Reject</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center; padding: 30px; color: #64748b;'>✅ No equipment requests pending.</td></tr>";
                }
                ?>
            </tbody>
        </table> 
    </div>

    <div class="section-title" style="margin-top: 50px;">📜 Activity History</div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Item / Facility</th>
                    <th style="text-align: right;">Final Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $history = mysqli_query($conn, "
                    (SELECT user_name, equipment_name as item, borrow_date as dte, status FROM reserve_equipment WHERE status != 'Pending')
                    UNION
                    (SELECT user_name, facility_name as item, reserve_date as dte, status FROM reserve_facilities WHERE status != 'Pending')
                    ORDER BY dte DESC LIMIT 8
                ");

                while($h = mysqli_fetch_assoc($history)) {
                    $status_class = "bg-" . strtolower($h['status']);
                    echo "<tr>
                        <td style='color: #64748b;'>".date('M d', strtotime($h['dte']))."</td>
                        <td>{$h['user_name']}</td>
                        <td>{$h['item']}</td>
                        <td style='text-align: right;'>
                            <span class='badge $status_class'>{$h['status']}</span>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>