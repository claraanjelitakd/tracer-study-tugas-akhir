<?php

namespace Tests\Feature;

use App\Models\Alumni;
use App\Models\DataAkademik;
use App\Models\Prodi;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SuperAdminDaftarAlumniTest extends TestCase
{
    use RefreshDatabase;

    protected User $superadmin;

    protected User $alumniUser;

    protected Alumni $alumni;

    protected Prodi $prodi;

    protected Questionnaire $questionnaire;

    protected QuestionSection $section;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = User::create([
            'username' => 'superadmin_test',
            'name' => 'Super Administrator Test',
            'password' => Hash::make('password123'),
            'role' => 'superadmin',
            'must_change_password' => false,
        ]);

        $this->prodi = Prodi::create([
            'kode_prodi' => '72',
            'nama_prodi' => 'Sistem Informasi',
        ]);

        $this->alumniUser = User::create([
            'username' => '72200001',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => Hash::make('password123'),
            'role' => 'alumni',
            'must_change_password' => false,
        ]);

        DataAkademik::create([
            'nim' => '72200001',
            'nama' => 'Budi Santoso',
            'angkatan_masuk' => '2020',
            'tahun_akademik_lulus' => 'Gasal 2024/2025',
            'tahun_lulus' => '2024',
            'ipk' => '3.85',
        ]);

        $this->alumni = Alumni::create([
            'user_id' => $this->alumniUser->id,
            'nim' => '72200001',
            'prodi_id' => $this->prodi->id,
            'posisi_jabatan' => 'Software Engineer',
        ]);

        $this->questionnaire = Questionnaire::create([
            'title' => 'Tracer Study 2026',
            'year' => 2026,
            'is_active' => true,
        ]);

        $this->section = QuestionSection::create([
            'questionnaire_id' => $this->questionnaire->id,
            'title' => 'Kurikulum, Fasilitas & Nilai Kedutawacanaan',
            'order' => 9,
        ]);

        Question::create([
            'question_section_id' => $this->section->id,
            'code' => 'F24A',
            'question_text' => 'Sebutkan sumberdana dalam pembiayaan kuliah S1 di UKDW :',
            'type' => 'single_choice',
            'is_required' => true,
            'order' => 1,
        ]);

        Question::create([
            'question_section_id' => $this->section->id,
            'code' => 'F24B',
            'question_text' => 'Jika Anda melanjutkan ke jenjang pascasarjana (S2), sebutkan sumberdana dalam pembiayaan kuliah S2 Anda :',
            'type' => 'single_choice',
            'is_required' => true,
            'order' => 2,
        ]);
    }

    /**
     * Test superadmin dapat membuka halaman direktori alumni.
     */
    public function test_superadmin_can_view_alumni_index_page(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.alumni.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('SuperAdmin/Alumni/Index')
            ->has('alumnis', 1)
            ->has('daftarTahun')
            ->has('prodis')
            ->has('filters')
            ->has('stats')
        );
    }

    /**
     * Test filter tahun dan semester kelulusan.
     */
    public function test_superadmin_can_filter_alumni_by_year_and_semester(): void
    {
        // Filter tahun yang cocok
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.alumni.index', ['tahun' => '2024/2025', 'semester' => 'Gasal']));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('SuperAdmin/Alumni/Index')
            ->has('alumnis', 1)
        );

        // Filter tahun yang tidak cocok
        $responseMismatch = $this->actingAs($this->superadmin)
            ->get(route('superadmin.alumni.index', ['tahun' => '2019/2020']));

        $responseMismatch->assertStatus(200);
        $responseMismatch->assertInertia(fn (Assert $page) => $page
            ->component('SuperAdmin/Alumni/Index')
            ->has('alumnis', 0)
        );
    }

    /**
     * Test superadmin dapat melihat halaman detail mahasiswa dengan F24.
     */
    public function test_superadmin_can_view_alumni_detail_page_with_questionnaire(): void
    {
        $response = $this->actingAs($this->superadmin)
            ->get(route('superadmin.alumni.show', $this->alumni->id));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('SuperAdmin/Alumni/Show')
            ->has('alumni')
            ->has('evaluasi')
            ->has('sections')
            ->where('sections.0.questions.0.code', 'F24A')
            ->where('sections.0.questions.0.is_mandatory', true)
            ->where('sections.0.questions.1.code', 'F24B')
            ->where('sections.0.questions.1.is_mandatory', true)
        );
    }

    /**
     * Test pengguna tanpa role superadmin diblokir.
     */
    public function test_non_superadmin_cannot_access_alumni_pages(): void
    {
        $response = $this->actingAs($this->alumniUser)
            ->get(route('superadmin.alumni.index'));

        $response->assertStatus(403);
    }

    /**
     * Test superadmin dapat memperbarui data profil alumni.
     */
    public function test_superadmin_can_update_alumni_profile(): void
    {
        $payload = [
            'nama' => 'Budi Santoso Diperbarui',
            'nomor_telepon' => '081234567890',
            'posisi_jabatan' => 'Lead Engineer',
            'nama_perusahaan' => 'PT Teknologi Unggul',
            'company_alamat' => 'Jl. Solo KM 10',
            'company_skala' => 'Nasional',
            'nama_atasan' => 'Pak Direktur',
            'email_atasan' => 'direktur@example.com',
            'telepon_atasan' => '081111222333',
        ];

        $response = $this->actingAs($this->superadmin)
            ->post(route('superadmin.alumni.profile.update', $this->alumni->id), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('data_akademiks', [
            'nim' => '72200001',
            'nama' => 'Budi Santoso Diperbarui',
            'nomor_telepon' => '081234567890',
        ]);
        $this->assertDatabaseHas('alumnis', [
            'id' => $this->alumni->id,
            'posisi_jabatan' => 'Lead Engineer',
        ]);
        $this->assertDatabaseHas('companies', [
            'nama_perusahaan' => 'PT Teknologi Unggul',
            'alamat' => 'Jl. Solo KM 10',
        ]);
    }
}
