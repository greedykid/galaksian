<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\ShipmentStatus;
use App\Enums\TripStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('availability_type', 'ready_stock')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->count();

        $totalOrders = Order::count();
        $pendingProductPayment = Order::where('status', OrderStatus::PENDING_PAYMENT_PRODUCT)->count();
        $readyForDelivery = Order::where('status', OrderStatus::READY_FOR_DELIVERY)->count();
        $pendingShippingPayment = Order::where('status', OrderStatus::PENDING_PAYMENT_SHIPPING)->count();
        $completedOrders = Order::where('status', OrderStatus::COMPLETED)->count();

        $totalRevenue = (int) Invoice::where('status', InvoiceStatus::PAID)->sum('amount');
        $totalShipments = Shipment::count();
        $bagasianShipments = Shipment::where('status', ShipmentStatus::SENT_TO_BAGASIAN)->count();

        $activeTrips = Trip::where('status', TripStatus::ACTIVE)->count();
        $totalUsers = User::where('role', 'user')->count();

        return $this->successResponse([
            'products' => [
                'total' => $totalProducts,
                'low_stock' => $lowStockProducts,
            ],
            'orders' => [
                'total' => $totalOrders,
                'pending_product_payment' => $pendingProductPayment,
                'ready_for_delivery' => $readyForDelivery,
                'pending_shipping_payment' => $pendingShippingPayment,
                'completed' => $completedOrders,
            ],
            'revenue' => [
                'total_paid' => $totalRevenue,
            ],
            'shipments' => [
                'total' => $totalShipments,
                'sent_to_bagasian' => $bagasianShipments,
            ],
            'trips' => [
                'active' => $activeTrips,
            ],
            'users' => [
                'total' => $totalUsers,
            ],
        ], 'Statistik dashboard admin berhasil diambil.');
    }
}
