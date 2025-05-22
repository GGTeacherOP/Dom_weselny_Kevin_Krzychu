document.addEventListener('DOMContentLoaded', function() {
    // Inicjalizacja gwiazdek
    const stars = document.querySelectorAll('#gwiazdki-ocena span');
    const ratingInput = document.getElementById('ocena');

    // Sprawdzamy, czy gwiazdki i pole oceny istnieją na stronie
    if (stars.length > 0 && ratingInput) {
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                ratingInput.value = value;

                // Aktualizacja wyglądu gwiazdek
                stars.forEach((s, idx) => {
                    s.textContent = idx < value ? '★' : '☆';
                    s.style.color = idx < value ? '#FFA000' : '#FFE7B3';
                });
            });
        });
    }

    // Obsługa formularza z AJAX
    const opinionForm = document.getElementById('form-opinia');

    if (opinionForm) { // Upewnij się, że formularz istnieje zanim dodasz nasłuchiwacz
        opinionForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Zapobiegaj domyślnej wysyłce formularza

            const submitBtn = this.querySelector('button[type="submit"]');
            const formData = new FormData(this);
            formData.append('submit_opinion', '1'); // Dodaj flagę submit_opinion

            // Wizualna informacja o wysyłaniu
            if (submitBtn) { // Upewnij się, że przycisk istnieje
                // Zapisz oryginalny tekst przycisku
                const originalBtnText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="btn-loading"><i class="fas fa-spinner fa-spin"></i> Wysyłanie...</span>';

                fetch('opinie.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    // Nie parsuj odpowiedzi jako JSON, bo PHP zwraca cały HTML
                    return response.text();
                })
                .then(() => {
                    // Przeładowanie strony po dodaniu opinii
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Wystąpił błąd podczas wysyłania opinii: ' + error.message);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalBtnText; // Przywróć oryginalny tekst
                    }
                });
            }
        });
    }
});