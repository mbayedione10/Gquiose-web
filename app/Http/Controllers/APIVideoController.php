<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Video;
use Illuminate\Http\Request;

class APIVideoController extends Controller
{
    public function videos()
    {
        $videos = Video::select('id', 'name', 'url')
            ->orderBy('id', 'desc')
            ->get();

        $data = [
            'videos' => $videos
        ];

        return ApiResponse::success($data);
    }
}
