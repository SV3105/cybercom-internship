function showLogin() {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    
    if (loginForm) loginForm.style.display = 'block';
    if (signupForm) signupForm.style.display = 'none';
    if (loginBtn) loginBtn.classList.add('active');
    if (signupBtn) signupBtn.classList.remove('active');
    
    updateLeftContent("Welcome Back!", "To keep connected with us please login with your personal info");
}

function showSignup() {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');

    if (loginForm) loginForm.style.display = 'none';
    if (signupForm) signupForm.style.display = 'block';
    if (signupBtn) signupBtn.classList.add('active');
    if (loginBtn) loginBtn.classList.remove('active');

    updateLeftContent("Hello, Friend!", "Enter your personal details and start your journey with us");
}

function updateLeftContent(title, text) {
    const authLeftH2 = document.querySelector('.auth-left h2');
    const authLeftP = document.querySelector('.auth-left p');
    
    if (authLeftH2 && authLeftP) {
        authLeftH2.style.opacity = 0;
        authLeftP.style.opacity = 0;
        setTimeout(() => {
            authLeftH2.innerText = title;
            authLeftP.innerText = text;
            authLeftH2.style.opacity = 1;
            authLeftP.style.opacity = 1;
        }, 200);
    }
}

function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input) {
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
}

function validateEmail(email) {
    return String(email)
        .toLowerCase()
        .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        );
}

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    if (loginForm) {
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

            // Server-side login
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', pass);

            fetch('loginhandler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect based on previous page or default to index
                    window.location.href = '../index.php';
                } else {
                    alert(data.message || 'Login failed.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during login.');
            });
        });
    }

    if (signupForm) {
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

            const formData = new FormData(signupForm);
            
            fetch('signuphandler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Signup successful! You can now login.');
                    location.reload();
                } else {
                    alert('Signup failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred during signup.');
            });
        });
    }
});
