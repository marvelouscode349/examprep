<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $stream   = $user->stream ?? 'general';

        $subjects = Subject::where('is_active', true)
            ->where(function ($q) use ($stream) {
                $q->where('stream', $stream)
                  ->orWhere('stream', 'all');
            })
            ->get(['id', 'name', 'slug', 'stream', 'icon']);

        return response()->json([
            'success'  => true,
            'stream'   => $stream,
            'subjects' => $subjects,
        ]);
    }
}

