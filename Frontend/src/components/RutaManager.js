import React, { useEffect, useState } from "react";

export default function RutaManager({ onSelectItem }) {
  const [rutas, setRutas] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedRuta, setSelectedRuta] = useState(null);

  useEffect(() => {
    fetch("http://localhost/proyectos/sistemabusesflores/backend/rutas.php")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data)) {
          setRutas(data);
        } else {
          setRutas([]);
        }
      })
      .catch((err) => console.error("❌ Error cargando rutas:", err))
      .finally(() => setLoading(false));
  }, []);

  const handleSelect = (ruta) => {
    setSelectedRuta(ruta);
    if (onSelectItem) onSelectItem(ruta);
  };

  return (
    <div className="viaje-container">
      <h3 className="text-center">📍 Gestión de Rutas</h3>
      <div className="tabla-scroll">
        {loading ? (
          <p>Cargando rutas...</p>
        ) : (
          <table className="table table-bordered table-hover text-center">
            <thead className="table-dark">
              <tr>
                <th>ID</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Duración</th>
              </tr>
            </thead>
            <tbody>
              {rutas.length > 0 ? (
                rutas.map((r) => (
                  <tr
                    key={r.Id_Ruta}
                    onClick={() => handleSelect(r)}
                    className={
                      selectedRuta?.Id_Ruta === r.Id_Ruta ? "fila-seleccionada" : ""
                    }
                    style={{ cursor: "pointer" }}
                  >
                    <td>{r.Id_Ruta}</td>
                    <td>{r.Origen}</td>
                    <td>{r.Llegada}</td>
                    <td>{r.Duracion}</td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="4">No hay rutas registradas.</td>
                </tr>
              )}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
