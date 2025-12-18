// ================================
// Shared Helper functions that are used by both forms (in signup.html and questionarrie.html)
// ================================

// Hide all errors and remove invalid borders inside a form
function clearErrors(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  // hide all error-message blocks
  form.querySelectorAll(".error-message").forEach(err => {
    err.style.display = "none";
  });

  // remove red borders
  form.querySelectorAll(".is-invalid").forEach(input => {
    input.classList.remove("is-invalid");
  });
}

// Mark one field invalid and show its error message
function showFieldError(fieldId, errorId) {
  const field = document.getElementById(fieldId);
  const error = document.getElementById(errorId);

  if (field) field.classList.add("is-invalid");
  if (error) error.style.display = "block";
}

// ================================
// Form 1: Questionnaire Validation
// ================================
function validateQuestionnaireForm() {
  clearErrors("feedbackForm");
  let valid = true;

  const username = document.getElementById("username").value.trim();
  const password = document.getElementById("password").value;
  const ageStr = document.getElementById("age").value;
  const favoriteGame = document.getElementById("favorite_game").value;

  const rating = document.querySelector('input[name="rating"]:checked');
  const improvements = document.querySelectorAll('input[name="improvements"]:checked');

  // Required + Regex: username (letters/numbers/_ , min 4)
  const usernamePattern = /^[A-Za-z0-9_]{4,}$/;
  if (!usernamePattern.test(username)) {
    showFieldError("username", "usernameError");
    valid = false;
  }

  // Required + Regex: password (letters/numbers/@, min 8)
  const passwordPattern = /^[A-Za-z0-9@]{8,}$/;
  if (!passwordPattern.test(password)) {
    showFieldError("password", "passwordError");
    valid = false;
  }

  // Range: age must be 5+
  const age = parseInt(ageStr, 10);
  if (isNaN(age) || age < 5 || age > 110) {
    showFieldError("age", "ageError");
    valid = false;
  }

  // Required: dropdown selection
  if (favoriteGame === "") {
    showFieldError("favorite_game", "gameError");
    valid = false;
  }

  // Required: rating radio
  if (!rating) {
    document.getElementById("ratingError").style.display = "block";
    valid = false;
  }

  // Logical: at least 2 improvements
  if (improvements.length < 2) {
    document.getElementById("improvementsError").style.display = "block";
    valid = false;
  }

  return valid;
}

// ================================
// Form 2: Signup Validation 
// ================================
function validateSignupForm() {
  clearErrors("signupForm");
  let valid = true;

  const fullName = document.getElementById("fullname").value.trim();
  const username = document.getElementById("new_username").value.trim();
  const email = document.getElementById("new_email").value.trim();
  const password = document.getElementById("new_password").value;
  const confirm = document.getElementById("confirm_password").value;
  const favGame = document.getElementById("favorite_game").value;
  const termsChecked = document.getElementById("terms").checked;

  const fullNamePattern = /^[A-Za-z ]{2,40}$/;
  if (!fullNamePattern.test(fullName)) { showFieldError("fullname", "fullNameError"); valid = false; }

  const usernamePattern = /^[A-Za-z0-9_]{4,}$/;
  if (!usernamePattern.test(username)) { showFieldError("new_username", "newUsernameError"); valid = false; }

  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) { showFieldError("new_email", "newEmailError"); valid = false; }

  const passwordPattern = /^[A-Za-z0-9@]{8,}$/;
  if (!passwordPattern.test(password)) { showFieldError("new_password", "newPasswordError"); valid = false; }

  if (confirm !== password || confirm.length === 0) {
    showFieldError("confirm_password", "confirmPasswordError");
    valid = false;
  }

  if (favGame === "") { showFieldError("favorite_game", "favGameError"); valid = false; }

  if (!termsChecked) {
    document.getElementById("termsError").style.display = "block";
    valid = false;
  }

  return valid;
}

