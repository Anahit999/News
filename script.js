// DOM Elements
const registerBtn = document.getElementById('registerBtn');
const loginBtn = document.getElementById('loginBtn');
const logoutBtn = document.getElementById('logoutBtn');
const addNewsBtn = document.getElementById('addNewsBtn');
const addNewsForm = document.getElementById('addNewsForm');
const newsForm = document.getElementById('newsForm');
const registerForm = document.getElementById('registerForm');
const loginForm = document.getElementById('loginForm');
const newsArticles = document.getElementById('newsArticles');
const categoryList = document.getElementById('categoryList');
const userInfo = document.querySelector('.user-info');
const usernameSpan = document.querySelector('.username');
const navLinks = document.querySelectorAll('.nav-links a');

// Check if user is logged in
function checkLoginStatus() {
    fetch('check_login.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedIn) {
                updateAuthUI(true, data.username);
            } else {
                updateAuthUI(false);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Update authentication UI
function updateAuthUI(isLoggedIn, username = '') {
    if (isLoggedIn) {
        registerBtn.style.display = 'none';
        loginBtn.style.display = 'none';
        userInfo.style.display = 'flex';
        usernameSpan.textContent = username;
        logoutBtn.style.display = 'block';
        addNewsBtn.style.display = 'block';
    } else {
        registerBtn.style.display = 'block';
        loginBtn.style.display = 'block';
        userInfo.style.display = 'none';
        addNewsBtn.style.display = 'none';
        addNewsForm.style.display = 'none';
    }
}

// Show message
function showMessage(elementId, message, type = 'success') {
    const element = document.getElementById(element
