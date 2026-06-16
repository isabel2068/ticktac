<?php
$success = isset($_GET['success']) ? true : false;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signup - TICKTAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ticktac.css">
    
    <style>
        html, body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        .profile-card {
            border: 2px dashed #0d6efd;
            padding: 10px !important;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #001e3c; /* Matches your screenshot background */
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* THE SHARED CONTAINER */
        .login-main-card {
            display: flex;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 900px; /* Adjust width as needed */
            min-height: 500px;
        }

        /* LEFT SIDE: IMAGE */
        .login-left-side {
            flex: 1;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-right: 1px solid #eee;
            padding: 20px;
        }

        .login-left-side img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
        }

        .left-title {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        /* RIGHT SIDE: YOUR LOGIN FORM */
        .login-right-side {
            flex: 1;
            padding: 20px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Re-using your specific card styles for the right side */
        .profile-container {
            position: relative;
            width: 80px; 
            height: 80px;
            margin: 0 auto;
        }

        .profile-pic {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .form-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .input-group-text { background-color: #f8f9fa; }
        .eye-wrapper { display: none; cursor: pointer; background-color: #fff !important; }
        .login-link { text-decoration: none; font-weight: 600; color: #0d6efd; }

        /* RESPONSIVE: Hide left side on mobile */
        @media (max-width: 768px) {
            .login-left-side { display: none; }
            .login-main-card { max-width: 450px; }
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="login-main-card">
        
        <div class="login-left-side">
            <h4 class="left-title">Welcome to TICKTAC</h4>
            <p class="text-muted small"><i>Calendar System</i></p>
            <!-- <img src="" alt="Illustration"> -->
        </div>

        <div class="login-right-side">
            <div class="text-center mb-2">
                <h5 class="mb-1">Create Account</h5>
                <p class="text-muted small mb-2">Enter your details to get started</p>
                
            </div>

            <form action="register_process.php" method="POST" enctype="multipart/form-data" id="signupForm">

            <div class="profile-card bg-light rounded-2 mb-2 text-center">
            <label for="profileUpload" style="cursor:pointer; display:block;">

            <div class="profile-container mx-auto">
                <img src="default_profile/user.png" id="profilePreview" class="rounded-circle profile-pic">

                <div class="upload-btn position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-1"
                    style="width:24px;height:24px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-camera-fill"></i>
                </div>
            </div>
            </label>

            <!-- REAL INPUT -->
            <input type="file"
                name="profileUpload"
                id="profileUpload"
                class="form-control mt-2"
                accept="image/*"
                onchange="previewImage(event)"
                style="display:none;">
            </div>

                <div class="form-section-title">Personal Info</div>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="fname" class="form-control" placeholder="first name" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <input type="text" name="lname" class="form-control" placeholder="last name" required>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-at"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="...@gmail.com" required>
                        </div>
                        <small id="emailMsg" class="text-danger d-none" style="font-size:12.3px">
                            Please enter a valid email address
                        </small>
                    </div>
                </div>

                <div class="form-section-title">Login Details</div>
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="username" required>
                        </div>
                        <small id="usernameTakenMsg" class="text-danger d-none" style="font-size:12.3px">
                        Username already taken
                    </small>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control" id="pass" placeholder="password" oninput="manageEye(this, 'eyeWrap1')" required>
                            <span class="input-group-text eye-wrapper" id="eyeWrap1" onclick="toggleEye('pass', 'eyeIcon1')">
                                <i class="bi bi-eye-slash" id="eyeIcon1"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control" id="confirmPass" placeholder="confirm password" oninput="manageEye(this, 'eyeWrap2')" required>
                            <span class="input-group-text eye-wrapper" id="eyeWrap2" onclick="toggleEye('confirmPass', 'eyeIcon2')">
                                <i class="bi bi-eye-slash" id="eyeIcon2"></i>
                            </span>
                            <small id="passMatchMsg" class="text-danger d-none mt-1" style="font-size:12.3px">
                                Passwords do not match
                            </small>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button class="btn btn-primary py-2 fw-bold" type="submit">Signup</button>
                </div>

                <div class="text-center mt-3">
                    <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="login-link">Login</a></p>
                </div>
            </form>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    const password = document.getElementById('pass');
    const confirmPassword = document.getElementById('confirmPass');
    const msg = document.getElementById('passMatchMsg');
    const form = document.getElementById('signupForm');

    // LIVE CHECK (while typing)
    function checkPasswordMatch() {
        if (confirmPassword.value.length === 0) {
            msg.classList.add('d-none');
            return true;
        }

        if (password.value !== confirmPassword.value) {
            msg.classList.remove('d-none');
            return false;
        } else {
            msg.classList.add('d-none');
            return true;
        }
    }

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    // BLOCK SUBMIT IF NOT MATCHING
    form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();

            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Please make sure both passwords match.',
            });

            msg.classList.remove('d-none');
        }
    });

    function previewImage(event) {
    console.log(event.target.files); // 🔥 MUST show file

    const file = event.target.files[0];
    if (file) {
        document.getElementById('profilePreview').src =
            URL.createObjectURL(file);
        }
    }

    function manageEye(input, wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        wrapper.style.display = input.value.length > 0 ? 'flex' : 'none';
    }

    function toggleEye(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    }


    const email = document.querySelector('input[name="email"]');
    const emailMsg = document.getElementById('emailMsg');

    // EMAIL FORMAT REGEX
    function isValidEmail(emailValue) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue);
    }

    // LIVE EMAIL CHECK
    function checkEmail() {
        if (email.value.length === 0) {
            emailMsg.classList.add('d-none');
            email.classList.remove('is-invalid');
            return false;
        }

        if (!isValidEmail(email.value)) {
            emailMsg.classList.remove('d-none');
            email.classList.add('is-invalid');
            return false;
        } else {
            emailMsg.classList.add('d-none');
            email.classList.remove('is-invalid');
            return true;
        }
    }

    email.addEventListener('input', checkEmail);


    form.addEventListener('submit', function(e) {

    const emailValid = isValidEmail(email.value);
    const passMatch = password.value === confirmPassword.value;

    if (!emailValid || !passMatch) {
        e.preventDefault();

        let message = "";

        if (!emailValid) {
            message += "Invalid email format. ";
            emailMsg.classList.remove('d-none');
        }

        if (!passMatch) {
            message += "Passwords do not match.";
            msg.classList.remove('d-none');
        }

        Swal.fire({
            icon: 'error',
            title: 'Form Error',
            text: message,
        });
    }
    });

    const username = document.querySelector('input[name="username"]');
    const usernameTakenMsg = document.getElementById('usernameTakenMsg');

    let usernameTimer;

    username.addEventListener('input', function () {

        clearTimeout(usernameTimer);

        const value = username.value.trim();

        if (value.length === 0) {
            usernameTakenMsg.classList.add('d-none');
            username.classList.remove('is-invalid');
            return;
        }

        usernameTimer = setTimeout(() => {

            fetch('check_username.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'username=' + encodeURIComponent(value)
            })
            .then(res => res.text())
            .then(data => {

                if (data === 'taken') {
                    usernameTakenMsg.classList.remove('d-none');
                    username.classList.add('is-invalid');
                } else {
                    usernameTakenMsg.classList.add('d-none');
                    username.classList.remove('is-invalid');
                }

            });

        }, 400); // small delay to avoid spam requests
    });
</script>



<script>
    // ✅ REGISTRATION SUCCESS ALERT
if (params.get('registered') === '1') {
    Swal.fire({
        icon: 'success',
        title: 'Registration Successful',
        text: 'Your account has been created!',
        timer: 1500,
        showConfirmButton: false
    });
}

document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Registration Successful',
        text: 'Your account has been created!',
        timer: 1500,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'login.php';
    });
});
</script>


</body>
</html>