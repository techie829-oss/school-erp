<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CmsPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenants
        $tenants = Tenant::all();

        // Fixed pages configuration
        $pages = [
            [
                'slug' => '', // Empty slug for home page (root /)
                'title' => 'Home',
                'meta_description' => 'Welcome to our school',
                'content' => [
                    'sections' => [
                        // Hero Section
                        [
                            'type' => 'hero',
                            'title' => 'Hero Section',
                            'order' => 1,
                            'settings' => [
                                'background' => 'gradient-to-br from-primary-50 via-white to-primary-100',
                                'padding' => 'py-24 lg:py-32',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'badge' => 'Excellence in Education Since 2000',
                                'badge_style' => 'bg-primary-100 text-primary-700',
                                'heading' => 'Welcome to Our School',
                                'heading_highlight' => true,
                                'description' => 'Empowering students to achieve their full potential through innovative learning and character development.',
                                'buttons' => [
                                    [
                                        'text' => 'Apply for Admission',
                                        'url' => '/admission',
                                        'style' => 'primary',
                                        'icon' => 'arrow-right',
                                    ],
                                    [
                                        'text' => 'Learn More',
                                        'url' => '/about',
                                        'style' => 'secondary',
                                    ],
                                ],
                            ],
                        ],
                        // School Stats Section
                        [
                            'type' => 'stats',
                            'title' => 'School Statistics',
                            'order' => 2,
                            'settings' => [
                                'background' => 'gradient-to-br from-white to-gray-50',
                                'padding' => 'py-16',
                                'grid_columns' => 'grid-cols-2 md:grid-cols-4',
                            ],
                            'data' => [
                                'items' => [
                                    [
                                        'label' => 'Active Students',
                                        'value' => '500+',
                                        'icon' => 'users',
                                        'icon_color' => 'primary',
                                        'value_color' => 'primary-600',
                                    ],
                                    [
                                        'label' => 'Years Experience',
                                        'value' => '25+',
                                        'icon' => 'check',
                                        'icon_color' => 'green',
                                        'value_color' => 'green-600',
                                    ],
                                    [
                                        'label' => 'Success Rate',
                                        'value' => '95%',
                                        'icon' => 'chart',
                                        'icon_color' => 'blue',
                                        'value_color' => 'blue-600',
                                    ],
                                    [
                                        'label' => 'Institution Type',
                                        'value' => 'School',
                                        'icon' => 'building',
                                        'icon_color' => 'purple',
                                        'value_color' => 'purple-600',
                                    ],
                                ],
                            ],
                        ],
                        // Features Section
                        [
                            'type' => 'features',
                            'title' => 'Why Choose Us',
                            'order' => 3,
                            'settings' => [
                                'background' => 'gradient-to-b from-gray-50 to-white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-3',
                            ],
                            'data' => [
                                'badge' => 'Why Choose Us',
                                'heading' => 'Why Choose Our School?',
                                'description' => 'Discover what makes us the preferred choice for quality education and holistic development',
                                'items' => [
                                    [
                                        'title' => 'Academic Excellence',
                                        'description' => 'Comprehensive curriculum designed to challenge and inspire students to reach their highest potential through innovative teaching methods.',
                                        'icon' => 'book',
                                        'icon_color' => 'primary',
                                        'icon_bg' => 'from-primary-100 to-primary-200',
                                    ],
                                    [
                                        'title' => 'Experienced Faculty',
                                        'description' => 'Our dedicated teachers bring years of experience and passion for education to every classroom, ensuring personalized attention.',
                                        'icon' => 'users-group',
                                        'icon_color' => 'green',
                                        'icon_bg' => 'from-green-100 to-green-200',
                                    ],
                                    [
                                        'title' => 'Modern Facilities',
                                        'description' => 'State-of-the-art classrooms, laboratories, and recreational facilities to support holistic development of every student.',
                                        'icon' => 'building',
                                        'icon_color' => 'blue',
                                        'icon_bg' => 'from-blue-100 to-blue-200',
                                    ],
                                ],
                            ],
                        ],
                        // Programs Preview Section
                        [
                            'type' => 'programs',
                            'title' => 'Our Programs',
                            'order' => 4,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
                            ],
                            'data' => [
                                'badge' => 'Our Offerings',
                                'heading' => 'Our Programs',
                                'description' => 'Comprehensive educational programs designed for all age groups and learning styles',
                                'items' => [
                                    [
                                        'title' => 'Primary School',
                                        'subtitle' => 'Grades 1-5',
                                        'description' => 'Foundation building programs',
                                        'icon' => 'book-open',
                                        'color' => 'primary',
                                        'bg_gradient' => 'from-primary-50 via-primary-100 to-primary-50',
                                    ],
                                    [
                                        'title' => 'Middle School',
                                        'subtitle' => 'Grades 6-8',
                                        'description' => 'Critical thinking development',
                                        'icon' => 'document-text',
                                        'color' => 'green',
                                        'bg_gradient' => 'from-green-50 via-green-100 to-green-50',
                                    ],
                                    [
                                        'title' => 'High School',
                                        'subtitle' => 'Grades 9-12',
                                        'description' => 'College preparation focus',
                                        'icon' => 'academic-cap',
                                        'color' => 'blue',
                                        'bg_gradient' => 'from-blue-50 via-blue-100 to-blue-50',
                                    ],
                                    [
                                        'title' => 'Special Programs',
                                        'subtitle' => 'Arts, Sports, STEM',
                                        'description' => 'Extracurricular excellence',
                                        'icon' => 'light-bulb',
                                        'color' => 'purple',
                                        'bg_gradient' => 'from-purple-50 via-purple-100 to-purple-50',
                                    ],
                                ],
                                'cta_button' => [
                                    'text' => 'View All Programs',
                                    'url' => '/programs',
                                    'style' => 'primary',
                                ],
                            ],
                        ],
                        // Testimonials Section
                        [
                            'type' => 'testimonials',
                            'title' => 'Testimonials',
                            'order' => 5,
                            'settings' => [
                                'background' => 'gradient-to-br from-gray-50 to-white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-3',
                            ],
                            'data' => [
                                'badge' => 'What Parents Say',
                                'heading' => 'Testimonials',
                                'description' => 'Hear from our satisfied parents and students',
                                'items' => [
                                    [
                                        'rating' => 5,
                                        'quote' => 'The best decision we made for our child\'s education. The teachers are dedicated and the facilities are outstanding.',
                                        'author' => 'Sarah Mitchell',
                                        'role' => 'Parent',
                                        'initials' => 'SM',
                                        'avatar_color' => 'primary',
                                    ],
                                    [
                                        'rating' => 5,
                                        'quote' => 'Excellent academic standards and a nurturing environment. Our daughter has flourished here beyond our expectations.',
                                        'author' => 'John Davis',
                                        'role' => 'Parent',
                                        'initials' => 'JD',
                                        'avatar_color' => 'green',
                                    ],
                                    [
                                        'rating' => 5,
                                        'quote' => 'Outstanding support system and amazing teachers. The school prepares students for success in all aspects of life.',
                                        'author' => 'Emma Wilson',
                                        'role' => 'Parent',
                                        'initials' => 'EW',
                                        'avatar_color' => 'blue',
                                    ],
                                ],
                            ],
                        ],
                        // Quick Links Section
                        [
                            'type' => 'quick_links',
                            'title' => 'Quick Links',
                            'order' => 6,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-2 md:grid-cols-4',
                            ],
                            'data' => [
                                'badge' => 'Quick Access',
                                'heading' => 'Quick Links',
                                'description' => 'Easy access to important information and resources',
                                'items' => [
                                    [
                                        'title' => 'Admission',
                                        'description' => 'Apply now',
                                        'url' => '/admission',
                                        'icon' => 'document',
                                        'color' => 'primary',
                                    ],
                                    [
                                        'title' => 'Programs',
                                        'description' => 'View courses',
                                        'url' => '/programs',
                                        'icon' => 'book-open',
                                        'color' => 'green',
                                    ],
                                    [
                                        'title' => 'Facilities',
                                        'description' => 'Explore campus',
                                        'url' => '/facilities',
                                        'icon' => 'building',
                                        'color' => 'blue',
                                    ],
                                    [
                                        'title' => 'Contact',
                                        'description' => 'Get in touch',
                                        'url' => '/contact',
                                        'icon' => 'envelope',
                                        'color' => 'purple',
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'type' => 'cta',
                            'title' => 'Call to Action',
                            'order' => 7,
                            'settings' => [
                                'background' => 'gradient-to-r from-primary-600 via-primary-700 to-primary-600',
                                'padding' => 'py-20',
                                'text_color' => 'white',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'heading' => 'Ready to Join Our School?',
                                'description' => 'Take the first step towards your child\'s bright future and academic excellence',
                                'buttons' => [
                                    [
                                        'text' => 'Start Application',
                                        'url' => '/admission',
                                        'style' => 'white',
                                        'icon' => 'arrow-right',
                                    ],
                                    [
                                        'text' => 'Contact Us',
                                        'url' => '/contact',
                                        'style' => 'outline-white',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                'meta_description' => 'Learn about our school',
                'content' => [
                    'sections' => [
                        // Hero Section
                        [
                            'type' => 'hero',
                            'title' => 'About Hero Section',
                            'order' => 1,
                            'settings' => [
                                'background' => 'gradient-to-br from-primary-50 via-white to-primary-100',
                                'padding' => 'py-24 lg:py-32',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'badge' => 'Learn More About Us',
                                'badge_style' => 'bg-primary-100 text-primary-700',
                                'heading' => 'About Our School',
                                'heading_highlight' => true,
                                'description' => 'Learn about our mission, values, and commitment to student success.',
                            ],
                        ],
                        // About Content Section
                        [
                            'type' => 'content',
                            'title' => 'Our Story',
                            'order' => 2,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'layout' => 'two-column',
                            ],
                            'data' => [
                                'badge' => 'Our Story',
                                'heading' => 'Building Excellence Since Day One',
                                'paragraphs' => [
                                    'Founded with a vision to provide exceptional education, Our School has been at the forefront of academic excellence for over two decades. We believe in nurturing not just academic skills, but also character, creativity, and leadership qualities.',
                                    'Our commitment to personalized learning and holistic development has made us a preferred choice for parents who want the best for their children\'s future.',
                                ],
                                'stats' => [
                                    [
                                        'label' => 'Active Students',
                                        'value' => '500+',
                                        'color' => 'primary',
                                        'bg_gradient' => 'from-primary-50 to-primary-100',
                                    ],
                                    [
                                        'label' => 'Years of Excellence',
                                        'value' => '25+',
                                        'color' => 'green',
                                        'bg_gradient' => 'from-green-50 to-green-100',
                                    ],
                                ],
                                'info_box' => [
                                    'title' => 'School Information',
                                    'bg_gradient' => 'from-primary-100 via-primary-50 to-primary-200',
                                    'items' => [
                                        [
                                            'label' => 'Name',
                                            'icon' => 'building',
                                            'icon_color' => 'primary',
                                        ],
                                        [
                                            'label' => 'Location',
                                            'icon' => 'map-pin',
                                            'icon_color' => 'green',
                                        ],
                                        [
                                            'label' => 'Type',
                                            'icon' => 'users',
                                            'icon_color' => 'blue',
                                        ],
                                        [
                                            'label' => 'Status',
                                            'icon' => 'check-circle',
                                            'icon_color' => 'purple',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // Mission & Values Section
                        [
                            'type' => 'features',
                            'title' => 'Mission & Values',
                            'order' => 3,
                            'settings' => [
                                'background' => 'gradient-to-b from-gray-50 to-white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-3',
                            ],
                            'data' => [
                                'badge' => 'Our Foundation',
                                'heading' => 'Our Mission & Values',
                                'description' => 'Guiding principles that shape our educational approach and define who we are',
                                'items' => [
                                    [
                                        'title' => 'Innovation',
                                        'description' => 'Embracing new technologies and teaching methodologies to enhance learning outcomes and prepare students for the future.',
                                        'icon' => 'light-bulb',
                                        'icon_color' => 'yellow',
                                        'icon_bg' => 'from-yellow-100 to-yellow-200',
                                    ],
                                    [
                                        'title' => 'Community',
                                        'description' => 'Building strong partnerships between students, parents, teachers, and the community to create a supportive learning environment.',
                                        'icon' => 'users-group',
                                        'icon_color' => 'green',
                                        'icon_bg' => 'from-green-100 to-green-200',
                                    ],
                                    [
                                        'title' => 'Excellence',
                                        'description' => 'Striving for the highest standards in academics, character, and personal development to ensure student success.',
                                        'icon' => 'bolt',
                                        'icon_color' => 'blue',
                                        'icon_bg' => 'from-blue-100 to-blue-200',
                                    ],
                                ],
                            ],
                        ],
                        // Vision Section
                        [
                            'type' => 'content',
                            'title' => 'Vision & Core Principles',
                            'order' => 4,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'layout' => 'two-column-reverse',
                            ],
                            'data' => [
                                'left_column' => [
                                    'type' => 'vision_box',
                                    'badge' => 'Our Vision',
                                    'heading' => 'Shaping Tomorrow\'s Leaders',
                                    'paragraphs' => [
                                        'To be recognized as a premier educational institution that empowers students to become confident, compassionate, and capable leaders who make a positive impact on society.',
                                        'We envision a school where every student discovers their unique potential and is equipped with the knowledge, skills, and values needed to thrive in an ever-changing world.',
                                    ],
                                    'bg_gradient' => 'from-primary-600 to-primary-700',
                                    'text_color' => 'white',
                                ],
                                'right_column' => [
                                    'badge' => 'What We Stand For',
                                    'heading' => 'Our Core Principles',
                                    'items' => [
                                        [
                                            'title' => 'Holistic Development',
                                            'description' => 'Nurturing academic, social, emotional, and physical growth in every student.',
                                            'icon' => 'check',
                                            'icon_color' => 'primary',
                                        ],
                                        [
                                            'title' => 'Individualized Learning',
                                            'description' => 'Recognizing and supporting each student\'s unique learning style and pace.',
                                            'icon' => 'check',
                                            'icon_color' => 'green',
                                        ],
                                        [
                                            'title' => 'Character Building',
                                            'description' => 'Instilling integrity, respect, responsibility, and ethical values.',
                                            'icon' => 'check',
                                            'icon_color' => 'blue',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'type' => 'cta',
                            'title' => 'Join Community CTA',
                            'order' => 5,
                            'settings' => [
                                'background' => 'gradient-to-r from-primary-600 via-primary-700 to-primary-600',
                                'padding' => 'py-20',
                                'text_color' => 'white',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'heading' => 'Join Our School Community',
                                'description' => 'Be part of an institution that values excellence, innovation, and student success',
                                'buttons' => [
                                    [
                                        'text' => 'Apply Now',
                                        'url' => '/admission',
                                        'style' => 'white',
                                        'icon' => 'arrow-right',
                                    ],
                                    [
                                        'text' => 'Contact Us',
                                        'url' => '/contact',
                                        'style' => 'outline-white',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'programs',
                'title' => 'Programs',
                'meta_description' => 'Discover the comprehensive educational programs we offer to nurture your child\'s potential.',
                'content' => [
                    'sections' => [
                        // Hero Section
                        [
                            'type' => 'hero',
                            'title' => 'Programs Hero Section',
                            'order' => 1,
                            'settings' => [
                                'background' => 'gradient-to-br from-blue-50 via-white to-purple-50',
                                'padding' => 'py-24 lg:py-32',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'badge' => 'Educational Excellence',
                                'badge_style' => 'bg-blue-100 text-blue-700',
                                'heading' => 'Our Programs',
                                'heading_highlight' => true,
                                'description' => 'Discover comprehensive educational programs designed to nurture your child\'s potential and prepare them for a successful future.',
                            ],
                        ],
                        // Programs Grid Section
                        [
                            'type' => 'programs',
                            'title' => 'Academic Programs',
                            'order' => 2,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                            ],
                            'data' => [
                                'badge' => 'What We Offer',
                                'heading' => 'Academic Programs',
                                'description' => 'Comprehensive curriculum designed to meet diverse learning needs',
                                'items' => [
                                    [
                                        'title' => 'Elementary Education',
                                        'description' => 'Foundational learning program for grades 1-5, focusing on building strong academic fundamentals and developing critical thinking skills.',
                                        'features' => [
                                            'Age-appropriate curriculum',
                                            'Interactive learning methods',
                                            'Character development',
                                        ],
                                        'icon' => 'book-open',
                                        'color' => 'blue',
                                        'bg_gradient' => 'from-blue-50 to-blue-100',
                                        'link' => '/admission',
                                        'link_text' => 'Learn More',
                                    ],
                                    [
                                        'title' => 'Middle School',
                                        'description' => 'Comprehensive program for grades 6-8, emphasizing subject specialization, research skills, and independent learning.',
                                        'features' => [
                                            'Subject specialization',
                                            'Research & project work',
                                            'Leadership opportunities',
                                        ],
                                        'icon' => 'document-text',
                                        'color' => 'green',
                                        'bg_gradient' => 'from-green-50 to-green-100',
                                        'link' => '/admission',
                                        'link_text' => 'Learn More',
                                    ],
                                    [
                                        'title' => 'High School',
                                        'description' => 'Advanced program for grades 9-12, preparing students for higher education and career success through rigorous academics.',
                                        'features' => [
                                            'College preparation',
                                            'Advanced placement courses',
                                            'Career counseling',
                                        ],
                                        'icon' => 'academic-cap',
                                        'color' => 'purple',
                                        'bg_gradient' => 'from-purple-50 to-purple-100',
                                        'link' => '/admission',
                                        'link_text' => 'Learn More',
                                    ],
                                    [
                                        'title' => 'STEM Program',
                                        'description' => 'Specialized Science, Technology, Engineering, and Mathematics program fostering innovation and problem-solving skills.',
                                        'features' => [
                                            'Hands-on experiments',
                                            'Technology integration',
                                            'Competition participation',
                                        ],
                                        'icon' => 'light-bulb',
                                        'color' => 'orange',
                                        'bg_gradient' => 'from-orange-50 to-orange-100',
                                        'link' => '/admission',
                                        'link_text' => 'Learn More',
                                    ],
                                    [
                                        'title' => 'Arts & Music',
                                        'description' => 'Creative arts program nurturing artistic expression, musical talent, and cultural appreciation in students.',
                                        'features' => [
                                            'Visual arts classes',
                                            'Music & performance',
                                            'Annual showcases',
                                        ],
                                        'icon' => 'video-camera',
                                        'color' => 'red',
                                        'bg_gradient' => 'from-red-50 to-red-100',
                                        'link' => '/admission',
                                        'link_text' => 'Learn More',
                                    ],
                                    [
                                        'title' => 'Sports & Athletics',
                                        'description' => 'Comprehensive sports program promoting physical fitness, teamwork, and competitive excellence across multiple disciplines.',
                                        'features' => [
                                            'Multiple sports options',
                                            'Professional coaching',
                                            'Inter-school competitions',
                                        ],
                                        'icon' => 'users-group',
                                        'color' => 'indigo',
                                        'bg_gradient' => 'from-indigo-50 to-indigo-100',
                                        'link' => '/admission',
                                        'link_text' => 'Learn More',
                                    ],
                                ],
                            ],
                        ],
                        // Program Features Section
                        [
                            'type' => 'features',
                            'title' => 'Program Highlights',
                            'order' => 3,
                            'settings' => [
                                'background' => 'gradient-to-b from-gray-50 to-white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
                            ],
                            'data' => [
                                'badge' => 'Why Choose Us',
                                'heading' => 'Program Highlights',
                                'description' => 'What makes our programs stand out',
                                'items' => [
                                    [
                                        'title' => 'Expert Faculty',
                                        'description' => 'Highly qualified and experienced teachers dedicated to student success.',
                                        'icon' => 'book-open',
                                        'icon_color' => 'primary',
                                        'icon_bg' => 'from-primary-100 to-primary-200',
                                    ],
                                    [
                                        'title' => 'Modern Facilities',
                                        'description' => 'State-of-the-art classrooms, labs, and learning spaces.',
                                        'icon' => 'light-bulb',
                                        'icon_color' => 'green',
                                        'icon_bg' => 'from-green-100 to-green-200',
                                    ],
                                    [
                                        'title' => 'Flexible Scheduling',
                                        'description' => 'Programs designed to accommodate diverse learning needs.',
                                        'icon' => 'clock',
                                        'icon_color' => 'blue',
                                        'icon_bg' => 'from-blue-100 to-blue-200',
                                    ],
                                    [
                                        'title' => 'Certified Programs',
                                        'description' => 'Accredited curriculum meeting national education standards.',
                                        'icon' => 'shield-check',
                                        'icon_color' => 'purple',
                                        'icon_bg' => 'from-purple-100 to-purple-200',
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'type' => 'cta',
                            'title' => 'Get Started CTA',
                            'order' => 4,
                            'settings' => [
                                'background' => 'gradient-to-r from-primary-600 via-primary-700 to-primary-600',
                                'padding' => 'py-20',
                                'text_color' => 'white',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'heading' => 'Ready to Get Started?',
                                'description' => 'Join our community and give your child the best educational experience',
                                'buttons' => [
                                    [
                                        'text' => 'Apply for Admission',
                                        'url' => '/admission',
                                        'style' => 'white',
                                        'icon' => 'arrow-right',
                                    ],
                                    [
                                        'text' => 'Contact Us',
                                        'url' => '/contact',
                                        'style' => 'outline-white',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'facilities',
                'title' => 'Facilities',
                'meta_description' => 'Explore our state-of-the-art facilities designed to enhance learning, creativity, and overall student development.',
                'content' => [
                    'sections' => [
                        // Hero Section
                        [
                            'type' => 'hero',
                            'title' => 'Facilities Hero Section',
                            'order' => 1,
                            'settings' => [
                                'background' => 'gradient-to-br from-green-50 via-white to-teal-50',
                                'padding' => 'py-24 lg:py-32',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'badge' => 'World-Class Infrastructure',
                                'badge_style' => 'bg-green-100 text-green-700',
                                'heading' => 'Our Facilities',
                                'heading_highlight' => true,
                                'description' => 'Explore our state-of-the-art facilities designed to enhance learning, creativity, and overall student development.',
                            ],
                        ],
                        // Facilities Grid Section
                        [
                            'type' => 'gallery',
                            'title' => 'Campus Facilities',
                            'order' => 2,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                            ],
                            'data' => [
                                'badge' => 'What We Offer',
                                'heading' => 'Campus Facilities',
                                'description' => 'Modern infrastructure supporting excellence in education',
                                'items' => [
                                    [
                                        'title' => 'Modern Library',
                                        'description' => 'Extensive collection of books, digital resources, and quiet study spaces for research and learning.',
                                        'features' => ['50,000+ books collection', 'Digital reading resources', 'Quiet study zones'],
                                        'icon' => 'book-open',
                                        'color' => 'blue',
                                        'bg_gradient' => 'from-blue-400 to-blue-600',
                                    ],
                                    [
                                        'title' => 'Science Laboratories',
                                        'description' => 'Fully equipped labs for Physics, Chemistry, and Biology with modern equipment and safety measures.',
                                        'features' => ['Physics, Chemistry, Biology labs', 'Modern equipment & tools', 'Safety-certified facilities'],
                                        'icon' => 'beaker',
                                        'color' => 'purple',
                                        'bg_gradient' => 'from-purple-400 to-purple-600',
                                    ],
                                    [
                                        'title' => 'Computer Labs',
                                        'description' => 'High-tech computer laboratories with latest hardware and software for digital learning and programming.',
                                        'features' => ['Latest hardware & software', 'High-speed internet', 'Programming & coding tools'],
                                        'icon' => 'computer-desktop',
                                        'color' => 'indigo',
                                        'bg_gradient' => 'from-indigo-400 to-indigo-600',
                                    ],
                                    [
                                        'title' => 'Sports Complex',
                                        'description' => 'Comprehensive sports facilities including indoor and outdoor courts, fields, and fitness centers.',
                                        'features' => ['Basketball & volleyball courts', 'Football & cricket fields', 'Fitness & gymnasium'],
                                        'icon' => 'home',
                                        'color' => 'green',
                                        'bg_gradient' => 'from-green-400 to-green-600',
                                    ],
                                    [
                                        'title' => 'Auditorium',
                                        'description' => 'Spacious auditorium with modern audio-visual equipment for assemblies, performances, and events.',
                                        'features' => ['500+ seating capacity', 'Advanced sound system', 'Stage & lighting setup'],
                                        'icon' => 'video-camera',
                                        'color' => 'red',
                                        'bg_gradient' => 'from-red-400 to-red-600',
                                    ],
                                    [
                                        'title' => 'Cafeteria',
                                        'description' => 'Clean and spacious dining area serving healthy, nutritious meals prepared in hygienic conditions.',
                                        'features' => ['Healthy meal options', 'Hygienic food preparation', 'Comfortable seating area'],
                                        'icon' => 'adjustments-horizontal',
                                        'color' => 'orange',
                                        'bg_gradient' => 'from-yellow-400 to-orange-600',
                                    ],
                                    [
                                        'title' => 'Art & Craft Room',
                                        'description' => 'Dedicated space for creative expression with art supplies, tools, and display areas for student artwork.',
                                        'features' => ['Complete art supplies', 'Pottery & sculpture tools', 'Artwork display gallery'],
                                        'icon' => 'paint-brush',
                                        'color' => 'pink',
                                        'bg_gradient' => 'from-pink-400 to-pink-600',
                                    ],
                                    [
                                        'title' => 'Music Room',
                                        'description' => 'Soundproof music room equipped with various instruments and audio equipment for music education.',
                                        'features' => ['Various musical instruments', 'Soundproof environment', 'Recording facilities'],
                                        'icon' => 'musical-note',
                                        'color' => 'teal',
                                        'bg_gradient' => 'from-teal-400 to-teal-600',
                                    ],
                                    [
                                        'title' => 'Medical Room',
                                        'description' => 'Well-equipped medical facility with trained staff to handle emergencies and provide first aid.',
                                        'features' => ['Qualified medical staff', 'First aid equipment', '24/7 emergency support'],
                                        'icon' => 'heart',
                                        'color' => 'cyan',
                                        'bg_gradient' => 'from-cyan-400 to-cyan-600',
                                    ],
                                ],
                            ],
                        ],
                        // Additional Facilities Section
                        [
                            'type' => 'features',
                            'title' => 'Additional Amenities',
                            'order' => 3,
                            'settings' => [
                                'background' => 'gradient-to-b from-gray-50 to-white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
                            ],
                            'data' => [
                                'badge' => 'More Facilities',
                                'heading' => 'Additional Amenities',
                                'description' => 'Supporting facilities that enhance the learning experience',
                                'items' => [
                                    [
                                        'title' => 'Smart Classrooms',
                                        'description' => 'Interactive whiteboards and digital learning tools',
                                        'icon' => 'device-phone-mobile',
                                        'icon_color' => 'blue',
                                        'icon_bg' => 'from-blue-100 to-blue-200',
                                    ],
                                    [
                                        'title' => 'Security System',
                                        'description' => '24/7 surveillance and access control',
                                        'icon' => 'shield-check',
                                        'icon_color' => 'green',
                                        'icon_bg' => 'from-green-100 to-green-200',
                                    ],
                                    [
                                        'title' => 'Wi-Fi Campus',
                                        'description' => 'High-speed internet throughout the campus',
                                        'icon' => 'signal',
                                        'icon_color' => 'purple',
                                        'icon_bg' => 'from-purple-100 to-purple-200',
                                    ],
                                    [
                                        'title' => 'Transportation',
                                        'description' => 'Safe and reliable school bus service',
                                        'icon' => 'home',
                                        'icon_color' => 'orange',
                                        'icon_bg' => 'from-orange-100 to-orange-200',
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'type' => 'cta',
                            'title' => 'Experience Facilities CTA',
                            'order' => 4,
                            'settings' => [
                                'background' => 'gradient-to-r from-primary-600 via-primary-700 to-primary-600',
                                'padding' => 'py-20',
                                'text_color' => 'white',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'heading' => 'Experience Our Facilities',
                                'description' => 'Schedule a campus tour to see our world-class facilities in person',
                                'buttons' => [
                                    [
                                        'text' => 'Schedule a Tour',
                                        'url' => '/contact',
                                        'style' => 'white',
                                        'icon' => 'arrow-right',
                                    ],
                                    [
                                        'text' => 'Apply Now',
                                        'url' => '/admission',
                                        'style' => 'outline-white',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'admission',
                'title' => 'Admission',
                'meta_description' => 'Learn about our admission process and requirements for joining our school community.',
                'content' => [
                    'sections' => [
                        // Hero Section
                        [
                            'type' => 'hero',
                            'title' => 'Admission Hero Section',
                            'order' => 1,
                            'settings' => [
                                'background' => 'gradient-to-br from-indigo-50 via-white to-purple-50',
                                'padding' => 'py-24 lg:py-32',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'badge' => 'Join Our Community',
                                'badge_style' => 'bg-indigo-100 text-indigo-700',
                                'heading' => 'Admission Information',
                                'heading_highlight' => true,
                                'description' => 'Learn about our admission process and requirements for joining our school community.',
                            ],
                        ],
                        // Admission Process Section
                        [
                            'type' => 'content',
                            'title' => 'Admission Process',
                            'order' => 2,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'layout' => 'timeline',
                            ],
                            'data' => [
                                'badge' => 'How to Apply',
                                'heading' => 'Admission Process',
                                'description' => 'Simple steps to join our school community',
                                'steps' => [
                                    [
                                        'number' => 1,
                                        'title' => 'Inquiry & Application',
                                        'description' => 'Submit an online inquiry form or visit our campus to learn more about our programs. Complete the admission application form with all required details.',
                                        'icon' => 'document-text',
                                        'color' => 'blue',
                                    ],
                                    [
                                        'number' => 2,
                                        'title' => 'Document Submission',
                                        'description' => 'Submit all required documents including birth certificate, previous school records, medical reports, and passport-sized photographs.',
                                        'icon' => 'document-text',
                                        'color' => 'green',
                                    ],
                                    [
                                        'number' => 3,
                                        'title' => 'Assessment & Interview',
                                        'description' => 'Students may be required to take an assessment test. Parents and students will have an interview session with the admission committee.',
                                        'icon' => 'clipboard-document-check',
                                        'color' => 'purple',
                                    ],
                                    [
                                        'number' => 4,
                                        'title' => 'Admission Decision',
                                        'description' => 'After reviewing all applications, successful candidates will receive an admission offer letter with details about enrollment and fees.',
                                        'icon' => 'check-circle',
                                        'color' => 'orange',
                                    ],
                                    [
                                        'number' => 5,
                                        'title' => 'Enrollment & Orientation',
                                        'description' => 'Complete the enrollment process by paying fees and submitting final documents. Attend the orientation program to get familiar with the school.',
                                        'icon' => 'users-group',
                                        'color' => 'teal',
                                    ],
                                ],
                            ],
                        ],
                        // Requirements Section
                        [
                            'type' => 'features',
                            'title' => 'Admission Requirements',
                            'order' => 3,
                            'settings' => [
                                'background' => 'gradient-to-b from-gray-50 to-white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3',
                            ],
                            'data' => [
                                'badge' => 'What You Need',
                                'heading' => 'Admission Requirements',
                                'description' => 'Documents and information needed for admission',
                                'items' => [
                                    [
                                        'title' => 'Academic Records',
                                        'description' => '',
                                        'features' => ['Previous school transcripts', 'Report cards (last 2 years)', 'Transfer certificate'],
                                        'icon' => 'document-text',
                                        'icon_color' => 'blue',
                                        'icon_bg' => 'from-blue-100 to-blue-200',
                                    ],
                                    [
                                        'title' => 'Personal Documents',
                                        'description' => '',
                                        'features' => ['Birth certificate', 'Passport-sized photos (4 copies)', 'ID proof (Aadhaar/Passport)'],
                                        'icon' => 'identification',
                                        'icon_color' => 'green',
                                        'icon_bg' => 'from-green-100 to-green-200',
                                    ],
                                    [
                                        'title' => 'Medical Records',
                                        'description' => '',
                                        'features' => ['Medical fitness certificate', 'Vaccination records', 'Blood group certificate'],
                                        'icon' => 'heart',
                                        'icon_color' => 'purple',
                                        'icon_bg' => 'from-purple-100 to-purple-200',
                                    ],
                                ],
                            ],
                        ],
                        // Important Dates Section
                        [
                            'type' => 'stats',
                            'title' => 'Important Dates',
                            'order' => 4,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'grid_columns' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
                            ],
                            'data' => [
                                'badge' => 'Timeline',
                                'heading' => 'Important Dates',
                                'description' => 'Key dates for the admission process',
                                'items' => [
                                    [
                                        'label' => 'Application Opens',
                                        'value' => 'January 1',
                                        'description' => 'Start submitting your applications',
                                        'icon' => 'calendar',
                                        'icon_color' => 'blue',
                                        'value_color' => 'blue-600',
                                    ],
                                    [
                                        'label' => 'Last Date',
                                        'value' => 'March 31',
                                        'description' => 'Final deadline for applications',
                                        'icon' => 'calendar',
                                        'icon_color' => 'green',
                                        'value_color' => 'green-600',
                                    ],
                                    [
                                        'label' => 'Assessments',
                                        'value' => 'April 15',
                                        'description' => 'Assessment tests scheduled',
                                        'icon' => 'calendar',
                                        'icon_color' => 'purple',
                                        'value_color' => 'purple-600',
                                    ],
                                    [
                                        'label' => 'Results',
                                        'value' => 'May 1',
                                        'description' => 'Admission results announced',
                                        'icon' => 'calendar',
                                        'icon_color' => 'orange',
                                        'value_color' => 'orange-600',
                                    ],
                                ],
                            ],
                        ],
                        // FAQ Section
                        [
                            'type' => 'content',
                            'title' => 'FAQ',
                            'order' => 5,
                            'settings' => [
                                'background' => 'gradient-to-b from-gray-50 to-white',
                                'padding' => 'py-20',
                                'layout' => 'faq',
                            ],
                            'data' => [
                                'badge' => 'Got Questions?',
                                'heading' => 'Frequently Asked Questions',
                                'description' => 'Common questions about our admission process',
                                'items' => [
                                    [
                                        'question' => 'What is the age requirement for admission?',
                                        'answer' => 'Age requirements vary by grade level. Generally, students should be age-appropriate for their grade. Please contact our admission office for specific age requirements for each grade.',
                                    ],
                                    [
                                        'question' => 'Is there an entrance exam?',
                                        'answer' => 'Yes, students may be required to take an assessment test depending on the grade level. The test evaluates basic academic skills and helps us understand the student\'s learning needs.',
                                    ],
                                    [
                                        'question' => 'What are the fee structures?',
                                        'answer' => 'Fee structures vary by grade level and program. Detailed fee information is provided during the admission process. We also offer scholarships and financial aid for eligible students.',
                                    ],
                                    [
                                        'question' => 'Can I visit the campus before applying?',
                                        'answer' => 'Absolutely! We encourage prospective families to visit our campus. You can schedule a campus tour by contacting our admission office. We also organize open house events throughout the year.',
                                    ],
                                ],
                            ],
                        ],
                        // CTA Section
                        [
                            'type' => 'cta',
                            'title' => 'Begin Journey CTA',
                            'order' => 6,
                            'settings' => [
                                'background' => 'gradient-to-r from-primary-600 via-primary-700 to-primary-600',
                                'padding' => 'py-20',
                                'text_color' => 'white',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'heading' => 'Ready to Begin Your Journey?',
                                'description' => 'Start your admission process today and join our vibrant learning community',
                                'buttons' => [
                                    [
                                        'text' => 'Contact Admission Office',
                                        'url' => '/contact',
                                        'style' => 'white',
                                        'icon' => 'arrow-right',
                                    ],
                                    [
                                        'text' => 'Learn More About Us',
                                        'url' => '/',
                                        'style' => 'outline-white',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'meta_description' => 'Get in touch with us for any questions, inquiries, or to schedule a campus visit.',
                'content' => [
                    'sections' => [
                        // Hero Section
                        [
                            'type' => 'hero',
                            'title' => 'Contact Hero Section',
                            'order' => 1,
                            'settings' => [
                                'background' => 'gradient-to-br from-teal-50 via-white to-cyan-50',
                                'padding' => 'py-24 lg:py-32',
                                'has_background_pattern' => true,
                                'has_decorative_elements' => true,
                            ],
                            'data' => [
                                'badge' => 'We\'re Here to Help',
                                'badge_style' => 'bg-teal-100 text-teal-700',
                                'heading' => 'Contact Us',
                                'heading_highlight' => true,
                                'description' => 'Get in touch with us for any questions, inquiries, or to schedule a campus visit.',
                            ],
                        ],
                        // Contact Form & Info Section
                        [
                            'type' => 'content',
                            'title' => 'Contact Form & Information',
                            'order' => 2,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                                'layout' => 'two-column',
                            ],
                            'data' => [
                                'left_column' => [
                                    'type' => 'contact_form',
                                    'badge' => 'Send Us a Message',
                                    'heading' => 'Get In Touch',
                                    'description' => 'Fill out the form below and we\'ll get back to you as soon as possible.',
                                    'fields' => [
                                        ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'required' => true],
                                        ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
                                        ['name' => 'email', 'label' => 'Email Address', 'type' => 'email', 'required' => true],
                                        ['name' => 'phone', 'label' => 'Phone Number', 'type' => 'tel', 'required' => false],
                                        ['name' => 'subject', 'label' => 'Subject', 'type' => 'select', 'required' => true, 'options' => ['admission' => 'Admission Inquiry', 'general' => 'General Information', 'visit' => 'Campus Tour', 'academic' => 'Academic Programs', 'other' => 'Other']],
                                        ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
                                    ],
                                ],
                                'right_column' => [
                                    'badge' => 'Contact Information',
                                    'heading' => 'Visit or Reach Us',
                                    'description' => 'We\'re here to answer your questions and help you learn more about our school.',
                                    'contact_items' => [
                                        [
                                            'type' => 'address',
                                            'label' => 'Address',
                                            'icon' => 'map-pin',
                                            'icon_color' => 'blue',
                                        ],
                                        [
                                            'type' => 'phone',
                                            'label' => 'Phone',
                                            'icon' => 'phone',
                                            'icon_color' => 'green',
                                        ],
                                        [
                                            'type' => 'email',
                                            'label' => 'Email',
                                            'icon' => 'envelope',
                                            'icon_color' => 'purple',
                                        ],
                                    ],
                                    'office_hours' => [
                                        ['day' => 'Monday - Friday', 'time' => '8:00 AM - 5:00 PM'],
                                        ['day' => 'Saturday', 'time' => '9:00 AM - 1:00 PM'],
                                        ['day' => 'Sunday', 'time' => 'Closed'],
                                    ],
                                ],
                            ],
                        ],
                        // Map Section
                        [
                            'type' => 'content',
                            'title' => 'Location Map',
                            'order' => 3,
                            'settings' => [
                                'background' => 'gray-50',
                                'padding' => 'py-20',
                            ],
                            'data' => [
                                'badge' => 'Find Us',
                                'heading' => 'Location',
                                'description' => 'Visit our campus or get directions',
                                'map_placeholder' => true,
                            ],
                        ],
                        // Social Media & Quick Links Section
                        [
                            'type' => 'content',
                            'title' => 'Social Media & Quick Links',
                            'order' => 4,
                            'settings' => [
                                'background' => 'white',
                                'padding' => 'py-20',
                            ],
                            'data' => [
                                'badge' => 'Connect With Us',
                                'heading' => 'Follow Our Journey',
                                'description' => 'Stay connected through our social media channels',
                                'social_media' => ['facebook', 'twitter', 'instagram', 'linkedin', 'youtube'],
                                'quick_links' => [
                                    ['title' => 'Admission', 'url' => '/admission', 'icon' => 'document-text', 'color' => 'primary'],
                                    ['title' => 'Programs', 'url' => '/programs', 'icon' => 'book-open', 'color' => 'blue'],
                                    ['title' => 'Facilities', 'url' => '/facilities', 'icon' => 'building', 'color' => 'green'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($tenants as $tenant) {
            foreach ($pages as $pageData) {
                // Check if page already exists
                $existingPage = CmsPage::forTenant($tenant->id)
                    ->where('slug', $pageData['slug'])
                    ->first();

                if (!$existingPage) {
                    // Get page slug for config lookup (empty string becomes 'home')
                    $configSlug = $pageData['slug'] === '' ? 'home' : $pageData['slug'];

                    // Get fields config for this page
                    $fields = config("all.cms_fields.{$configSlug}", []);
                    $languages = config('content.pages.languages', ['en' => 'English']);

                    // Initialize fields structure with default values from config
                    $fieldValues = [];
                    foreach ($fields as $field) {
                        $fieldName = $field['name'];
                        foreach ($languages as $langCode => $langName) {
                            $defaultValue = config("content.pages.pages.{$configSlug}.{$langCode}.{$fieldName}", '');

                            // Replace tenant placeholders in default values
                            if (is_string($defaultValue) && str_contains($defaultValue, '{tenant_')) {
                                $tenantData = $tenant->data ?? [];
                                $defaultValue = str_replace('{tenant_name}', $tenantData['name'] ?? 'Our School', $defaultValue);
                                $defaultValue = str_replace('{tenant_description}', $tenantData['description'] ?? 'Excellence in Education', $defaultValue);
                                $defaultValue = str_replace('{tenant_student_count}', $tenantData['student_count'] ?? '500+', $defaultValue);
                            }

                            $fieldValues["{$fieldName}_{$langCode}"] = $defaultValue;
                        }
                    }

                    // Merge old content structure with new fields structure
                    $content = $pageData['content'] ?? [];
                    if (isset($content['sections'])) {
                        // Keep old sections for backward compatibility, but add fields
                        $content['fields'] = $fieldValues;
                    } else {
                        // If no old structure, just use fields
                        $content = ['fields' => $fieldValues];
                    }

                    // Initialize default components for home page only from config
                    if ($pageData['slug'] === '') {
                        $defaultComponents = config('content.pages.default_components', [
                            'features' => [],
                            'programs' => [],
                            'testimonials' => [],
                            'quick_links' => [],
                        ]);

                        // Always set components structure
                        $content['components'] = [
                            'features' => $defaultComponents['features'] ?? [],
                            'programs' => $defaultComponents['programs'] ?? [],
                            'testimonials' => $defaultComponents['testimonials'] ?? [],
                            'quick_links' => $defaultComponents['quick_links'] ?? [],
                        ];
                    }

                    CmsPage::create([
                        'tenant_id' => $tenant->id,
                        'slug' => $pageData['slug'],
                        'title' => $pageData['title'],
                        'meta_description' => $pageData['meta_description'] ?? null,
                        'content' => $content,
                        'is_published' => true,
                    ]);
                } else {
                    // Update existing page to include fields structure if missing
                    $content = $existingPage->content ?? [];
                    if (is_string($content)) {
                        $content = json_decode($content, true) ?? [];
                    }
                    if (!is_array($content)) {
                        $content = [];
                    }

                    // If fields structure is missing or incomplete, add/update it
                    $configSlug = $pageData['slug'] === '' ? 'home' : $pageData['slug'];
                    $fields = config("all.cms_fields.{$configSlug}", []);
                    $languages = config('content.pages.languages', ['en' => 'English']);

                    if (!isset($content['fields'])) {
                        $content['fields'] = [];
                    }

                    // Populate all fields with default values (including missing ones)
                    foreach ($fields as $field) {
                        $fieldName = $field['name'];
                        foreach ($languages as $langCode => $langName) {
                            $key = "{$fieldName}_{$langCode}";
                            // Only set if not already set
                            if (!isset($content['fields'][$key])) {
                                $defaultValue = config("content.pages.pages.{$configSlug}.{$langCode}.{$fieldName}", '');

                                // Replace tenant placeholders
                                if (is_string($defaultValue) && str_contains($defaultValue, '{tenant_')) {
                                    $tenantData = $tenant->data ?? [];
                                    $defaultValue = str_replace('{tenant_name}', $tenantData['name'] ?? 'Our School', $defaultValue);
                                    $defaultValue = str_replace('{tenant_description}', $tenantData['description'] ?? 'Excellence in Education', $defaultValue);
                                    $defaultValue = str_replace('{tenant_student_count}', $tenantData['student_count'] ?? '500+', $defaultValue);
                                }

                                if (!empty($defaultValue)) {
                                    $content['fields'][$key] = $defaultValue;
                                }
                            }
                        }
                    }

                    $existingPage->update(['content' => $content]);

                    // Initialize default components for home page if missing or empty
                    if ($pageData['slug'] === '') {
                        $defaultComponents = config('content.pages.default_components', [
                            'features' => [],
                            'programs' => [],
                            'testimonials' => [],
                            'quick_links' => [],
                        ]);

                        // Always ensure components structure exists
                        if (!isset($content['components'])) {
                            $content['components'] = [
                                'features' => [],
                                'programs' => [],
                                'testimonials' => [],
                                'quick_links' => [],
                            ];
                        }

                        // Apply defaults for each type if empty
                        foreach (['features', 'programs', 'testimonials', 'quick_links'] as $type) {
                            if (empty($content['components'][$type]) && !empty($defaultComponents[$type])) {
                                $content['components'][$type] = $defaultComponents[$type];
                            }
                        }

                        $existingPage->update(['content' => $content]);
                    }

                    // Initialize default program_cards for programs page if missing or empty
                    if ($pageData['slug'] === 'programs') {
                        $defaultProgramCards = config('content.pages.default_components.program_cards', []);

                        // Always ensure components structure exists
                        if (!isset($content['components'])) {
                            $content['components'] = [
                                'program_cards' => [],
                            ];
                        }

                        // Apply defaults if empty
                        if (empty($content['components']['program_cards']) && !empty($defaultProgramCards)) {
                            $content['components']['program_cards'] = $defaultProgramCards;
                            $existingPage->update(['content' => $content]);
                        }
                    }

                    // Initialize default facility_cards and amenity_cards for facilities page if missing or empty
                    if ($pageData['slug'] === 'facilities') {
                        $defaultFacilityCards = config('content.pages.default_components.facility_cards', []);
                        $defaultAmenityCards = config('content.pages.default_components.amenity_cards', []);

                        // Always ensure components structure exists
                        if (!isset($content['components'])) {
                            $content['components'] = [
                                'facility_cards' => [],
                                'amenity_cards' => [],
                            ];
                        }

                        // Apply defaults if empty
                        if (empty($content['components']['facility_cards']) && !empty($defaultFacilityCards)) {
                            $content['components']['facility_cards'] = $defaultFacilityCards;
                        }
                        if (empty($content['components']['amenity_cards']) && !empty($defaultAmenityCards)) {
                            $content['components']['amenity_cards'] = $defaultAmenityCards;
                        }

                        if ((!empty($content['components']['facility_cards']) && empty($existingPage->content['components']['facility_cards'] ?? [])) ||
                            (!empty($content['components']['amenity_cards']) && empty($existingPage->content['components']['amenity_cards'] ?? []))) {
                            $existingPage->update(['content' => $content]);
                        }
                    }

                    // Initialize default components for admission page if missing or empty
                    if ($pageData['slug'] === 'admission') {
                        $defaultProcessSteps = config('content.pages.default_components.process_steps', []);
                        $defaultRequirementCards = config('content.pages.default_components.requirement_cards', []);
                        $defaultDateCards = config('content.pages.default_components.date_cards', []);
                        $defaultFaqItems = config('content.pages.default_components.faq_items', []);

                        // Always ensure components structure exists
                        if (!isset($content['components'])) {
                            $content['components'] = [
                                'process_steps' => [],
                                'requirement_cards' => [],
                                'date_cards' => [],
                                'faq_items' => [],
                            ];
                        }

                        // Apply defaults if empty
                        if (empty($content['components']['process_steps']) && !empty($defaultProcessSteps)) {
                            $content['components']['process_steps'] = $defaultProcessSteps;
                        }
                        if (empty($content['components']['requirement_cards']) && !empty($defaultRequirementCards)) {
                            $content['components']['requirement_cards'] = $defaultRequirementCards;
                        }
                        if (empty($content['components']['date_cards']) && !empty($defaultDateCards)) {
                            $content['components']['date_cards'] = $defaultDateCards;
                        }
                        if (empty($content['components']['faq_items']) && !empty($defaultFaqItems)) {
                            $content['components']['faq_items'] = $defaultFaqItems;
                        }

                        if ((!empty($content['components']['process_steps']) && empty($existingPage->content['components']['process_steps'] ?? [])) ||
                            (!empty($content['components']['requirement_cards']) && empty($existingPage->content['components']['requirement_cards'] ?? [])) ||
                            (!empty($content['components']['date_cards']) && empty($existingPage->content['components']['date_cards'] ?? [])) ||
                            (!empty($content['components']['faq_items']) && empty($existingPage->content['components']['faq_items'] ?? []))) {
                            $existingPage->update(['content' => $content]);
                        }
                    }
                }
            }
        }
    }
}
