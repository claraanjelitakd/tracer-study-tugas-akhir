<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Mencari perusahaan berdasarkan nama (autocomplete) atau filter provinsi/kabupaten.
     * Endpoint ini digunakan oleh FormKarier.vue untuk dropdown perusahaan interaktif.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        // 1. Inisialisasi Query Builder untuk model Company
        $query = Company::query();

        // 2. Filter berdasarkan kata kunci pencarian (nama_perusahaan)
        if ($request->filled('q')) {
            $query->where('nama_perusahaan', 'like', '%' . $request->q . '%');
        }

        // 3. (Opsional) Filter berdasarkan Provinsi
        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        // 4. (Opsional) Filter berdasarkan Kabupaten
        if ($request->filled('kabupaten_id')) {
            $query->where('kabupaten_id', $request->kabupaten_id);
        }

        // 5. Eksekusi query dengan batas 20 hasil teratas untuk performa UI
        $companies = $query->limit(20)->get(['id', 'nama_perusahaan', 'province_id', 'kabupaten_id', 'status_verifikasi']);

        // 6. Kembalikan data dalam format JSON untuk dirender oleh Vue
        return response()->json($companies);
    }
}
