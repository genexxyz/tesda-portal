@props([
    'disabled' => false,
    'label' => null,
    'placeholder' => null,
    'error' => null,
    'required' => false,
    'options' => [],
    'valueField' => 'id',
    'textField' => 'name'
])

<div class="relative mb-3">
    @if($label)
        <label for="{{ $attributes->get('id') }}" class="block pl-1 text-sm font-semibold text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500 ml-1">*</span>
            @endif
        </label>
    @endif

    <select 
        {{ $disabled ? 'disabled' : '' }}
        {{ $required ? 'required' : '' }}
        {!! $attributes->merge([
            'class' => 'w-full text-sm pl-5 pr-10 font-medium border-2 border-gray-400 rounded-md h-10 bg-white focus:border-primary focus:ring-primary' . 
                      ($error ? ' border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500' : '')
        ]) !!}>
        
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if(is_array($options) || $options instanceof \Illuminate\Support\Collection)
            @foreach($options as $option)
                @if(is_object($option))
                    <option value="{{ $option->{$valueField} }}">{{ $option->{$textField} }}</option>
                @elseif(is_array($option))
                    <option value="{{ $option[$valueField] }}">{{ $option[$textField] }}</option>
                @else
                    <option value="{{ $option }}">{{ $option }}</option>
                @endif
            @endforeach
        @endif
    </select>

    @error($attributes->wire('model')->value())
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>