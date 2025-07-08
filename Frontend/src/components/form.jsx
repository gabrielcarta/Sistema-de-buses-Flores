import { useEffect, useState } from 'react';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { useNavigate } from "react-router-dom";

function FormularioViaje() {
  const [cities, setCities] = useState([]);
  const [origin, setOrigin] = useState("");
  const [destination, setDestination] = useState("");
  const [datedeparture, setDatedeparture] = useState("");
  const [datereturn, setDatereturn] = useState("");

  const navigate = useNavigate();

  // Obtener las ciudades desde PHP al cargar
  useEffect(() => {
    fetch('http://localhost/proyectos/sistemabuses/backend/form-trip.php')
      .then(response => response.json())
      .then(data => {
        setCities(data);
      })
      .catch(error => {
        console.error('Error al obtener ciudades:', error);
      });
  }, []);

  const handleSubmit = (e) => {
    e.preventDefault(); // evita que se recargue la página

    // Redirige a "/resultados" pasando los datos
    navigate('/trippage', {
      state: { origin, destination, datedeparture, datereturn },
    });
  };

  return (
    <form onSubmit={handleSubmit}>
        <label>
            📍 Origen
            <select value={origin} onChange={e => setOrigin(e.target.value)} required>
              <option value="">Seleccione ciudad</option>
              {cities.map((cities, i) => (
              <option key={i} value={cities}>{cities}</option>
              ))}
            </select>
        </label>
      
        <label>
            📍Destino
            <select value={destination} onChange={e => setDestination(e.target.value)} required>
              <option value="">Seleccione ciudad</option>
              {cities.map((cities, i) => (
              <option key={i} value={cities}>{cities}</option>
              ))}
            </select>
        </label>

        <label>
            📅 Salida
            <input type="date" value={datedeparture} onChange={e => setDatedeparture(e.target.value)} required />
        </label>

        <label>
            📅 Retorno (Opcinal)
            <input type="date" value={datereturn} onChange={e => setDatereturn(e.target.value)}/>
        </label>

          <button type="submit">Buscar</button>
    </form>
  );
}

export default FormularioViaje;