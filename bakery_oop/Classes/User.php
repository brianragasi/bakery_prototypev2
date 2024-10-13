<?php
require_once "Database.php";

class User extends Database {
    public function register($name, $email, $password) {
        $name = $this->conn->real_escape_string($name);
        $email = $this->conn->real_escape_string($email);

        $checkEmailSql = "SELECT * FROM users WHERE email = '$email'";
        $checkEmailResult = $this->conn->query($checkEmailSql);

        if ($checkEmailResult->num_rows > 0) {
            return "Error: This email address is already registered.";
        }

        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";

        if ($this->conn->query($sql)) {
            return "Registration successful!";
        } else {
            return "Error: " . $this->conn->error;
        }
    }

    public function login($email, $password) {
      $email = $this->conn->real_escape_string($email);
  
      $sql = "SELECT * FROM users WHERE email = '$email'";
      $result = $this->conn->query($sql);
  
      if ($result->num_rows == 1) {
          $user = $result->fetch_assoc();
          if (password_verify($password, $user['password'])) {
              session_start();
              $_SESSION['user_id'] = $user['id'];
              $_SESSION['user_name'] = $user['name']; // Store user's NAME in 'user_name'
              $_SESSION['user_email'] = $user['email'];
  
              // --- ADMIN CHECK AND REDIRECT ---
              if ($user['isAdmin'] == 1) {
                  $_SESSION['admin'] = true;
                  header("Location: ../views/admin/admin_dashboard.php");
                  exit;
              } else {
                  $_SESSION['admin'] = false;
                  header("Location: ../views/user/index.php");  // Correct redirect path
                  exit;
              }
              // --- END ADMIN CHECK AND REDIRECT ---
  
          } else {
              return "Invalid password.";
          }
      } else {
          return "User not found.";
      }
  }
    public function getUserDetails($userId) {
        $userId = $this->conn->real_escape_string($userId);
        $sql = "SELECT * FROM users WHERE id = '$userId'";
        $result = $this->conn->query($sql);
        return ($result->num_rows == 1) ? $result->fetch_assoc() : null;
    }

    public function updateProfile($userId, $name, $email, $password = null) {
        $userId = $this->conn->real_escape_string($userId);
        $name = $this->conn->real_escape_string($name);
        $email = $this->conn->real_escape_string($email);

        $updateFields = "name = '$name', email = '$email'";

        if (!is_null($password) && !empty($password)) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $updateFields .= ", password = '$password'";
        }

        $sql = "UPDATE users SET $updateFields WHERE id = '$userId'";
        return $this->conn->query($sql);
    }

    public function executeQuery($sql) {
        return $this->conn->query($sql);
    }

    public function addLoyaltyPoints($userId, $points) {
        $userId = $this->conn->real_escape_string($userId);
        $points = $this->conn->real_escape_string($points);
        $sql = "UPDATE users SET loyalty_points = loyalty_points + '$points' WHERE id = '$userId'";
        return $this->conn->query($sql);
    }

    public function deductLoyaltyPoints($userId, $points) {
        $userId = $this->conn->real_escape_string($userId);
        $points = $this->conn->real_escape_string($points);
        
        // Ensure you don't deduct more points than the user has
        $currentPoints = $this->getLoyaltyPoints($userId);
        if ($points > $currentPoints) {
            return false; // Or throw an exception, or deduct only up to the available points
        }
        
        $sql = "UPDATE users SET loyalty_points = loyalty_points - '$points' WHERE id = '$userId'";
        return $this->conn->query($sql);
    }

    public function getLoyaltyPoints($userId) {
        $userId = $this->conn->real_escape_string($userId);
        $sql = "SELECT loyalty_points FROM users WHERE id = '$userId'";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['loyalty_points'];
        }
        return 0; // Return 0 if no loyalty points found or error
    }

}
?>