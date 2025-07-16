// src/components/CrudModal.js
import React from "react";
import "../styles/modal.css";

export default function CrudModal({ visible, onClose, tipo, onSubmit }) {
  if (!visible) return null;

  const handleSubmit = (e) => {
    e.preventDefault();
    const form = new FormData(e.target);
    onSubmit(form); // Lo envías al padre
  };

  return (
    <div className="modal-overlay">
      <div className="modal-content">
        <h4>Crear {tipo}</h4>
        <form onSubmit={handleSubmit}>
          {tipo === "bus" && (
            <>
              <input name="accion" type="hidden" value="insertar_bus" />
              <input name="placa" placeholder="Placa" required />
              <input name="servicio" placeholder="Servicio" required />
              <input name="n_pisos" type="number" placeholder="Pisos" />
              <input name="n_asientos" type="number" placeholder="Asientos" />
              <input name="id_sede" type="number" placeholder="ID Sede" />
            </>
          )}

          {tipo === "ruta" && (
            <>
              <input name="accion" type="hidden" value="insertar_ruta" />
              <input name="duracion" placeholder="Duración" required />
              <input name="id_origen" placeholder="ID Origen" required />
              <input name="id_llegada" placeholder="ID Llegada" required />
            </>
          )}

          {tipo === "viaje" && (
            <>
              <input name="accion" type="hidden" value="insertar_viaje" />
              <input name="hora_salida" placeholder="Hora salida" required />
              <input name="hora_llegada" placeholder="Hora llegada" required />
              <input name="fecha_salida" placeholder="Fecha salida" required />
              <input name="fecha_llegada" placeholder="Fecha llegada" required />
              <input name="id_bus" placeholder="ID Bus" required />
              <input name="id_ruta" placeholder="ID Ruta" required />
              <input name="precio_piso1" placeholder="Precio Piso 1" required />
              <input name="precio_piso2" placeholder="Precio Piso 2" required />
            </>
          )}

          <div className="modal-buttons">
            <button type="submit">✅ Crear</button>
            <button type="button" onClick={onClose}>❌ Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  );
}
