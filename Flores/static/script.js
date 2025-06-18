document.getElementById('bookingForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const origin = document.getElementById('origin').value;
  const destination = document.getElementById('destination').value;
  const date = document.getElementById('date').value;

  alert(`Buscando pasajes de ${origin} a ${destination} para el ${date}`);
});
