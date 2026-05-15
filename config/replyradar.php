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

    'scoring' => [
        'intent_weight'      => 0.35,
        'match_weight'       => 0.40,
        'engagement_weight'  => 0.20,
        'quality_weight'     => 0.05,
    ],

    'reddit' => [
        'base_url'    => 'https://www.reddit.com',
        'user_agent'  => 'ReplyRadar/1.0',
        'per_keyword' => 25, // posts a traer por keyword
    ],

    'keyword_synonyms' => [
        // Se pueden añadir desde el config sin tocar código
        'pricing'    => ['cost', 'subscription', 'billing', 'plan', 'tier', 'fee'],
        'saas'       => ['software', 'app', 'platform', 'tool', 'startup'],
        'email'      => ['newsletter', 'outreach', 'inbox', 'campaign', 'smtp'],
        'lead'       => ['prospect', 'customer', 'client', 'buyer', 'user'],
        'content'    => ['post', 'article', 'blog', 'copy', 'writing'],
    ],
    
    'scoring' => [
        'intent_weight'      => 0.45,  // era 0.35 — intent es el más predictivo
        'match_weight'       => 0.45,  // era 0.40 — match sigue siendo clave
        'engagement_weight'  => 0.08,  // era 0.20 — el engagement en Reddit es ruidoso
        'quality_weight'     => 0.02,  // era 0.05
    ],
];