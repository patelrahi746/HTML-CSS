// register.js – client‑side validation for the registration form

document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('#registerForm');
  if (!form) return;

  const nameInput = form.querySelector('input[name="fullName"]');
  const emailInput = form.querySelector('input[name="email"]');
  const passwordInput = form.querySelector('input[name="password"]');
  const deptSelect = form.querySelector('select[name="department"]');

  const showError = (elem, message) => {
    let err = elem.parentNode.querySelector('.error');
    if (!err) {
      err = document.createElement('span');
      err.className = 'error';
      err.style.color = 'red';
      elem.parentNode.appendChild(err);
    }
    err.textContent = message;
  };

  const clearError = (elem) => {
    const err = elem.parentNode.querySelector('.error');
    if (err) err.textContent = '';
  };

  form.addEventListener('submit', function (e) {
    let valid = true;
    // Full Name validation – at least two words, letters only
    const nameVal = nameInput.value.trim();
    if (!/^[A-Za-z]+\s+[A-Za-z]+(\s+[A-Za-z]+)*$/.test(nameVal)) {
      showError(nameInput, 'Please enter your full name (first and last).');
      valid = false;
    } else {
      clearError(nameInput);
    }
    // Email validation
    const emailVal = emailInput.value.trim();
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(emailVal)) {
      showError(emailInput, 'Enter a valid email address.');
      valid = false;
    } else {
      clearError(emailInput);
    }
    // Password strength – at least 8 chars, 1 number, 1 uppercase, 1 special
    const pwdVal = passwordInput.value;
    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(pwdVal)) {
      showError(passwordInput, 'Password must be 8+ chars, include uppercase, number, and special char.');
      valid = false;
    } else {
      clearError(passwordInput);
    }
    // Department selection
    if (!deptSelect.value) {
      showError(deptSelect, 'Select a department.');
      valid = false;
    } else {
      clearError(deptSelect);
    }

    if (!valid) {
      e.preventDefault(); // stop form submission
    }
  });
});

  const form = document.querySelector('form');
  if (!form) return;

  const nameInput = form.querySelector('input[type="text"][placeholder*="full name");
  const emailInput = form.querySelector('input[type="email");
  const passwordInput = form.querySelector('input[type="password");
  const confirmInput = form.querySelector('input[type="password"][value=""); // placeholder not needed
  const deptSelect = form.querySelector('select');

  const showError = (elem, message) => {
    let err = elem.parentNode.querySelector('.error');
    if (!err) {
      err = document.createElement('span');
      err.className = 'error';
      err.style.color = 'red';
      elem.parentNode.appendChild(err);
    }
    err.textContent = message;
  };

  const clearError = (elem) => {
    const err = elem.parentNode.querySelector('.error');
    if (err) err.textContent = '';
  };

  form.addEventListener('submit', function (e) {
    let valid = true;
    // Name validation – at least two words, letters only
    const nameVal = nameInput.value.trim();
    if (!/^[A-Za-z]+\s+[A-Za-z]+(\s+[A-Za-z]+)*$/.test(nameVal)) {
      showError(nameInput, 'Please enter your full name (first and last).');
      valid = false;
    } else {
      clearError(nameInput);
    }
    // Email validation
    const emailVal = emailInput.value.trim();
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(emailVal)) {
      showError(emailInput, 'Enter a valid email address.');
      valid = false;
    } else {
      clearError(emailInput);
    }
    // Password strength – at least 8 chars, 1 number, 1 uppercase, 1 special
    const pwdVal = passwordInput.value;
    if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/.test(pwdVal)) {
      showError(passwordInput, 'Password must be 8+ chars, include uppercase, number, and special char.');
      valid = false;
    } else {
      clearError(passwordInput);
    }
    // Confirm password
    if (pwdVal !== confirmInput.value) {
      showError(confirmInput, 'Passwords do not match.');
      valid = false;
    } else {
      clearError(confirmInput);
    }
    // Department selection
    if (!deptSelect.value) {
      showError(deptSelect, 'Select a department.');
      valid = false;
    } else {
      clearError(deptSelect);
    }

    if (!valid) {
      e.preventDefault(); // stop form submission
    }
  });
});
