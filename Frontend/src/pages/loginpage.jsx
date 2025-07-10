import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import '../styles/loginpagestyle.css';

// Simulación de usuarios para autenticación
const users = [
  { username: 'admin', password: 'admin123', role: 'admin' },
  { username: 'cliente', password: 'cliente123', role: 'cliente' }
];

export const LoginPage = () => {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const navigate = useNavigate();

    const handleSubmit = (e) => {
    e.preventDefault();

    // Ejemplo simple de autenticación
    const user = users.find(
      u => u.username === username && u.password === password
    );

    if (user) {
      if (user.role === 'admin') {
        navigate('/admin');
      } else if (user.role === 'cliente') {
        navigate('/homepage');
      }
    } else {
      alert('Usuario o contraseña incorrectos');
    }
    };

  return (
    <div className="login-page-center">
      <div className="login-container">
        <h2>Iniciar sesión</h2>
        <form onSubmit={handleSubmit}>
          <input
            type="text"
            placeholder="Usuario"
            value={username}
            onChange={e => setUsername(e.target.value)}
            required
          />
          <input
            type="password"
            placeholder="Contraseña"
            value={password}
            onChange={e => setPassword(e.target.value)}
            required
          />
          <button type="submit">Entrar</button>
        </form>
      </div>
    </div>
  );
}
