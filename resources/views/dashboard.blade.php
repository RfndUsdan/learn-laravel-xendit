<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="border p-4 rounded-lg shadow-sm">
                                <h4 class="font-bold">{{ $product->name }}</h4>
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                <p>Rp {{ number_format($product->price) }}</p>
                                <form action="{{ route('checkout') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
                                        Beli
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>