<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user     = $request->user();
        $projects = $user->projects()->with('keywords')->get();

        // Oportunidades globales del usuario ordenadas por score
        $opportunities = \App\Models\Post::whereHas('keyword.project', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('final_score')
            ->limit(50)
            ->get([
                'id', 'title', 'subreddit', 'url', 'author',
                'reddit_score', 'num_comments', 'intent_score',
                'match_score', 'final_score', 'posted_at', 'keyword_id',
            ]);

        return Inertia::render('Dashboard', [
            'auth' => [
                'user' => $user,
            ],
            'projects'      => $projects,
            'opportunities' => $opportunities,
            'stats'         => [
                'total_posts'    => $opportunities->count(),
                'hot_count'      => $opportunities->where('final_score', '>=', 80)->count(),
                'avg_score'      => round($opportunities->avg('final_score'), 1),
                'top_subreddit'  => $opportunities->groupBy('subreddit')
                                    ->sortByDesc->count()->keys()->first(),
            ],
        ]);
    }
}
