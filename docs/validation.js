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
  let valid = true;

  // Helpers
  function showError(input, errorId) {
    input.classList.add("is-invalid");
    document.getElementById(errorId).style.display = "block";
    valid = false;
  }
  function hideError(input, errorId) {
    input.classList.remove("is-invalid");
    document.getElementById(errorId).style.display = "none";
  }

  // Inputs
  const username = document.getElementById("username");
  const password = document.getElementById("password");
  const age = document.getElementById("age");
  const favoriteGame = document.getElementById("favorite_game");

  // Regex rules 
  const usernameRegex = /^[A-Za-z0-9_]{4,}$/;
  const passwordRegex = /^[A-Za-z0-9@]{8,}$/;

  // Username
  if (!usernameRegex.test(username.value.trim())) showError(username, "usernameError");
  else hideError(username, "usernameError");

  // Password
  if (!passwordRegex.test(password.value.trim())) showError(password, "passwordError");
  else hideError(password, "passwordError");

  // Age
  const ageVal = parseInt(age.value, 10);
  if (isNaN(ageVal) || ageVal < 5) showError(age, "ageError");
  else hideError(age, "ageError");

  // Favorite game
  if (favoriteGame.value === "") showError(favoriteGame, "gameError");
  else hideError(favoriteGame, "gameError");

  // Rating
  const ratingChecked = document.querySelector('input[name="rating"]:checked');
  if (!ratingChecked) {
    document.getElementById("ratingError").style.display = "block";
    valid = false;
  } else {
    document.getElementById("ratingError").style.display = "none";
  }

  const improvementsChecked = document.querySelectorAll('input[name="improvements[]"]:checked');
  if (improvementsChecked.length < 2) {
    document.getElementById("improvementsError").style.display = "block";
    valid = false;
  } else {
    document.getElementById("improvementsError").style.display = "none";
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

