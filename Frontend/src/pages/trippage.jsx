import React, { useEffect, useState } from "react";
import { useLocation } from "react-router-dom";
import BusCard from "../components/buscard";

export default function TripPage() {
  const { state } = useLocation();
  const [viajesIda, setViajesIda] = useState([]);
  const [viajesRetorno, setViajesRetorno] = useState([]);

  const origin = state?.origin;
  const destination = state?.destination;
  const departureDate = state?.datedeparture;
  const returnDate = state?.datereturn;

  useEffect(() => {
    if (origin && destination && departureDate) {
      fetch(
        `http://localhost/proyectos/sistemabusesflores/backend/get-trips.php?origin=${origin}&destination=${destination}&datedeparture=${departureDate}&datereturn=${returnDate || ''}`
      )
        .then((res) => res.json())
        .then((data) => {
          console.log("Datos recibidos del backend:", data);
          setViajesIda(data.ida || []);
          setViajesRetorno(data.retorno || []);
        })
        .catch((err) => console.error("Error cargando viajes", err));
    }
  }, [origin, destination, departureDate, returnDate]);

  if (!state) {
    return <p style={{ padding: "20px" }}>No se recibieron datos del formulario.</p>;
  }

  return (
    <div style={{ padding: "20px" }}>
      <h2>🚌 Viajes de ida: {origin} → {destination}</h2>
      {viajesIda.length > 0 ? (
        viajesIda.map((bus) => <BusCard key={bus.Id_Viaje} bus={bus} />)
      ) : (
        <p>No hay viajes de ida disponibles</p>
      )}

      {returnDate && (
        <>
          <h2 style={{ marginTop: "40px" }}>🚌 Viajes de retorno: {destination} → {origin}</h2>
          {viajesRetorno.length > 0 ? (
            viajesRetorno.map((bus) => <BusCard key={bus.Id_Viaje} bus={bus} />)
          ) : (
            <p>No hay viajes de retorno disponibles</p>
          )}
        </>
      )}
    </div>
  );
}
