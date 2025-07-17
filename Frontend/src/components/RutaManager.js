import React, { useEffect, useState } from "react";
import axios from "axios";

export default function RutaManager({ onSelectItem, reloadFlag, crudAction }) {
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
  }, [reloadFlag]);

  useEffect(() => {
    if (!crudAction) return;

    switch (crudAction.accion) {
      case "crear": {
        const duracion = prompt("Ingrese duración de la ruta:", "4h");
        const id_origen = parseInt(prompt("Ingrese ID de ciudad origen:", "1"));
        const id_llegada = parseInt(prompt("Ingrese ID de ciudad destino:", "2"));

        if (!duracion || isNaN(id_origen) || isNaN(id_llegada)) {
          alert("❌ Datos inválidos para crear la ruta.");
          return;
        }

        axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
          accion: "insertar_ruta",
          duracion,
          id_origen,
          id_llegada,
        })
        .then(() => {
          alert("✅ Ruta creada.");
          crudAction.onDone();
          crudAction.onClear && crudAction.onClear();
        })
        .catch((err) => console.error("❌ Error al crear ruta:", err));
        break;
      }

      case "editar": {
        if (!selectedRuta) {
          alert("Selecciona una ruta para editar.");
          return;
        }
        const nuevaDuracion = prompt("Editar duración:", selectedRuta.Duracion);
        const nuevoOrigen = parseInt(prompt("Editar ID de ciudad origen:", selectedRuta.Id_Origen));
        const nuevoDestino = parseInt(prompt("Editar ID de ciudad destino:", selectedRuta.Id_Llegada));

        axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
          accion: "actualizar_ruta",
          id_ruta: selectedRuta.Id_Ruta,
          duracion: nuevaDuracion,
          id_origen: nuevoOrigen,
          id_llegada: nuevoDestino,
        })
        .then(() => {
          alert("✏️ Ruta editada.");
          crudAction.onDone();
          crudAction.onClear && crudAction.onClear();
        })
        .catch((err) => console.error("❌ Error al editar ruta:", err));
        break;
      }

      case "eliminar": {
        if (!selectedRuta) {
          alert("Selecciona una ruta para eliminar.");
          return;
        }

        if (!window.confirm("¿Estás seguro de eliminar esta ruta?")) return;

        axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
          accion: "eliminar_ruta",
          id_ruta: selectedRuta.Id_Ruta,
        })
        .then(() => {
          alert("🗑️ Ruta eliminada.");
          crudAction.onDone();
          crudAction.onClear && crudAction.onClear();
        })
        .catch((err) => console.error("❌ Error al eliminar ruta:", err));
        break;
      }

      default:
        break;
    }
  }, [crudAction]);

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