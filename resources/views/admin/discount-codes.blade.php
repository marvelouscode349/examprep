@extends('adminlte::page')

@section('title', 'Discount Codes')

@section('content_header')
    <h1>Discount Codes</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

<div class="row">
    <!-- Create Code Form -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Create New Code</h3></div>
            <form method="POST" action="{{ route('admin.codes.create') }}">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Code</label>
                        <div class="input-group">
                            <input type="text" name="code" class="form-control text-uppercase"
                                   placeholder="e.g. EARLYNG30" value="{{ old('code') }}" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary" onclick="generateCode()">
                                    Generate
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Discount Percent (%)</label>
                        <input type="number" name="percent" class="form-control"
                               value="{{ old('percent', 10) }}" min="1" max="100" required>
                    </div>

                    <div class="form-group">
                        <label>Max Uses <small class="text-muted">(leave blank for unlimited)</small></label>
                        <input type="number" name="max_uses" class="form-control"
                               value="{{ old('max_uses') }}" min="1" placeholder="Unlimited">
                    </div>

                    <div class="form-group">
                        <label>Expiry Date <small class="text-muted">(leave blank for no expiry)</small></label>
                        <input type="date" name="expires_at" class="form-control"
                               value="{{ old('expires_at') }}">
                    </div>

                    <div class="form-group">
                        <label>Description <small class="text-muted">(internal note)</small></label>
                        <input type="text" name="description" class="form-control"
                               value="{{ old('description') }}" placeholder="e.g. For warm leads launch">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Create Code</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Codes List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">All Codes ({{ $codes->count() }})</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Used</th>
                            <th>Max Uses</th>
                            <th>Expires</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($codes as $code)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $code->code }}</strong>
                                @if($code->description)
                                    <div style="font-size:.75rem;color:#666">{{ $code->description }}</div>
                                @endif
                            </td>
                            <td><span class="badge badge-warning">{{ $code->percent }}% OFF</span></td>
                            <td>{{ $code->used_count }}</td>
                            <td>{{ $code->max_uses ?? '∞' }}</td>
                            <td>
                                @if($code->expires_at)
                                    <span class="{{ $code->expires_at->isPast() ? 'text-danger' : '' }}">
                                        {{ $code->expires_at->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                            <td>
                                @if($code->isValid())
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex" style="gap:4px">
                                    <form method="POST" action="{{ route('admin.codes.toggle', $code) }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-xs {{ $code->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $code->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.codes.delete', $code) }}"
                                          onsubmit="return confirm('Delete this code?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if($codes->isEmpty())
                        <tr><td colspan="7" class="text-center text-muted py-3">No discount codes yet</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script>
function generateCode() {
    fetch('{{ route('admin.codes.generate') }}')
        .then(r => r.json())
        .then(d => {
            document.querySelector('input[name="code"]').value = d.code;
        });
}
</script>
@stop