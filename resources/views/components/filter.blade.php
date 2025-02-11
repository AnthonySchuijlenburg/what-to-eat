<div class="flex flex-col my-4">
    <h3 class="text-xl mb-2">{{$label}}</h3>
    @foreach($options as $option => $label)
        <label class="flex items-center gap-2 hover:cursor-pointer">
            <input
                @checked(!!request($id) && in_array($option, request($id)))
                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500  focus:ring-2"
                type="checkbox"
                name="{{$id.'[]'}}"
                value="{{$option}}"
            >
            {{$label}}
        </label>
    @endforeach
</div>
