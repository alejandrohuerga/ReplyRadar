<?php
namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Project;
use App\Services\RedditFetcherService;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function store(Request $request, Project $project, RedditFetcherService $fetcher)
    {
        abort_if($project->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'term' => 'required|string|max:100',
        ]);

        // Límite de keywords del plan
        $user    = $request->user();
        $limit   = config("replyradar.plans.{$user->plan}.max_keywords");
        $current = Keyword::whereHas('project', fn($q) =>
            $q->where('user_id', $user->id)
        )->count();

        if ($current >= $limit) {
            return back()->withErrors(['limit' => 'Has alcanzado el límite de keywords de tu plan.']);
        }

        $keyword = $project->keywords()->create($validated);

        // Fetch inmediato en background
        $fetcher->fetchForKeyword($keyword);

        return back()->with('success', "Keyword '{$keyword->term}' añadida y procesada.");
    }

    public function toggle(Request $request, Keyword $keyword)
    {
        abort_if($keyword->project->user_id !== $request->user()->id, 403);
        $keyword->update(['is_active' => !$keyword->is_active]);
        return back();
    }

    public function destroy(Request $request, Keyword $keyword)
    {
        abort_if($keyword->project->user_id !== $request->user()->id, 403);
        $keyword->delete();
        return back()->with('success', 'Keyword eliminada.');
    }
}