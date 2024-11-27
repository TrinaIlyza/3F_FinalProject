<?php
include 'setup.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form input
    $user_id = $_POST['user_id']; // ID of the customer
    $therapist_id = $_POST['therapist_id']; // ID of the therapist
    $service_id = $_POST['service_id']; // ID of the selected service
    $appointment_date = $_POST['appointment_date']; // Selected date
    $start_time = $_POST['start_time']; // Selected start time

    // Calculate end time (assuming service duration is fetched dynamically)
    $duration_query = $conn->prepare("SELECT duration FROM services WHERE service_id = ?");
    $duration_query->bind_param("i", $service_id);
    $duration_query->execute();
    $duration_query->bind_result($service_duration);
    $duration_query->fetch();
    $duration_query->close();

    $end_time = date("H:i:s", strtotime($start_time . " + {$service_duration} minutes"));

    // Validate inputs
    if (!empty($user_id) && !empty($therapist_id) && !empty($service_id) && !empty($appointment_date) && !empty($start_time)) {
        // Prepare SQL query
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, therapist_id, service_id, appointment_date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiisss", $user_id, $therapist_id, $service_id, $appointment_date, $start_time, $end_time);

        // Execute query and provide feedback
        if ($stmt->execute()) {
            echo "Appointment booked successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Please fill in all required fields.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Service</title>
</head>
<body>
    <h2>Book a Service</h2>
    <form method="POST" action="book_service.php">
        <label for="user_id">Customer ID:</label>
        <input type="number" id="user_id" name="user_id" required><br><br>

        <label for="therapist_id">Therapist ID:</label>
        <input type="number" id="therapist_id" name="therapist_id" required><br><br>

        <label for="service_id">Service ID:</label>
        <input type="number" id="service_id" name="service_id" required><br><br>

        <label for="appointment_date">Date:</label>
        <input type="date" id="appointment_date" name="appointment_date" required><br><br>

        <label for="start_time">Start Time:</label>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <input type="submit" value="Book Appointment">
    </form>
</body>
</html>
