<?php
include "../classes/Cart.php";

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_to_cart'])) { // Check for "add to cart" action
        $productId = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Validate quantity (ensure it's numeric and greater than 0)
        if (!is_numeric($quantity) || $quantity < 1) {
            $quantity = 1;
        }

        // Add item to cart (or update quantity if already in cart)
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        header("Location: ../views/user/cart.php"); // Redirect to cart
        exit; 

    } elseif (isset($_POST['update_quantity'])) {
        $productId = $_POST['product_id'];
        $newQuantity = $_POST['quantity'];

        // Validate quantity
        if (!is_numeric($newQuantity) || $newQuantity < 1) {
            $newQuantity = 1; 
        }

        $_SESSION['cart'][$productId] = $newQuantity;
        header("Location: ../views/user/cart.php");
        exit;

    } elseif (isset($_POST['remove_item'])) {
        $productId = $_POST['product_id'];
        unset($_SESSION['cart'][$productId]);
        header("Location: ../views/user/cart.php");
        exit;
    }
}
?>