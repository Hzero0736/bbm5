@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</h1>
    </div>
</div>
@endsection

@section('content')
<!-- Info Boxes -->
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalLiter, 2) }} L</h3>
                <p>Total BBM</p>
            </div>
            <div class="icon">
                <i class="fas fa-gas-pump"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($totalDana, 0, ',', '.') }}</h3>
                <p>Total Dana</p>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalClaims }}</h3>
                <p>Total Klaim</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    @if(Auth::user()->roles->contains('nama', 'Admin'))
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $pendingUsers }}</h3>
                <p>User Menunggu Approval</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>
    </div>
    @endif


    @push('scripts')
    <script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            toast: true,
            position: 'top-end',
            timer: 3000
        });
        @endif
    </script>
    @endpush

    @endsection