import React, { useState, useEffect } from "react";
import Sidebar from "../components/sidebar";
import BusManager from "../components/BusManager";
import RutaManager from "../components/RutaManager";
import ViajeManager from "../components/ViajeManager";
import CrudModal from "../components/CrudModal";
import "../styles/adminpagestyle.css";

export default function AdminPage() {
  const [vistaActiva, setVistaActiva] = useState("buses");
  const [crudAccion, setCrudAccion] = useState(null);
  const [itemSeleccionado, setItemSeleccionado] = useState(null);
  const [reloadFlag, setReloadFlag] = useState(false);
  const [modalVisible, setModalVisible] = useState(false);
  const [tipoCrud, setTipoCrud] = useState("");

  const recargarDatos = () => {
    setReloadFlag((prev) => !prev);
  };

  const ejecutarCrud = (accion, item) => {
    const formData = new FormData();

    if (vistaActiva === "buses") {
      formData.append("accion", `${accion}_bus`);
      if (accion !== "eliminar") {
        formData.append("placa", item?.Placa || "AAA-000");
        formData.append("servicio", item?.Servicio || "ejecutivo");
        formData.append("n_pisos", item?.N_Pisos || 2);
        formData.append("n_asientos", item?.N_asientos || 52);
        formData.append("id_sede", item?.Id_Sede || 1);
      }
      if (accion !== "crear") formData.append("id_bus", item?.Id_Bus);
    }

    if (vistaActiva === "rutas") {
      formData.append("accion", `${accion}_ruta`);
      if (accion !== "eliminar") {
        formData.append("duracion", item?.Duracion || "5h");
        formData.append("id_origen", item?.Id_Origen || 1);
        formData.append("id_llegada", item?.Id_Llegada || 2);
      }
      if (accion !== "crear") formData.append("id_ruta", item?.Id_Ruta);
    }

    if (vistaActiva === "viajes") {
      formData.append("accion", `${accion}_viaje`);
      if (accion !== "eliminar") {
        formData.append("hora_salida", item?.Hora_salida || "08:00");
        formData.append("hora_llegada", item?.Hora_llegada || "14:00");
        formData.append("fecha_salida", item?.Fecha_salida || "2025-07-20");
        formData.append("fecha_llegada", item?.Fecha_llegada || "2025-07-20");
        formData.append("id_bus", item?.Id_Bus || 1);
        formData.append("id_ruta", item?.Id_Ruta || 1);
        formData.append("precio_piso1", 40);
        formData.append("precio_piso2", 50);
      }
      if (accion !== "crear") formData.append("id_viaje", item?.Id_Viaje);
    }

    fetch("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        alert("✅ Resultado: " + data);
        setItemSeleccionado(null);
        recargarDatos();
      })
      .catch((err) => alert("❌ Error: " + err));
  };

  useEffect(() => {
    if (crudAccion) {
      if (crudAccion === "crear") {
        setTipoCrud(vistaActiva.slice(0, -1)); // "buses" → "bus"
        setModalVisible(true);
      } else {
        if (!itemSeleccionado) {
          alert("Selecciona un elemento primero.");
        } else {
          ejecutarCrud(crudAccion, itemSeleccionado);
        }
      }
      setCrudAccion(null);
    }
  }, [crudAccion, vistaActiva, itemSeleccionado]);

  const enviarFormulario = (formData) => {
    fetch("http://localhost/proyectos/sistemabusesflores/backend/procesar-crud.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.text())
      .then((data) => {
        alert("✅ Resultado: " + data);
        setModalVisible(false);
        setItemSeleccionado(null);
        recargarDatos();
      })
      .catch((err) => alert("❌ Error: " + err));
  };

  const renderVista = () => {
    if (vistaActiva === "buses") {
      return <BusManager onSelectItem={setItemSeleccionado} reloadFlag={reloadFlag} />;
    } else if (vistaActiva === "rutas") {
      return <RutaManager onSelectItem={setItemSeleccionado} reloadFlag={reloadFlag} />;
    } else if (vistaActiva === "viajes") {
      return <ViajeManager onSelectItem={setItemSeleccionado} reloadFlag={reloadFlag} />;
    }
  };

  return (
    <div className="admin-wrapper">
      <Sidebar
        vistaActiva={vistaActiva}
        onSelect={setVistaActiva}
        onCrudAction={setCrudAccion}
      />
      <main className="admin-main">{renderVista()}</main>
      <CrudModal
        visible={modalVisible}
        tipo={tipoCrud}
        onClose={() => setModalVisible(false)}
        onSubmit={enviarFormulario}
      />
    </div>
  );
}
