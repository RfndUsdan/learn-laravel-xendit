<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $apiInstance;

    public function __construct()
    {
        // 1. Inisialisasi Konfigurasi secara Instance (Bukan Statis)
        $config = new Configuration();
        $config->setApiKey(env('XENDIT_SECRET_KEY'));

        // 2. Masukkan konfigurasi ke API Instance
        $this->apiInstance = new InvoiceApi(null, $config);
    }

    public function checkout(Request $request)
    {
        // Pastikan user sudah login (biasanya sudah ditangani middleware auth)
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $product = Product::findOrFail($request->product_id);
        $external_id = (string) Str::uuid();

        $create_invoice_request = new CreateInvoiceRequest([
            'external_id' => $external_id,
            'amount'      => (float) $product->price,
            'payer_email' => $user->email, // Jauh lebih aman
            'description' => 'Pembayaran: ' . $product->name,
            'invoice_duration' => 86400,
            'success_redirect_url' => route('dashboard'),
        ]);

        try {
            $result = $this->apiInstance->createInvoice($create_invoice_request);
            $paymentUrl = $result->getInvoiceUrl();

            Order::create([
                'user_id'       => $user->id,
                'product_id'    => $product->id,
                'external_id'   => $external_id,
                'checkout_link' => $paymentUrl,
                'status'        => 'PENDING',
            ]);

            return redirect($paymentUrl);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
    public function notification(Request $request) 
    {
        // 1. Ambil Token Callback untuk keamanan (agar orang lain tidak bisa tembak manual)
        $callbackToken = $request->header('x-callback-token');

        // 2. Verifikasi Token (Nanti kita ambil tokennya dari Dashboard Xendit)
        if ($callbackToken !== env('XENDIT_CALLBACK_TOKEN')) {
            return response()->json(['message' => 'Token tidak valid'], 403);
        }

        // 3. Ambil data External ID dan Status dari Xendit
        $external_id = $request->external_id ?? ($request->data['reference_id'] ?? null);
        $status = $request->status; // Contoh: 'PAID' atau 'SETTLED'

        // 4. Cari order di database berdasarkan external_id
        $order = \App\Models\Order::where('external_id', $external_id)->first();

            if ($order) {
                if ($status === 'PAID' || $status === 'SETTLED') {
                    $order->update(['status' => 'PAID']);
                }

                elseif ($status === 'EXPIRED') {
                    $order->update(['status' => 'EXPIRED']);
                }

                return response()->json(['message' => 'Status berhasil diperbarui'], 200);
            }

        return response()->json(['message' => 'Webhook diterima (Data dummy/Order tidak ada)'], 200);
    }
    public function index(Request $request)
    {
        $orders = Order::whereHas('product', function($query) {
            // Jika kamu ingin memastikan produknya masih ada
        })
        ->whereIn('product_id', \App\Models\Product::pluck('id')) // Opsional: pastikan produk valid
        ->latest()
        ->get();
        $orders = Order::with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }
}