<?php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Services\InventoryService;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */

class ItemController extends Controller
{
    protected InventoryService $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
        $this->middleware('auth');
    }

    public function index()
    {
        return response()->json(Item::with('category','satuan')->get());
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate(['stock_item' => 'required|numeric']);
        $item = $this->inventory->setStock($id, $request->stock_item);
        return response()->json($item);
    }
}
