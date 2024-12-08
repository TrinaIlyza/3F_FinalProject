can you modify the code to look the different as well as its design as well as the codes itself so it doesnt look like it was copied, but make it function the same way, and to not replace any name or variables whatsoever. please make it:
 <?php
include('database.php');

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'therapist') {
    header("Location: login.php"); 
    exit();
}
$appointmentsQuery = $conn->prepare("
    SELECT a.appointment_id, u.full_name AS customer_name, 
           t.full_name AS therapist_name, s.service_name, a.status
    FROM appointments a
    JOIN users u ON a.user_id = u.user_id
    JOIN users t ON a.therapist_id = t.user_id
    JOIN services s ON a.service_id = s.service_id
    ORDER BY a.appointment_date DESC
");
$appointmentsQuery->execute();
$appointmentsResult = $appointmentsQuery->get_result();

$servicesQuery = $conn->prepare("SELECT * FROM services");
$servicesQuery->execute();
$servicesResult = $servicesQuery->get_result();

$availabilityQuery = $conn->prepare("
    SELECT av.availability_id, t.full_name AS therapist_name, av.date, av.start_time, av.end_time
    FROM availability av
    JOIN users t ON av.therapist_id = t.user_id
    ORDER BY av.date DESC
");
$availabilityQuery->execute();
$availabilityResult = $availabilityQuery->get_result();
$query = "
    SELECT p.payment_id, 
           u.full_name AS customer_name, 
           s.service_name, 
           t.full_name AS therapist_name, 
           p.amount, 
           p.payment_status, 
           p.payment_date 
    FROM payments p
    JOIN appointments a ON p.appointment_id = a.appointment_id
    JOIN users u ON a.user_id = u.user_id  -- Get customer name
    JOIN services s ON a.service_id = s.service_id  -- Get service name
    JOIN users t ON a.therapist_id = t.user_id  -- Get therapist name
";
$paymentsResult = $conn->query($query);

$paymentsQuery = $conn->prepare("
    SELECT p.payment_id, u.full_name AS customer_name, p.amount, p.payment_status
    FROM payments p
    JOIN appointments a ON p.appointment_id = a.appointment_id
    JOIN users u ON a.user_id = u.user_id
    ORDER BY p.payment_date DESC
");
$paymentsQuery->execute();
$paymentsResult = $paymentsQuery->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_availability'])) {
    $therapist_id = $_POST['therapist_id'];
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    if (empty($therapist_id) || empty($date) || empty($start_time) || empty($end_time)) {
        echo "Please fill in all fields.";
    } else {
        $query = "INSERT INTO availability (therapist_id, date, start_time, end_time) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isss', $therapist_id, $date, $start_time, $end_time);

        if ($stmt->execute()) {
            header("Location: adminDashboard.php");
            exit();
        } else {
            echo "Error adding availability.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $service_name = $_POST['service_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];

    if (empty($service_name) || empty($description) || empty($price) || empty($duration)) {
        echo "Please fill in all fields.";
    } else {
        $query = "INSERT INTO services (service_name, description, price, duration) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssdi', $service_name, $description, $price, $duration);

        if ($stmt->execute()) {
            header("Location: adminDashboard.php");
            exit();
        } else {
            echo "Error adding service.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service_id'])) {
    $service_id = $_POST['edit_service_id'];
    $service_name = $_POST['service_name'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];

    $query = "UPDATE services SET service_name = ?, price = ?, duration = ? WHERE service_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sdii', $service_name, $price, $duration, $service_id);

    if ($stmt->execute()) {
        header("Location: adminDashboard.php");
        exit();
    } else {
        echo "Error updating service.";
    }
}

if (isset($_GET['delete_service_id'])) {
    $service_id = $_GET['delete_service_id'];
    $deleteQuery = "DELETE FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $service_id);
    
    if ($stmt->execute()) {
        header("Location: adminDashboard.php");
        exit();
    } else {
        echo "Error deleting service.";
    }
}

if (isset($_GET['delete_availability_id'])) {
    $availability_id = $_GET['delete_availability_id'];
    $deleteQuery = "DELETE FROM availability WHERE availability_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $availability_id);
    
    if ($stmt->execute()) { 
        header("Location: adminDashboard.php");
        exit();
    } else {
        echo "Error deleting availability.";
    }
}

$query = "SELECT p.payment_id, u.full_name AS customer_name, p.amount, p.payment_status 
          FROM payments p
          JOIN appointments a ON p.appointment_id = a.appointment_id
          JOIN users u ON a.user_id = u.user_id";  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="adminDashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-dashboard-container">
        <header class="admin-dashboard-header">
            <h1>Admin Dashboard</h1>
            <button onclick="window.location.href='index.php';">Logout</button>
        </header>
        <nav class="admin-dashboard-nav">
            <ul>
                <li><a href="#manage-appointments">Manage Appointments</a></li>
                <li><a href="#manage-services">Manage Services</a></li>
                <li><a href="#therapist-availability">Therapist Availability</a></li>
                <li><a href="#payments-reports">Payments & Reports</a></li>
            </ul>
        </nav>
        <main class="admin-dashboard-main">
            <section id="manage-appointments" class="admin-dashboard-section">
                <h2>Manage Appointments</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Customer</th>
                            <th>Therapist</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = $appointmentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $appointment['appointment_id']; ?></td>
                                <td><?php echo $appointment['customer_name']; ?></td>
                                <td><?php echo $appointment['therapist_name']; ?></td>
                                <td><?php echo $appointment['service_name']; ?></td>
                                <td><?php echo ucfirst($appointment['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <section id="manage-services" class="admin-dashboard-section">
                <h2>Manage Services</h2>
                <button onclick="openAddServicePopup()">Add New Service</button>
                <ul class="service-list">
                    <?php while ($service = $servicesResult->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo $service['service_name']; ?></strong> - ₱<?php echo $service['price']; ?> 
                            (<?php echo $service['duration']; ?> mins)

                            <button onclick="editService(<?php echo $service['service_id']; ?>, '<?php echo $service['service_name']; ?>', <?php echo $service['price']; ?>, <?php echo $service['duration']; ?>)">Edit</button>

                            <a href="adminDashboard.php?delete_service_id=<?php echo $service['service_id']; ?>" onclick="return confirm('Are you sure you want to delete this service?')">
                                <button>Delete</button>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>

            <div id="addServiceForm" style="display:none;">
                <h3>Add New Service</h3>
                <form method="POST" action="adminDashboard.php">
                    <label for="service_name">Service Name</label>
                    <input type="text" name="service_name" id="service_name" required>
                    
                    <label for="description">Description</label>
                    <textarea name="description" id="description" required></textarea>
                    
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" required>
                    
                    <label for="duration">Duration (mins)</label>
                    <input type="number" name="duration" id="duration" required>
                    
                    <button type="submit" name="add_service">Add Service</button>
                </form>
                <button onclick="closeAddServiceForm()">Cancel</button>
            </div>
            <div id="editServiceForm" style="display:none;">
                <h3>Edit Service</h3>
                <form method="POST" action="adminDashboard.php">
                    <input type="hidden" name="edit_service_id" id="edit_service_id">
                    
                    <label for="service_name">Service Name</label>
                    <input type="text" name="service_name" id="service_name" required>
                    
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" required>
                    
                    <label for="duration">Duration (mins)</label>
                    <input type="number" name="duration" id="duration" required>
                    
                    <button type="submit">Update Service</button>
                </form>
                <button onclick="closeEditForm()">Cancel</button>
            </div>

            <section id="therapist-availability" class="admin-dashboard-section">
                <h2>Therapist Availability</h2>
                
                <button onclick="openAddAvailabilityForm()">Add Availability</button>
                
                <table>
                    <thead>
                        <tr>
                            <th>Therapist</th>
                            <th>Date</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $availabilityQuery = "SELECT a.availability_id, u.full_name AS therapist_name, a.date, a.start_time, a.end_time 
                                            FROM availability a
                                            JOIN users u ON a.therapist_id = u.user_id
                                            WHERE u.role = 'therapist'";
                        $availabilityResult = $conn->query($availabilityQuery);
                        while ($availability = $availabilityResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $availability['therapist_name']; ?></td>
                                <td><?php echo $availability['date']; ?></td>
                                <td><?php echo $availability['start_time']; ?></td>
                                <td><?php echo $availability['end_time']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <div id="addAvailabilityForm" style="display:none;">
                <h3>Add or Edit Therapist Availability</h3>
                <form method="POST" action="adminDashboard.php">
                    <label for="therapist_id">Select Therapist</label>
                    <select name="therapist_id" id="therapist_id" required>
                        <?php 
                        $therapistsQuery = "SELECT user_id, full_name FROM users WHERE role = 'therapist'";
                        $therapistsResult = $conn->query($therapistsQuery);
                        while ($therapist = $therapistsResult->fetch_assoc()): ?>
                            <option value="<?php echo $therapist['user_id']; ?>"><?php echo $therapist['full_name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    
                    <label for="date">Date</label>
                    <input type="date" name="date" id="date" required>

                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" required>

                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" required>

                    <button type="submit" name="add_availability">Add Availability</button>
                    <button type="button" onclick="closeAddAvailabilityForm()">Cancel</button>
                </form>
            </div>

            <section id="payments-reports" class="admin-dashboard-section">
                <h2>Payments & Reports</h2>

                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Therapist</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $paymentsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $payment['payment_id']; ?></td>
                                <td><?php echo $payment['customer_name']; ?></td>
                                <td><?php echo $payment['service_name']; ?></td>
                                <td><?php echo $payment['therapist_name']; ?></td>
                                <td>₱<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo ucfirst($payment['payment_status']); ?></td>
                                <td><?php echo $payment['payment_date']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>


            <section id="reports" class="admin-dashboard-section mt-10">
                <h2>Reports</h2>
                
                <h3>Earnings</h3>
                <canvas id="earningsChart"></canvas>

                <h3>Bookings</h3>
                <canvas id="bookingsChart"></canvas>

                <h3>Customer Satisfaction</h3>
                <canvas id="satisfactionChart"></canvas>

                <script>
                    var earningsData = <?php
                        $earningsQuery = "SELECT MONTH(p.created_at) AS month, SUM(p.amount) AS total_earnings 
                                        FROM payments p 
                                        WHERE p.payment_status = 'paid' 
                                        GROUP BY MONTH(p.created_at)";
                        $earningsResult = $conn->query($earningsQuery);
                        $earnings = [];
                        $months = [];
                        while ($row = $earningsResult->fetch_assoc()) {
                            $months[] = $row['month'];
                            $earnings[] = $row['total_earnings'];
                        }
                        echo json_encode(['labels' => $months, 'data' => $earnings]);
                    ?>;
                    
                    var earningsChart = new Chart(document.getElementById('earningsChart'), {
                        type: 'bar',
                        data: {
                            labels: earningsData.labels,
                            datasets: [{
                                label: 'Total Earnings',
                                data: earningsData.data,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    var bookingsData = <?php
                        $bookingsQuery = "SELECT MONTH(a.created_at) AS month, COUNT(a.appointment_id) AS total_bookings 
                                        FROM appointments a
                                        GROUP BY MONTH(a.created_at)";
                        $bookingsResult = $conn->query($bookingsQuery);
                        $bookings = [];
                        while ($row = $bookingsResult->fetch_assoc()) {
                            $bookings[] = $row['total_bookings'];
                        }
                        echo json_encode(['labels' => $months, 'data' => $bookings]);
                    ?>;

                    var bookingsChart = new Chart(document.getElementById('bookingsChart'), {
                        type: 'line',
                        data: {
                            labels: bookingsData.labels,
                            datasets: [{
                                label: 'Total Bookings',
                                data: bookingsData.data,
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });

                    var satisfactionData = <?php
                        $satisfactionQuery = "SELECT AVG(r.rating) AS average_satisfaction 
                                            FROM reviews r";
                        $satisfactionResult = $conn->query($satisfactionQuery);
                        $averageSatisfaction = $satisfactionResult->fetch_assoc()['average_satisfaction'];
                        echo json_encode([ 'data' => [$averageSatisfaction]]);
                    ?>;

                    var satisfactionChart = new Chart(document.getElementById('satisfactionChart'), {
                        type: 'doughnut',
                        data: {
                            labels: ['Satisfaction'],
                            datasets: [{
                                label: 'Customer Satisfaction',
                                data: satisfactionData.data,
                                backgroundColor: ['rgba(75, 192, 192, 0.2)'],
                                borderColor: ['rgba(75, 192, 192, 1)'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                        }
                    });
                </script>
            </section>
        </main>
    </div>

    <script>
    function openAddAvailabilityForm() {
        document.getElementById('addAvailabilityForm').style.display = 'block';
                }

    function closeAddAvailabilityForm() {
        document.getElementById('addAvailabilityForm').style.display = 'none';
                }
    function openAddServicePopup() {
        document.getElementById('addServiceForm').style.display = 'block';
    }

    function closeAddServiceForm() {
        document.getElementById('addServiceForm').style.display = 'none';
    }

    function editService(serviceId, serviceName, price, duration) {
        document.getElementById('edit_service_id').value = serviceId;
        document.getElementById('service_name').value = serviceName;
        document.getElementById('price').value = price;
        document.getElementById('duration').value = duration;
        document.getElementById('editServiceForm').style.display = 'block';
    }

    function closeEditForm() {
        document.getElementById('editServiceForm').style.display = 'none';
    }


</script>
</body>
</html>