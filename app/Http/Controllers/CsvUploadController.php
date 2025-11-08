<?php

namespace App\Http\Controllers;

use App\Models\CsvUpload;
use Illuminate\Http\Request;
use App\Jobs\ProcessCsvUpload;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class CsvUploadController extends Controller
{
    public function index()
    {
        $uploads = CsvUpload::latest()->get();
        return view('csv-uploads.index', compact('uploads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:51200' // 50MB
        ]);

        try {
            $file = $request->file('csv_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Store file
            $file->storeAs('csv_uploads', $filename);
            
            // Create upload record
            $csvUpload = CsvUpload::create([
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'status' => 'pending'
            ]);
            
            // Dispatch job
            ProcessCsvUpload::dispatch($csvUpload);
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully and processing started.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUploads()
    {
        $uploads = CsvUpload::latest()->get();
        
        return response()->json([
            'uploads' => $uploads->map(function($upload) {
                return [
                    'id' => $upload->id,
                    'original_name' => $upload->original_name,
                    'status' => $upload->status,
                    'total_rows' => $upload->total_rows,
                    'processed_rows' => $upload->processed_rows,
                    'created_at' => $upload->created_at->format('Y-m-d H:i:s'),
                    'error_message' => $upload->error_message,
                ];
            })
        ]);
    }
}