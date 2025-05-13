 document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const answer = question.nextElementSibling;
                question.classList.toggle('active');
                answer.classList.toggle('show');
                
                // Zamykanie innych otwartych odpowiedzi
                document.querySelectorAll('.faq-question').forEach(q => {
                    if (q !== question && q.classList.contains('active')) {
                        q.classList.remove('active');
                        q.nextElementSibling.classList.remove('show');
                    }
                });
            });
        });
        
        // Skrypt dla formularza kontaktowego
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Tutaj można dodać kod do wysłania formularza
            alert('Dziękujemy za wiadomość! Skontaktujemy się z Tobą wkrótce.');
            this.reset();
        });