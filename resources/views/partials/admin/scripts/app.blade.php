<script>
    function adminApp() {
        return {
            // Auth
            isLoggedIn: false,
            adminToken: null,
            adminUser: null,
            loginEmail: '',
            loginPassword: '',
            loginLoading: false,

            // UI
            activeView: 'dashboard',
            currentLang: localStorage.getItem('galaksian_admin_lang') || 'id',
            translations: window.ADMIN_TRANSLATIONS || {},
            toast: { show: false, message: '', type: 'success' },

            // Dashboard
            dashboard: {},
            dashboardLoading: false,

            // Orders
            orders: [],
            recentOrders: [],
            ordersLoading: false,
            orderSearch: '',
            filterOrderStatus: '',
            ordersPage: 1,
            ordersMeta: {},
            pendingOrdersCount: 0,

            // Order Detail
            selectedOrder: null,
            orderDetailLoading: false,
            actionStatus: '',
            actionNote: '',
            actionShippingJastip: 0,
            actionShippingLocal: 0,
            actionShipmentId: null,
            actionInvoiceAmount: null,
            actionInvoiceDesc: '',
            actionLoading: false,

            orderStatusList: [
                { value: 'pending_payment_product', label: '' },
                { value: 'paid_product', label: '' },
                { value: 'processing', label: '' },
                { value: 'packing', label: '' },
                { value: 'ready_for_delivery', label: '' },
                { value: 'pending_payment_shipping', label: '' },
                { value: 'shipping_paid', label: '' },
                { value: 'delivering', label: '' },
                { value: 'completed', label: '' },
                { value: 'cancelled', label: '' },
            ],

            // =================== PRODUCTS ===================
            products: [],
            productsLoading: false,
            productsMeta: {},
            productsPage: 1,
            productSearch: '',
            productFilterBrand: '',
            productFilterCategory: '',
            productFilterActive: '',
            productForm: { id: null, name: '', sku: '', slug: '', price: 0, discount_price: null, stock: 0, low_stock_threshold: 0, availability_type: 'ready_stock', brand_id: '', category_id: '', description: '', is_active: true, images: [] },
            productFormLoading: false,
            productImportJson: '',
            importLoading: false,

            // =================== SHIPMENTS ===================
            shipments: [],
            shipmentsLoading: false,
            shipmentForm: { id: null, shipment_number: '', trip_id: '', origin_country: 'ID', destination_country: 'JP', bagasian_reference: '', packing_estimate_weight: '', packing_estimate_volume: '', packing_estimate_cost: 0, notes: '' },

            // =================== TRIPS ===================
            trips: [],
            tripsLoading: false,
            tripForm: { id: null, code: '', origin_country: 'ID', destination_country: 'JP', departure_at: '', arrival_at: '', cutoff_at: '', status: 'draft', notes: '' },

            // =================== REFUNDS ===================
            refunds: [],
            refundsLoading: false,
            refundsMeta: {},
            refundForm: { order_id: '', invoice_id: '', amount: 0, reason: '', refund_method: 'bank_transfer' },

            // =================== USERS ===================
            users: [],
            usersLoading: false,
            usersMeta: {},
            usersPage: 1,
            userSearch: '',
            userForm: { id: null, name: '', phone: '', email: '', role: 'user' },

            // =================== CATALOG (Brand/Category/Banner) ===================
            brands: [],
            categories: [],
            banners: [],
            catalogLoading: false,
            brandForm: { id: null, name: '', slug: '', logo_path: '', is_active: true },
            categoryForm: { id: null, name: '', slug: '', image_path: '', is_active: true },
            bannerForm: { id: null, title: '', image_path: '', link_url: '', cta_text: '', order: 0, is_active: true, locale: 'id', country_filter: 'all' },

            init() {
                // Restore session
                const token = localStorage.getItem('galaksian_admin_token');
                const user = localStorage.getItem('galaksian_admin_user');
                if (token && user) {
                    this.adminToken = token;
                    this.adminUser = JSON.parse(user);
                    this.isLoggedIn = true;
                    this.loadDashboard();
                    this.loadOrders();
                }
                this.updateStatusLabels();
            },

            updateStatusLabels() {
                this.orderStatusList.forEach(s => {
                    s.label = this.t('status_' + s.value, s.value);
                });
            },

            // =================== AUTH ===================
            async doLogin() {
                if (!this.loginEmail || !this.loginPassword) {
                    this.showToast('Email dan password wajib diisi.', 'error');
                    return;
                }
                this.loginLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/auth/login', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        // Kontrak backend memakai field 'login' (email/phone), bukan 'email'.
                        body: JSON.stringify({ login: this.loginEmail, password: this.loginPassword })
                    });
                    const json = await res.json();
                    if (json.success && json.data?.token) {
                        this.adminToken = json.data.token;
                        this.adminUser = json.data.user;
                        localStorage.setItem('galaksian_admin_token', json.data.token);
                        localStorage.setItem('galaksian_admin_user', JSON.stringify(json.data.user));
                        this.isLoggedIn = true;
                        this.showToast(this.t('welcome_admin', 'Selamat datang') + ', ' + (json.data.user?.name || 'Admin'));
                        this.loadDashboard();
                        this.loadOrders();
                    } else {
                        this.showToast(json.message || 'Login gagal.', 'error');
                    }
                } catch (e) {
                    this.showToast('Koneksi gagal.', 'error');
                } finally {
                    this.loginLoading = false;
                }
            },

            logout() {
                this.isLoggedIn = false;
                this.adminToken = null;
                this.adminUser = null;
                localStorage.removeItem('galaksian_admin_token');
                localStorage.removeItem('galaksian_admin_user');
                this.showToast('Berhasil keluar.');
            },

            // =================== NAVIGATION ===================
            goTo(view) {
                this.activeView = view;
                if (view === 'orders') this.loadOrders();
                if (view === 'dashboard') this.loadDashboard();
                if (view === 'products' || view === 'product-form') this.loadProducts();
                if (view === 'shipments' || view === 'shipment-form') this.loadShipments();
                if (view === 'trips' || view === 'trip-form') this.loadTrips();
                if (view === 'refunds') this.loadRefunds();
                if (view === 'users' || view === 'user-form') this.loadUsers();
                if (view === 'brands') this.loadCatalog('brands');
                if (view === 'categories') this.loadCatalog('categories');
                if (view === 'banners') this.loadCatalog('banners');
            },

            // =================== DASHBOARD ===================
            async loadDashboard() {
                this.dashboardLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/dashboard', { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) {
                        this.dashboard = json.data;
                    }
                } catch (e) {
                    console.error('Dashboard error:', e);
                } finally {
                    this.dashboardLoading = false;
                }
            },

            // =================== ORDERS ===================
            async loadOrders() {
                this.ordersLoading = true;
                try {
                    let url = '/api/v1/admin/orders?per_page=15&page=' + this.ordersPage;
                    if (this.filterOrderStatus) url += '&status=' + this.filterOrderStatus;
                    if (this.orderSearch) url += '&q=' + encodeURIComponent(this.orderSearch);

                    const res = await fetch(url, { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) {
                        this.orders = json.data.data || [];
                        this.ordersMeta = json.data.meta || {};
                        if (!this.filterOrderStatus && !this.orderSearch && this.ordersPage === 1) {
                            this.recentOrders = this.orders;
                        }
                        // Count pending
                        this.pendingOrdersCount = this.orders.filter(o =>
                            ['pending_payment_product', 'paid_product', 'processing'].includes(o.status)
                        ).length;
                    }
                } catch (e) {
                    console.error('Orders error:', e);
                } finally {
                    this.ordersLoading = false;
                }
            },

            searchOrders() {
                this.ordersPage = 1;
                this.loadOrders();
            },

            async openOrderDetail(orderId) {
                this.activeView = 'order-detail';
                this.selectedOrder = null;
                this.orderDetailLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/orders/' + orderId, { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success && json.data) {
                        this.selectedOrder = json.data;
                        this.actionStatus = json.data.status;
                        this.actionNote = '';
                        // Field ongkir & shipment dibaca dari respons OrderDetailResource:
                        // pricing.* dan shipment (object), bukan top-level flat.
                        const pricing = json.data.pricing || {};
                        this.actionShippingJastip = pricing.shipping_jastip_amount || 0;
                        this.actionShippingLocal = pricing.shipping_local_amount || 0;
                        this.actionShipmentId = json.data.shipment?.id || null;
                        this.actionInvoiceAmount = null;
                        this.actionInvoiceDesc = '';
                    } else {
                        this.showToast(json.message || 'Gagal memuat order.', 'error');
                        this.goTo('orders');
                    }
                } catch (e) {
                    this.showToast('Gagal memuat order.', 'error');
                    this.goTo('orders');
                } finally {
                    this.orderDetailLoading = false;
                }
            },

            // =================== ADMIN ACTIONS ===================
            async doUpdateStatus() {
                if (!this.selectedOrder) return;
                this.actionLoading = true;
                try {
                    const body = { status: this.actionStatus, note: this.actionNote || null };
                    if (this.actionStatus === 'ready_for_delivery' || this.actionStatus === 'pending_payment_shipping') {
                        body.shipping_jastip_amount = this.actionShippingJastip || 0;
                        body.shipping_local_amount = this.actionShippingLocal || 0;
                    }
                    const res = await fetch('/api/v1/admin/orders/' + this.selectedOrder.id + '/status', {
                        method: 'PATCH',
                        headers: this.getHeaders(),
                        body: JSON.stringify(body)
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.selectedOrder = json.data;
                        this.actionStatus = json.data.status;
                        this.showToast(this.t('update_status', 'Update Status') + ': ' + this.getStatusLabel(json.data.status));
                    } else {
                        this.showToast(json.message || 'Gagal update status.', 'error');
                    }
                } catch (e) {
                    this.showToast('Gagal update status.', 'error');
                } finally {
                    this.actionLoading = false;
                }
            },

            async doAssignShipment() {
                if (!this.selectedOrder || !this.actionShipmentId) return;
                this.actionLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/orders/' + this.selectedOrder.id + '/assign-shipment', {
                        method: 'POST',
                        headers: this.getHeaders(),
                        body: JSON.stringify({ shipment_id: this.actionShipmentId })
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.selectedOrder = json.data;
                        this.showToast(this.t('assign_shipment', 'Assign ke Shipment') + ' berhasil.');
                    } else {
                        this.showToast(json.message || 'Gagal assign shipment.', 'error');
                    }
                } catch (e) {
                    this.showToast('Gagal assign shipment.', 'error');
                } finally {
                    this.actionLoading = false;
                }
            },

            async doCreateInvoice() {
                if (!this.selectedOrder || !this.actionInvoiceAmount) return;
                this.actionLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/orders/' + this.selectedOrder.id + '/invoices', {
                        method: 'POST',
                        headers: this.getHeaders(),
                        body: JSON.stringify({
                            amount: this.actionInvoiceAmount,
                            description: this.actionInvoiceDesc || 'Invoice tambahan',
                            type: 'additional'
                        })
                    });
                    const json = await res.json();
                    if (json.success) {
                        // Reload order detail
                        await this.openOrderDetail(this.selectedOrder.id);
                        this.showToast(this.t('create_additional_invoice', 'Terbitkan Invoice') + ' berhasil.');
                    } else {
                        this.showToast(json.message || 'Gagal buat invoice.', 'error');
                    }
                } catch (e) {
                    this.showToast('Gagal buat invoice.', 'error');
                } finally {
                    this.actionLoading = false;
                }
            },

            // =================== PRODUCTS ===================
            async loadProducts() {
                this.productsLoading = true;
                try {
                    let url = '/api/v1/admin/products?per_page=20&page=' + this.productsPage;
                    if (this.productSearch) url += '&q=' + encodeURIComponent(this.productSearch);
                    if (this.productFilterBrand) url += '&brand_id=' + this.productFilterBrand;
                    if (this.productFilterCategory) url += '&category_id=' + this.productFilterCategory;
                    if (this.productFilterActive !== '') url += '&is_active=' + this.productFilterActive;
                    const res = await fetch(url, { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) {
                        this.products = json.data.data || [];
                        this.productsMeta = json.data.meta || {};
                    }
                } catch (e) { console.error('Products error:', e); }
                finally { this.productsLoading = false; }
            },

            async openProductForm(id) {
                this.activeView = 'product-form';
                // Muat brand & kategori untuk dropdown form.
                this.loadCatalog('brands');
                this.loadCatalog('categories');
                if (!id) {
                    this.productForm = { id: null, name: '', sku: '', slug: '', price: 0, discount_price: null, stock: 0, low_stock_threshold: 0, availability_type: 'ready_stock', brand_id: '', category_id: '', description: '', is_active: true, images: [] };
                    return;
                }
                try {
                    const res = await fetch('/api/v1/admin/products/' + id, { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) {
                        const d = json.data;
                        this.productForm = {
                            id: d.id, name: d.name, sku: d.sku || '', slug: d.slug || '',
                            price: d.price || 0, discount_price: d.discount_price ?? null,
                            stock: d.stock || 0, low_stock_threshold: d.low_stock_threshold || 0,
                            availability_type: d.availability_type || 'ready_stock',
                            brand_id: d.brand?.id || '', category_id: d.category?.id || '',
                            description: d.description || '', is_active: !!d.is_active,
                            images: (d.images || []).map(i => i.path),
                        };
                    }
                } catch (e) { console.error('Product form error:', e); }
            },

            async saveProduct() {
                this.productFormLoading = true;
                try {
                    const body = {
                        name: this.productForm.name, sku: this.productForm.sku || null,
                        price: this.productForm.price, discount_price: this.productForm.discount_price || null,
                        stock: this.productForm.stock, low_stock_threshold: this.productForm.low_stock_threshold,
                        availability_type: this.productForm.availability_type,
                        brand_id: this.productForm.brand_id || null, category_id: this.productForm.category_id || null,
                        description: this.productForm.description || null, is_active: this.productForm.is_active,
                    };
                    const url = this.productForm.id ? '/api/v1/admin/products/' + this.productForm.id : '/api/v1/admin/products';
                    const method = this.productForm.id ? 'PUT' : 'POST';
                    const res = await fetch(url, { method, headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    if (json.success) {
                        this.showToast(this.productForm.id ? 'Produk diperbarui.' : 'Produk dibuat.');
                        this.goTo('products');
                    } else { this.showToast(json.message || 'Gagal simpan produk.', 'error'); }
                } catch (e) { this.showToast('Gagal simpan produk.', 'error'); }
                finally { this.productFormLoading = false; }
            },

            async deleteProduct(id) {
                if (!confirm('Hapus produk ini?')) return;
                try {
                    const res = await fetch('/api/v1/admin/products/' + id, { method: 'DELETE', headers: this.getHeaders() });
                    const json = await res.json();
                    this.showToast(json.success ? 'Produk dihapus.' : (json.message || 'Gagal hapus.'), json.success ? 'success' : 'error');
                    if (json.success) this.loadProducts();
                } catch (e) { this.showToast('Gagal hapus produk.', 'error'); }
            },

            searchProducts() { this.productsPage = 1; this.loadProducts(); },

            async doImportProducts() {
                if (!this.productImportJson.trim()) { this.showToast('Isi JSON dulu.', 'error'); return; }
                this.importLoading = true;
                try {
                    let items;
                    try { items = JSON.parse(this.productImportJson); } catch (e) { this.showToast('JSON tidak valid.', 'error'); this.importLoading = false; return; }
                    if (!Array.isArray(items)) { this.showToast('Harus berupa array.', 'error'); this.importLoading = false; return; }
                    const res = await fetch('/api/v1/admin/products/import', { method: 'POST', headers: this.getHeaders(), body: JSON.stringify({ items }) });
                    const json = await res.json();
                    this.showToast(json.success ? ('Impor ' + (json.data?.imported_count ?? 0) + ' produk.') : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.productImportJson = ''; this.loadProducts(); this.goTo('products'); }
                } catch (e) { this.showToast('Gagal import.', 'error'); }
                finally { this.importLoading = false; }
            },

            // =================== BRANDS / CATEGORIES / BANNERS ===================
            async loadCatalog(kind) {
                this.catalogLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/' + kind, { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) {
                        if (kind === 'brands') this.brands = json.data.data || json.data || [];
                        if (kind === 'categories') this.categories = json.data.data || json.data || [];
                        if (kind === 'banners') this.banners = json.data.data || json.data || [];
                    }
                } catch (e) { console.error('Catalog error:', e); }
                finally { this.catalogLoading = false; }
            },

            async saveBrand() {
                const body = { name: this.brandForm.name, slug: this.brandForm.slug || null, logo_path: this.brandForm.logo_path || null, is_active: this.brandForm.is_active };
                const url = this.brandForm.id ? '/api/v1/admin/brands/' + this.brandForm.id : '/api/v1/admin/brands';
                const method = this.brandForm.id ? 'PUT' : 'POST';
                try {
                    const res = await fetch(url, { method, headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    this.showToast(json.success ? 'Brand tersimpan.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.brandForm = { id: null, name: '', slug: '', logo_path: '', is_active: true }; this.loadCatalog('brands'); }
                } catch (e) { this.showToast('Gagal simpan brand.', 'error'); }
            },

            async saveCategory() {
                const body = { name: this.categoryForm.name, slug: this.categoryForm.slug || null, image_path: this.categoryForm.image_path || null, is_active: this.categoryForm.is_active };
                const url = this.categoryForm.id ? '/api/v1/admin/categories/' + this.categoryForm.id : '/api/v1/admin/categories';
                const method = this.categoryForm.id ? 'PUT' : 'POST';
                try {
                    const res = await fetch(url, { method, headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    this.showToast(json.success ? 'Kategori tersimpan.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.categoryForm = { id: null, name: '', slug: '', image_path: '', is_active: true }; this.loadCatalog('categories'); }
                } catch (e) { this.showToast('Gagal simpan kategori.', 'error'); }
            },

            async saveBanner() {
                const body = { title: this.bannerForm.title, image_path: this.bannerForm.image_path || null, link_url: this.bannerForm.link_url || null, cta_text: this.bannerForm.cta_text || null, order: this.bannerForm.order, is_active: this.bannerForm.is_active, locale: this.bannerForm.locale || 'id', country_filter: this.bannerForm.country_filter || 'all' };
                const url = this.bannerForm.id ? '/api/v1/admin/banners/' + this.bannerForm.id : '/api/v1/admin/banners';
                const method = this.bannerForm.id ? 'PUT' : 'POST';
                try {
                    const res = await fetch(url, { method, headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    this.showToast(json.success ? 'Banner tersimpan.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.bannerForm = { id: null, title: '', image_path: '', link_url: '', cta_text: '', order: 0, is_active: true, locale: 'id', country_filter: 'all' }; this.loadCatalog('banners'); }
                } catch (e) { this.showToast('Gagal simpan banner.', 'error'); }
            },

            async deleteCatalog(kind, id) {
                if (!confirm('Hapus item ini?')) return;
                try {
                    const res = await fetch('/api/v1/admin/' + kind + '/' + id, { method: 'DELETE', headers: this.getHeaders() });
                    const json = await res.json();
                    this.showToast(json.success ? 'Dihapus.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) this.loadCatalog(kind);
                } catch (e) { this.showToast('Gagal hapus.', 'error'); }
            },

            catalogAddLabel() {
                if (this.activeView === 'brands') return this.t('add_brand', '+ Brand');
                if (this.activeView === 'categories') return this.t('add_category', '+ Kategori');
                return this.t('add_banner', '+ Banner');
            },

            catalogAddNew() {
                if (this.activeView === 'brands') this.catalogEdit({ id: null, name: '', slug: '', logo_path: '', is_active: true }, 'brand');
                else if (this.activeView === 'categories') this.catalogEdit({ id: null, name: '', slug: '', image_path: '', is_active: true }, 'category');
                else this.catalogEdit({ id: null, title: '', image_path: '', link_url: '', cta_text: '', order: 0, is_active: true, locale: 'id', country_filter: 'all' }, 'banner');
            },

            // Form inline sederhana untuk katalog (prompt-based, pragmatis utk Fase 6).
            catalogEdit(item, kind) {
                if (kind === 'brand') {
                    const name = prompt('Nama brand', item.name || ''); if (name === null) return;
                    this.brandForm = { id: item.id || null, name: name.trim(), slug: item.slug || '', logo_path: item.logo_path || '', is_active: item.is_active !== false };
                    if (!this.brandForm.slug) this.brandForm.slug = name.toLowerCase().replace(/\s+/g, '-');
                    this.saveBrand();
                } else if (kind === 'category') {
                    const name = prompt('Nama kategori', item.name || ''); if (name === null) return;
                    this.categoryForm = { id: item.id || null, name: name.trim(), slug: item.slug || '', image_path: item.image_path || '', is_active: item.is_active !== false };
                    if (!this.categoryForm.slug) this.categoryForm.slug = name.toLowerCase().replace(/\s+/g, '-');
                    this.saveCategory();
                } else if (kind === 'banner') {
                    const title = prompt('Judul banner', item.title || ''); if (title === null) return;
                    const link = prompt('Link URL', item.link_url || '');
                    const cta = prompt('CTA text', item.cta_text || '');
                    this.bannerForm = { id: item.id || null, title: title.trim(), image_path: item.image_path || '', link_url: link || '', cta_text: cta || '', order: item.order || 0, is_active: item.is_active !== false, locale: item.locale || 'id', country_filter: item.country_filter || 'all' };
                    this.saveBanner();
                }
            },

            // =================== SHIPMENTS ===================
            async loadShipments() {
                this.shipmentsLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/shipments', { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) this.shipments = json.data.data || json.data || [];
                } catch (e) { console.error('Shipments error:', e); }
                finally { this.shipmentsLoading = false; }
            },

            async saveShipment() {
                const body = { shipment_number: this.shipmentForm.shipment_number || null, trip_id: this.shipmentForm.trip_id || null, origin_country: this.shipmentForm.origin_country, destination_country: this.shipmentForm.destination_country, bagasian_reference: this.shipmentForm.bagasian_reference || null, packing_estimate_weight: this.shipmentForm.packing_estimate_weight || null, packing_estimate_volume: this.shipmentForm.packing_estimate_volume || null, packing_estimate_cost: this.shipmentForm.packing_estimate_cost || null, notes: this.shipmentForm.notes || null };
                const url = this.shipmentForm.id ? '/api/v1/admin/shipments/' + this.shipmentForm.id : '/api/v1/admin/shipments';
                const method = this.shipmentForm.id ? 'PUT' : 'POST';
                try {
                    const res = await fetch(url, { method, headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    this.showToast(json.success ? 'Shipment tersimpan.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.shipmentForm = { id: null, shipment_number: '', trip_id: '', origin_country: 'ID', destination_country: 'JP', bagasian_reference: '', packing_estimate_weight: '', packing_estimate_volume: '', packing_estimate_cost: 0, notes: '' }; this.goTo('shipments'); }
                } catch (e) { this.showToast('Gagal simpan shipment.', 'error'); }
            },

            async sendBagasian(id) {
                try {
                    const res = await fetch('/api/v1/admin/shipments/' + id + '/send-bagasian', { method: 'POST', headers: this.getHeaders() });
                    const json = await res.json();
                    this.showToast(json.success ? 'Bagasian terkirim.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    this.loadShipments();
                } catch (e) { this.showToast('Gagal kirim bagasian.', 'error'); }
            },

            // =================== TRIPS ===================
            async loadTrips() {
                this.tripsLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/trips', { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) this.trips = json.data.data || json.data || [];
                } catch (e) { console.error('Trips error:', e); }
                finally { this.tripsLoading = false; }
            },

            async saveTrip() {
                const body = { code: this.tripForm.code, origin_country: this.tripForm.origin_country, destination_country: this.tripForm.destination_country, departure_at: this.tripForm.departure_at || null, arrival_at: this.tripForm.arrival_at || null, cutoff_at: this.tripForm.cutoff_at || null, status: this.tripForm.status || 'draft', notes: this.tripForm.notes || null };
                const url = this.tripForm.id ? '/api/v1/admin/trips/' + this.tripForm.id : '/api/v1/admin/trips';
                const method = this.tripForm.id ? 'PUT' : 'POST';
                try {
                    const res = await fetch(url, { method, headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    this.showToast(json.success ? 'Trip tersimpan.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.tripForm = { id: null, code: '', origin_country: 'ID', destination_country: 'JP', departure_at: '', arrival_at: '', cutoff_at: '', status: 'draft', notes: '' }; this.goTo('trips'); }
                } catch (e) { this.showToast('Gagal simpan trip.', 'error'); }
            },

            async editTrip(t) { this.tripForm = { id: t.id, code: t.code || '', origin_country: t.origin_country || 'ID', destination_country: t.destination_country || 'JP', departure_at: t.departure_at || '', arrival_at: t.arrival_at || '', cutoff_at: t.cutoff_at || '', status: t.status || 'draft', notes: t.notes || '' }; this.activeView = 'trip-form'; },
            async deleteTrip(id) { if (confirm('Hapus trip?')) { try { const res = await fetch('/api/v1/admin/trips/' + id, { method: 'DELETE', headers: this.getHeaders() }); const json = await res.json(); this.showToast(json.success ? 'Trip dihapus.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error'); if (json.success) this.loadTrips(); } catch (e) { this.showToast('Gagal hapus trip.', 'error'); } } },

            // =================== REFUNDS ===================
            async loadRefunds() {
                this.refundsLoading = true;
                try {
                    const res = await fetch('/api/v1/admin/refunds?per_page=20', { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) { this.refunds = json.data.data || []; this.refundsMeta = json.data.meta || {}; }
                } catch (e) { console.error('Refunds error:', e); }
                finally { this.refundsLoading = false; }
            },

            async createRefund() {
                try {
                    const res = await fetch('/api/v1/admin/refunds', { method: 'POST', headers: this.getHeaders(), body: JSON.stringify({ order_id: this.refundForm.order_id, invoice_id: this.refundForm.invoice_id || null, amount: this.refundForm.amount, reason: this.refundForm.reason, refund_method: this.refundForm.refund_method }) });
                    const json = await res.json();
                    this.showToast(json.success ? 'Refund diajukan.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) { this.refundForm = { order_id: '', invoice_id: '', amount: 0, reason: '', refund_method: 'bank_transfer' }; this.loadRefunds(); }
                } catch (e) { this.showToast('Gagal buat refund.', 'error'); }
            },

            async decideRefund(id, action) {
                try {
                    const res = await fetch('/api/v1/admin/refunds/' + id + '/' + action, { method: 'POST', headers: this.getHeaders() });
                    const json = await res.json();
                    this.showToast(json.success ? 'Refund ' + action + '.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    this.loadRefunds();
                } catch (e) { this.showToast('Gagal proses refund.', 'error'); }
            },

            // =================== USERS ===================
            async loadUsers() {
                this.usersLoading = true;
                try {
                    let url = '/api/v1/admin/users?per_page=20&page=' + this.usersPage;
                    if (this.userSearch) url += '&q=' + encodeURIComponent(this.userSearch);
                    const res = await fetch(url, { headers: this.getHeaders() });
                    const json = await res.json();
                    if (json.success) { this.users = json.data.data || []; this.usersMeta = json.data.meta || {}; }
                } catch (e) { console.error('Users error:', e); }
                finally { this.usersLoading = false; }
            },

            searchUsers() { this.usersPage = 1; this.loadUsers(); },

            async saveUser() {
                const body = { name: this.userForm.name || null, phone: this.userForm.phone || null, email: this.userForm.email || null };
                try {
                    const res = await fetch('/api/v1/admin/users/' + this.userForm.id, { method: 'PUT', headers: this.getHeaders(), body: JSON.stringify(body) });
                    const json = await res.json();
                    this.showToast(json.success ? 'User diperbarui.' : (json.message || 'Gagal.'), json.success ? 'success' : 'error');
                    if (json.success) this.loadUsers();
                } catch (e) { this.showToast('Gagal update user.', 'error'); }
            },

            async editUser(u) { this.userForm = { id: u.id, name: u.name || '', phone: u.phone || '', email: u.email || '', role: u.role || 'user' }; this.activeView = 'user-form'; },

            // =================== HELPERS ===================
            getHeaders() {
                return {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + this.adminToken
                };
            },

            t(key, fallback = '') {
                const dict = this.translations?.[this.currentLang];
                return dict?.[key] || fallback || key;
            },

            toggleLang() {
                this.currentLang = this.currentLang === 'id' ? 'en' : 'id';
                localStorage.setItem('galaksian_admin_lang', this.currentLang);
                this.updateStatusLabels();
            },

            showToast(msg, type = 'success') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3000);
            },

            formatRupiah(num) {
                if (num === null || num === undefined) return 'Rp 0';
                return 'Rp ' + Number(num).toLocaleString('id-ID');
            },

            formatDate(dateStr) {
                if (!dateStr) return '-';
                const d = new Date(dateStr);
                return d.toLocaleDateString(this.currentLang === 'id' ? 'id-ID' : 'en-US', {
                    day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
                });
            },

            getStatusLabel(status) {
                return this.t('status_' + status, status);
            },

            getStatusColor(status) {
                const map = {
                    'draft': 'bg-zinc-100 text-zinc-600',
                    'pending_payment_product': 'bg-amber-100 text-amber-700',
                    'paid_product': 'bg-blue-100 text-blue-700',
                    'processing': 'bg-indigo-100 text-indigo-700',
                    'packing': 'bg-violet-100 text-violet-700',
                    'ready_for_delivery': 'bg-cyan-100 text-cyan-700',
                    'pending_payment_shipping': 'bg-orange-100 text-orange-700',
                    'shipping_paid': 'bg-teal-100 text-teal-700',
                    'delivering': 'bg-sky-100 text-sky-700',
                    'completed': 'bg-green-100 text-green-700',
                    'cancelled': 'bg-red-100 text-red-600',
                    'refund_requested': 'bg-rose-100 text-rose-700',
                    'refunded': 'bg-pink-100 text-pink-700',
                };
                return map[status] || 'bg-zinc-100 text-zinc-600';
            },
        };
    }
</script>
