import React, { useState } from 'react';
import '../styles/adminpagestyle.css';

export const AdminPage = () => {
  const [viajes, setViajes] = useState([
    { id: 1, origen: 'Lima', destino: 'Cusco', fecha: '2025-07-15' },
    { id: 2, origen: 'Arequipa', destino: 'Tacna', fecha: '2025-07-20' },
  ]);
  const [selectedId, setSelectedId] = useState(null);

  // Ejemplo de añadir viaje
  const handleAdd = () => {
    const origen = prompt('Origen:');
    const destino = prompt('Destino:');
    const fecha = prompt('Fecha (YYYY-MM-DD):');
    if (origen && destino && fecha) {
      setViajes([
        ...viajes,
        { id: Date.now(), origen, destino, fecha }
      ]);
    }
  };

  // Ejemplo de modificar viaje
  const handleEdit = () => {
    if (!selectedId) {
      alert('Selecciona un viaje para modificar.');
      return;
    }
    const viaje = viajes.find(v => v.id === selectedId);
    const origen = prompt('Nuevo origen:', viaje.origen);
    const destino = prompt('Nuevo destino:', viaje.destino);
    const fecha = prompt('Nueva fecha (YYYY-MM-DD):', viaje.fecha);
    if (origen && destino && fecha) {
      setViajes(viajes.map(v =>
        v.id === selectedId ? { ...v, origen, destino, fecha } : v
      ));
    }
  };

  // Ejemplo de eliminar viaje
  const handleDelete = () => {
    if (!selectedId) {
      alert('Selecciona un viaje para eliminar.');
      return;
    }
    if (window.confirm('¿Seguro que deseas eliminar este viaje?')) {
      setViajes(viajes.filter(v => v.id !== selectedId));
      setSelectedId(null);
    }
  };

  return (
    <div className="admin-container">
      <h2 className="admin-title">Panel de Administración</h2>
      <div className="admin-content">
        {/* Herramientas */}
        <div className="admin-tools">
          <button onClick={handleAdd}>Añadir viaje</button>
          <button onClick={handleEdit}>Modificar viaje</button>
          <button onClick={handleDelete}>Eliminar viaje</button>
        </div>
        {/* Viajes disponibles */}
        <div className="admin-viajes">
          <h3>Viajes disponibles</h3>
          <table>
            <thead>
              <tr>
                <th>Origen</th>
                <th>Destino</th>
                <th>Fecha</th>
                <th>ID</th>
              </tr>
            </thead>
            <tbody>
              {viajes.map(viaje => (
                <tr
                  key={viaje.id}
                  className={selectedId === viaje.id ? 'selected' : ''}
                  onClick={() => setSelectedId(viaje.id)}
                  style={{ cursor: 'pointer' }}
                >
                  <td>{viaje.origen}</td>
                  <td>{viaje.destino}</td>
                  <td>{viaje.fecha}</td>
                  <td>{viaje.id}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};