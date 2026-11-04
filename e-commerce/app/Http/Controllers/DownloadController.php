<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    public function download(License $license, ActivityLogger $activity)
    {
        abort_unless(Auth::check() && Auth::id() === $license->user_id, 403);
        abort_if($license->status !== 'active', 403, 'This license is not active.');

        $activity->record('downloaded', $license, ['product_id' => $license->product_id]);

        $product = $license->product;

        if ($product->file_path && Storage::exists($product->file_path)) {
            return Storage::download($product->file_path, $product->slug.'-download'.'.'.pathinfo($product->file_path, PATHINFO_EXTENSION));
        }

        $content = "Sample download placeholder for {$product->title}. Upload the final product file from the creator dashboard to replace this file.";

        return Response::streamDownload(fn () => print ($content), Str::slug($product->title).'-sample.txt');
    }
}
