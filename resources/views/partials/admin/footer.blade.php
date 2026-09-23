<footer class="admin-footer">

    <div class="d-flex
                justify-content-between
                align-items-center">

        <div>

            &copy; {{ date('Y') }}

            @if(auth()->user()->school)

                {{ auth()->user()->school->school_name }}

            @else

                School ERP

            @endif

        </div>

        <div class="text-muted">

            School ERP

        </div>

    </div>

</footer>