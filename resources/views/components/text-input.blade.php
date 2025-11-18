@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 text-black text-sm focus:border-gray-400 focus:ring-gray-300 rounded-md shadow-sm']) }}>
