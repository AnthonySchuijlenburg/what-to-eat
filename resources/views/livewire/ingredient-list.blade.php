<div>
        <div>
            <input type="text" wire:model="item" class="border rounded px-2 py-1">
            <button type="button" wire:click="addItem" class="bg-blue-500 text-white px-3 py-1 rounded">Add</button>
        </div>

    <ul>
        @foreach ($items as $index => $listItem)
            <li class="flex justify-between bg-gray-100 p-2 my-1 rounded">
                {{ $listItem }}
                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-500">X</button>

                <!-- Hidden input to submit items as part of the form -->
                <input type="hidden" name="ingredients[]" value="{{ $listItem }}">
            </li>
        @endforeach
    </ul>
</div>
