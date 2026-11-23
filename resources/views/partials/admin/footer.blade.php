<footer class="admin-footer">

    <div class="d-flex
                justify-content-between
                align-items-center">

        <div>

            &copy; {{ date('Y') }}

            {{ auth()->user()?->school?->school_name ?? 'School ERP' }}

        </div>

        <div class="text-muted">

            School ERP

        </div>

    </div>

</footer>