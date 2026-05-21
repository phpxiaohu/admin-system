<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 获取商品列表
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->where('is_active', true);

        // 搜索功能
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 价格筛选
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 排序
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // 分页
        $perPage = $request->get('pageSize', 10);
        $paginator = $query->paginate($perPage);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'page' => $paginator->currentPage() + 1,
                'pageSize' => $paginator->perPage(),
                'total' => $paginator->total(),
                'hasMore' => $paginator->hasMorePages(),
            ],
            'message' => '获取成功',
        ]);
    }

    /**
     * 获取商品详情
     */
    public function show(Product $product): JsonResponse
    {
        if (!$product->is_active) {
            return response()->json([
                'message' => '商品已下架',
            ], 404);
        }

        return response()->json([
            'message' => '获取成功',
            'data' => $product,
        ]);
    }
}
