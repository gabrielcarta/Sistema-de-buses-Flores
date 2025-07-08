import logo from './logo.svg';
import './App.css';
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { NavBar } from './components/navbar';
import { FormPage } from './pages/formpage';

function App() {
  return (
    <div className="App">
      <header className='App-header'>
        <BrowserRouter>
          <NavBar />
          <Routes>
            <Route path="/" element={<FormPage />} />
          </Routes>
        </BrowserRouter>
      </header>
      <main className='App-main'>
        <img src={logo} className="App-logo" alt="logo" />
        <p>
          Este es mi primer proyecto con React c:</p>
      </main>
    </div>
  );
}

export default App;
