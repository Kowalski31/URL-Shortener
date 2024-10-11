import React, { useState } from 'react';
import toastr from 'toastr';

function URLForm({ setShortenedUrl, fetchUrls }) {
    const [originalUrl, setOriginalUrl] = useState('');
    const [customShortCode, setCustomShortCode] = useState('');

    const handleSubmit = async (e) => {
        e.preventDefault();
        const urlPattern = /^(https?:\/\/)?((([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,})|((\d{1,3}\.){3}\d{1,3}))(:\d{1,5})?(\/[^\s]*)?(\?[^\s]*)?(#[^\s]*)?$/;

        if (!urlPattern.test(originalUrl)) {
            toastr.error('Invalid URL format');
            return;
        }

        try {
            const response = await fetch("/api/shorten", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ original_url: originalUrl, custom_short_code: customShortCode }),
            });
            const data = await response.json();
            if (data.status === 'success') {
                setShortenedUrl(data);
                fetchUrls();
            } else {
                toastr.error(data.error || 'Error shortening URL');
            }
        } catch (error) {
            toastr.error('Error shortening URL');
        }
    };

    return (
        <form onSubmit={handleSubmit} className="mb-5 d-flex flex-column align-items-center">
            <div className="mb-3 w-75">
                <label htmlFor="original_url" className="form-label">Enter a URL</label>
                <input
                    type="text"
                    className="form-control"
                    id="original_url"
                    value={originalUrl}
                    onChange={(e) => setOriginalUrl(e.target.value)}
                    placeholder="https://example.com"
                    required
                />
            </div>

            <div className="mb-3 w-75">
                <label htmlFor="custom_short_code" className="form-label">Custom Short Code (optional)</label>
                <input
                    type="text"
                    className="form-control"
                    id="custom_short_code"
                    value={customShortCode}
                    onChange={(e) => setCustomShortCode(e.target.value)}
                    placeholder="Enter a custom unique code (3-10 characters)"
                />
            </div>

            <button type="submit" className="btn btn-primary">Shorten</button>
        </form>
    );
}


export default URLForm;
