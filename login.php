<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TICKTAC - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="ticktac.css">
    
    <style>
        body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Strict no-scroll */
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* background-image: url(bg-white.png); */
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.9; /* Adjust this value (0.0 to 1.0) */
            z-index: -1; /* Places the image behind your content */
        }

        /* The wrapper that centers the card and ensures spacing */
        .main-content {
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Creates the space at the top and bottom of the screen */
            padding-top: 20px;
            padding-bottom: 20px;
            height: 100vh;
        }

        .card {
            border: none;
            border-radius: 16px;
            width: 95%; /* Responsive width for mobile */
            max-width: 480px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            /* Prevents the card from growing too tall */
            /* max-height: 85vh;  */
            display: flex;
            flex-direction: column;
            padding: 1rem 1.5rem 1rem 0 !important;
        }

        /* Allows the form to be scrollable INTERNALLY only if the screen is tiny */
        .card-body-wrapper {
            /* overflow-y: auto; */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE 10+ */
        }
        .card-body-wrapper::-webkit-scrollbar { display: none; /* Chrome/Safari */ }

        .profile-card {
            border: 2px dashed #0d6efd;
            padding: 10px !important;
        }

        .profile-container {
            position: relative;
            width: 80px; 
            height: 80px;
            margin: 0 auto;
        }

        .profile-pic {
            width: 90px;
            height: 90px;
            /* box-shadow: 0 4px 10px rgba(0,0,0,0.1); */
        }

        .form-section-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        .input-group-text { background-color: #f8f9fa; font-size: 0.85rem; }
        .form-control { font-size: 0.85rem; padding: 0.45rem 0.75rem; }

        .eye-wrapper {
            display: none;
            cursor: pointer;
            background-color: #fff !important;
        }

        .register-link {
            text-decoration: none;
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>
<body class="bg-colored">

<div class="main-content">
    <div class="card card-front" style="max-width: 60rem;">
        <div class="row g-0">
            <div class="col-md-7 text-center">
                <img src="default_profile/twologos.jpg" alt="" style="width:30rem; height:6rem;">
                <!-- <h4 class="left-title pt-3">Welcome to TICKTAC</h4>
                <p class="text-muted small mb-0"><i>Calendar System</i></p> -->
                <!-- <img src="https://img.freepik.com/free-vector/businessman-planning-events-deadlines-agenda_74855-6274.jpg" class="img-fluid rounded-start" style="width:23rem; height:20rem;" alt="..."> -->
                 <img src="finalgraphics.png" alt="" style="width:30rem; height: 20rem;">
            </div>

            <div class="col-md-5">
                <div class="card-body-wrapper">
                    <div class="text-center mb-2 mt-2">
                        <div class="profile-container mb-3 d-flex justify-content-center">
                            <img src="finallogocal.png" class="profile-pic" alt="Profile" style="width:20rem; height: 6rem;">
                        </div>
                        <h5 class="mb-1">Sign In</h5>
                        <p class="text-muted small">Please enter your credentials to sign-in</p>
                    </div>

                    <form action="login_process.php" method="POST">
                        <div class="form-section-title">Login Details</div>
                        
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                    <input type="password" name="password" class="form-control" id="pass" placeholder="Password" oninput="manageEye(this, 'eyeWrap1')" required>
                                    <span class="input-group-text eye-wrapper" id="eyeWrap1" onclick="toggleEye('pass', 'eyeIcon1')">
                                        <i class="bi bi-eye-slash" id="eyeIcon1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button class="btn btn-primary py-2 fw-bold" type="submit">Login</button>
                        </div>

                        <div class="text-center mt-3">
                            <p class="small text-muted mb-0">Don't have an account? <a href="register.php" class="register-link">Signup</a></p>
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

document.addEventListener("DOMContentLoaded", function () {

    const params = new URLSearchParams(window.location.search);

    // ❌ LOGIN ERROR
    if (params.get('error') === 'invalid') {
        Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: 'Invalid username or password',
        });
    }

    // ✅ LOGIN SUCCESS
    if (params.get('success') === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Welcome back!',
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = 'dashboard.php';
        });
    }

    // ✅ REGISTRATION SUCCESS
    if (params.get('registered') === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful',
            text: 'Your account has been created!',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // ✅ LOGOUT SUCCESS (FIXED)
    if (params.get('logout') === '1') {
        Swal.fire({
            icon: 'success',
            title: 'Logged Out',
            text: 'You have successfully logged out.',
            timer: 1500,
            showConfirmButton: false
        });
    }
});
</script>

</body>
</html>


