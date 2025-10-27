<?php
namespace App\Services;

use App\Models\Item;

class InventoryService
{
    // update stok absolute (set)
    public function setStock(int $itemId, float $value): Item
    {
        $item = Item::findOrFail($itemId);
        $item->stock_item = $value;
        $item->save();
        return $item;
    }

    // increment stok (dipakai saat penerimaan approved)
    public function incrementStock(int $itemId, float $amount): Item
    {
        $item = Item::findOrFail($itemId);
        $item->stock_item = $item->stock_item + $amount;
        $item->save();
        return $item;
    }

    // cek ketersediaan stok
    public function hasStock(int $itemId, float $need): bool
    {
        $item = Item::findOrFail($itemId);
        return $item->stock_item >= $need;
    }
}
