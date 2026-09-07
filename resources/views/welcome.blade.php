<!DOCTYPE html>
<html lang="id" class="min-h-full bg-zinc-100">
<head>
    @include('partials.head')
</head>
<body class="min-h-full bg-zinc-100 text-zinc-900 font-sans antialiased flex justify-center selection:bg-red-500 selection:text-white">

    <!-- MOBILE CONTAINER (Max 430px, strict viewport bounds) -->
    <div x-data="galaksianApp()" x-init="init()" 
         @touchmove.window="sheetIsDragging ? moveSheetDrag($event) : null"
         @touchend.window="sheetIsDragging ? endSheetDrag(sheetDraggingModal) : null"
         @mousemove.window="sheetIsDragging ? moveSheetDrag($event) : (heroIsMouseDragging ? handleHeroMouseMove($event) : null)"
         @mouseup.window="sheetIsDragging ? endSheetDrag(sheetDraggingModal) : (heroIsMouseDragging ? handleHeroMouseUp() : null)"
         class="w-full max-w-[430px] bg-white min-h-screen flex flex-col relative shadow-[0_0_50px_rgba(0,0,0,0.06)] border-x border-zinc-200" x-cloak>

        <!-- TOP APP BAR (FIXED) -->
        @include('partials.header')

        <!-- TOP APP BAR SPACER (Guarantees content never slides under fixed header on Home) -->
        <div :class="(activeTab === 'home' && !activeSubView) ? 'h-[96px]' : 'h-0'" class="shrink-0 transition-all duration-150"></div>

        <!-- SCROLLABLE MAIN CONTENT -->
        <main class="flex-1 bottom-nav-safe">
            <!-- HOME TAB & SUBVIEWS -->
            @include('partials.views.home.index')
            @include('partials.views.home.filter-page')
            @include('partials.views.home.flash-sale-view')
            @include('partials.views.home.indonesia-catalog-view')
            @include('partials.views.home.special-for-you-view')
            @include('partials.views.home.buy-again-view')

            <!-- CART TAB & SUBVIEWS -->
            @include('partials.views.cart.index')
            @include('partials.views.cart.checkout')
            @include('partials.views.cart.payment-instruction')

            <!-- TRANSACTIONS TAB & SUBVIEWS -->
            @include('partials.views.transactions.index')
            @include('partials.views.transactions.detail')
            @include('partials.views.transactions.qris')
            @include('partials.views.transactions.shipping-payment')

            <!-- PROFILE TAB -->
            @include('partials.views.profile.index')
        </main>

        <!-- STICKY ACTION BAR (CART & CHECKOUT) -->
        @include('partials.sticky-action-bar')

        <!-- STICKY BOTTOM NAVIGATION BAR -->
        @include('partials.bottom-nav')

        <!-- MODALS -->
        @include('partials.modals.oos-resolution')
        @include('partials.modals.product-detail')
        @include('partials.modals.address-form')
        @include('partials.modals.review-form')
        @include('partials.modals.invoice-download')

        <!-- FLOATING TOAST NOTIFICATION -->
        @include('partials.toast')

    </div>

    <!-- SCRIPTS -->
    @include('partials.scripts.translations')
    @include('partials.scripts.app')

</body>
</html>
