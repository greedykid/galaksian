    <script>
        function galaksianApp() {
            return {
                activeTab: 'home',
                activeSubView: null, // null | 'brand-category' | 'flash-sale' | 'indonesia-catalog' | 'special-for-you' | 'buy-again' | 'checkout' | 'payment-instruction' | 'order-detail' | 'qris-payment'
                flashSaleSubtab: 'all',
                catalogSubtab: 'all',
                specialSubtab: 'all',
                buyAgainSubtab: 'all',
                
                // Order Detail state
                selectedOrderId: null,
                selectedOrderDetail: null,
                orderDetailLoading: false,
                adminSelectedStatus: 'paid_product',
                profileTab: 'biodata',

                // QRIS View state (Figma reference)
                showQrisInstructions: false,
                isCheckingQris: false,
                qrisSecondsRemaining: 2697, // 44:57 matching Figma screenshot
                qrisTimerInterval: null,

                // OOS (Barang Habis) Simulation & Resolution state
                showOosModal: false,
                oosChoice: 'replace', // 'refund' | 'replace'
                oosSelectedItem: null,
                oosReplacementProduct: null,
                oosReplacementCandidates: [
                    { id: 991, name: 'Calbee Jagabee Potato Crisps (Butter Shoyu)', price: 42000, image: 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&fit=crop&q=80' },
                    { id: 992, name: 'Meiji Black Chocolate Bar 120g Tokyo', price: 65000, image: 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=400&fit=crop&q=80' },
                    { id: 993, name: 'Tokyo Banana Custard Cream Cake (8 pcs)', price: 185000, image: 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?w=400&fit=crop&q=80' },
                    { id: 994, name: 'Hada Labo Gokujyun Premium Lotion 170ml', price: 145000, image: 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400&fit=crop&q=80' },
                    { id: 995, name: 'Indomie Mi Goreng Spesial', price: 4500, image: 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80' }
                ],
                transactionTab: 'berlangsung',
                curatedTab: 'special',
                heroSlide: 0,
                heroInterval: null,
                heroTouchStartX: 0,
                heroTouchStartY: 0,
                heroDragDeltaX: 0,
                heroIsSwiping: false,
                heroIsMouseDragging: false,
                sheetDraggingModal: null,
                sheetDragStartY: 0,
                sheetDragCurrentY: 0,
                sheetDragOffset: 0,
                sheetIsDragging: false,
                selectedCountry: 'all',
                currentLang: localStorage.getItem('galaksian_lang') || 'id',
                langDropdownOpen: false,
                
                showNotifications: false,
                activeProduct: null,
                showProductDetailModal: false,
                detailModalQty: 1,
                showAddressModal: false,
                showReviewModal: false,
                showInvoiceDownloadModal: false,
                selectedInvoiceOrder: null,

                flashSaleCountdown: { h: '04', m: '18', s: '29' },
                searchQuery: '',
                isSearching: false,
                searchResults: [],
                hasSearched: false,
                searchTotal: 0,
                catalogFilter: 'all',
                isPastKatalog: false,
                stickySearchExpanded: false,
                waCsUrl: 'https://wa.me/6281200000001?text=Halo%20Admin%20Galaksian%2C%20saya%20butuh%20bantuan%20jastip',

                toast: { show: false, message: '', type: 'success' },

                isLoggedIn: false,
                authToken: null,
                currentUser: null,
                authPhone: '081234567890',
                authOtp: '',
                otpStep: 'phone',
                authLoading: false,

                homeData: {
                    banners: [],
                    flash_sales: [],
                    brands: [],
                    categories: [],
                    special_for_you: [],
                    beli_lagi: [],
                    best_sellers: [],
                    promo_products: [],
                    product_grid: { data: [] },
                    active_trip: null
                },

                cartToken: '',
                cart: { items: [], total_qty: 0, pricing: null, voucher_applied: null },
                voucherCode: '',

                selectedFilter: { type: 'kategori', name: '', slug: '', subtab: 'all' },

                userAddresses: [],
                defaultAddress: null,
                addressForm: {
                    recipient_name: '',
                    phone: '',
                    address: '',
                    city: 'Jakarta Selatan',
                    postal_code: '12190',
                    delivery_note: 'leave_at_front_door',
                    is_default: true
                },

                checkoutForm: {
                    address_id: null,
                    payment_method: 'virtual_account',
                    notes: ''
                },
                checkoutFormNotesManual: '',
                isInsuranceChecked: true,
                giftOptionEnabled: false,
                giftCardFrom: '',
                giftCardTo: '',
                giftCardMessage: '',
                voucherStates: {
                    'GALAKSIAN10': 'unclaimed',
                    'NEWUSER15': 'unclaimed',
                    'HEMAT5': 'unclaimed',
                    'POTONGAN15K': 'unclaimed'
                },
                itemNotes: {},
                editingNoteItemId: null,
                tempNoteText: '',
                isSubmittingCheckout: false,

                transactionSearchOpen: false,
                transactionSearchQuery: '',
                addressSearchQuery: '',
                profileKtp: '3171012505870003',
                inlineEditingField: null,
                inlineEditValue: '',
                inlineEditLoading: false,
                editingAddressId: null,

                changePasswordModal: false,
                currentPassword: '',
                newPassword: '',
                confirmPassword: '',
                showCurrentPassword: false,
                showNewPassword: false,
                showConfirmPassword: false,
                changePasswordLoading: false,

                paymentResult: null,
                isSimulatingPayment: false,
                orders: [],

                reviewForm: {
                    orderId: null,
                    productId: null,
                    rating: 5,
                    comment: ''
                },

                translations: window.GALAKSIAN_TRANSLATIONS,

                setLanguage(lang) {
                    if (lang !== 'id' && lang !== 'en') return;
                    this.currentLang = lang;
                    localStorage.setItem('galaksian_lang', lang);
                    this.langDropdownOpen = false;
                    document.documentElement.lang = lang;
                    this.showToast(lang === 'id' ? 'Bahasa berhasil diubah ke Bahasa Indonesia.' : 'Language successfully changed to English.');
                },

                t(key, fallback = '') {
                    const lang = this.currentLang;
                    if (this.translations && this.translations[lang] && this.translations[lang][key] !== undefined) {
                        return this.translations[lang][key];
                    }
                    if (this.translations && this.translations['id'] && this.translations['id'][key] !== undefined) {
                        return this.translations['id'][key];
                    }
                    return fallback || key;
                },

                init() {
                    this.currentLang = localStorage.getItem('galaksian_lang') || 'id';
                    document.documentElement.lang = this.currentLang;
                    this.cartToken = localStorage.getItem('galaksian_cart_token') || 'cart-' + Math.random().toString(36).substring(2, 12);
                    localStorage.setItem('galaksian_cart_token', this.cartToken);

                    this.authToken = localStorage.getItem('galaksian_token') || null;
                    if (this.authToken) {
                        this.isLoggedIn = true;
                        this.fetchUserProfile();
                    }

                    this.startCountdown();
                    this.startHeroCarousel();
                    this.fetchHome();
                    this.fetchCart();
                    this.initScrollListener();
                },

                showToast(msg, type = 'success') {
                    let translated = msg;
                    if (this.translations && this.translations[this.currentLang]) {
                        const dict = this.translations[this.currentLang];
                        if (dict[msg]) {
                            translated = dict[msg];
                        } else if (this.currentLang === 'en') {
                            if (msg.includes('Refund seharga barang')) {
                                translated = msg.replace('Refund seharga barang', 'Refund for item price').replace('berhasil diajukan', 'successfully requested');
                            } else if (msg.includes('Produk diganti! Selisih lunas otomatis dipotong dari saldo refund')) {
                                translated = 'Product replaced! Difference automatically settled from refund balance.';
                            } else if (msg.includes('Produk diganti! Invoice Tambahan diterbitkan:')) {
                                translated = msg.replace('Produk diganti! Invoice Tambahan diterbitkan:', 'Product replaced! Additional invoice issued:');
                            } else if (msg.includes('Produk diganti! Terbit Invoice Tambahan:')) {
                                translated = msg.replace('Produk diganti! Terbit Invoice Tambahan:', 'Product replaced! Additional invoice issued:');
                            } else if (msg.includes('Produk diganti ke')) {
                                translated = msg.replace('Produk diganti ke', 'Product replaced with').replace('Kelebihan dana', 'Excess balance').replace('diajukan refund', 'refund requested');
                            } else if (msg.includes('Produk berhasil diganti ke')) {
                                translated = msg.replace('Produk berhasil diganti ke', 'Product successfully replaced with');
                            } else if (msg.includes('Voucher ') && msg.includes('berhasil digunakan')) {
                                translated = msg.replace('Voucher', 'Voucher').replace('berhasil digunakan.', 'successfully applied.');
                            } else if (msg.includes('Masuk sebagai')) {
                                translated = msg.replace('Masuk sebagai', 'Signed in as');
                            } else if (msg.includes('Status order diperbarui:')) {
                                translated = msg.replace('Status order diperbarui:', 'Order status updated:');
                            } else if (msg.includes('Status berhasil diubah ke:')) {
                                translated = msg.replace('Status berhasil diubah ke:', 'Status successfully changed to:');
                            }
                        }
                    }
                    this.toast.message = translated;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 3000);
                },

                getHeaders() {
                    const headers = {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Cart-Token': this.cartToken
                    };
                    if (this.authToken) {
                        headers['Authorization'] = 'Bearer ' + this.authToken;
                    }
                    return headers;
                },

                goToTab(tab) {
                    this.activeTab = tab;
                    this.activeSubView = null;
                    this.selectedOrderDetail = null;
                    this.selectedOrderId = null;
                    if (this.qrisTimerInterval) clearInterval(this.qrisTimerInterval);
                    if (tab === 'cart') this.fetchCart();
                    if (tab === 'transactions') {
                        this.transactionTab = 'berlangsung';
                        this.fetchOrders();
                    }
                    if (tab === 'profile' && this.isLoggedIn) {
                        this.fetchUserProfile();
                        this.fetchAddresses();
                    }
                },

                closeSubView() {
                    this.activeSubView = null;
                },

                setCountry(c) {
                    this.selectedCountry = c;
                    this.fetchHome();
                },

                async fetchHome() {
                    try {
                        const url = this.selectedCountry === 'all' 
                            ? '/api/v1/home' 
                            : '/api/v1/home?country=' + this.selectedCountry;
                        const res = await fetch(url, { headers: this.getHeaders() });
                        const json = await res.json();
                        if (json.success) {
                            this.homeData = json.data;
                        }
                    } catch (e) {
                        console.error('Home load error:', e);
                    }
                },

                async searchProducts() {
                    const q = this.searchQuery ? this.searchQuery.trim() : '';
                    if (!q) {
                        this.clearSearch();
                        return;
                    }
                    this.isSearching = true;
                    this.hasSearched = true;
                    const currentQuery = q;
                    try {
                        const res = await fetch('/api/v1/products?q=' + encodeURIComponent(q), { headers: this.getHeaders() });
                        const json = await res.json();
                        // Prevent race conditions if user typed something else while request was flying
                        if (this.searchQuery.trim() !== currentQuery) {
                            return;
                        }
                        if (json.success) {
                            const items = Array.isArray(json.data) ? json.data : (json.data?.data || []);
                            this.searchResults = items;
                            this.searchTotal = (json.data && json.data.meta && json.data.meta.total !== undefined)
                                ? json.data.meta.total
                                : items.length;
                            if (this.homeData && this.homeData.product_grid) {
                                this.homeData.product_grid.data = items;
                            }
                        } else {
                            this.searchResults = [];
                            this.searchTotal = 0;
                        }
                    } catch (e) {
                        console.error('Search error:', e);
                        if (this.searchQuery.trim() === currentQuery) {
                            this.searchResults = [];
                            this.searchTotal = 0;
                        }
                    } finally {
                        if (this.searchQuery.trim() === currentQuery) {
                            this.isSearching = false;
                        }
                    }
                },

                clearSearch() {
                    this.searchQuery = '';
                    this.isSearching = false;
                    this.hasSearched = false;
                    this.searchResults = [];
                    this.searchTotal = 0;
                    this.stickySearchExpanded = false;
                    this.fetchHome();
                },

                initScrollListener() {
                    const checkScroll = () => {
                        if (this.activeTab !== 'home' || this.activeSubView) {
                            this.isPastKatalog = false;
                            return;
                        }
                        const el = document.getElementById('katalog-produk-indonesia');
                        if (el) {
                            const rect = el.getBoundingClientRect();
                            // When the top of Katalog Produk Indonesia reaches or passes near the sticky header (rect.top <= 100)
                            this.isPastKatalog = rect.top <= 100;
                        } else {
                            this.isPastKatalog = window.scrollY > 600;
                        }
                    };

                    window.addEventListener('scroll', checkScroll, { passive: true });
                },

                focusSearch() {
                    this.$nextTick(() => {
                        const input = document.getElementById('global-search-input');
                        if (input) {
                            input.focus();
                        }
                    });
                },

                getCuratedProducts() {
                    if (this.curatedTab === 'special') return this.homeData.special_for_you || [];
                    if (this.curatedTab === 'belilagi') return this.homeData.beli_lagi?.length ? this.homeData.beli_lagi : this.homeData.special_for_you;
                    if (this.curatedTab === 'bestseller') return this.homeData.best_sellers || [];
                    if (this.curatedTab === 'promo') return this.homeData.promo_products || [];
                    return this.homeData.product_grid?.data || [];
                },

                openBrandCategoryView(type, name, slug) {
                    this.selectedFilter.type = type;
                    this.selectedFilter.name = name;
                    this.selectedFilter.slug = slug;
                    this.selectedFilter.subtab = 'all';
                    this.activeSubView = 'brand-category';
                },

                getFilteredSubViewProducts() {
                    let items = this.homeData.product_grid?.data || [];
                    if (this.selectedFilter.type === 'brand') {
                        items = items.filter(p => p.brand && p.brand.slug === this.selectedFilter.slug);
                    }
                    if (this.selectedFilter.subtab === 'cheapest') {
                        return [...items].sort((a, b) => a.final_price - b.final_price);
                    }
                    if (this.selectedFilter.subtab === 'promo') {
                        return items.filter(p => p.has_discount);
                    }
                    return items.length ? items : (this.homeData.special_for_you || []);
                },

                openFlashSaleView(subtab = 'all') {
                    this.flashSaleSubtab = subtab;
                    this.activeSubView = 'flash-sale';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                openIndonesiaCatalogView(subtab = 'all') {
                    this.catalogSubtab = subtab;
                    this.activeSubView = 'indonesia-catalog';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                openSpecialForYouView(subtab = 'all') {
                    this.specialSubtab = subtab;
                    this.activeSubView = 'special-for-you';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                openBuyAgainView(subtab = 'all') {
                    this.buyAgainSubtab = subtab;
                    this.activeSubView = 'buy-again';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                getFlashSaleProducts() {
                    const baseItems = this.homeData.flash_sales?.length
                        ? [...this.homeData.flash_sales]
                        : (this.homeData.promo_products?.length
                            ? [...this.homeData.promo_products]
                            : [...(this.homeData.product_grid?.data || [])]);

                    if (this.flashSaleSubtab === 'huge-discount') {
                        return baseItems.filter(p => p.has_discount || (p.price && p.price > p.final_price * 1.3));
                    }
                    if (this.flashSaleSubtab === 'bundles') {
                        const bundles = baseItems.filter(p => /bundle|paket|set|hemat|isi/i.test(p.name || ''));
                        return bundles.length ? bundles : baseItems.slice(0, 4);
                    }
                    if (this.flashSaleSubtab === 'under100k') {
                        return baseItems.filter(p => p.final_price <= 100000);
                    }
                    return baseItems;
                },

                getIndonesiaCatalogProducts() {
                    const allItems = this.homeData.product_grid?.data || [];
                    if (this.catalogSubtab === 'all') return allItems;

                    const matchTerms = {
                        'mie-sembako': ['mie', 'indomie', 'sedap', 'beras', 'minyak', 'gula', 'sembako', 'terigu', 'tepung'],
                        'kopi-teh': ['kopi', 'teh', 'coffee', 'tea', 'luwak', 'kapal api', 'sariwangi', 'matcha'],
                        'sambal-bumbu': ['sambal', 'bumbu', 'kecap', 'saus', 'racik', 'abc', 'indofood', 'sasa', 'royco', 'masako'],
                        'herbal': ['tolak angin', 'jamu', 'kayu putih', 'herbal', 'madu', 'sidomuncul'],
                        'snack': ['kerupuk', 'chips', 'snack', 'biskuit', 'chiki', 'taro', 'kacang', 'wafer', 'permen']
                    };

                    const terms = matchTerms[this.catalogSubtab] || [];
                    const filtered = allItems.filter(p => {
                        const name = (p.name || '').toLowerCase();
                        const cat = (p.category?.name || '').toLowerCase();
                        return terms.some(t => name.includes(t) || cat.includes(t));
                    });

                    return filtered.length ? filtered : allItems.slice(0, 8);
                },

                getSpecialForYouProducts() {
                    const baseItems = this.homeData.special_for_you?.length
                        ? [...this.homeData.special_for_you]
                        : (this.homeData.best_sellers?.length
                            ? [...this.homeData.best_sellers]
                            : [...(this.homeData.product_grid?.data || [])]);

                    if (this.specialSubtab === 'top-rated') {
                        return [...baseItems].sort((a, b) => (b.rating || 4.9) - (a.rating || 4.9));
                    }
                    if (this.specialSubtab === 'trending') {
                        return [...baseItems].reverse();
                    }
                    if (this.specialSubtab === 'most-reviewed') {
                        return [...baseItems].sort((a, b) => (b.stock || 0) - (a.stock || 0));
                    }
                    return baseItems;
                },

                getBuyAgainProducts() {
                    const baseItems = this.homeData.beli_lagi?.length
                        ? [...this.homeData.beli_lagi]
                        : (this.homeData.best_sellers?.length
                            ? [...this.homeData.best_sellers]
                            : [...(this.homeData.product_grid?.data?.slice(0, 10) || [])]);

                    if (this.buyAgainSubtab === 'sembako') {
                        const sembako = baseItems.filter(p => /mie|indomie|bumbu|beras|minyak/i.test(p.name || ''));
                        return sembako.length ? sembako : baseItems.slice(0, 4);
                    }
                    if (this.buyAgainSubtab === 'snack') {
                        const snack = baseItems.filter(p => /snack|kopi|teh|kerupuk|biskuit/i.test(p.name || ''));
                        return snack.length ? snack : baseItems.slice(2, 6);
                    }
                    if (this.buyAgainSubtab === 'most-frequent') {
                        return baseItems.slice(0, 5);
                    }
                    return baseItems;
                },

                formatYen(num) {
                    if (num === null || num === undefined) return '¥0';
                    return '¥' + Math.round(Number(num) / 105).toLocaleString();
                },

                startHeroCarousel() {
                    if (this.heroInterval) clearInterval(this.heroInterval);
                    this.heroInterval = setInterval(() => {
                        this.heroSlide = (this.heroSlide + 1) % 3;
                    }, 4000);
                },

                pauseHeroCarousel() {
                    if (this.heroInterval) clearInterval(this.heroInterval);
                },

                setHeroSlide(idx) {
                    this.heroSlide = idx;
                    this.startHeroCarousel();
                },

                nextHeroSlide() {
                    this.heroSlide = (this.heroSlide + 1) % 3;
                    this.startHeroCarousel();
                },

                prevHeroSlide() {
                    this.heroSlide = (this.heroSlide - 1 + 3) % 3;
                    this.startHeroCarousel();
                },

                getHeroTrackStyle() {
                    if ((this.heroIsSwiping || this.heroIsMouseDragging) && this.heroDragDeltaX !== 0) {
                        return `transform: translateX(calc(-${this.heroSlide * 100}% + ${this.heroDragDeltaX}px)); transition: none;`;
                    }
                    return `transform: translateX(-${this.heroSlide * 100}%); transition: transform 500ms ease-in-out;`;
                },

                handleHeroTouchStart(e) {
                    this.pauseHeroCarousel();
                    const touch = e.touches ? e.touches[0] : e;
                    this.heroTouchStartX = touch.clientX;
                    this.heroTouchStartY = touch.clientY;
                    this.heroDragDeltaX = 0;
                    this.heroIsSwiping = true;
                },

                handleHeroTouchMove(e) {
                    if (!this.heroIsSwiping) return;
                    const touch = e.touches ? e.touches[0] : e;
                    const deltaX = touch.clientX - this.heroTouchStartX;
                    const deltaY = touch.clientY - this.heroTouchStartY;
                    if (Math.abs(deltaX) > Math.abs(deltaY)) {
                        this.heroDragDeltaX = deltaX;
                    }
                },

                handleHeroTouchEnd() {
                    if (!this.heroIsSwiping) return;
                    this.heroIsSwiping = false;
                    const threshold = 35;
                    if (this.heroDragDeltaX < -threshold) {
                        this.heroSlide = (this.heroSlide + 1) % 3;
                    } else if (this.heroDragDeltaX > threshold) {
                        this.heroSlide = (this.heroSlide - 1 + 3) % 3;
                    }
                    this.heroDragDeltaX = 0;
                    this.startHeroCarousel();
                },

                handleHeroMouseDown(e) {
                    this.pauseHeroCarousel();
                    this.heroTouchStartX = e.clientX;
                    this.heroTouchStartY = e.clientY;
                    this.heroDragDeltaX = 0;
                    this.heroIsMouseDragging = true;
                },

                handleHeroMouseMove(e) {
                    if (!this.heroIsMouseDragging) return;
                    this.heroDragDeltaX = e.clientX - this.heroTouchStartX;
                },

                handleHeroMouseUp() {
                    if (!this.heroIsMouseDragging) return;
                    this.heroIsMouseDragging = false;
                    const threshold = 35;
                    if (this.heroDragDeltaX < -threshold) {
                        this.heroSlide = (this.heroSlide + 1) % 3;
                    } else if (this.heroDragDeltaX > threshold) {
                        this.heroSlide = (this.heroSlide - 1 + 3) % 3;
                    }
                    this.heroDragDeltaX = 0;
                    this.startHeroCarousel();
                },

                getSheetStyle(modalName) {
                    if (this.sheetDraggingModal === modalName) {
                        if (this.sheetIsDragging) {
                            return `transform: translateY(${this.sheetDragOffset}px); transition: none;`;
                        } else {
                            return `transform: translateY(0px); transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);`;
                        }
                    }
                    return '';
                },

                startSheetDrag(modalName, e) {
                    this.sheetDraggingModal = modalName;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    this.sheetDragStartY = clientY;
                    this.sheetDragCurrentY = clientY;
                    this.sheetDragOffset = 0;
                    this.sheetIsDragging = true;
                },

                moveSheetDrag(e) {
                    if (!this.sheetIsDragging) return;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    this.sheetDragCurrentY = clientY;
                    const deltaY = clientY - this.sheetDragStartY;
                    if (deltaY > 0) {
                        this.sheetDragOffset = deltaY;
                    } else {
                        this.sheetDragOffset = Math.max(-20, deltaY * 0.15);
                    }
                },

                endSheetDrag(modalName) {
                    if (!this.sheetIsDragging) return;
                    this.sheetIsDragging = false;
                    const target = modalName || this.sheetDraggingModal;
                    const threshold = 70;
                    if (this.sheetDragOffset > threshold) {
                        this.closeBottomSheet(target);
                        this.sheetDragOffset = 0;
                        this.sheetDraggingModal = null;
                    } else {
                        this.sheetDragOffset = 0;
                        setTimeout(() => {
                            if (!this.sheetIsDragging) {
                                this.sheetDraggingModal = null;
                            }
                        }, 220);
                    }
                },

                closeBottomSheet(modalName) {
                    if (modalName === 'productDetail') {
                        this.closeProductDetailModal();
                    } else if (modalName === 'address') {
                        this.showAddressModal = false;
                    } else if (modalName === 'review') {
                        this.showReviewModal = false;
                    } else if (modalName === 'invoiceDownload') {
                        this.showInvoiceDownloadModal = false;
                    } else if (modalName === 'oos') {
                        this.showOosModal = false;
                    }
                },

                openProductDetail(prod) {
                    this.activeProduct = prod;
                    this.detailModalQty = 1;
                    this.showProductDetailModal = true;
                },

                closeProductDetailModal() {
                    this.showProductDetailModal = false;
                    setTimeout(() => {
                        if (!this.showProductDetailModal) {
                            this.activeProduct = null;
                        }
                    }, 320);
                },

                getCartItemQty(productId) {
                    if (!this.cart.items) return 0;
                    const found = this.cart.items.find(i => i.product_id === productId || (i.product && i.product.id === productId));
                    return found ? found.qty : 0;
                },

                async addToCart(prod, qty = 1) {
                    try {
                        const res = await fetch('/api/v1/cart/items', {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({ product_id: prod.id, qty: qty })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.cart = json.data;
                            this.showToast('Item berhasil ditambahkan ke keranjang.');
                        } else {
                            this.showToast(json.message || 'Gagal menambahkan ke keranjang', 'error');
                        }
                    } catch (e) {
                        this.showToast('Koneksi server gagal', 'error');
                    }
                },

                async changeCartQty(productId, delta) {
                    const found = this.cart.items.find(i => i.product_id === productId || (i.product && i.product.id === productId));
                    if (!found) return;
                    const newQty = found.qty + delta;
                    if (newQty <= 0) {
                        this.removeCartItem(found.id);
                    } else {
                        this.updateCartItemQty(found.id, newQty);
                    }
                },

                async updateCartItemQty(itemId, qty) {
                    if (qty <= 0) {
                        this.removeCartItem(itemId);
                        return;
                    }
                    try {
                        const res = await fetch('/api/v1/cart/items/' + itemId, {
                            method: 'PATCH',
                            headers: this.getHeaders(),
                            body: JSON.stringify({ qty: qty })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.cart = json.data;
                        }
                    } catch (e) {
                        console.error('Update qty error:', e);
                    }
                },

                async removeCartItem(itemId) {
                    try {
                        const res = await fetch('/api/v1/cart/items/' + itemId, {
                            method: 'DELETE',
                            headers: this.getHeaders()
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.cart = json.data;
                            this.showToast('Item telah dihapus.');
                        }
                    } catch (e) {
                        console.error('Remove item error:', e);
                    }
                },

                async fetchCart() {
                    try {
                        const res = await fetch('/api/v1/cart', { headers: this.getHeaders() });
                        const json = await res.json();
                        if (json.success) {
                            this.cart = json.data;
                        }
                    } catch (e) {
                        console.error('Cart fetch error:', e);
                    }
                },

                async applyVoucher() {
                    if (!this.voucherCode.trim()) return;
                    try {
                        const res = await fetch('/api/v1/cart/voucher', {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({ code: this.voucherCode.trim() })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.cart = json.data;
                            this.voucherStates[this.voucherCode.trim()] = 'applied';
                            this.showToast('Voucher ' + this.voucherCode + ' berhasil digunakan.');
                        } else {
                            this.showToast(json.message || 'Voucher tidak valid', 'error');
                        }
                    } catch (e) {
                        this.showToast('Gagal menerapkan voucher', 'error');
                    }
                },

                removeVoucher() {
                    const currentCode = this.cart?.voucher_applied?.code;
                    if (currentCode) {
                        this.voucherStates[currentCode] = 'claimed';
                    }
                    this.cart.voucher_applied = null;
                    if (this.cart.pricing) {
                        this.cart.pricing.voucher_discount = 0;
                        this.cart.pricing.product_total = (this.cart.pricing.subtotal || 0) + (this.cart.pricing.handling_fee || 5000);
                    }
                    this.voucherCode = '';
                    this.showToast('Voucher dibatalkan.');
                },

                getVoucherState(code) {
                    if (this.cart?.voucher_applied?.code === code) return 'applied';
                    return this.voucherStates[code] || 'unclaimed';
                },

                async handleVoucherAction(code) {
                    const state = this.getVoucherState(code);
                    if (state === 'unclaimed') {
                        this.voucherStates[code] = 'claimed';
                        this.showToast('Voucher ' + code + ' berhasil diklaim!');
                    } else if (state === 'claimed') {
                        this.voucherCode = code;
                        await this.applyVoucher();
                        if (this.cart?.voucher_applied?.code === code) {
                            this.voucherStates[code] = 'applied';
                        }
                    } else if (state === 'applied') {
                        this.removeVoucher();
                    }
                },

                applyPromoCode(code) {
                    this.handleVoucherAction(code);
                },

                openItemNote(item) {
                    this.editingNoteItemId = item.id;
                    this.tempNoteText = this.itemNotes[item.id] || '';
                },

                saveItemNote(itemId) {
                    const text = (this.tempNoteText || '').trim();
                    if (text) {
                        this.itemNotes[itemId] = text;
                    } else {
                        delete this.itemNotes[itemId];
                    }
                    this.editingNoteItemId = null;
                    this.syncCheckoutNotes();
                    this.showToast('Catatan produk disimpan.');
                },

                cancelItemNote() {
                    this.editingNoteItemId = null;
                    this.tempNoteText = '';
                },

                syncCheckoutNotes() {
                    const parts = [];
                    if (this.giftOptionEnabled) {
                        const from = this.giftCardFrom ? ` Dari: ${this.giftCardFrom.trim()}` : '';
                        const to = this.giftCardTo ? ` Untuk: ${this.giftCardTo.trim()}` : '';
                        const msg = this.giftCardMessage ? ` Pesan: "${this.giftCardMessage.trim()}"` : '';
                        parts.push(`[Bingkisan & Kartu Ucapan (+Rp 10.000)${from}${to}${msg}]`);
                    }
                    for (const [id, note] of Object.entries(this.itemNotes)) {
                        if (note && note.trim()) {
                            const item = this.cart?.items?.find(i => i.id == id);
                            const name = item?.product?.name || ('Item #' + id);
                            parts.push(`[${name}: ${note.trim()}]`);
                        }
                    }
                    if (this.checkoutFormNotesManual && this.checkoutFormNotesManual.trim()) {
                        parts.push(this.checkoutFormNotesManual.trim());
                    }
                    this.checkoutForm.notes = parts.join(' ');
                },

                async proceedToCheckout() {
                    if (!this.isLoggedIn) {
                        this.showToast('Silakan login untuk melanjutkan checkout.', 'error');
                        this.goToTab('profile');
                        return;
                    }
                    await this.fetchAddresses();
                    if (this.defaultAddress) {
                        this.checkoutForm.address_id = this.defaultAddress.id;
                    } else if (this.userAddresses.length > 0) {
                        this.checkoutForm.address_id = this.userAddresses[0].id;
                    }
                    this.activeSubView = 'checkout';
                },

                async submitCheckout() {
                    if (!this.checkoutForm.address_id) {
                        this.showToast('Pilih alamat pengiriman terlebih dahulu.', 'error');
                        return;
                    }
                    this.isSubmittingCheckout = true;
                    try {
                        const payload = {
                            address_id: this.checkoutForm.address_id,
                            payment_method: this.checkoutForm.payment_method,
                            notes: this.checkoutForm.notes || null,
                            voucher_code: this.cart.voucher_applied?.code || null
                        };
                        const res = await fetch('/api/v1/checkout', {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify(payload)
                        });
                        const json = await res.json();
                        if (json.success) {
                            const newOrder = json.data.order;
                            this.paymentResult = {
                                order: newOrder,
                                invoice: json.data.invoice,
                                payment: json.data.payment,
                                isPaid: false
                            };
                            this.selectedOrderId = newOrder.id;
                            this.selectedOrderDetail = null;
                            this.activeSubView = 'payment-instruction';
                            this.fetchCart();
                            this.fetchOrders();
                            this.showToast('Pesanan dibuat. Silakan selesaikan pembayaran.');
                        } else {
                            this.showToast(json.message || 'Checkout gagal.', 'error');
                        }
                    } catch (e) {
                        this.showToast('Terjadi kesalahan saat checkout.', 'error');
                    } finally {
                        this.isSubmittingCheckout = false;
                    }
                },

                
                
                // ================= OOS (BARANG HABIS) & INVOICE METHODS =================
                getOosOldItemPrice() {
                    if (!this.oosSelectedItem) return 0;
                    const raw = this.oosSelectedItem.price ?? this.oosSelectedItem.unit_price;
                    if (raw !== undefined && raw !== null && raw > 0) return Number(raw);
                    if (this.oosSelectedItem.subtotal && this.oosSelectedItem.qty) {
                        return Math.round(Number(this.oosSelectedItem.subtotal) / Number(this.oosSelectedItem.qty));
                    }
                    return Number(this.oosSelectedItem.subtotal) || 0;
                },

                triggerOosSimulation(item) {
                    item.is_oos = true;
                    this.openOosModal(item);
                },

                openOosModal(item) {
                    this.oosSelectedItem = item;
                    const price = Number(item.price ?? item.unit_price ?? (item.qty ? item.subtotal / item.qty : 0)) || 0;
                    this.oosSelectedItem.price = price;
                    this.oosSelectedItem.unit_price = price;
                    this.oosSelectedItem.product_name = item.product_name || item.name || 'Produk Pesanan';
                    this.oosSelectedItem.name = this.oosSelectedItem.product_name;

                    this.oosReplacementProduct = this.oosReplacementCandidates[0];
                    this.oosChoice = 'replace';
                    this.showOosModal = true;
                },

                async resolveOosRefund() {
                    if (!this.oosSelectedItem) return;
                    const refundAmount = this.oosSelectedItem.subtotal || (this.getOosOldItemPrice() * (this.oosSelectedItem.qty || 1));
                    const itemName = this.oosSelectedItem.product_name || this.oosSelectedItem.name || 'Produk Pesanan';

                    try {
                        const res = await fetch(`/api/v1/orders/${this.selectedOrderDetail.id}/items/${this.oosSelectedItem.id}/resolve-oos`, {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({
                                resolution: 'refund',
                                amount: refundAmount
                            })
                        });
                        const json = await res.json();
                        if (json.success && json.data) {
                            this.selectedOrderDetail = json.data;
                            if (this.oosSelectedItem) {
                                this.oosSelectedItem.resolution = 'refunded';
                                this.oosSelectedItem.refund_status = 'requested';
                                this.oosSelectedItem.refund_amount = refundAmount;
                            }
                            this.showToast(`Refund seharga barang (${this.formatRupiah(refundAmount)}) berhasil diajukan.`);
                        } else {
                            this.oosSelectedItem.resolution = 'refunded';
                            this.oosSelectedItem.refund_status = 'requested';
                            this.oosSelectedItem.refund_amount = refundAmount;
                            this.oosSelectedItem.is_oos = true;
                            if (!this.selectedOrderDetail.status_histories) this.selectedOrderDetail.status_histories = [];
                            this.selectedOrderDetail.status_histories.unshift({
                                id: Date.now(),
                                note: `Barang habis di toko JP (${itemName}). Pengembalian dana (partial refund) sebesar ${this.formatRupiah(refundAmount)} diajukan & diproses.`,
                                created_at: new Date().toISOString()
                            });
                            this.showToast(`Refund seharga barang (${this.formatRupiah(refundAmount)}) berhasil diajukan.`);
                        }
                    } catch (e) {
                        console.error('Refund API error:', e);
                        this.oosSelectedItem.resolution = 'refunded';
                        this.oosSelectedItem.refund_status = 'requested';
                        this.oosSelectedItem.refund_amount = refundAmount;
                        this.oosSelectedItem.is_oos = true;
                        this.showToast(`Refund seharga barang (${this.formatRupiah(refundAmount)}) berhasil diajukan.`);
                    }
                    this.showOosModal = false;
                },

                async resolveOosReplacement() {
                    if (!this.oosSelectedItem || !this.oosReplacementProduct) return;
                    const oldName = this.oosSelectedItem.product_name || this.oosSelectedItem.name || 'Produk Pesanan';
                    const oldPrice = this.getOosOldItemPrice();
                    const newProduct = this.oosReplacementProduct;
                    const priceDiff = newProduct.price - oldPrice;

                    try {
                        const res = await fetch(`/api/v1/orders/${this.selectedOrderDetail.id}/items/${this.oosSelectedItem.id}/resolve-oos`, {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({
                                resolution: 'replace',
                                replacement_name: newProduct.name,
                                replacement_price: newProduct.price
                            })
                        });
                        const json = await res.json();
                        if (json.success && json.data) {
                            this.selectedOrderDetail = json.data;
                            if (priceDiff > 0) {
                                const hasPaidOffset = json.data.invoices && json.data.invoices.some(i => i.type === 'additional' && i.status === 'paid');
                                if (hasPaidOffset) {
                                    this.showToast(`Produk diganti! Selisih lunas otomatis dipotong dari saldo refund.`);
                                } else {
                                    this.showToast(`Produk diganti! Invoice Tambahan diterbitkan: ${this.formatRupiah(priceDiff)}`);
                                }
                            } else if (priceDiff < 0) {
                                const overpaid = Math.abs(priceDiff);
                                if (this.selectedOrderDetail && this.selectedOrderDetail.items) {
                                    const matchItem = this.selectedOrderDetail.items.find(it => it.id === this.oosSelectedItem.id);
                                    if (matchItem) {
                                        matchItem.refund_amount = overpaid;
                                        matchItem.refund_status = 'requested';
                                    }
                                }
                                this.showToast(`Produk diganti ke ${newProduct.name}! Kelebihan dana ${this.formatRupiah(overpaid)} diajukan refund.`);
                            } else {
                                this.showToast(`Produk berhasil diganti ke ${newProduct.name}.`);
                            }
                        } else {
                            // Fallback in-memory
                            const currentRefundBalance = this.getTotalRefundAmount();
                            this.oosSelectedItem.product_name = `${newProduct.name} (Pengganti ${oldName})`;
                            this.oosSelectedItem.name = this.oosSelectedItem.product_name;
                            this.oosSelectedItem.price = newProduct.price;
                            this.oosSelectedItem.unit_price = newProduct.price;
                            this.oosSelectedItem.subtotal = newProduct.price * (this.oosSelectedItem.qty || 1);
                            this.oosSelectedItem.resolution = 'replaced';
                            this.oosSelectedItem.is_oos = false;

                            if (!this.selectedOrderDetail.status_histories) {
                                this.selectedOrderDetail.status_histories = [];
                            }
                            if (priceDiff > 0) {
                                if (currentRefundBalance >= priceDiff) {
                                    // Offset in memory
                                    if (!this.selectedOrderDetail.invoices) this.selectedOrderDetail.invoices = [];
                                    this.selectedOrderDetail.invoices.push({
                                        id: Date.now(),
                                        type: 'additional',
                                        status: 'paid',
                                        invoice_number: 'INV-ADD-' + Math.floor(Math.random() * 899999 + 100000),
                                        amount: priceDiff,
                                        description: `Tagihan Selisih Ganti Produk: ${newProduct.name} (Pengganti ${oldName}) - Lunas dipotong dari saldo refund`,
                                    });
                                    this.selectedOrderDetail.remaining_refund_total = currentRefundBalance - priceDiff;
                                    this.selectedOrderDetail.status_histories.unshift({
                                        id: Date.now(),
                                        note: `Barang ${oldName} habis di JP. Diganti ke ${newProduct.name} (Selisih +${this.formatRupiah(priceDiff)}). Selisih lunas otomatis dipotong dari dana refund barang habis. Sisa refund: ${this.formatRupiah(currentRefundBalance - priceDiff)}.`,
                                        created_at: new Date().toISOString()
                                    });
                                    this.showToast(`Produk diganti! Selisih lunas otomatis dipotong dari saldo refund.`);
                                } else {
                                    this.selectedOrderDetail.status_histories.unshift({
                                        id: Date.now(),
                                        note: `Barang ${oldName} habis di JP. Diganti ke ${newProduct.name} (Terbit Invoice Tambahan ${this.formatRupiah(priceDiff)}).`,
                                        created_at: new Date().toISOString()
                                    });
                                    this.showToast(`Produk diganti! Terbit Invoice Tambahan: ${this.formatRupiah(priceDiff)}`);
                                }
                            } else if (priceDiff < 0) {
                                const overpaid = Math.abs(priceDiff);
                                this.oosSelectedItem.refund_amount = overpaid;
                                this.oosSelectedItem.refund_status = 'requested';
                                this.selectedOrderDetail.status_histories.unshift({
                                    id: Date.now(),
                                    note: `Barang ${oldName} habis di JP. Diganti ke ${newProduct.name}. Kelebihan pembayaran sebesar ${this.formatRupiah(overpaid)} otomatis diajukan refund kepada pembeli.`,
                                    created_at: new Date().toISOString()
                                });
                                this.showToast(`Produk diganti ke ${newProduct.name}! Kelebihan dana ${this.formatRupiah(overpaid)} diajukan refund.`);
                            } else {
                                this.selectedOrderDetail.status_histories.unshift({
                                    id: Date.now(),
                                    note: `Barang ${oldName} habis di JP. Diganti ke ${newProduct.name} (Harga sama, tanpa penyesuaian biaya).`,
                                    created_at: new Date().toISOString()
                                });
                                this.showToast(`Produk berhasil diganti ke ${newProduct.name}.`);
                            }
                        }
                    } catch (e) {
                        console.error('Replacement API error:', e);
                        this.oosSelectedItem.product_name = `${newProduct.name} (Pengganti ${oldName})`;
                        this.oosSelectedItem.resolution = 'replaced';
                        this.oosSelectedItem.is_oos = false;
                        if (priceDiff < 0) {
                            this.oosSelectedItem.refund_amount = Math.abs(priceDiff);
                            this.oosSelectedItem.refund_status = 'requested';
                        }
                    }

                    this.showOosModal = false;
                },

                hasRefundItems() {
                    return this.getTotalRefundAmount() > 0;
                },

                getTotalRefundAmount() {
                    if (!this.selectedOrderDetail) return 0;
                    if (this.selectedOrderDetail.remaining_refund_total !== undefined && this.selectedOrderDetail.remaining_refund_total !== null) {
                        return this.selectedOrderDetail.remaining_refund_total;
                    }
                    if (this.selectedOrderDetail.refunds && this.selectedOrderDetail.refunds.length > 0) {
                        const pending = this.selectedOrderDetail.refunds.filter(r => r.status === 'pending');
                        return pending.reduce((sum, r) => sum + (r.amount || 0), 0);
                    }
                    if (!this.selectedOrderDetail.items) return 0;
                    const total = this.selectedOrderDetail.items.reduce((sum, it) => {
                        if (it.refund_amount && it.refund_amount > 0) return sum + it.refund_amount;
                        if (it.resolution === 'refunded' || it.refund_status === 'requested') return sum + (it.subtotal || 0);
                        return sum;
                    }, 0);
                    return total;
                },

                getWaRefundUrl(item = null) {
                    const order = this.selectedOrderDetail;
                    const orderNum = order ? order.order_number : '-';
                    const custName = order?.address?.recipient_name || order?.address_snapshot?.recipient_name || this.userProfile?.name || 'Pelanggan';

                    let refundAmountStr = '';
                    let itemDetail = '';

                    if (item) {
                        const amt = item.refund_amount || item.subtotal || 0;
                        refundAmountStr = this.formatRupiah(amt);
                        itemDetail = '\n- Produk: ' + (item.product_name || item.name);
                    } else {
                        const total = this.getTotalRefundAmount();
                        refundAmountStr = this.formatRupiah(total);
                        const refundedItems = order?.items?.filter(it => (it.refund_amount && it.refund_amount > 0) || it.refund_status || it.resolution === 'refunded');
                        if (refundedItems && refundedItems.length > 0) {
                            itemDetail = '\n- Rincian Produk: ' + refundedItems.map(it => `${it.product_name || it.name} (Refund ${this.formatRupiah(it.refund_amount || it.subtotal || 0)})`).join(', ');
                        }
                    }

                    const isEn = this.currentLang === 'en';
                    const text = isEn ?
                        `Hello Admin Galaksian,\n\nI would like to request a refund (price difference) for my order:\n- Order Number: ${orderNum}\n- Customer: ${custName}${itemDetail}\n- Total Refund Amount: ${refundAmountStr}\n\nPlease kindly process this refund to my bank account / e-wallet. Thank you!` :
                        `Halo Admin Galaksian,\n\nSaya ingin meminta proses pengembalian dana (refund selisih harga produk) untuk pesanan saya:\n- No. Pesanan: ${orderNum}\n- Nama: ${custName}${itemDetail}\n- Total Selisih yang Direfund: ${refundAmountStr}\n\nMohon bantuannya untuk memproses refund tersebut ke rekening/e-wallet saya. Terima kasih!`;

                    return 'https://wa.me/6281234567890?text=' + encodeURIComponent(text);
                },

                isProductInvoicePaid(order) {
                    if (!order || !order.invoices) return false;
                    const productInvoices = order.invoices.filter(i => i.type === 'product');
                    if (productInvoices.length === 0) return false;
                    return productInvoices.every(i => i.status === 'paid');
                },

                getShippingInvoice(order) {
                    if (!order || !order.invoices) return null;
                    return order.invoices.find(i => i.type === 'shipping');
                },

                getProductInvoices(order) {
                    if (!order || !order.invoices) return [];
                    return order.invoices.filter(i => i.type === 'product');
                },

                getAdditionalInvoices(order) {
                    if (!order || !order.invoices) return [];
                    return order.invoices.filter(i => i.type === 'additional');
                },

                getCurrentStep(status) {
                    const orderOrder = [
                        'pending_payment_product',
                        'paid_product',
                        'processing',
                        'packing',
                        'ready_for_delivery',
                        'pending_payment_shipping',
                        'shipping_paid',
                        'delivering',
                        'completed'
                    ];
                    const idx = orderOrder.indexOf(status);
                    if (idx <= 1) return 1;
                    if (idx === 2) return 2;
                    if (idx === 3) return 3;
                    if (idx <= 5) return 4;
                    if (idx === 6 || idx === 7) return 5;
                    if (idx >= 8) return 6;
                    return 1;
                },

                isStepPassed(status, stepNum) {
                    return this.getCurrentStep(status) >= stepNum;
                },

                isStepActive(status, stepNum) {
                    return this.getCurrentStep(status) === stepNum;
                },

                // ================= TRANSACTION DETAIL HANDLERS =================
                async openOrderDetail(orderId) {
                    if (!orderId) return;
                    this.selectedOrderId = orderId;
                    this.selectedOrderDetail = null;
                    this.activeTab = 'transactions';
                    this.activeSubView = 'order-detail';
                    this.orderDetailLoading = true;
                    try {
                        const res = await fetch('/api/v1/orders/' + orderId, { headers: this.getHeaders() });
                        const json = await res.json();
                        if (json.success && json.data) {
                            this.selectedOrderDetail = json.data;
                            this.adminSelectedStatus = json.data.status;
                        } else {
                            this.showToast(json.message || 'Gagal memuat detail transaksi.');
                        }
                    } catch (e) {
                        console.error('Fetch order detail error:', e);
                        this.showToast('Gagal memuat detail transaksi.');
                    } finally {
                        this.orderDetailLoading = false;
                    }
                },

                closeOrderDetail() {
                    if (this.qrisTimerInterval) clearInterval(this.qrisTimerInterval);
                    this.activeSubView = null;
                    this.selectedOrderDetail = null;
                    this.selectedOrderId = null;
                },

                // ================= QRIS PAYMENT METHODS =================
                openQrisPayView() {
                    this.activeSubView = 'qris-payment';
                    this.showQrisInstructions = false;
                    this.qrisSecondsRemaining = 2697; // 44:57 matching Figma screenshot
                    if (this.qrisTimerInterval) clearInterval(this.qrisTimerInterval);
                    this.qrisTimerInterval = setInterval(() => {
                        if (this.qrisSecondsRemaining > 0) {
                            this.qrisSecondsRemaining--;
                        } else {
                            clearInterval(this.qrisTimerInterval);
                        }
                    }, 1000);
                },

                closeQrisPayView() {
                    if (this.qrisTimerInterval) clearInterval(this.qrisTimerInterval);
                    if (this.selectedOrderId) {
                        this.activeSubView = 'order-detail';
                    } else {
                        this.activeSubView = null;
                    }
                },

                getQrisPayAmount() {
                    if (!this.selectedOrderDetail) return 748000;
                    if (this.selectedOrderDetail.invoices && this.selectedOrderDetail.invoices.length > 0) {
                        const pending = this.selectedOrderDetail.invoices.find(inv => inv.status === 'pending');
                        if (pending && pending.amount > 0) return pending.amount;
                    }
                    if (this.selectedOrderDetail.pricing && this.selectedOrderDetail.pricing.grand_total) {
                        return this.selectedOrderDetail.pricing.grand_total;
                    }
                    if (this.selectedOrderDetail.pricing && this.selectedOrderDetail.pricing.product_total) {
                        return this.selectedOrderDetail.pricing.product_total;
                    }
                    return 748000;
                },

                get qrisCountdownText() {
                    const mins = Math.floor(this.qrisSecondsRemaining / 60);
                    const secs = this.qrisSecondsRemaining % 60;
                    return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
                },

                async checkQrisPaymentStatus() {
                    this.isCheckingQris = true;
                    try {
                        if (!this.selectedOrderDetail) {
                            await new Promise(r => setTimeout(r, 600));
                            this.showToast('Menunggu pembayaran QRIS...');
                            return;
                        }

                        const pendingInv = this.selectedOrderDetail.invoices ? this.selectedOrderDetail.invoices.find(inv => inv.status === 'pending') : null;
                        if (pendingInv) {
                            await this.simulatePaymentWebhook(pendingInv.invoice_number);
                            this.showToast('Pembayaran QRIS berhasil dikonfirmasi!');
                            await this.openOrderDetail(this.selectedOrderDetail.id);
                        } else {
                            await this.openOrderDetail(this.selectedOrderDetail.id);
                            this.showToast('Status tagihan telah diperiksa dan diperbarui.');
                        }
                    } catch (e) {
                        console.error('Check QRIS error:', e);
                        this.showToast('Gagal memeriksa status pembayaran.');
                    } finally {
                        this.isCheckingQris = false;
                    }
                },

                cancelQrisPayment() {
                    if (confirm(this.currentLang === 'en' ? 'Are you sure you want to cancel the transaction / exit QRIS payment?' : 'Apakah Anda yakin ingin membatalkan transaksi / keluar dari laman pembayaran QRIS?')) {
                        this.closeQrisPayView();
                        this.showToast('Pembayaran QRIS ditutup.');
                    }
                },

                async changeOrderStatus(orderId) {
                    try {
                        const loginRes = await fetch('/api/v1/admin/auth/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ login: 'admin@galaksian.com', password: 'password' })
                        });
                        const loginJson = await loginRes.json();
                        if (!loginJson.success) throw new Error('Admin login error');
                        const adminToken = loginJson.data.token;

                        const body = {
                            status: this.adminSelectedStatus,
                            note: 'Simulasi admin testing frontend detail transaksi'
                        };
                        if (this.adminSelectedStatus === 'ready_for_delivery') {
                            body.shipping_jastip_amount = 25000;
                            body.shipping_local_amount = 10000;
                        }

                        const patchRes = await fetch(`/api/v1/admin/orders/${orderId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' + adminToken
                            },
                            body: JSON.stringify(body)
                        });
                        const patchJson = await patchRes.json();
                        if (patchJson.success) {
                            this.showToast('Status berhasil diubah ke: ' + this.adminSelectedStatus);
                            await this.openOrderDetail(orderId);
                            await this.fetchOrders();
                        } else {
                            this.showToast(patchJson.message || 'Gagal update status admin.');
                        }
                    } catch (e) {
                        console.error('Change status error:', e);
                        this.showToast('Gagal update status admin.');
                    }
                },

                formatDateTime(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr);
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                },

                async simulatePaymentWebhook(invoiceNumber) {
                    if (!invoiceNumber) return;
                    this.isSimulatingPayment = true;
                    try {
                        const res = await fetch('/api/v1/webhooks/payment', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({
                                event_id: 'sim-' + Date.now(),
                                invoice_number: invoiceNumber,
                                status: 'paid'
                            })
                        });
                        const json = await res.json();
                        if (json.success) {
                            if (this.paymentResult) this.paymentResult.isPaid = true;
                            this.showToast('Webhook berhasil. Status pembayaran LUNAS.');

                            const targetOrderId = json.data?.order_id 
                                || this.paymentResult?.order?.id 
                                || this.selectedOrderDetail?.id 
                                || this.selectedOrderId;

                            if (targetOrderId) {
                                this.selectedOrderId = targetOrderId;
                                if (this.activeSubView === 'order-detail') {
                                    await this.openOrderDetail(targetOrderId);
                                }
                            }
                            await this.fetchOrders();
                        } else {
                            this.showToast(json.message || 'Webhook gagal diproses', 'error');
                        }
                    } catch (e) {
                        this.showToast('Koneksi webhook gagal', 'error');
                    } finally {
                        this.isSimulatingPayment = false;
                    }
                },

                async fetchOrders() {
                    if (!this.isLoggedIn) return;
                    try {
                        let url = '/api/v1/orders?type=' + this.transactionTab;
                        if (this.transactionSearchQuery && this.transactionSearchQuery.trim()) {
                            url += '&search=' + encodeURIComponent(this.transactionSearchQuery.trim());
                        }
                        const res = await fetch(url, { headers: this.getHeaders() });
                        const json = await res.json();
                        if (json.success) {
                            this.orders = json.data.data || [];
                        }
                    } catch (e) {
                        console.error('Fetch orders error:', e);
                    }
                },

                getFilteredOrders() {
                    if (!this.orders) return [];
                    const q = (this.transactionSearchQuery || '').toLowerCase().trim();
                    if (!q) return this.orders;
                    return this.orders.filter(order => {
                        const num = (order.order_number || '').toLowerCase();
                        const itemMatch = order.items?.some(i => (i.product_name || i.name || '').toLowerCase().includes(q));
                        return num.includes(q) || itemMatch;
                    });
                },

                getShippingInvoice(order) {
                    if (!order.invoices) return null;
                    return order.invoices.find(inv => inv.type === 'shipping');
                },

                openInvoiceDownloadModal(order) {
                    if (!order || !order.invoices || order.invoices.length === 0) {
                        this.showToast(this.t('no_invoices_available', 'Tidak ada invoice yang tersedia untuk pesanan ini.'), 'error');
                        return;
                    }
                    if (order.invoices.length === 1) {
                        this.downloadInvoicePdf(order.id, order.invoices[0].id);
                        return;
                    }
                    this.selectedInvoiceOrder = order;
                    this.showInvoiceDownloadModal = true;
                },

                async downloadAllInvoices(order) {
                    if (!order || !order.invoices || order.invoices.length === 0) return;
                    this.showToast(this.t('downloading_all_invoices', 'Mengunduh seluruh invoice...'));
                    for (const inv of order.invoices) {
                        await this.downloadInvoicePdf(order.id, inv.id);
                        await new Promise(r => setTimeout(r, 700));
                    }
                    this.showInvoiceDownloadModal = false;
                    this.showToast(this.t('all_invoices_downloaded', 'Semua invoice berhasil diunduh.'));
                },

                getInvoiceTypeLabel(type) {
                    switch (type) {
                        case 'product':
                            return this.t('product_invoice_badge', 'Invoice Produk');
                        case 'additional':
                            return this.t('additional_invoice_badge', 'Invoice Tambahan');
                        case 'shipping':
                            return this.t('shipping_invoice_badge', 'Invoice Ongkir');
                        default:
                            return (type || 'Invoice').toUpperCase();
                    }
                },

                getInvoiceTypeBadgeClass(type) {
                    switch (type) {
                        case 'product':
                            return 'bg-zinc-200 text-zinc-800 border border-zinc-300';
                        case 'additional':
                            return 'bg-amber-100 text-amber-900 border border-amber-200';
                        case 'shipping':
                            return 'bg-blue-100 text-blue-900 border border-blue-200';
                        default:
                            return 'bg-zinc-100 text-zinc-700 border border-zinc-200';
                    }
                },

                async downloadInvoicePdf(orderId, invoiceId) {
                    try {
                        const res = await fetch(`/api/v1/orders/${orderId}/invoices/${invoiceId}/download`, {
                            headers: this.getHeaders()
                        });
                        if (!res.ok) throw new Error('Download failed');
                        const blob = await res.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Invoice-${orderId}-${invoiceId}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        this.showToast(this.t('invoice_download_success', 'Invoice PDF berhasil diunduh.'));
                    } catch (e) {
                        this.showToast(this.t('invoice_download_failed', 'Gagal mengunduh invoice PDF'), 'error');
                    }
                },

                async adminSimulateStatus(orderId, newStatus, shippingAmount = null) {
                    try {
                        const loginRes = await fetch('/api/v1/admin/auth/login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ login: 'admin@galaksian.com', password: 'password' })
                        });
                        const loginJson = await loginRes.json();
                        if (!loginJson.success) throw new Error('Admin auth error');
                        const adminToken = loginJson.data.token;

                        const body = { status: newStatus, note: 'Simulasi admin pengembang' };
                        if (shippingAmount) {
                            body.shipping_jastip_amount = shippingAmount - 10000;
                            body.shipping_local_amount = 10000;
                        }
                        const patchRes = await fetch(`/api/v1/admin/orders/${orderId}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Authorization': 'Bearer ' + adminToken
                            },
                            body: JSON.stringify(body)
                        });
                        const patchJson = await patchRes.json();
                        if (patchJson.success) {
                            this.showToast(`Status order diperbarui: ${newStatus}`);
                            await this.openOrderDetail(orderId);
                            await this.fetchOrders();
                        } else {
                            this.showToast(patchJson.message || 'Gagal update status admin', 'error');
                        }
                    } catch (e) {
                        this.showToast('Simulasi admin gagal', 'error');
                    }
                },

                async reorderItems(order) {
                    if (!order.items) return;
                    for (const item of order.items) {
                        await this.addToCart({ id: item.product_id }, item.qty);
                    }
                    this.showToast('Semua item telah dimasukkan kembali ke keranjang.');
                    this.goToTab('cart');
                },

                openReviewModal(order) {
                    this.reviewForm.orderId = order.id;
                    this.reviewForm.productId = order.items[0]?.product_id || null;
                    this.reviewForm.rating = 5;
                    this.reviewForm.comment = '';
                    this.showReviewModal = true;
                },

                async submitReview() {
                    if (!this.reviewForm.productId) return;
                    try {
                        const res = await fetch(`/api/v1/orders/${this.reviewForm.orderId}/review`, {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({
                                product_id: this.reviewForm.productId,
                                rating: this.reviewForm.rating,
                                comment: this.reviewForm.comment
                            })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.showToast('Ulasan Anda telah tersimpan.');
                            this.showReviewModal = false;
                        } else {
                            this.showToast(json.message || 'Gagal mengirim ulasan', 'error');
                        }
                    } catch (e) {
                        this.showToast('Koneksi ulasan gagal', 'error');
                    }
                },

                async fetchAddresses() {
                    if (!this.isLoggedIn) return;
                    try {
                        const res = await fetch('/api/v1/addresses', { headers: this.getHeaders() });
                        const json = await res.json();
                        if (json.success) {
                            this.userAddresses = json.data || [];
                            this.defaultAddress = this.userAddresses.find(a => a.is_default) || this.userAddresses[0] || null;
                        }
                    } catch (e) {
                        console.error('Address fetch error:', e);
                    }
                },

                openAddressModal(addr = null) {
                    if (addr) {
                        this.editingAddressId = addr.id;
                        this.addressForm = {
                            recipient_name: addr.recipient_name,
                            phone: addr.phone,
                            address: addr.address,
                            city: addr.city || 'Jakarta Selatan',
                            postal_code: addr.postal_code || '12190',
                            delivery_note: addr.delivery_note || 'leave_at_front_door',
                            is_default: !!addr.is_default
                        };
                    } else {
                        this.editingAddressId = null;
                        this.addressForm = {
                            recipient_name: this.currentUser?.name || '',
                            phone: this.currentUser?.phone || '',
                            address: '',
                            city: 'Jakarta Selatan',
                            postal_code: '12190',
                            delivery_note: 'leave_at_front_door',
                            is_default: this.userAddresses.length === 0
                        };
                    }
                    this.showAddressModal = true;
                },

                getFilteredAddresses() {
                    if (!this.userAddresses) return [];
                    const q = (this.addressSearchQuery || '').toLowerCase().trim();
                    if (!q) return this.userAddresses;
                    return this.userAddresses.filter(a => {
                        const name = (a.recipient_name || '').toLowerCase();
                        const addr = (a.address || '').toLowerCase();
                        const city = (a.city || '').toLowerCase();
                        const phone = (a.phone || '').toLowerCase();
                        return name.includes(q) || addr.includes(q) || city.includes(q) || phone.includes(q);
                    });
                },

                async saveAddress() {
                    if (!this.addressForm.recipient_name || !this.addressForm.phone || !this.addressForm.address) {
                        this.showToast('Mohon lengkapi semua data alamat.', 'error');
                        return;
                    }
                    try {
                        const url = this.editingAddressId ? ('/api/v1/addresses/' + this.editingAddressId) : '/api/v1/addresses';
                        const method = this.editingAddressId ? 'PUT' : 'POST';
                        const res = await fetch(url, {
                            method: method,
                            headers: this.getHeaders(),
                            body: JSON.stringify(this.addressForm)
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.showToast(this.editingAddressId ? 'Alamat berhasil diperbarui.' : 'Alamat berhasil disimpan.');
                            this.showAddressModal = false;
                            this.editingAddressId = null;
                            this.fetchAddresses();
                        } else {
                            this.showToast(json.message || 'Gagal menyimpan alamat', 'error');
                        }
                    } catch (e) {
                        this.showToast('Koneksi alamat gagal', 'error');
                    }
                },

                async setDefaultAddress(addr) {
                    try {
                        const res = await fetch('/api/v1/addresses/' + addr.id, {
                            method: 'PUT',
                            headers: this.getHeaders(),
                            body: JSON.stringify({
                                recipient_name: addr.recipient_name,
                                phone: addr.phone,
                                address: addr.address,
                                is_default: true
                            })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.showToast('Alamat utama diperbarui.');
                            this.fetchAddresses();
                        }
                    } catch (e) {
                        this.showToast('Gagal mengatur alamat utama', 'error');
                    }
                },

                async deleteAddress(id) {
                    if (!confirm(this.currentLang === 'en' ? 'Delete this address from your saved list?' : 'Hapus alamat ini dari daftar?')) return;
                    try {
                        const res = await fetch('/api/v1/addresses/' + id, {
                            method: 'DELETE',
                            headers: this.getHeaders()
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.showToast('Alamat telah dihapus.');
                            this.fetchAddresses();
                        }
                    } catch (e) {
                        this.showToast('Gagal menghapus alamat', 'error');
                    }
                },

                async quickLoginDemo() {
                    this.authLoading = true;
                    try {
                        await fetch('/api/v1/auth/otp/request', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ phone: '6281234567890' })
                        });
                        const res = await fetch('/api/v1/auth/otp/verify', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ phone: '6281234567890', code: '123456' })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.authToken = json.data.token;
                            this.currentUser = json.data.user;
                            this.isLoggedIn = true;
                            localStorage.setItem('galaksian_token', this.authToken);
                            this.showToast('Masuk sebagai ' + this.currentUser.name);
                            this.fetchCart();
                            this.fetchAddresses();
                        } else {
                            this.showToast(json.message || 'Login gagal', 'error');
                        }
                    } catch (e) {
                        this.showToast('Gagal melakukan login', 'error');
                    } finally {
                        this.authLoading = false;
                    }
                },

                async requestOtp() {
                    if (!this.authPhone) return;
                    this.authLoading = true;
                    try {
                        const res = await fetch('/api/v1/auth/otp/request', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ phone: this.authPhone })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.otpStep = 'verify';
                            this.showToast('Kode OTP terkirim (Demo: 123456)');
                        } else {
                            this.showToast(json.message || 'Gagal mengirim OTP', 'error');
                        }
                    } catch (e) {
                        this.showToast('Koneksi OTP gagal', 'error');
                    } finally {
                        this.authLoading = false;
                    }
                },

                async verifyOtp() {
                    if (!this.authOtp) return;
                    this.authLoading = true;
                    try {
                        const res = await fetch('/api/v1/auth/otp/verify', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ phone: this.authPhone, code: this.authOtp })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.authToken = json.data.token;
                            this.currentUser = json.data.user;
                            this.isLoggedIn = true;
                            localStorage.setItem('galaksian_token', this.authToken);
                            this.showToast('Verifikasi sukses.');
                            this.fetchCart();
                            this.fetchAddresses();
                        } else {
                            this.showToast(json.message || 'OTP tidak cocok', 'error');
                        }
                    } catch (e) {
                        this.showToast('Verifikasi gagal', 'error');
                    } finally {
                        this.authLoading = false;
                    }
                },

                async fetchUserProfile() {
                    try {
                        const res = await fetch('/api/v1/me', { headers: this.getHeaders() });
                        const json = await res.json();
                        if (json.success) {
                            this.currentUser = json.data;
                            if (json.data.identity_number) {
                                this.profileKtp = json.data.identity_number;
                            }
                            this.fetchAddresses();
                        } else {
                            this.logout();
                        }
                    } catch (e) {
                        console.error('Fetch me error:', e);
                    }
                },

                startInlineEdit(field, currentValue) {
                    this.inlineEditingField = field;
                    this.inlineEditValue = currentValue || '';
                    this.$nextTick(() => {
                        const inputEl = this.$refs['inlineInput_' + field];
                        if (inputEl) {
                            inputEl.focus();
                            inputEl.select();
                        }
                    });
                },

                cancelInlineEdit() {
                    this.inlineEditingField = null;
                    this.inlineEditValue = '';
                },

                async saveInlineEdit(field) {
                    if (!this.currentUser) return;
                    const val = (this.inlineEditValue || '').trim();
                    if (!val && field !== 'identity_number') {
                        this.showToast('Kolom ini tidak boleh kosong.', 'error');
                        return;
                    }
                    this.inlineEditLoading = true;
                    try {
                        const payload = {};
                        if (field === 'name') {
                            this.currentUser.name = val;
                            payload.name = val;
                        } else if (field === 'email') {
                            this.currentUser.email = val;
                            payload.email = val;
                        } else if (field === 'phone') {
                            this.currentUser.phone = val;
                            payload.phone = val;
                        } else if (field === 'identity_number') {
                            this.profileKtp = val;
                            this.currentUser.identity_number = val;
                            payload.identity_number = val;
                        }

                        const res = await fetch('/api/v1/me', {
                            method: 'PUT',
                            headers: this.getHeaders(),
                            body: JSON.stringify(payload)
                        });
                        const json = await res.json();
                        if (json.success) {
                            if (json.data) {
                                this.currentUser = { ...this.currentUser, ...json.data };
                                if (json.data.identity_number) this.profileKtp = json.data.identity_number;
                            }
                            localStorage.setItem('galaksian_user', JSON.stringify(this.currentUser));
                            this.showToast('Biodata berhasil diperbarui.');
                            this.inlineEditingField = null;
                            this.inlineEditValue = '';
                        } else {
                            this.showToast(json.message || 'Gagal memperbarui biodata.', 'error');
                        }
                    } catch (e) {
                        this.showToast('Gagal memperbarui biodata.', 'error');
                    } finally {
                        this.inlineEditLoading = false;
                    }
                },

                openChangePasswordModal() {
                    this.currentPassword = '';
                    this.newPassword = '';
                    this.confirmPassword = '';
                    this.showCurrentPassword = false;
                    this.showNewPassword = false;
                    this.showConfirmPassword = false;
                    this.changePasswordModal = true;
                },

                closeChangePasswordModal() {
                    this.changePasswordModal = false;
                    this.currentPassword = '';
                    this.newPassword = '';
                    this.confirmPassword = '';
                },

                async submitChangePassword() {
                    if (!this.newPassword || this.newPassword.length < 8) {
                        this.showToast('Password baru minimal 8 karakter.', 'error');
                        return;
                    }
                    if (this.newPassword !== this.confirmPassword) {
                        this.showToast('Konfirmasi password baru tidak cocok.', 'error');
                        return;
                    }
                    this.changePasswordLoading = true;
                    try {
                        const res = await fetch('/api/v1/change-password', {
                            method: 'POST',
                            headers: this.getHeaders(),
                            body: JSON.stringify({
                                current_password: this.currentPassword,
                                password: this.newPassword,
                                password_confirmation: this.confirmPassword
                            })
                        });
                        const json = await res.json();
                        if (json.success) {
                            this.showToast('Password berhasil diperbarui.');
                            this.closeChangePasswordModal();
                        } else {
                            this.showToast(json.message || 'Gagal mengubah password.', 'error');
                        }
                    } catch (e) {
                        this.showToast('Gagal mengubah password.', 'error');
                    } finally {
                        this.changePasswordLoading = false;
                    }
                },

                openChangeAvatar() {
                    this.$refs.avatarFileInput?.click();
                },

                async handleAvatarUpload(event) {
                    const file = event.target.files?.[0];
                    if (!file) return;
                    if (file.size > 3 * 1024 * 1024) {
                        this.showToast('Ukuran gambar maksimal 3MB.', 'error');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = async (e) => {
                        const base64Url = e.target.result;
                        await this.saveAvatar(base64Url);
                    };
                    reader.readAsDataURL(file);
                },

                async saveAvatar(url) {
                    if (!this.currentUser) return;
                    this.currentUser.avatar_url = url;
                    localStorage.setItem('galaksian_user_avatar', url);
                    localStorage.setItem('galaksian_user', JSON.stringify(this.currentUser));
                    try {
                        await fetch('/api/v1/me', {
                            method: 'PUT',
                            headers: this.getHeaders(),
                            body: JSON.stringify({ avatar_url: url })
                        });
                        this.showToast('Foto profil berhasil diperbarui.');
                    } catch (e) {
                        this.showToast('Foto profil berhasil disimpan.');
                    }
                },

                getProfileAvatar() {
                    if (this.currentUser?.avatar_url) return this.currentUser.avatar_url;
                    const local = localStorage.getItem('galaksian_user_avatar');
                    if (local) return local;
                    return 'https://images.unsplash.com/photo-1534447677768-be436bb09401?w=400&fit=crop&q=80';
                },

                logout() {
                    this.isLoggedIn = false;
                    this.authToken = null;
                    this.currentUser = null;
                    this.otpStep = 'phone';
                    this.authOtp = '';
                    localStorage.removeItem('galaksian_token');
                    this.showToast('Anda telah keluar.');
                    this.goToTab('home');
                },

                formatRupiah(num) {
                    if (num === null || num === undefined) return 'Rp 0';
                    return 'Rp ' + Number(num).toLocaleString('id-ID');
                },

                formatDate(dateStr) {
                    if (!dateStr) return '';
                    const d = new Date(dateStr);
                    const locale = this.currentLang === 'en' ? 'en-US' : 'id-ID';
                    return d.toLocaleDateString(locale, { day: 'numeric', month: 'short', year: 'numeric' });
                },

                                getOosButtonText() {
                    const diff = (this.oosReplacementProduct?.price || 0) - this.getOosOldItemPrice();
                    const refundTotal = this.getTotalRefundAmount();
                    const isEn = this.currentLang === 'en';
                    if (diff > 0) {
                        if (refundTotal >= diff) {
                            return isEn ? 'Replace Product (Settled via Refund Balance)' : 'Ganti Produk (Lunas Potong Saldo Refund)';
                        }
                        if (refundTotal > 0) {
                            const remaining = diff - refundTotal;
                            return isEn ? `Replace Product & Offset Refund (Remaining ${this.formatRupiah(remaining)})` : `Ganti Produk & Potong Refund (Sisa Bayar ${this.formatRupiah(remaining)})`;
                        }
                        return isEn ? 'Replace Product & Issue Additional Invoice' : 'Ganti Produk & Terbitkan Invoice Tambahan';
                    } else if (diff < 0) {
                        const overpaid = this.getOosOldItemPrice() - (this.oosReplacementProduct?.price || 0);
                        return isEn ? `Replace Product & Claim Refund Diff (${this.formatRupiah(overpaid)})` : `Ganti Produk & Ajukan Refund Selisih (${this.formatRupiah(overpaid)})`;
                    } else {
                        return isEn ? 'Confirm Product Replacement' : 'Konfirmasi Ganti Produk';
                    }
                },

                getDeliveryNoteLabel(note) {
                    const isEn = this.currentLang === 'en';
                    const dict = isEn ? {
                        'leave_at_front_door': 'Front door',
                        'contact_before_delivery': 'Call before delivery',
                        'hand_to_receiver': 'Hand to receiver directly',
                        'security_desk': 'Security desk',
                        'other': 'Other'
                    } : {
                        'leave_at_front_door': 'Taruh di depan pintu',
                        'contact_before_delivery': 'Hubungi sebelum antar',
                        'hand_to_receiver': 'Serahkan langsung ke penerima',
                        'security_desk': 'Titip di pos satpam',
                        'other': 'Lainnya'
                    };
                    return dict[note] || note;
                },

                getOrderStatusColor(status) {
                    const map = {
                        'pending_payment_product': 'bg-amber-50 text-amber-800 border-amber-200',
                        'paid_product': 'bg-blue-50 text-blue-800 border-blue-200',
                        'processing': 'bg-zinc-100 text-zinc-800 border-zinc-200',
                        'packing': 'bg-purple-50 text-purple-800 border-purple-200',
                        'ready_for_delivery': 'bg-orange-50 text-orange-800 border-orange-200',
                        'pending_payment_shipping': 'bg-amber-50 text-amber-800 border-amber-200',
                        'shipping_paid': 'bg-cyan-50 text-cyan-800 border-cyan-200',
                        'delivering': 'bg-teal-50 text-teal-800 border-teal-200',
                        'completed': 'bg-emerald-50 text-emerald-800 border-emerald-200',
                        'cancelled': 'bg-zinc-100 text-zinc-600 border-zinc-200',
                        'refunded': 'bg-red-50 text-red-800 border-red-200'
                    };
                    return map[status] || 'bg-zinc-100 text-zinc-700 border-zinc-200';
                },

                getOrderStatusLabel(status) {
                    const isEn = this.currentLang === 'en';
                    const dict = isEn ? {
                        'pending_payment_product': 'Awaiting Product Payment',
                        'paid_product': 'Product Paid',
                        'processing': 'Shopping in Japan',
                        'packing': 'Packing in Progress',
                        'ready_for_delivery': 'Ready to Ship (Shipping Fee)',
                        'pending_payment_shipping': 'Awaiting Shipping Payment',
                        'shipping_paid': 'Shipping Paid',
                        'delivering': 'In Delivery',
                        'completed': 'Order Completed',
                        'cancelled': 'Cancelled',
                        'refunded': 'Refunded'
                    } : {
                        'pending_payment_product': 'Menunggu Bayar Produk',
                        'paid_product': 'Produk Lunas',
                        'processing': 'Dibelanjakan di JP',
                        'packing': 'Sedang Dipacking',
                        'ready_for_delivery': 'Siap Kirim (Ongkir)',
                        'pending_payment_shipping': 'Menunggu Bayar Ongkir',
                        'shipping_paid': 'Ongkir Lunas',
                        'delivering': 'Dalam Pengiriman',
                        'completed': 'Pesanan Selesai',
                        'cancelled': 'Dibatalkan',
                        'refunded': 'Dana Dikembalikan'
                    };
                    return dict[status] || status;
                },

                isStepPassed(status, stepNum) {
                    const orderOrder = [
                        'pending_payment_product',
                        'paid_product',
                        'processing',
                        'packing',
                        'ready_for_delivery',
                        'pending_payment_shipping',
                        'shipping_paid',
                        'delivering',
                        'completed'
                    ];
                    const idx = orderOrder.indexOf(status);
                    const currentStep = idx <= 1 ? 1 : idx === 2 ? 2 : idx === 3 ? 3 : idx <= 6 ? 4 : idx === 7 ? 5 : 6;
                    return currentStep >= stepNum;
                },

                copyToClipboard(text) {
                    navigator.clipboard.writeText(text);
                    this.showToast(this.t('copied_clipboard', 'Nomor berhasil disalin.'));
                },

                startCountdown() {
                    let totalSeconds = 4 * 3600 + 18 * 60 + 29;
                    setInterval(() => {
                        if (totalSeconds <= 0) totalSeconds = 12 * 3600;
                        totalSeconds--;
                        const h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                        const m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                        const s = String(totalSeconds % 60).padStart(2, '0');
                        this.flashSaleCountdown = { h, m, s };
                    }, 1000);
                },

                getFallbackImage(prod) {
                    if (!prod) return 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&fit=crop&q=80';
                    const name = prod.name ? prod.name.toLowerCase() : '';
                    if (name.includes('indomie') || name.includes('mie')) {
                        return 'https://images.unsplash.com/photo-1612927601601-6638404737ce?w=400&fit=crop&q=80';
                    }
                    if (name.includes('sambal') || name.includes('bumbu') || name.includes('rendang')) {
                        return 'https://images.unsplash.com/photo-1588166524941-3bf61a9c41db?w=400&fit=crop&q=80';
                    }
                    if (name.includes('meiji') || name.includes('chocolate') || name.includes('cokelat') || name.includes('bourbon') || name.includes('alfort')) {
                        return 'https://images.unsplash.com/photo-1549007994-cb92caebd54b?w=400&fit=crop&q=80';
                    }
                    if (name.includes('headphone') || name.includes('sony') || name.includes('switch') || name.includes('nintendo') || name.includes('elektronik')) {
                        return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&fit=crop&q=80';
                    }
                    if (name.includes('tea') || name.includes('matcha') || name.includes('oolong') || name.includes('kopi') || name.includes('coffee')) {
                        return 'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=400&fit=crop&q=80';
                    }
                    if (name.includes('sunscreen') || name.includes('shiseido') || name.includes('suncut') || name.includes('biore') || name.includes('uv') || name.includes('lotion') || name.includes('cleansing') || name.includes('hada labo') || name.includes('curel') || name.includes('fancl') || name.includes('dhc') || name.includes('cushion') || name.includes('skincare') || name.includes('beauty')) {
                        return 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400&fit=crop&q=80';
                    }
                    if (name.includes('hair') || name.includes('wax') || name.includes('makarizo') || name.includes('gatsby')) {
                        return 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&fit=crop&q=80';
                    }
                    if (name.includes('sukro') || name.includes('kacang') || name.includes('calbee') || name.includes('potato') || name.includes('crisps') || name.includes('snack') || name.includes('caramel') || name.includes('pino')) {
                        return 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&fit=crop&q=80';
                    }
                    return 'https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=400&fit=crop&q=80';
                }
            }
        }
    </script>
