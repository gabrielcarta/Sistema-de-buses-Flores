import React, { useEffect, useState } from "react";
import axios from "axios";

export default function ViajeManager({ onSelectItem }) {
  const [viajes, setViajes] = useState([]);
  const [total, setTotal] = useState(0);
  const [pagina, setPagina] = useState(1);
  const [selectedViaje, setSelectedViaje] = useState(null);
  const limite = 400;

useEffect(() => {
  axios
    .get(`http://localhost/proyectos/sistemabusesflores/backend/viajes.php?page=${pagina}&limit=${limite}`)
    .then(res => {
      setViajes(res.data.data);  // ✅ solo los viajes
      setTotal(res.data.total);  // ✅ total para paginación
    })
    .catch(err => console.error("Error al cargar viajes:", err));
}, [pagina]); // 👈 Dependencia: cuando cambia la página, vuelve a ejecutar el efecto

  const totalPaginas = Math.ceil(total / limite);

  const handleSelect = (viaje) => {
    setSelectedViaje(viaje);
    if (onSelectItem) onSelectItem(viaje);
  };

  return (
    <div className="viaje-container">
      <h3 className="text-center">🗓️ Gestión de Viajes</h3>
      <div className="tabla-scroll">
        <table className="table table-bordered table-hover text-center">
          <thead className="table-dark">
            <tr>
              <th>ID</th>
              <th>Origen</th>
              <th>Destino</th>
              <th>Fecha Salida</th>
              <th>Hora Salida</th>
              <th>Fecha Llegada</th>
              <th>Hora Llegada</th>
            </tr>
          </thead>
          <tbody>
            {viajes.length > 0 ? (
              viajes.map((v) => (
                <tr
                  key={v.Id_Viaje}
                  onClick={() => handleSelect(v)}
                  className={
                    selectedViaje?.Id_Viaje === v.Id_Viaje ? "fila-seleccionada" : ""
                  }
                  style={{ cursor: "pointer" }}
                >
                  <td>{v.Id_Viaje}</td>
                  <td>{v.Id_Origen}</td>
                  <td>{v.Id_Llegada}</td>
                  <td>{v.Fecha_salida}</td>
                  <td>{v.Hora_salida}</td>
                  <td>{v.Fecha_llegada}</td>
                  <td>{v.Hora_llegada}</td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="7">No hay viajes registrados.</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      <div className="pagination-controls">
        <button
          onClick={() => setPagina(pagina - 1)}
          disabled={pagina === 1}
          className="btn btn-secondary"
        >
          ← Anterior
        </button>
        <span>
          Página {pagina} de {totalPaginas}
        </span>
        <button
          onClick={() => setPagina(pagina + 1)}
          disabled={pagina === totalPaginas}
          className="btn btn-secondary"
        >
          Siguiente →
        </button>
      </div>
    </div>
  );
}
