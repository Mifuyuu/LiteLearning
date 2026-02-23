<?php

namespace App\Livewire\Student;

use App\Models\StoreItem;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Store extends Component
{
    public function purchase(int $itemId, GamificationService $gamificationService)
    {
        $user = Auth::user();
        $item = StoreItem::findOrFail($itemId);

        try {
            $gamificationService->purchaseItem($user, $item);
            $this->dispatch('notify', message: __('Item purchased successfully!'), type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: __($e->getMessage()), type: 'error');
        }
    }

    public function equip(int $itemId, GamificationService $gamificationService)
    {
        $user = Auth::user();
        $item = StoreItem::findOrFail($itemId);

        try {
            $gamificationService->equipItem($user, $item);
            $this->dispatch('notify', message: __('Item equipped!'), type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: __($e->getMessage()), type: 'error');
        }
    }

    public function render()
    {
        $user = Auth::user();
        $storeItems = StoreItem::where('is_active', true)->get();
        $ownedItemIds = $user->storeItems()->pluck('store_items.id')->toArray();
        
        return view('livewire.student.store', [
            'storeItems' => $storeItems,
            'ownedItemIds' => $ownedItemIds,
            'activeNameColor' => $user->active_name_color,
            'activeAvatarFrame' => $user->active_avatar_frame,
            'coins' => $user->coins,
        ]);
    }
}
