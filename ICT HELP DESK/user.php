<?php
session_start();
include 'db.php';
if (!isset($_SESSION['username'])) { header("Location: index.php"); }

if (isset($_POST['submitRes'])) {
    $u = $_SESSION['username'];
    $t = mysqli_real_escape_string($conn, $_POST['event_title']);
    $type = $_POST['res_type'];
    $item = $_POST['item_name'];
    $date = $_POST['event_date'];

    $sql = "INSERT INTO reservations (user_name, event_title, res_type, item_name, event_date, status) 
            VALUES ('$u', '$t', '$type', '$item', '$date', 'Pending')";
    
    if (mysqli_query($conn, $sql)) { echo "<script>alert('Sent!'); window.location='user.php';</script>"; }
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="style.css">
<body>
    <div class="card">
        <h3>➕ Select Reservation</h3>
        <form method="POST">
            <input type="text" name="event_title" placeholder="Purpose/Event" required>
            <select name="res_type" id="type" onchange="updateItems()" required>
                <option value="">-- Pilia ang Type --</option>
                <option value="Facility">Facility</option>
                <option value="Equipment">Equipment</option>
            </select>
            <select name="item_name" id="item" required><option value="">Pilia una ang Type</option></select>
            <input type="datetime-local" name="event_date" required>
            <button type="submit" name="submitRes" class="btn-submit">I-submit</button>
        </form>
        <a href="index.php">Logout</a>
    </div>

    <script>
    function updateItems() {
        var type = document.getElementById('type').value;
        var item = document.getElementById('item');
        item.innerHTML = "";
        var list = (type == "Facility") ? ["ICT Lab 1", "ICT Lab 2", "AVR"] : ["Projector", "Laptop", "Speaker"];
        list.forEach(opt => { var el = document.createElement("option"); el.value = opt; el.textContent = opt; item.appendChild(el); });
    }
    </script>
</body>
</html>