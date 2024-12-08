<?php
require 'database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $service_id = $_POST['service'];
    $therapist_id = $_POST['therapist'];
    $user_id = $_SESSION['user_id'];
    $appointment_date = $_POST['date'];
    $start_time = $_POST['time'];

    $query = $conn->prepare("SELECT duration FROM services WHERE service_id = ?");
    $query->bind_param("i", $service_id);
    $query->execute();
    $query->bind_result($duration);
    $query->fetch();
    $query->close();

    if (!$duration) {
        die("Unable to fetch service duration.");
    }

    $end_time = date("H:i", strtotime($start_time) + $duration * 60);

    $insert_stmt = $conn->prepare("INSERT INTO appointments (user_id, therapist_id, service_id, appointment_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->bind_param("iiisss", $user_id, $therapist_id, $service_id, $appointment_date, $start_time, $end_time);

    if ($insert_stmt->execute()) {
        $appointment_id = $insert_stmt->insert_id;

        if (isset($_POST['confirm_booking'])) {
            header("Location: booking.php?appointment_id=$appointment_id");
            exit;
        }
    } else {
        echo "An error occurred: " . $insert_stmt->error;
    }
}

$services_data = $conn->query("SELECT service_id, service_name, description, price FROM services")->fetch_all(MYSQLI_ASSOC);
$therapists_data = $conn->query("SELECT user_id, full_name FROM users WHERE role = 'therapist'")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body class="bg-blue-100 text-gray-700 font-sans">
    <header class="bg-blue-600 p-4 text-white text-center font-bold">
        <h1>Schedule Your Appointment</h1>
    </header>

    <main class="container mx-auto py-8">
        <form id="appointment-form" method="POST" action="booking.php" class="bg-white shadow-md rounded p-6">
            <section class="mb-6">
                <h2 class="text-lg font-bold mb-4">1. Choose Your Service and Therapist</h2>
                <div class="mb-4">
                    <label for="service" class="block mb-2 text-sm">Service:</label>
                    <select id="service" name="service" class="w-full border rounded p-2" required>
                        <option value="">Select a Service</option>
                        <?php foreach ($services_data as $service): ?>
                            <option value="<?= $service['service_id']; ?>" data-price="<?= $service['price']; ?>">
                                <?= $service['service_name']; ?> - $<?= $service['price']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="therapist" class="block mb-2 text-sm">Therapist:</label>
                    <select id="therapist" name="therapist" class="w-full border rounded p-2" required>
                        <option value="">Select a Therapist</option>
                        <?php foreach ($therapists_data as $therapist): ?>
                            <option value="<?= $therapist['user_id']; ?>">
                                <?= $therapist['full_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </section>

            <section class="mb-6">
                <h2 class="text-lg font-bold mb-4">2. Pick a Date and Time</h2>
                <div class="mb-4">
                    <label for="date" class="block mb-2 text-sm">Date:</label>
                    <input type="text" id="date" name="date" class="w-full border rounded p-2 flatpickr" required>
                </div>
                <div>
                    <label for="time" class="block mb-2 text-sm">Time:</label>
                    <select id="time" name="time" class="w-full border rounded p-2" required>
                        <option value="">Select Time</option>
                    </select>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-bold mb-4">3. Confirm Details</h2>
                <div class="mb-4">
                    <p><strong>Service:</strong> <span id="summary-service"></span></p>
                    <p><strong>Therapist:</strong> <span id="summary-therapist"></span></p>
                    <p><strong>Date:</strong> <span id="summary-date"></span></p>
                    <p><strong>Time:</strong> <span id="summary-time"></span></p>
                </div>
                <div>
                    <button type="submit" name="confirm_booking" class="w-full bg-blue-500 text-white p-2 rounded">
                        Confirm Appointment
                    </button>
                </div>
            </section>
        </form>
    </main>

    <script>
        flatpickr("#date", { minDate: "today" });

        document.addEventListener('DOMContentLoaded', () => {
            const timeOptions = ["09:00", "10:00", "11:00", "13:00", "14:00"];
            const timeSelect = document.getElementById("time");
            timeOptions.forEach(time => {
                const opt = document.createElement("option");
                opt.value = time;
                opt.textContent = time;
                timeSelect.appendChild(opt);
            });
        });
    </script>
</body>
</html>
