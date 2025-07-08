import React, {useState} from 'react'
import { Link } from 'react-router-dom';

export const NavBar = () => {

  const [isOpen, setIsOpen] = useState(false);

  return (
    <div>
      <header className="navbar">
        <nav className="navbar-container">
          <div className="logo">
            <Link to="/">Flores</Link>
          </div>

          <div className="menu-box">
            <ul className="nav-links">
              <li><Link to="#numero">(01)4800725</Link></li>
              <li><Link to="#compras">Mis compras por internet</Link></li>
              <li><Link to="#documento">Mi documento electrónico</Link></li>
              <li><Link to="#envios">Seguimiento de envíos</Link></li>
            </ul>
          </div>
          
        <button className="burger" onClick={() => setIsOpen(prev => !prev)}>
          <span></span>
          <span></span>
          <span></span>
        </button>

        {/* Menú desplegable */}
        <div className={`dropdown-menu ${isOpen ? 'open' : ''}`}>
            <Link to="#servicios">Servicios</Link>
            <Link to="#destinos">Destinos</Link>
            <Link to="#contactos">Contactos</Link>
          </div>
        </nav>
      </header>
    </div>
  )
}
