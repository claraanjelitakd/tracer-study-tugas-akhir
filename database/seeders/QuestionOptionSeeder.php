<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

class QuestionOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Catatan Arsitektur Branching / Jump Logic:
     * Seluruh alur percabangan kuesioner dikelola secara modular pada kolom 'jump_to' di tabel 'question_options'.
     * - Jika jump_to diisi kode pertanyaan (misal 'F8', 'F11', 'F17-1'), alumni yang memilih opsi tersebut
     *   akan diarahkan langsung ke pertanyaan target, dan pertanyaan di antaranya akan dilewati (skipped).
     * - Jika jump_to bernilai null, alur berjalan normal ke nomor pertanyaan berikutnya.
     */
    public function run(): void
    {
        $options = [
            // F2D1 (Khusus Teologi / Filsafat Keilahian)
            ['question_code' => 'F2D1', 'code' => 'F2D1-01', 'option_text' => 'Gerejawi'],
            ['question_code' => 'F2D1', 'code' => 'F2D1-02', 'option_text' => 'Non Gerejawi'],

            // F2G
            ['question_code' => 'F2G', 'code' => 'F2G-01', 'option_text' => 'Direksi / Top Manager'],
            ['question_code' => 'F2G', 'code' => 'F2G-02', 'option_text' => 'Midle Manager'],
            ['question_code' => 'F2G', 'code' => 'F2G-03', 'option_text' => 'Low manager'],
            ['question_code' => 'F2G', 'code' => 'F2G-04', 'option_text' => 'Supervisor'],
            ['question_code' => 'F2G', 'code' => 'F2G-05', 'option_text' => 'Staff'],

            // F2H
            ['question_code' => 'F2H', 'code' => 'F2H-01', 'option_text' => 'Lokal'],
            ['question_code' => 'F2H', 'code' => 'F2H-02', 'option_text' => 'Nasional'],
            ['question_code' => 'F2H', 'code' => 'F2H-03', 'option_text' => 'Internasional'],

            // F3 (Jika tidak mencari kerja, langsung lompat ke F8)
            ['question_code' => 'F3', 'code' => 'F3-01', 'option_text' => 'Kira-kira ... bulan sebelum lulus', 'jump_to' => null],
            ['question_code' => 'F3', 'code' => 'F3-02', 'option_text' => 'Kira-kira ... bulan sesudah lulus', 'jump_to' => null],
            ['question_code' => 'F3', 'code' => 'F3-03', 'option_text' => 'Saya tidak mencari kerja', 'jump_to' => 'F8'],

            // F4
            ['question_code' => 'F4', 'code' => 'F4-01', 'option_text' => 'Melalui iklan di koran/majalah, brosur', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-02', 'option_text' => 'Melamar ke perusahaan tanpa mengetahui lowongan yang ada', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-03', 'option_text' => 'Pergi ke bursa/pameran kerja', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-04', 'option_text' => 'Mencari lewat internet/iklan online/milis', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-05', 'option_text' => 'Dihubungi oleh perusahaan', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-06', 'option_text' => 'Menghubungi Kemenakertrans', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-07', 'option_text' => 'Menghubungi agen tenaga kerja komersial/swasta', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-08', 'option_text' => 'Memeroleh informasi dari pusat/kantor pengembangan karir fakultas/universitas', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-09', 'option_text' => 'Menghubungi kantor kemahasiswaan/hubungan alumni', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-10', 'option_text' => 'Membangun jejaring (network) sejak masih kuliah', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-11', 'option_text' => 'Melalui relasi (misalnya dosen, orang tua, saudara, teman, dll.)', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-12', 'option_text' => 'Membangun bisnis sendiri', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-13', 'option_text' => 'Melalui penempatan kerja atau magang', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-14', 'option_text' => 'Bekerja di tempat yang sama dengan tempat kerja semasa kuliah', 'jump_to' => null],
            ['question_code' => 'F4', 'code' => 'F4-15', 'option_text' => 'Lainnya', 'jump_to' => null],

            // F5
            ['question_code' => 'F5', 'code' => 'F5-01', 'option_text' => 'Kira-kira ... bulan sebelum lulus ujian', 'jump_to' => null],
            ['question_code' => 'F5', 'code' => 'F5-02', 'option_text' => 'Kira-kira ... bulan setelah lulus ujian', 'jump_to' => null],

            // F8
            ['question_code' => 'F8', 'code' => 'F8-01', 'option_text' => 'Ya', 'jump_to' => 'F11'],
            ['question_code' => 'F8', 'code' => 'F8-02', 'option_text' => 'Tidak', 'jump_to' => 'F9'],

            // F9
            ['question_code' => 'F9', 'code' => 'F9-01', 'option_text' => 'Saya masih belajar/melanjutkan kuliah profesi atau pascasarjana'],
            ['question_code' => 'F9', 'code' => 'F9-02', 'option_text' => 'Saya menikah'],
            ['question_code' => 'F9', 'code' => 'F9-03', 'option_text' => 'Saya sibuk dengan keluarga dan anak-anak'],
            ['question_code' => 'F9', 'code' => 'F9-04', 'option_text' => 'Saya sekarang sedang mencari pekerjaan'],
            ['question_code' => 'F9', 'code' => 'F9-05', 'option_text' => 'Lainnya'],

            // F10
            ['question_code' => 'F10', 'code' => 'F10-01', 'option_text' => 'Tidak', 'jump_to' => 'F17-1'],
            ['question_code' => 'F10', 'code' => 'F10-02', 'option_text' => 'Tidak, tapi saya sedang menunggu hasil lamaran kerja', 'jump_to' => 'F17-1'],
            ['question_code' => 'F10', 'code' => 'F10-03', 'option_text' => 'Ya, saya akan mulai bekerja dalam 2 minggu ke depan', 'jump_to' => null],
            ['question_code' => 'F10', 'code' => 'F10-04', 'option_text' => 'Ya, tapi saya belum pasti akan bekerja dalam 2 minggu ke depan', 'jump_to' => null],
            ['question_code' => 'F10', 'code' => 'F10-05', 'option_text' => 'Lainnya, tuliskan', 'jump_to' => 'F17-1'],

            // F11
            ['question_code' => 'F11', 'code' => 'F11-01', 'option_text' => 'Instansi pemerintah (termasuk BUMN)'],
            ['question_code' => 'F11', 'code' => 'F11-02', 'option_text' => 'Organisasi non-profit/Lembaga Swadaya Masyarakat'],
            ['question_code' => 'F11', 'code' => 'F11-03', 'option_text' => 'Perusahaan swasta'],
            ['question_code' => 'F11', 'code' => 'F11-04', 'option_text' => 'Wiraswasta/perusahaan sendiri'],
            ['question_code' => 'F11', 'code' => 'F11-05', 'option_text' => 'Lainnya, tuliskan'],

            // F13
            ['question_code' => 'F13', 'code' => 'F13-01', 'option_text' => 'Dari Pekerjaan Utama'],
            ['question_code' => 'F13', 'code' => 'F13-02', 'option_text' => 'Dari Lembur dan Tips'],
            ['question_code' => 'F13', 'code' => 'F13-03', 'option_text' => 'Dari Pekerjaan Lainnya'],

            // F14
            ['question_code' => 'F14', 'code' => 'F14-01', 'option_text' => 'Sangat Erat'],
            ['question_code' => 'F14', 'code' => 'F14-02', 'option_text' => 'Erat'],
            ['question_code' => 'F14', 'code' => 'F14-03', 'option_text' => 'Cukup Erat'],
            ['question_code' => 'F14', 'code' => 'F14-04', 'option_text' => 'Kurang Erat'],
            ['question_code' => 'F14', 'code' => 'F14-05', 'option_text' => 'Tidak Sama Sekali'],

            // F15
            ['question_code' => 'F15', 'code' => 'F15-01', 'option_text' => 'Setingkat Lebih Tinggi'],
            ['question_code' => 'F15', 'code' => 'F15-02', 'option_text' => 'Tingkat yang Sama'],
            ['question_code' => 'F15', 'code' => 'F15-03', 'option_text' => 'Setingkat Lebih Rendah'],
            ['question_code' => 'F15', 'code' => 'F15-04', 'option_text' => 'Tidak Perlu Pendidikan Tinggi'],

            // F16
            ['question_code' => 'F16', 'code' => 'F16-01', 'option_text' => 'Pertanyaan tidak sesuai; pekerjaan saya sekarang sudah sesuai dengan pendidikan saya.'],
            ['question_code' => 'F16', 'code' => 'F16-02', 'option_text' => 'Saya belum mendapatkan pekerjaan yang lebih sesuai.'],
            ['question_code' => 'F16', 'code' => 'F16-03', 'option_text' => 'Di pekerjaan ini saya memeroleh prospek karir yang baik.'],
            ['question_code' => 'F16', 'code' => 'F16-04', 'option_text' => 'Saya lebih suka bekerja di area pekerjaan yang tidak ada hubungannya dengan pendidikan saya.'],
            ['question_code' => 'F16', 'code' => 'F16-05', 'option_text' => 'Saya dipromosikan ke posisi yang kurang berhubungan dengan pendidikan saya dibanding posisi sebelumnya.'],
            ['question_code' => 'F16', 'code' => 'F16-06', 'option_text' => 'Saya dapat memeroleh pendapatan yang lebih tinggi di pekerjaan ini.'],
            ['question_code' => 'F16', 'code' => 'F16-07', 'option_text' => 'Pekerjaan saya saat ini lebih aman/terjamin/secure.'],
            ['question_code' => 'F16', 'code' => 'F16-08', 'option_text' => 'Pekerjaan saya saat ini lebih menarik.'],
            ['question_code' => 'F16', 'code' => 'F16-09', 'option_text' => 'Pekerjaan saya saat ini lebih memungkinkan saya mengambil pekerjaan tambahan/jadwal yang fleksibel, dll.'],
            ['question_code' => 'F16', 'code' => 'F16-10', 'option_text' => 'Pekerjaan saya saat ini lokasinya lebih dekat dari rumah saya.'],
            ['question_code' => 'F16', 'code' => 'F16-11', 'option_text' => 'Pekerjaan saya saat ini dapat lebih menjamin kebutuhan keluarga saya.'],
            ['question_code' => 'F16', 'code' => 'F16-12', 'option_text' => 'Pada awal meniti karir ini, saya harus menerima pekerjaan yang tidak berhubungan dengan pendidikan saya.'],
            ['question_code' => 'F16', 'code' => 'F16-13', 'option_text' => 'Lainnya'],

            // F18
            ['question_code' => 'F18', 'code' => 'F18-01', 'option_text' => '<25%'],
            ['question_code' => 'F18', 'code' => 'F18-02', 'option_text' => '>25% - 50%'],
            ['question_code' => 'F18', 'code' => 'F18-03', 'option_text' => '>50%'],

            // F23
            ['question_code' => 'F23', 'code' => 'F23-01', 'option_text' => 'Bekerja dan menyelesaikan tugas dan tanggung jawab dengan baik'],
            ['question_code' => 'F23', 'code' => 'F23-02', 'option_text' => 'Berkolaborasi dengan rekan kerja dan menghargai keragaman dalam kerja tim'],
            ['question_code' => 'F23', 'code' => 'F23-03', 'option_text' => 'Memiliki kedisiplinan diri dan etos kerja yang baik'],
            ['question_code' => 'F23', 'code' => 'F23-04', 'option_text' => 'Menjadi pribadi yang proaktif dan kreatif di lingkungan kerja'],
            ['question_code' => 'F23', 'code' => 'F23-05', 'option_text' => 'Menunjukkan empati pada rekan kerja dan masyarakat'],
            ['question_code' => 'F23', 'code' => 'F23-06', 'option_text' => 'Lainnya (sebutkan, misal menjadi pengurus keagamaan)'],
        ];

        // F12 (89 Opsi KBLI)
        $f12_kbli = [
            'Pertanian tanaman peternakan perburuan dan kegiatan yang berhubungan dengan itu',
            'Kehutanan dan penebangan kayu',
            'Perikanan',
            'Pertambangan batu bara dan lignit',
            'Pertambangan minyak bumi dan gas alam dan panas bumi',
            'Pertambangan bijih logam',
            'Pertambangan dan penggalian lainnya',
            'Jasa pertambangan',
            'Industri makanan',
            'Industri minuman',
            'Industri pengolahan tembakau',
            'Industri tekstil',
            'Industri pakaian jadi',
            'Industri kulit barang dari kulit dan alas kaki',
            'Industri kayu barang dari kayu dan gabus (bukan furnitur) dan barang anyaman dari bamboo rotan dan sejenisnya',
            'Industri kertas dan barang dari kertas',
            'Industri pencetakan dan reproduksi media rekaman',
            'Industri produk dari batu bara dan pengilangan minyak bumi',
            'Industri bahan kimia dan barang dari bahan kimia',
            'Industri farmasi produk obat kimia dan obat tradisional',
            'Industri karet barang dari karet dan plastik',
            'Industri barang galian bukan logam',
            'Industri logam dasar',
            'Industri barang logam bukan mesin dan peralatannya',
            'Industri computer barang elektronik dan optik',
            'Industri peralatan listrik',
            'Industri mesin dan perlengkapan ytdl',
            'Industri kendaraan bermotor trailer dan semi trailer',
            'Industri alat angkutan lainnya',
            'Industri furnitur',
            'Industri pengolahan lainnya',
            'Jasa reparasi dan pemasangan mesin dan peralatan',
            'Pengadaan listrik gas uap/air panas dan udara dingin',
            'Pengadaan air',
            'Pengolahan limbah',
            'Pengolahan sampah dan daur ulang',
            'Jasa pembersihan dan pengelolaan sampah lainnya',
            'Konstruksi gedung',
            'Konstruksi bangunan sipil',
            'Konstruksi khusus',
            'Perdagangan reparasi dan perawatan mobil dan spd motor',
            'Perdagangan besar bukan mobil dan sepeda motor',
            'Perdagangan eceran bukan mobil dan motor',
            'Angkutan darat, angkutan melalui saluran pipa',
            'Angkutan udara',
            'Pergudangan dan jasa penunjang angkutan',
            'Pos dan kurir',
            'Penyediaan akomodasi',
            'Penyediaan makanan dan minuman',
            'Penerbitan',
            'Produksi gambar bergerak video dan program televisi perekaman suara dan penerbitan musik',
            'Penyiaran dan pemrograman',
            'Telekomunikasi',
            'Kegiatan pemrograman konsultasi komputer dan kegiatan yang berhubungan dengan itu',
            'Kegiatan jasa informasi',
            'Jasa keuangan bukan asuransi dan dana pensiun',
            'Asuransi reasuransi,dana pension bkn jaminan sosial wajib',
            'Jasa penunjang jasa keuangan asuransi dan dana pensiun',
            'Real estat',
            'Jasa hukum dan akuntansi',
            'Kegiatan kantor pusat dan konsultasi manajemen',
            'Jasa arsitektur dan teknik sipil; analisis dan uji teknis',
            'Penelitian dan pengembangan ilmu pengetahuan',
            'Periklanan dan penelitian pasar',
            'Jasa professional ilmiah dan teknis lainnya',
            'Jasa kesehatan hewan',
            'Jasa persewaan dan sewa guna usaha tanpa hak opsi',
            'Jasa ketenagakerjaan',
            'Jasa agen perjalanan, tur dan jasa reservasi lainnya',
            'Jasa keamanan dan penyelidikan',
            'Jasa untuk gedung dan pertamanan',
            'Jasa administrasi kantor jasa penunjang kantor dan jasa penunjang usaha lainnya',
            'Administrasi pemerintahan pertahanan, jaminan sosial wajib',
            'Jasa pendidikan',
            'Jasa kesehatan manusia',
            'Jasa kegiatan sosial di dalam panti',
            'Jasa kegiatan sosial di luar panti',
            'Kegiatan hiburan kesenian dan kreativitas',
            'Perpustakaan arsip museum, kegiatan kebudayaan lainnya',
            'Kegiatan perjudian dan pertaruhan',
            'Kegiatan olahraga dan rekreasi lainnya',
            'Kegiatan keanggotaan organisasi',
            'Jasa reparasi komputer dan barang keperluan pribadi dan perlengkapan rumah tangga',
            'Jasa perorangan lainnya',
            'Jasa perorangan yang melayani rumah tangga',
            'Kegiatan yang menghasilkan barang dan jasa oleh rumah tangga yang digunakan sendiri untuk memenuhi kebutuhan',
            'Kegiatan badan internasional,badan ekstra internasional',
            'Angkutan air',
            'Pendeta / Pastur / Rohaniwan',
        ];

        foreach ($f12_kbli as $index => $aspect) {
            $options[] = [
                'question_code' => 'F12',
                'code' => 'F12-'.str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                'option_text' => $aspect,
            ];
        }

        // F17-1 sampai F17-54 (Rating 1-5)
        $f17Ratings = [
            ['code' => '01', 'option_text' => 'Sangat Rendah'],
            ['code' => '02', 'option_text' => 'Rendah'],
            ['code' => '03', 'option_text' => 'Cukup'],
            ['code' => '04', 'option_text' => 'Tinggi'],
            ['code' => '05', 'option_text' => 'Sangat Tinggi'],
        ];

        for ($i = 1; $i <= 54; $i++) {
            foreach ($f17Ratings as $r) {
                $options[] = [
                    'question_code' => "F17-{$i}",
                    'code' => "F17-{$i}-{$r['code']}",
                    'option_text' => $r['option_text'],
                ];
            }
        }

        // F19-1 sampai F19-6 (Rating 1-5)
        $f19Ratings = [
            ['code' => '1', 'option_text' => 'Tidak Sama Sekali'],
            ['code' => '2', 'option_text' => '2'],
            ['code' => '3', 'option_text' => '3'],
            ['code' => '4', 'option_text' => '4'],
            ['code' => '5', 'option_text' => 'Sangat Besar'],
        ];
        for ($i = 1; $i <= 6; $i++) {
            foreach ($f19Ratings as $r) {
                $options[] = [
                    'question_code' => "F19-{$i}",
                    'code' => "F19-{$i}-{$r['code']}",
                    'option_text' => $r['option_text'],
                ];
            }
        }

        // F20-1 sampai F20-5 (Rating 1-5, F20-6 adalah text input)
        $f20Ratings = [
            ['code' => '1', 'option_text' => 'Sangat Buruk'],
            ['code' => '2', 'option_text' => '2'],
            ['code' => '3', 'option_text' => '3'],
            ['code' => '4', 'option_text' => '4'],
            ['code' => '5', 'option_text' => 'Sangat Baik'],
        ];
        for ($i = 1; $i <= 5; $i++) {
            foreach ($f20Ratings as $r) {
                $options[] = [
                    'question_code' => "F20-{$i}",
                    'code' => "F20-{$i}-{$r['code']}",
                    'option_text' => $r['option_text'],
                ];
            }
        }

        // F21-1 sampai F21-9 (Rating 1-5)
        for ($i = 1; $i <= 9; $i++) {
            foreach ($f20Ratings as $r) {
                $options[] = [
                    'question_code' => "F21-{$i}",
                    'code' => "F21-{$i}-{$r['code']}",
                    'option_text' => $r['option_text'],
                ];
            }
        }

        // F22-1 sampai F22-7 (Rating 1-5)
        for ($i = 1; $i <= 7; $i++) {
            foreach ($f20Ratings as $r) {
                $options[] = [
                    'question_code' => "F22-{$i}",
                    'code' => "F22-{$i}-{$r['code']}",
                    'option_text' => $r['option_text'],
                ];
            }
        }

        // F24A: Sumber dana pembiayaan kuliah S1 di UKDW (Wajib)
        $f24aList = [
            ['code' => 'F24A-01', 'option_text' => 'Biaya Sendiri / Keluarga'],
            ['code' => 'F24A-02', 'option_text' => 'Beasiswa ADIK'],
            ['code' => 'F24A-03', 'option_text' => 'Beasiswa BIDIKMISI'],
            ['code' => 'F24A-04', 'option_text' => 'Beasiswa PPA'],
            ['code' => 'F24A-05', 'option_text' => 'Beasiswa Afirmasi'],
            ['code' => 'F24A-06', 'option_text' => 'Beasiswa Perusahaan / Swasta'],
            ['code' => 'F24A-07', 'option_text' => 'Lainnya (Tuliskan)'],
        ];
        foreach ($f24aList as $item) {
            $options[] = [
                'question_code' => 'F24A',
                'code' => $item['code'],
                'option_text' => $item['option_text'],
            ];
        }

        // F24B: Sumber dana pembiayaan kuliah S2 Pascasarjana (Wajib)
        $f24bList = [
            ['code' => 'F24B-00', 'option_text' => 'Tidak melanjutkan S2'],
            ['code' => 'F24B-01', 'option_text' => 'Melanjutkan dengan biaya sendiri'],
            ['code' => 'F24B-02', 'option_text' => 'Melanjutkan dengan beasiswa'],
        ];
        foreach ($f24bList as $item) {
            $options[] = [
                'question_code' => 'F24B',
                'code' => $item['code'],
                'option_text' => $item['option_text'],
            ];
        }

        $orderCounter = 1;
        foreach ($options as $opt) {
            // Find question_id by code
            $q = Question::where('code', $opt['question_code'])->first();
            if ($q) {
                QuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'code' => $opt['code']],
                    [
                        'option_text' => $opt['option_text'],
                        'jump_to' => $opt['jump_to'] ?? null,
                        'order' => $orderCounter++,
                    ]
                );
            }
        }
    }
}
