@extends('adminlte::page')

@section('title', 'Marketers')

@section('content_header')
    <h1>Marketer Management</h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

<div class="row">
    <!-- Add Marketer -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Add Marketer</h3></div>
            <form method="POST" action="{{ route('admin.marketers.create') }}">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}" required placeholder="e.g. Emeka John">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}" placeholder="08012345678">
                    </div>
                    <p class="text-muted" style="font-size:.8rem">
                        A unique referral code will be generated automatically.
                        Commission: 20% of first month payment.
                    </p>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">Add Marketer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Marketers List -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">All Marketers ({{ $marketers->count() }})</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Referral Code</th>
                            <th>Referrals</th>
                            <th>Pending</th>
                            <th>Total Paid</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($marketers as $m)
                        <tr>
                            <td>
                                <strong>{{ $m->name }}</strong>
                                <div style="font-size:.75rem;color:#666">{{ $m->email }}</div>
                            </td>
                            <td>
                                <code class="text-primary">{{ $m->referral_code }}</code>
                            </td>
                            <td>{{ $m->total_referrals }}</td>
                            <td>
                                <span class="{{ $m->pending_commission > 0 ? 'text-warning font-weight-bold' : 'text-muted' }}">
                                    ₦{{ number_format($m->pending_commission) }}
                                </span>
                            </td>
                            <td>₦{{ number_format($m->paid_commission) }}</td>
                            <td>
                                <span class="badge badge-{{ $m->is_active ? 'success' : 'danger' }}">
                                    {{ $m->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex" style="gap:4px">
                                    @if($m->pending_commission > 0)
                                    <form method="POST" action="{{ route('admin.marketers.pay', $m) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success"
                                                onclick="return confirm('Mark ₦{{ number_format($m->pending_commission) }} as paid?')">
                                            Pay
                                        </button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.marketers.toggle', $m) }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-xs {{ $m->is_active ? 'btn-warning' : 'btn-success' }}">
                                            {{ $m->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @if($marketers->isEmpty())
                        <tr><td colspan="7" class="text-center text-muted py-3">No marketers yet</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop