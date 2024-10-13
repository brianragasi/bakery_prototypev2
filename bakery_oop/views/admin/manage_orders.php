<?php
include '../../classes/AdminOrder.php';

session_start(); 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) { 
    header("Location: ../login.php"); 
    exit();
}

$adminOrder = new AdminOrder();
$orders = $adminOrder->getOrders();

// Message handling (with added ID for JavaScript)
if (isset($_GET['success']) || isset($_GET['error'])) { 
    echo "<div id='flash-message' class='fixed inset-0 flex items-center justify-center z-50'>"; 
    if (isset($_GET['success'])) {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative' role='alert'>
                  <span class='block sm:inline'>" . htmlspecialchars($_GET['success']) . "</span>
              </div>";
    } elseif (isset($_GET['error'])) {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative' role='alert'>
                  <span class='block sm:inline'>" . htmlspecialchars($_GET['error']) . "</span>
              </div>";
    }
    echo "</div>"; 
} 
?>

<!DOCTYPE html>
<html>
<head>
    <title>BakeEase Bakery - Manage Orders</title>
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
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1; /* Makes the main content take up the remaining space */
        }

        footer {
            flex-shrink: 0; /* Prevents the footer from shrinking */
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen flex flex-col">
    <header class="bg-primary text-white shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Manage Orders</h1>
            <a href="admin_dashboard.php" class="text-white hover:text-accent">Back to Dashboard</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <section class="manage-orders bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6">Manage Orders</h2>

            <!-- Table Container with Scrolling -->
            <div class="overflow-x-auto overflow-y-auto max-h-[100vh]"> 
                <table class="min-w-full divide-y divide-gray-200 table-auto">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Method</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address/Pickup Time</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Type</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th> 
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $order['id'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $order['customer_name'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $order['product_names'] ?></td> 
                                    <td class="px-6 py-4 whitespace-nowrap">₱<?= $order['total_price'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $order['payment_method'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"> 
                                        <?= $order['order_address'] ?> 
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?= $order['order_type'] ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"> 
                                        <?= $order['total_quantity'] ?> 
                                    </td> 
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- Modified Form with iframe target -->
                                        <form method='post' action='../../actions/admin-order-actions.php' 
                                              target="update-iframe" class="inline-block"> 
                                            <input type='hidden' name='order_id' value='<?= $order['id'] ?>'>
                                            <select name='new_status' 
                                                    class="border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary">
                                                <option value='pending' <?= ($order['status'] == 'pending' ? 'selected' : '') ?>>Pending</option>
                                                <option value='processing' <?= ($order['status'] == 'processing' ? 'selected' : '') ?>>Processing</option>
                                                <option value='shipped' <?= ($order['status'] == 'shipped' ? 'selected' : '') ?>>Shipped</option>
                                                <option value='delivered' <?= ($order['status'] == 'delivered' ? 'selected' : '') ?>>Delivered</option>
                                                <option value='cancelled' <?= ($order['status'] == 'cancelled' ? 'selected' : '') ?>>Cancelled</option>
                                            </select>
                                            <button type='submit' name='update_order_status' 
                                                    class="bg-primary text-white font-bold py-1 px-2 rounded ml-2 hover:bg-green-600 transition-colors">
                                                Update
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href='../../actions/admin-order-actions.php?delete_order=<?= $order['id'] ?>' 
                                           onclick='return confirm("Are you sure you want to delete this order?")' 
                                           class='text-red-600 hover:text-red-900'>
                                            Delete
                                        </a>
                                    </td> 
                                </tr> 
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="px-6 py-4 whitespace-nowrap text-center">No orders found.</td> 
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Hidden Iframe -->
    <iframe name="update-iframe" style="display: none;"></iframe>

    <footer class="bg-primary text-white py-4">
        <div class="container mx-auto px-4 text-center">
            <p>© 2023 BakeEase Bakery. All rights reserved.</p>
        </div>
    </footer>

    <!-- JavaScript to Dismiss Messages  -->
    <script>
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.remove();
            }, 5000); // Automatically removes after 5 seconds
            flashMessage.addEventListener('click', () => {
                flashMessage.remove();
            });
        }
    </script> 
</body>
</html>
