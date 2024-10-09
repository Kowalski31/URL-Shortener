<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\type;

use App\Models\UrlMapping;

class URLShortenerController extends Controller
{
    public function getURLs()
    {
        $url_mappings = UrlMapping::select('original_url', 'short_url', 'created_at')->get()
            ->map(function ($url_mapping) {
                return [
                    'original_url' => $url_mapping->original_url,
                    'short_url' => $url_mapping->short_url,
                    'created_at' => $url_mapping->created_at->format('Y-m-d H:i:s')
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $url_mappings
        ]);
    }

    public function shortenURL(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url',
            'custom_short_code' => 'nullable|string|min:3|max:10|alpha_dash|unique:url_mappings,short_url',
        ]);

        if ($validator->failed()) {
            return response()->json([
                'error' => 'Invalid URL format',
                'message' => $validator->errors(),
            ], 400);
        }

        $originalURL = $request->input('original_url');

        $parseUrl = parse_url($originalURL);

        if (!isset($parseUrl['scheme'])) {
            $originalURL = 'https://' . $originalURL;
        }

        $customShortCode = $request->input('custom_short_code');

        if ($customShortCode) {
            $shortURL = $customShortCode;
        } else {
            $shortURL = $this->encode($originalURL);
        }

        $object = new UrlMapping();
        $object->original_url = $originalURL;
        $object->short_url = $shortURL;
        $object->save();



        return response()->json([
            'status' => 'success',
            'original_url' => $originalURL,
            'short_url' => $shortURL,
            'created_at' => $object->created_at->format('Y-m-d H:i:s')
        ], 200);
    }

    public function redirectShortURL(String $shortURL)
    {
        $record = UrlMapping::where('short_url', $shortURL)->first();

        if ($record) {
            return redirect()->away($record->original_url);
        }

        return response()->json([
            'error' => 'Short URL not found',
        ], 404);
    }


    // Encode function: Shorten URL using MD5 and Base64
    public function encode(String $originalURL)
    {
        do {
            $originalURLHash = md5($originalURL . microtime());

            $originalURLHashBase64Safe = rtrim(strtr(base64_encode(hex2bin($originalURLHash)), '+/', '-_'), '=');

            $shortURL = substr($originalURLHashBase64Safe, 0, 7);

            $existing_record = UrlMapping::where('short_url', $shortURL)->first();
        } while ($existing_record);

        return $shortURL;
    }

    // Decode function: Convert short URL back to hash bytes
    public function decode(Request $request)
    {
        $shortURL = $request->input('short_url');
        try {
            // Decode Base64 URL Safe
            $decodedBytes = base64_decode(str_pad(strtr($shortURL, '-_', '+/'), strlen($shortURL) % 4, '=', STR_PAD_RIGHT));

            return response()->json([
                'decoded_bytes' => bin2hex($decodedBytes)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid short URL'
            ], 400);
        }
    }
}
