<?php
include '../../classes/AdminUser.php';
include '../../classes/Product.php';
include '../../classes/Order.php';

session_start();

// --- STRICT ADMIN LOGIN CHECK ---
if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: ../login.php?error=" . urlencode("You are not authorized to access the admin panel.")); // Redirect with error message
    exit();
}
// --- END ADMIN LOGIN CHECK ---

$adminUser = new AdminUser(); 
$product = new Product(); 
$order = new Order();

// Fetch data for the dashboard
$totalUsers = count($adminUser->displayUsers());  
$totalProducts = count($product->getProducts()); 
$totalOrders = count($order->getOrders()); 

// Handle sales report form submission (with input validation)
if (isset($_GET['time_period'])) {
    $timePeriod = htmlspecialchars($_GET['time_period'], ENT_QUOTES, 'UTF-8'); // Sanitize input 

    // Validate time period
    if ($timePeriod !== 'weekly' && $timePeriod !== 'monthly') {
        $timePeriod = 'weekly'; // Default to weekly if invalid input 
    }

    // SQL query to fetch sales data (including status filter)
    if ($timePeriod == 'weekly') {
        $sql = "SELECT p.name, 
                   SUM(oi.quantity) AS total_quantity_sold, 
                   SUM(oi.quantity * p.price) AS total_sales_value
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND o.status = 'delivered' 
                GROUP BY p.id
                ORDER BY total_sales_value DESC;";
    } elseif ($timePeriod == 'monthly') {
        $sql = "SELECT p.name, 
                   SUM(oi.quantity) AS total_quantity_sold, 
                   SUM(oi.quantity * p.price) AS total_sales_value
                FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                JOIN products p ON oi.product_id = p.id
                WHERE o.order_date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) AND o.status = 'delivered' 
                GROUP BY p.id
                ORDER BY total_sales_value DESC;"; 
    }

    $result = $order->executeQuery($sql); // Execute the query
    $salesData = $result->fetch_all(MYSQLI_ASSOC); // Fetch the results
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4CAF50',
                        secondary: '#8BC34A',
                        accent: '#FFC107',
                    },
                    fontFamily: {
                        'sans': ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <!-- Header Section -->
    <header class="bg-primary text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/doodle/48/000000/bread.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="../../actions/logout_admin.php" class="hover:text-accent transition-colors">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Dashboard Section -->
    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-center mb-8">Admin Dashboard</h2>

        <!-- Dashboard Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12"> 
            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Users</h3>
                <p class="text-2xl font-bold mb-4">Total Users: <?= $totalUsers; ?></p>
                <a href="manage_users.php" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Manage Users</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Products</h3>
                <p class="text-2xl font-bold mb-4">Total Products: <?= $totalProducts; ?></p>
                <a href="manage_products.php" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Manage Products</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md text-center">
                <h3 class="text-xl font-semibold mb-4">Orders</h3>
                <p class="text-2xl font-bold mb-4">Total Orders: <?= $totalOrders; ?></p>
                <a href="manage_orders.php" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors">Manage Orders</a>
            </div>
        </div>

        <!-- Sales Report Section -->
        <section class="sales-report bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Sales Report</h2>

            <!-- Filter Options -->
            <form method="get" action="" class="mb-6"> 
                <label for="time_period" class="block text-gray-700 font-bold">Select Time Period:</label>
                <select name="time_period" id="time_period" class="border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
                <button type="submit" class="bg-primary text-white font-bold py-2 px-4 rounded ml-2 hover:bg-green-600 transition-colors">View Report</button>
            </form>

            <!-- Sales Report Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity Sold</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (isset($salesData) && !empty($salesData)): ?> 
                            <?php foreach ($salesData as $row): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $row['name'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $row['total_quantity_sold'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">$<?= number_format($row['total_sales_value'], 2) ?></td> 
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-center">No sales data found for the selected period.</td> 
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section> 
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>