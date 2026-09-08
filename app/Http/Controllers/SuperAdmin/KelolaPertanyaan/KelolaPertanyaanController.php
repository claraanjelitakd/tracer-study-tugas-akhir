<?php

namespace App\Http\Controllers\SuperAdmin\KelolaPertanyaan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionSection;
use App\Models\Questionnaire;
use App\Models\Prodi;

/**
 * Class KelolaPertanyaanController (SuperAdmin)
 * 
 * Fungsi:
 * Controller untuk modul pengelolaan instrumen kuesioner Tracer Study oleh Superadmin.
 * Memungkinkan Superadmin untuk mengatur butir pertanyaan, opsi jawaban, alur percabangan (jump logic),
 * serta scoping sasaran program studi secara terintegrasi.
 * 
 * Fitur Utama:
 * 1. Tampilan instrumen kuesioner per-bagian (section) bergaya Google Forms.
 * 2. Manajemen CRUD (Create, Read, Update, Delete) butir pertanyaan kuesioner.
 * 3. Penyusunan dan penataan ulang (reordering) urutan nomor pertanyaan dalam section yang sama.
 * 4. Pengelolaan opsi pilihan jawaban beserta target jump_to logic tanpa input manual urutan.
 */
class KelolaPertanyaanController extends Controller
{
    /**
     * Menampilkan daftar pertanyaan kuesioner beserta opsi dan logika jump_to untuk Superadmin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        // 1. Memuat seluruh butir pertanyaan beserta relasi section, kuesioner induk, opsi, dan prodi
        $questions = Question::with(['section.questionnaire', 'options', 'prodi'])
            ->orderBy('order', 'asc')
            ->get();

        // 2. Memuat seluruh bagian (section) kuesioner berdasarkan urutan tampilan
        $sections = QuestionSection::with('questionnaire')
            ->orderBy('order', 'asc')
            ->get();

        // 3. Memuat daftar program studi aktif untuk penargetan pertanyaan spesifik
        $prodis = Prodi::orderBy('kode_prodi', 'asc')->get(['id', 'kode_prodi', 'nama_prodi']);

        // 4. Memetakan kode pertanyaan ke teks pertanyaan untuk referensi jump_to di tabel
        $targetQuestionMap = Question::pluck('question_text', 'code')->toArray();

        // 5. Menyusun daftar target lompatan (jump logic) untuk dropdown pilihan opsi
        $availableJumpTargets = Question::orderBy('order', 'asc')
            ->get(['id', 'code', 'question_text'])
            ->map(function ($q) {
                return [
                    'code' => $q->code,
                    'label' => $q->code . ' — ' . Str::limit($q->question_text, 65),
                    'text' => $q->question_text,
                ];
            });

        // 6. Mengembalikan tampilan Inertia SuperAdmin/Pertanyaan/Index
        return Inertia::render('SuperAdmin/Pertanyaan/Index', [
            'questions' => $questions,
            'sections' => $sections,
            'prodis' => $prodis,
            'availableJumpTargets' => $availableJumpTargets,
            'targetQuestionMap' => $targetQuestionMap,
        ]);
    }

    /**
     * Menyimpan butir pertanyaan baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input pertanyaan baru
        $validated = $request->validate([
            'question_section_id' => 'required|exists:question_sections,id',
            'prodi_id' => 'nullable|exists:prodis,id',
            'code' => 'required|string|unique:questions,code|max:50',
            'question_text' => 'required|string',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['is_required'] = $request->boolean('is_required');

        // Otomatisasi penentuan nomor urut jika tidak diisi manual oleh Superadmin
        if (empty($validated['order'])) {
            $validated['order'] = (Question::max('order') ?? 0) + 1;
        }

        // Simpan data pertanyaan baru
        Question::create($validated);

        return redirect()->back()->with('success', 'Pertanyaan baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data butir pertanyaan yang sudah ada.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        // Validasi pembaruan data pertanyaan
        $validated = $request->validate([
            'question_section_id' => 'required|exists:question_sections,id',
            'prodi_id' => 'nullable|exists:prodis,id',
            'code' => 'required|string|max:50|unique:questions,code,' . $question->id,
            'question_text' => 'required|string',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['is_required'] = $request->boolean('is_required');

        // Jangan timpa nomor urut jika dikosongkan pada form edit
        if (empty($validated['order'])) {
            unset($validated['order']);
        }

        // Perbarui record di database
        $question->update($validated);

        return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Menghapus data butir pertanyaan beserta seluruh pilihan opsi terkait.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        // Hapus relasi opsi terlebih dahulu sebelum menghapus pertanyaan induk
        $question->options()->delete();
        $question->delete();

        return redirect()->back()->with('success', 'Pertanyaan dan seluruh opsinya berhasil dihapus.');
    }

    /**
     * Memindahkan / mengubah urutan nomor pertanyaan dalam section yang sama (Google Forms style).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reorder(Request $request)
    {
        // Metode 1: Pemindahan bertahap (naik 1 tingkat atau turun 1 tingkat)
        if ($request->has(['id', 'direction'])) {
            $validated = $request->validate([
                'id' => 'required|exists:questions,id',
                'direction' => 'required|in:up,down',
            ]);

            $currentQuestion = Question::findOrFail($validated['id']);
            $operator = $validated['direction'] === 'up' ? '<' : '>';
            $sortOrder = $validated['direction'] === 'up' ? 'desc' : 'asc';

            // Pertanyaan penukar hanya dicari dalam bagian (section) kuesioner yang sama
            $adjacentQuestion = Question::where('question_section_id', $currentQuestion->question_section_id)
                ->where('order', $operator, $currentQuestion->order)
                ->orderBy('order', $sortOrder)
                ->first();

            if ($adjacentQuestion) {
                // Tukar urutan antara kedua pertanyaan
                $tempOrder = $currentQuestion->order;
                $currentQuestion->update(['order' => $adjacentQuestion->order]);
                $adjacentQuestion->update(['order' => $tempOrder]);
            }

            return redirect()->back()->with('success', 'Posisi urutan pertanyaan berhasil dipindahkan.');
        }

        // Metode 2: Pemindahan batch melalui pengiriman array urutan baru
        if ($request->has('orders')) {
            $validated = $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:questions,id',
                'orders.*.order' => 'required|integer',
            ]);

            foreach ($validated['orders'] as $item) {
                Question::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return redirect()->back()->with('success', 'Seluruh urutan pertanyaan berhasil diperbarui.');
        }

        return redirect()->back()->withErrors(['message' => 'Parameter pengurutan tidak valid.']);
    }

    /**
     * Menyimpan opsi pilihan baru untuk butir pertanyaan tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $questionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeOption(Request $request, $questionId)
    {
        $question = Question::findOrFail($questionId);

        // Validasi input opsi jawaban baru
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'option_text' => 'required|string',
            'jump_to' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        // Urutan opsi dihitung otomatis di backend (urutan tertinggi + 1)
        if (empty($validated['order'])) {
            $validated['order'] = ($question->options()->max('order') ?? 0) + 1;
        }

        // Kode opsi otomatis jika dikosongkan (contoh: F3-01)
        if (empty($validated['code'])) {
            $optionCount = $question->options()->count() + 1;
            $validated['code'] = $question->code . '-' . str_pad($optionCount, 2, '0', STR_PAD_LEFT);
        }

        // Ubah string kosong menjadi NULL untuk integritas foreign-reference jump_to
        if (empty($validated['jump_to'])) {
            $validated['jump_to'] = null;
        }

        // Simpan opsi baru ke dalam database
        $question->options()->create($validated);

        return redirect()->back()->with('success', 'Pilihan opsi jawaban berhasil ditambahkan.');
    }

    /**
     * Memperbarui data opsi pilihan jawaban pertanyaan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $optionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateOption(Request $request, $optionId)
    {
        $option = QuestionOption::findOrFail($optionId);

        // Validasi pembaruan data opsi
        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'option_text' => 'required|string',
            'jump_to' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        // Bersihkan empty string menjadi null
        if (empty($validated['jump_to'])) {
            $validated['jump_to'] = null;
        }

        if (empty($validated['order'])) {
            unset($validated['order']); // Pertahankan nomor urut yang sudah ada
        }

        // Perbarui opsi di database
        $option->update($validated);

        return redirect()->back()->with('success', 'Pilihan opsi jawaban berhasil diperbarui.');
    }

    /**
     * Menghapus opsi pilihan jawaban pertanyaan.
     *
     * @param  int  $optionId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyOption($optionId)
    {
        $option = QuestionOption::findOrFail($optionId);
        $option->delete();

        return redirect()->back()->with('success', 'Pilihan opsi jawaban berhasil dihapus.');
    }
}
