<?php

/*
|--------------------------------------------------------------------------
| Editable site content
|--------------------------------------------------------------------------
|
| "groups" are the single texts/files of the site (edited on Admin → Page
| content). Each field: [label, type, default, hint?].
|   types: text | textarea | lines (one entry per line) | image | file
|
| "collections" are the repeatable items stored in the "portfolie" table
| (edited on their own admin pages). Each field: [label, type, options?].
|   types: text | textarea | tags | url | color | image | icon | select | checkbox
|
| In texts marked "rich", *word* becomes the italic accent word and
| **word** becomes bold.
|
*/

$heading = fn (string $eyebrow, string $title, string $sub) => [
    'eyebrow' => ['Small label', 'text', $eyebrow],
    'title' => ['Heading', 'text', $title, 'Use *word* for the italic accent word.'],
    'sub' => ['Side text', 'textarea', $sub],
];

return [

    'groups' => [

        'site' => [
            'label' => 'Site & files',
            'fields' => [
                'name' => ['Your name', 'text', 'Anmol Saini', 'Used in the preloader, footer and page title.'],
                'tagline' => ['Browser title tagline', 'text', 'Software Engineer'],
                'description' => ['Search description', 'textarea', 'Anmol Saini — Full-stack Software Engineer building scalable web apps with Laravel, React.js, PHP and MySQL.'],
                'logo_mark' => ['Logo letters', 'text', 'AS'],
                'logo_text' => ['Logo text', 'text', 'Anmol'],
                'photo' => ['Profile photo', 'image', 'assets/profile.jpg'],
                'favicon' => ['Favicon (browser tab icon)', 'favicon', '', 'A small square image, e.g. 64×64 or 512×512. Until you upload one, your logo letters are shown.'],
                'cv' => ['CV / résumé (PDF)', 'file', 'Anmol_Saini_CV.pdf'],
                'footer' => ['Footer line', 'text', '© {year} Anmol Saini. Designed & built with care.', 'The whole line at the bottom of the site. {year} becomes the current year.'],
            ],
        ],

        'hero' => [
            'label' => 'Hero (top of home)',
            'fields' => [
                'status' => ['Status pill', 'text', 'Software Engineer @ Innovitt Global B.V.'],
                'first_name' => ['First line of the name', 'text', 'Anmol'],
                'last_name' => ['Second line of the name', 'text', 'Saini'],
                'sub' => ['Intro paragraph', 'textarea', 'Full-stack developer crafting **scalable, user-friendly** web applications with Laravel, React.js & MySQL, for clients from **Lucknow** to **London** to **Mauritius**.', 'Use **text** for bold.'],
                'typed' => ['Typing line words', 'lines', "building scalable web apps\nlaravel --full-stack\nreact.js --interfaces\nmysql --data-driven\nopen to opportunities", 'One per line.'],
                'primary_button' => ['First button', 'text', 'See my work'],
                'cv_button' => ['CV button', 'text', 'Download CV'],
                'badge' => ['Round badge text', 'text', 'Open to work • Full-stack dev • '],
                'photo_name' => ['Name on the photo', 'text', 'Anmol Saini'],
                'photo_role' => ['Role on the photo', 'text', 'Software Engineer · Lucknow'],
                'stats' => ['Numbers row', 'lines', "2+ | Years of experience\n10+ | Projects delivered\n3 | Countries served", 'One per line: number | label. Add + after the number if you want it shown.'],
            ],
        ],

        'about' => [
            'label' => 'About',
            'fields' => $heading('About me', 'A developer who loves *building* things that work.', 'Final-year B.Tech CSE student, already writing production code every day.') + [
                'intro' => ['Big intro line', 'textarea', 'I turn ideas into *clean, scalable* web applications, from database to pixel.', 'Use *word* for the italic accent word.'],
                'text_1' => ['Paragraph 1', 'textarea', "I'm a motivated Computer Science engineer with hands-on experience in **PHP, Laravel, WordPress and React.js**. I enjoy both sides of the stack: responsive, intuitive interfaces and solid backends that scale.", 'Use **text** for bold.'],
                'text_2' => ['Paragraph 2', 'textarea', "I've built real products for international clients, including a **job portal for a London-based company** and a **CMS for the Mauritius Institute of Professional Accountants**.", 'Use **text** for bold.'],
                'sign_role' => ['Role under your name', 'text', 'Software Engineer'],
                'role_label' => ['Current job: small label', 'text', 'Currently at'],
                'role_company' => ['Current job: company', 'text', 'Innovitt Global B.V.'],
                'role_text' => ['Current job: line', 'text', 'Software Engineer · Full-stack with Laravel, React.js & MySQL'],
                'stats' => ['Two number cards', 'lines', "2+ | Years building for the web\n11 | Technologies in my toolkit", 'One per line: number | label.'],
                'location_label' => ['Location: small label', 'text', 'Based in'],
                'location' => ['Location', 'text', 'Bareilly, Uttar Pradesh'],
                'location_text' => ['Location: line', 'text', 'Working from Lucknow · Open to remote & on-site roles'],
                'hobbies_label' => ['Strengths: small label', 'text', 'Strengths & hobbies'],
                'hobbies' => ['Strengths & hobbies', 'lines', "Hard & smart working\nHonest & punctual\nCricket\nTravelling", 'One per line.'],
            ],
        ],

        'services' => [
            'label' => 'Services heading',
            'fields' => $heading('What I do', 'Services I *offer*', 'From a simple landing page to a multi-module admin system, I can take it end-to-end.') + [
                'strip' => ['Moving strip words', 'lines', "Full-stack development\nLaravel\nReact.js\nScalable apps\nClean code\nResponsive UI\nOn-time delivery", 'One per line.'],
            ],
        ],

        'skills' => [
            'label' => 'Skills heading',
            'fields' => $heading('Tech stack', 'Tools I use to *ship*', 'The languages, frameworks and tools I work with every day.'),
        ],

        'experience' => [
            'label' => 'Experience heading',
            'fields' => $heading('Career', "Where I've *worked*", 'Two roles, one direction: building real software for real users.'),
        ],

        'projects' => [
            'label' => 'Projects heading',
            'fields' => [
                'eyebrow' => ['Small label', 'text', 'Selected work'],
                'title' => ['Heading', 'text', 'Featured *projects*', 'Use *word* for the italic accent word.'],
            ],
        ],

        'process' => [
            'label' => 'Process heading',
            'fields' => $heading('How I work', 'From idea to *launch*', 'A simple, transparent process that keeps projects on time and on target.'),
        ],

        'global' => [
            'label' => 'Global reach heading',
            'fields' => $heading('Global reach', 'Work that crossed *borders*', 'Projects delivered for clients and institutions across three countries.'),
        ],

        'education' => [
            'label' => 'Education heading',
            'fields' => $heading('Education', 'Academic *journey*', 'A strong foundation in computer science, from diploma to engineering degree.'),
        ],

        'cta' => [
            'label' => 'Call to action',
            'fields' => [
                'label' => ['Small label', 'text', 'Available for new opportunities'],
                'title' => ['Heading', 'textarea', "Have an idea?\nLet's *build* it.", 'A new line starts a new row. Use *word* for the italic word.'],
                'button' => ['Button', 'text', 'Start a conversation'],
            ],
        ],

        'contact' => [
            'label' => 'Contact',
            'fields' => $heading('Contact', "Let's *talk*", 'Have a project or an opportunity in mind? Reach out any way you like.') + [
                'email' => ['Email', 'text', 'anmolsaini9193@gmail.com'],
                'phone' => ['Phone (as shown)', 'text', '+91 91930 36341'],
                'whatsapp' => ['WhatsApp number', 'text', '919193036341', 'Digits only, with country code.'],
                'linkedin' => ['LinkedIn link', 'text', 'https://www.linkedin.com/in/anmol-saini-2b0a50287/'],
                'linkedin_label' => ['LinkedIn text', 'text', 'anmol-saini-2b0a50287'],
                'instagram' => ['Instagram link', 'text', 'https://www.instagram.com/anmol_saini_9045/'],
                'instagram_label' => ['Instagram text', 'text', '@anmol_saini_9045'],
                'address' => ['Location', 'text', 'Fatehganj West, Bareilly, U.P.'],
                'form_title' => ['Form heading', 'text', 'Send a message'],
                'form_text' => ['Form note', 'text', 'Fill this in and I will get back to you. You will also get a confirmation email.'],
                'form_success' => ['Message after sending', 'text', 'Thank you! Your message has been sent. Please check your email for a confirmation.'],
            ],
        ],

        'labels' => [
            'label' => 'Small labels & buttons',
            'fields' => [
                'hero_social' => ['Hero: before the social icons', 'text', 'Find me on'],
                'scroll_hint' => ['Hero: scroll hint', 'text', 'Scroll to explore'],
                'current_role' => ['Experience: current job tag', 'text', 'Current role'],
                'step' => ['Process: word before the number', 'text', 'Step'],
                'tools' => ['Skills: word after the count', 'text', 'tools', 'Shown as "3 tools".'],
                'filter_all' => ['Projects: first filter tab', 'text', 'All'],
                'email_title' => ['Contact: email label', 'text', 'Email me'],
                'phone_title' => ['Contact: phone label', 'text', 'Call me'],
                'whatsapp_title' => ['Contact: WhatsApp label', 'text', 'WhatsApp'],
                'whatsapp_text' => ['Contact: WhatsApp text', 'text', 'Chat with me'],
                'linkedin_title' => ['Contact: LinkedIn label', 'text', 'LinkedIn'],
                'instagram_title' => ['Contact: Instagram label', 'text', 'Instagram'],
                'address_title' => ['Contact: location label', 'text', 'Location'],
                'form_name' => ['Form: name field', 'text', 'Your name'],
                'form_email' => ['Form: email field', 'text', 'Your email'],
                'form_phone' => ['Form: mobile field', 'text', 'Mobile number'],
                'form_subject' => ['Form: subject field', 'text', 'Subject'],
                'form_message' => ['Form: message field', 'text', 'Tell me about your project…'],
                'form_button' => ['Form: button', 'text', 'Send message'],
                'footer_resume' => ['Footer: CV link', 'text', 'Resume'],
            ],
        ],

        'login' => [
            'label' => 'Login page',
            'fields' => [
                'status' => ['Status pill', 'text', 'Admin portal · Private access'],
                'text' => ['Intro line', 'textarea', "Sign in to manage your portfolio's **look, theme colors** and **pages** from one place.", 'Use **text** for bold.'],
                'prompt' => ['Typing line', 'text', 'php artisan login'],
                'eyebrow' => ['Small label above the form', 'text', 'Welcome back'],
                'title' => ['Form heading', 'text', 'Admin *login*', 'Use *word* for the italic accent word.'],
                'note' => ['Form note', 'text', 'Enter your credentials to continue.'],
                'button' => ['Button', 'text', 'Sign in'],
                'back' => ['Back link', 'text', 'Back to home'],
            ],
        ],

    ],

    'collections' => [

        'projects' => [
            'label' => 'Projects',
            'singular' => 'project',
            'title' => 'title',
            'subtitle' => 'category',
            'fields' => [
                'title' => ['Project name', 'text', 'required' => true],
                'category' => ['Category label', 'text', 'hint' => 'e.g. Job portal, EdTech · LMS'],
                'filter' => ['Filter tab', 'text', 'hint' => 'Projects with the same tab name are grouped under one filter button, e.g. Client, EdTech, Web.'],
                'flag' => ['Place', 'text', 'hint' => 'e.g. London, UK'],
                'description' => ['Description', 'textarea', 'required' => true],
                'tags' => ['Tags', 'tags', 'hint' => 'Comma separated, e.g. Laravel, MySQL'],
                'url' => ['Live link', 'url', 'hint' => 'Leave empty if there is no live site.'],
                'image' => ['Screenshot', 'image'],
                'color_1' => ['Card color 1', 'color', 'default' => '#ff5b2e'],
                'color_2' => ['Card color 2', 'color', 'default' => '#b8321a'],
            ],
        ],

        'experiences' => [
            'label' => 'Experience',
            'singular' => 'job',
            'title' => 'title',
            'subtitle' => 'company',
            'fields' => [
                'title' => ['Job title', 'text', 'required' => true],
                'company' => ['Company', 'text', 'required' => true],
                'date' => ['Dates', 'text', 'hint' => 'e.g. Mar 2025 — Present'],
                'current' => ['This is my current job', 'checkbox'],
                'description' => ['Description', 'textarea'],
                'tags' => ['Tags', 'tags', 'hint' => 'Comma separated.'],
                'location' => ['Location', 'text', 'hint' => 'e.g. Lucknow, IN'],
                'year' => ['Big background year', 'text', 'hint' => 'e.g. 2025'],
            ],
        ],

        'education' => [
            'label' => 'Education',
            'singular' => 'qualification',
            'title' => 'title',
            'subtitle' => 'level',
            'fields' => [
                'title' => ['Qualification', 'text', 'required' => true],
                'level' => ['Short level', 'text', 'hint' => 'e.g. B.Tech, 12th'],
                'status' => ['Status', 'text', 'hint' => 'e.g. Completed, Final year'],
                'description' => ['Institute / details', 'textarea'],
                'board' => ['Board / university', 'text'],
                'icon' => ['Icon', 'icon', 'default' => 'graduation'],
                'main' => ['Highlight this card', 'checkbox'],
            ],
        ],

        'services' => [
            'label' => 'Services',
            'singular' => 'service',
            'title' => 'title',
            'subtitle' => 'stack',
            'fields' => [
                'title' => ['Service name', 'text', 'required' => true],
                'description' => ['Description', 'textarea'],
                'stack' => ['Tech line', 'text', 'hint' => 'e.g. PHP · Laravel · CodeIgniter'],
                'icon' => ['Icon', 'icon', 'default' => 'code'],
            ],
        ],

        'skills' => [
            'label' => 'Skills',
            'singular' => 'skill',
            'title' => 'name',
            'subtitle' => 'group',
            'fields' => [
                'name' => ['Skill name', 'text', 'required' => true],
                'group' => ['Group', 'text', 'required' => true, 'hint' => 'Skills with the same group name are shown in one card, e.g. Backend.'],
                'logo' => ['Logo image', 'image', 'hint' => 'Upload the logo of this skill. A square PNG or WebP with a transparent background looks best.'],
                'devicon' => ['Devicon name', 'text', 'hint' => 'Optional. If you do not upload a logo, a ready-made one from devicon.dev can be used, e.g. laravel-original.'],
                'marquee' => ['Show in the moving logo strip', 'checkbox', 'default' => true],
            ],
        ],

        'process' => [
            'label' => 'Process steps',
            'singular' => 'step',
            'title' => 'title',
            'subtitle' => 'description',
            'fields' => [
                'title' => ['Step name', 'text', 'required' => true],
                'description' => ['Description', 'textarea'],
                'icon' => ['Icon', 'icon', 'default' => 'code'],
            ],
        ],

        'countries' => [
            'label' => 'Countries',
            'singular' => 'country',
            'title' => 'name',
            'subtitle' => 'label',
            'fields' => [
                'name' => ['Country', 'text', 'required' => true],
                'code' => ['Short code', 'text', 'hint' => 'e.g. IN, UK'],
                'city' => ['City on the globe', 'text', 'hint' => 'The name shown next to this country\'s dot on the globe, e.g. London. Leave empty to use the country name. The first country in the list is your home base; the globe shows up to 8 dots.'],
                'description' => ['What you did there', 'textarea'],
                'label' => ['Small tag', 'text', 'hint' => 'e.g. Client, Home base'],
            ],
        ],

    ],

];
