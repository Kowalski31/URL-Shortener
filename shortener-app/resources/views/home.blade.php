<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <style>
        .text-truncate {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body>
    <div class="container mt-5 ">
        <h1 class="text-center">URL Shortener</h1>

        <form id="urlForm" class="mb-5 d-flex flex-column align-items-center">
            <div class="mb-3 w-75 ">
                <label for="original_url" class="form-label">Enter a URL</label>
                <input type="text" class="form-control" id="original_url" name="original_url"
                    placeholder="https://example.com" required>
                <div class="invalid-feedback" id="urlError"></div>
            </div>

            <div class="mb-3 w-75 ">
                <label for="custom_short_code" class="form-label">Custom Short Code (optional)</label>
                <input type="text" class="form-control" id="custom_short_code" name="custom_short_code"
                    placeholder="Enter a custom unique code (3-10 characters)" maxlength="10" pattern="[A-Za-z0-9_\-]{3,10}">
                <div class="invalid-feedback" id="customCodeError"></div>
            </div>

            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>

        <div id="shortened-url-display" class="mt-4" style="display: none;">
            <div class="alert alert-success d-flex justify-content-between align-items-center">
                <span>Your Shortened URL: <a href="#" id="shortened-url-link" target="_blank"></a></span>
                <button class="btn btn-outline-secondary" id="copy-button">Copy</button>
            </div>
        </div>

        <div class="table-responsive">
            <h4>Shortened URLs List:</h4>
            <table class="table table-responsive table-bordered" id="urlTable">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Original URL</th>
                        <th scope="col">Shortened URL</th>
                        <th scope="col">Created At</th>
                    </tr>
                </thead>
                <tbody id="urlList"></tbody>
            </table>
        </div>

        <nav id="paginationControls" aria-label="Page navigation" class="d-flex justify-content-center mt-3">
            <ul class="pagination"></ul>
        </nav>
    </div>


    <script>
        async function fetchUrls(page = 1) {
            try {
                const response = await fetch(`{{ route('api.get.urls') }}?page=${page}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const urlList = document.getElementById('urlList');
                    const paginationControls = document.getElementById('paginationControls').querySelector('.pagination');


                    urlList.innerHTML = '';
                    paginationControls.innerHTML = '';


                    data.data.data.forEach((urlMapping, index) => {
                        const row = `
                    <tr>
                        <td>${index + 1}</td>
                        <td class="text-truncate"><a href="${urlMapping.original_url}" target="_blank">${urlMapping.original_url}</a></td>
                        <td>
                            <a href="${urlMapping.short_url}" target="_blank">${urlMapping.short_url}</a>
                            <button class="btn btn-sm btn-secondary ml-2" onclick="copyToClipboard('${urlMapping.short_url}')">Copy</button>
                        </td>
                        <td>${urlMapping.created_at}</td>
                    </tr>
                `;
                        urlList.insertAdjacentHTML('beforeend', row);
                    });


                    const currentPage = data.data.current_page;
                    const lastPage = data.data.last_page;

                    if (data.data.prev_page_url) {
                        paginationControls.innerHTML += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="fetchUrls(${currentPage - 1})" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                `;
                    }

                    for (let page = 1; page <= lastPage; page++) {
                        paginationControls.innerHTML += `
                    <li class="page-item ${page === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="fetchUrls(${page})">${page}</a>
                    </li>
                `;
                    }

                    if (data.data.next_page_url) {
                        paginationControls.innerHTML += `
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="fetchUrls(${currentPage + 1})" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                `;
                    }
                } else {
                    alert(data.error || 'Error fetching URLs');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error fetching URLs');
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            fetchUrls();
        });

        document.getElementById('urlForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const urlInput = document.getElementById('original_url');
            const customCodeInput = document.getElementById('custom_short_code');
            const urlValue = urlInput.value.trim();
            const customCodeValue = customCodeInput.value;
            const urlPattern = /^(https?:\/\/)?((([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,})|((\d{1,3}\.){3}\d{1,3}))(:\d{1,5})?(\/[^\s]*)?(\?[^\s]*)?(#[^\s]*)?$/;

            document.getElementById('urlError').textContent = '';
            urlInput.classList.remove('is-invalid');

            if (!urlPattern.test(urlValue)) {
                urlInput.classList.add('is-invalid');
                document.getElementById('urlError').textContent = 'Invalid URL format';
                return;
            }

            try {
                const response = await fetch("{{ route('api.shorten.url') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        original_url: urlValue,
                        custom_short_code: customCodeValue
                    })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    const shortenedUrlDisplay = document.getElementById('shortened-url-display');
                    shortenedUrlDisplay.style.display = 'block'; // Hiện div
                    shortenedUrlDisplay.style.display = 'flex'; // Thay đổi display thành flex khi hiển thị
                    shortenedUrlDisplay.classList.add('justify-content-center');

                    const shortenedUrlLink = document.getElementById('shortened-url-link');
                    shortenedUrlLink.textContent = data.short_url; // Cập nhật URL rút gọn
                    shortenedUrlLink.href = data.short_url;

                    // Sao chép vào clipboard
                    document.getElementById('copy-button').addEventListener('click', function() {
                        navigator.clipboard.writeText(data.short_url);
                        toastr.success('Shortened URL copied to clipboard!');
                    });


                    const urlList = document.getElementById('urlList');
                    const newIndex = urlList.children.length + 1;

                    const row = `
                        <tr>
                            <td>${newIndex}</td>
                            <td class="text-truncate"><a href="${data.original_url}" target="_blank">${data.original_url}</a></td>
                            <td>
                                <a href="${data.short_url}" target="_blank">${data.short_url}</a>
                                <button class="btn btn-sm btn-secondary ml-2" onclick="copyToClipboard('${data.short_url}')">Copy</button>
                            </td>
                            <td>${data.created_at}</td>
                        </tr>
                    `;

                    urlList.insertAdjacentHTML('beforeend', row);
                    urlInput.value = '';
                    customCodeInput.value = '';
                } else {
                    alert(data.error || 'Error shortening URL');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error shortening URL');
            }
        });


        function copyToClipboard(url) {
            const tempInput = document.createElement('input');
            document.body.appendChild(tempInput);
            tempInput.value = url;
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            toastr.success('Copied to clipboard: ' + url);
        }
    </script>

</body>

</html>