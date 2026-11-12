{{-- Administration --}}

        <div class="menu-title">
            Administration
        </div>


        @hasanyrole('Super Admin|Admin')
        <a href="{{ route('access.users.index') }}"><i class="bi bi-person-gear"></i> Users</a>
        @endhasanyrole


        @role('Super Admin')
        <a href="{{ route('access.roles.index') }}"><i class="bi bi-shield-lock"></i> Roles & Permissions</a>
        @endrole


        <a href="#">

            <i class="bi bi-gear"></i>

            Settings

        </a>

