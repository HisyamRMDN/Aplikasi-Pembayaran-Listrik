<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\TarifHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::withCount('pelanggans')
            ->orderBy('daya', 'asc')
            ->get();

        $recentHistories = TarifHistory::with(['tarif', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.tarif.index', compact('tarifs', 'recentHistories'));
    }

    public function create()
    {
        return view('admin.tarif.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'daya' => 'required|integer|min:100|unique:tarifs,daya',
            'tarif_per_kwh' => 'required|numeric|min:0',
        ]);

        Tarif::create($validated);

        return redirect()->route('tarif.index')
            ->with('success', 'Golongan tarif baru berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $tarif = Tarif::withCount('pelanggans')
            ->with(['histories.user'])
            ->findOrFail($id);

        return view('admin.tarif.show', compact('tarif'));
    }

    public function edit(string $id)
    {
        $tarif = Tarif::withCount('pelanggans')->findOrFail($id);

        return view('admin.tarif.edit', compact('tarif'));
    }

    public function update(Request $request, string $id)
    {
        $tarif = Tarif::findOrFail($id);

        $validated = $request->validate([
            'daya' => 'required|integer|min:100|unique:tarifs,daya,' . $tarif->id_tarif . ',id_tarif',
            'tarif_per_kwh' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($tarif, $validated) {
            $tarifLama = (float) $tarif->tarif_per_kwh;
            $tarifBaru = (float) $validated['tarif_per_kwh'];

            $tarif->update($validated);

            if ($tarifLama !== $tarifBaru) {
                TarifHistory::create([
                    'id_tarif' => $tarif->id_tarif,
                    'tarif_lama' => $tarifLama,
                    'tarif_baru' => $tarifBaru,
                    'id_user' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('tarif.index')
            ->with('success', 'Data tarif berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $tarif = Tarif::withCount('pelanggans')->findOrFail($id);

        if ($tarif->pelanggans_count > 0) {
            return back()->with('error', 'Tarif tidak dapat dihapus karena masih digunakan oleh ' . $tarif->pelanggans_count . ' pelanggan.');
        }

        $tarif->delete();

        return redirect()->route('tarif.index')
            ->with('success', 'Golongan tarif berhasil dihapus');
    }

        public function pelangganIndex()
    {
        $pelanggan = Auth::guard('pelanggan')->user();
        $tarifs = Tarif::orderBy('daya')->get();

        return view('pelanggan.tarif', compact('tarifs', 'pelanggan'));
    }
}
