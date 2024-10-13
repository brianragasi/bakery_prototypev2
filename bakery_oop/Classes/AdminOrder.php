<?php
require_once "Database.php";

class AdminOrder extends Database {
  public function getOrders() {
    $sql = "SELECT o.*, u.name AS customer_name, 
               GROUP_CONCAT(p.name SEPARATOR ', ') AS product_names, 
               SUM(oi.quantity) AS total_quantity, 
               CASE 
                   WHEN o.order_type = 'delivery' THEN o.delivery_address 
                   ELSE o.address 
               END AS order_address 
            FROM orders o
            INNER JOIN users u ON o.user_id = u.id
            INNER JOIN order_items oi ON o.id = oi.order_id
            INNER JOIN products p ON oi.product_id = p.id
            GROUP BY o.id 
            ORDER BY o.id DESC";
    $result = $this->conn->query($sql);
    return ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}
    public function getOrder($orderId) {
        $orderId = mysqli_real_escape_string($this->conn, $orderId);

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
        $orderId = mysqli_real_escape_string($this->conn, $orderId);
        $newStatus = mysqli_real_escape_string($this->conn, $newStatus);

        $sql = "UPDATE orders SET status = '$newStatus' WHERE id = '$orderId'";
        return $this->conn->query($sql);
    }

    public function deleteOrder($orderId) {
      $orderId = mysqli_real_escape_string($this->conn, $orderId);

      // Start a transaction
      $this->conn->begin_transaction();

      try {
          // 1. Delete related order items
          $deleteItemsSql = "DELETE FROM order_items WHERE order_id = '$orderId'";
          if (!$this->conn->query($deleteItemsSql)) {
              throw new Exception("Error deleting order items: " . $this->conn->error);
          }

          // 2. Delete the order (no changes needed here for customer_email)
          $deleteOrderSql = "DELETE FROM orders WHERE id = '$orderId'"; 
          if (!$this->conn->query($deleteOrderSql)) {
              throw new Exception("Error deleting order: " . $this->conn->error);
          }

          // Commit the transaction (both operations successful)
          $this->conn->commit();
          return true;

      } catch (Exception $e) {
          // Rollback the transaction if any query failed
          $this->conn->rollback();
          error_log("Order deletion failed: " . $e->getMessage()); 
          return false;
      }
  }
}

?>