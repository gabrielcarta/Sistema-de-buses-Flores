// src/components/BusManager.js
import React, { useEffect, useState } from "react";
import axios from "axios";
import "../styles/adminpagestyle.css";

export default function BusManager({onSelectItem}) {
  const [buses, setBuses] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedBus, setSelectedBus] = useState(null);

  useEffect(() => {
    axios
      .get("http://localhost/proyectos/sistemabusesflores/backend/buses.php")
      .then((res) => setBuses(res.data))
      .catch((err) => console.error("❌ Error al cargar buses:", err))
      .finally(() => setLoading(false));
  }, []);

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
