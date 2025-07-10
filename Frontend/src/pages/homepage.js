import React from 'react'
import FormularioViaje from '../components/formularioviaje';


export const HomePage = () => {
  return (
    <header className="App-header">
      <section className="App-Form">
        <div className="form-container">
          <FormularioViaje />
        </div>
      </section>
    </header>
  )
}
