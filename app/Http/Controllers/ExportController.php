<?php
namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function posts(Request $request): StreamedResponse
    {
        $user = $request->user();

        // Solo usuarios Pro o Business
        if (!$user->isPro()) {
            abort(403, 'Export disponible solo en plan Pro.');
        }

        $projectId = $request->query('project_id');

        $query = Post::whereHas('keyword.project', fn($q) => $q->where('user_id', $user->id))
            ->orderByDesc('final_score');

        if ($projectId) {
            $query->whereHas('keyword', fn($q) => $q->where('project_id', $projectId));
        }

        $posts = $query->limit(500)->get([
            'title', 'subreddit', 'url', 'author',
            'reddit_score', 'num_comments',
            'intent_score', 'match_score', 'final_score',
            'posted_at',
        ]);

        $filename = 'replyradar-export-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($posts) {
            $handle = fopen('php://output', 'w');

            // Cabeceras CSV
            fputcsv($handle, [
                'Title', 'Subreddit', 'URL', 'Author',
                'Upvotes', 'Comments',
                'Intent Score', 'Match Score', 'Final Score',
                'Posted At',
            ]);

            foreach ($posts as $post) {
                fputcsv($handle, [
                    $post->title,
                    $post->subreddit,
                    $post->url,
                    $post->author,
                    $post->reddit_score,
                    $post->num_comments,
                    $post->intent_score,
                    $post->match_score,
                    $post->final_score,
                    $post->posted_at?->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
