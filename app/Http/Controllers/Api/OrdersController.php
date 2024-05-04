<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;
use App\Models\ProductOrder;
use App\Models\Product;

class OrdersController extends Controller
{
    /**
     * Request:
     * auth request
     * @param products list with qty
     */
    public function addProductToOrder(Request $request)
{
    $request->validate([
        'id' => 'required|exists:products,id',
        'qty' => 'required|integer|min:1',
    ]);

    $userId = $request->user()->id= $request->input('product');
    $qty= $request->input('quantity');

    $order = Order::firstOrCreate([
        'user_id' => $userId,
    ]);

    $order->products()->attach($id, ['quantity' => $qty]);

    return response()->json(['message' => 'Product added to the order successfully'], 200);
}


    public function store(Request $request): JsonResponse
    {
        // user - date - total: server
        // products : request

        // Validation 1
        if (empty($request->products)) {
            return response()->json(
                [
                    'message' => 'products are not exist!',
                ],
                400,
            );
        }

        // adding order products to database
        $total = 0;
        $requestProducts = $request->products;

        // get products ids
        $requestProductsIds = [];
        foreach ($requestProducts as $rP) {
            $requestProductsIds[] = $rP['id'];
        }

        // get products from DB
        $dbProducts = Product::whereIn('id', $requestProductsIds)->get();

        // Validation 2
        // check if products count is same (qty, stock ...)
        if (count($requestProducts) != count($dbProducts)) {
            return response()->json(
                [
                    'message' => 'Some of the products that you requested was not found!',
                ],
                400,
            );
        }

        foreach ($requestProducts as $rP) {
            foreach ($dbProducts as $dP) {
                if ($rP['id'] == $dP->id) {
                    $rP['object'] = $dP;
                }
            }
        }

        // create order
        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => 0,
            'date' => date('Y-m-d H:i:s'),
        ]);

        // todo: get all products from db at once
        foreach ($requestProducts as $rProduct) {

            $total += $rP['object']->price * $rProduct['qty'];

            // add the product
            ProductOrder::create([
                'product' => $rProduct['id'],
                'qty' => $rProduct['qty'],
                'order' => $order->id,
            ]);
        }

        // set the total amount
        $order->total = $total;
        $order->save();

        return response()->json([
            'message' => 'Order has been created successfully',
            'order' => $order,
        ], 200);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexAdmin(Request $request): JsonResponse
    {
        // $products = ProductOrder::where('id', '>=', 0)
        //     ->with('product_object')->get();
        // return response()->json([
        //     'message' => 'Orders has been retrived successfully',
        //     'order' => $products,
        // ], 200);

        $orders = Order::with('products')->get();//where('user_id', $request->user()->id)
          //  ->

        return response()->json([
            'message' => 'Orders has been retrived successfully',
            'order' => $orders,
        ], 200);
    }
    public function indexUser(Request $request): JsonResponse
    {
        // $products = ProductOrder::where('id', '>=', 0)
        //     ->with('product_object')->get();
        // return response()->json([
        //     'message' => 'Orders has been retrived successfully',
        //     'order' => $products,
        // ], 200);

        $orders = Order::where('user_id', $request->user()->id)->with('products')->get();


        return response()->json([
            'message' => 'Orders has been retrived successfully',
            'order' => $orders,
        ], 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

     public function update(Request $request, Order $order)
    {
        // get & check order
        $order = Order::where(
            [
                ['id', $request->order],
      //          ['user_id', $request->user()->id]
            ]
        )->with('products')->first();

        // check order & products
        if (empty($order) || empty($request->products)) {
            return response()->json([
                'message' => 'Order/Products were not found'
            ], 400);
        }

        // check products in request
        foreach ($request->products as $product) {
            foreach ($order->products as  $prodOrder) {
                if ($product['id'] == $prodOrder->id) {
                    // requested Product Order Record to be updated was found =>
                    // check update Type:
                    // 1) delete
                    if ($product['qty'] == -1) {
                        $prodOrder->delete();
                    }
                    // 2) update
                    else if (
                        $product['qty'] > 0
                        && $product['qty'] !=  $prodOrder->qty
                    ) {
                        $prodOrder->qty = $product['qty'];
                        $prodOrder->save();
                    }
                }
            }
        }

        // update total
        $total = 0; // init total
        $order = $order->fresh(); // sync ram & db
        // calc the new total
        foreach ($order->products as $pO) {
            $total = $total + ($pO->qty * $pO->product_object->price);
        }
        $order->total = $total;
        $order->save();


        return response()->json([
            'message' => 'Order has been updated successfully',
            'order' => $order,
        ], 200);
    }
     
public function deleteOrder(Request $request, $orderId)
{
    // get & check order
    $order = Order::where('id', $orderId)->first();

    // check order
    if (empty($order)) {
        return response()->json([
            'message' => 'Order not found'
        ], 404);
    }

    // delete order
    $order->delete();

    return response()->json([
        'message' => 'Order has been deleted successfully',
    ], 200);
}


}

/*
// // init total
// $total = $order->total;

// check products in request
foreach ($request->products as $product) {
    foreach ($order->products as $k => $prodOrder) {
    if ($product['id'] == $prodOrder->id) {
        // check update:
        // 1) delete
        if ($product['qty'] == -1) {
            // $total -= ($prodOrder->qty * $prodOrder->product_object->price);
            $prodOrder->delete();
            // unset($order->products[$k]);
        }
    // 2) update
    else if (
        $product['qty'] > 0
        && $product['qty'] !=  $prodOrder->qty
    ) {
        // $total -= ($prodOrder->qty * $prodOrder->product_object->price);
        $prodOrder->qty = $product['qty'];
                $prodOrder->save();
                // $total += ($prodOrder->qty * $prodOrder->product_object->price);
            }
        }
    }
}
*/
