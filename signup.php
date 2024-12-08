<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($conn->real_escape_string($_POST['full_name']));
    $email = htmlspecialchars($conn->real_escape_string($_POST['email']));
    $phone_number = htmlspecialchars($conn->real_escape_string($_POST['phone_number']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($conn->real_escape_string($_POST['role']));

    $email_check_query = "SELECT 1 FROM users WHERE email = '$email'";
    $email_exists = $conn->query($email_check_query);

    if ($email_exists->num_rows > 0) {
        $error_message = "This email is already registered. Please use a different one.";
    } else {
        $add_user_query = "INSERT INTO users (full_name, email, phone_number, password, role) 
                           VALUES ('$full_name', '$email', '$phone_number', '$password', '$role')";

        if ($conn->query($add_user_query)) {
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Registration failed: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f3f4f6, #e0f7fa);
            font-family: 'Open Sans', sans-serif;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-submit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-submit:hover {
            background-color: #45a049;
        }
        .error-message {
            color: #d9534f;
        }
    </style>
</head>
<body>
    <div class="flex items-center justify-center min-h-screen">
        <div class="form-container w-full max-w-lg">
            <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Sign Up</h2>

            <?php if (isset($error_message)): ?>
                <p class="error-message text-center mb-4"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="space-y-4">
                <div>
                    <label for="full_name" class="block text-sm font-semibold text-gray-600">Full Name</label>
                    <input type="text" id="full_name" name="full_name" 
                           class="block w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" 
                           placeholder="Your full name" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-600">Email</label>
                    <input type="email" id="email" name="email" 
                           class="block w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" 
                           placeholder="Your email address" required>
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-semibold text-gray-600">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" 
                           class="block w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" 
                           placeholder="Your phone number" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-600">Password</label>
                    <input type="password" id="password" name="password" 
                           class="block w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" 
                           placeholder="Choose a strong password" required>
                </div>

                <div>
                    <label for="role" class="block text-sm font-semibold text-gray-600">Role</label>
                    <select id="role" name="role" 
                            class="block w-full mt-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-400" 
                            required>
                        <option value="customer">Customer</option>
                        <option value="therapist">Therapist</option>
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn-submit w-full px-4 py-2 rounded-lg font-semibold focus:outline-none">
                        Register
                    </button>
                </div>
            </form>

            <p class="text-center text-sm mt-4">
                Already registered? 
                <a href="login.php" class="text-green-500 hover:underline">Log in here</a>.
            </p>
        </div>
    </div>
</body>
</html>