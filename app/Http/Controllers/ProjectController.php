<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = $request->user()
            ->projects()
            ->withCount(['keywords', 'posts'])
            ->latest()
            ->get();

        return view('projects.index', [
            'projects' => $projects,
            'canCreate' => $this->canCreateProject($request->user()),
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->canCreateProject($request->user())) {
            return back()->withErrors(['limit' => 'Has alcanzado el límite de proyectos de tu plan.']);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $request->user()->projects()->create($validated);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado.');
    }

    public function show(Request $request, Project $project)
    {
        abort_if($project->user_id !== $request->user()->id, 403);

        $project->load('keywords');

        $sort = $request->input('sort', 'final_score');
        $sortColumn = in_array($sort, ['final_score', 'posted_at']) ? $sort : 'final_score';

        $posts = Post::whereHas('keyword', fn($q) =>
                $q->where('project_id', $project->id)
            )
            ->orderByDesc($sortColumn)
            ->limit(100)
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
        if ($request->user()->plan === 'free') {
            $blurredIds = $posts->where('match_score', '>', 75)->pluck('id');

            $teaserPosts = $posts->whereBetween('match_score', [80, 85]);
            if ($teaserPosts->isNotEmpty()) {
                $teaser = $teaserPosts->random();
                $blurredIds = $blurredIds->reject(fn($id) => $id === $teaser->id);
            }
        }

        return view('projects.show', [
            'project'  => $project,
            'posts'    => $posts,
            'blurredIds' => $blurredIds,
            'canAddKeyword' => $this->canAddKeyword($request->user(), $project),
            'canExport' => $request->user()->isPro(),
        ]);
    }

    public function destroy(Request $request, Project $project)
    {
        abort_if($project->user_id !== $request->user()->id, 403);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }

    private function canCreateProject($user): bool
    {
        $limit = config("replyradar.plans.{$user->plan}.max_projects");
        return $user->projects()->count() < $limit;
    }

    public function refresh(Request $request, Project $project)
    {
        abort_if($project->user_id !== $request->user()->id, 403);

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

    private function canAddKeyword($user, Project $project): bool
    {
        $limit   = config("replyradar.plans.{$user->plan}.max_keywords");
        $current = \App\Models\Keyword::whereHas('project', fn($q) =>
            $q->where('user_id', $user->id)
        )->count();
        return $current < $limit;
    }
}

