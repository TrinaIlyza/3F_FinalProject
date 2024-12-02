<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
<header>
    <h1>Book an Appointment</h1>
</header>
<div class="container">
    <form method="POST" action="../book_service.php">
        <label for="user_id">Your ID:</label>
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
</div>
</body>
</html>
