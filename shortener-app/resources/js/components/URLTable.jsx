import React from 'react';
import 'toastr/build/toastr.css';
import toastr from 'toastr';


// export default URLTable;
const URLTable = ({ shortenedUrls = [] }) => {
    return (
        <div>
            <style>
                {`
                    .text-truncate {
                        max-width: 300px;
                        white-space: nowrap;
                        overflow: hidden;
                        text-overflow: ellipsis;
                    }
                `}
            </style>

            <h4>Shortened URLs List:</h4>
            <table className="table table-responsive table-bordered">
                <thead className="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Original URL</th>
                        <th scope="col">Shortened URL</th>
                        <th scope="col">Created At</th>
                    </tr>
                </thead>
                <tbody>
                    {Array.isArray(shortenedUrls) && shortenedUrls.length > 0 ? (
                        shortenedUrls.map((url, index) => (
                            <tr key={index}>
                                <td>{index + 1}</td>
                                <td className="text-truncate">
                                    <a href={url.original_url} target="_blank" rel="noopener noreferrer">
                                        {url.original_url}
                                    </a>
                                </td>
                                <td>
                                    <a href={`/${url.short_url}`} target="_blank" rel="noopener noreferrer">
                                        {url.short_url}
                                    </a>
                                    <button className="btn btn-sm btn-secondary ml-2" onClick={() => copyToClipboard(url.short_url)}>
                                        Copy
                                    </button>
                                </td>
                                <td>{url.created_at}</td>
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td colSpan="4" className="text-center">
                                No URLs found.
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
};

function copyToClipboard(url) {
    navigator.clipboard.writeText(`${window.location.origin}/${url}`);
    toastr.success('Copied to clipboard: ' + url);
}

export default URLTable;