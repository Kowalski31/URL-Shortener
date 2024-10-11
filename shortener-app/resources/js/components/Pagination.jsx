import React from 'react';

function Pagination({ pagination, fetchUrls }) {
    const { currentPage, lastPage } = pagination;

    return (
        <nav aria-label="Page navigation" className="d-flex justify-content-center mt-3">
            <ul className="pagination">
                {currentPage > 1 && (
                    <li className="page-item">
                        <button className="page-link" onClick={() => fetchUrls(currentPage - 1)}>&laquo;</button>
                    </li>
                )}
                {Array.from({ length: lastPage }, (_, i) => i + 1).map((page) => (
                    <li key={page} className={`page-item ${page === currentPage ? 'active' : ''}`}>
                        <button className="page-link" onClick={() => fetchUrls(page)}>{page}</button>
                    </li>
                ))}
                {currentPage < lastPage && (
                    <li className="page-item">
                        <button className="page-link" onClick={() => fetchUrls(currentPage + 1)}>&raquo;</button>
                    </li>
                )}
            </ul>
        </nav>
    );
}

export default Pagination;
