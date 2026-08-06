<?php

namespace App\Livewire\Student;

use App\Exceptions\GamificationException;
use App\Models\StoreItem;
use App\Services\GamificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Store extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.store');
    }

    public Collection $storeItems;

    public array $ownedItemIds = [];

    public function mount(): void
    {
        $this->refreshStore();
    }

    private function refreshStore(): void
    {
        $user = Auth::user();
        $this->storeItems = StoreItem::where('is_active', true)->get();
        $this->ownedItemIds = $user->storeItems()->pluck('store_items.id')->toArray();
    }

    public function purchase(int $itemId, GamificationService $gamificationService)
    {
        $user = Auth::user();
        $item = StoreItem::findOrFail($itemId);

        try {
            $gamificationService->purchaseItem($user, $item);
            $this->refreshStore();
            $this->dispatch('notify', message: 'ซื้อไอเทมเรียบร้อยแล้ว! ไปที่คลังเก็บของเพื่อสวมใส่', type: 'success');
        } catch (GamificationException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.student.store', [
            'storeItems' => $this->storeItems,
            'ownedItemIds' => $this->ownedItemIds,
            'coins' => $user->coins,
        ]);
    }
}
