// src/components/BusManager.js
import React, { useEffect, useState } from "react";
import axios from "axios";
import "../styles/adminpagestyle.css";

export default function BusManager({ onSelectItem, reloadFlag, crudAction }) {
  const [buses, setBuses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedBus, setSelectedBus] = useState(null);

  useEffect(() => {
    axios
      .get("http://localhost/proyectos/sistemabusesflores/backend/buses.php")
      .then((res) => setBuses(res.data))
      .catch((err) => console.error("❌ Error al cargar buses:", err))
      .finally(() => setLoading(false));
  }, [reloadFlag]);

  useEffect(() => {
    if (!crudAction) return;

    switch (crudAction.accion) {
      case "crear":
        const placa = prompt("Ingrese la placa del bus:", "XXX-000");
        const servicio = prompt("Ingrese el tipo de servicio:", "Economico");
        const n_pisos = parseInt(prompt("Ingrese n° de pisos:", "1"));
        const n_asientos = parseInt(prompt("Ingrese n° de asientos:", "40"));
        const id_sede = parseInt(prompt("Ingrese ID de sede:", "1"));

        if (!placa || !servicio || isNaN(n_pisos) || isNaN(n_asientos) || isNaN(id_sede)) {
          alert("❌ Datos inválidos para crear el bus.");
          return;
        }

        axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
          accion: "insertar_bus",
          placa,
          servicio,
          n_pisos,
          n_asientos,
          id_sede,
        })
        .then(() => {
          alert("✅ Bus creado.");
          crudAction.onDone();
          crudAction.onClear && crudAction.onClear();
        })
        .catch((err) => console.error("❌ Error al crear bus:", err));
        break;

      case "editar":
        if (!selectedBus) {
          alert("Selecciona un bus para editar.");
          return;
        }
        const nuevaPlaca = prompt("Editar Placa:", selectedBus.Placa);
        const nuevoServicio = prompt("Editar Servicio:", selectedBus.Servicio);

        axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
          accion: "actualizar_bus",
          Id_bus: selectedBus.Id_Bus,
          nuevaPlaca,
          nuevoServicio,
          N_pisos: selectedBus.N_Pisos,
          N_asientos: selectedBus.N_asientos,
          Id_Sede: selectedBus.Id_Sede,
        })
        .then(() => {
          alert("✏️ Bus editado.");
          crudAction.onDone();
          crudAction.onClear && crudAction.onClear();
        })
        .catch((err) => console.error("❌ Error al editar bus:", err));
        break;

      case "eliminar":
        if (!selectedBus) {
          alert("Selecciona un bus para eliminar.");
          return;
        }

        console.log("Bus a eliminar:", selectedBus);

        if (!window.confirm("¿Estás seguro de eliminar este bus?")) return;
          axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
            accion: "eliminar_bus",
            Id_Bus: selectedBus.Id_Bus,
          })
          .then(() => {
            alert("🗑️ Bus eliminado.");
            crudAction.onDone();
            crudAction.onClear && crudAction.onClear();
          })
          .catch((err) => console.error("❌ Error al eliminar bus:", err));
        break;

      default:
        break;
    }
  }, [crudAction]);


  const handleSelect = (bus) => {
    setSelectedBus(bus);
    onSelectItem(bus); // le avisas al padre cuál fue seleccionado
  };

  return (
    <div className="viaje-container">
      <h3>🚌 Gestión de Buses</h3>
      <div className="tabla-scroll">
        {loading ? (
          <p>Cargando buses...</p>
        ) : (
          <table className="table table-bordered table-hover text-center">
            <thead className="table-dark">
              <tr>
                <th>ID</th>
                <th>Placa</th>
                <th>Servicio</th>
                <th>Pisos</th>
                <th>Asientos</th>
                <th>Sede</th>
              </tr>
            </thead>
            <tbody>
              {buses.length > 0 ? (
                buses.map((bus) => (
                    <tr
                      key={bus.Id_Bus}
                      className={selectedBus?.Id_Bus === bus.Id_Bus ? "fila-seleccionada" : ""}
                      onClick={() => handleSelect(bus)}
                      style={{ cursor: "pointer" }}
                    >
                    <td>{bus.Id_Bus}</td>
                    <td>{bus.Placa}</td>
                    <td>{bus.Servicio}</td>
                    <td>{bus.N_Pisos}</td>
                    <td>{bus.N_asientos}</td>
                    <td>{bus.Id_Sede}</td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="6">No hay buses registrados.</td>
                </tr>
              )}
            </tbody>
          </table>
        )}
      </div>
    </div>
  );
}
