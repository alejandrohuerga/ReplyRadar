<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user     = $request->user();
        $projects = $user->projects()->with('keywords')->get();

        $sort = $request->input('sort', 'final_score');
        $sortColumn = in_array($sort, ['final_score', 'match_score', 'posted_at']) ? $sort : 'final_score';

        $opportunities = \App\Models\Post::whereHas('keyword.project', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc($sortColumn)
            ->limit(50)
            ->get([
                'id', 'title', 'title_es', 'title_en',
                'content', 'content_es', 'content_en',
                'subreddit', 'url', 'author',
                'reddit_score', 'num_comments', 'intent_score',
                'match_score', 'final_score', 'posted_at', 'keyword_id',
                'source', 'like_count', 'retweet_count', 'reply_count',
                'author_handle', 'author_followers',
            ]);

        $blurredIds = collect([]);
        if ($user->plan === 'free') {
            $blurredIds = $opportunities->where('match_score', '>', 75)->pluck('id');

            $teaserPosts = $opportunities->whereBetween('match_score', [80, 85]);
            if ($teaserPosts->isNotEmpty()) {
                $teaser = $teaserPosts->random();
                $blurredIds = $blurredIds->reject(fn($id) => $id === $teaser->id);
            }
        }

        return view('dashboard.index', [
            'projects'      => $projects,
            'opportunities' => $opportunities,
            'blurredIds'    => $blurredIds,
            'sort'          => $sort,
            'stats'         => [
                'total_posts'    => $opportunities->count(),
                'hot_count'      => $opportunities->where('final_score', '>=', 60)->count(),
                'avg_score'      => round($opportunities->avg('final_score'), 1),
                'top_subreddit'  => $opportunities->groupBy('subreddit')
                                    ->sortByDesc->count()->keys()->first(),
            ],
        ]);
    }

    public function refresh(Request $request)
    {
        $php = PHP_BINARY;
        $artisan = base_path('artisan');
        $cmd = "\"{$php}\" \"{$artisan}\" fetch:reddit-posts --quiet";

        if (class_exists('COM')) {
            try {
                $wsh = new COM('WScript.Shell');
                $wsh->Run($cmd, 0, false);
            } catch (\Exception $e) {
                pclose(popen($cmd, "r"));
            }
        } else {
            pclose(popen($cmd, "r"));
        }

        return back()->with('success', __('Refresh started — posts will appear in a moment.'));
    }
}
