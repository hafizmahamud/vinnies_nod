<header id="site-header">
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                <a href="{{ route('home') }}" class="logo"><img src="{{ asset('assets/img/logo.jpg') }}" alt="Vinnies"></a>
            </div>
            <div class="col-sm-9">
                <div class="row">
                    <div class="col-sm-8">
                        <p class="site-description"><strong>{{ config('app.name') }}</strong></p>
                    </div>
                    <div class="col-sm-4 text-right">
                        <p class="site-user">
                            Welcome, <strong>{{ $currentUser->first_name }} {{ $currentUser->last_name }}</strong>
                            @if ($currentUser->branch_display)
                                from <strong>{{ $currentUser->branch_display }}</strong>
                            @endif
                            <!-- <span class="separator">|</span> -->
                            <br>
                            <a href="{{ route('logout') }}"><strong>Logout</strong> <i class="fa fa-sign-out" aria-hidden="true"></i></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @include('partials.navigation')
        </div>
    </div>
</header>
