<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CampainFX;
use Illuminate\Support\Facades\Log;

class HomeCampainFXAPIController extends Controller
{
    public function index()
    {
        try {
            $listCampain = CampainFX::all();
            return response()->json($listCampain, 200);
        } catch (\Exception $e) {
            Log::error('Campain loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Campain loading failed.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $campainFX = CampainFX::findOrFail($id);
            return response()->json($campainFX, 200);
        } catch (\Exception $e) {
            Log::error('Campain loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Campain loading failed.'], 500);
        }
    }
    
    public function new()
    {
        try {
            $desired_status_ORIG = 'ORIG';
            $listCampain = CampainFX::where('status', $desired_status_ORIG)
                            ->orderBy('updated_at', 'desc')
                            ->get();
            
            return response()->json($listCampain, 200);

        } catch (\Exception $e) {
            Log::error('Campain loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Campain loading failed.'], 500);
        }
    }

    public function run()
    {
        try {
            $desired_status_RUN = 'RUN';
            $listCampain = CampainFX::where('status', $desired_status_RUN)
                            ->orderBy('updated_at', 'desc')
                            ->get();
            return response()->json($listCampain, 200);
        } catch (\Exception $e) {
            Log::error('Campain loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Campain loading failed.'], 500);
        }
    }

    public function done()
    {
        try {
            $desired_status_DONE = 'DONE';
            $listCampain = CampainFX::where('status', $desired_status_DONE)
                            ->orderBy('updated_at', 'desc')
                            ->get();
            return response()->json($listCampain, 200);
        } catch (\Exception $e) {
            Log::error('Campain loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Campain loading failed.'], 500);
        }
    }

    public function detail($id)
    {
        try {
            $campainFX = CampainFX::findOrFail($id);
            return response()->json($campainFX, 200);
        } catch (\Exception $e) {
            Log::error('Campain loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Campain loading failed.'], 500);
        }
    }

    public function contact()
    {
        try {
            return response()->json(['message' => 'Contact us at support@example.com'], 200);
        } catch (\Exception $e) {
            Log::error('Contact loading failed: ' . $e->getMessage());
            return response()->json(['error' => 'Contact loading failed.'], 500);
        }
    }
}
