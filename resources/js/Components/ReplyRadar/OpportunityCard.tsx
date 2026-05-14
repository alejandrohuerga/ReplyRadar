import ScoreBadge from './ScoreBadge';

type Post = {
    id: number;
    title: string;
    subreddit: string;
    url: string;
    author: string;
    reddit_score: number;
    num_comments: number;
    intent_score: number;
    match_score: number;
    final_score: number;
    posted_at: string;
};

type Props = {
    post: Post;
};

export default function OpportunityCard({ post }: Props) {
    return (
        <div className="bg-white rounded-xl border border-gray-200 p-4 hover:border-indigo-300 hover:shadow-sm transition-all">
            <div className="flex items-start justify-between gap-4">
                
                <div className="flex-1 min-w-0">

                    <a
                        href={post.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-sm font-medium text-gray-900 hover:text-indigo-600 line-clamp-2 transition-colors"
                    >
                        {post.title}
                    </a>

                    <div className="flex items-center gap-3 mt-2 flex-wrap">
                        <span className="text-xs text-indigo-600 font-medium">
                            r/{post.subreddit}
                        </span>

                        <span className="text-xs text-gray-400">
                            ↑ {post.reddit_score}
                        </span>

                        <span className="text-xs text-gray-400">
                            💬 {post.num_comments}
                        </span>

                        {post.posted_at && (
                            <span className="text-xs text-gray-400">
                                {new Date(post.posted_at).toLocaleDateString('es-ES')}
                            </span>
                        )}
                    </div>
                </div>

                <div className="flex-shrink-0">
                    <ScoreBadge score={Math.round(post.final_score)} />
                </div>
            </div>

            {/* Score breakdown */}
            <div className="mt-3 pt-3 border-t border-gray-100 grid grid-cols-3 gap-2">

                <div className="text-center">
                    <div className="text-xs text-gray-400">
                        Intención
                    </div>

                    <div className="text-sm font-semibold text-gray-700">
                        {Math.round(post.intent_score)}
                    </div>

                    <div className="mt-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div
                            className="h-full bg-indigo-400 rounded-full"
                            style={{ width: `${post.intent_score}%` }}
                        />
                    </div>
                </div>

                <div className="text-center">
                    <div className="text-xs text-gray-400">
                        Match
                    </div>

                    <div className="text-sm font-semibold text-gray-700">
                        {Math.round(post.match_score)}
                    </div>

                    <div className="mt-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div
                            className="h-full bg-purple-400 rounded-full"
                            style={{ width: `${post.match_score}%` }}
                        />
                    </div>
                </div>

                <div className="text-center">
                    <div className="text-xs text-gray-400">
                        Engagement
                    </div>

                    <div className="text-sm font-semibold text-gray-700">
                        {post.reddit_score}
                    </div>

                    <div className="mt-1 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div
                            className="h-full bg-orange-400 rounded-full"
                            style={{
                                width: `${Math.min(100, post.reddit_score / 10)}%`
                            }}
                        />
                    </div>
                </div>

            </div>
        </div>
    );
}