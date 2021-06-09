<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Library\PdfForm;

class PcrController extends Controller
{
    public function index(Request $request)
    {
        try{
            $data = [
                'fill_8' => $request->fio ?? 'Таскынбаев Аманбек',
                'fill_9' => $request->iin ?? 941220350516,
                'fill_10' => $request->gender ?? 'Муж',
                'fill_12' => $request->tel ?? '',
                'fill_14' => $request->date ?? '',
                'fill_3' => $request->date ?? '',
                'fill_111' => $request->date ?? '',
                'fill_112' => $request->dob ?? '',
            ];
            // return $data;
            $pdf_path = env("PDF_FORM_PATH", '/var/www/pcr_create/public/for_pcr.pdf');
            $pdf = new PdfForm($pdf_path, $data);
            $pdf->flatten()->save();
            // $bs64 = chunk_split(base64_encode(file_get_contents($pdf->output)));
            $bs64 = base64_encode(file_get_contents($pdf->output));
            unlink($pdf->output);
            return view('test')->with('data', ['file' => $bs64, 'file_name' => 'pcr.pdf']);
            return response()->json(['file' => $bs64, 'file_name' => 'pcr.pdf'], 200);
        } catch (\Exception $e) {
            return response()->json(['messageBack' => $e->getMessage()], 500);
        }
    }
}
