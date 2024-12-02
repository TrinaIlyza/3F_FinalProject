<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Manage Bookings</h1>
</header>
<div class="container">
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Customer</th>
                <th>Therapist</th>
                <th>Service</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Fetch and display bookings from the database -->
            <?php
            include '../setup.php';
            $sql = "SELECT * FROM appointments";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$row['appointment_id']}</td>
                        <td>{$row['user_id']}</td>
                        <td>{$row['therapist_id']}</td>
                        <td>{$row['service_id']}</td>
                        <td>{$row['appointment_date']}</td>
                        <td>{$row['start_time']}</td>
                        <td>{$row['status']}</td>
                        <td><button>Edit</button> <button>Delete</button></td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No bookings found</td></tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
