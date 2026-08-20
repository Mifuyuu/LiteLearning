<?php

namespace App\Livewire\Student;

use App\Exceptions\GamificationException;
use App\Models\Achievement;
use App\Models\StoreItem;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.app')]
class Inventory extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.inventory');
    }

    public array $ownedItems = [];

    public array $badgeItems = [];

    public ?string $activeNameColor = null;

    public ?string $activeAvatarFrame = null;

    public int $coins = 0;

    public function mount(): void
    {
        $this->loadInventory();
    }

    private function loadInventory(): void
    {
        $user = Auth::user();

        $items = $user->storeItems()->get();
        /** @var \Illuminate\Database\Eloquent\Collection $items */

        $this->ownedItems = $items->map(fn (StoreItem $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'type' => $item->type,
            'value' => $item->value,
            'is_active' => (bool) $item->pivot->is_active,
        ])->toArray();

        $achievements = $user->achievements()->get();

        $this->badgeItems = $achievements->map(fn (Achievement $achievement) => [
            'id' => $achievement->id,
            'name' => $achievement->name,
            'description' => $achievement->description,
            'badge_image' => $achievement->badge_image,
            'is_active' => (bool) $achievement->pivot->is_displayed,
        ])->toArray();

        $this->activeNameColor = $user->active_name_color;
        $this->activeAvatarFrame = $user->active_avatar_frame;
        $this->coins = $user->coins;
    }

    public function equip(int $itemId, GamificationService $gamificationService): void
    {
        $user = Auth::user();
        $item = StoreItem::findOrFail($itemId);

        try {
            $gamificationService->equipItem($user, $item);
            $this->loadInventory();
            $this->dispatch('notify', message: __('messages.store.equipped'), type: 'success');
        } catch (GamificationException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch('notify', message: __('messages.store.equip_error'), type: 'error');
        }
    }

    public function unequip(int $itemId, GamificationService $gamificationService): void
    {
        $user = Auth::user();
        $item = StoreItem::findOrFail($itemId);

        if (! $user->storeItems()->where('store_item_id', $item->id)->exists()) {
            $this->dispatch('notify', message: __('messages.store.not_owned'), type: 'error');
            return;
        }

        $user->storeItems()->updateExistingPivot($item->id, ['is_active' => false]);
        $this->loadInventory();
        $this->dispatch('notify', message: __('messages.store.unequipped'), type: 'success');
    }

    public function toggleBadge(int $achievementId, GamificationService $gamificationService): void
    {
        /** @var User $user */
        $user = Auth::user();
        $achievement = Achievement::findOrFail($achievementId);

        try {
            $gamificationService->toggleAchievementDisplay($user, $achievement);
            $this->loadInventory();
            $this->dispatch('notify', message: __('messages.store.equipped'), type: 'success');
        } catch (GamificationException $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.student.inventory', [
            'nameColorItems' => array_filter($this->ownedItems, fn ($item) => $item['type'] === 'name_color'),
            'avatarFrameItems' => array_filter($this->ownedItems, fn ($item) => $item['type'] === 'avatar_frame'),
            'displayedBadgeCount' => count(array_filter($this->badgeItems, fn ($item) => $item['is_active'])),
            'maxDisplayedBadges' => User::MAX_DISPLAYED_BADGES,
        ]);
    }
}
