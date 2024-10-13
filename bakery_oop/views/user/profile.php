<?php
require_once '../../classes/User.php';
include '../../classes/Order.php'; 

session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User();
$order = new Order(); 
$userId = $_SESSION['user_id'];
$userDetails = $user->getUserDetails($userId);
$orders = $order->getOrdersForUser($userId); // This is where the query is executed
$loyaltyPoints = $user->getLoyaltyPoints($userId); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    if ($user->updateProfile($userId, $name, $email, $password)) {
        echo "Profile updated successfully!";
        $userDetails = $user->getUserDetails($userId); 
    } else {
        echo "Error: There was a problem updating your profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery - Profile</title>
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

<body class="bg-green-50 text-gray-800 font-sans">

    <!-- Header Section -->
    <header class="bg-primary text-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="logo flex items-center">
                <img src="https://img.icons8.com/doodle/48/000000/bread.png" alt="BakeEase Logo" class="w-10 h-10 mr-2">
                <h1 class="text-2xl font-bold">BakeEase Bakery</h1>
            </div>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="index.php" class="hover:text-accent transition-colors">Home</a></li>
                    <li><a href="products.php" class="hover:text-accent transition-colors">Products</a></li>
                    <li><a href="contact.php" class="hover:text-accent transition-colors">Contact</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile.php" class="hover:text-accent transition-colors">Profile</a></li>
                        <li><a href="../../actions/actions.logout.php" class="hover:text-accent transition-colors">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="hover:text-accent transition-colors">Login</a></li> 
                        <li><a href="register.php" class="hover:text-accent transition-colors">Register</a></li>
                    <?php endif; ?>
                    <li>
                        <a href="cart.php" class="relative hover:text-accent transition-colors">
                            Cart (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Profile Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="profile bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-3xl font-bold text-center mb-6">User Profile</h2>

            <?php if ($userDetails): ?>
                <!-- Profile Details -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Profile Details</h3>
                    <p><strong>Name:</strong> <?= $userDetails['name'] ?></p>
                    <p><strong>Email:</strong> <?= $userDetails['email'] ?></p>
                </div>

                <!-- Update Profile Form -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold mb-4">Update Your Profile</h3>
                    <form method="post" action="" class="space-y-4">
                        <div>
                            <label for="name" class="block text-lg font-semibold">Name:</label>
                            <input type="text" id="name" name="name" value="<?= $userDetails['name'] ?>" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="email" class="block text-lg font-semibold">Email:</label>
                            <input type="email" id="email" name="email" value="<?= $userDetails['email'] ?>" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label for="password" class="block text-lg font-semibold">New Password (optional):</label>
                            <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>
                        <button type="submit" name="update_profile" class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary">Update Profile</button>
                    </form>
                </div>

                <!-- Order History Section -->
                <div class="profile-section mb-8">
                    <h3 class="text-xl font-semibold mb-4">Order History</h3>
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead class="bg-gray-50 text-gray-700 font-bold">
                            <tr>
                                <th class="py-3 px-6 text-left">Order ID</th>
                                <th class="py-3 px-6 text-left">Product Names</th>
                                <th class="py-3 px-6 text-left">Total Quantity</th>
                                <th class="py-3 px-6 text-left">Total Price</th>
                                <th class="py-3 px-6 text-left">Payment Method</th>
                                <th class="py-3 px-6 text-left">Address</th>
                                <th class="py-3 px-6 text-left">Status</th>
                                <th class="py-3 px-6 text-left">Order Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php if (!empty($orders)): ?>
                                <?php foreach ($orders as $order): ?>
                                 
                                    <tr>
                                        <td class="py-3 px-6"><?= $order['id'] ?></td>
                                        <td class="py-3 px-6"><?= $order['product_names'] ?></td>
                                        <td class="py-3 px-6"><?= $order['total_quantity'] ?></td>
                                        <td class="py-3 px-6">$<?= $order['total_price'] ?></td>
                                        <td class="py-3 px-6"><?= $order['payment_method'] ?></td>
                                        <td class="py-3 px-6"><?= $order['address'] ?></td> 
                                        <td class="py-3 px-6"><?= $order['status'] ?></td>
                                        <td class="py-3 px-6"><?= $order['order_date'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="py-3 px-6 text-center text-gray-600">No orders found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Loyalty Points Section -->
                <div class="profile-section">
                    <h3 class="text-xl font-semibold mb-4">Loyalty Points</h3>
                    <p class="text-lg">Your current loyalty points: <span class="font-bold"><?= $loyaltyPoints ?></span></p> 
                </div>

            <?php else: ?>
                <p class="text-center text-red-600">User details not found.</p>
            <?php endif; ?>

        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">About Us</h3>
                    <p>BakeEase Bakery is your go-to place for delicious, freshly baked goods. We take pride in our quality ingredients and passionate bakers.</p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-accent">FAQ</a></li>
                        <li><a href="#" class="hover:text-accent">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-accent">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Connect With Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="hover:text-accent"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg></a>
                        <a href="#" class="hover:text-accent"><svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg></a>
                    </div>
                </div>
            </div>
            <div class="mt-8 text-center">
                <p>© 2023 BakeEase Bakery. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>