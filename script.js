function togglePassword() {
    const passwordInput = document.getElementById('password');
    const isPasswordVisible = passwordInput.type === 'text';
    
    // Toggle visibility
    passwordInput.type = isPasswordVisible ? 'password' : 'text';
    
    // Update the eye icon
    const toggleButton = document.getElementById('toggle-password');
    toggleButton.classList.toggle('fa-eye', isPasswordVisible);
    toggleButton.classList.toggle('fa-eye-slash', !isPasswordVisible);
}

function showAboutUs() {
    document.getElementById("main-content").style.display = "none"; // Hide main content
    document.getElementById("about-us").style.display = "block"; // Show About Us
    document.getElementById("overlay").style.display = "block"; // Show overlay
}

function hideAboutUs() {
    document.getElementById("main-content").style.display = "block"; // Show main content
    document.getElementById("about-us").style.display = "none"; // Hide About Us
    document.getElementById("overlay").style.display = "none"; // Hide overlay
}

function showContactUs() {
    document.getElementById("main-content").style.display = "none"; // Hide main
    document.getElementById("contact-us").style.display = "block"; // Show contact Us content
    document.getElementById("overlay").style.display = "block"; // Show overlay
}
function hideContactUs() {
    document.getElementById("main-content").style.display = "block"; // Show main content
    document.getElementById("contact-us").style.display = "none"; // Hide contact Us
    document.getElementById("overlay").style.display = "none"; // Hide overlay
}