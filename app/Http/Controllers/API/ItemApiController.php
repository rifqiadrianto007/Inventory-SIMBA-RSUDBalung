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

    // ✅ Ambil semua item
    public function index()
    {
        $items = Item::with(['category', 'satuan'])->get();
        return response()->json($items, 200);
    }

    // ✅ Ambil detail item
    public function show($id)
    {
        $item = Item::with(['category', 'satuan'])->find($id);
        if (!$item) {
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        }
        return response()->json($item, 200);
    }

    // ✅ Update stok item
    public function updateStock(Request $request, $id)
    {
        $request->validate(['stock_item' => 'required|numeric|min:0']);
        $item = $this->inventory->updateStock($id, $request->stock_item);

        if (!$item) {
            return response()->json(['error' => 'Gagal memperbarui stok'], 400);
        }

        return response()->json([
            'message' => 'Stok item berhasil diperbarui',
            'data' => $item
        ], 200);
    }
}
