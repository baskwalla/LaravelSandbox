<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    /**
     * Show images
     * 
     * @return $paths
     */
    public function show()
    {
        $images = DB::table('images')->get();

        foreach ($images as $image) {
            $raw = parse_url($image->url);
            $paths[] = 'http://dn89wdc6gtf3n.cloudfront.net/images/' . $image->filename;
        }

        return view('images', ['images' => $paths]);
    }

    /**
     * Upload image to S3
     * 
     * @param Request $request
     * @return Illuminate\Support\Facades\Storage response
     */
    public function upload(Request $request)
    {
        $path = $request->file('image')->store('images', 's3');

        $image = Image::create([
            'filename' => basename($path),
            'url' => Storage::disk('s3')->url($path)
        ]);

        return Storage::disk('s3')->response('images/' . $image->filename);
    }
}
