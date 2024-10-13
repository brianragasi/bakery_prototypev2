<?php
include "../classes/AdminOrder.php"; 

session_start(); 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) { 
    // Output simple error message to iframe
    echo "Unauthorized access."; 
    exit(); 
}

$adminOrder = new AdminOrder();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_order_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['new_status']; 

    if ($adminOrder->updateOrderStatus($orderId, $newStatus)) {
        
        // --- BEGIN EMAIL NOTIFICATION --- 

        // 1. Get customer email (make sure column name is correct)
        $orderDetails = $adminOrder->getOrder($orderId);
        $customerEmail = "ragasibrian6@gmail.com"; // Replace with YOUR actual email
        echo "Attempting to send email to: " . $customerEmail . "...";

        // 2. Construct email content
        $subject = "Your BakeEase Bakery Order Status Update";
        $message = "Hello,\n\nYour order (ID: $orderId) status has been updated to: $newStatus.\n\n";
        $message .= "Thank you for your order!\n\nBakeEase Bakery";
        
        // 3. Set email headers
        $headers = "From: ragasibrian6@gmail.com" . "\r\n" . // Replace with your actual email
                   "Reply-To: ragasibrian2@gmail.com" . "\r\n" . 
                   "Content-Type: text/plain; charset=UTF-8";  

        // 4. Send the email (and log errors)
        if (mail($customerEmail, $subject, $message, $headers)) {
            error_log("Email sent successfully to: " . $customerEmail); 
        } else {
            error_log("Email Error: " . error_get_last()['message']); 
        }

        // --- END EMAIL NOTIFICATION ---

        // Redirect parent page with success message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?success=" . urlencode("Order status updated successfully.") . "';</script>";
        exit;
    } else { 
        // Redirect parent page with error message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?error=" . urlencode("Error updating order status.") . "';</script>";
        exit;
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['delete_order'])) {
    $orderIdToDelete = $_GET['delete_order'];

    if ($adminOrder->deleteOrder($orderIdToDelete)) {
        // Redirect parent page with success message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?success=" . urlencode("Order deleted successfully.") . "';</script>";
        exit; 
    } else {
        // Redirect parent page with error message
        echo "<script>parent.window.location.href = '../views/admin/manage_orders.php?error=" . urlencode("Error deleting order.") . "';</script>";
        exit;
    }
} 
?>