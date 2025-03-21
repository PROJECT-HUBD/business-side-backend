<?php

// app/Http/Controllers/MaintenanceController.php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MaintenanceController extends Controller
{
    // Fetch the current maintenance settings
    public function index()
    {
        $maintenance = Maintenance::first(); // Get the first record
        return response()->json($maintenance);
    }

    // Store or update maintenance settings
    public function store(Request $request)
    {
        $validated = $request->validate([
            'maintain_status' => 'required|boolean',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'maintain_description' => 'nullable|string|max:100',
        ]);

        $maintenance = Maintenance::updateOrCreate(
            ['id' => 1], // Assuming only one record for the maintenance
            $validated
        );

        return response()->json($maintenance);
    }

    // Delete the maintenance settings
    public function destroy()
    {
        Maintenance::where('id', 1)->delete(); // Assuming only one record for the maintenance
        return response()->json(['message' => 'Maintenance data cleared']);
    }
}

