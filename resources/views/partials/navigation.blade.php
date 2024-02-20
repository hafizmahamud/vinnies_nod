@if (!auth()->user()->google2fa_enabled_at)
<nav id="site-nav" class="text-right">
    <a href="{{ route('resources.index') }}" class="first disabled">Resources</a>

    @can('read.projects')
        <a href="{{ route('projects.list') }}" class="disabled">Projects</a>
    @endcan

    @can('read.local-conf')
        <a href="{{ route('local-conferences.list') }}" class="disabled">Australian Conferences</a>
    @endcan

    @can('read.os-conf')
        <a href="{{ route('overseas-conferences.list') }}" class="disabled">Overseas Conferences</a>
    @endcan

    @can('read.twinnings')
        <a href="{{ route('twinnings.list') }}" class="disabled">Twinnings</a>
    @endcan

    @can('read.new-remittances')
        <a href="{{ route('new-remittances.list') }}" class="disabled">Remittances</a>
    @endcan

    @can('read.beneficiaries')
        <a href="{{ route('beneficiaries.list') }}" class="disabled">Beneficiaries</a>
    @endcan

    @can('read.users')
        <a href="{{ route('users.list') }}" class="disabled">Users</a>
    @endcan

    @can('read.reports')
        <a href="{{ route('reports.list') }}" class="disabled">Reports</a>
    @endcan

    <a href="{{ route('2fa.index') }}" class="last">MFA</a>
</nav>
@else
<nav id="site-nav" class="text-right">

    <a href="{{ route('resources.index') }}" class="first">Resources</a>

    @can('read.projects')
        <a href="{{ route('projects.list') }}">Projects</a>
    @endcan

    @can('read.local-conf')
        <a href="{{ route('local-conferences.list') }}">Australian Conferences</a>
    @endcan

    @can('read.os-conf')
        <a href="{{ route('overseas-conferences.list') }}">Overseas Conferences</a>
    @endcan

    @can('read.twinnings')
        <a href="{{ route('twinnings.list') }}">Twinnings</a>
    @endcan

    @can('read.new-remittances')
        <a href="{{ route('new-remittances.list') }}">Remittances</a>
    @endcan

    @can('read.beneficiaries')
        <a href="{{ route('beneficiaries.list') }}">Beneficiaries</a>
    @endcan

    @can('read.users')
        <a href="{{ route('users.list') }}">Users</a>
    @endcan

    @can('read.reports')
        <a href="{{ route('reports.list') }}">Reports</a>
    @endcan

    <a href="{{ route('2fa.index') }}" class="last">MFA</a>
</nav>
@endif

