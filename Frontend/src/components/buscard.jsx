import React, { useEffect, useState } from "react";
import "../styles/buscard.css";

export default function BusCard({ bus }) {
  const [detalle, setDetalle] = useState(null);
  const [mostrarAsientos, setMostrarAsientos] = useState(false);
  const [asientoSeleccionado, setAsientoSeleccionado] = useState(null);
  const [formularioVisible, setFormularioVisible] = useState(false);

  useEffect(() => {
    if (bus?.Id_Viaje) {
      fetch(`http://localhost/proyectos/sistemabusesflores/backend/get-details.php?id=${bus.Id_Viaje}`)
        .then((res) => res.json())
        .then((data) => setDetalle(data))
        .catch((err) => console.error("Error cargando detalle de viaje:", err));
    }
  }, [bus]);

  if (!detalle) return <p>Cargando viaje #{bus?.Id_Viaje}...</p>;

  const generarAsientos = (cantidad, piso) => {
    const asientos = [];
    for (let i = 1; i <= cantidad; i++) {
      asientos.push(`${piso}-${i}`);
    }
    return asientos;
  };

  const asientosPiso1 = generarAsientos(detalle.Asientos_1_Disponibles || 0, 1);
  const asientosPiso2 = generarAsientos(detalle.Asientos_2_Disponibles || 0, 2);
  const asientosTotales = [...asientosPiso1, ...asientosPiso2];

  const handleSubmit = async (e) => {
    e.preventDefault();
    const form = new FormData(e.target);

    const piso = asientoSeleccionado.split("-")[0];
    const numeroAsiento = asientoSeleccionado.split("-")[1];
    const precio = piso === "1" ? detalle.Precio_1_Piso : detalle.Precio_2_Piso;

    form.append("id_viaje", detalle.Id_Viaje);
    form.append("id_asiento", numeroAsiento);
    form.append("precio", precio);

    try {
      const res = await fetch("http://localhost/proyectos/sistemabusesflores/backend/registrar_compra.php", {
        method: "POST",
        body: form,
      });

      const data = await res.json();
      const text = await res.text();

      try {
        const data = JSON.parse(text);
        if (data.success) {
          alert(`✅ Compra realizada. Código QR: ${data.codigoQR}`);
        } else {
          alert(`❌ Error del servidor: ${data.mensaje}`);
        }
      } catch (err) {
        console.error("❌ La respuesta no fue JSON válido:", text);
        alert("❌ Error del servidor (respuesta inválida). Revisa la consola.");
      }

    } catch (err) {
      console.error("❌ Error al conectar con el servidor:", err);
      alert("❌ Error al conectar con el servidor.");
    }
  };

  return (
    <div className="bus-card" style={cardStyle}>
      <div style={leftStyle}>
        <img src="/bus-icon.png" alt="bus" style={{ width: "40px", marginBottom: "8px" }} />
        <button style={verFotoStyle}>Ver Foto</button>
      </div>

      <div style={rightStyle}>
        <div style={headerStyle}>
          <span style={idStyle}>Viaje #{detalle.Id_Viaje}</span>
          <span style={empresaStyle}>{detalle.Servicio || "Flores"}</span>
        </div>

        <div style={rowStyle}><strong>Salida:</strong> {detalle.Origen} a las <b>{detalle.Hora_salida}</b></div>
        <div style={rowStyle}><strong>Llegada:</strong> {detalle.Llegada} a las <b>{detalle.Hora_llegada}</b></div>
        <div style={rowStyle}><strong>Duración:</strong> {detalle.Duracion}</div>
        <div style={rowStyle}><strong>Ubicación Sede:</strong> {detalle.Ubicacion_Sede}</div>

        <div style={preciosStyle}>
          <div><strong>1° Piso:</strong> S/ {detalle.Precio_1_Piso} ({detalle.Asientos_1_Disponibles} libres)</div>
          <div><strong>2° Piso:</strong> S/ {detalle.Precio_2_Piso} ({detalle.Asientos_2_Disponibles} libres)</div>
        </div>

        <div style={{ textAlign: "right" }}>
          <button style={botonStyle} onClick={() => setMostrarAsientos(!mostrarAsientos)}>
            {mostrarAsientos ? "Cancelar" : "Elegir Asiento"}
          </button>
        </div>

        {mostrarAsientos && (
          <div style={{ marginTop: "10px", padding: "10px", border: "1px dashed gray" }}>
            <p><strong>Selecciona un asiento:</strong></p>
            <div style={{ display: "flex", flexWrap: "wrap", gap: "10px" }}>
              {asientosTotales.map((asiento) => (
                <button
                  key={asiento}
                  style={{
                    padding: "6px 10px",
                    backgroundColor: asiento === asientoSeleccionado ? "#28a745" : "#f0f0f0",
                    border: "1px solid #ccc",
                    borderRadius: "5px",
                    cursor: "pointer"
                  }}
                  onClick={() => setAsientoSeleccionado(asiento)}
                >
                  {asiento}
                </button>
              ))}
            </div>

            {asientoSeleccionado && !formularioVisible && (
              <div style={{ marginTop: "10px", textAlign: "right" }}>
                <button
                  onClick={() => setFormularioVisible(true)}
                  style={{ padding: "8px 12px", backgroundColor: "#28a745", color: "white", border: "none", borderRadius: "5px" }}
                >
                  Continuar a formulario
                </button>
              </div>
            )}

            {formularioVisible && (
              <form
                onSubmit={(e) => {
                  e.preventDefault();

                  const form = new FormData(e.target);

                  // Asegúrate de tener estas 3 variables del viaje
                  form.append("id_viaje", detalle.Id_Viaje);
                  form.append("id_asiento", asientoSeleccionado); // Debes enviar el ID real, no "1-2"
                  
                  // Determina el precio según el piso
                  const piso = asientoSeleccionado?.startsWith("1-") ? 1 : 2;
                  const precio = piso === 1 ? detalle.Precio_1_Piso : detalle.Precio_2_Piso;
                  form.append("precio", precio);

                  fetch("http://localhost/proyectos/sistemabusesflores/backend/registrar_compra.php", {
                    method: "POST",
                    body: form,
                  })
                    .then((res) => res.ok ? res.json() : res.text().then(t => { throw new Error(t); }))
                    .then((data) => {
                      if (data.success) {
                        alert(`✅ Compra realizada. Código QR: ${data.codigoQR}`);
                      } else {
                        alert(`❌ Error en el servidor: ${data.mensaje}`);
                      }
                    })
                    .catch((err) => {
                      console.error("❌ Error al conectar con el servidor:", err);
                      alert("❌ Error al realizar la compra. Revisa la consola.");
                    });
                }}
                style={{
                  marginTop: "15px",
                  display: "flex",
                  flexDirection: "column",
                  gap: "10px",
                  backgroundColor: "#f9f9f9",
                  padding: "15px",
                  border: "1px solid #ccc",
                  borderRadius: "8px"
                }}
              >
                <h4>Datos del pasajero</h4>
                <input type="text" name="dni" placeholder="DNI" required />
                <input type="text" name="nombre" placeholder="Nombre" required />
                <input type="text" name="apellido" placeholder="Apellido paterno" required />
                <input type="text" name="apellido2" placeholder="Apellido materno" required />
                <input type="date" name="fecha_nac" required />
                <select name="sexo" required>
                  <option value="">Sexo</option>
                  <option value="M">Masculino</option>
                  <option value="F">Femenino</option>
                </select>
                <input type="tel" name="telefono" placeholder="Teléfono" required />
                <input type="email" name="correo" placeholder="Correo electrónico" required />
                <select name="medio_pago" required>
                  <option value="">Medio de pago</option>
                  <option value="Yape">Yape</option>
                  <option value="Efectivo">Efectivo</option>
                  <option value="Tarjeta">Tarjeta</option>
                </select>

                <button
                  type="submit"
                  style={{ backgroundColor: "#007bff", color: "white", padding: "8px", borderRadius: "4px" }}
                >
                  Confirmar compra
                </button>
              </form>
            )}
          </div>
        )}
      </div>
    </div>
  );
}

const cardStyle = { display: "flex", padding: "15px", border: "1px solid #ddd", borderRadius: "10px", marginBottom: "20px" };
const leftStyle = { width: "80px", display: "flex", flexDirection: "column", alignItems: "center", marginRight: "20px" };
const rightStyle = { flex: 1 };
const headerStyle = { display: "flex", justifyContent: "space-between", marginBottom: "10px" };
const idStyle = { fontWeight: "bold" };
const empresaStyle = { backgroundColor: "#eee", padding: "2px 8px", borderRadius: "4px" };
const rowStyle = { marginBottom: "5px" };
const preciosStyle = { marginTop: "10px", marginBottom: "10px" };
const verFotoStyle = { padding: "4px 6px", fontSize: "12px", backgroundColor: "#ccc", border: "none", borderRadius: "4px" };
const botonStyle = { padding: "8px 12px", backgroundColor: "#007bff", color: "white", border: "none", borderRadius: "5px" };

// --- Inline Styles ---