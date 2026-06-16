<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fast Side-Eye Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="alert.css">
    <link rel="stylesheet" href="ticktac.css">
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        body::before {
            content: "";
            position: fixed; /* Fixed keeps the background from moving when you scroll */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            
            background-image: url('dti.jpg'); /* Put your image path here */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            
            /* opacity: 0.15;  */
            filter: blur(4px);
            z-index: -1;   /* Pushes it behind absolutely everything else on the page */
        }

        .login-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 420px;
        }

        .login-card {
            background: white;
            padding: 20px 40px 30px;
            border-radius: 35px;
            box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
            width: 100%;
            z-index: 2;
            text-align: center;
        }

        .brand-logo {
            width: 130px;
            height: auto;
            margin-bottom: 5px;
        }

        h5 {
            font-weight: 700;
            margin-bottom: 20px;
            color: #212529;
            letter-spacing: -0.5px;
        }

        .input-group {
            border: 1px solid #dee2e6;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            transition: border-color 0.2s;
        }

        .input-group:focus-within {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }

        .input-group-text {
            background: transparent;
            border: none;
            padding: 0 15px;
            color: #6c757d;
        }

        .form-floating > .form-control {
            border: none;
            border-radius: 0;
            height: 50px;           /* Your desired height */
            min-height: 50px;
            padding-top: 1.2rem;    /* Reduced top padding to move text up */
            padding-bottom: 0.5rem;
        }

        .form-floating > .form-control:focus {
            box-shadow: none;
        }

        .form-floating > label {
            padding-top: 0.8rem;    /* Centers the placeholder vertically when empty */
            font-size: 0.9rem;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            transform: scale(0.85) translateY(-0.7rem) translateX(0.15rem);
            color: #0d6efd; /* Optional: colors the label when active */
        }

        .btn-continue {
            border: none;
            padding: 16px;
            width: 100%;
            border-radius: 50px;
            font-weight: 600;
            margin-top: 10px;
            transition: transform 0.2s;
        }

        .btn-continue:active {
            transform: scale(0.98);
        }

        .footer-links {
            margin-top: 20px;
            font-size: 0.85rem;
            font-weight: 400;
        }

        .custom-link {
            color: #6c757d;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .custom-link:hover {
            color: #212529;
        }

        .custom-links {
            color: #0080ff; 
            font-weight: 700;
            text-decoration: none;            
        }

        .custom-links:hover {
            color: #0080ff79; 
        }

        .title-cus{
            font-size: 0.65rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
            margin-top: 10px;
            letter-spacing: 0.5px;
            text-align: start;
        }

    </style>
</head>
<body>

<div class="login-container">
    <!-- LOGIN CARD -->
    <div class="login-card">
        <img src="finallogo.png" alt="Logo" class="brand-logo">
        <div>
            <h5 class="mb-0">Sign In</h5>
        </div>
        
        <form action="login_process2.php" method="POST" id="loginForm" novalidate>
    
        <div class="title-cus">Login Details</div>

        <!-- USERNAME OR EMAIL -->
        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="bi bi-person-circle"></i>
            </span>
            <div class="form-floating flex-grow-1">
            <input type="text"
            class="form-control shy-input"
            id="floatingInput"
            name="login"
            placeholder="Username"
            value="<?= isset($_SESSION['old_login']) ? htmlspecialchars($_SESSION['old_login']) : '' ?>"
            required>
                <label for="floatingInput">Username</label>
            </div>
        </div>

        <!-- PASSWORD -->
        <div class="input-group mb-2">
            <span class="input-group-text">
                <i class="bi bi-shield-lock-fill"></i>
            </span>
            <div class="form-floating flex-grow-1">
            <input type="password"
                class="form-control shy-input"
                id="floatingPassword"
                name="password"
                placeholder="Password"
                required>
                <label for="floatingPassword">Password</label>
            </div>

            <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                <i class="bi bi-eye-slash" id="eyeIcon"></i>
            </span>
        </div>

        <div class="text-end mb-2">
            <a href="#" class="custom-link small" style="font-weight: 500;">
                Forgot password?
            </a>
        </div>

        <button type="submit" class="btn btn-primary btn-continue">
            Sign In
        </button>

        </form>

        <div class="footer-links">
            <span class="text-muted" style="font-weight: 300;">Don't have an account?</span>
            <a href="registerv2.php" class="custom-links ms-1">Sign Up</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const watchers = document.getElementById('watchers');
    const inputs = document.querySelectorAll('.shy-input');
    const pupils = document.querySelectorAll('.pupil');
    let timer;

    // --- CHARACTER ANIMATION LOGIC ---
    function lookAt(tx, ty) {
        if (watchers.classList.contains('state-away')) return;
        pupils.forEach(p => {
            const rect = p.getBoundingClientRect();
            const px = rect.left + rect.width / 2;
            const py = rect.top + rect.height / 2;
            const angle = Math.atan2(ty - py, tx - px);
            const dist = 10; 
            p.style.transform = `translate(${Math.cos(angle)*dist}px, ${Math.sin(angle)*dist}px)`;
        });
    }

    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            watchers.className = 'watchers-group state-away';
        });

        input.addEventListener('input', () => {
            watchers.className = 'watchers-group state-peeking';
            const r = input.getBoundingClientRect();
            lookAt(r.left + r.width/2, r.top + r.height/2);
            clearTimeout(timer);
            timer = setTimeout(() => {
                if(document.activeElement === input) {
                    watchers.className = 'watchers-group state-away';
                }
            }, 350); 
        });

        input.addEventListener('blur', () => {
            watchers.className = 'watchers-group';
        });
    });

    document.addEventListener('mousemove', (e) => {
        lookAt(e.clientX, e.clientY);
    });

    // --- PASSWORD TOGGLE LOGIC ---
    const toggleBtn = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#floatingPassword');
    const eyeIcon = document.querySelector('#eyeIcon');

    toggleBtn.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.classList.toggle('bi-eye');
        eyeIcon.classList.toggle('bi-eye-slash');
    });

    document.getElementById('loginForm').addEventListener('submit', function(e) {

    const login = document.querySelector('[name="login"]');
    const password = document.querySelector('[name="password"]');

    let isValid = true;

    // RESET styles
    login.classList.remove('is-invalid');
    password.classList.remove('is-invalid');

    // CHECK EMPTY FIELDS
    if (!login.value.trim()) {
        login.classList.add('is-invalid');
        isValid = false;
    }

    if (!password.value.trim()) {
        password.classList.add('is-invalid');
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();

        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please enter your username or password.'
        });

        return false;
    }
    });
</script>

<?php if (isset($_SESSION['error'])): ?>
<script>
    const errorType = "<?= $_SESSION['error']; ?>";

    if (errorType === "missing_fields") {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please enter your username and password.'
        });
    }

    if (errorType === "wrong_password") {
        Swal.fire({
            icon: 'error',
            title: 'Incorrect Password',
            text: 'The password you entered is incorrect.'
        });
    }

    if (errorType === "user_not_found") {
        Swal.fire({
            icon: 'error',
            title: 'User Not Found',
            text: 'No account found with that username'
        });
    }
</script>
<?php unset($_SESSION['error']); endif; ?>

<?php if (isset($_SESSION['login_success']) && $_SESSION['login_success'] === true): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Login Successful',
        text: 'Welcome <?= $_SESSION['first_name'] ?? '' ?>!',
        timer: 2000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'dashboard.php';
    });
</script>
<?php 
unset($_SESSION['login_success']);
endif; 
?>

<?php if (isset($_GET['logout']) && $_GET['logout'] == 1): ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'success',
        title: 'Logged Out',
        text: 'You have been successfully logged out.',
        timer: 1500,
        showConfirmButton: false
    });
});
</script>
<?php endif; ?>

</body>
</html>