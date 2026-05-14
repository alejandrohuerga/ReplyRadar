<?php
namespace App\Services;

class IntentScoringService
{
    // Señales de compra real / pain point real — score alto
    private array $buyerIntent = [
        'looking for a tool'    => 95,
        'looking for software'  => 95,
        'is there an app'       => 95,
        'is there a tool'       => 95,
        'recommend a tool'      => 92,
        'best tool for'         => 90,
        'what do you use for'   => 90,
        'how do you handle'     => 88,
        'tired of paying'       => 95,
        'too expensive'         => 88,
        'alternative to'        => 87,
        'alternatives to'       => 87,
        'switched from'         => 85,
        'cancelled my'          => 85,
        'frustrated with'       => 88,
        'sick of'               => 85,
        'wish there was'        => 90,
        'need something that'   => 88,
        'anyone use'            => 82,
        'does anyone know'      => 82,
        'how much do you pay'   => 90,
        'is it worth'           => 80,
        'worth the price'       => 82,
        'pricing is insane'     => 92,
        'pricing too high'      => 92,
        'cant afford'           => 88,
        "can't afford"          => 88,
        'how do i'              => 75,
        'how can i'             => 75,
        'struggling with'       => 80,
        'help me'               => 72,
        'best way to'           => 70,
        'need help with'        => 78,
        'trying to figure out'  => 80,
        'what is the best'      => 70,
        'should i use'          => 72,
        'how do you'            => 68,
    ];

    // Señales de spam / contenido de baja calidad — penalización fuerte
    private array $spamSignals = [
        'top companies'         => -60,
        'best companies'        => -55,
        'leading companies'     => -55,
        'top 10'                => -40,
        'top 5'                 => -35,
        'in 2025'               => -25,
        'in 2026'               => -25,
        'hire us'               => -70,
        'our services'          => -65,
        'contact us'            => -50,
        'get a quote'           => -60,
        'click here'            => -70,
        'limited offer'         => -70,
        'discount code'         => -65,
        'promo code'            => -65,
        'sponsored'             => -80,
        'advertisement'         => -80,
        'press release'         => -75,
        'ai tool spotlight'     => -70,
        'tool spotlight'        => -60,
        'company review'        => -50,
        'outsourcing companies' => -60,
        'development company'   => -45,
        'software company'      => -40,
    ];

    // Señales de discusión real (bonus moderado)
    private array $discussionSignals = [
        'thoughts?'     => 15,
        'advice?'       => 15,
        'experience?'   => 12,
        'opinions?'     => 12,
        'what do you think' => 10,
        'anyone else'   => 12,
        'just me or'    => 10,
        'am i the only' => 10,
        'update:'       => 8,
        'solved:'       => 8,
    ];

    public function score(string $title, string $content = ''): float
    {
        $text  = strtolower($title . ' ' . $content);
        $score = 30.0; // base neutral

        // Aplicar buyer intent (toma el más alto encontrado)
        $intentBonus = 0;
        foreach ($this->buyerIntent as $phrase => $value) {
            if (str_contains($text, $phrase)) {
                $intentBonus = max($intentBonus, $value);
            }
        }
        $score = $intentBonus > 0 ? (float) $intentBonus : $score;

        // Aplicar penalizaciones de spam (acumulativas)
        $spamPenalty = 0;
        foreach ($this->spamSignals as $phrase => $penalty) {
            if (str_contains($text, $phrase)) {
                $spamPenalty += $penalty;
            }
        }
        $score += $spamPenalty;

        // Aplicar bonus de discusión real
        $discussionBonus = 0;
        foreach ($this->discussionSignals as $phrase => $bonus) {
            if (str_contains($text, $phrase)) {
                $discussionBonus += $bonus;
            }
        }
        $score += $discussionBonus;

        // Bonus por signos de pregunta en título (conversación real)
        if (str_ends_with(trim($title), '?')) {
            $score += 10;
        }

        // Penalización por título todo en mayúsculas (clickbait)
        if ($title === strtoupper($title) && strlen($title) > 10) {
            $score -= 20;
        }

        // Penalización por título muy corto sin sustancia
        if (str_word_count($title) < 3) {
            $score -= 15;
        }

        return round(max(0, min(100, $score)), 1);
    }
}