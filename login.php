<?php
require 'database.php';

$services = [];
$sql = "SELECT * FROM users"; 
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row; 
    }
}

$reviews = [];
$sql = "SELECT r.rating, r.comment, u.full_name AS user, a.service_id, s.service_name 
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        JOIN appointments a ON r.appointment_id = a.appointment_id
        JOIN services s ON a.service_id = s.service_id
        ORDER BY r.created_at DESC LIMIT 6";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row; 
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness Spa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #e4f1f1;
            margin: 0;
            padding: 0;
        }

        .hero-section {
            background-color: #1c6e26; 
            color: white;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
        }

        .hero-content p {
            font-size: 1.125rem;
            font-weight: 300;
        }

        .service-container {
            background-color: #ffffff;
            padding: 4rem 0;
        }

        .service-card {
            background-color: #fafafa;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
        }

        .service-card:hover {
            transform: scale(1.05);
        }

        .service-card h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2b6c2f;
        }

        .service-card p {
            font-size: 1rem;
            color: #555;
        }

        .cta-container {
            background-color: #32a852;
            padding: 3rem 0;
            text-align: center;
        }

        .cta-container h2 {
            font-size: 2.5rem;
            font-weight: 600;
        }

        .cta-container a {
            background-color: white;
            color: #32a852;
            padding: 15px 25px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s;
        }

        .cta-container a:hover {
            background-color: #e4f1f1;
        }

        .review-card {
            background-color: #f9fafb;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin: 1rem 0;
        }

        .review-card h3 {
            font-size: 1.15rem;
            color: #2b6c2f;
            font-weight: bold;
        }

        .review-card p {
            color: #6b7280;
            font-size: 1rem;
        }

        .review-card .rating {
            color: #f59e0b;
            font-weight: bold;
        }

        .login-button {
            position: absolute;
            top: 15px;
            right: 30px;
            background-color: #32a852;
            padding: 12px 25px;
            border-radius: 6px;
            color: white;
            font-weight: 700;
            text-decoration: none;
        }

        .login-button:hover {
            background-color: #1c6e26;
        }
    </style>
</head>
<body>

    <a href="login.php" class="login-button">Login</a>

    <section class="hero-section h-screen bg-cover bg-center" style="background-image: url('img/bg.jpg');">
        <div class="hero-content max-w-4xl mx-auto text-center py-36">
            <h1>Your Wellness Starts Here</h1>
            <p class="mt-4">Relax, rejuvenate, and refresh with our top-rated spa services.</p>
            <div class="mt-6 flex justify-center gap-8">
                <a href="booking.php" class="bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-full font-medium">Book Now</a>
                <a href="service.php" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-6 rounded-full font-medium">See Services</a>
            </div>
        </div>
    </section>

    <section class="service-container">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">Our Premium Services</h2>
            <div class="flex flex-wrap gap-10 justify-center">
                <?php foreach ($services as $service): ?>
                    <div class="service-card max-w-sm">
                        <img src="img/<?php echo $service['service_name']; ?>.jpg" 
                             alt="<?php echo $service['service_name']; ?>" 
                             class="w-full h-56 object-cover">
                        <div class="p-6">
                            <h3><?php echo $service['service_name']; ?></h3>
                            <p class="mb-4"><?php echo $service['description']; ?></p>
                            <p class="font-bold mb-4">Price: $<?php echo $service['price']; ?></p>
                            <a href="booking.php?service_id=<?php echo $service['service_id']; ?>" 
                               class="bg-green-600 text-white py-2 px-5 rounded-full hover:bg-green-700">
                               Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="review-container py-16 bg-gray-100">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-4xl font-bold text-center text-gray-800 mb-8">Customer Testimonials</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <h3><?php echo htmlspecialchars($review['user']); ?></h3>
                        <p class="text-gray-500">Service: <?php echo htmlspecialchars($review['service_name']); ?></p>
                        <p class="rating">Rating: <?php echo $review['rating']; ?>/5</p>
                        <p>"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cta-container">
        <h2 class="text-3xl font-semibold text-white mb-4">Start Your Journey with Us</h2>
        <p class="text-lg text-white mb-6">Sign up now or book your first session!</p>
        <a href="signup.php" class="bg-white text-green-600 py-3 px-6 rounded-full">Get Started</a>
    </section>

</body>
</html>
