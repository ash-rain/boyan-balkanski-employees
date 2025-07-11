<?php

namespace App\Http\Controllers;

use App\Services\EmployeeCollaborationService;
use Illuminate\Http\Request;

class EmployeeCollaborationController extends Controller
{
    public function process(Request $request, EmployeeCollaborationService $service)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $collaborations = $service->findAllCollaborations($path);
            return response()->json($collaborations);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
