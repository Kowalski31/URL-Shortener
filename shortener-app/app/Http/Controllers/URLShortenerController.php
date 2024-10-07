<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\type;

use App\Models\UrlMapping;

class URLShortenerController extends Controller
{
    public function shortenURL(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url',
        ]);
        
        if($validator->failed()){
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

        $shortURL = $this->encode($originalURL);

        $object = new UrlMapping();
        $object->original_url = $originalURL;
        $object->short_url = $shortURL;
        $object->save();

        return redirect()->back()->with([
            'original_url' => $object->original_url,
            'short_url' => $object->short_url,
            'created_at' => $object->created_at->format('Y-m-d H:i:s')
        ]);
    }
    
    // Encode function: Shorten URL using MD5 and Base64
    public function encode(String $originalURL)
    {
        $originalURLHash = md5($originalURL);

        $originalURLHashBase64Safe = rtrim(strtr(base64_encode(hex2bin($originalURLHash)), '+/', '-_'), '=');

        $shortURL = substr($originalURLHashBase64Safe, 0, 7);

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
