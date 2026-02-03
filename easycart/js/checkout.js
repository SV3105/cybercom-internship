// Checkout Page JavaScript

let currentStep = 1;
const formData = {
    address: {},
    payment: {}
};

// Step Navigation
function nextStep(step) {
    // Validate current step before proceeding
    if (step === 2 && !validateAddressForm()) {
        return;
    }
    if (step === 3 && !validatePaymentForm()) {
        return;
    }

    // Hide current step
    document.getElementById(`step-${currentStep}`).style.display = 'none';
    
    // Update progress
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.remove('active');
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.add('completed');
    
    // Show next step
    currentStep = step;
    document.getElementById(`step-${currentStep}`).style.display = 'block';
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.add('active');
    
    // If moving to review step, populate review data
    if (step === 3) {
        populateReview();
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function prevStep(step) {
    // Hide current step
    document.getElementById(`step-${currentStep}`).style.display = 'none';
    
    // Update progress
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.remove('active');
    
    // Show previous step
    currentStep = step;
    document.getElementById(`step-${currentStep}`).style.display = 'block';
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.add('active');
    document.querySelector(`.progress-step[data-step="${currentStep}"]`).classList.remove('completed');
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Form Validation
function validateAddressForm() {
    const form = document.getElementById('addressForm');
    const inputs = form.querySelectorAll('input[required]');
    
    for (let input of inputs) {
        if (!input.value.trim()) {
            alert(`Please fill in: ${input.previousElementSibling.textContent.replace('*', '').trim()}`);
            input.focus();
            return false;
        }
    }
    
    // Email validation
    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address');
        document.getElementById('email').focus();
        return false;
    }
    
    // Phone validation
    const phone = document.getElementById('phone').value;
    if (phone.length < 10) {
        alert('Please enter a valid phone number');
        document.getElementById('phone').focus();
        return false;
    }
    
    // Store address data
    formData.address = {
        firstname: document.getElementById('firstname').value,
        lastname: document.getElementById('lastname').value,
        email: email,
        phone: phone,
        street: document.getElementById('street').value,
        city: document.getElementById('city').value,
        postcode: document.getElementById('postcode').value
    };
    
    return true;
}

function validatePaymentForm() {
    const selectedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (!selectedPayment) {
        alert('Please select a payment method');
        return false;
    }

    const method = selectedPayment.value;
    const detailsContainer = document.getElementById(`details-${method}`);

    // If there is a details container for this method (COD might not have inputs)
    if (detailsContainer) {
        const inputs = detailsContainer.querySelectorAll('input[type="text"], input[type="password"], select');
        
        for (let input of inputs) {
            // Check if visible and empty
            if (input.offsetParent !== null && !input.value.trim()) {
                // Get label text for better error message
                let labelText = "Field";
                const label = input.closest('.form-group')?.querySelector('label');
                if (label) labelText = label.textContent;

                alert(`Please enter ${labelText} for ${method.toUpperCase()}`);
                input.focus();
                return false;
            }
        }
        
        // Specific format validation (Optional)
        if (method === 'card') {
            const cardNum = detailsContainer.querySelector('input[placeholder*="0000 0000"]').value.replace(/\s/g, '');
            if (cardNum.length < 16 || isNaN(cardNum)) {
                alert('Please enter a valid Card Number');
                return false;
            }
        }
    }
    
    // Capture Details for Storage
    let info = {};
    if (method === 'card') {
         info = {
             card_number: detailsContainer.querySelector('input[placeholder*="0000 0000"]').value,
             card_holder: detailsContainer.querySelector('input[placeholder*="Name"]').value,
             expiry: detailsContainer.querySelector('input[placeholder*="MM/YY"]').value
         };
    }
    
    formData.payment = {
        method: method,
        info: JSON.stringify(info)
    };
    
    return true;
}

// Toggle Payment Details
function togglePaymentDetails(radio) {
    // Hide all details
    document.querySelectorAll('.payment-details').forEach(el => {
        el.style.display = 'none';
        // Remove 'active' class from parent label if needed for styling
        el.closest('.payment-method-group').querySelector('.payment-option').classList.remove('selected'); 
    });

    // Show selected details
    const selectedDetails = document.getElementById(`details-${radio.value}`);
    if (selectedDetails) {
        selectedDetails.style.display = 'block';
        // Add active class for styling
        radio.closest('.payment-option').classList.add('selected');
    }
}

// Populate Review Step
function populateReview() {
    // Address Review
    const addressHTML = `
        <p><strong>${formData.address.firstname} ${formData.address.lastname}</strong></p>
        <p>${formData.address.email}</p>
        <p>${formData.address.phone}</p>
        <p>${formData.address.street}</p>
        <p>${formData.address.city}, ${formData.address.postcode}</p>
    `;
    document.getElementById('review-address').innerHTML = addressHTML;
    
    // Payment Review
    const paymentLabels = {
        'cod': 'Cash on Delivery',
        'card': 'Credit / Debit Card',
        'upi': 'UPI Payment',
        'netbanking': 'Net Banking'
    };
    const paymentHTML = `
        <p><strong>${paymentLabels[formData.payment.method]}</strong></p>
    `;
    document.getElementById('review-payment').innerHTML = paymentHTML;
}

// Place Order
function placeOrder() {
    const btn = document.getElementById('placeOrderBtn');
    const originalText = btn.innerHTML;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Prepare form data
    const orderData = new FormData();
    orderData.append('action', 'checkout');
    
    // Add address fields
    for (let key in formData.address) {
        orderData.append(key, formData.address[key]);
    }
    
    // Add payment method
    // Add payment method and info
    orderData.append('payment_method', formData.payment.method);
    if (formData.payment.info) {
        orderData.append('payment_info', formData.payment.info);
    }
    
    // Submit to backend
    fetch('cart.php', {
        method: 'POST',
        body: orderData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Order placed successfully!');
            
            // Redirect to orders page
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.href = 'orders.php';
            }
        } else {
            alert('Order failed: ' + (data.message || 'Unknown error'));
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page loaded');
    
    // Initialize Payment Details
    const checkedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (checkedPayment) {
        togglePaymentDetails(checkedPayment);
    }
});
