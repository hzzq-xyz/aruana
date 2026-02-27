<div class="p-4 flex justify-center bg-gray-100 rounded-lg">
    {{-- Usando o mockup_css e mockup_image que estão na sua tabela de inventários --}}
    <div style="position: relative; {{ $inventario->mockup_css }}">
        <img src="{{ asset('storage/' . $inventario->mockup_image) }}" style="width: 100%; height: auto; display: block;">
        
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden;">
            @php
                $extension = pathinfo($record->file_path, PATHINFO_EXTENSION);
            @endphp

            @if(in_array(strtolower($extension), ['mp4', 'webm', 'mov']))
                <video autoplay loop muted playsinline style="width: 100%; height: 100%; object-fit: cover;">
                    <source src="{{ asset('storage/' . $record->file_path) }}" type="video/{{ $extension }}">
                </video>
            @else
                <img src="{{ asset('storage/' . $record->file_path) }}" style="width: 100%; height: 100%; object-fit: cover;">
            @endif
        </div>
    </div>
</div>