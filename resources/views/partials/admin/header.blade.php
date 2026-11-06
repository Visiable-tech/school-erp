

@if(!request()->is('dashboard'))
    <header class="admin-header">
@else 
    <header class="admin-header" style="left:0;">
@endif

    <div class="container-fluid">

        <div class="d-flex
                    align-items-center
                    justify-content-between">

            <div class="d-flex align-items-center">

                <button
                    id="sidebarToggle"
                    class="btn btn-light me-3 d-lg-none">

                    <i class="bi bi-list"></i>

                </button>


                <div>

                    <a href="{{ route('modules.index') }}" class="all_modules" style="color: #000;
    margin-left: 30px;
    font-weight: bold;
    font-size: 18px;
    text-decoration: none;">
                        <i class="bi bi-grid-3x3-gap-fill"></i> All Modules
                    </a>

                    @if(!request()->is('dashboard'))
                        @if(auth()->user()->school)

                            <strong>
                                <a href="/dashboard">{{ auth()->user()->school->school_name }}</a>
                            </strong>

                        @else

                            <strong>
                                School ERP
                            </strong>

                        @endif
                    @else 
                        @if(auth()->user()->school)

                             <a href="/dashboard"><img src="/assets/images/header_logo.png" style="width:30%;"></a>

                        @else

                            <strong>
                                School ERP
                            </strong>

                        @endif
                    @endif

                </div>

            </div>


            <div class="dropdown">

                <button
                    class="btn dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown">

                    <i class="bi bi-person-circle me-1"></i>

                    {{ auth()->user()->name }}

                </button>


                <ul class="dropdown-menu dropdown-menu-end">

                    <li>

                        <span class="dropdown-item-text">

                            <small class="text-muted">
                                Role
                            </small>

                            <br>

                            <strong>
                                {{ auth()->user()
                                    ->getRoleNames()
                                    ->implode(', ') }}
                            </strong>

                        </span>

                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                        <form
                            method="POST"
                            action="{{ route('logout') }}">

                            @csrf

                            <button
                                type="submit"
                                class="dropdown-item text-danger">

                                <i class="bi bi-box-arrow-right me-2"></i>

                                Logout

                            </button>

                        </form>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</header>