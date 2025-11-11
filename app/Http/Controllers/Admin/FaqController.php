<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\Faq\StoreFaqRequest;
use App\Http\Requests\Faq\UpdateFaqRequest;
use App\Models\Faq;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('id', 'asc')->get();
        return view('faq', compact('faqs'));
    }

    public function data()
    {
        $activeMenu = currentMenu();

        $query = Faq::select('id', 'question', 'answer');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('can_edit', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'edit'))
            ->addColumn('can_delete', fn($row) => Auth::user()->hasMenuPermission($activeMenu->id, 'destroy'))
            ->addColumn('edit_url', fn($row) => route('faq.index.edit', $row->id))
            ->addColumn('delete_url', fn($row) => route('faq.index.destroy', $row->id))
            ->make(true);
    }

    public function create() {}

    public function store(StoreFaqRequest $request)
    {
        DB::beginTransaction();
        try {
            Faq::create($request->validated());
            DB::commit();
            return redirect()->route('faq.index')->with('success', 'FAQ berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store FAQ Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan FAQ.');
        }
    }

    public function edit(Faq $faq) {}

    public function update(UpdateFaqRequest $request, Faq $faq)
    {
        if ($request->only(['question', 'answer']) == $faq->only(['question', 'answer'])) {
            return redirect()->route('faq.index')->with('info', 'Tidak ada perubahan data.');
        }

        DB::beginTransaction();
        try {
            $faq->update($request->validated());
            DB::commit();
            return redirect()->route('faq.index')->with('success', 'FAQ berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update FAQ Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(Faq $faq)
    {
        DB::beginTransaction();
        try {
            $faq->delete();
            DB::commit();
            return redirect()->route('faq.index')->with('success', 'FAQ berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete FAQ Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function getFaq(Request $request)
    {
        $query = $request->get('q');
        $perPage = 10;

        $faq = Faq::where('question', 'like', '%' . $query . '%')
            ->select('id', 'question')
            ->paginate($perPage);

        return response()->json($faq);
    }
}
