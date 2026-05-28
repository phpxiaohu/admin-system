<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * 获取购物车列表
     * 获取当前用户的购物车项列表，包含商品详细信息和总价计算。
     */
    public function index(Request $request): JsonResponse
    {
        // 获取当前用户的所有购物车项并格式化数据
        $user = $request->user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_image' => $item->product->image,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'stock' => $item->product->stock,
                ];
            });
        // 计算总价
        $total = $cartItems->sum('subtotal');
        // 返回购物车数据响应
        return response()->json([
            'message' => '获取成功',
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'count' => $cartItems->count(),
            ],
        ]);
    }

    /**
     * 添加商品到购物车
     * 验证商品信息和库存后，将商品添加到用户购物车。
     * 如果购物车中已存在该商品，则累加数量；否则创建新的购物车项。
     * 使用数据库事务和悲观锁确保数据一致性。
     */
    public function store(Request $request): JsonResponse
    {
        // 验证请求参数
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();

        // 使用事务确保数据一致性
        DB::beginTransaction();
        try {
            // 使用悲观锁锁定商品记录，防止并发修改
            $product = Product::where('id', $validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 检查商品是否上架
            if (!$product->is_active) {
                DB::rollBack();
                return response()->json([
                    'message' => '商品已下架',
                ], 400);
            }

            // 检查库存
            if ($product->stock < $validated['quantity']) {
                DB::rollBack();
                return response()->json([
                    'message' => '库存不足',
                ], 400);
            }

            // 检查购物车中是否已有该商品（使用锁）
            $cartItem = CartItem::where('user_id', $user->id)
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate() // 使用锁
                ->first();

            if ($cartItem) {
                // 更新数量
                $newQuantity = $cartItem->quantity + $validated['quantity'];

                // 检查总数量是否超过库存
                if ($product->stock < $newQuantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => '库存不足',
                    ], 400);
                }

                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                // 创建新的购物车项
                $cartItem = CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $validated['product_id'],
                    'quantity' => $validated['quantity'],
                ]);
            }

            DB::commit();

            // 返回添加成功的响应
            return response()->json([
                'message' => '添加成功',
                'data' => [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                ],
            ]);

        } catch (\Exception $e) {
            // 发生异常时回滚事务
            DB::rollBack();
            return response()->json([
                'message' => '添加失败',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 405);
        }
    }

    /**
     * 更新购物车商品数量
     *
     * 验证用户权限和库存后，更新购物车项的商品数量。
     * 使用事务和悲观锁确保数据一致性。
     */
    public function update(Request $request, CartItem $cartItem): JsonResponse
    {
        $user = $request->user();

        // 验证购物车项属于当前用户
        if ($cartItem->user_id !== $user->id) {
            return response()->json([
                'message' => '无权操作',
            ], 403);
        }

        // 验证请求参数
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 使用悲观锁锁定商品记录
            $product = Product::where('id', $cartItem->product_id)
                ->lockForUpdate()
                ->firstOrFail();

            // 检查库存
            if ($product->stock < $validated['quantity']) {
                DB::rollBack();
                return response()->json([
                    'message' => '库存不足',
                ], 400);
            }

            // 更新购物车项数量并保存
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();

            DB::commit();

            // 返回更新成功的响应
            return response()->json([
                'message' => '更新成功',
                'data' => [
                    'id' => $cartItem->id,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => $cartItem->subtotal,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => '更新失败',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 405);
        }
    }

    /**
     * 删除购物车商品
     * 验证用户权限后，从购物车中删除指定的商品项。
     */
    public function destroy(Request $request, CartItem $cartItem): JsonResponse
    {
        $user = $request->user();
        // 验证购物车项属于当前用户
        if ($cartItem->user_id !== $user->id) {
            return response()->json([
                'message' => '无权操作',
            ], 403);
        }
        // 删除购物车项
        $cartItem->delete();
        // 返回删除成功的响应
        return response()->json([
            'message' => '删除成功',
        ]);
    }

    /**
     * 清空购物车
     * 删除当前用户的所有购物车项。
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        // 删除当前用户的所有购物车项
        CartItem::where('user_id', $user->id)->delete();
        // 返回清空成功的响应
        return response()->json([
            'message' => '购物车已清空',
        ]);
    }
}
