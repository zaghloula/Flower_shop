document.addEventListener('DOMContentLoaded', function() {
    // Get the navbar element
    var navbar = document.querySelector('.navbar');
    
    // Get the search button
    var searchButton = document.getElementById('search-button');
    
    // Add click event listener to the search button
    searchButton.addEventListener('click', function() {
        // Toggle the 'active' class on the navbar
        navbar.classList.toggle('active');
    });
});
function navigateTo(section) {
    window.location.hash = section;
}
document.addEventListener("DOMContentLoaded", function() {
    // Function to handle form submission
    function handleSubmit(event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Here you can add code to handle form submission, such as sending the message
        
        // For demonstration purposes, let's just log a message
        console.log("Message sent!");
    }
    
    // Get the submit button element
    var submitBtn = document.querySelector(".submit-btn");
    
    // Add event listener for form submission
    submitBtn.addEventListener("click", handleSubmit);
});
// order.js
document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");

    form.addEventListener("submit", function(event) {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const amount = document.getElementById("amount").value.trim();
        const product = document.getElementById("product").value.trim();

        if (!name || !email || !amount || !product) {
            event.preventDefault();
            alert("Please fill in all fields.");
        }
    });
});
