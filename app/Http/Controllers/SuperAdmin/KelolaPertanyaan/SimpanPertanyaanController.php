<?php

namespace App\Http\Controllers\SuperAdmin\KelolaPertanyaan;

use App\Http\Controllers\Controller;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class SimpanPertanyaanController
 *
 * Fungsi:
 * Controller khusus untuk menangani aksi manipulasi data butir pertanyaan:
 * 1. Tambah pertanyaan baru (store)
 * 2. Perbarui data pertanyaan (update)
 * 3. Hapus pertanyaan beserta opsinya (destroy)
 * 4. Pindah urutan pertanyaan naik/turun dalam section yang sama (reorder)
 */
class SimpanPertanyaanController extends Controller
{
    /**
     * Menyimpan butir pertanyaan baru ke dalam database.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
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

        // Otomatisasi penentuan nomor urut jika dikosongkan
        if (empty($validated['order'])) {
            $validated['order'] = (Question::where('question_section_id', $validated['question_section_id'])->max('order') ?? 0) + 1;
        }

        // Simpan data pertanyaan baru
        $question = Question::create($validated);

        // Jika tipe pertanyaan adalah skala rating (rating_5 atau rating), otomatis buatkan 5 opsi skala penilaian standar (1-5)
        if (in_array($question->type, ['rating_5', 'rating'])) {
            $defaultRatings = [
                ['code' => $question->code.'-01', 'option_text' => 'Sangat Rendah', 'order' => 1],
                ['code' => $question->code.'-02', 'option_text' => 'Rendah', 'order' => 2],
                ['code' => $question->code.'-03', 'option_text' => 'Cukup', 'order' => 3],
                ['code' => $question->code.'-04', 'option_text' => 'Tinggi', 'order' => 4],
                ['code' => $question->code.'-05', 'option_text' => 'Sangat Tinggi', 'order' => 5],
            ];

            foreach ($defaultRatings as $opt) {
                $question->options()->create($opt);
            }
        }

        return redirect()->back()->with('success', 'Pertanyaan baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui data butir pertanyaan yang sudah ada.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $validated = $request->validate([
            'question_section_id' => 'required|exists:question_sections,id',
            'prodi_id' => 'nullable|exists:prodis,id',
            'code' => 'required|string|max:50|unique:questions,code,'.$question->id,
            'question_text' => 'required|string',
            'type' => 'required|string',
            'is_required' => 'boolean',
            'order' => 'nullable|integer',
        ]);

        $validated['is_required'] = $request->boolean('is_required');

        if (empty($validated['order'])) {
            unset($validated['order']);
        }

        $question->update($validated);

        // Jika tipe diperbarui ke skala rating dan belum memiliki opsi, otomatis buatkan 5 opsi skala penilaian
        if (in_array($question->type, ['rating_5', 'rating']) && $question->options()->count() === 0) {
            $defaultRatings = [
                ['code' => $question->code.'-01', 'option_text' => 'Sangat Rendah', 'order' => 1],
                ['code' => $question->code.'-02', 'option_text' => 'Rendah', 'order' => 2],
                ['code' => $question->code.'-03', 'option_text' => 'Cukup', 'order' => 3],
                ['code' => $question->code.'-04', 'option_text' => 'Tinggi', 'order' => 4],
                ['code' => $question->code.'-05', 'option_text' => 'Sangat Tinggi', 'order' => 5],
            ];

            foreach ($defaultRatings as $opt) {
                $question->options()->create($opt);
            }
        }

        return redirect()->back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Menghapus butir pertanyaan beserta seluruh pilihan opsi terkait.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        // Hapus opsi jawaban terlebih dahulu
        $question->options()->delete();
        $question->delete();

        return redirect()->back()->with('success', 'Pertanyaan dan seluruh opsi jawabannya berhasil dihapus.');
    }

    /**
     * Memindahkan urutan nomor pertanyaan dalam section yang sama.
     *
     * @return RedirectResponse
     */
    public function reorder(Request $request)
    {
        if ($request->has(['id', 'direction'])) {
            $validated = $request->validate([
                'id' => 'required|exists:questions,id',
                'direction' => 'required|in:up,down',
            ]);

            $currentQuestion = Question::findOrFail($validated['id']);
            $operator = $validated['direction'] === 'up' ? '<' : '>';
            $sortOrder = $validated['direction'] === 'up' ? 'desc' : 'asc';

            // Cari pertanyaan tetangga dalam section yang sama
            $adjacentQuestion = Question::where('question_section_id', $currentQuestion->question_section_id)
                ->where('order', $operator, $currentQuestion->order)
                ->orderBy('order', $sortOrder)
                ->first();

            if ($adjacentQuestion) {
                $tempOrder = $currentQuestion->order;
                $currentQuestion->update(['order' => $adjacentQuestion->order]);
                $adjacentQuestion->update(['order' => $tempOrder]);
            }

            return redirect()->back()->with('success', 'Posisi urutan pertanyaan berhasil dipindahkan.');
        }

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
}
