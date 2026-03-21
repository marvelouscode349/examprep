<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Science stream
            ['id' => 1,  'name' => 'Mathematics',          'slug' => 'mathematics',    'stream' => 'science',     'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 2,  'name' => 'English Language',     'slug' => 'english',        'stream' => 'all',         'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 3,  'name' => 'Chemistry',            'slug' => 'chemistry',      'stream' => 'science',     'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 4,  'name' => 'Biology',              'slug' => 'biology',        'stream' => 'science',     'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 5,  'name' => 'Physics',              'slug' => 'physics',        'stream' => 'science',     'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 6,  'name' => 'Further Mathematics',  'slug' => 'further-maths',  'stream' => 'science',     'exam_types' => '["JAMB","WAEC"]'],

            // Arts stream
            ['id' => 7,  'name' => 'Literature in English','slug' => 'literature',     'stream' => 'arts',        'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 8,  'name' => 'Government',           'slug' => 'government',     'stream' => 'arts',        'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 9,  'name' => 'CRK',                  'slug' => 'crk',            'stream' => 'arts',        'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 10, 'name' => 'History',              'slug' => 'history',        'stream' => 'arts',        'exam_types' => '["JAMB","WAEC"]'],
            ['id' => 11, 'name' => 'Geography',            'slug' => 'geography',      'stream' => 'arts',        'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 12, 'name' => 'IRK',                  'slug' => 'irk',            'stream' => 'arts',        'exam_types' => '["JAMB","WAEC"]'],

            // Commercial stream
            ['id' => 13, 'name' => 'Economics',            'slug' => 'economics',      'stream' => 'commercial',  'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 14, 'name' => 'Accounting',           'slug' => 'accounting',     'stream' => 'commercial',  'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 15, 'name' => 'Commerce',             'slug' => 'commerce',       'stream' => 'commercial',  'exam_types' => '["JAMB","WAEC","NECO"]'],
            ['id' => 16, 'name' => 'Business Studies',     'slug' => 'business-studies','stream' => 'commercial', 'exam_types' => '["JAMB","WAEC"]'],
        ];

        DB::table('subjects')->insertOrIgnore($subjects);
    }
}