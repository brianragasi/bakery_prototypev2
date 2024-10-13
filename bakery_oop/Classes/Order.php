<?php
require_once "Database.php";
require_once "User.php"; 

class Order extends Database {

  public function createOrder($userId, $customerEmail, $cartItems, $totalPrice, $paymentMethod, $address, $orderType, $pickupTime = null) {

    if (!isset($_SESSION['user_id'])) {
        return "Error: You must be logged in to place an order.";
    }

    $userId = $this->conn->real_escape_string($userId);
    $customerEmail = $this->conn->real_escape_string($customerEmail);
    $totalPrice = $this->conn->real_escape_string($totalPrice);
    $paymentMethod = $this->conn->real_escape_string($paymentMethod);
    $orderType = $this->conn->real_escape_string($orderType);
    $pickupTime = $this->conn->real_escape_string($pickupTime);

    // ** Consistent Address Handling **
    $deliveryAddress = ($orderType === 'delivery') ? $this->conn->real_escape_string($address) : null;
    $address = ($orderType === 'pickup') ? $this->conn->real_escape_string($address) : null; 

    // 1. CHECK STOCK AVAILABILITY
    foreach ($cartItems as $item) {
        $productId = $this->conn->real_escape_string($item['product_id']);
        $quantity = $this->conn->real_escape_string($item['quantity']);

        $sql = "SELECT quantity FROM products WHERE id = '$productId'";
        $result = $this->conn->query($sql);
        $product = $result->fetch_assoc();

        if (!$product || $product['quantity'] < $quantity) {
            return "Error: Not enough of product ID $productId in stock.";
        }
    }

    // 2. PROCEED WITH ORDER CREATION
    $sql = "INSERT INTO orders (user_id, customer_email, total_price, payment_method, address, order_type, pickup_time, delivery_address) 
            VALUES ('$userId', '$customerEmail', '$totalPrice', '$paymentMethod', '$address', '$orderType', '$pickupTime', '$deliveryAddress')";

    // Log the SQL query for debugging
    error_log("SQL Query: " . $sql); 

    if ($this->conn->query($sql) === TRUE) {
        $orderId = $this->conn->insert_id;

        foreach ($cartItems as $item) {
            $productId = $this->conn->real_escape_string($item['product_id']);
            $quantity = $this->conn->real_escape_string($item['quantity']);

            $sql = "INSERT INTO order_items (order_id, product_id, quantity) VALUES ('$orderId', '$productId', '$quantity')";
            if (!$this->conn->query($sql)) {
                error_log("Error inserting order item: " . $this->conn->error);
                return false; 
            }

            // 3. DECREASE PRODUCT QUANTITIES 
            $sql = "UPDATE products SET quantity = quantity - '$quantity' WHERE id = '$productId'";
            $this->conn->query($sql);
        }

        // Award loyalty points after successful order creation
        $pointsToAward = floor($totalPrice); // 1 point per $1 spent (adjust as needed)
        $user = new User();
        $user->addLoyaltyPoints($userId, $pointsToAward);

        unset($_SESSION['cart']);
        return $orderId; 

    } else {
        // Log the database error for debugging
        error_log("Database Error: " . $this->conn->error);
        return false; 
    }
}

    public function executeQuery($sql) {
        return $this->conn->query($sql);
    }

    public function getOrders() {
        $sql = "SELECT o.*, u.name AS customer_name, p.name AS product_name
                FROM orders o
                INNER JOIN users u ON o.user_id = u.id
                INNER JOIN order_items oi ON o.id = oi.order_id
                INNER JOIN products p ON oi.product_id = p.id
                ORDER BY o.id DESC";
        $result = $this->conn->query($sql);
        return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getOrdersForUser($userId) {
      $userId = $this->conn->real_escape_string($userId);
  
      $sql = "SELECT o.id, o.total_price, o.payment_method, 
                     CASE 
                         WHEN o.order_type = 'delivery' THEN o.delivery_address
                         ELSE o.address 
                     END AS address, 
                     o.status, o.order_date,
                     GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names,
                     SUM(oi.quantity) AS total_quantity
              FROM orders o
              JOIN order_items oi ON o.id = oi.order_id
              JOIN products p ON oi.product_id = p.id
              WHERE o.user_id = '$userId'
              GROUP BY o.id
              ORDER BY o.order_date DESC"; 
  
      $result = $this->conn->query($sql);
      return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
  }

    public function getOrder($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);
        $sql = "SELECT o.*, u.name AS customer_name, p.name AS product_name, o.customer_email
        FROM orders o
        INNER JOIN users u ON o.user_id = u.id
        INNER JOIN order_items oi ON o.id = oi.order_id
        INNER JOIN products p ON oi.product_id = p.id
        WHERE o.id = '$orderId'";
        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function updateOrderStatus($orderId, $newStatus) {
        $orderId = $this->conn->real_escape_string($orderId);
        $newStatus = $this->conn->real_escape_string($newStatus);

        $sql = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }

    public function deleteOrder($orderId) {
        $orderId = $this->conn->real_escape_string($orderId);
        $sql = "DELETE FROM orders WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }
}
?>