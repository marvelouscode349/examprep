@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>ExamPrep NG <small>Admin Dashboard</small></h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Stats Row -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3>{{ number_format($stats['total_users']) }}</h3><p>Total Users</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('admin.users') }}" class="small-box-footer">View All <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ number_format($stats['premium_users']) }}</h3><p>Premium Users</p></div>
            <div class="icon"><i class="fas fa-star"></i></div>
            <a href="{{ route('admin.users') }}?filter=premium" class="small-box-footer">View Premium <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3>₦{{ number_format($stats['total_revenue']) }}</h3><p>Total Revenue</p></div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="{{ route('admin.revenue') }}" class="small-box-footer">View Revenue <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ number_format($stats['total_questions']) }}</h3><p>Total Questions</p></div>
            <div class="icon"><i class="fas fa-question-circle"></i></div>
            <a href="#" class="small-box-footer">View Questions <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<!-- Second Row -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background:#6f42c1;color:#fff">
            <div class="inner"><h3>{{ number_format($stats['total_sessions']) }}</h3><p>Total Sessions</p></div>
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
            <a href="#" class="small-box-footer" style="color:rgba(255,255,255,.7)">All Time <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background:#20c997;color:#fff">
            <div class="inner"><h3>{{ $stats['sessions_today'] }}</h3><p>Sessions Today</p></div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <a href="#" class="small-box-footer" style="color:rgba(255,255,255,.7)">Today <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background:#fd7e14;color:#fff">
            <div class="inner"><h3>{{ $stats['new_users_today'] }}</h3><p>New Users Today</p></div>
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <a href="#" class="small-box-footer" style="color:rgba(255,255,255,.7)">Today <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box" style="background:#e83e8c;color:#fff">
            <div class="inner"><h3>₦{{ number_format($stats['revenue_month']) }}</h3><p>Revenue This Month</p></div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <a href="{{ route('admin.revenue') }}" class="small-box-footer" style="color:rgba(255,255,255,.7)">View <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sessions Chart -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar mr-1"></i> Sessions — Last 7 Days</h3>
            </div>
            <div class="card-body">
                <canvas id="sessionsChart" height="120"></canvas>
            </div>
        </div>
    </div>

    <!-- Questions by Subject -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-book mr-1"></i> Questions by Subject</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        @foreach($questionsBySubject as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td class="text-right"><strong>{{ number_format($row->total) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('sessionsChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($sessionsChart->pluck('date')) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode($sessionsChart->pluck('count')) !!},
            backgroundColor: 'rgba(78, 115, 223, 0.7)',
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@stop