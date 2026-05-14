type Props = { score: number; size?: 'sm' | 'md' | 'lg' };

export default function ScoreBadge({ score, size = 'md' }: Props) {
    const getColor = () => {
        if (score >= 80) return 'bg-red-100 text-red-700 border-red-200';
        if (score >= 60) return 'bg-orange-100 text-orange-700 border-orange-200';
        if (score >= 40) return 'bg-yellow-100 text-yellow-700 border-yellow-200';
        return 'bg-gray-100 text-gray-600 border-gray-200';
    };

    const getLabel = () => {
        if (score >= 80) return '🔥 Hot';
        if (score >= 60) return '⚡ Warm';
        if (score >= 40) return '💧 Cool';
        return '❄ Cold';
    };

    const sizes = { sm: 'text-xs px-2 py-0.5', md: 'text-sm px-2.5 py-1', lg: 'text-base px-3 py-1.5' };

    return (
        <span className={`inline-flex items-center gap-1 rounded-full border font-medium ${getColor()} ${sizes[size]}`}>
            {getLabel()}
            <span className="font-bold">{score}</span>
        </span>
    );
}