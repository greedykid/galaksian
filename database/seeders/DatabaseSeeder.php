<?php

namespace Database\Seeders;

use App\Enums\ProductAvailability;
use App\Enums\TripStatus;
use App\Enums\UserRole;
use App\Enums\VoucherScope;
use App\Enums\VoucherType;
use App\Models\Address;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Setting;
use App\Models\Trip;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        Setting::set('admin_whatsapp_number', '6281200000001');
        Setting::set('cs_whatsapp_number', '6281200000002');
        Setting::set('handling_fee_default', 5000);
        Setting::set('shipping_insurance_amount', 2000);
        Setting::set('new_user_discount_enabled', '1');
        Setting::set('new_user_discount_amount', 10000);
        Setting::set('invoice_expiry_minutes', 1440);
        Setting::set('shipping_estimate_text', 'Estimasi 7-14 hari kerja setelah jadwal trip berakhir.');

        // 2. Admin User
        $adminPassword = env('ADMIN_PASSWORD');
        $admin = User::firstOrCreate(
            ['phone' => '6281200000001'],
            [
                'name' => 'Galaksian Admin',
                'email' => 'admin@galaksian.com',
                'password' => Hash::make($adminPassword ?: 'password'),
                'role' => UserRole::ADMIN,
                'language' => 'id',
                'is_new_user' => false,
                // Paksa ganti password jika memakai password default (env tidak diset).
                'must_change_password' => empty($adminPassword),
            ]
        );

        // 3. Demo User
        $demoPassword = env('DEMO_PASSWORD', 'password');
        $user = User::firstOrCreate(
            ['phone' => '6281234567890'],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'password' => Hash::make($demoPassword),
                'role' => UserRole::USER,
                'language' => 'id',
                'is_new_user' => true,
            ]
        );

        // Demo Address
        Address::firstOrCreate(
            ['user_id' => $user->id, 'recipient_name' => 'Budi Santoso'],
            [
                'phone' => '6281234567890',
                'label' => 'Rumah',
                'address' => 'Jl. Sudirman No. 45, Kebayoran Baru',
                'delivery_note' => 'leave_at_front_door',
                'is_default' => true,
                'country' => 'ID',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postal_code' => '12190',
            ]
        );

        // 4. Trips
        $activeTrip = Trip::firstOrCreate(
            ['code' => 'TRIP-ID-JP-2026-09'],
            [
                'origin_country' => 'ID',
                'destination_country' => 'JP',
                'departure_at' => now()->addDays(10),
                'arrival_at' => now()->addDays(11),
                'cutoff_at' => now()->addDays(7),
                'status' => TripStatus::ACTIVE,
                'notes' => 'Trip jastip reguler Jakarta - Tokyo September 2026',
            ]
        );

        Trip::firstOrCreate(
            ['code' => 'TRIP-JP-ID-2026-10'],
            [
                'origin_country' => 'JP',
                'destination_country' => 'ID',
                'departure_at' => now()->addDays(25),
                'arrival_at' => now()->addDays(26),
                'cutoff_at' => now()->addDays(20),
                'status' => TripStatus::DRAFT,
                'notes' => 'Trip Tokyo - Jakarta Oktober 2026',
            ]
        );

        // 5. Brands
        $brandsData = [
            ['name' => 'Indofood', 'slug' => 'indofood', 'logo_path' => '/storage/brands/indofood.png'],
            ['name' => 'Dua Kelinci', 'slug' => 'dua-kelinci', 'logo_path' => '/storage/brands/dua-kelinci.png'],
            ['name' => 'Nestle', 'slug' => 'nestle', 'logo_path' => '/storage/brands/nestle.png'],
            ['name' => 'Garuda Food', 'slug' => 'garuda-food', 'logo_path' => '/storage/brands/garuda-food.png'],
            ['name' => 'Mayora', 'slug' => 'mayora', 'logo_path' => '/storage/brands/mayora.png'],
            ['name' => 'ABC', 'slug' => 'abc', 'logo_path' => '/storage/brands/abc.png'],
            ['name' => 'Meiji', 'slug' => 'meiji', 'logo_path' => '/storage/brands/meiji.png'],
            ['name' => 'Calbee', 'slug' => 'calbee', 'logo_path' => '/storage/brands/calbee.png'],
            ['name' => 'Shiseido', 'slug' => 'shiseido', 'logo_path' => '/storage/brands/shiseido.png'],
            ['name' => 'Sony', 'slug' => 'sony', 'logo_path' => '/storage/brands/sony.png'],
            ['name' => 'Rohto', 'slug' => 'rohto', 'logo_path' => '/storage/brands/rohto.png'],
            ['name' => 'Nintendo', 'slug' => 'nintendo', 'logo_path' => '/storage/brands/nintendo.png'],
            ['name' => 'Pixy', 'slug' => 'pixy', 'logo_path' => '/storage/brands/pixy.png'],
            ['name' => 'Bourbon', 'slug' => 'bourbon', 'logo_path' => '/storage/brands/bourbon.png'],
            ['name' => 'Kao', 'slug' => 'kao', 'logo_path' => '/storage/brands/kao.png'],
            ['name' => 'Mandom', 'slug' => 'mandom', 'logo_path' => '/storage/brands/mandom.png'],
            ['name' => 'Kose', 'slug' => 'kose', 'logo_path' => '/storage/brands/kose.png'],
            ['name' => 'Suntory', 'slug' => 'suntory', 'logo_path' => '/storage/brands/suntory.png'],
            ['name' => 'Morinaga', 'slug' => 'morinaga', 'logo_path' => '/storage/brands/morinaga.png'],
            ['name' => 'DHC', 'slug' => 'dhc', 'logo_path' => '/storage/brands/dhc.png'],
            ['name' => 'Curel', 'slug' => 'curel', 'logo_path' => '/storage/brands/curel.png'],
            ['name' => 'Fancl', 'slug' => 'fancl', 'logo_path' => '/storage/brands/fancl.png'],
            ['name' => 'Sido Muncul', 'slug' => 'sido-muncul', 'logo_path' => '/storage/brands/sido-muncul.png'],

        ];

        $brandModels = [];
        foreach ($brandsData as $b) {
            $brandModels[$b['slug']] = Brand::firstOrCreate(['slug' => $b['slug']], $b);
        }

        // 6. Categories
        $categoriesData = [
            ['name' => 'Makanan Instan', 'slug' => 'makanan-instan', 'image_path' => '/storage/categories/instant-food.png'],
            ['name' => 'Bumbu Dapur', 'slug' => 'bumbu-dapur', 'image_path' => '/storage/categories/seasoning.png'],
            ['name' => 'Minuman', 'slug' => 'minuman', 'image_path' => '/storage/categories/beverages.png'],
            ['name' => 'Bahan Masakan', 'slug' => 'bahan-masakan', 'image_path' => '/storage/categories/cooking-ingredients.png'],
            ['name' => 'Snack & Cemilan', 'slug' => 'snack-cemilan', 'image_path' => '/storage/categories/snacks.png'],
            ['name' => 'Skincare & Beauty', 'slug' => 'skincare-beauty', 'image_path' => '/storage/categories/skincare.png'],
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'image_path' => '/storage/categories/elektronik.png'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'image_path' => '/storage/categories/fashion.png'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'image_path' => '/storage/categories/kesehatan.png'],

        ];

        $categoryModels = [];
        foreach ($categoriesData as $c) {
            $categoryModels[$c['slug']] = Category::firstOrCreate(['slug' => $c['slug']], $c);
        }

        // 7. Banners
        Banner::firstOrCreate(['image_path' => '/storage/banners/banner1.jpg'], [
            'title' => 'Jastip Terpercaya Indonesia - Jepang',
            'link_url' => '/products',
            'cta_text' => 'Belanja Sekarang',
            'order' => 1,
            'is_active' => true,
            'locale' => 'id',
            'country_filter' => 'all',
        ]);

        Banner::firstOrCreate(['image_path' => '/storage/banners/banner2.jpg'], [
            'title' => 'Flash Sale Mingguan Kuliner Nusantara',
            'link_url' => '/products?is_flash_sale=1',
            'cta_text' => 'Lihat Promo',
            'order' => 2,
            'is_active' => true,
            'locale' => 'id',
            'country_filter' => 'ID',
        ]);

        // 8. Products
        $productsData = [
            [
                'name' => 'Indomie Goreng Original 85g (Dus 40 Pcs)',
                'slug' => 'indomie-goreng-original-dus',
                'sku' => 'IND-GOR-40',
                'description' => 'Mi instan goreng legendaris khas Indonesia. 1 dus isi 40 bungkus.',
                'brand_id' => $brandModels['indofood']->id,
                'category_id' => $categoryModels['makanan-instan']->id,
                'origin_country' => 'ID',
                'currency' => 'IDR',
                'price' => 140000,
                'discount_price' => 125000,
                'stock' => 50,
                'availability_type' => ProductAvailability::READY_STOCK,
                'is_active' => true,
                'is_flash_sale' => true,
                'flash_sale_start_at' => now()->subDay(),
                'flash_sale_end_at' => now()->addDays(3),
                'weight_gram' => 3800,
            ],
            [
                'name' => 'Sambal ABC Asli 335ml',
                'slug' => 'sambal-abc-asli-335ml',
                'sku' => 'ABC-SAM-335',
                'description' => 'Sambal pedas nikmat khas Indonesia dalam kemasan botol.',
                'brand_id' => $brandModels['abc']->id,
                'category_id' => $categoryModels['bumbu-dapur']->id,
                'origin_country' => 'ID',
                'currency' => 'IDR',
                'price' => 22000,
                'discount_price' => null,
                'stock' => 100,
                'availability_type' => ProductAvailability::READY_STOCK,
                'is_active' => true,
                'is_flash_sale' => false,
                'weight_gram' => 450,
            ],
            [
                'name' => 'Kacang Sukro Dua Kelinci Original 100g',
                'slug' => 'kacang-sukro-dua-kelinci-100g',
                'sku' => 'DK-SUK-100',
                'description' => 'Kacang bersalut gurih dan renyah cocok untuk teman santai.',
                'brand_id' => $brandModels['dua-kelinci']->id,
                'category_id' => $categoryModels['snack-cemilan']->id,
                'origin_country' => 'ID',
                'currency' => 'IDR',
                'price' => 11000,
                'discount_price' => 9500,
                'stock' => 80,
                'availability_type' => ProductAvailability::READY_STOCK,
                'is_active' => true,
                'is_flash_sale' => true,
                'flash_sale_start_at' => now()->subDay(),
                'flash_sale_end_at' => now()->addDays(2),
                'weight_gram' => 120,
            ],
            [
                'name' => 'Bumbu Rendang Indofood 45g',
                'slug' => 'bumbu-rendang-indofood-45g',
                'sku' => 'IND-REN-45',
                'description' => 'Bumbu racik instan untuk membuat rendang daging khas Padang.',
                'brand_id' => $brandModels['indofood']->id,
                'category_id' => $categoryModels['bumbu-dapur']->id,
                'origin_country' => 'ID',
                'currency' => 'IDR',
                'price' => 8500,
                'discount_price' => null,
                'stock' => 0,
                'availability_type' => ProductAvailability::OPEN_PO,
                'is_active' => true,
                'is_flash_sale' => false,
                'weight_gram' => 50,
            ],
            [
                'name' => 'Meiji Meltykiss Chocolate Japan 56g',
                'slug' => 'meiji-meltykiss-chocolate-japan-56g',
                'sku' => 'MEI-MEL-56',
                'description' => 'Cokelat premium asal Jepang yang meleleh lembut di lidah.',
                'brand_id' => $brandModels['meiji']->id,
                'category_id' => $categoryModels['snack-cemilan']->id,
                'origin_country' => 'JP',
                'currency' => 'IDR',
                'price' => 65000,
                'discount_price' => 58000,
                'stock' => 25,
                'availability_type' => ProductAvailability::READY_STOCK,
                'is_active' => true,
                'is_flash_sale' => false,
                'weight_gram' => 80,
            ],
            [
                'name' => 'Calbee Jagabee Potato Crisps Japan',
                'slug' => 'calbee-jagabee-potato-crisps-japan',
                'sku' => 'CAL-JAG-01',
                'description' => 'Keripik kentang stick gurih renyah langsung dari Tokyo.',
                'brand_id' => $brandModels['calbee']->id,
                'category_id' => $categoryModels['snack-cemilan']->id,
                'origin_country' => 'JP',
                'currency' => 'IDR',
                'price' => 45000,
                'discount_price' => null,
                'stock' => 0,
                'availability_type' => ProductAvailability::OPEN_PO,
                'is_active' => true,
                'is_flash_sale' => false,
                'weight_gram' => 100,
            ],
        ];

        foreach ($productsData as $pData) {
            $prod = Product::firstOrCreate(['slug' => $pData['slug']], $pData);
            ProductImage::firstOrCreate(
                ['product_id' => $prod->id, 'path' => "/storage/products/{$prod->slug}.jpg"],
                ['order' => 0, 'is_primary' => true]
            );
        }

        // 9. Vouchers
        Voucher::firstOrCreate(
            ['code' => 'GALAKSIAN10'],
            [
                'type' => VoucherType::PERCENT,
                'value' => 10,
                'max_discount' => 20000,
                'min_order_amount' => 0,
                'usage_limit' => 1000,
                'usage_per_user' => 2,
                'applicable_scope' => VoucherScope::PRODUCT,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addYears(2),
                'is_active' => true,
            ]
        );

        Voucher::firstOrCreate(
            ['code' => 'POTONGAN15K'],
            [
                'type' => VoucherType::FIXED,
                'value' => 15000,
                'min_order_amount' => 50000,
                'usage_limit' => 500,
                'usage_per_user' => 1,
                'applicable_scope' => VoucherScope::PRODUCT,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addYears(2),
                'is_active' => true,
            ]
        );

        Voucher::firstOrCreate(
            ['code' => 'HEMAT5'],
            [
                'type' => VoucherType::PERCENT,
                'value' => 5,
                'max_discount' => 15000,
                'min_order_amount' => 0,
                'usage_limit' => 1000,
                'usage_per_user' => 2,
                'applicable_scope' => VoucherScope::PRODUCT,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addYears(2),
                'is_active' => true,
            ]
        );

        Voucher::firstOrCreate(
            ['code' => 'NEWUSER15'],
            [
                'type' => VoucherType::FIXED,
                'value' => 15000,
                'min_order_amount' => 100000,
                'usage_limit' => 1000,
                'usage_per_user' => 1,
                'applicable_scope' => VoucherScope::PRODUCT,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addYears(2),
                'is_active' => true,
            ]
        );

        $this->call(DemoProductSeeder::class);
    }
}
