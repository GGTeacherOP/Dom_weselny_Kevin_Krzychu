// Obsługa gwiazdek przy dodawaniu opinii
const gwiazdki = document.querySelectorAll('#gwiazdki-ocena span');
const ocenaInput = document.getElementById('ocena');

gwiazdki.forEach(gwiazdka => {
    gwiazdka.addEventListener('click', function() {
        const wartosc = parseInt(this.getAttribute('data-value'));
        ocenaInput.value = wartosc;
        
        gwiazdki.forEach((g, index) => {
            if (index < wartosc) {
                g.classList.add('active');
                g.textContent = '★';
            } else {
                g.classList.remove('active');
                g.textContent = '☆';
            }
        });
    });
});

// Obsługa formularza
document.getElementById('form-opinia').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const autor = document.getElementById('autor').value;
    const ocena = parseInt(ocenaInput.value);
    const tresc = document.getElementById('tresc').value;
    const data = new Date().toLocaleDateString('pl-PL');
    
    if (ocena === 0) {
        alert('Proszę wystawić ocenę!');
        return;
    }
    
    // Tworzymy nową opinię
    const opiniaElement = document.createElement('div');
    opiniaElement.className = 'opinia';
    
    let gwiazdkiHTML = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= ocena) {
            gwiazdkiHTML += '★';
        } else {
            gwiazdkiHTML += '☆';
        }
    }
    
    opiniaElement.innerHTML = `
        <div class="opinia-header">
            <span class="autor">${autor}</span>
            <span class="data">${data}</span>
        </div>
        <div class="gwiazdki">${gwiazdkiHTML}</div>
        <p>${tresc}</p>
    `;
    
    // Dodajemy nową opinię na początek kontenera
    document.getElementById('opinie-container').prepend(opiniaElement);
    
    // Czyścimy formularz
    this.reset();
    ocenaInput.value = '0';
    gwiazdki.forEach(g => {
        g.classList.remove('active');
        g.textContent = '☆';
    });
    
    alert('Dziękujemy za dodanie opinii!');
});