import React from 'react';
import 'toastr/build/toastr.css';
import toastr from 'toastr';

function ShortenedURLDisplay({ shortenedUrl }) {
    const copyToClipboard = () => {
        navigator.clipboard.writeText(shortenedUrl.short_url);
        toastr.success('Shortened URL copied to clipboard!');
    };

    return (
        <div className="mt-4">
            <div className="alert alert-success d-flex justify-content-between align-items-center">
                <span>Your Shortened URL: <a href={shortenedUrl.short_url} target="_blank" rel="noopener noreferrer">{shortenedUrl.short_url}</a></span>
                <button className="btn btn-outline-secondary" onClick={copyToClipboard}>Copy</button>
            </div>
        </div>
    );
}

export default ShortenedURLDisplay;
