import React, { useState } from 'react';

import ReactDOM from 'react-dom/client';

import App from './App';
import URLForm from './URLForm';
import URLTable from './URLTable';
import ShortenedURLDisplay from './ShortenedURLDisplay';
import Pagination from './Pagination';

// export default function HelloReact(){
//     return (
//         <h1>Hello React</h1>
//     );
// }

// const container = document.getElementById('hello-react');
// const root = ReactDOM.createRoot(container);
// root.render(<HelloReact />);

const container = document.getElementById('url-shortener-app');
const root = ReactDOM.createRoot(container);
root.render(<App />);