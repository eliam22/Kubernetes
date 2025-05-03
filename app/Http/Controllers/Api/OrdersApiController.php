<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;

class OrdersApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['customer','products'])->where('archived', false)->get();
        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order = Order::create($validated);

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return response()->json($order);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update($validated);

        return response()->json($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(null, 204);
    }

    /**
     * Search orders by various criteria
     */
    public function search(Request $request)
    {
        \Log::info('Search parameters:', $request->all());

        try {
            $query = Order::with(['customer', 'products']);
    
            if ($request->has('invoice_number')) {
                $query->where('invoice_number', $request->invoice_number);
            }
    
            if ($request->has('customer_name')) {
                $query->whereHas('customer', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->customer_name . '%');
                });
            }
    
            $order = $query->first();
    
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }
    
            \Log::info('Found order:', ['id' => $order->id]);
            return response()->json($order);
        } catch (\Exception $e) {
            \Log::error('Search error: ' . $e->getMessage());
            return response()->json(['error' => 'Error searching order'], 500);
        }
    }
}