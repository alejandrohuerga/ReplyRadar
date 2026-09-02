<?php
namespace App\Http\Controllers;

use App\Models\Keyword;
use App\Models\Project;
use App\Services\MastodonFetcherService;
use App\Services\RedditFetcherService;
use App\Services\TwitterFetcherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KeywordController extends Controller
{
    public function store(
        Request $request,
        Project $project,
        RedditFetcherService   $redditFetcher,
        MastodonFetcherService $mastodonFetcher,
        TwitterFetcherService  $twitterFetcher,
    )
    {
        abort_if($project->user_id !== $request->user()->id, 403);

        $validated = $request->validate([
            'term' => 'required|string|max:100',
        ]);

        $user    = $request->user();
        $limit   = config("replyradar.plans.{$user->plan}.max_keywords");
        $current = Keyword::whereHas('project', fn($q) =>
            $q->where('user_id', $user->id)
        )->count();

        if ($current >= $limit) {
            return back()->withErrors(['limit' => 'Has alcanzado el límite de keywords de tu plan.']);
        }

        $keyword = $project->keywords()->create($validated);

        return back()->with('success', "Keyword '{$keyword->term}' añadida. Usa el botón Refresh para buscar posts.");
    }

    public function toggle(Request $request, Keyword $keyword)
    {
        abort_if($keyword->project->user_id !== $request->user()->id, 403);
        $keyword->update(['is_active' => !$keyword->is_active]);
        return back();
    }

    public function destroy(Request $request, Keyword $keyword)
    {
        abort_unless($request->isMethod('delete') || $request->isMethod('post'), 405);
        abort_if($keyword->project->user_id !== $request->user()->id, 403);

        $projectId = $keyword->project_id;

        try {
            $keyword->delete();
            Log::info("Keyword {$keyword->id} eliminada del proyecto {$projectId}");
            return back()->with('success', 'Keyword eliminada.');
        } catch (\Exception $e) {
            Log::error("Error al eliminar keyword {$keyword->id}: {$e->getMessage()}");
            return back()->withErrors(['error' => 'No se pudo eliminar la keyword.']);
        }
    }
}