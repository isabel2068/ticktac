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

        .login-container {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 420px;
        }

        /* --- CHARACTER ANIMATION STYLES --- */
        .watchers-group { 
            display: flex; 
            gap: 10px; 
            margin-bottom: -38px; 
            z-index: 0; 
        }

        .funny-person {
            width: 140px;
            height: 130px;
            background: #ffe0bd;
            border: 4px solid #212529;
            border-radius: 70px 70px 10px 10px;
            position: relative;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .hair-messy {
            width: 100%;
            height: 45%;
            background: #212529;
            position: absolute;
            top: 0;
            border-radius: 65px 65px 0 0;
        }
        
        .hair-strands {
            position: absolute;
            width: 20px;
            height: 2px;
            background: #212529;
            left: -10px;
            top: 50px;
            box-shadow: 0 10px 0 #212529, 155px -10px 0 #212529;
        }

        .eye-row {
            position: absolute;
            top: 55px;
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .eye-white {
            width: 38px;
            height: 38px;
            background: white;
            border: 3px solid #212529;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .pupil {
            width: 18px;
            height: 18px;
            background: #212529;
            border-radius: 50%;
            position: relative;
            transition: transform 0.1s cubic-bezier(0.25, 1, 0.5, 1);
        }
        
        .pupil::after {
            content: '';
            position: absolute;
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            top: 3px;
            left: 3px;
        }

        .mouth {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 4px;
            background: #212529;
            border-radius: 10px;
        }

        .state-away .pupil { 
            transform: translate(-10px, -5px) !important; 
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
            background-color: transparent;
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
    <!-- CHARACTERS -->
    <div id="watchers" class="watchers-group">
        <div class="funny-person">
            <div class="hair-messy"></div>
            <div class="hair-strands"></div>
            <div class="eye-row">
                <div class="eye-white"><div class="pupil"></div></div>
                <div class="eye-white"><div class="pupil"></div></div>
            </div>
            <div class="mouth" style="width: 15px; height: 15px; border-radius: 50%; border: 3px solid #212529; background: transparent;"></div>
        </div>

        <div class="funny-person" style="background: #e0ac69;">
            <div class="hair-messy" style="background: #4a3728; border-radius: 100px 10px 0 0;"></div>
            <div class="eye-row">
                <div class="eye-white"><div class="pupil"></div></div>
                <div class="eye-white"><div class="pupil"></div></div>
            </div>
            <div class="mouth" style="transform: translateX(-50%) rotate(10deg); width: 40px;"></div>
        </div>
    </div>

    <!-- LOGIN CARD -->
    <div class="login-card">
        <img src="finallogo.png" alt="Logo" class="brand-logo">
        <h5 class="text-center mb-0">Sign In</h5>
        
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

<?php if (isset($_SESSION['logout_success'])): ?>
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
<?php unset($_SESSION['logout_success']); endif; ?>

</body>
</html>