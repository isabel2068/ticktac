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
            overflow: hidden; /* Strict no-scroll */
            background-color: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
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
        }

        .card {
            border: none;
            border-radius: 16px;
            width: 95%; /* Responsive width for mobile */
            max-width: 480px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            /* Prevents the card from growing too tall */
            max-height: 85vh; 
            display: flex;
            flex-direction: column;
            padding: 1rem 1.5rem !important;
        }

        /* Allows the form to be scrollable INTERNALLY only if the screen is tiny */
        .card-body-wrapper {
            overflow-y: auto;
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

        .login-link {
            text-decoration: none;
            font-weight: 600;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-light border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand" href="#"><b>TICKTAC</b></a>
        <div class="ms-auto">
            <img src="dtilogo.png" style="width:45px; height:45px" alt="Logo" onerror="this.src='https://via.placeholder.com/40'">
        </div>
    </div>
</nav>

<div class="main-content">
    <div class="card">
        <div class="card-body-wrapper">
            <div class="text-center mb-2">
                <h5 class="mb-1">Create Account</h5>
                <p class="text-muted small mb-2">Enter your details to get started</p>
                
            </div>

            <form action="register_process.php" method="POST" enctype="multipart/form-data">

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
                            <input type="text" name="fname" class="form-control" placeholder="First Name" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-at"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="...@gmail.com" required>
                        </div>
                    </div>
                </div>

                <div class="form-section-title">Login Details</div>
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="password" class="form-control" id="pass" placeholder="Password" oninput="manageEye(this, 'eyeWrap1')" required>
                            <span class="input-group-text eye-wrapper" id="eyeWrap1" onclick="toggleEye('pass', 'eyeIcon1')">
                                <i class="bi bi-eye-slash" id="eyeIcon1"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <input type="password" name="confirm_password" class="form-control" id="confirmPass" placeholder="Confirm Password" oninput="manageEye(this, 'eyeWrap2')" required>
                            <span class="input-group-text eye-wrapper" id="eyeWrap2" onclick="toggleEye('confirmPass', 'eyeIcon2')">
                                <i class="bi bi-eye-slash" id="eyeIcon2"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="d-grid mt-3">
                    <button class="btn btn-primary py-2 fw-bold" type="submit">Signup</button>
                </div>

                <div class="text-center mt-2">
                    <p class="small text-muted mb-0">Already have an account? <a href="login.php" class="login-link">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
</script>

</body>
</html>