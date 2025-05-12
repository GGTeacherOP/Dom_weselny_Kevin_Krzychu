 // Funkcje do obsługi modala
 function openModal(src) {
    const modal = document.getElementById('myModal');
    const modalImg = document.getElementById('img01');
    
    modal.style.display = "block";
    modalImg.src = src;
}

function closeModal() {
    document.getElementById('myModal').style.display = "none";
}

// Zamknij modal po kliknięciu poza obrazem
window.onclick = function(event) {
    const modal = document.getElementById('myModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}