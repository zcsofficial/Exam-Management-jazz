document.getElementById('register-form').addEventListener('submit', function(event) {
    var password = document.querySelector('input[name="password"]').value;
    var confirmPassword = document.querySelector('input[name="confirm-password"]').value;
    
    if (password !== confirmPassword) {
        event.preventDefault();
        alert('Passwords do not match.');
    }
});
