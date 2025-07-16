import React, { useEffect, useState } from "react";
import "../styles/buscard.css";

export default function BusCard({ bus }) {
  const [detalle, setDetalle] = useState(null);

  useEffect(() => {
    if (bus?.Id_Viaje) {
      fetch(`http://localhost/proyectos/sistemabusesflores/backend/get-details.php?id=${bus.Id_Viaje}`)
        .then((res) => res.json())
        .then((data) => setDetalle(data))
        .catch((err) => console.error("Error cargando detalle de viaje:", err));
    }
  }, [bus]);

  if (!detalle) return <p>Cargando viaje #{bus?.Id_Viaje}...</p>;

  return (
    <div className="bus-card" style={cardStyle}>
      {/* Columna Izquierda: icono + ver foto */}
      <div style={leftStyle}>
        <img src="/bus-icon.png" alt="bus" style={{ width: "40px", marginBottom: "8px" }} />
        <button style={verFotoStyle}>Ver Foto</button>
      </div>

      {/* Columna Derecha: Info */}
      <div style={rightStyle}>
        <div style={headerStyle}>
          <span style={idStyle}>Viaje #{detalle.Id_Viaje}</span>
          <span style={empresaStyle}>{detalle.Servicio || "Flores"}</span>
        </div>

        <div style={rowStyle}>
          <strong>Salida:</strong> {detalle.Origen} a las <b>{detalle.Hora_salida}</b>
        </div>
        <div style={rowStyle}>
          <strong>Llegada:</strong> {detalle.Llegada} a las <b>{detalle.Hora_llegada}</b>
        </div>
        <div style={rowStyle}>
          <strong>Duración:</strong> {detalle.Duracion}
        </div>
        <div style={rowStyle}>
          <strong>Ubicación Sede:</strong> {detalle.Ubicacion_Sede}
        </div>

        <div style={preciosStyle}>
          <div>
            <strong>1° Piso:</strong> S/ {detalle.Precio_1_Piso} ({detalle.Asientos_1_Disponibles} libres)
          </div>
          <div>
            <strong>2° Piso:</strong> S/ {detalle.Precio_2_Piso} ({detalle.Asientos_2_Disponibles} libres)
          </div>
        </div>

        <div style={{ textAlign: "right" }}>
          <button style={botonStyle}>Elegir Asiento</button>
        </div>
      </div>
    </div>
  );
}


// --- Inline Styles ---
const cardStyle = {
  display: "flex",
  border: "1px solid #ddd",
  borderRadius: "8px",
  padding: "15px",
  marginBottom: "20px",
  boxShadow: "0 2px 6px rgba(0,0,0,0.1)",
};

const leftStyle = {
  width: "100px",
  display: "flex",
  flexDirection: "column",
  alignItems: "center",
  justifyContent: "center",
  borderRight: "1px solid #ddd",
  paddingRight: "10px",
};

const rightStyle = {
  flex: 1,
  paddingLeft: "15px",
  fontFamily: "Arial, sans-serif",
};

const headerStyle = {
  display: "flex",
  justifyContent: "space-between",
  marginBottom: "10px",
};

const idStyle = {
  fontWeight: "bold",
  fontSize: "14px",
};

const empresaStyle = {
  fontSize: "16px",
  fontWeight: "bold",
  color: "#f97316", // naranja
};

const rowStyle = {
  fontSize: "14px",
  marginBottom: "4px",
};

const preciosStyle = {
  marginTop: "10px",
  marginBottom: "10px",
  fontSize: "14px",
  display: "flex",
  justifyContent: "space-between",
};

const botonStyle = {
  backgroundColor: "white",
  border: "1px solid orange",
  color: "orange",
  padding: "6px 12px",
  borderRadius: "4px",
  cursor: "pointer",
};

const verFotoStyle = {
  fontSize: "12px",
  color: "orange",
  background: "none",
  border: "none",
  cursor: "pointer",
  textDecoration: "underline",
};
