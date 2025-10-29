<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Services\InventoryService;

class ItemApiController extends Controller
{
    protected InventoryService $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index()
    {
        return response()->json(Item::with('category', 'satuan')->get());
    }

    public function show($id)
    {
        return response()->json(Item::with('category', 'satuan')->findOrFail($id));
    }

    /**
     * PUT /api/item/{id}/update-stock
     * body: { "stock_item": 10 }
     */
    public function updateStock(Request $request, $id)
    {
        $data = $request->validate([
            'stock_item' => 'required|numeric|min:0',
        ]);

        try {
            $item = $this->inventory->updateStock((int)$id, (float)$data['stock_item']);
            return response()->json([
                'ok' => true,
                'item' => $item
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 422);
        }
    }
}
