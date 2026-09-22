<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role_id', function ($q) {
                $q->select('id')->from('roles')->where('name', 'Customer');
            })->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'total_sales' => Order::sum('total') ?? 0,
            'low_stock_products' => Product::whereColumn('stock_qty', '<=', 'low_stock_qty')->count(),
            'today_orders' => Order::whereDate('created_at', $today)->count(),
        ];

        $recentOrders = Order::with(['user', 'items'])->latest()->limit(5)->get();
        $topProducts = OrderItem::groupBy('product_id')
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->with('product')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recentOrders', 'topProducts'));
    }
}
