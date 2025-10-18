import react from 'react';
import { createRoot } from 'react-dom/client';

function App() {
    return <h1>osu!pretend</h1>;
}

const root = createRoot(document.getElementById('app'));
root.render(<App />);
