@extends('adminlte::page')

@section('title', 'Revenue')

@section('content_header')
    <h1>Revenue Tracking</h1>
@stop

@section('content')

<!-- Plan Stats -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">weekly Plans</span>
                <span class="info-box-number">{{ $planStats['weekly'] }}</span>
                <span class="progress-description">₦{{ number_format($planStats['weekly'] * 1499) }} total</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-calendar-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">monthly Plans</span>
                <span class="info-box-number">{{ $planStats['monthly'] }}</span>
                <span class="progress-description">₦{{ number_format($planStats['monthly'] * 3999) }} total</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-calendar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Yearly Plans</span>
                <span class="info-box-number">{{ $planStats['yearly'] }}</span>
                <span class="progress-description">₦{{ number_format($planStats['yearly'] * 14999) }} total</span>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Revenue Chart -->
<div class="card mb-4">
    <div class="card-header"><h3 class="card-title">Revenue — Last 6 Months</h3></div>
    <div class="card-body">
        <canvas id="revenueChart" height="80"></canvas>
    </div>
</div>

<!-- Subscription Table -->
<div class="card">
    <div class="card-header"><h3 class="card-title">Recent Subscriptions</h3></div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Starts</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $sub)
                <tr>
                    <td>
                        <strong>{{ $sub->user->name ?? '--' }}</strong>
                        <div style="font-size:.75rem;color:#666">{{ $sub->user->email ?? '' }}</div>
                    </td>
                    <td><span class="badge badge-primary">{{ ucfirst($sub->plan) }}</span></td>
                    <td>
                        ₦{{ number_format(match($sub->plan) {
                            'monthly'   => 1499,
                            'quarterly' => 3999,
                            'yearly'    => 14999,
                            default     => 0
                        }) }}
                    </td>
                    <td><code style="font-size:.75rem">{{ $sub->paystack_reference }}</code></td>
                    <td>
                        <span class="badge badge-{{ $sub->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($sub->status) }}
                        </span>
                    </td>
                    <td>{{ $sub->starts_at->format('d M Y') }}</td>
                    <td>{{ $sub->expires_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
        datasets: [{
            label: 'Revenue (₦)',
            data: {!! json_encode($monthlyRevenue->pluck('revenue')) !!},
            borderColor: 'rgba(78, 115, 223, 1)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});
</script>
@stop