<?php

namespace Database\Seeders;

use App\Models\PortfolioItem;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    /**
     * Fill the "portfolie" table with the original portfolio content.
     * A collection that already has rows is left alone, so this is safe to re-run.
     */
    public function run(): void
    {
        foreach ($this->items() as $type => $rows) {
            if (PortfolioItem::where('type', $type)->exists()) {
                continue;
            }

            foreach ($rows as $i => $data) {
                PortfolioItem::create(['type' => $type, 'data' => $data, 'position' => $i + 1]);
            }
        }
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    protected function items(): array
    {
        $project = fn ($title, $category, $filter, $flag, $description, $tags, $c1, $c2, $image, $url = '') => [
            'title' => $title, 'category' => $category, 'filter' => $filter, 'flag' => $flag,
            'description' => $description, 'tags' => $tags, 'color_1' => $c1, 'color_2' => $c2,
            'image' => $image, 'url' => $url,
        ];

        $skill = fn ($group, $name, $devicon, $marquee = true) => [
            'group' => $group, 'name' => $name, 'devicon' => $devicon, 'marquee' => $marquee,
        ];

        return [

            'projects' => [
                $project('ConiferGB Job Portal', 'Job portal', 'Client', 'London, UK', 'A complete job portal for a London-based client covering job listings, candidate applications and a full admin management panel.', 'Laravel, MySQL, Admin panel', '#ff5b2e', '#b8321a', 'assets/projects/conifer.png', 'https://conifergb.com/'),
                $project('CMS & MIPA Website', 'CMS · Institution', 'Client', 'Mauritius', 'Content management system and official website for the Mauritius Institute of Professional Accountants (MIPA).', 'CMS, PHP, Responsive', '#3a3632', '#1f1d1b', 'assets/projects/mipa.png', 'https://mipa.demodomains.in/'),
                $project('Advocate Website', 'Law & Justice', 'Web', 'India', 'A platform to manage advocate profiles and client interactions, making legal services easier to find.', 'Laravel, MySQL, Bootstrap', '#e9b44c', '#b87a22', 'assets/projects/advocate.png'),
                $project('Learning Management System', 'EdTech · LMS', 'EdTech', 'India', 'Modules for course management and student progress tracking with an intuitive learner dashboard.', 'Laravel, React.js, MySQL', '#9fb88a', '#5a7547', 'assets/projects/lms.png', 'https://flms.demodomains.in/'),
                $project('University Management System', 'EdTech · UMS', 'EdTech', 'India', 'Student, faculty and admin modules powered by a modern React UI for daily university operations.', 'React.js, Laravel, REST', '#7b8fa6', '#44566b', 'assets/projects/ums.png'),
                $project('Goel Group of Institutions', 'Institution · Group site', 'Web', 'Lucknow, India', 'Central website for the Goel Group of Institutions with courses, admissions enquiry, placements and links to every member college.', 'WordPress, Elementor, Responsive', '#1e3a8a', '#0f1f4d', 'assets/projects/goel.jpg', 'http://goel.edu.in/'),
                $project('GITM Website', 'Institution · Engineering', 'Web', 'Lucknow, India', 'Website for the Goel Institute of Technology & Management covering academics, admissions, circulars, NIRF and placements.', 'WordPress, Elementor, Responsive', '#d9a53a', '#8f6a1c', 'assets/projects/gitm.jpg', 'http://gitm.goel.edu.in/'),
                $project('GAMCH Website', 'Institution · Ayurveda', 'Web', 'Lucknow, India', 'Website for the Goel Ayurvedic Medical College & Hospital with hospital, college and NCISM information.', 'WordPress, Elementor', '#f3941d', '#a85f0c', 'assets/projects/gamch.jpg', 'https://gamch.goel.edu.in/'),
                $project('GIPS Website', 'Institution · Pharmacy', 'Web', 'Lucknow, India', 'Website for the Goel Institute of Pharmacy & Sciences, an NBA-accredited pharmacy college, with committees, faculty and placements.', 'WordPress, Elementor', '#1a1a8c', '#0b0b4a', 'assets/projects/gips.jpg', 'https://gips.goel.edu.in/'),
                $project('GIPCS Website', 'Institution · Pharmacy', 'Web', 'Lucknow, India', 'Website for the Goel Institute of Pharmaceutical Sciences with courses, events, gallery and placement details.', 'WordPress, Elementor', '#9b0a14', '#5a050b', 'assets/projects/gipcs.jpg', 'http://gipcs.goel.edu.in/'),
            ],

            'experiences' => [
                ['title' => 'Software Engineer', 'company' => 'Innovitt Global B.V.', 'date' => 'Mar 2025 — Present', 'current' => true, 'description' => 'Contributing to scalable web applications and full-stack development, building features end-to-end from database design to polished React interfaces.', 'tags' => 'Laravel, React.js, MySQL, Full-stack', 'location' => 'Lucknow, IN', 'year' => '2025'],
                ['title' => 'Apprenticeship', 'company' => 'Softpro India', 'date' => 'Sep 2024 — Feb 2025', 'current' => false, 'description' => 'Worked on web development projects using PHP, Laravel and React.js, gaining hands-on exposure to both frontend and backend modules.', 'tags' => 'PHP, Laravel, React.js', 'location' => 'Lucknow, IN', 'year' => '2024'],
            ],

            'education' => [
                ['title' => 'B.Tech, Computer Science & Engineering', 'level' => 'B.Tech', 'status' => 'Final year', 'description' => 'Jahangirabad Institute of Technology, Barabanki (Lucknow)', 'board' => 'AKTU', 'icon' => 'graduation', 'main' => true],
                ['title' => 'Diploma, Computer Science & Engineering', 'level' => 'Diploma', 'status' => 'Completed', 'description' => 'Technical diploma program', 'board' => 'BTEUP', 'icon' => 'monitor', 'main' => false],
                ['title' => 'Intermediate', 'level' => '12th', 'status' => 'Completed', 'description' => 'Senior secondary education', 'board' => 'U.P. Board', 'icon' => 'book', 'main' => false],
                ['title' => 'High School', 'level' => '10th', 'status' => 'Completed', 'description' => 'Secondary education', 'board' => 'U.P. Board', 'icon' => 'book-open', 'main' => false],
            ],

            'services' => [
                ['title' => 'Backend Development', 'description' => 'Robust APIs, authentication, admin panels and business logic that scale with your users.', 'stack' => 'PHP · Laravel · CodeIgniter', 'icon' => 'server'],
                ['title' => 'Frontend & React UI', 'description' => 'Fast, interactive interfaces and dashboards with reusable components and clean state.', 'stack' => 'React.js · JavaScript · Tailwind', 'icon' => 'monitor'],
                ['title' => 'CMS & WordPress', 'description' => 'Content-managed websites that clients can update themselves without touching code.', 'stack' => 'WordPress · Custom CMS', 'icon' => 'layout'],
                ['title' => 'Responsive Design', 'description' => 'Pixel-clean layouts that look and work great on every screen, from phones to wide monitors.', 'stack' => 'HTML · CSS · Bootstrap', 'icon' => 'phone'],
            ],

            'skills' => [
                $skill('Backend', 'PHP', 'php-plain'),
                $skill('Backend', 'Laravel', 'laravel-original'),
                $skill('Backend', 'CodeIgniter', 'codeigniter-plain'),
                $skill('Database & CMS', 'MySQL', 'mysql-original'),
                $skill('Database & CMS', 'WordPress', 'wordpress-plain'),
                $skill('Frontend', 'React.js', 'react-original'),
                $skill('Frontend', 'JavaScript', 'javascript-plain'),
                $skill('Frontend', 'HTML5', 'html5-plain'),
                $skill('Frontend', 'CSS3', 'css3-plain'),
                $skill('Frontend', 'Bootstrap', 'bootstrap-plain'),
                $skill('Frontend', 'Tailwind', 'tailwindcss-original'),
                $skill('Frontend', 'Responsive UI', '', false),
            ],

            'process' => [
                ['title' => 'Discover', 'description' => 'Understand the goals, users and requirements before writing a single line.', 'icon' => 'search'],
                ['title' => 'Design', 'description' => 'Plan the database, modules and a responsive UI that feels right.', 'icon' => 'pen'],
                ['title' => 'Develop', 'description' => 'Build clean, maintainable code with Laravel & React, tested as I go.', 'icon' => 'code'],
                ['title' => 'Deliver', 'description' => 'Deploy, hand over, and keep supporting as the product grows.', 'icon' => 'rocket'],
            ],

            'countries' => [
                ['name' => 'India', 'code' => 'IN', 'city' => 'Lucknow', 'description' => 'Innovitt Global, Softpro India, plus Advocate, LMS & UMS projects', 'label' => 'Home base'],
                ['name' => 'United Kingdom', 'code' => 'UK', 'city' => 'London', 'description' => 'ConiferGB job portal for a London-based client', 'label' => 'Client'],
                ['name' => 'Mauritius', 'code' => 'MU', 'city' => 'Mauritius', 'description' => 'CMS & website for the Mauritius Institute of Professional Accountants', 'label' => 'Institution'],
            ],

        ];
    }
}
