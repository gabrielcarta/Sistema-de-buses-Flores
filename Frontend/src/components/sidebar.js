// src/components/Sidebar.js
import React from "react";
import "../styles/sidebarstyles.css"; // Agregaremos estilos personalizados

export default function Sidebar({ vistaActiva, onSelect, onCrudAction }) {
  return (
    <div className="sidebar">
      <h4>Admin</h4>
      <button
        className={vistaActiva === "buses" ? "active" : ""}
        onClick={() => onSelect("buses")}
      >
        🚌 Buses
      </button>
      {vistaActiva === "buses" && (
        <div className="crud-buttons">
          <button onClick={() => onCrudAction("crear")}>➕ Crear</button>
          <button onClick={() => onCrudAction("editar")}>✏️ Editar</button>
          <button onClick={() => onCrudAction("eliminar")}>🗑️ Eliminar</button>
        </div>
      )}

      <button
        className={vistaActiva === "rutas" ? "active" : ""}
        onClick={() => onSelect("rutas")}
      >
        📍 Rutas
      </button>
      {vistaActiva === "rutas" && (
        <div className="crud-buttons">
          <button onClick={() => onCrudAction("crear")}>➕ Crear</button>
          <button onClick={() => onCrudAction("editar")}>✏️ Editar</button>
          <button onClick={() => onCrudAction("eliminar")}>🗑️ Eliminar</button>
        </div>
      )}

      <button
        className={vistaActiva === "viajes" ? "active" : ""}
        onClick={() => onSelect("viajes")}
      >
        🗓️ Viajes
      </button>
      {vistaActiva === "viajes" && (
        <div className="crud-buttons">
          <button onClick={() => onCrudAction("crear")}>➕ Crear</button>
          <button onClick={() => onCrudAction("editar")}>✏️ Editar</button>
          <button onClick={() => onCrudAction("eliminar")}>🗑️ Eliminar</button>
        </div>
      )}
    </div>
  );
}

