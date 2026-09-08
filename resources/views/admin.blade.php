<!DOCTYPE html>
<html lang="id" class="min-h-full bg-zinc-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Galaksian Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: { red: '#E60012', dark: '#111827', muted: '#6B7280', border: '#E5E7EB', bg: '#F9FAFB' }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .tabular { font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="min-h-full bg-zinc-50 text-zinc-900 font-sans antialiased flex justify-center">

    <div x-data="adminApp()" x-init="init()" class="w-full max-w-[430px] bg-white min-h-screen flex flex-col relative shadow-[0_0_50px_rgba(0,0,0,0.06)] border-x border-zinc-200" x-cloak>

        <!-- LOGIN VIEW -->
        @include('partials.admin.views.login')

        <!-- AUTHENTICATED VIEWS -->
        <template x-if="isLoggedIn">
            <div class="flex flex-col min-h-screen">
                <!-- HEADER -->
                @include('partials.admin.header')

                <!-- MAIN CONTENT -->
                <main class="flex-1 pb-20">
                    @include('partials.admin.views.dashboard')
                    @include('partials.admin.views.orders.index')
                    @include('partials.admin.views.orders.detail')
                    @include('partials.admin.views.products.index')
                    @include('partials.admin.views.products.form')
                    @include('partials.admin.views.products.import')
                    @include('partials.admin.views.shipments.index')
                    @include('partials.admin.views.shipments.form')
                    @include('partials.admin.views.trips.index')
                    @include('partials.admin.views.trips.form')
                    @include('partials.admin.views.refunds.index')
                    @include('partials.admin.views.users.index')
                    @include('partials.admin.views.users.form')
                    @include('partials.admin.views.catalog.index')
                    @include('partials.admin.views.more')
                </main>

                <!-- BOTTOM NAV -->
                @include('partials.admin.bottom-nav')

                <!-- TOAST -->
                <div x-show="toast.show" x-transition:enter="transition ease-out duration-200" x-transition:leave="transition ease-in duration-150"
                     class="fixed top-4 left-1/2 -translate-x-1/2 z-[999] max-w-[390px] w-full px-4">
                    <div class="rounded-xl px-4 py-3 text-sm font-semibold shadow-lg text-center"
                         :class="toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-zinc-900 text-white'">
                        <span x-text="toast.message"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @include('partials.admin.scripts.translations')
    @include('partials.admin.scripts.app')

</body>
</html>
