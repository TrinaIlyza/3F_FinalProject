<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id']; 

// Fetch user data
$query = $conn->prepare("SELECT full_name, email, phone_number FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: login.php");
    exit();
}

$username = $user['full_name'];
$email = $user['email'];
$phone_number = $user['phone_number'];

$upcomingAppointments = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? AND appointment_date >= CURDATE() ORDER BY appointment_date ASC");
$upcomingAppointments->bind_param("i", $user_id);
$upcomingAppointments->execute();
$upcomingAppointmentsResult = $upcomingAppointments->get_result();

$pastAppointments = $conn->prepare("SELECT * FROM appointments WHERE user_id = ? AND appointment_date < CURDATE() ORDER BY appointment_date DESC");
$pastAppointments->bind_param("i", $user_id);
$pastAppointments->execute();
$pastAppointmentsResult = $pastAppointments->get_result();

if (isset($_POST['submit_review'])) {
    $appointment_id = $_POST['appointment_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $reviewQuery = $conn->prepare("INSERT INTO reviews (appointment_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $reviewQuery->bind_param("iiis", $appointment_id, $user_id, $rating, $comment);

    if ($reviewQuery->execute()) {
        echo "Review successfully submitted!";
    } else {
        echo "Error: " . $reviewQuery->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="userDashboard.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Hello, <?php echo htmlspecialchars($username); ?>!</h1>
            <button onclick="window.location.href='index.php';">Logout</button>
        </header>

        <nav class="navigation">
            <ul>
                <li><a href="#appointments-upcoming">Upcoming Appointments</a></li>
                <li><a href="#appointments-past">Past Appointments</a></li>
                <li><a href="#settings">Account Settings</a></li>
                <li><a href="#promos">Promotions & Rewards</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <section id="appointments-upcoming" class="section">
                <h2>Upcoming Appointments</h2>
                <div class="appointments">
                    <?php if ($upcomingAppointmentsResult->num_rows > 0): ?>
                        <?php while ($appointment = $upcomingAppointmentsResult->fetch_assoc()): ?>
                            <div class="appointment-item">
                                <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                                <p><strong>Time:</strong> <?php echo $appointment['start_time']; ?> - <?php echo $appointment['end_time']; ?></p>
                                <p><strong>Therapist:</strong> <?php echo $appointment['therapist_id']; ?></p>
                                <button>Cancel</button>
                                <button>Reschedule</button>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No upcoming appointments.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="appointments-past" class="section">
                <h2>Past Appointments</h2>
                <?php if ($pastAppointmentsResult->num_rows > 0): ?>
                    <?php while ($appointment = $pastAppointmentsResult->fetch_assoc()): ?>
                        <div class="appointment-item">
                            <p><strong>Date:</strong> <?php echo $appointment['appointment_date']; ?></p>
                            <p><strong>Therapist:</strong> <?php echo $appointment['therapist_id']; ?></p>

                            <?php if (empty($appointment['review_id'])): ?>
                                <form action="userDashboard.php" method="POST">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                    <label for="rating_<?php echo $appointment['appointment_id']; ?>">Rating:</label>
                                    <input type="number" id="rating_<?php echo $appointment['appointment_id']; ?>" name="rating" min="1" max="5" required>
                                    <label for="comment_<?php echo $appointment['appointment_id']; ?>">Comment:</label>
                                    <textarea id="comment_<?php echo $appointment['appointment_id']; ?>" name="comment" required></textarea>
                                    <button type="submit" name="submit_review">Submit Review</button>
                                </form>
                            <?php else: ?>
                                <p>Review already submitted.</p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No past appointments.</p>
                <?php endif; ?>
            </section>

            <section id="settings" class="section">
                <h2>Account Settings</h2>
                <div class="settings-item">
                    <h3>Profile</h3>
                    <button onclick="openProfilePopup()">Edit Profile</button>
                </div>

                <div class="settings-item">
                    <h3>Change Password</h3>
                    <button onclick="openPasswordPopup()">Change Password</button>
                </div>

                <div id="profilePopup" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closeProfilePopup()">&times;</span>
                        <h2>Edit Profile</h2>
                        <form action="updateProfile.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                            <label for="full_name">Full Name:</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                            
                            <button type="submit">Save Changes</button>
                        </form>
                    </div>
                </div>

                <div id="passwordPopup" class="popup">
                    <div class="popup-content">
                        <span class="close" onclick="closePasswordPopup()">&times;</span>
                        <h2>Change Password</h2>
                        <form action="updatePassword.php" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />
                            <label for="current_password">Current Password:</label>
                            <input type="password" id="current_password" name="current_password" required>

                            <label for="new_password">New Password:</label>
                            <input type="password" id="new_password" name="new_password" required>

                            <label for="confirm_password">Confirm New Password:</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>

                            <button type="submit">Change Password</button>
                        </form>
                    </div>
                </div>
            </section>

            <section id="promos" class="section">
                <h2>Promotions & Rewards</h2>
                <p>No promotions available at this time. Check back later!</p>
            </section>
        </main>
    </div>

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }

        .navigation {
            background-color: #f0f5f4;
            border-bottom: 1px solid #ddd;
        }

        .navigation ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: space-around;
        }

        .navigation li a {
            text-decoration: none;
            padding: 15px 20px;
            display: block;
            color: #333;
            font-weight: 600;
        }

        .navigation li a:hover {
            background-color: #6aa84f;
            color: white;
        }

        .main-content {
            padding: 20px;
        }

        .section {
            margin-bottom: 40px;
        }

        .section h2 {
            margin-bottom: 20px;
            color: #4CAF50;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .appointment-item {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .appointment-item p {
            margin: 5px 0;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .popup {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .popup-content h2 {
            margin-bottom: 20px;
            color: #4CAF50;
        }

        .popup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
        }

        .popup input,
        .popup textarea,
        .popup button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .popup textarea {
            height: 100px;
        }
    </style>

    <script>
        function openProfilePopup() {
            document.getElementById("profilePopup").style.display = "flex";
        }

        function closeProfilePopup() {
            document.getElementById("profilePopup").style.display = "none";
        }

        function openPasswordPopup() {
            document.getElementById("passwordPopup").style.display = "flex";
        }

        function closePasswordPopup() {
            document.getElementById("passwordPopup").style.display = "none";
        }
    </script>
</body>
</html>