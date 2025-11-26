<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Budget Management') }} - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        body {
            overflow-x: hidden;
        }

        /* Main content area offset for sidebar */
        #main-content {
            margin-left: 250px;
            min-height: 100vh;
            padding-top: 56px; /* Height of top navbar */
        }

        @media (max-width: 767.98px) {
            #main-content {
                margin-left: 0;
                padding-top: 0;
            }
        }

        /* Currency Input Right Alignment */
        .currency-input {
            text-align: right;
        }

        /* Required Field Asterisk Styling */
        .required::after {
            content: " *";
            color: red;
        }

        /* Select2 Custom Styling */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(1.5em + 0.5rem + 2px);
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            padding: 0.25rem 0.5rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + 0.5rem);
        }

        .select2-dropdown {
            font-size: 0.875rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
            margin-right: 0.5rem;
        }

        /* DataTables info and controls smaller */
        .dataTables_info,
        .dataTables_length label,
        .dataTables_filter label,
        .dataTables_paginate {
            font-size: 0.875rem !important;
        }

        .dataTables_length select,
        .dataTables_filter input {
            font-size: 0.875rem !important;
        }

        /* Global Parsley Validation Styles */
        .parsley-errors-list {
            list-style: none;
            padding: 0;
            margin: 0.25rem 0 0 0;
            font-size: 0.875rem;
            color: #dc3545;
        }

        .parsley-errors-list li {
            margin-bottom: 0.25rem;
        }

        .parsley-error {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        .parsley-success {
            border-color: #198754 !important;
        }

        /* Custom error styling for select elements */
        select.parsley-error {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        /* Validation feedback animation */
        .parsley-errors-list {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    @include('layouts.navigation')

    <main id="main-content" style="margin-top: 20px;">
        @if(session('success'))
            <div class="container-fluid px-4">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container-fluid px-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- AutoNumeric for currency formatting -->
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.8.1/dist/autoNumeric.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Parsley.js -->
    <script src="https://cdn.jsdelivr.net/npm/parsleyjs@2.9.2/dist/parsley.min.js"></script>

    <script>
        $(document).ready(function() {
            // Global Parsley Configuration
            window.Parsley.setLocale('en');
            window.Parsley.options.errorClass = 'parsley-error';
            window.Parsley.options.successClass = 'parsley-success';
            window.Parsley.options.errorsWrapper = '<ul class="parsley-errors-list"></ul>';
            window.Parsley.options.errorTemplate = '<li></li>';
            window.Parsley.options.trigger = 'change';
            window.Parsley.options.excluded = 'input[type=button], input[type=submit], input[type=reset], input[type=hidden], [disabled], :hidden';

            // Auto-initialize Parsley on forms with data-parsley-validate attribute
            $('form[data-parsley-validate]').each(function() {
                $(this).parsley();
            });

            // Initialize Select2 on all select elements with Bootstrap 5 theme
            $('select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder') || 'Pilih...';
                },
                allowClear: true
            });

            // Re-initialize Select2 when dynamically added selects are added
            $(document).on('DOMNodeInserted', function(e) {
                if ($(e.target).is('select') || $(e.target).find('select').length) {
                    $(e.target).find('select:not(.select2-hidden-accessible)').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Pilih...',
                        allowClear: true
                    });
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
