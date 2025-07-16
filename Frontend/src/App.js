import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { NavBar } from './components/navbar';
import { HomePage } from './pages/homepage';
import { LoginPage } from './pages/loginpage';
import AdminPage from './pages/adminpage';
import TripPage from "./pages/trippage";

function App() {
  return (
    <div className="App">
        <BrowserRouter>
          <NavBar />
          <Routes>
            <Route path="/" element={<LoginPage />} />
            <Route path="/homepage" element={<HomePage />} />
            <Route path="/admin" element={<AdminPage />} />
            <Route path="/trippage" element={<TripPage />} />
          </Routes>
        </BrowserRouter>
    </div>
  );
}

export default App;
