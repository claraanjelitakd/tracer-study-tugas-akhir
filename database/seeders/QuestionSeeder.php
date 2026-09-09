<?php

namespace Database\Seeders;

use App\Models\Prodi;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari ID Prodi Filsafat Keilahian (Kode 31) untuk pertanyaan F2E spesifik prodi.
        // Pertanyaan lain bernilai prodi_id = null (berlaku untuk semua program studi).
        $prodiFilsafat = Prodi::where('kode_prodi', '31')->first();
        $prodiFilsafatId = $prodiFilsafat ? $prodiFilsafat->id : null;

        $questions = [
            // Identitas & Informasi Pribadi (Section 1)
            ['question_section_id' => 1, 'code' => 'F1', 'question_text' => 'Nomor Mahasiswa', 'type' => 'text', 'is_required' => true, 'order' => 1],
            ['question_section_id' => 1, 'code' => 'F2A', 'question_text' => 'Nama Mahasiswa', 'type' => 'text', 'is_required' => true, 'order' => 2],
            ['question_section_id' => 1, 'code' => 'F2B', 'question_text' => 'Nomor Telepon/HP', 'type' => 'text', 'is_required' => true, 'order' => 3],
            ['question_section_id' => 1, 'code' => 'F2C', 'question_text' => 'Alamat Email', 'type' => 'text', 'is_required' => true, 'order' => 4],
            ['question_section_id' => 1, 'code' => 'F2D', 'question_text' => 'Alamat Sekarang', 'type' => 'text', 'is_required' => true, 'order' => 5],

            // Status Pekerjaan & Perusahaan (Section 2)
            // Catatan: F2D1 khusus ditujukan untuk alumni Prodi Filsafat Keilahian (kode 31).
            ['question_section_id' => 2, 'prodi_id' => $prodiFilsafatId, 'code' => 'F2D1', 'question_text' => 'Jenis Pekerjaan Anda (Khusus Alumni Teologi)', 'type' => 'single_choice', 'is_required' => true, 'order' => 6],
            ['question_section_id' => 2, 'code' => 'F2E', 'question_text' => 'Nama Perusahaan/ Instansi/ Institusi', 'type' => 'text', 'is_required' => false, 'order' => 7],
            ['question_section_id' => 2, 'code' => 'F2E1', 'question_text' => 'Nama Atasan di Perusahaan/Instansi/Institusi tempat anda bekerja', 'type' => 'text', 'is_required' => false, 'order' => 8],
            ['question_section_id' => 2, 'code' => 'F2E2', 'question_text' => 'Nomor Telepon Atasan di Perusahaan/Instansi/Institusi tempat anda bekerja', 'type' => 'text', 'is_required' => false, 'order' => 9],
            ['question_section_id' => 2, 'code' => 'F2E3', 'question_text' => 'Email Atasan di Perusahaan/ Instansi/ Institusi tempat anda bekerja', 'type' => 'text', 'is_required' => false, 'order' => 10],
            ['question_section_id' => 2, 'code' => 'F2F', 'question_text' => 'Alamat Perusahaan/ Instansi/Institusi (Kabupaten, Provinsi, Alamat, Kode Pos)', 'type' => 'text', 'is_required' => false, 'order' => 11],
            ['question_section_id' => 2, 'code' => 'F2G', 'question_text' => 'Posisi Jabatan Anda Sekarang', 'type' => 'single_choice', 'is_required' => false, 'order' => 12],
            ['question_section_id' => 2, 'code' => 'F2H', 'question_text' => 'Skala Perusahaan/ Instansi/ Institusi', 'type' => 'single_choice', 'is_required' => false, 'order' => 13],

            // Pencarian Kerja (Section 3 & 4)
            ['question_section_id' => 3, 'code' => 'F3', 'question_text' => 'Kapan anda mulai mencari pekerjaan? Mohon pekerjaan sambilan tidak dimasukkan', 'type' => 'radio_input', 'is_required' => true, 'order' => 14],

            ['question_section_id' => 4, 'code' => 'F4', 'question_text' => 'Bagaimana anda mencari pekerjaan tersebut? Jawaban bisa lebih dari satu', 'type' => 'multiple_choice', 'is_required' => true, 'order' => 15],
            ['question_section_id' => 4, 'code' => 'F5', 'question_text' => 'Berapa bulan waktu yang dihabiskan (sebelum dan sesudah kelulusan) untuk memeroleh pekerjaan pertama?', 'type' => 'radio_input', 'is_required' => true, 'order' => 16],
            ['question_section_id' => 4, 'code' => 'F6', 'question_text' => 'Berapa perusahaan/ instansi/ institusi yang sudah anda lamar (lewat surat atau e-mail) sebelum anda memeroleh pekerjaan pertama?', 'type' => 'number', 'is_required' => true, 'order' => 17],
            ['question_section_id' => 4, 'code' => 'F7', 'question_text' => 'Berapa banyak perusahaan/ instansi/institusi yang merespons lamaran anda?', 'type' => 'number', 'is_required' => true, 'order' => 18],

            // Situasi Saat Ini (Section 5 & 6)
            ['question_section_id' => 5, 'code' => 'F8', 'question_text' => 'Apakah anda bekerja saat ini (termasuk kerja sambilan dan wirausaha)?', 'type' => 'single_choice', 'is_required' => true, 'order' => 19],

            ['question_section_id' => 6, 'code' => 'F9', 'question_text' => 'Bagaimana anda menggambarkan situasi anda saat ini? Jawaban bisa lebih dari satu', 'type' => 'multiple_choice', 'is_required' => true, 'order' => 20],
            ['question_section_id' => 6, 'code' => 'F10', 'question_text' => 'Apakah anda aktif mencari pekerjaan dalam 4 minggu terakhir? Pilihlah Satu Jawaban.', 'type' => 'single_choice', 'is_required' => true, 'order' => 21],

            // Karakteristik Pekerjaan Saat Ini (Section 7)
            ['question_section_id' => 7, 'code' => 'F11', 'question_text' => 'Apa jenis perusahaan/instansi/institusi tempat anda bekerja sekarang?', 'type' => 'single_choice', 'is_required' => true, 'order' => 22],
            ['question_section_id' => 7, 'code' => 'F12', 'question_text' => 'Tempat anda bekerja saat ini bergerak di bidang apa? (Klasifikasi Baku Lapangan Usaha Indonesia, Kemnakertrans, 2009)', 'type' => 'searchable_select', 'is_required' => true, 'order' => 23],
            ['question_section_id' => 7, 'code' => 'F13', 'question_text' => 'Kira-kira berapa pendapatan anda setiap bulannya?', 'type' => 'multiple_number', 'is_required' => true, 'order' => 24],
            ['question_section_id' => 7, 'code' => 'F14', 'question_text' => 'Seberapa erat hubungan antara bidang studi dengan pekerjaan anda?', 'type' => 'single_choice', 'is_required' => true, 'order' => 25],
            ['question_section_id' => 7, 'code' => 'F15', 'question_text' => 'Tingkat pendidikan apa yang paling tepat/ sesuai untuk pekerjaan anda saat ini?', 'type' => 'single_choice', 'is_required' => true, 'order' => 26],
            ['question_section_id' => 7, 'code' => 'F16', 'question_text' => 'Jika menurut anda pekerjaan anda saat ini tidak sesuai dengan pendidikan anda, mengapa anda mengambilnya? Jawaban bisa lebih dari satu', 'type' => 'multiple_choice', 'is_required' => false, 'order' => 27],
        ];

        // Section 8: Evaluasi Kompetensi (F17-1 sampai F17-54)
        $f17Aspects = [
            1 => 'Pengetahuan di bidang atau disiplin ilmu anda',
            2 => 'Pengetahuan di luar bidang atau disiplin ilmu anda',
            3 => 'Pengetahuan umum',
            4 => 'Ketrampilan internet',
            5 => 'Ketrampilan komputer',
            6 => 'Berpikir kritis',
            7 => 'Ketrampilan riset',
            8 => 'Kemampuan belajar',
            9 => 'Kemampuan berkomunikasi',
            10 => 'Bekerja di bawah tekanan',
            11 => 'Manajemen waktu',
            12 => 'Bekerja secara mandiri',
            13 => 'Bekerja dalam tim/bekerjasama dengan orang lain',
            14 => 'Kemampuan dalam memecahkan masalah',
            15 => 'Negosiasi',
            16 => 'Kemampuan analisis',
            17 => 'Toleransi',
            18 => 'Kemampuan adaptasi',
            19 => 'Loyalitas dan integritas',
            20 => 'Bekerja dengan orang yang berbeda budaya maupun latar belakang',
            21 => 'Kepemimpinan',
            22 => 'Kemampuan dalam memegang tanggungjawab',
            23 => 'Inisiatif',
            24 => 'Manajemen proyek/ program',
            25 => 'Kemampuan untuk memresentasikan ide/produk/laporan',
            26 => 'Kemampuan dalam menulis laporan, memo dan dokumen',
            27 => 'Kemampuan untuk terus belajar sepanjang hayat',
        ];

        $currentOrder = 28;
        foreach ($f17Aspects as $aspectNum => $aspectName) {
            $numA = ($aspectNum - 1) * 2 + 1;
            $numB = ($aspectNum - 1) * 2 + 2;

            $questions[] = [
                'question_section_id' => 8,
                'code' => "F17-{$numA}",
                'question_text' => "{$aspectName} — Kompetensi yang dikuasai",
                'type' => 'single_choice',
                'is_required' => true,
                'order' => $currentOrder++,
            ];
            $questions[] = [
                'question_section_id' => 8,
                'code' => "F17-{$numB}",
                'question_text' => "{$aspectName} — Kontribusi PT dalam Kompetensi",
                'type' => 'single_choice',
                'is_required' => true,
                'order' => $currentOrder++,
            ];
        }

        // Section 9: Kurikulum, Fasilitas & Nilai Kedutawacanaan (F18, F19-1..6, F20-1..6, F21-1..9, F22-1..7, F23)
        $questions[] = [
            'question_section_id' => 9,
            'code' => 'F18',
            'question_text' => 'Seberapa besar prosentase kesesuaian jumlah Mata Kuliah yang Anda ambil pada program S1 UKDW dengan bidang pekerjaan anda saat ini :',
            'type' => 'single_choice',
            'is_required' => true,
            'order' => $currentOrder++,
        ];

        // F19-1 sampai F19-6
        $f19Aspects = [
            1 => 'Perkuliahan',
            2 => 'Demonstrasi (peragaan)',
            3 => 'Partisipasi dalam proyek riset',
            4 => 'Magang',
            5 => 'Praktikum/kerja lapangan',
            6 => 'Diskusi',
        ];
        foreach ($f19Aspects as $num => $aspect) {
            $questions[] = [
                'question_section_id' => 9,
                'code' => "F19-{$num}",
                'question_text' => "Menurut Anda, seberapa besar manfaat yang didapatkan dari proses pembelajaran di UKDW berdasarkan aspek: {$aspect}?",
                'type' => 'single_choice',
                'is_required' => true,
                'order' => $currentOrder++,
            ];
        }

        // F20-1 sampai F20-6
        $f20Aspects = [
            1 => 'Kesempatan untuk berinteraksi dengan dosen-dosen di luar jadwal kuliah',
            2 => 'Pembimbingan Akademik',
            3 => 'Kesempatan berpartisipasi dalam proyek riset',
            4 => 'Kondisi umum belajar mengajar',
            5 => 'Kesempatan utk memasuki dan menjadi bagian dari jejaring ilmiah profesional',
        ];
        foreach ($f20Aspects as $num => $aspect) {
            $questions[] = [
                'question_section_id' => 9,
                'code' => "F20-{$num}",
                'question_text' => "Bagaimana penilaian anda, terhadap aspek belajar: {$aspect}?",
                'type' => 'single_choice',
                'is_required' => true,
                'order' => $currentOrder++,
            ];
        }

        // F20-6 (Lainnya: Text Box)
        $questions[] = [
            'question_section_id' => 9,
            'code' => 'F20-6',
            'question_text' => 'Bagaimana penilaian anda, terhadap aspek belajar lainnya: Tuliskan',
            'type' => 'text',
            'is_required' => false,
            'order' => $currentOrder++,
        ];

        // F21-1 sampai F21-9
        $f21Aspects = [
            1 => 'Perpustakaan',
            2 => 'Teknologi Informasi dan Komunikasi',
            3 => 'Modul Belajar',
            4 => 'Ruang belajar',
            5 => 'Laboratorium',
            6 => 'Akomodasi',
            7 => 'Kantin',
            8 => 'Pusat kegiatan mahasiswa dan fasilitasnya, ruang rekreasi',
            9 => 'Fasilitas layanan kesehatan',
        ];
        foreach ($f21Aspects as $num => $aspect) {
            $questions[] = [
                'question_section_id' => 9,
                'code' => "F21-{$num}",
                'question_text' => "Selama anda kuliah di UKDW, bagaimana pendapat anda terhadap kondisi fasilitas belajar: {$aspect}?",
                'type' => 'single_choice',
                'is_required' => true,
                'order' => $currentOrder++,
            ];
        }

        // F22-1 sampai F22-7
        $f22Aspects = [
            1 => 'Pembelajaran di kelas',
            2 => 'Magang/kerja lapangan/praktikum',
            3 => 'Pengabdian dan penjangkauan masyarakat',
            4 => 'Pelaksanaan riset/penulisan skripsi',
            5 => 'Organisasi kemahasiswaan',
            6 => 'Kegiatan ekstrakulikuler',
            7 => 'Rekreasi dan olahraga',
        ];
        foreach ($f22Aspects as $num => $aspect) {
            $questions[] = [
                'question_section_id' => 9,
                'code' => "F22-{$num}",
                'question_text' => "Bagaimana penilaian anda, terhadap pengalaman belajar: {$aspect}?",
                'type' => 'single_choice',
                'is_required' => true,
                'order' => $currentOrder++,
            ];
        }

        // F23: Penerapan Nilai-Nilai Kedutawacanaan
        $questions[] = [
            'question_section_id' => 9,
            'code' => 'F23',
            'question_text' => 'Bagaimana Anda menerapkan dan menghidupi nilai-nilai Kedutawacanaan yang Anda dapatkan selama kuliah di UKDW dalam menjalankan pekerjaan Anda saat ini?',
            'type' => 'multiple_choice',
            'is_required' => true,
            'order' => $currentOrder++,
        ];

        /**
         * F24: Studi Lanjut & Sumber Pembiayaan Kuliah (Standar Tracer Study Dikti)
         * - F24A: Sumber dana pembiayaan kuliah S1 di UKDW (Wajib diisi oleh seluruh alumni)
         * - F24B: Sumber dana pembiayaan kuliah pascasarjana/S2 (Wajib diisi; opsi 0 jika tidak melanjutkan S2)
         */
        $questions[] = [
            'question_section_id' => 9,
            'code' => 'F24A',
            'question_text' => 'Sebutkan sumberdana dalam pembiayaan kuliah S1 di UKDW :',
            'type' => 'single_choice',
            'is_required' => true,
            'order' => $currentOrder++,
        ];

        $questions[] = [
            'question_section_id' => 9,
            'code' => 'F24B',
            'question_text' => 'Jika Anda melanjutkan ke jenjang pascasarjana (S2), sebutkan sumberdana dalam pembiayaan kuliah S2 Anda :',
            'type' => 'single_choice',
            'is_required' => true,
            'order' => $currentOrder++,
        ];

        foreach ($questions as $q) {
            Question::updateOrCreate(
                ['code' => $q['code']],
                $q
            );
        }
    }
}
