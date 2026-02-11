<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pesanan Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b">
                            <th class="py-3 px-4">Produk</th>
                            <th class="py-3 px-4">Harga</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-4 px-4 font-bold">{{ $order->product->name }}</td>
                                <td class="py-4 px-4">Rp {{ number_format($order->product->price) }}</td>
                                <td class="py-4 px-4">
                                    @if($order->status == 'PAID')
                                        <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Lunas</span>
                                    @elseif($order->status == 'EXPIRED')
                                        <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Hangus</span>
                                    @else
                                        <span class="bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs">Menunggu Pembayaran</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-4 px-4">
                                    @if($order->status == 'PENDING')
                                        <a href="{{ $order->checkout_link }}" target="_blank" class="text-indigo-600 hover:underline font-bold">
                                            Bayar Sekarang
                                        </a>
                                    @elseif($order->status == 'PAID')
                                        <span class="text-gray-400 italic text-sm">Selesai</span>
                                    @else
                                        <form action="{{ route('checkout') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $order->product_id }}">
                                            <button type="submit" class="text-blue-600 hover:underline text-sm">Pesan Lagi</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">Belum ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>