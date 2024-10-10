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
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">URL Shortener</h1>

        <form id="urlForm">
            <div class="mb-3">
                <label for="original_url" class="form-label">Enter a URL</label>
                <input type="text" class="form-control" id="original_url" name="original_url"
                    placeholder="https://example.com" required>
                <div class="invalid-feedback" id="urlError"></div>
            </div>

            <div class="mb-3">
                <label for="custom_short_code" class="form-label">Custom Short Code (optional)</label>
                <input type="text" class="form-control" id="custom_short_code" name="custom_short_code"
                    placeholder="Enter a custom unique code (3-10 characters)" maxlength="10" pattern="[A-Za-z0-9_\-]{3,10}">
                <div class="invalid-feedback" id="customCodeError"></div>
            </div>

            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>

        <div class="mt-3">
            <h4>Shortened URLs List:</h4>
            <table class="table table-bordered" id="urlTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Original URL</th>
                        <th>Shortened URL</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody id="urlList"></tbody>
            </table>
        </div>
    </div>


    <script>
        function displayAllUrls() {
            fetch("{{ route('get.urls') }}", {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const urlList = document.getElementById('urlList');
                        urlList.innerHTML = '';

                        var urlMappings = data.data;
                        urlMappings.forEach((urlMapping, index) => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                                <td>${index + 1}</td>
                                <td><a href="${urlMapping.original_url}" target="_blank">${urlMapping.original_url}</a></td>
                                <td>
                                    <a href="${urlMapping.short_url}" target="_blank" id="shortUrl-${index}">${urlMapping.short_url}</a>
                                    <button class="btn btn-sm btn-secondary ml-2" onclick="copyToClipboard('shortUrl-${index}')">Copy</button>
                                </td>
                                <td>${urlMapping.created_at}</td>
                            `;

                            urlList.appendChild(row);
                        });
                    } else {
                        alert(data.error || 'Error fetching URLs');
                    }
                })
                .catch(error => {
                    alert('Error fetching URLs');
                    console.error('Error:', error);
                });
        }


        document.addEventListener('DOMContentLoaded', displayAllUrls);


        document.getElementById('urlForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const urlInput = document.getElementById('original_url');
            const customCodeInput = document.getElementById('custom_short_code');
            const urlValue = urlInput.value.trim();
            const customCodeValue = customCodeInput.value;
            const urlPattern = /^(https?:\/\/)?((([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,})|((\d{1,3}\.){3}\d{1,3}))(:\d{1,5})?(\/[^\s]*)?(\?[^\s]*)?(#[^\s]*)?$/;


            document.getElementById('urlError').textContent = '';
            urlInput.classList.remove('is-invalid');

            console.log(urlPattern.test(urlValue));

            if (urlPattern.test(urlValue) == false) {
                urlInput.classList.add('is-invalid');
                document.getElementById('urlError').textContent = 'Invalid URL format';
                return;
            }


            fetch("{{ route('shorten.url') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        original_url: urlValue,
                        custom_short_code: customCodeValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const urlList = document.getElementById('urlList');

                        const row = document.createElement('tr');
                        const newIndex = urlList.children.length + 1;

                        row.innerHTML = `
                                <td>${newIndex}</td>
                                <td><a href="${data.original_url}" target="_blank">${data.original_url}</a></td>
                                <td><a href="${data.short_url}" target="_blank">${data.short_url}</a></td>
                                <td>${data.created_at}</td>
                            `;

                        urlList.appendChild(row);

                        urlInput.value = '';
                        customCodeInput.value = '';
                    } else {
                        alert(data.error || 'Error shortening URL');
                    }
                })
                .catch(error => {
                    alert('Error shortening URL');
                    console.error('Error:', error);
                });
        });

        function copyToClipboard(elementId) {
            const urlElement = document.getElementById(elementId);
            const tempInput = document.createElement('input');
            document.body.appendChild(tempInput);
            tempInput.value = urlElement.href;
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            toastr.success('Copied to clipboard: ' + urlElement.href);
        }
    </script>
    
</body>

</html>