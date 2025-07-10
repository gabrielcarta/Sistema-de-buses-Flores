import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { NavBar } from './components/navbar';
import { HomePage } from './pages/homepage';
import { LoginPage } from './pages/loginpage';
import { AdminPage } from './pages/adminpage';

function App() {
  return (
    <div className="App">
        <BrowserRouter>
          <NavBar />
          <Routes>
            <Route path="/" element={<LoginPage />} />
            <Route path="/homepage" element={<HomePage />} />
            <Route path="/admin" element={<AdminPage />} />
          </Routes>
        </BrowserRouter>
    </div>
  );
}

export default App;
