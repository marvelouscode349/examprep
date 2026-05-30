@extends('admin.layout')

@section('content')
<div class="container">
    <h1>Export Questions CSV</h1>

    @if ($errors->any())
        <div style="background: #7a7676; padding: 12px; margin-bottom: 16px; border: 1px solid #ffb3b3;">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
<form method="POST" action="{{ route('admin.question-csv-export.export') }}">
        @csrf

        <div style="margin-bottom: 16px;">
            <label for="subject_id">Subject</label><br>

            <select name="subject_id" id="subject_id" required>
                <option value="">Select subject</option>

                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected(old('subject_id') == $subject->id)>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="exam_type">Exam Type</label><br>

            <select name="exam_type" id="exam_type" required>
                @foreach ($examTypes as $examType)
                    <option value="{{ $examType }}" @selected(old('exam_type', 'JAMB') === $examType)>
                        {{ $examType }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom: 16px;">
            <label for="limit">Number of Questions</label><br>

            <input
                type="number"
                name="limit"
                id="limit"
                value="{{ old('limit', 50) }}"
                min="1"
                max="1000"
                required
            >
        </div>

        <button type="submit">
            Download CSV
        </button>
    </form>
</div>
@endsection