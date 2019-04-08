(function() {

    let passwordToggle = document.querySelector('#passwordToggle');
    let passwordInput = document.getElementsByName('sender_password')[0];

    passwordToggle.addEventListener('click', (e) => {
        passwordInput.type === 'text' ? passwordInput.type = 'password' : passwordInput.type = 'text';
    });

})();