<?php

namespace App\Utils\Traits;

use Illuminate\Http\JsonResponse;

trait CRUD
{
    public function delete($item): void
    {
        if (file_exists($item->photo)) {
            unlink(public_path($item->photo));
        }
        if ($item->photos()->exits()) {
            foreach ($item->photos as $itemPhoto) {
                if (file_exists($itemPhoto->photo)) {
                    unlink(public_path($itemPhoto->photo));
                }
            }
            $item->photos()->delete();
        }
        $item->delete();
    }
}
