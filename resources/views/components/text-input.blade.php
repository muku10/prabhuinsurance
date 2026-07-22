@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 hover:border-prabhu-red-500 focus:border-prabhu-red-500 focus:ring-prabhu-red-500 rounded-md shadow-sm']) }}>
