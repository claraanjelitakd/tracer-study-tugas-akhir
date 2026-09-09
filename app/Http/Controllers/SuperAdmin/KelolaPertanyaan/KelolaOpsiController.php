<?php

namespace App\Http\Controllers\SuperAdmin\KelolaPertanyaan;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Class KelolaOpsiController
 *
 * Fungsi:
 * Controller khusus untuk menangani aksi pengelolaan opsi pilihan jawaban pada pertanyaan:
 * 1. Tambah pilihan opsi jawaban baru (storeOption)
 * 2. Perbarui data opsi jawaban dan target lompatan alur branching (updateOption)
 * 3. Hapus opsi pilihan jawaban (destroyOption)
 */
class KelolaOpsiController extends Controller
{
    /**
     * Menyimpan opsi pilihan jawaban baru untuk butir pertanyaan tertentu.
     *
     * @param  int  $questionId
     * @return RedirectResponse
     */
    public function storeOption(Request $request, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'option_text' => 'required|string',
            'jump_to' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        if (empty($validated['order'])) {
            $validated['order'] = ($question->options()->max('order') ?? 0) + 1;
        }

        if (empty($validated['code'])) {
            $optionCount = $question->options()->count() + 1;
            $validated['code'] = $question->code.'-'.str_pad($optionCount, 2, '0', STR_PAD_LEFT);
        }

        if (empty($validated['jump_to'])) {
            $validated['jump_to'] = null;
        }

        $question->options()->create($validated);

        return redirect()->back()->with('success', 'Pilihan opsi jawaban berhasil ditambahkan.');
    }

    /**
     * Memperbarui data opsi jawaban dan target alur percabangan.
     *
     * @param  int  $optionId
     * @return RedirectResponse
     */
    public function updateOption(Request $request, $optionId)
    {
        $option = QuestionOption::findOrFail($optionId);

        $validated = $request->validate([
            'code' => 'nullable|string|max:50',
            'option_text' => 'required|string',
            'jump_to' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        if (empty($validated['jump_to'])) {
            $validated['jump_to'] = null;
        }

        if (empty($validated['order'])) {
            unset($validated['order']);
        }

        $option->update($validated);

        return redirect()->back()->with('success', 'Pilihan opsi jawaban berhasil diperbarui.');
    }

    /**
     * Menghapus opsi pilihan jawaban.
     *
     * @param  int  $optionId
     * @return RedirectResponse
     */
    public function destroyOption($optionId)
    {
        $option = QuestionOption::findOrFail($optionId);
        $option->delete();

        return redirect()->back()->with('success', 'Pilihan opsi jawaban berhasil dihapus.');
    }
}
