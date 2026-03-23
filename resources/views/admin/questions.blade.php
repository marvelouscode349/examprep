@extends('adminlte::page')

@section('title', 'Questions')

@section('content_header')
    <h1>Question Bank</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('error') }}
</div>
@endif

<div class="row">
    <!-- Question counts table -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-database mr-1"></i>
                    Questions by Subject
                    <span class="badge badge-primary ml-2">
                        {{ $breakdown->sum('total_count') }} total
                    </span>
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Subject</th>
                            <th class="text-center">JAMB</th>
                            <th class="text-center">WAEC</th>
                            <th class="text-center">Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($breakdown as $subject)
                        <tr>
                            <td>
                                <span style="font-size:1.1rem">{{ $subject->icon }}</span>
                                {{ $subject->name }}
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $subject->jamb_count > 0 ? 'info' : 'secondary' }}">
                                    {{ number_format($subject->jamb_count) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $subject->waec_count > 0 ? 'success' : 'secondary' }}">
                                    {{ number_format($subject->waec_count) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($subject->total_count) }}</strong>
                            </td>
                            <td>
                                <div class="d-flex gap-1" style="gap:4px">
                                    @if($subject->jamb_count > 0)
                                    <form method="POST" action="{{ route('admin.questions.delete') }}"
                                          onsubmit="return confirm('Delete ALL {{ number_format($subject->jamb_count) }} JAMB questions for {{ $subject->name }}? This cannot be undone.')">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                        <input type="hidden" name="exam_type" value="JAMB">
                                        <button type="submit" class="btn btn-xs btn-warning" title="Delete JAMB">
                                            <i class="fas fa-trash"></i> JAMB
                                        </button>
                                    </form>
                                    @endif

                                    @if($subject->waec_count > 0)
                                    <form method="POST" action="{{ route('admin.questions.delete') }}"
                                          onsubmit="return confirm('Delete ALL {{ number_format($subject->waec_count) }} WAEC questions for {{ $subject->name }}? This cannot be undone.')">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                        <input type="hidden" name="exam_type" value="WAEC">
                                        <button type="submit" class="btn btn-xs btn-danger" title="Delete WAEC">
                                            <i class="fas fa-trash"></i> WAEC
                                        </button>
                                    </form>
                                    @endif

                                    @if($subject->total_count > 0)
                                    <form method="POST" action="{{ route('admin.questions.delete') }}"
                                          onsubmit="return confirm('Delete ALL {{ number_format($subject->total_count) }} questions for {{ $subject->name }}? This cannot be undone.')">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                        <input type="hidden" name="exam_type" value="ALL">
                                        <button type="submit" class="btn btn-xs btn-danger" title="Delete All">
                                            <i class="fas fa-trash"></i> All
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td>TOTAL</td>
                            <td class="text-center">{{ number_format($breakdown->sum('jamb_count')) }}</td>
                            <td class="text-center">{{ number_format($breakdown->sum('waec_count')) }}</td>
                            <td class="text-center">{{ number_format($breakdown->sum('total_count')) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- CSV Upload -->
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-upload mr-1"></i> Import CSV</h3>
            </div>
            <form method="POST" action="{{ route('admin.questions.import') }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="card-body">

                    @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Subject</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">Select subject</option>
                            @foreach($breakdown as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Exam Type</label>
                        <select name="exam_type" class="form-control" required>
                            <option value="JAMB">JAMB</option>
                            <option value="WAEC">WAEC</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>CSV File</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="csv_file"
                                   accept=".csv,.txt" required>
                            <label class="custom-file-label">Choose CSV file</label>
                        </div>
                        <small class="text-muted">Max 10MB</small>
                    </div>

                    <!-- CSV Format Guide -->
                    <div class="alert alert-info p-2" style="font-size:.82rem">
                        <strong><i class="fas fa-info-circle"></i> CSV Format:</strong><br>
                        <code>question,option_a,option_b,option_c,option_d,correct_answer,year</code>
                        <hr class="my-1">
                        <strong>correct_answer</strong> can be any of these:<br>
                        <code>A</code> &nbsp;|&nbsp;
                        <code>option_a</code> &nbsp;|&nbsp;
                        <code>Option A</code> &nbsp;|&nbsp;
                        <code>Option D</code><br><br>
                        <strong>year</strong> is optional — leave blank if unknown.<br><br>
                        <strong>Example row:</strong><br>
                        <small>
                        <code>What is photosynthesis?,Process of making food,Breathing,Movement,Growth,A,2019</code>
                        </small>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-upload mr-1"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>

        <!-- Download sample CSV -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-download mr-1"></i> Sample CSV</h3>
            </div>
            <div class="card-body">
                <p style="font-size:.85rem">Download a sample CSV to see the correct format:</p>
                <a href="" class="btn btn-secondary btn-sm">
                    <i class="fas fa-download mr-1"></i> Download Sample
                </a>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
// Show filename when file is selected
document.querySelector('.custom-file-input')?.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Choose file';
    this.nextElementSibling.textContent = fileName;
});
</script>
@stop