<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sections
    |--------------------------------------------------------------------------
    |
    | Every building block a page can show (resources/views/site/sections).
    | On inner pages they appear in this order; on the home page the middle
    | sections follow the order of the active theme.
    |
    */

    'sections' => [
        'hero' => 'Hero (name, photo, buttons)',
        'marquee' => 'Moving logo strip',
        'about' => 'About',
        'services' => 'Services',
        'strip' => 'Moving accent strip',
        'skills' => 'Skills',
        'experience' => 'Experience',
        'projects' => 'Projects',
        'process' => 'Process',
        'global' => 'Global reach',
        'education' => 'Education',
        'cta' => 'Call to action',
        'contact' => 'Contact',
    ],

    /*
    |--------------------------------------------------------------------------
    | Public pages
    |--------------------------------------------------------------------------
    |
    | The pages of the site and their defaults. The menu name, browser title,
    | menu visibility and sections of each page can be changed in the admin
    | panel (Site pages); those choices are stored in the settings table.
    |
    */

    'pages' => [
        'home' => [
            'path' => '/',
            'label' => 'Home',
            'title' => '',
            'nav' => false,
            'sections' => ['hero', 'marquee', 'about', 'services', 'strip', 'skills', 'experience', 'projects', 'process', 'global', 'education', 'cta', 'contact'],
        ],
        'about' => ['path' => '/about', 'label' => 'About', 'title' => 'About', 'nav' => true, 'sections' => ['about', 'experience', 'education', 'cta']],
        'services' => ['path' => '/services', 'label' => 'Services', 'title' => 'Services', 'nav' => true, 'sections' => ['services', 'strip', 'process', 'cta']],
        'skills' => ['path' => '/skills', 'label' => 'Skills', 'title' => 'Skills', 'nav' => true, 'sections' => ['marquee', 'skills', 'cta']],
        'experience' => ['path' => '/experience', 'label' => 'Experience', 'title' => 'Experience', 'nav' => true, 'sections' => ['experience', 'global', 'cta']],
        'projects' => ['path' => '/projects', 'label' => 'Projects', 'title' => 'Projects', 'nav' => true, 'sections' => ['projects', 'cta']],
        'education' => ['path' => '/education', 'label' => 'Education', 'title' => 'Education', 'nav' => true, 'sections' => ['education', 'cta']],
        'contact' => ['path' => '/contact', 'label' => 'Contact', 'title' => 'Contact', 'nav' => false, 'sections' => ['contact']],
    ],

];
