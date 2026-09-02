<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionOptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            // F2E
            ['question_code' => 'F2E', 'code' => 'F2E-01', 'option_text' => 'Gerejawi'],
            ['question_code' => 'F2E', 'code' => 'F2E-02', 'option_text' => 'Non Gerejawi'],

            // F2G
            ['question_code' => 'F2G', 'code' => 'F2G-01', 'option_text' => 'Direksi / Top Manager'],
            ['question_code' => 'F2G', 'code' => 'F2G-02', 'option_text' => 'Middle Manager'],
            ['question_code' => 'F2G', 'code' => 'F2G-03', 'option_text' => 'Low Manager'],
            ['question_code' => 'F2G', 'code' => 'F2G-04', 'option_text' => 'Supervisor'],
            ['question_code' => 'F2G', 'code' => 'F2G-05', 'option_text' => 'Staff'],

            // F2H
            ['question_code' => 'F2H', 'code' => 'F2H-01', 'option_text' => 'Lokal'],
            ['question_code' => 'F2H', 'code' => 'F2H-02', 'option_text' => 'Nasional'],
            ['question_code' => 'F2H', 'code' => 'F2H-03', 'option_text' => 'Internasional'],

            // F3
            ['question_code' => 'F3', 'code' => 'F3-01', 'option_text' => 'Kira-kira ... bulan sebelum lulus'],
            ['question_code' => 'F3', 'code' => 'F3-02', 'option_text' => 'Kira-kira ... bulan sesudah lulus'],
            ['question_code' => 'F3', 'code' => 'F3-03', 'option_text' => 'Saya tidak mencari kerja (Jump ke F8)'],

            // F4
            ['question_code' => 'F4', 'code' => 'F4-01', 'option_text' => 'Iklan koran/majalah/brosur'],
            ['question_code' => 'F4', 'code' => 'F4-02', 'option_text' => 'Melamar tanpa tahu lowongan'],
            ['question_code' => 'F4', 'code' => 'F4-03', 'option_text' => 'Bursa/pameran kerja'],
            ['question_code' => 'F4', 'code' => 'F4-04', 'option_text' => 'Internet/iklan online/milis'],
            ['question_code' => 'F4', 'code' => 'F4-05', 'option_text' => 'Dihubungi perusahaan'],
            ['question_code' => 'F4', 'code' => 'F4-06', 'option_text' => 'Kemenakertrans'],
            ['question_code' => 'F4', 'code' => 'F4-07', 'option_text' => 'Agen tenaga kerja swasta'],
            ['question_code' => 'F4', 'code' => 'F4-08', 'option_text' => 'Pusat karir universitas'],
            ['question_code' => 'F4', 'code' => 'F4-09', 'option_text' => 'Kantor kemahasiswaan/alumni'],
            ['question_code' => 'F4', 'code' => 'F4-10', 'option_text' => 'Membangun jejaring/network kuliah'],
            ['question_code' => 'F4', 'code' => 'F4-11', 'option_text' => 'Relasi (dosen, orang tua, saudara, teman)'],
            ['question_code' => 'F4', 'code' => 'F4-12', 'option_text' => 'Membangun bisnis sendiri'],
            ['question_code' => 'F4', 'code' => 'F4-13', 'option_text' => 'Penempatan kerja/magang'],
            ['question_code' => 'F4', 'code' => 'F4-14', 'option_text' => 'Tempat kerja semasa kuliah'],
            ['question_code' => 'F4', 'code' => 'F4-15', 'option_text' => 'Lainnya'],

            // F5
            ['question_code' => 'F5', 'code' => 'F5-01', 'option_text' => 'Kira-kira ... bulan sebelum lulus'],
            ['question_code' => 'F5', 'code' => 'F5-02', 'option_text' => 'Kira-kira ... bulan sesudah lulus'],

            // F8
            ['question_code' => 'F8', 'code' => 'F8-01', 'option_text' => 'Ya (Jump ke F11)'],
            ['question_code' => 'F8', 'code' => 'F8-02', 'option_text' => 'Tidak (Lanjut ke F9)'],

            // F9
            ['question_code' => 'F9', 'code' => 'F9-01', 'option_text' => 'Masih belajar/kuliah profesi/pascasarjana'],
            ['question_code' => 'F9', 'code' => 'F9-02', 'option_text' => 'Menikah'],
            ['question_code' => 'F9', 'code' => 'F9-03', 'option_text' => 'Sibuk keluarga dan anak-anak'],
            ['question_code' => 'F9', 'code' => 'F9-04', 'option_text' => 'Sedang mencari pekerjaan'],
            ['question_code' => 'F9', 'code' => 'F9-05', 'option_text' => 'Lainnya'],

            // F10
            ['question_code' => 'F10', 'code' => 'F10-01', 'option_text' => 'Tidak'],
            ['question_code' => 'F10', 'code' => 'F10-02', 'option_text' => 'Tidak, tapi menunggu hasil lamaran'],
            ['question_code' => 'F10', 'code' => 'F10-03', 'option_text' => 'Ya, akan mulai bekerja 2 minggu ke depan (Jump ke F17)'],
            ['question_code' => 'F10', 'code' => 'F10-04', 'option_text' => 'Ya, tapi belum pasti bekerja 2 minggu ke depan (Jump ke F17)'],
            ['question_code' => 'F10', 'code' => 'F10-05', 'option_text' => 'Lainnya (aktif mencari) (Jump ke F17)'],

            // F11
            ['question_code' => 'F11', 'code' => 'F11-01', 'option_text' => 'Instansi pemerintah/BUMN'],
            ['question_code' => 'F11', 'code' => 'F11-02', 'option_text' => 'Organisasi non-profit / LSM'],
            ['question_code' => 'F11', 'code' => 'F11-03', 'option_text' => 'Perusahaan swasta'],
            ['question_code' => 'F11', 'code' => 'F11-04', 'option_text' => 'Wiraswasta / perusahaan sendiri'],
            ['question_code' => 'F11', 'code' => 'F11-05', 'option_text' => 'Lainnya'],

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
            ['question_code' => 'F16', 'code' => 'F16-01', 'option_text' => 'Pekerjaan sudah sesuai'],
            ['question_code' => 'F16', 'code' => 'F16-02', 'option_text' => 'Belum dapat kerja yang lebih sesuai'],
            ['question_code' => 'F16', 'code' => 'F16-03', 'option_text' => 'Prospek karir baik'],
            ['question_code' => 'F16', 'code' => 'F16-04', 'option_text' => 'Lebih suka area kerja tak berhubungan'],
            ['question_code' => 'F16', 'code' => 'F16-05', 'option_text' => 'Dipromosikan ke posisi kurang berhubungan'],
            ['question_code' => 'F16', 'code' => 'F16-06', 'option_text' => 'Pendapatan lebih tinggi'],
            ['question_code' => 'F16', 'code' => 'F16-07', 'option_text' => 'Lebih aman/secure'],
            ['question_code' => 'F16', 'code' => 'F16-08', 'option_text' => 'Lebih menarik'],
            ['question_code' => 'F16', 'code' => 'F16-09', 'option_text' => 'Jadwal fleksibel / kerja tambahan'],
            ['question_code' => 'F16', 'code' => 'F16-10', 'option_text' => 'Lokasi dekat rumah'],
            ['question_code' => 'F16', 'code' => 'F16-11', 'option_text' => 'Menjamin kebutuhan keluarga'],
            ['question_code' => 'F16', 'code' => 'F16-12', 'option_text' => 'Awal meniti karir terima kerja tak berhubungan'],
            ['question_code' => 'F16', 'code' => 'F16-13', 'option_text' => 'Lainnya'],

            // F18
            ['question_code' => 'F18', 'code' => 'F18-01', 'option_text' => '< 25%'],
            ['question_code' => 'F18', 'code' => 'F18-02', 'option_text' => '> 25% - 50%'],
            ['question_code' => 'F18', 'code' => 'F18-03', 'option_text' => '> 50%'],
        ];

        // F12
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
            'Pendeta / Pastur / Rohaniwan'
        ];

        foreach ($f12_kbli as $index => $aspect) {
            $options[] = [
                'question_code' => 'F12',
                'code' => 'F12-'.str_pad($index+1, 2, '0', STR_PAD_LEFT),
                'option_text' => $aspect
            ];
        }
        // Untuk F17 (Matrix Dual), options-nya adalah baris aspek (1-27)
        $f17_aspects = [
            'Pengetahuan bidang/disiplin ilmu', 'Pengetahuan di luar bidang ilmu', 'Pengetahuan umum',
            'Keterampilan internet', 'Keterampilan komputer', 'Berpikir kritis', 'Keterampilan riset',
            'Kemampuan belajar', 'Kemampuan berkomunikasi', 'Bekerja di bawah tekanan', 'Manajemen waktu',
            'Bekerja secara mandiri', 'Bekerja dalam tim', 'Memecahkan masalah', 'Negosiasi', 'Kemampuan analisis',
            'Toleransi', 'Kemampuan adaptasi', 'Loyalitas dan integritas', 'Bekerja dengan orang beda budaya',
            'Kepemimpinan', 'Memegang tanggung jawab', 'Inisiatif', 'Manajemen proyek/program',
            'Mempresentasikan ide/produk/laporan', 'Menulis laporan, memo, dokumen', 'Belajar sepanjang hayat'
        ];
        
        foreach ($f17_aspects as $index => $aspect) {
            $options[] = [
                'question_code' => 'F17',
                'code' => 'F17-'.($index+1),
                'option_text' => $aspect
            ];
        }

        // F19
        $f19_aspects = ['Perkuliahan', 'Demonstrasi (peragaan)', 'Partisipasi proyek riset', 'Magang', 'Praktikum / kerja lapangan', 'Diskusi'];
        foreach ($f19_aspects as $index => $aspect) {
            $options[] = ['question_code' => 'F19', 'code' => 'F19-'.($index+1), 'option_text' => $aspect];
        }

        // F20
        $f20_aspects = ['Perpustakaan', 'TIK', 'Modul Belajar', 'Ruang belajar', 'Laboratorium', 'Akomodasi', 'Kantin', 'Pusat kegiatan mahasiswa & rekreasi', 'Fasilitas layanan kesehatan'];
        foreach ($f20_aspects as $index => $aspect) {
            $options[] = ['question_code' => 'F20', 'code' => 'F20-'.($index+1), 'option_text' => $aspect];
        }

        // F21
        $f21_aspects = ['Pembelajaran di kelas', 'Magang / kerja lapangan / praktikum', 'Pengabdian dan penjangkauan masyarakat', 'Pelaksanaan riset / penulisan skripsi', 'Organisasi kemahasiswaan', 'Kegiatan ekstrakurikuler', 'Rekreasi dan olahraga'];
        foreach ($f21_aspects as $index => $aspect) {
            $options[] = ['question_code' => 'F21', 'code' => 'F21-'.($index+1), 'option_text' => $aspect];
        }

        // F22
        $f22_aspects = ['Interaksi dengan dosen di luar kuliah', 'Pembimbingan Akademik', 'Partisipasi dalam proyek riset', 'Kondisi umum belajar mengajar', 'Jejaring ilmiah profesional', 'Lainnya'];
        foreach ($f22_aspects as $index => $aspect) {
            $options[] = ['question_code' => 'F22', 'code' => 'F22-'.($index+1), 'option_text' => $aspect];
        }

        $orderCounter = 1;
        foreach ($options as $opt) {
            // Find question_id by code
            $q = \App\Models\Question::where('code', $opt['question_code'])->first();
            if ($q) {
                \App\Models\QuestionOption::updateOrCreate(
                    ['question_id' => $q->id, 'code' => $opt['code']],
                    [
                        'option_text' => $opt['option_text'],
                        'order' => $orderCounter++
                    ]
                );
            }
        }
    }
}
