<?php
include("config.php");
//require 'vendor/autoload.php'; // PHPMailer autoload


require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = "";
$msg = "";

if (isset($_REQUEST['reg'])) {
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $phone = $_REQUEST['phone'];
    $plainPassword = $_REQUEST['pass'];
    $utype = $_REQUEST['utype'];

    $uimage = $_FILES['uimage']['name'];
    $temp_name1 = $_FILES['uimage']['tmp_name'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "<p class='alert alert-warning'>Invalid email format</p>";
    } else {
        // Check if email already exists
        $query = "SELECT * FROM user WHERE uemail='$email'";
        $res = mysqli_query($con, $query);
        $num = mysqli_num_rows($res);

        if ($num == 1) {
            $error = "<p class='alert alert-warning'>Email Id already Exists</p>";
        } else {
            if (!empty($name) && !empty($email) && !empty($phone) && !empty($plainPassword) && !empty($uimage)) {
                // Hash the password using bcrypt
                $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

                // Generate an 8-digit verification code
                $verificationCode = rand(10000000, 99999999);

                // Insert user details into the database along with the verification code
                $sql = "INSERT INTO user (uname, uemail, uphone, upass, utype, uimage, verification_code, is_verified) 
                        VALUES ('$name', '$email', '$phone', '$hashedPassword', '$utype', '$uimage', '$verificationCode', 0)";
                $result = mysqli_query($con, $sql);
                move_uploaded_file($temp_name1, "admin/user/$uimage");

                if ($result) {
                    // Send verification email
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                                                $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'mobarokislam14@gmail.com';
                        $mail->Password = 'byahvkpshgywlwkp';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('mobarokislam@gmail.com', 'real_state_homex');
                        $mail->addAddress($email);


                        $mail->isHTML(true);
                        $mail->Subject = 'Email Verification';
                        $mail->Body = "Hi $name,<br><br>Your verification code is: <strong>$verificationCode</strong><br><br>
                        Please click on the link below to verify your email:<br>
                        <a href='http://localhost/real_state/rs_project_main/verify.php?email=$email'>Click here to verify your email</a>";

                        $mail->send();

                        // Show success message with a link to the verification page
                        $msg = "<p class='alert alert-success'>Registration Successful! A verification email has been sent to $email.<br>
                                Please <a href='verify.php?email=$email'>click here</a> to verify your email.</p>";
                    } catch (Exception $e) {
                        $error = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } else {
                    $error = "<p class='alert alert-warning'>Registration Not Successful</p>";
                }
            } else {
                $error = "<p class='alert alert-warning'>Please fill in all the fields</p>";
            }
        }
    }
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
<!-- Required meta tags -->
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!-- Meta Tags -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="shortcut icon" href="images/favicon.ico">

<!--	Fonts
	========================================================-->
<link href="https://fonts.googleapis.com/css?family=Muli:400,400i,500,600,700&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Comfortaa:400,700" rel="stylesheet">

<!--	Css Link
	========================================================-->
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/bootstrap-slider.css">
<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="css/layerslider.css">
<link rel="stylesheet" type="text/css" href="css/color.css">
<link rel="stylesheet" type="text/css" href="css/owl.carousel.min.css">
<link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
<link rel="stylesheet" type="text/css" href="fonts/flaticon/flaticon.css">
<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/login.css">

<!--	Title
	=========================================================-->
<title>Homex - Real Estate Template</title>
 <style>
     .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .alert-warning {
            color: #ff0000; /* Red for warning */
            font-size: 12px; /* Small font size */
            margin-top: 5px; /* Space above the error message */
        }

        input#password.error {
            border-color: #ff0000; /* Red border when password is weak */
        }

        input#password.success {
            border-color: #28a745; /* Green border when password is strong */
        }
    </style>
     <script>
        function validateName() {
            const nameInput = document.getElementById('name');
            const nameError = document.getElementById('nameError');
            const namePattern = /^[a-zA-Z\s'-]+$/;

            if (!namePattern.test(nameInput.value)) {
                nameError.textContent = "Name can only contain letters, spaces, hyphens, and apostrophes.";
            } else {
                nameError.textContent = "";
            }
        }
        function validatePhone() {
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phoneError');
        const bangladeshPhonePattern = /^(?:\+8801|01)[3-9]\d{8}$/;

        if (!bangladeshPhonePattern.test(phoneInput.value)) {
            phoneError.textContent = "Invalid phone number format. It should be +8801XXXXXXXXX or 01XXXXXXXXX.";
        } else {
            phoneError.textContent = "";
        }
    }
        function validatePassword() {
        const passwordInput = document.getElementById('password');
        const passwordError = document.getElementById('passwordError');
        const password = passwordInput.value;

        const criteria = {
            length: password.length >= 8,                   // At least 8 characters
            uppercase: /[A-Z]/.test(password),              // At least one uppercase letter
            lowercase: /[a-z]/.test(password),              // At least one lowercase letter
            number: /[0-9]/.test(password),                 // At least one number
            specialChar: /[!@#$%^&*(),.?":{}|<>]/.test(password) // At least one special character
        };

        let message = "Password must have:";
        if (!criteria.length) message += "<br>- At least 8 characters";
        if (!criteria.uppercase) message += "<br>- At least one uppercase letter";
        if (!criteria.lowercase) message += "<br>- At least one lowercase letter";
        if (!criteria.number) message += "<br>- At least one number";
        if (!criteria.specialChar) message += "<br>- At least one special character";

        if (criteria.length && criteria.uppercase && criteria.lowercase && criteria.number && criteria.specialChar) {
            passwordError.innerHTML = "<span style='color: green;'>Strong password!</span>";
            passwordInput.classList.remove('error');
            passwordInput.classList.add('success');
        } else {
            passwordError.innerHTML = message;
            passwordInput.classList.remove('success');
            passwordInput.classList.add('error');
    }
}

    </script>
</head>
<body>

<!--	Page Loader
=============================================================
<div class="page-loader position-fixed z-index-9999 w-100 bg-white vh-100">
	<div class="d-flex justify-content-center y-middle position-relative">
	  <div class="spinner-border" role="status">
		<span class="sr-only">Loading...</span>
	  </div>
	</div>
</div>
--> 


<div id="page-wrapper">
    <div class="row"> 
        <!--	Header start  -->
		<?php include("include/header.php");?>
        <!--	Header end  -->
        
        <!--	Banner   --->
        <div class="banner-full-row page-banner" style="background-image:url('images/breadcromb.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="page-name float-left text-white text-uppercase mt-1 mb-0"><b>Register</b></h2>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="breadcrumb" class="float-left float-md-right">
                            <ol class="breadcrumb bg-transparent m-0 p-0">
                                <li class="breadcrumb-item text-white"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Register</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
         <!--	Banner   --->
	<!-- verifiations start -->	 
<div id="verificationModal" class="modal">
    <div class="modal-content">
        <h2>Email Verification</h2>
        <form id="verificationForm">
            <div class="form-group">
                <label for="verification_code">Enter Verification Code</label>
                <input type="text" id="verification_code" class="form-control" placeholder="Enter code">
            </div>
            <button type="button" class="btn btn-success" id="verifyCodeBtn">Verify</button>
        </form>
    </div>
</div>
<!-- verifications -->
		 
        <div class="page-wrappers login-body full-row bg-gray">
            <div class="login-wrapper">
            	<div class="container">
                	<div class="loginbox">
                        <div class="login-right">
							<div class="login-right-wrap">
								<h1>Register</h1>
								<p class="account-subtitle">Access to our dashboard</p>
								<?php echo $error; ?><?php echo $msg; ?>
								<!-- Form -->
								<form method="post" enctype="multipart/form-data">
									<div class="form-group">
										<input type="text" id="name" name="name" class="form-control " placeholder="Your Name*" onkeyup="validateName()">
                                        <span id="nameError" class="alert-warning"></span>
									</div>
									<div class="form-group">
										<input type="email"  name="email" class="form-control" placeholder="Your Email*">
									</div>
									<div class="form-group">
										<input type="text" id="phone"  name="phone" class="form-control" placeholder="Your Phone*" onkeyup="validatePhone()" maxlength="11" >
                                        <span id="phoneError" class="alert-warning"></span>
									</div>
									<div class="form-group">
										<input type="password" id="password" name="pass"  class="form-control" placeholder="Your Password*" onkeyup="validatePassword()">
                                         <span id="passwordError" class="alert-warning"></span>
									</div>

									 <div class="form-check-inline">
									  <label class="form-check-label">
										<input type="radio" class="form-check-input" name="utype" value="user" checked>User
									  </label>
									</div>
									<div class="form-check-inline">
									  <label class="form-check-label">
										<input type="radio" class="form-check-input" name="utype" value="agent">Agent
									  </label>
									</div>
									<div class="form-check-inline disabled">
									  <label class="form-check-label">
										<input type="radio" class="form-check-input" name="utype" value="builder">Builder
									  </label>
									</div> 
									
									<div class="form-group">
										<label class="col-form-label"><b>User Image</b></label>
										<input class="form-control" name="uimage" type="file">
									</div>

									<!-- Google reCAPTCHA widget -->
        					<div class="form-group">
										<div class="g-recaptcha" 			data-sitekey="6LeA9TsqAAAAAAcgDY2E5Cjn6Xf6FXSJwTAcjs_q">

									</div>
									</div>
									
									<button class="btn btn-primary" name="reg" value="Register" type="submit">Register</button>
									
								</form>
								
								<div class="login-or">
									<span class="or-line"></span>
									<span class="span-or">or</span>
								</div>
								
								<!-- Social Login -->
								<div class="social-login">
									<span>Register with</span>
									<a href="#" class="facebook"><i class="fab fa-facebook-f"></i></a>
									<a href="#" class="google"><i class="fab fa-google"></i></a>
									<a href="#" class="facebook"><i class="fab fa-twitter"></i></a>
									<a href="#" class="google"><i class="fab fa-instagram"></i></a>
								</div>
								<!-- /Social Login -->
								
								<div class="text-center dont-have">Already have an account? <a href="login.php">Login</a></div>
								
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	<!--	login  -->
        
        
        <!--	Footer   start-->
		<?php include("include/footer.php");?>
		<!--	Footer   start-->
        
        <!-- Scroll to top --> 
        <a href="#" class="bg-secondary text-white hover-text-secondary" id="scroll"><i class="fas fa-angle-up"></i></a> 
        <!-- End Scroll To top --> 
    </div>
</div>
<!-- Wrapper End --> 

<!--	Js Link
============================================================--> 
<!-- verification start-->

<script>
$(document).ready(function() {
    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'register.php', // PHP file for processing the registration
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                // If registration is successful, show the verification modal
                if (response.includes('Registration Successful')) {
                    $('#verificationModal').fadeIn();
                } else {
                    alert(response); // Handle errors
                }
            }
        });
    });

    // Handle the verification code submission
    $('#verifyCodeBtn').on('click', function() {
        var code = $('#verification_code').val();
        $.ajax({
            url: 'verify.php', // PHP file for handling code verification
            type: 'POST',
            data: { verification_code: code, email: $('#email').val() },
            success: function(response) {
                if (response === 'verified') {
                    alert('Email verified successfully!');
                    $('#verificationModal').fadeOut();
                    // You can redirect to another page or log in the user here
                } else {
                    alert('Invalid verification code. Please try again.');
                }
            }
        });
    });
});
</script>

<!-- verification end-->



<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>


 <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="js/jquery.min.js"></script> 
<!--jQuery Layer Slider --> 
<script src="js/greensock.js"></script> 
<script src="js/layerslider.transitions.js"></script> 
<script src="js/layerslider.kreaturamedia.jquery.js"></script> 
<!--jQuery Layer Slider --> 
<script src="js/popper.min.js"></script> 
<script src="js/bootstrap.min.js"></script> 
<script src="js/owl.carousel.min.js"></script> 
<script src="js/tmpl.js"></script> 
<script src="js/jquery.dependClass-0.1.js"></script> 
<script src="js/draggable-0.1.js"></script> 
<script src="js/jquery.slider.js"></script> 
<script src="js/wow.js"></script> 
<script src="js/custom.js"></script>
</body>
</html>