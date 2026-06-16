<?php 
session_start(); 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3-Step Side-Eye Registration</title>

    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <link rel="stylesheet" href="alert.css">
    <link rel="stylesheet" href="ticktac.css">
<style>
    /* --- BASE STYLES --- */
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

    /* --- LOGIN/REGISTRATION CARD --- */
    .login-card {
        background: white;
        padding: 20px 40px 30px;
        border-radius: 35px;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        width: 100%;
        z-index: 2;
        text-align: center;
        min-height: 440px;
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

    /* --- FORM INPUTS --- */
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
        color: #6c757d; 
        padding: 0 15px;
    }

    .form-floating > .form-control {
        border: none;
        border-radius: 0;
        height: 50px;
        min-height: 50px;
        padding-top: 1.2rem;
        padding-bottom: 0.5rem;
    }

    .form-floating > .form-control:focus { box-shadow: none; }

    .form-floating > label {
        padding-top: 0.8rem;
        font-size: 0.9rem;
    }

    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        transform: scale(0.85) translateY(-0.7rem) translateX(0.15rem);
        color: #0d6efd;
    }

    /* --- BUTTONS --- */
    .btn-action, .btn-continue {
        border: none;
        padding: 16px;
        width: 100%;
        border-radius: 50px;
        font-weight: 600;
        margin-top: 16px;
        transition: transform 0.2s;
    }

    .btn-action:active, .btn-continue:active { transform: scale(0.98); }

    /* --- PROFILE PICTURE STEP --- */
    .profile-upload-container {
        position: relative;
        width: 191px;
        height: 191px;
        margin: 50px auto 20px;
    }

    .profile-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #f0f0f0;
        background: #f8f9fa;
    }

    .upload-btn-wrapper {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background: #e8ebf1;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border: 3px solid white;
        transition: transform 0.2s;
    }

    .upload-btn-wrapper:hover { transform: scale(1.1); }

    .upload-btn-wrapper input[type=file] {
        position: absolute;
        left: 0; top: 0; opacity: 0; width: 100%; height: 100%; cursor: pointer;
    }

    /* --- STEP TRANSITIONS --- */
    .reg-step { display: none; }
    .reg-step.active { display: block; }

    /* --- MODAL & CROPPER --- */
    .modal.fade .modal-dialog {
        transform: scale(0.8) translateY(30px);
        opacity: 0;
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
    }

    .modal.show .modal-dialog {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    .modal-backdrop.show {
        opacity: 0.5;
        backdrop-filter: blur(8px);
    }

    .img-container { max-height: 400px; overflow: hidden; background: #ffffff; }
    #imageToCrop { max-width: 100%; display: block; opacity: 0; transition: opacity 0.3s ease; }
    
    .cropper-view-box, .cropper-face { border-radius: 50%; }
    .modal-content { border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 30px 60px rgba(0,0,0,0.12); }
    .cropper-modal { background-color: #ffffff; opacity: 1; }

    /* --- FOOTER & MISC --- */
    .footer-links { margin-top: 20px; font-size: 0.85rem; }
    
    .custom-link { 
        color: #6c757d; 
        text-decoration: none; 
        font-weight: 600; 
        transition: color 0.2s;
    }
    .custom-link:hover { color: #212529; }

    .custom-links {
        color: #0080ff; 
        font-weight: 700;
        text-decoration: none; 
        transition: color 0.2s;             
    }
    .custom-links:hover { color: #0080ff79; }

    .title-cus {
        font-size: 0.65rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 5px;
        margin-top: 0px;
        letter-spacing: 0.5px;
        text-align: start;
    }
</style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <!-- Added ID here -->
        <form action="register_process2.php" method="POST" enctype="multipart/form-data" id="registerForm" novalidate>
        <img src="finallogo.png" alt="Logo" class="brand-logo" id="main-logo">
        <h5 class="text-center mb-2" id="form-title">Create Account</h5>
        
        <!-- STEP 1: Name & Email -->
        <div id="step-1" class="reg-step active">
        <div class="title-cus">User Info</div>
            <div class="d-flex gap-2 mb-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                    <div class="form-floating flex-grow-1">
                        <input type="text" name="first_name" id="regFirstName" class="form-control shy-input" placeholder="First Name" required>
                        <label>First Name</label>
                    </div>
                </div>
                <div class="input-group">
                    <div class="form-floating flex-grow-1">
                        <input type="text" name="last_name" id="regLastName" class="form-control shy-input" placeholder="Last Name" required>
                        <label>Last Name</label>
                    </div>
                </div>
            </div>
            <div class="input-group mb-4">
                <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                <div class="form-floating flex-grow-1">
                <input type="email" name="email" id="regEmail" class="form-control shy-input" placeholder="Email" required>
                    <label>Email Address</label>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-action" onclick="checkStep1()">Next</button>
        </div>

        <!-- STEP 2: Credentials -->
        <div id="step-2" class="reg-step">
            <div class="title-cus">Login Details</div>
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                <div class="form-floating flex-grow-1">
                <input type="text" name="username" id="regUser" class="form-control shy-input" placeholder="User" required>
                    <label>Username</label>
                </div>
            </div>
            <div class="input-group mb-4">
                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                <div class="form-floating flex-grow-1">
                <input type="password" name="password" id="regPass" class="form-control shy-input" placeholder="Pass" required>
                    <label>Password</label>
                </div>
                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                    <i class="bi bi-eye-slash" id="eyeIcon"></i>
                </span>
            </div>
            <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-action" onclick="goToStep(1)">Back</button>

            <button type="button" class="btn btn-primary btn-action" onclick="checkStep2()">Next</button>
            </div>
        </div>

        <!-- STEP 3: Photo -->
        <div id="step-3" class="reg-step">
            <div class="profile-upload-container">
                <img src="   https://cdn-icons-png.flaticon.com/512/9131/9131529.png" id="imagePreview" class="profile-preview">
                <div class="upload-btn-wrapper text-primary fs-3">
                    <i class="bi bi-camera-fill"></i>
                    <input type="file" name="profile_pic" id="profileInput" accept="image/*">
                </div>
                <input type="hidden" name="cropped_image" id="croppedImage">
            </div>
            <p class="text-muted small mb-4">Click the camera to upload a photo</p>
            <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-action" onclick="goToStep(2)">Back</button>
            <button type="submit" class="btn btn-primary btn-action">Complete</button>
            </div>
        </div>

        <div class="footer-links">
            <span class="text-muted" style="font-weight: 300;">Already a member?</span>
            <a href="loginv2.php" class="custom-links ms-1">Sign In</a>
        </div>
        </form>
    </div>
</div>

<!-- CROP MODAL -->
<div class="modal fade" id="cropModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <!-- <h6 class="modal-title fw-bold">Crop and resize</h6> -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 mt-3">
                <div class="img-container">
                    <img id="imageToCrop">
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="cropConfirm">Apply</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const watchers = document.getElementById('watchers');
    const pupils = document.querySelectorAll('.pupil');
    const cropModalEl = document.getElementById('cropModal');
    const cropModal = new bootstrap.Modal(cropModalEl);
    const imageToCrop = document.getElementById('imageToCrop');
    const mainLogo = document.getElementById('main-logo'); // Reference to logo
    let timer, cropper;

    function goToStep(step) {

    // VALIDATE CURRENT STEP FIRST
    if (!validateStep(step - 1)) {
        return; // stop if invalid
    }

    document.querySelectorAll('.reg-step').forEach(s => s.classList.remove('active'));
    document.getElementById(`step-${step}`).classList.add('active');

    const titles = ["Create Account", "Create Account", "Profile"];
    document.getElementById('form-title').innerText = titles[step - 1];

    if (step === 3) {
        mainLogo.classList.add('d-none');
    } else {
        mainLogo.classList.remove('d-none');
    }

    attachInputEvents();
    }

    function validateStep(stepNumber) {
    if (stepNumber === 0) return true; // first load skip

    const currentStep = document.getElementById(`step-${stepNumber}`);
    const inputs = currentStep.querySelectorAll('input[required]');

    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Missing Fields',
            text: 'Please fill in all required fields before proceeding.'
        });
    }

    return isValid;
    }

    document.getElementById('registerForm').addEventListener('submit', function(e) {

    const image = document.getElementById('croppedImage').value;

    // STOP submission FIRST
    if (!image) {
        e.preventDefault();
        e.stopPropagation(); // <-- important

        Swal.fire({
            icon: 'error',
            title: 'Profile Picture Required',
            text: 'Please upload your profile picture.'
        });

        return false; // <-- extra safety
    }
    });



    // --- CROPPER & MODAL LOGIC ---
    document.getElementById('profileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                imageToCrop.src = event.target.result;
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        cropModal.show();
                    });
                });
            };
            reader.readAsDataURL(file);
        }
    });

    cropModalEl.addEventListener('shown.bs.modal', () => {
        cropper = new Cropper(imageToCrop, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            guides: false,
            center: false,
            highlight: false,
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
        });
        imageToCrop.style.opacity = "1";
    });

    cropModalEl.addEventListener('hidden.bs.modal', () => {
        if(cropper) cropper.destroy();
        imageToCrop.style.opacity = "0";
        document.getElementById('profileInput').value = "";
    });

    document.getElementById('cropConfirm').addEventListener('click', () => {
    const canvas = cropper.getCroppedCanvas({
        width: 300,
        height: 300
    });

    // show preview
    const base64 = canvas.toDataURL('image/png');
    document.getElementById('imagePreview').src = base64;

    // store for backend
    document.getElementById('croppedImage').value = base64;

    cropModal.hide();
    });

    // --- SIDE-EYE ANIMATION ---
    function lookAt(tx, ty) {
        if (watchers.classList.contains('state-away')) return;
        pupils.forEach(p => {
            const rect = p.getBoundingClientRect();
            const px = rect.left + rect.width / 2;
            const py = rect.top + rect.height / 2;
            const angle = Math.atan2(ty - py, tx - px);
            p.style.transform = `translate(${Math.cos(angle)*10}px, ${Math.sin(angle)*10}px)`;
        });
    }

    function attachInputEvents() {
        document.querySelectorAll('.shy-input').forEach(input => {
            input.onfocus = () => watchers.className = 'watchers-group state-away';
            input.oninput = () => {
                watchers.className = 'watchers-group state-peeking';
                const r = input.getBoundingClientRect();
                lookAt(r.left + r.width/2, r.top + r.height/2);
                clearTimeout(timer);
                timer = setTimeout(() => {
                    if(document.activeElement === input) watchers.className = 'watchers-group state-away';
                }, 400); 
            };
            input.onblur = () => watchers.className = 'watchers-group';
        });
    }

    document.addEventListener('mousemove', (e) => lookAt(e.clientX, e.clientY));

    document.getElementById('togglePassword').onclick = function() {
        const pass = document.getElementById('regPass');
        const icon = document.getElementById('eyeIcon');
        const isPass = pass.type === 'password';
        pass.type = isPass ? 'text' : 'password';
        icon.className = isPass ? 'bi bi-eye' : 'bi bi-eye-slash';
    };

    attachInputEvents();

    // =========================
    // EMAIL VALIDATION + CHECK
    // =========================
    async function validateEmail() {
        const emailInput = document.getElementById('regEmail');
        const email = emailInput.value.trim();

        // EMAIL FORMAT CHECK
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailPattern.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.'
            });
            return false;
        }

        // CHECK IF EMAIL EXISTS (AJAX)
        try {
            const res = await fetch('check_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'email=' + encodeURIComponent(email)
            });

            const data = await res.json();

            if (data.exists) {
                Swal.fire({
                    icon: 'error',
                    title: 'Email Already Exists',
                    text: 'Please use a different email.'
                });
                return false;
            }

            return true;

        } catch (error) {
            console.error(error);
            return false;
        }
    }

    async function checkStep1() {

    // basic required validation first
    if (!validateStep(1)) return;

    // email validation + uniqueness
    const validEmail = await validateEmail();

    if (validEmail) {
        goToStep(2);
    }
    }

  // =========================
// USERNAME VALIDATION + CHECK
// =========================
async function validateUsername() {
    const usernameInput = document.getElementById('regUser');
    const username = usernameInput.value.trim();

    // MIN LENGTH CHECK
    if (username.length < 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Invalid Username',
            text: 'Username must be at least 5 characters.'
        });
        return false;
    }

    try {
        const res = await fetch('check_username2.php', { // ✅ FIXED HERE
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'username=' + encodeURIComponent(username)
        });

        const data = await res.json();

        if (data.exists) {
            Swal.fire({
                icon: 'error',
                title: 'Username Already Taken',
                text: 'Please choose another username.'
            });
            return false;
        }

        return true;

    } catch (error) {
        console.error('Username check failed:', error);
        return false;
    }
    }

    // =========================
    // PASSWORD VALIDATION (MIN 8 CHARS ONLY)
    // =========================
    function validatePassword() {
        const passwordInput = document.getElementById('regPass');
        const password = passwordInput.value.trim();

        if (password.length < 8) {
            Swal.fire({
                icon: 'warning',
                title: 'Weak Password',
                text: 'Password must be at least 8 characters long.'
            });
            return false;
        }

        return true;
    }

    async function checkStep2() {

    // required fields first
    if (!validateStep(2)) return;

    // username check
    const validUsername = await validateUsername();
    if (!validUsername) return;

    // password check
    const validPassword = validatePassword();
    if (!validPassword) return;

    // proceed
    goToStep(3);
    }
</script>

<?php
if (isset($_SESSION['success']) && $_SESSION['success'] === "registered") {
    echo "
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Registration Successful',
            text: 'You have successfully registered!',
            confirmButtonText: 'Continue'
        }).then(() => {
            window.location.href = 'loginv2.php';
        });
    </script>
    ";
    unset($_SESSION['success']);
}

?>

</body>
</html>