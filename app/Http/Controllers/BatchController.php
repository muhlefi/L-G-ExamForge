<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /* ----------------------------------------------------------------
     | DASHBOARD – list all batches
     | ---------------------------------------------------------------*/
    public function index()
    {
        $batches = Batch::latest()->get();
        return view('batches.index', compact('batches'));
    }

    /* ----------------------------------------------------------------
     | SHOW form to create a new batch
     | ---------------------------------------------------------------*/
    public function create()
    {
        return view('batches.create');
    }

    /* ----------------------------------------------------------------
     | STORE new batch (session header only)
     | ---------------------------------------------------------------*/
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_name'    => 'required|string|max:200',
            'class_name'     => 'required|string|max:100',
            'teacher_name'   => 'required|string|max:100',
            'subject'        => 'required|string|max:100',
            'topic'          => 'required|string|max:200',
            'school_level'   => 'required|in:SD,SMP,SMA',
            'material_scope' => 'nullable|string',
        ]);

        $batch = Batch::create($validated);

        return redirect()->route('batches.show', $batch->id)
                         ->with('success', 'Sesi berhasil dibuat! Sekarang tambahkan kelompok soal.');
    }
}
