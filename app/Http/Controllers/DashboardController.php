<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $projects = $user->projects()->with('keywords')->get();

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

        $blurredIds = collect([]);
        if ($user->plan === 'free') {
            $blurredIds = $opportunities->take(5)->pluck('id');
        }

        return view('dashboard.index', [
            'projects'      => $projects,
            'opportunities' => $opportunities,
            'blurredIds'    => $blurredIds,
            'stats'         => [
                'total_posts'    => $opportunities->count(),
                'hot_count'      => $opportunities->where('final_score', '>=', 40)->count(),
                'avg_score'      => round($opportunities->avg('final_score'), 1),
                'top_subreddit'  => $opportunities->groupBy('subreddit')
                                    ->sortByDesc->count()->keys()->first(),
            ],
        ]);
    }
}
