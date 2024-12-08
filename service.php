<?php
require 'database.php';

$filters = [];
$sort_column = 'service_name'; 
$sort_direction = 'ASC';

if (!empty($_GET['price_min'])) {
    $filters[] = sprintf("price >= %f", (float)$_GET['price_min']);
}
if (!empty($_GET['price_max'])) {
    $filters[] = sprintf("price <= %f", (float)$_GET['price_max']);
}
if (!empty($_GET['duration_min'])) {
    $filters[] = sprintf("duration >= %d", (int)$_GET['duration_min']);
}
if (!empty($_GET['duration_max'])) {
    $filters[] = sprintf("duration <= %d", (int)$_GET['duration_max']);
}

if (!empty($_GET['sort'])) {
    $sort_options = [
        'price_asc' => ['column' => 'price', 'direction' => 'ASC'],
        'price_desc' => ['column' => 'price', 'direction' => 'DESC'],
        'duration_asc' => ['column' => 'duration', 'direction' => 'ASC'],
        'duration_desc' => ['column' => 'duration', 'direction' => 'DESC']
    ];

    if (isset($sort_options[$_GET['sort']])) {
        $sort_column = $sort_options[$_GET['sort']]['column'];
        $sort_direction = $sort_options[$_GET['sort']]['direction'];
    }
}

$sql_query = "SELECT * FROM services";
if (!empty($filters)) {
    $sql_query .= " WHERE " . implode(' AND ', $filters);
}
$sql_query .= sprintf(" ORDER BY %s %s", $sort_column, $sort_direction);

$result_set = $conn->query($sql_query);
$services_list = [];
if ($result_set && $result_set->num_rows > 0) {
    while ($data_row = $result_set->fetch_assoc()) {
        $services_list[] = $data_row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 text-gray-900 font-inter">
    <div class="container mx-auto px-4 py-8">
        <nav class="mb-8">
            <a href="index.php" class="bg-blue-700 text-white py-2 px-4 rounded-lg shadow hover:bg-blue-800 transition">
                &larr; Back
            </a>
        </nav>

        <h1 class="text-4xl font-bold text-center text-blue-700 mb-6">Explore Our Services</h1>

        <div class="flex flex-wrap gap-6">
            <aside class="flex-1 bg-white shadow rounded-lg p-6 border border-blue-200">
                <h2 class="text-lg font-bold mb-4 text-blue-600">Filter Options</h2>
                <form method="GET" action="service.php">
                    <?php
                    $inputs = [
                        'price_min' => 'Minimum Price',
                        'price_max' => 'Maximum Price',
                        'duration_min' => 'Minimum Duration (mins)',
                        'duration_max' => 'Maximum Duration (mins)'
                    ];
                    foreach ($inputs as $name => $label) {
                        $value = htmlspecialchars($_GET[$name] ?? '', ENT_QUOTES);
                        echo <<<HTML
                        <div class="mb-4">
                            <label for="$name" class="block font-medium">$label</label>
                            <input type="number" name="$name" id="$name" class="mt-1 block w-full rounded-md border-gray-300" value="$value">
                        </div>
                        HTML;
                    }
                    ?>
                    <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded-lg w-full">Apply Filters</button>
                </form>
            </aside>

            <main class="flex-3 grid gap-8">
                <div class="mb-4 flex justify-end">
                    <form method="GET" action="service.php" class="flex items-center gap-4">
                        <label for="sort" class="font-medium">Sort By:</label>
                        <select name="sort" id="sort" class="rounded-md border-gray-300">
                            <option value="price_asc" <?php echo ($_GET['sort'] ?? '') === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price_desc" <?php echo ($_GET['sort'] ?? '') === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="duration_asc" <?php echo ($_GET['sort'] ?? '') === 'duration_asc' ? 'selected' : ''; ?>>Duration: Short to Long</option>
                            <option value="duration_desc" <?php echo ($_GET['sort'] ?? '') === 'duration_desc' ? 'selected' : ''; ?>>Duration: Long to Short</option>
                        </select>
                        <button type="submit" class="bg-blue-700 text-white py-2 px-4 rounded-lg">Sort</button>
                    </form>
                </div>

                <?php foreach ($services_list as $service): ?>
                    <div class="bg-white shadow rounded-lg overflow-hidden border border-blue-100">
                        <img src="img/<?php echo $service['service_name']; ?>.jpg" alt="<?php echo htmlspecialchars($service['service_name']); ?>" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h3 class="text-2xl font-bold mb-2 text-blue-600"><?php echo htmlspecialchars($service['service_name']); ?></h3>
                            <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($service['description']); ?></p>
                            <p class="text-gray-800 font-semibold">Price: $<?php echo htmlspecialchars($service['price']); ?></p>
                            <p class="text-gray-600">Duration: <?php echo htmlspecialchars($service['duration']); ?> mins</p>
                            <a href="booking.php?service_id=<?php echo htmlspecialchars($service['service_id']); ?>" class="mt-4 inline-block bg-blue-700 text-white py-2 px-4 rounded-lg">Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </main>
        </div>
    </div>
</body>
</html>
