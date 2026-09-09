<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\DataAkademik;
use App\Models\Prodi;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use App\Models\Response;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AlumniKuesionerMultipleNumberTest extends TestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected Alumni $alumni;

    protected Questionnaire $questionnaire;

    protected QuestionSection $section;

    protected Question $questionF13;

    protected QuestionOption $opt1;

    protected QuestionOption $opt2;

    protected QuestionOption $opt3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'username' => '71190001',
            'name' => 'Alumni Test',
            'email' => 'alumni@example.com',
            'password' => Hash::make('password123'),
            'role' => 'alumni',
            'must_change_password' => false,
        ]);

        $prodi = Prodi::create([
            'kode_prodi' => '71',
            'nama_prodi' => 'Informatika',
            'jenjang' => 'S1',
        ]);

        DataAkademik::create([
            'nim' => '71190001',
            'nama' => 'Alumni Test',
            'status_mahasiswa' => 'Lulus',
        ]);

        $this->alumni = Alumni::create([
            'user_id' => $this->user->id,
            'prodi_id' => $prodi->id,
            'nim' => '71190001',
            'expert' => 'Web Development',
            'minat' => 'Cloud Computing',
        ]);

        $this->questionnaire = Questionnaire::create([
            'title' => 'Tracer Study Test',
            'year' => 2026,
            'is_active' => true,
        ]);

        $this->section = QuestionSection::create([
            'questionnaire_id' => $this->questionnaire->id,
            'title' => 'Karakteristik Pekerjaan Saat Ini',
            'order' => 7,
        ]);

        $this->questionF13 = Question::create([
            'question_section_id' => $this->section->id,
            'code' => 'F13',
            'question_text' => 'Kira-kira berapa pendapatan anda setiap bulannya?',
            'type' => 'multiple_number',
            'is_required' => true,
            'order' => 1,
        ]);

        $this->opt1 = QuestionOption::create([
            'question_id' => $this->questionF13->id,
            'code' => 'F13-01',
            'option_text' => 'Dari Pekerjaan Utama',
            'order' => 1,
        ]);

        $this->opt2 = QuestionOption::create([
            'question_id' => $this->questionF13->id,
            'code' => 'F13-02',
            'option_text' => 'Dari Lembur dan Tips',
            'order' => 2,
        ]);

        $this->opt3 = QuestionOption::create([
            'question_id' => $this->questionF13->id,
            'code' => 'F13-03',
            'option_text' => 'Dari Pekerjaan Lainnya',
            'order' => 3,
        ]);
    }

    /**
     * Memverifikasi penyimpanan jawaban multiple_number dengan input ribuan:
     * - Nilai input ribuan dikonversi menjadi Rupiah penuh (x 1000).
     * - Total salary dihitung akumulatif dan disimpan pada answer_json['total'].
     * - answer_text menyertakan rincian opsi dan Total Pendapatan.
     */
    public function test_can_store_multiple_number_with_total_and_thousands_conversion(): void
    {
        $payload = [
            'answers' => [
                $this->questionF13->id => [
                    'F13-01' => 6500, // 6.500.000
                    'F13-02' => 750,  // 750.000
                    'F13-03' => 0,    // 0
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/alumni/kuesioner', $payload);
        $response->assertStatus(302);

        $saved = Response::where('alumni_id', $this->alumni->id)
            ->where('question_id', $this->questionF13->id)
            ->first();

        $this->assertNotNull($saved);
        $this->assertEquals(6500000, $saved->answer_json['F13-01']);
        $this->assertEquals(750000, $saved->answer_json['F13-02']);
        $this->assertEquals(0, $saved->answer_json['F13-03']);
        $this->assertEquals(7250000, $saved->answer_json['total']);

        $this->assertStringContainsString('Dari Pekerjaan Utama: Rp 6.500.000', $saved->answer_text);
        $this->assertStringContainsString('Dari Lembur dan Tips: Rp 750.000', $saved->answer_text);
        $this->assertStringContainsString('Total Pendapatan: Rp 7.250.000', $saved->answer_text);
    }

    /**
     * Memverifikasi jika alumni mengubah nilai gaji, total salary di database ikut terupdate.
     */
    public function test_updating_multiple_number_recalculates_total(): void
    {
        // Simpan jawaban awal
        $this->actingAs($this->user)->post('/alumni/kuesioner', [
            'answers' => [
                $this->questionF13->id => [
                    'F13-01' => 5000,
                    'F13-02' => 500,
                    'F13-03' => 0,
                ],
            ],
        ]);

        $saved1 = Response::where('alumni_id', $this->alumni->id)
            ->where('question_id', $this->questionF13->id)
            ->first();
        $this->assertEquals(5500000, $saved1->answer_json['total']);

        // Update nominal F13-01 naik menjadi 7000 (Rp 7.000.000) dan F13-03 menjadi 1000 (Rp 1.000.000)
        $this->actingAs($this->user)->post('/alumni/kuesioner', [
            'answers' => [
                $this->questionF13->id => [
                    'F13-01' => 7000,
                    'F13-02' => 500,
                    'F13-03' => 1000,
                ],
            ],
        ]);

        $saved2 = Response::where('alumni_id', $this->alumni->id)
            ->where('question_id', $this->questionF13->id)
            ->first();

        $this->assertEquals(8500000, $saved2->answer_json['total']);
        $this->assertStringContainsString('Total Pendapatan: Rp 8.500.000', $saved2->answer_text);
    }

    /**
     * Memverifikasi kuesioner mengirimkan nilai dalam satuan ribuan ke frontend saat dimuat kembali.
     */
    public function test_loading_kuesioner_normalizes_multiple_number_to_thousands(): void
    {
        // Simpan jawaban ke DB dengan nominal penuh
        Response::create([
            'alumni_id' => $this->alumni->id,
            'question_id' => $this->questionF13->id,
            'answer_text' => 'Dari Pekerjaan Utama: Rp 6.000.000, Dari Lembur dan Tips: Rp 500.000, Dari Pekerjaan Lainnya: Rp 0, Total Pendapatan: Rp 6.500.000',
            'answer_json' => [
                'F13-01' => 6000000,
                'F13-02' => 500000,
                'F13-03' => 0,
                'total' => 6500000,
            ],
        ]);

        $response = $this->actingAs($this->user)->get('/alumni/kuesioner');
        $response->assertStatus(200);
        $response->assertInertia(function ($page) {
            $initialAnswers = $page->toArray()['props']['initialAnswers'];
            $qAnswers = $initialAnswers[$this->questionF13->id];

            $this->assertEquals(6000, $qAnswers['F13-01']);
            $this->assertEquals(500, $qAnswers['F13-02']);
            $this->assertEquals(0, $qAnswers['F13-03']);
        });
    }

    /**
     * Memverifikasi proteksi jika alumni menginput angka nominal penuh (>= 1.000.000)
     * sistem tidak mengalikan ulang dengan 1000 (mencegah kesalahan menjadi miliaran).
     */
    public function test_full_nominal_input_is_protected_from_double_multiplication(): void
    {
        $payload = [
            'answers' => [
                $this->questionF13->id => [
                    'F13-01' => 5000000, // Alumni mengetik 5.000.000 langsung
                    'F13-02' => 0,
                    'F13-03' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post('/alumni/kuesioner', $payload);
        $response->assertStatus(302);

        $saved = Response::where('alumni_id', $this->alumni->id)
            ->where('question_id', $this->questionF13->id)
            ->first();

        $this->assertEquals(5000000, $saved->answer_json['F13-01']);
        $this->assertEquals(5000000, $saved->answer_json['total']);
    }
}
