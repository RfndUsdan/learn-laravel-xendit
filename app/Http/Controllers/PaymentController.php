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
}