<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// use App\Models\

class URLShortenerController extends Controller
{
    // Encode function: Shorten URL using MD5 and Base64
    public function encode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url',
        ]);

        if($validator->failed()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $originalURL = $request->input('original_url');

        $parseUrl = parse_url($originalURL);

        if(!isset($parseUrl['scheme']))
        {
            $originalURL = 'https://' . $originalURL;
        }

        $originalURLHash = md5($originalURL);

        $originalURLHashBase64Safe = rtrim(strtr(base64_encode(hex2bin($originalURLHash)), '+/', '-_'), '=');

        $shortURL = substr($originalURLHashBase64Safe, 0, 7);

        // $object = new 

        return redirect()->back()->with([
            'original_url' => $originalURL,
            'short_url' => url('/').'/'.$shortURL,
        ]);

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
