@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>Users <small class="text-muted">{{ $users->total() }} total</small></h1>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    {{ session('success') }}
</div>
@endif

<!-- Search & Filter -->
<div class="card">
    <div class="card-header">
        <form method="GET" class="d-flex gap-2" style="gap:10px">
            <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..."
                   value="{{ request('search') }}" style="max-width:300px">
            <select name="filter" class="form-control" style="max-width:150px" onchange="this.form.submit()">
                <option value="">All Users</option>
                <option value="premium" {{ request('filter') === 'premium' ? 'selected' : '' }}>Premium</option>
                <option value="free"    {{ request('filter') === 'free'    ? 'selected' : '' }}>Free</option>
                <option value="banned"  {{ request('filter') === 'banned'  ? 'selected' : '' }}>Banned</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Stream</th>
                    <th>Plan</th>
                    <th>Streak</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="{{ $user->is_banned ? 'table-danger' : '' }}">
                    <td>
                        <strong>{{ $user->name }}</strong>
                        @if($user->is_banned)
                            <span class="badge badge-danger ml-1">Banned</span>
                        @endif
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '--' }}</td>
                    <td><span class="badge badge-info">{{ $user->stream ?? '--' }}</span></td>
                    <td>
                        @if($user->subscription_status === 'active')
                            <span class="badge badge-success">Premium</span>
                            <div style="font-size:.75rem;color:#666">
                                Expires {{ $user->subscription_expires_at?->format('d M Y') }}
                            </div>
                        @else
                            <span class="badge badge-secondary">Free</span>
                        @endif
                    </td>
                    <td>🔥 {{ $user->streak_days ?? 0 }}</td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex" style="gap:4px">
                            @if($user->subscription_status !== 'active')
                            <form method="POST" action="{{ route('admin.users.premium', $user) }}">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-success" title="Make Premium">
                                    <i class="fas fa-star"></i>
                                </button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-xs {{ $user->is_banned ? 'btn-warning' : 'btn-danger' }}"
                                    title="{{ $user->is_banned ? 'Unban' : 'Ban' }}">
                                    <i class="fas fa-{{ $user->is_banned ? 'unlock' : 'ban' }}"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $users->links() }}
    </div>
</div>

@stop