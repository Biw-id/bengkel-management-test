<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Bengkel AHASS') — AHASS Menara Agung (36915)</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <style>
        :root {
            --honda-red: #cc0000;
            --honda-red-hover: #b30000;
            --honda-dark: #1e293b;
            --bg-light: #f8fafc;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: #334155;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            background: var(--honda-red);
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .sidebar-brand small {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .nav-section-title {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 700;
            padding: 1rem 1.5rem 0.25rem;
        }

        .sidebar-menu .nav-link {
            padding: 0.7rem 1.5rem;
            color: #475569;
            font-weight: 500;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar-menu .nav-link:hover {
            color: var(--honda-red);
            background-color: #fef2f2;
            border-left-color: var(--honda-red);
        }

        .sidebar-menu .nav-link.active {
            color: var(--honda-red);
            background-color: #fee2e2;
            border-left-color: var(--honda-red);
            font-weight: 600;
        }

        .sidebar-menu .nav-link i {
            font-size: 1.1rem;
        }

        /* Main Content */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.75rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-body {
            padding: 1.75rem;
            flex: 1;
        }

        /* Card & UI enhancements */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        .btn-honda {
            background-color: var(--honda-red);
            border-color: var(--honda-red);
            color: #ffffff;
        }

        .btn-honda:hover, .btn-honda:focus {
            background-color: var(--honda-red-hover);
            border-color: var(--honda-red-hover);
            color: #ffffff;
        }

        .btn-outline-honda {
            border-color: var(--honda-red);
            color: var(--honda-red);
        }

        .btn-outline-honda:hover {
            background-color: var(--honda-red);
            color: #ffffff;
        }

        .badge-dealer {
            background-color: #fee2e2;
            color: var(--honda-red);
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 0.375rem;
        }

        .table th {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .table td {
            vertical-align: middle;
            font-size: 0.88rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }

        /* Select2 theme tweaks */
        .select2-container--bootstrap-5 .select2-selection {
            border-color: #dee2e6;
            border-radius: 0.375rem;
            min-height: 38px;
        }

        @media (max-width: 768px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.show {
                margin-left: 0;
            }
            #main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-gear-wide-connected fs-3"></i>
            <div>
                <h5>AHASS SYSTEM</h5>
                <small>Menara Agung · ID 36915</small>
            </div>
        </div>

        <div class="sidebar-menu mt-2">
            <div class="nav-section-title">Master Data</div>
            <a href="{{ route('master-jasa.index') }}" class="nav-link {{ request()->routeIs('master-jasa.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i>
                <span>Master Jasa</span>
            </a>
            <a href="{{ route('master-part.index') }}" class="nav-link {{ request()->routeIs('master-part.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i>
                <span>Master Part</span>
            </a>
            <a href="{{ route('master-mekanik.index') }}" class="nav-link {{ request()->routeIs('master-mekanik.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Master Mekanik</span>
            </a>
            <a href="{{ route('master-customer.index') }}" class="nav-link {{ request()->routeIs('master-customer.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Master Customer</span>
            </a>

            <div class="nav-section-title">Servis & Transaksi</div>
            <a href="{{ route('form-sa.index') }}" class="nav-link {{ request()->routeIs('form-sa.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Form Service Advisor</span>
            </a>
            <a href="{{ route('work-orders.index') }}" class="nav-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i>
                <span>Work Order</span>
            </a>
            <a href="{{ route('nota.index') }}" class="nav-link {{ request()->routeIs('nota.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i>
                <span>Nota Jasa & Part</span>
            </a>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div id="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle" aria-label="Toggle Navigation">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div class="d-none d-sm-block">
                    <span class="badge-dealer">
                        <i class="bi bi-building me-1"></i> AHASS Menara Agung · Dealer ID: <strong>36915</strong>
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold small">Service Advisor / Admin</div>
                    <div class="text-muted" style="font-size: 0.75rem;">AHASS Padang, Sumbar</div>
                </div>
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                    SA
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Setup CSRF token for all jQuery AJAX calls
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });

        // SweetAlert2 Toast helper
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Sidebar toggle for mobile
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('show');
        });

        // Utility: Format Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        // Utility: Reset Form Errors
        function clearFormErrors(formElement) {
            $(formElement).find('.is-invalid').removeClass('is-invalid');
            $(formElement).find('.invalid-feedback').remove();
        }

        // Utility: Display Form Errors
        function displayFormErrors(formElement, errors) {
            clearFormErrors(formElement);
            for (const [field, message] of Object.entries(errors)) {
                const input = $(formElement).find(`[name="${field}"]`);
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${message}</div>`);
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
