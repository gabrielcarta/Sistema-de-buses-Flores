import React, { useState, useEffect } from "react";
import Sidebar from "../components/sidebar";
import BusManager from "../components/BusManager";
import RutaManager from "../components/RutaManager";
import ViajeManager from "../components/ViajeManager";
import "../styles/adminpagestyle.css";

export default function AdminPage() {
  const [vistaActiva, setVistaActiva] = useState("buses");
  const [itemSeleccionado, setItemSeleccionado] = useState(null);
  const [reloadFlag, setReloadFlag] = useState(false);
  const [crudAction, setCrudAction] = useState(null);

  const handleCrudAction = (accion) => {
    setCrudAction({
      accion,
      onDone: () => {
        setReloadFlag((prev) => !prev); // fuerza recarga
        setCrudAction(null); // limpia acción
      },
    });
  };

  const renderVista = () => {
    if (vistaActiva === "buses") {
      return (
        <BusManager
          onSelectItem={setItemSeleccionado}
          reloadFlag={reloadFlag}
          crudAction={crudAction}
        />
      );
    } else if (vistaActiva === "rutas") {
      return (
        <RutaManager
          onSelectItem={setItemSeleccionado}
          reloadFlag={reloadFlag}
          crudAction={crudAction}
        />
      );
    } else if (vistaActiva === "viajes") {
      return (
        <ViajeManager
          onSelectItem={setItemSeleccionado}
          reloadFlag={reloadFlag}
          crudAction={crudAction}
        />
      );
    }
  };

  return (
    <div className="admin-wrapper">
      <Sidebar
        vistaActiva={vistaActiva}
        onSelect={setVistaActiva}
        onCrudAction={handleCrudAction}
      />
      <main className="admin-main">{renderVista()}</main>
    </div>
  );
}
