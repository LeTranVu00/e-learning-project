<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Dashboard' ?> - E-Learning</title>

    <!-- CSS / JS dùng chung -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <!-- Ckeditor nếu cần -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#f59e0b', dark: '#111827' } } } }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        /* Fix spinner number input */
        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            opacity: 1;
            height: 100%;
            cursor: pointer;
            width: 24px;
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            overflow: hidden;
        }

        .modal-card {
            will-change: transform, opacity;
            overscroll-behavior: contain;
        }

        .ck-editor__editable_inline {
            min-height: 200px;
        }

        .ck.ck-balloon-panel {
            z-index: 99999 !important;
        }

        /* Dark mode for ckeditor (basic overrides) */
        .dark .ck-editor__editable_inline {
            background-color: #374151 !important;
            color: white !important;
            border-color: #4b5563 !important;
        }

        .dark .ck-toolbar {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
        }

        .dark .ck-button {
            color: #d1d5db !important;
        }

        .dark .ck-button:hover {
            background-color: #4b5563 !important;
        }

        .dark .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
            border-color: #4b5563 !important;
        }

        .dark .ck-button__label {
            color: #d1d5db !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans flex min-h-screen transition-colors duration-300 overflow-y-scroll"
    x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">

    <?php require_once 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col min-w-0">

        <?php require_once 'topbar.php'; ?>

        <!-- Content Area -->
        <div class="flex-1 p-4 sm:p-6 md:p-8">