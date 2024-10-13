<?php
include '../../classes/Product.php';

session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// *** SESSION CHECK ***
if (!isset($_SESSION['user_id'])) { 
    header("Location: ../login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI'])); 
    exit;
} 
// *** END SESSION CHECK ***

$productObj = new Product();
$products = $productObj->getProducts();

// Base URL for images (Centralized)
$imageBaseUrl = 'http://localhost/bakery_oop/assets/images/'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {

    // Check if the user is logged in before adding to cart
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php?redirect_to=" . urlencode($_SERVER['REQUEST_URI'])); 
        exit();
    }

    $productId = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Get product details (including quantity)
    $product = $productObj->getProduct($productId);

    // Check if quantity is greater than 0 AND sufficient quantity is available
    if ($product && $product['quantity'] > 0 && $product['quantity'] >= $quantity) {
        // Product is in stock and sufficient quantity is available
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
    <title>BakeEase Bakery - Products</title>
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

    <!-- Product Gallery Section -->
    <main class="container mx-auto px-4 py-8">
        <section class="product-gallery">
            <div class="flex justify-between items-center mb-8">
               
                <h2 class="text-3xl font-bold text-center">Our Delicious Products</h2>
            </div>

            <?php if (isset($_GET['message'])): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Error</p>
                    <p><?= htmlspecialchars($_GET['message']) ?></p>
                </div>
            <?php endif; ?> 

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php
                if (!empty($products)) {
                    foreach ($products as $product) {
                        ?>
                        <div class='bg-white rounded-lg shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300'>
                            <a href="product_details.php?id=<?= $product['id'] ?>" class="block">
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

                                    <!-- Add to Cart Section (Conditional) -->
                                    <?php if ($product['quantity'] > 0): ?>
                                        <?php if (isset($_SESSION['user_id'])): ?> 
                                            <form method='post' action='' class="flex items-center">
                                                <input type='hidden' name='product_id' value='<?= $product['id'] ?>'>
                                                <input type='number' name='quantity' value='1' min='1' class="w-16 px-2 py-1 border rounded-l focus:outline-none focus:ring-2 focus:ring-primary">
                                                <button type='submit' name='add_to_cart' class='flex-grow bg-primary text-white font-bold py-2 px-4 rounded-r hover:bg-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-primary'>
                                                    Add to Cart
                                                </button>
                                            </form>
                                        <?php else: ?> 
                                            <a href="../login.php?redirect_to=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="bg-primary text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition-colors inline-block text-center w-full">
                                                Login to Add to Cart
                                            </a> 
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-red-600 font-bold text-center">Out of Stock</p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center text-gray-600 text-xl'>No products available at the moment. Check back soon!</p>"; 
                }
                ?>
            </div>
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
