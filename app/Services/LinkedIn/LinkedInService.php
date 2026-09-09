<?php

namespace App\Services\LinkedIn;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class LinkedInService
{
    /**
     * Memanggil script Python MCP client untuk mengambil data profil LinkedIn.
     *
     * @param  string  $identifier  username atau url profil linkedin
     */
    public function fetchProfileData(string $identifier): array
    {
        // Lokasi script python
        $scriptPath = base_path('scripts/linkedin_mcp_client.py');

        // Membangun process, asumsi `uv run` atau `python` tersedia di sistem
        // Untuk tahap ini, kita gunakan 'python' as default. Anda bisa menggantinya dengan 'uv run'
        // jika menggunakan uv.
        $process = new Process(['py', $scriptPath, $identifier], null, [
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\WINDOWS',
            'PATH' => getenv('PATH'),
            'USERPROFILE' => getenv('USERPROFILE'),
            'PYTHONIOENCODING' => 'utf-8',
        ]);

        // Atur timeout yang cukup lama karena scraping bisa butuh waktu (misal 60 detik)
        $process->setTimeout(60);

        try {
            $process->run();

            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();

            // Extract JSON from output (since python script might print other setup logs)
            preg_match('/\{.*\}/s', $output, $matches);
            $jsonString = $matches[0] ?? $output;
            $data = json_decode($jsonString, true);

            file_put_contents(storage_path('logs/linkedin_sync_dump.json'), $jsonString);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('LinkedIn MCP JSON Parse Error: '.json_last_error_msg());
                Log::error('Raw Output: '.$output);
                throw new \Exception('Gagal membaca respons dari sistem LinkedIn: '.json_last_error_msg());
            }

            if (isset($data['error'])) {
                throw new \Exception($data['error']);
            }

            if (! $process->isSuccessful()) {
                throw new \Exception($errorOutput ?: 'Terjadi kesalahan internal pada script.');
            }

            return $data;

        } catch (\Exception $exception) {
            $errorOutput = $exception->getMessage();

            // FALLBACK UNTUK TESTING UI JIKA PYTHON BELUM DIINSTALL:
            if (str_contains(strtolower($errorOutput), 'not recognized') || str_contains(strtolower($errorOutput), 'tidak dikenali') || str_contains(strtolower($errorOutput), 'cannot find the file')) {
                return [
                    'current_job' => 'Senior Developer (MOCK - PYTHON NOT INSTALLED)',
                    'current_company' => 'PT Teknologi Masa Depan',
                    'industry' => 'Information Technology',
                    'location' => 'Jakarta Raya, Indonesia',
                    'raw_output' => 'Data ini adalah mock dari Laravel karena perintah "python" tidak ditemukan di sistem Anda.',
                ];
            }

            Log::error('LinkedIn MCP Process Failed: '.$errorOutput);
            throw new \Exception('Gagal terhubung ke sistem LinkedIn. Detail: '.$errorOutput);
        }
    }
}
