<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Question;
use App\Models\QuestionGroup;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index()
    {
        $totals = [
            'batches' => Batch::count(),
            'groups' => QuestionGroup::count(),
            'questions' => Question::count(),
            'groups_done' => QuestionGroup::where('status', 'done')->count(),
            'groups_pending' => QuestionGroup::where('status', 'pending')->count(),
        ];

        $questionTypeBreakdown = Question::query()
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $cognitiveBreakdown = Question::query()
            ->select('cognitive_level', DB::raw('COUNT(*) as total'))
            ->groupBy('cognitive_level')
            ->orderBy('cognitive_level')
            ->pluck('total', 'cognitive_level');

        $topSubjects = Question::query()
            ->select('subject', DB::raw('COUNT(*) as total'))
            ->whereNotNull('subject')
            ->groupBy('subject')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentBatches = Batch::query()
            ->withCount(['questionGroups', 'questions'])
            ->latest()
            ->limit(5)
            ->get();

        return view('stats.index', compact(
            'totals',
            'questionTypeBreakdown',
            'cognitiveBreakdown',
            'topSubjects',
            'recentBatches'
        ));
    }
}
