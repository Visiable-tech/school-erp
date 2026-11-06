        <div class="menu-title">
            Main
        </div>


        <a
            href="{{ route('dashboard') }}"
            class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">

            <i class="bi bi-speedometer2"></i>

            Dashboard

        </a>



        