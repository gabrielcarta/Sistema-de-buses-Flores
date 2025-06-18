// script.js

// Envuelve TODO el código que interactúa con el DOM dentro de este evento.
document.addEventListener('DOMContentLoaded', function() {

    // 1. Lógica para el envío del formulario (originalmente estaba fuera del DOMContentLoaded)
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) { // Siempre es buena práctica verificar si el elemento existe
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita que el formulario se envíe de forma predeterminada

            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const date = document.getElementById('date').value;

            // En lugar de alert, podrías mostrar un mensaje en un div o modal en el futuro.
            alert(`Buscando pasajes de ${origin} a ${destination} para el ${date}`);
        });
    } else {
        console.error("Error: El formulario con id 'bookingForm' no se encontró.");
    }


    // 2. Lógica para cargar las ciudades (esta ya estaba dentro del DOMContentLoaded)
    fetch('../templates/get_cities.php') // Asegúrate de que esta ruta sea correcta
        .then(response => {
            if (!response.ok) {
                // Lanza un error si la respuesta no es 200 OK (ej. 404, 500)
                throw new Error('La respuesta de red no fue exitosa: ' + response.statusText);
            }
            return response.json(); // Parsea la respuesta como JSON
        })
        .then(cities => {
            const originSelect = document.getElementById('origin');
            const destinationSelect = document.getElementById('destination');

            // Asegúrate de que los elementos select existen antes de intentar usarlos
            if (originSelect && destinationSelect) {
                cities.forEach(city => {
                    const option1 = document.createElement('option');
                    option1.value = city;
                    option1.textContent = city;
                    originSelect.appendChild(option1);

                    const option2 = document.createElement('option');
                    option2.value = city;
                    option2.textContent = city;
                    destinationSelect.appendChild(option2);
                });
            } else {
                console.error("Error: Los elementos select 'origin' o 'destination' no se encontraron.");
            }
        })
        .catch(error => {
            console.error('Hubo un problema al obtener las ciudades:', error);
            // Podrías mostrar un mensaje de error al usuario en la UI aquí
        });
});