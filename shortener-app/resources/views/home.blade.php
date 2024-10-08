<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">URL Shortener</h1>

        <!-- Form nhập URL -->
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
                    placeholder="Enter a custom code (3-10 characters)" maxlength="10" pattern="[A-Za-z0-9_\-]{3,10}">
                <div class="invalid-feedback" id="customCodeError"></div>
            </div>

            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>

        <!-- Bảng danh sách các URL rút gọn -->
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

    <!-- Validate và gửi request AJAX -->
    <script>
        // Hàm để hiển thị tất cả các URL
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
                        urlList.innerHTML = ''; // Xóa danh sách cũ

                        data.data.forEach((urlMapping, index) => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                    <td>${index + 1}</td>
                    <td><a href="${urlMapping.original_url}" target="_blank">${urlMapping.original_url}</a></td>
                    <td><a href="${window.location.origin}/${urlMapping.short_url}" target="_blank">${urlMapping.short_url}</a></td>
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

        // Gọi hàm này ngay khi tài liệu đã tải
        document.addEventListener('DOMContentLoaded', displayAllUrls);

        document.getElementById('urlForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn form nạp lại trang
            const urlInput = document.getElementById('original_url');
            const customCodeInput = document.getElementById('custom_short_code'); // Thêm dòng này
            const urlValue = urlInput.value;
            const customCodeValue = customCodeInput.value; // Lấy giá trị custom short code
            const urlPattern = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;

            // Xóa lỗi hiện tại
            document.getElementById('urlError').textContent = '';
            urlInput.classList.remove('is-invalid');

            if (!urlPattern.test(urlValue)) {
                urlInput.classList.add('is-invalid');
                document.getElementById('urlError').textContent = 'Invalid URL format';
                return;
            }

            // Gửi AJAX request
            fetch("{{ route('shorten.url') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        original_url: urlValue,
                        custom_short_code: customCodeValue // Thêm trường custom_short_code vào body
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const urlList = document.getElementById('urlList');
                        urlList.innerHTML = ''; // Xóa danh sách cũ

                        data.data.forEach((urlMapping, index) => {
                            const row = document.createElement('tr');

                            row.innerHTML = `
                        <td>${index + 1}</td>
                        <td><a href="${urlMapping.original_url}" target="_blank">${urlMapping.original_url}</a></td>
                        <td><a href="${window.location.origin}/${urlMapping.short_url}" target="_blank">${urlMapping.short_url}</a></td>
                        <td>${urlMapping.created_at}</td>
                    `;

                            urlList.appendChild(row);
                        });

                        urlInput.value = ''; // Xóa input sau khi gửi thành công
                        customCodeInput.value = ''; // Xóa custom short code sau khi gửi thành công
                    } else {
                        alert(data.error || 'Error shortening URL');
                    }
                })
                .catch(error => {
                    alert('Error shortening URL');
                    console.error('Error:', error);
                });
        });
    </script>
</body>

</html>