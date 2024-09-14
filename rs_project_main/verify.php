<?php
include("config.php");

$error = "";
$msg = "";

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    if (isset($_POST['verify'])) {
        $verificationCode = $_POST['verification_code'];

        // Query to check if the verification code matches
        $query = "SELECT * FROM user WHERE uemail='$email' AND verification_code='$verificationCode'";
        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) > 0) {
            // If the code is correct, update the user status to verified
            $sql = "UPDATE user SET is_verified = 1 WHERE uemail='$email'";
            mysqli_query($con, $sql);

            $msg = "<p class='alert alert-success'>Email verified successfully!</p>";
        } else {
            $error = "<p class='alert alert-danger'>Invalid verification code.</p>";
        }
    }
} else {
    header("Location: register.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>Email Verification</h2>
    <p>Please enter the 8-digit verification code sent to your email.</p>

    <!-- Display error or success message -->
    <?php if ($error) echo $error; ?>
    <?php if ($msg) echo $msg; ?>

    <!-- Form to input the verification code -->
    <form method="POST" id="verificationForm">
        <div class="form-group">
            <label for="verification_code">Verification Code</label>
            <input type="text" id="verification_code" name="verification_code" class="form-control" placeholder="Enter 8-digit code" required>
        </div>
        <button type="submit" name="verify" class="btn btn-primary">Verify</button>
        <div class="text-center dont-have">Login <a href="login.php">Login</a></div>
    </form>
</div>

<!-- Bootstrap and jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script><!-- Optional JS to auto-show the popup form -->
<script>
$(document).ready(function() {
    $('#verificationModal').modal('show'); // Automatically show modal when page loads
});
</script>

</body>
</html>
