<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Used until the admin saves a choice from the dashboard.
    |
    */

    'default_mode' => 'dark',
    'default_color' => 'orange',

    'default_layout' => 'classic',

    'modes' => ['dark', 'light'],

    /*
    |--------------------------------------------------------------------------
    | Themes (layouts)
    |--------------------------------------------------------------------------
    |
    | Each theme sets the page background, the corner radius of cards and the
    | order of the sections on the home page. Colors are separate (see below).
    |
    | background: grid | aurora | dots | plain | lines | blueprint
    | order: ids of the home page sections, top to bottom. The hero always
    |        stays first and the contact section + footer always stay last.
    |
    */

    'sections' => [
        'about' => 'About',
        'services' => 'Services',
        'skills' => 'Skills',
        'experience' => 'Experience',
        'projects' => 'Projects',
        'process' => 'Process',
        'global' => 'Global reach',
        'education' => 'Education',
    ],

    'layouts' => [
        'classic' => [
            'label' => 'Classic',
            'description' => 'The original look: floating pill header, big-name footer, soft grid background.',
            'background' => 'grid',
            'radius' => '22px',
            'order' => ['about', 'services', 'skills', 'experience', 'projects', 'process', 'global', 'education'],
        ],
        'showcase' => [
            'label' => 'Showcase',
            'description' => 'Glass top bar, centered hero with round photo, accent footer band, aurora glow.',
            'background' => 'aurora',
            'radius' => '28px',
            'order' => ['projects', 'services', 'about', 'skills', 'experience', 'global', 'process', 'education'],
        ],
        'developer' => [
            'label' => 'Developer',
            'description' => 'Terminal feel: solid top bar, photo on the left, squared buttons, one-line footer.',
            'background' => 'dots',
            'radius' => '14px',
            'order' => ['skills', 'projects', 'experience', 'about', 'services', 'process', 'education', 'global'],
        ],
        'minimal' => [
            'label' => 'Minimal',
            'description' => 'No boxes: plain text header, flat cards, monochrome photo, tiny centered footer.',
            'background' => 'plain',
            'radius' => '12px',
            'order' => ['about', 'experience', 'education', 'skills', 'projects', 'services', 'process', 'global'],
        ],
        'agency' => [
            'label' => 'Agency',
            'description' => 'Bold: accent-colored header, uppercase headings, offset photo block, dark footer.',
            'background' => 'lines',
            'radius' => '22px',
            'order' => ['services', 'process', 'projects', 'global', 'about', 'skills', 'experience', 'education'],
        ],
        'blueprint' => [
            'label' => 'Blueprint',
            'description' => 'Technical drawing: sharp outlined header, dashed cards, outlined text, blueprint grid.',
            'background' => 'blueprint',
            'radius' => '6px',
            'order' => ['experience', 'education', 'skills', 'about', 'projects', 'services', 'process', 'global'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Accent colors
    |--------------------------------------------------------------------------
    |
    | Each color has a dark and a light variant: [accent, accent-2, accent-ink].
    | The ink is the text color used on top of the accent.
    |
    */

    'colors' => [
        'orange' => [
            'label' => 'Orange',
            'dark' => ['#ff5b2e', '#ff8a4c', '#1a0d07'],
            'light' => ['#ec4a1c', '#ff7a3d', '#ffffff'],
        ],
        'blue' => [
            'label' => 'Blue',
            'dark' => ['#4f9dff', '#7db8ff', '#06121f'],
            'light' => ['#1d5fd6', '#4a8af0', '#ffffff'],
        ],
        'green' => [
            'label' => 'Green',
            'dark' => ['#3ddc84', '#7be8ab', '#06170d'],
            'light' => ['#0f8a4b', '#2fae6c', '#ffffff'],
        ],
        'purple' => [
            'label' => 'Purple',
            'dark' => ['#a78bfa', '#c4b5fd', '#140c2b'],
            'light' => ['#6d3fe0', '#8f6bf0', '#ffffff'],
        ],
        'pink' => [
            'label' => 'Pink',
            'dark' => ['#ff5c93', '#ff8fb5', '#24060f'],
            'light' => ['#d6246a', '#ee5a92', '#ffffff'],
        ],
        'teal' => [
            'label' => 'Teal',
            'dark' => ['#2dd4bf', '#6ee7d7', '#04201c'],
            'light' => ['#0b8478', '#22a89a', '#ffffff'],
        ],
        'red' => [
            'label' => 'Red',
            'dark' => ['#ff4d4d', '#ff8080', '#240606'],
            'light' => ['#d92626', '#f05252', '#ffffff'],
        ],
        'amber' => [
            'label' => 'Amber',
            'dark' => ['#ffb020', '#ffd166', '#231603'],
            'light' => ['#b86e00', '#e08a00', '#ffffff'],
        ],
        'lime' => [
            'label' => 'Lime',
            'dark' => ['#a3e635', '#bef264', '#152103'],
            'light' => ['#4d7c0f', '#65a30d', '#ffffff'],
        ],
        'cyan' => [
            'label' => 'Cyan',
            'dark' => ['#22d3ee', '#67e8f9', '#03222a'],
            'light' => ['#0e7490', '#0891b2', '#ffffff'],
        ],
        'indigo' => [
            'label' => 'Indigo',
            'dark' => ['#818cf8', '#a5b4fc', '#0c0f2e'],
            'light' => ['#4338ca', '#6366f1', '#ffffff'],
        ],
        'rose' => [
            'label' => 'Rose',
            'dark' => ['#fb7185', '#fda4af', '#2a0810'],
            'light' => ['#be123c', '#e11d48', '#ffffff'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Page backgrounds
    |--------------------------------------------------------------------------
    |
    | Ready-made page background colors offered next to "Default" and the
    | color picker. Cards, borders and the text color are derived from the
    | chosen color (see resources/views/partials/theme.blade.php).
    |
    */

    'backgrounds' => [
        'snow' => ['label' => 'Snow', 'color' => '#ffffff'],
        'mint' => ['label' => 'Mint', 'color' => '#e8f5ee'],
        'sky' => ['label' => 'Sky', 'color' => '#e8f1fb'],
        'blush' => ['label' => 'Blush', 'color' => '#fcecef'],
        'midnight' => ['label' => 'Midnight', 'color' => '#0b1220'],
        'charcoal' => ['label' => 'Charcoal', 'color' => '#17181c'],
    ],

];
