<?php
namespace App\Services;

use App\Models\Item;

class InventoryService
{
    /**
    * Set stock item ke nilai tertentu secara aman (transaction + lock).
    *
    * @param int $itemId
    * @param float|int $newStock
    * @return \App\Models\Item
    * @throws \RuntimeException
    */
    public function updateStock(int $itemId, float $newStock)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($itemId, $newStock) {
            $item = \App\Models\Item::where('id_item', $itemId)->lockForUpdate()->firstOrFail();

            if ($newStock < 0) {
                throw new \RuntimeException("Nilai stok tidak boleh negatif.");
            }

            $item->stock_item = $newStock;
            $item->save();

            // opsional: catat log atau notifikasi kecil
            return $item->fresh();
        });
    }

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
