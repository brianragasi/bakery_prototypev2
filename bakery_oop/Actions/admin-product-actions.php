<?php

include "../classes/AdminProduct.php";

session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['admin']) || !$_SESSION['admin']) {
    header("Location: ../views/login.php");
    exit();
}

$adminProduct = new AdminProduct();
$product = new Product(); 

// Define the relative upload path (relative to document root)
$relativeUploadPath = "/bakery_oop/assets/images/";

// Function to sanitize filenames
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename); 
    return $filename;
}

function handleImageUpload($file, $relativeUploadPath) {
    $targetDirectory = $_SERVER['DOCUMENT_ROOT'] . $relativeUploadPath;
    $targetFile = $targetDirectory . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["error" => "File is not an image."];
    }

    if ($file["size"] > 5000000) {
        return ["error" => "Sorry, your file is too large."];
    }

    $allowedTypes = array("jpg", "jpeg", "png", "gif");
    if(!in_array($imageFileType, $allowedTypes)) {
        return ["error" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."];
    }

    $originalFileName = $targetFile;
    $i = 1;
    while (file_exists($targetFile)) {
        $targetFile = $originalFileName . "_" . $i . "." . $imageFileType;
        $i++;
    }

    if (is_uploaded_file($file["tmp_name"])) {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $imageFilename = sanitizeFilename(basename($targetFile)); // Sanitize filename
            return ["success" => $imageFilename];  // Return only the filename
        } else {
            $uploadError = $file["error"]; 
            return ["error" => "Sorry, there was an error uploading your file. Error code: " . $uploadError];
        }
    } else {
        error_log("Temporary uploaded file not found: " . $file["tmp_name"]);
        return ["error" => "Temporary uploaded file not found."];
    }
}

// Add Product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = $adminProduct->escapeString($_POST['name']);
    $description = $adminProduct->escapeString($_POST['description']);
    $price = (float)$adminProduct->escapeString($_POST['price']);
    $quantity = (int)$adminProduct->escapeString($_POST['quantity']);
    $imagePath = null;  

    if ($_FILES['image']['error'] === 0) { 
        $uploadResult = handleImageUpload($_FILES["image"], $relativeUploadPath);

        if (isset($uploadResult["success"])) {
            $imagePath = $uploadResult["success"]; 
        } else {
            $errorMessage = $uploadResult["error"];
            header("Location: ../views/admin/add_product.php?error=" . urlencode($errorMessage));
            exit; 
        }
    }

    if ($adminProduct->addProduct($name, $description, $price, $quantity, $imagePath)) {
        header("Location: ../views/admin/manage_products.php?success=Product added successfully.");
        exit;
    } else {
        $error = $adminProduct->getError(); 
        header("Location: ../views/admin/add_product.php?error=Error adding product: " . urlencode($error));
        exit;
    }
}

// Update Product 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    // Check and sanitize inputs
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $name = isset($_POST['name']) ? $adminProduct->escapeString($_POST['name']) : null;
    $description = isset($_POST['description']) ? $adminProduct->escapeString($_POST['description']) : null;
    $price = isset($_POST['price']) ? (float)$adminProduct->escapeString($_POST['price']) : null;
    $quantity = isset($_POST['quantity']) ? (int)$adminProduct->escapeString($_POST['quantity']) : null;
    $imagePath = null;

    if ($_FILES['image']['error'] === 0) { 
        $uploadResult = handleImageUpload($_FILES["image"], $relativeUploadPath);

        if (isset($uploadResult["success"])) {
            $imagePath = $uploadResult["success"]; 

            // Delete the old image if one exists for this product
            $oldProduct = $adminProduct->getProduct($productId);
        } else {
            $errorMessage = $uploadResult["error"];
            header("Location: ../views/admin/edit_product.php?id=$productId&error=" . urlencode($errorMessage));
            exit; 
        }
    }

    if ($product->updateProduct($productId, $name, $description, $price, $quantity, $imagePath)) {
        if ($oldProduct && !empty($oldProduct['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $oldProduct['image'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $oldProduct['image']); 
        }
        header("Location: ../views/admin/manage_products.php?success=Product updated successfully.");
        exit;
    } else {
        $error = $product->getError(); 
        header("Location: ../views/admin/edit_product.php?id=$productId&error=Error updating product: " . urlencode($error));
        exit;
    }
}

// Delete Product
if (isset($_POST['delete_product'])) { // Check if request is POST
    $productIdToDelete = (int)$_POST['delete_product'];

    if ($product->deleteProduct($productIdToDelete)) {
        // Redirect parent page (manage_products.php) with success message
        echo "<script>parent.window.location.href = '../views/admin/manage_products.php?success=Product deleted successfully.';</script>"; 
        exit;
    } else {
        $error = $product->getError();
        // Redirect parent page with error message
        echo "<script>parent.window.location.href = '../views/admin/manage_products.php?error=" . urlencode("Error deleting product: " . $error) . "';</script>"; 
        exit;
    }
}

// Set Featured (Toggle)
if (isset($_POST['toggle_featured'])) { // Check for POST request 
    $productId = $adminProduct->escapeString($_POST['toggle_featured']);
    $productData = $adminProduct->getProduct($productId);

    if ($productData) {
        $newFeaturedStatus = $productData['featured'] ? 0 : 1; 
        if ($adminProduct->setFeatured($productId, $newFeaturedStatus)) {
            $message = $newFeaturedStatus ? "Product featured successfully." : "Product unfeatured successfully.";
        } else {
            $message = "Error updating featured status: " . $adminProduct->getError();
        }
    } else {
        $message = "Product not found.";
    }

    // Redirect parent page with message 
    echo "<script>parent.window.location.href = '../views/admin/manage_products.php?message=" . urlencode($message) . "';</script>"; 
    exit;
}



// Handle Quantity Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $newQuantity = $_POST['new_quantity'];

    if ($adminProduct->updateProductQuantity($productId, $newQuantity)) { 
        header("Location: ../views/admin/manage_products.php?success=Quantity updated successfully.");
        exit;
    } else {
        header("Location: ../views/admin/manage_products.php?error=Error updating quantity.");
        exit;
    }
}

?>
