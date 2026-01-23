<?php
$title = "Login / Sign Up - EasyCart";
$base_path = "../";
$page = "auth";
$extra_css = "auth.css";
include '../includes/header.php';
include '../includes/users_data.php';

// Prepare data for JS validation
$users_json = json_encode($users);
?>

    <div class="auth-page-wrapper">
        <div class="auth-container">
            <!-- Left Side: Branding/Image -->
            <div class="auth-left">
                <div class="auth-content">
                    <h2>Welcome Back!</h2>
                    <p>Access your orders, wishlist, and recommendations.</p>
                </div>
            </div>

            <!-- Right Side: Forms -->
            <div class="auth-right">
                <div class="auth-toggle">
                    <button id="loginBtn" class="active" onclick="showLogin()">Login</button>
                    <button id="signupBtn" onclick="showSignup()">Sign Up</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="auth-form">
                    <h3>Sign In</h3>
                    <!-- Social Login Removed -->
                    
                    <div class="form-group">
                        <input type="email" id="loginEmail" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" required id="loginPass">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('loginPass', this)"></i>
                    </div>
                    <button type="submit" class="btn">Sign In</button>
                </form>

                <!-- Signup Form -->
                <form id="signupForm" class="auth-form" style="display: none;">
                    <h3>Create Account</h3>
                    <!-- Social Login Removed -->

                    <div class="form-group">
                        <input type="text" id="signupName" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="form-group">
                        <input type="email" id="signupEmail" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <input type="password" id="signupPass" name="password" placeholder="Password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signupPass', this)"></i>
                    </div>
                    <div class="form-group">
                        <input type="password" id="signupConfirmPass" placeholder="Confirm Password" required>
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('signupConfirmPass', this)"></i>
                    </div>
                    <button type="submit" class="btn">Sign Up</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const signupForm = document.getElementById('signupForm');
        const loginBtn = document.getElementById('loginBtn');
        const signupBtn = document.getElementById('signupBtn');
        const authLeftH2 = document.querySelector('.auth-left h2');
        const authLeftP = document.querySelector('.auth-left p');

        function showLogin() {
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
            loginBtn.classList.add('active');
            signupBtn.classList.remove('active');
            
            // Update Text on Left Side with animation
            updateLeftContent("Welcome Back!", "To keep connected with us please login with your personal info");
        }

        function showSignup() {
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
            signupBtn.classList.add('active');
            loginBtn.classList.remove('active');

            // Update Text on Left Side with animation
             updateLeftContent("Hello, Friend!", "Enter your personal details and start your journey with us");
        }

        function updateLeftContent(title, text) {
            authLeftH2.style.opacity = 0;
            authLeftP.style.opacity = 0;
            setTimeout(() => {
                authLeftH2.innerText = title;
                authLeftP.innerText = text;
                authLeftH2.style.opacity = 1;
                authLeftP.style.opacity = 1;
            }, 200);
        }

        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // --- Form Validations ---

        // Get data from PHP
        const usersData = <?php echo $users_json; ?>;

        function validateEmail(email) {
            return String(email)
                .toLowerCase()
                .match(
                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
        }

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const pass = document.getElementById('loginPass').value;

            if (!validateEmail(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            if (pass.length < 6) {
                alert('Password must be at least 6 characters long.');
                return;
            }

            // Check credentials
            const user = usersData.find(u => u.email.toLowerCase() === email.toLowerCase());

            if (!user) {
                alert('Email not found. Please sign up first.');
                return;
            }

            if (user.password !== pass) {
                alert('Incorrect password. Please try again.');
                return;
            }

            alert('Login successful! Welcome, ' + user.name);
            window.location.href = '../index.php';
        });

        signupForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('signupName').value;
            const email = document.getElementById('signupEmail').value;
            const pass = document.getElementById('signupPass').value;
            const confirmPass = document.getElementById('signupConfirmPass').value;

            if (name.trim().length < 2) {
                alert('Please enter your full name.');
                return;
            }

            if (!validateEmail(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            if (pass.length < 6) {
                alert('Password must be at least 6 characters long.');
                return;
            }

            if (pass !== confirmPass) {
                alert('Passwords do not match.');
                return;
            }

            // --- Server Side Signup (Dynamic Data) ---
            const formData = new FormData(signupForm);
            
            fetch('signup_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Signup successful! You can now login.');
                    location.reload(); // Reload to update the JS usersData array
                } else {
                    alert('Signup failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during signup.');
            });
        });
    </script>
<?php include '../includes/footer.php'; ?>
