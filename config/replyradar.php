<?php

return [

    'plans' => [
        'free' => [
            'max_projects' => 1,
            'max_keywords' => 5,
            'max_posts'    => 50,
            'history_days' => 7,
            'export'       => false,
        ],
        'pro' => [
            'max_projects' => 5,
            'max_keywords' => 50,
            'max_posts'    => 9999,
            'history_days' => 90,
            'export'       => true,
        ],
        'business' => [
            'max_projects' => 9999,
            'max_keywords' => 9999,
            'max_posts'    => 9999,
            'history_days' => 365,
            'export'       => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Scoring Weights
    |--------------------------------------------------------------------------
    | Controla cómo se combinan las dimensiones en la puntuación final.
    | urgency_depth es la media de urgency_score + depth_score.
    */
    'scoring' => [
        'intent_weight'          => 0.35,
        'match_weight'           => 0.25,
        'engagement_weight'      => 0.05,
        'freshness_weight'       => 0.15,
        'urgency_depth_weight'   => 0.15,
        'competition_penalty'    => 0.05,
    ],

    /*
    |--------------------------------------------------------------------------
    | Reddit API Config
    |--------------------------------------------------------------------------
    | controla cómo se fetchean posts de Reddit.
    | sort_modes: modos de búsqueda (cada modo = N posts extra)
    | per_mode: posts por modo
    | dedup_hash: si true, evita duplicados entre modos via content_hash
    | fetch_comments_for_top: si > 0, fetchea comentarios para posts en ese percentil
    */
    'reddit' => [
        'base_url'               => 'https://www.reddit.com',
        'user_agent'             => 'ReplyRadar/2.0 (business opportunity detector)',
        'per_mode'               => 25,
        'sort_modes'             => ['relevance', 'new', 'hot'],
        'dedup_hash'             => true,
        'fetch_comments'         => false,
        'comments_per_post'      => 30,
        'time_filter'            => 'week',

        'oauth' => [
            'enabled'        => env('REDDIT_OAUTH_ENABLED', false),
            'client_id'      => env('REDDIT_CLIENT_ID'),
            'client_secret'  => env('REDDIT_CLIENT_SECRET'),
            'username'       => env('REDDIT_USERNAME'),
            'password'       => env('REDDIT_PASSWORD'),
            'token_url'      => 'https://www.reddit.com/api/v1/access_token',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Urgency Signals
    |--------------------------------------------------------------------------
    | Palabras/frases que indican que el usuario necesita una solución AHORA.
    */
    'urgency_signals' => [
        'urgent'                => 95,
        'asap'                  => 95,
        'immediately'           => 92,
        'need help right now'   => 95,
        'desperate'             => 98,
        'anyone available'      => 85,
        'need to fix'           => 80,
        'broken'                => 75,
        'not working'           => 70,
        'emergency'             => 98,
        'critical'              => 90,
        'deadline'              => 85,
        'overdue'               => 88,
        'panic'                 => 95,
        'going crazy'           => 90,
        'losing my mind'        => 92,
        'help me please'        => 85,
        'need a solution'       => 82,
        'running out of time'   => 92,
        'been trying for'       => 75,
        'wasted hours'          => 80,
        'days trying'           => 78,
        'cannot figure out'     => 72,
        'stuck'                 => 70,
        'driving me crazy'      => 85,
        'pull my hair out'      => 88,
        'about to give up'      => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Budget Signals
    |--------------------------------------------------------------------------
    | Indican que el usuario tiene presupuesto o está dispuesto a pagar.
    */
    'budget_signals' => [
        'willing to pay'        => 95,
        'happy to pay'          => 92,
        'can pay'               => 88,
        'budget'                => 85,
        'paid solution'         => 82,
        'dont mind paying'      => 90,
        'worth paying'          => 85,
        'spend money on'        => 82,
        'pay for a tool'        => 95,
        'pay for software'      => 95,
        'monthly budget'        => 85,
        'under budget'          => 80,
        'cost effective'        => 70,
        'reasonable price'      => 75,
        'pricing'               => 70,
        'how much'              => 72,
        'whats the cost'        => 75,
        'invest in'             => 78,
        'roi'                   => 72,
    ],

    /*
    |--------------------------------------------------------------------------
    | Problem Depth Signals
    |--------------------------------------------------------------------------
    | Detectan cuán detallado/real es el problema descrito.
    */
    'depth_signals' => [
        'context_length_min'    => 200,
        'context_length_max'    => 2000,
        'detail_indicators'     => [
            'for example'        => 15,
            'for instance'       => 15,
            'specifically'       => 12,
            'here is the'        => 10,
            'the problem is'     => 12,
            'i have been'        => 10,
            'i have tried'       => 15,
            'i already tried'    => 18,
            'tried several'      => 18,
            'i tested'           => 14,
            'what happens is'    => 12,
            'the error'          => 10,
            'this is what'       => 10,
            'let me explain'     => 14,
            'to give you'        => 12,
            'background'         => 10,
            'context'            => 10,
            'here is what'       => 8,
            'currently using'    => 12,
            'i am currently'     => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Competition Signals (para análisis de comentarios)
    |--------------------------------------------------------------------------
    | Frases que indican que alguien ya ofreció una solución en los comentarios.
    */
    'competition_signals' => [
        'i use'                 => 5,
        'try using'             => 8,
        'check out'             => 8,
        'you should try'        => 10,
        'i recommend'           => 8,
        'have you tried'        => 6,
        'you can use'           => 5,
        'this is what i use'    => 8,
        'i have been using'     => 6,
        'take a look at'        => 7,
        'there is a tool'       => 10,
        'there is an app'       => 10,
        'there is a service'    => 9,
        'i built'               => 6,
        'i made'                => 5,
        'shameless plug'        => 12,
        'we offer'              => 10,
        'our product'           => 10,
        'our tool'              => 10,
        'our platform'          => 10,
        'disclaimer'            => 8,
    ],

    /*
    |--------------------------------------------------------------------------
    | OP Engagement Signals (en comentarios del OP)
    |--------------------------------------------------------------------------
    */
    'op_engagement_signals' => [
        'thank you'             => 10,
        'thanks'                => 8,
        'i will try'            => 12,
        'going to try'          => 10,
        'looks interesting'     => 12,
        'looks promising'       => 14,
        'i ll check'            => 10,
        "i'll check"            => 10,
        'sounds good'           => 8,
        'great suggestion'      => 12,
        'exactly what i needed' => 18,
        'that looks perfect'    => 16,
        'signing up'            => 20,
        'just signed up'        => 22,
        'i will look into'      => 10,
        'that might work'       => 12,
        'can you tell me more'  => 14,
        'how much'              => 12,
        'pricing'               => 12,
    ],

    /*
    |--------------------------------------------------------------------------
    | Keyword Matching - Configuración avanzada
    |--------------------------------------------------------------------------
    */
    'matching' => [
        'enable_stemming'       => true,
        'min_word_length'       => 3,
        'exact_phrase_title_bonus' => 80,
        'exact_phrase_content_bonus' => 55,
        'all_words_title_bonus' => 60,
        'partial_match_max'     => 25,
        'semantic_threshold'    => 0.5,
        'synonym_match_boost'   => 0.5,
        'proximity_max_spread'  => 40,
    ],

    /*
    |--------------------------------------------------------------------------
    | Time Decay
    |--------------------------------------------------------------------------
    | Controla cómo envejecen los posts.
    | decay_hours: horas hasta que el score se reduce a la mitad
    | max_decay: decay mínimo (un post nunca decay < este valor)
    | boost_hours: horas de "freshness boost" para posts recientes
    */
    'time_decay' => [
        'decay_hours'  => 48,
        'max_decay'    => 0.20,
        'boost_hours'  => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Enrichment
    |--------------------------------------------------------------------------
    | Controla el enriquecimiento post-fetch (análisis de comentarios).
    */
    'enrichment' => [
        'enabled'               => true,
        'top_percentile'        => 20,
        'max_posts_per_keyword' => 5,
        'min_total_score'       => 50,
        'batch_size'            => 10,
    ],

    'stripe_prices' => [
        'pro'      => env('STRIPE_PRO_PRICE_ID'),
        'business' => env('STRIPE_BUSINESS_PRICE_ID'),
        'promo_14' => env('STRIPE_PROMO_14'),
    ],
];
