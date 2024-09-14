<?php
// Check if a session is already active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already active
}

// Define timeout duration (in seconds). Example: 15 minutes = 900 seconds.
$timeout_duration = 100; // Adjust this value as needed

// Check if the user is logged in (assuming session 'uid' is set upon login)
if (isset($_SESSION['uid'])) {

    // Check if "last activity" is set in the session
    if (isset($_SESSION['last_activity'])) {

        // Calculate the time elapsed since the last interaction
        $elapsed_time = time() - $_SESSION['last_activity'];

        // If the elapsed time exceeds the timeout duration, log out the user
        if ($elapsed_time > $timeout_duration) {
            session_unset();     // Unset $_SESSION variables
            session_destroy();   // Destroy the session completely

            // Redirect to login page or show timeout message
            header("Location: login.php?session_expired=true");
            exit;
        }
    }

    // Update last activity time stamp
    $_SESSION['last_activity'] = time();
} else {
    // If the user is not logged in, redirect to login page
    header("Location: login.php");
    exit;
}
?>
