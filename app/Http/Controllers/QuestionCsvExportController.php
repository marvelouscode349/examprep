<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionCsvExportController extends Controller
{
    public function index()
    {
        $subjects = DB::table('subjects')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name']);

        $examTypes = ['JAMB', 'WAEC', 'NECO', 'Post-UTME'];

        return view('admin.question-csv-export.index', compact('subjects', 'examTypes'));
    }

    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'exam_type' => ['required', 'in:JAMB,WAEC,NECO,Post-UTME'],
            'limit' => ['required', 'integer', 'min:1', 'max:1000'],
        ]);

        $subject = DB::table('subjects')
            ->where('id', $validated['subject_id'])
            ->first();

        abort_if(!$subject, 404, 'Subject not found.');

        $limit = (int) $validated['limit'];
        $examType = $validated['exam_type'];

        $totalAvailable = DB::table('questions')
            ->where('subject_id', $validated['subject_id'])
            ->where('exam_type', $examType)
            ->count();

        if ($totalAvailable < $limit) {
            return back()
                ->withErrors([
                    'limit' => "Only {$totalAvailable} questions are available for {$subject->name} {$examType}.",
                ])
                ->withInput();
        }

        $safeSubjectName = strtolower(str_replace(' ', '_', $subject->name));
        $safeExamType = strtolower(str_replace('-', '_', $examType));

        $filename = "{$safeSubjectName}_{$safeExamType}_{$limit}_questions.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->streamDownload(function () use ($validated, $examType, $limit) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens the CSV correctly
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'question_uid',
                'exam_type',
                'subject',
                'topic',
                'question',
                'option_a',
                'option_b',
                'option_c',
                'option_d',
                'correct_answer',
                'explanation',
                'question_image',
                'year',
            ]);

            $questions = DB::table('questions as q')
                ->join('subjects as s', 'q.subject_id', '=', 's.id')
                ->leftJoin('topics as t', 'q.topic_id', '=', 't.id')
                ->where('q.subject_id', $validated['subject_id'])
                ->where('q.exam_type', $examType)
                ->whereNotNull('q.question_text')
                ->whereNotNull('q.option_a')
                ->whereNotNull('q.option_b')
                ->whereNotNull('q.option_c')
                ->whereNotNull('q.option_d')
                ->whereNotNull('q.correct_answer')
                ->inRandomOrder()
                ->limit($limit)
                ->select([
                    'q.id',
                    'q.exam_type',
                    's.name as subject',
                    't.name as topic',
                    'q.question_text',
                    'q.option_a',
                    'q.option_b',
                    'q.option_c',
                    'q.option_d',
                    'q.correct_answer',
                    'q.explanation',
                    'q.image_url',
                    'q.year',
                ])
                ->get();

            foreach ($questions as $question) {
                $subjectCode = strtoupper(substr(preg_replace('/\s+/', '', $question->subject), 0, 3));

                $extractedImageUrl = $this->extractFirstImageUrl($question->question_text);

                // Use image extracted from question HTML first.
                // If no image exists inside question_text, fallback to q.image_url.
                $questionImage = $extractedImageUrl ?: ($question->image_url ?? '');

                fputcsv($file, [
                    strtoupper($question->exam_type) . '-' . $subjectCode . '-' . str_pad($question->id, 4, '0', STR_PAD_LEFT),
                    $question->exam_type,
                    $question->subject,
                    $question->topic ?? '',
                    $question->question_text, // leave HTML exactly as it is
                    $question->option_a,
                    $question->option_b,
                    $question->option_c,
                    $question->option_d,
                    $question->correct_answer ?? '',
                    $question->explanation ?? '',
                    $questionImage,
                    $question->year ?? '',
                ]);
            }

            fclose($file);
        }, $filename, $headers);
    }

    private function extractFirstImageUrl(?string $html): string
    {
        if (!$html) {
            return '';
        }

        // Decode in case HTML is stored as escaped entities like:
        // &lt;img src=&quot;https://example.com/image.jpg&quot;&gt;
        $decodedHtml = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Match first img src with double quote, single quote, or no quote
        preg_match('/<img[^>]+src=["\']?([^"\'>\s]+)["\']?/i', $decodedHtml, $matches);

        return $matches[1] ?? '';
    }
}