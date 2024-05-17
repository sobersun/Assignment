@extends('layout.main.main')


@section('content')


<div class="content">

    <!-- Navbar Start -->
    <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
        <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
            <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
        </a>
        <a href="#" class="sidebar-toggler flex-shrink-0">
            <i class="fa fa-bars"></i>
        </a>
        <div class="navbar-nav align-items-center ms-auto">
            <div class="nav-item dropdown">

                <span class="d-none d-lg-inline-flex">Total Earnings : @if(auth()->check())
                    {{ auth()->user()->getTotalEarning() }}

                    {{ !empty(auth()->user()->earning->commission_percentage) ? '(Including referalls)' : '(No Referalls Added)' }}

                    @endif</span>

            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <span class="d-none d-lg-inline-flex">
                        @if(auth()->check())
                        {{ auth()->user()->name }}
                        @endif
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->


    <!-- Sale & Revenue Start -->
    <div class="col-12">
        <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">My Referral</h6>
            <div class="table-responsive">
                <table class="table">
                    <thead>

                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">State</th>
                            <th scope="col">Referral %</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @if($referrals->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center">No referrals yet</td>
                        </tr>
                        @else
                        @foreach ($referrals as $referral)
                        <tr>
                            <td scope="row">{{ $referral->id }} </td>
                            <td>{{ $referral->name}} </td>
                            <td>{{ $referral->email}}</td>
                            <td>{!! $referral->getStateValue() !!}</td>
                            <td>{{auth()->user()->earning->commission_percentage }} </td>
                            <td>{{ \Carbon\Carbon::parse($referral->created_at)->format('j F Y') }} </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="navbar-nav align-items-center ms-auto">
                    {{ $referrals->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Content End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
</div>
@endsection