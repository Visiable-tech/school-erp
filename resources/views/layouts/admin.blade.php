<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Dashboard') | School ERP
    </title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <style>

        body {
            background: #f5f7fb;
            font-size: 14px;
        }

        /* Sidebar */

        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;

            width: 260px;

            background: #17263c;
            color: #fff;

            overflow-y: auto;

            z-index: 1000;

            transition: all .3s;
        }

        .sidebar-brand {
            height: 70px;

            display: flex;
            align-items: center;

            padding: 0 20px;

            border-bottom:
                1px solid rgba(255,255,255,.1);
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 15px 10px;
        }

        .sidebar-menu .menu-title {
            color: #8fa3bf;

            font-size: 11px;
            font-weight: 600;

            text-transform: uppercase;

            padding: 15px 15px 7px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;

            color: #c9d3df;

            text-decoration: none;

            padding: 10px 15px;

            border-radius: 6px;

            margin-bottom: 3px;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: #243a59;
            color: #fff;
        }

        .sidebar-menu a i {
            width: 25px;
            font-size: 17px;
        }

        .sidebar-submenu {
            padding-left: 25px;
        }

        .sidebar-submenu a {
            font-size: 13px;
            padding: 8px 15px;
        }

        /* Header */

        .admin-header {
            position: fixed;

            left: 260px;
            right: 0;
            top: 0;

            height: 70px;

            background: #fff;

            border-bottom: 1px solid #e8ecf1;

            display: flex;
            align-items: center;

            z-index: 999;
        }

        /* Content */

        .admin-main {
            margin-left: 260px;
            padding-top: 70px;

            min-height: 100vh;

            display: flex;
            flex-direction: column;
        }

        .admin-content {
            padding: 25px;

            /* Push footer to bottom when page has little content */
            flex: 1;
        }

        .admin-footer {
            padding: 15px 25px;

            background: #fff;

            border-top: 1px solid #e8ecf1;

            margin-top: auto;
        }

        /* Mobile */

        @media(max-width: 991px) {

            .admin-sidebar {
                left: -260px;
            }

            .admin-sidebar.show {
                left: 0;
            }

            .admin-header {
                left: 0;
            }

            .admin-main {
                margin-left: 0;
            }

        }

    </style>

    @stack('styles')

</head>

<body>

@include('partials.admin.sidebar')

<div class="admin-main">

    @include('partials.admin.header')

    <main class="admin-content">

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show">

                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        @if($errors->any())

            <div class="alert alert-danger alert-dismissible fade show">

                <ul class="mb-0">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        @yield('content')

    </main>

    @include('partials.admin.footer')

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>


<script>

    document
        .getElementById('sidebarToggle')
        ?.addEventListener('click', function () {

            document
                .getElementById('adminSidebar')
                .classList.toggle('show');

        });

</script>

@stack('scripts')

</body>

</html>