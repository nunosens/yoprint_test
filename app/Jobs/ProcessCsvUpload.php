<?php

namespace App\Jobs;

use App\Models\CsvUpload;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\CharsetConverter;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $csvUpload;

    public function __construct(CsvUpload $csvUpload)
    {
        $this->csvUpload = $csvUpload;
    }

    public function handle()
    {
        $this->csvUpload->update(['status' => 'processing']);

        try {
            $filePath = Storage::path('csv_uploads/' . $this->csvUpload->filename);
            
            $csv = Reader::createFromPath($filePath, 'r');
            $csv->setHeaderOffset(0);
            
            CharsetConverter::addTo($csv, 'UTF-8', 'UTF-8');
            
            $records = $csv->getRecords();
            $totalRows = count($csv);
            
            $this->csvUpload->update(['total_rows' => $totalRows]);
            
            $processed = 0;
            foreach ($records as $record) {
                $cleanedRecord = array_map(function($value) {
                    return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
                }, $record);
                
                Product::updateOrCreate(
                    ['unique_key' => $cleanedRecord['UNIQUE_KEY']],
                    [
                        'product_title' => $cleanedRecord['PRODUCT_TITLE'],
                        'product_description' => $cleanedRecord['PRODUCT_DESCRIPTION'],
                        'style_number' => $cleanedRecord['STYLE#'],
                        'sanmar_mainframe_color' => $cleanedRecord['SANMAR_MAINFRAME_COLOR'],
                        'size' => $cleanedRecord['SIZE'],
                        'color_name' => $cleanedRecord['COLOR_NAME'],
                        'piece_price' => $cleanedRecord['PIECE_PRICE'],
                    ]
                );
                
                $processed++;
                $this->csvUpload->update(['processed_rows' => $processed]);
            }
            
            $this->csvUpload->update(['status' => 'completed']);
            
        } catch (\Exception $e) {
            $this->csvUpload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}