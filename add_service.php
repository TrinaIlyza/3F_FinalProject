<?php
include 'setup.php'; // Use the correct database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form input
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $price = $_POST['price'];

    // Validate inputs
    if (!empty($service_name) && !empty($description) && !empty($duration) && !empty($price)) {
        // Prepare the SQL query to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO services (service_name, description, duration, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $service_name, $description, $duration, $price);

        // Execute the query
        if ($stmt->execute()) {
            echo "New service added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Please fill in all fields.";
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.8">
    <title>Add New Service</title>
</head>
<body>
    <h2>Add a New Service</h2>

    <!-- Form for adding new service -->
    <form method="POST" action="add_service.php">
        <label for="service_name">Service Name:</label>
        <input type="text" id="service_name" name="service_name" required><br><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="duration">Duration (in minutes):</label>
        <input type="number" id="duration" name="duration" required><br><br>

        <label for="price">Price:</label>
        <input type="number" step="0.01" id="price" name="price" required><br><br>

        <input type="submit" value="Add Service">
    </form>

    <a href="index.php">Back to Services</a>
</body>
</html>
