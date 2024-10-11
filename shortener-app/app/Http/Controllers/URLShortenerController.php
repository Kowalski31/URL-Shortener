<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

use App\Models\UrlMapping;

class URLShortenerController extends Controller
{
    public function getURLs()
    {
        $url_mappings = UrlMapping::select('original_url', 'short_url', 'created_at')
            ->paginate(10)
            ->through(function ($url_mapping) {
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

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation error',
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
            $shortURL = $this->encode();
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

    public function encode()
    {
        $attempts = 0;
        $maxAttempts = 5;

        do {
            $shortURL = substr(bin2hex(random_bytes(4)), 0, 7);

            $existing_record = UrlMapping::where('short_url', $shortURL)->first();

            $attempts++;

            if ($attempts >= $maxAttempts) {
                throw new \Exception('Unable to generate unique short URL. Please try again.');
            }
        } while ($existing_record);

        return $shortURL;
    }

    // Decode function: Convert short URL back to hash bytes

    // public function decode(Request $request)
    // {
    //     $shortURL = $request->input('short_url');
    //     try {
    //         // Decode Base64 URL Safe
    //         $decodedBytes = base64_decode(str_pad(strtr($shortURL, '-_', '+/'), strlen($shortURL) % 4, '=', STR_PAD_RIGHT));

    //         return response()->json([
    //             'decoded_bytes' => bin2hex($decodedBytes)
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => 'Invalid short URL'
    //         ], 400);
    //     }
    // }
}
