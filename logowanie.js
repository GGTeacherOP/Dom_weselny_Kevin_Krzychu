document.addEventListener('DOMContentLoaded', function() {
            // Przełączanie między logowaniem a rejestracją
            const showRegister = document.getElementById('show-register');
            const showLogin = document.getElementById('show-login');
            const loginBox = document.getElementById('login-box');
            const registerBox = document.getElementById('register-box');

            showRegister.addEventListener('click', function(e) {
                e.preventDefault();
                loginBox.style.display = 'none';
                registerBox.style.display = 'block';
            });

            showLogin.addEventListener('click', function(e) {
                e.preventDefault();
                registerBox.style.display = 'none';
                loginBox.style.display = 'block';
            });

            // Obsługa formularza logowania
            const loginForm = document.getElementById('login-form');
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                
                // Wysłanie danych logowania do serwera
                fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&action=login`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Zalogowano pomyślnie - zapisz dane sesji i przekieruj
                        sessionStorage.setItem('loggedIn', 'true');
                        sessionStorage.setItem('userId', data.userId);
                        sessionStorage.setItem('userRole', data.role);
                        sessionStorage.setItem('userName', data.name);
                        
                        // Przekierowanie w zależności od roli
                        if (data.role === 'admin' || data.role === 'manager') {
                            window.location.href = 'panel_admina.html';
                        } else {
                            window.location.href = 'moje_konto.html';
                        }
                    } else {
                        // Błąd logowania
                        showMessage(loginBox, data.message, 'error');
                    }
                })
                .catch(error => {
                    showMessage(loginBox, 'Wystąpił błąd podczas logowania', 'error');
                    console.error('Error:', error);
                });
            });

            // Obsługa formularza rejestracji
            const registerForm = document.getElementById('register-form');
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const email = document.getElementById('register-email').value;
                const password = document.getElementById('register-password').value;
                const confirmPassword = document.getElementById('register-confirm').value;
                const firstName = document.getElementById('register-firstname').value;
                const lastName = document.getElementById('register-lastname').value;
                const phone = document.getElementById('register-phone').value;
                const address = document.getElementById('register-address').value;
                
                // Walidacja hasła
                if (password !== confirmPassword) {
                    showMessage(registerBox, 'Hasła nie są identyczne', 'error');
                    return;
                }
                
                // Wysłanie danych rejestracji do serwera
                fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&firstname=${encodeURIComponent(firstName)}&lastname=${encodeURIComponent(lastName)}&phone=${encodeURIComponent(phone)}&address=${encodeURIComponent(address)}&action=register`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(registerBox, data.message, 'success');
                        // Automatyczne logowanie po rejestracji
                        setTimeout(() => {
                            document.getElementById('login-email').value = email;
                            document.getElementById('login-password').value = password;
                            loginForm.dispatchEvent(new Event('submit'));
                        }, 1500);
                    } else {
                        showMessage(registerBox, data.message, 'error');
                    }
                })
                .catch(error => {
                    showMessage(registerBox, 'Wystąpił błąd podczas rejestracji', 'error');
                    console.error('Error:', error);
                });
            });

            // Funkcja wyświetlająca komunikaty
            function showMessage(parentElement, message, type) {
                // Usuń istniejące komunikaty
                const existingMessages = parentElement.querySelectorAll('.message');
                existingMessages.forEach(msg => msg.remove());
                
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${type}`;
                messageDiv.textContent = message;
                parentElement.insertBefore(messageDiv, parentElement.firstChild);
            }
        });

        // Funkcja sprawdzająca czy użytkownik jest zalogowany (można użyć w innych plikach)
        function checkLoggedIn() {
            return sessionStorage.getItem('loggedIn') === 'true';
        }

        // Funkcja wylogowania
        function logout() {
            sessionStorage.removeItem('loggedIn');
            sessionStorage.removeItem('userId');
            sessionStorage.removeItem('userRole');
            sessionStorage.removeItem('userName');
            window.location.href = 'index.html';
        }