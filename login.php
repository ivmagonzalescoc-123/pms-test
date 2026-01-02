<?php
session_start();
require_once 'conn.php';

// If already logged in, redirect to admin
if (isset($_SESSION['owner_id'])) {
    header('Location: admin.php');
    exit;
}

// Handle POST login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($input === '' || $password === '') {
        header('Location: index.html?error=1');
        exit;
    }

    $stmt = $conn->prepare("SELECT owner_id, firstname, lastname, middlename, username, password, email, contact_no FROM owner WHERE username = ? OR email = ? LIMIT 1");
    $stmt->bind_param('ss', $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Plain-text password check as requested (not secure)
        if ($password === $row['password']) {
            // Successful login: store minimal session info
            $_SESSION['owner_id'] = $row['owner_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];

            header('Location: admin.php');
            exit;
        }
    }

    // Failed login
    header('Location: index.html?error=1');
    exit;
}

// If GET request just redirect to the static login page
header('Location: index.html');
exit;

?>
