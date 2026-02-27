<div class="space-y-4">
    <div class="bg-gray-800 text-white p-4 rounded-t-lg">
        <h3 class="font-bold">{{ $record->inventario->canal }}</h3>
        <p class="text-sm">{{ $record->inventario->cidade }} - {{ $record->inventario->endereco }}</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($record->fotos as $foto)
            <div class="relative group rounded-lg overflow-hidden shadow-lg border border-gray-200">
                <img src="{{ asset('storage/' . $foto) }}" class="w-full h-64 object-cover">
                <div class="absolute bottom-0 left-0 right-0 bg-black/50 p-2 text-white text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                    {{ $record->inventario->codigo }} - {{ $record->data_checking->format('d/m/Y') }}
                </div>
            </div>
        @endforeach
    </div>
</div>