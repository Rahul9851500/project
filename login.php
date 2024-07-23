<?php
include 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username and password are not empty
    if (!empty($username) && !empty($password)) {
        $sql = "SELECT id, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    // Login successful
                    $_SESSION['user_id'] = $id;
                    $_SESSION['username'] = $username;
                    echo "Login successful";
                    // Redirect to a new page
                    header("Location: dashboard.html");
                    exit();
                } else {
                    echo "Invalid password";
                }
            } else {
                echo "No user found with that username";
            }

            $stmt->close();
        } else {
            echo "Failed to prepare the SQL statement";
        }
    } else {
        echo "Username and password should not be empty";
    }

    $conn->close();
}
?>
