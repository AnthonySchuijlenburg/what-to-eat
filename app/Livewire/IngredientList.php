<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class IngredientList extends Component
{
    public $items = [];

    public $item = '';

    public function mount(): void
    {
        $this->items = request('ingredients') ?? [];
    }

    public function addItem(): void
    {
        if (! empty($this->item)) {
            $this->items[] = $this->item;
            $this->item = '';
        }
    }

    public function removeItem($index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function render(): View
    {
        return view('livewire.ingredient-list');
    }
}
