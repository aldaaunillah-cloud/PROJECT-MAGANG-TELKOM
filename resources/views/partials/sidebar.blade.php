<div class="sidebar">
    <div class="logo" style="text-align:center; padding:10px 20px;">
        <img src="{{ asset('image/logo.png') }}" alt="Telkom Indonesia" style="width: 100%; max-width: 160px;">
    </div>

    <ul>
        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" onclick="window.location.href='{{ route('dashboard') }}'">
            <i class="bi bi-house-door-fill"></i>
            Dashboard
        </li>

        <li class="{{ request()->routeIs('data.all') ? 'active' : '' }}" onclick="window.location.href='{{ route('data.all') }}'">
            <i class="bi bi-file-earmark-text"></i>
            Data All
        </li>

        <p class="menu-title">
            MASTER DATA
        </p>

        <li class="{{ request()->routeIs('agency.mentah') ? 'active' : '' }}" onclick="window.location.href='{{ route('agency.mentah') }}'">
            <i class="bi bi-database"></i>
            Agency Mentah
        </li>

        <li class="{{ request()->routeIs('rekap.billing') ? 'active' : '' }}" onclick="window.location.href='{{ route('rekap.billing') }}'">
            <i class="bi bi-calendar3"></i>
            Rekap Billing 1-2
        </li>

        <li class="{{ request()->routeIs('saldo') ? 'active' : '' }}" onclick="window.location.href='{{ route('saldo') }}'">
            <i class="bi bi-wallet2"></i>
            Saldo
        </li>

        <li class="{{ request()->routeIs('riwayat.reminder') ? 'active' : '' }}" onclick="window.location.href='{{ route('riwayat.reminder') }}'">
            <i class="bi bi-chat-dots"></i>
            Riwayat Reminder
        </li>

        <li class="logout">
            <i class="bi bi-power"></i>
            Logout
        </li>
    </ul>
</div>