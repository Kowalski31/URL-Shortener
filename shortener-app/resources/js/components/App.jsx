import React, { useState, useEffect } from 'react';

import ReactDOM from 'react-dom/client';

import URLForm from './URLForm';
import URLTable from './URLTable';
import ShortenedURLDisplay from './ShortenedURLDisplay';
import Pagination from './Pagination';
import 'bootstrap/dist/css/bootstrap.min.css';

import 'toastr/build/toastr.min.css';
import toastr from 'toastr';

function App() {
    const [shortenedUrls, setShortenedUrls] = useState([]);
    const [shortenedUrl, setShortenedUrl] = useState(null);
    const [pagination, setPagination] = useState({ currentPage: 1, lastPage: 1 });

    const fetchUrls = async (page = 1) => {
        try {
            const response = await fetch(`/api/get-urls?page=${page}`);
            const data = await response.json();
            
            if (data.status === 'success') {
                // console.log(JSON.stringify(data));
                setShortenedUrls(data.data.data);
                setPagination({ currentPage: data.data.current_page, lastPage: data.data.last_page });
            } else {
                toastr.error(data.error || 'Error fetching URLs');
            }
        } catch (error) {
            toastr.error('Error fetching URLs');
        }
    };

    useEffect(() => {
        fetchUrls();
    }, []);

    return (
        <div className="container mt-5">
            <h1 className="text-center">URL Shortener</h1>

            <URLForm setShortenedUrl={setShortenedUrl} fetchUrls={fetchUrls} />

            {shortenedUrl && <ShortenedURLDisplay shortenedUrl={shortenedUrl} />}

            <URLTable shortenedUrls={shortenedUrls} />

            <Pagination pagination={pagination} fetchUrls={fetchUrls} />
        </div>
    );
}

export default App;

// const container = document.getElementById('url-shortener-app');
// const root = ReactDOM.createRoot(container);
// root.render(<App />);