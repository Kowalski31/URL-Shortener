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
        <form id="urlForm" action="{{ route('shorten.url') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="original_url" class="form-label">Enter a URL</label>
                <input type="text" class="form-control @error('original_url') is-invalid @enderror" id="original_url" name="original_url" placeholder="https://example.com" required>
                @error('original_url')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Shorten</button>
        </form>

        <!-- Display rút gọn URL nếu có -->
        <!-- Bảng danh sách các URL rút gọn -->
        @if(session('url_mappings') && count(session('url_mappings')) > 0)
        <div class="mt-3">
            <h4>Shortened URLs List:</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Original URL</th>
                        <th>Shortened URL</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('url_mappings') as $index => $urlMapping)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><a href="{{ $urlMapping['original_url'] }}" target="_blank">{{ $urlMapping['original_url'] }}</a></td>
                        <td><a href="{{ url('/') . '/' . $urlMapping['short_url'] }}" target="_blank">{{ $urlMapping['short_url'] }}</a></td>
                        <td>{{ $urlMapping['created_at'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

    </div>

    <!-- Validate ở phía Frontend -->
    <script>
        document.getElementById('urlForm').addEventListener('submit', function(e) {
            const urlInput = document.getElementById('original_url');
            const urlValue = urlInput.value;
            const urlPattern = /^(https?:\/\/)?([\da-z.-]+)\.([a-z.]{2,6})([/\w .-]*)*\/?$/;

            if (!urlPattern.test(urlValue)) {
                e.preventDefault();
                alert('Invalid URL format');
            }
        });
    </script>
</body>

</html>