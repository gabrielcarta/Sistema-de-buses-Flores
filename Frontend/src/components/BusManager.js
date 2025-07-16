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
          axios.post("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
            accion: "insertar_bus",
            placa: "XXX-000",
            servicio: "Economico",
            n_pisos: 1,
            n_asientos: 40,
            id_sede: 1,
          })
          .then(() => {
            alert("✅ Bus creado.");
            crudAction.onDone(); // para que se actualice el reloadFlag
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
            id_bus: selectedBus.Id_Bus,
            placa: nuevaPlaca,
            servicio: nuevoServicio,
            n_pisos: selectedBus.N_Pisos,
            n_asientos: selectedBus.N_asientos,
            id_sede: selectedBus.Id_Sede,
          })
          .then(() => {
            alert("✏️ Bus editado.");
            crudAction.onDone();
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
            id_bus: selectedBus.Id_Bus,
          })
          .then(() => {
            alert("🗑️ Bus eliminado.");
            crudAction.onDone();
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
