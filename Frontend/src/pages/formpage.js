import React from 'react'
import FormularioViaje from '../components/form';

export const FormPage = () => {
  return (
    <header className="App-header">
        <section className="App-Form">
          <section class="hero-container">
            <div class="hero">
              <h1>Hoy empezamos el cambio</h1>
              <p>Compra tu pasaje en línea</p>
            </div>
          </section>
          <section class="form-container">
            <FormularioViaje />
          </section>
        </section>
    </header>
  )
}
