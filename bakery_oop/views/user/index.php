<?php
include '../../classes/Product.php';

session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// 1. Check for "welcome_shown" session variable 
if (!isset($_SESSION['welcome_shown'])) { 
    // Check if the user is logged in
    if (isset($_SESSION['user_id'])) {
        // Welcome message for logged-in users
        if (isset($_SESSION['new_registration']) && $_SESSION['new_registration'] === true) {
            echo "<script>alert('Welcome, " . htmlspecialchars($_SESSION['user_name']) . "! Thank you for registering.');</script>";
            unset($_SESSION['new_registration']); 
        } else {
            echo "<script>alert('Welcome back, " . htmlspecialchars($_SESSION['user_name']) . "!');</script>";
        }
    }
    // 2. Set the session variable to indicate welcome has been shown
    $_SESSION['welcome_shown'] = true; 
}

$productObj = new Product();
$featuredProducts = $productObj->getFeaturedProducts(); 

// Base URL for images 
$imageBaseUrl = 'http://localhost/bakery_oop/assets/images/'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Get product details, including available quantity
    $product = $productObj->getProduct($productId);

    // Check stock
    if ($product && $product['quantity'] > 0 && $product['quantity'] >= $quantity) {
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        // Product is out of stock or insufficient quantity
        $message = "Error: Not enough of this product in stock.";
        header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BakeEase Bakery</title>
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
    <style>
        /* ... your existing styles ... */
    </style>
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

    <main class="container mx-auto px-4 py-8">
        <section class="hero mb-12"> 
    <div class="bg-secondary py-16 text-center rounded-lg shadow-md">
        <?php if (isset($_SESSION['user_id'])) : ?>
            <?php if (isset($_SESSION['new_registration']) && $_SESSION['new_registration'] === true): ?>
                <h2 class="text-4xl font-bold text-white mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                <p class="text-lg text-white mb-6">Thank you for registering. Start exploring our delicious treats!</p> 
                <?php unset($_SESSION['new_registration']); ?> 
            <?php else: ?>
                <h2 class="text-4xl font-bold text-white mb-4">Welcome Back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                <p class="text-lg text-white mb-6">We're glad to see you again. What will you bake today?</p> 
            <?php endif; ?>
        <?php else: ?>
            <h2 class="text-4xl font-bold text-white mb-4">Welcome to BakeEase Bakery!</h2> 
            <p class="text-lg text-white mb-6">Indulge in the aroma of freshly baked goods and treat yourself to our delectable creations.</p> 
        <?php endif; ?>

        <a href="products.php" class="bg-primary hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 my-6" onclick="updateNavigationStack(this.href); return true;">Explore Our Products</a> 
    </div>
</section>

        <!-- Featured Products -->
        <section class="featured-products">
            <h2 class="text-3xl font-bold text-center mb-8">Our Featured Products</h2>

            <?php if (isset($_GET['message'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error</p>
                    <p><?= htmlspecialchars($_GET['message']) ?></p> 
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"> 
                <?php if (!empty($featuredProducts)): ?>
                    <?php foreach ($featuredProducts as $product): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                            <a href="product_details.php?id=<?= $product['id'] ?>" class="block" onclick="updateNavigationStack(this.href); return true;"> 
                                <img src="<?= $imageBaseUrl . $product['image'] ?>" alt="<?= $product['name'] ?>" class="w-full h-48 object-cover"> 
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2"><?= $product['name'] ?></h3>
                                    <p class="text-gray-600 mb-4"><?= $product['description'] ?></p> 
                                    <div class="flex justify-between items-center mb-4"> 
                                        <p class="text-primary font-bold text-lg">$<?= $product['price'] ?></p> 
                                        <p class="text-sm">
                                            <?php if ($product['quantity'] > 0): ?>
                                                <span class="text-green-600 bg-green-100 px-2 py-1 rounded-full">In Stock (<?= $product['quantity'] ?>)</span>
                                            <?php else: ?>
                                                <span class="text-red-600 bg-red-100 px-2 py-1 rounded-full">Out of Stock</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <?php if ($product['quantity'] > 0): ?>
                                        <form method='post' action='' class="flex items-center"> 
                                            <input type='hidden' name='product_id' value='<?= $product['id'] ?>'> 
                                            <input type='number' name='quantity' value='1' min='1' class="w-16 px-2 py-1 border rounded-l focus:outline-none focus:ring-2 focus:ring-primary"> 
                                            <button type='submit' name='add_to_cart' class='flex-grow bg-primary text-white font-bold py-2 px-4 rounded-r hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary'>
                                                Add to Cart 
                                            </button> 
                                        </form> 
                                    <?php else: ?>
                                        <p class="text-red-600 font-bold text-center">Out of Stock</p>
                                    <?php endif; ?> 
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600 text-xl">No products available at the moment. Check back soon!</p> 
                <?php endif; ?>
            </div> 
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="bg-primary text-white mt-12 py-8">
        <div class="container mx-auto px-4">
            <p class="text-center">© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>
    <script src="assets/js/script.js"></script> 
    <script> 
        function updateNavigationStack(url) {
            navStack.push(url);
            return true; 
        }
    </script> 
</body>
</html>