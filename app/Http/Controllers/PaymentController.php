<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use App\Mail\PaymentSuccessMail;
use App\Mail\PaymentFailedMail;
use Illuminate\Support\Facades\Mail;

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
        $callbackToken = $request->header('x-callback-token');
        
        // 1. Validasi Token
        if ($callbackToken !== env('XENDIT_CALLBACK_TOKEN')) {
            return response()->json(['message' => 'Token tidak valid'], 403);
        }

        // 2. Logging untuk Debug
        \Illuminate\Support\Facades\Log::info('Data Webhook Xendit:', $request->all());

        $external_id = $request->external_id;
        $status = $request->status;

        // 3. Cari Order
        $order = Order::where('external_id', $external_id)->first();

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        // 4. Proses berdasarkan Status
        if ($status === 'PAID' || $status === 'SETTLED') {
            $order->update(['status' => 'PAID']);
            
            // Kirim email hanya jika user ada
            if ($order->user) {
                Mail::to($order->user->email)->send(new PaymentSuccessMail($order));
            }
            
            return response()->json(['message' => 'Berhasil Update PAID & Kirim Email'], 200);
        } 
        
        elseif ($status === 'EXPIRED') {
            $order->update(['status' => 'EXPIRED']);
            
            if ($order->user) {
                Mail::to($order->user->email)->send(new \App\Mail\PaymentFailedMail($order));
            }
            
            return response()->json(['message' => 'Berhasil Update EXPIRED & Kirim Email'], 200);
        }

        // 5. Respon jika status dikirim tapi bukan PAID atau EXPIRED
        return response()->json(['message' => 'Webhook diterima, status: ' . $status], 200);
    }
    public function index(Request $request)
    {
       $orders = Order::with('product')
        ->where('user_id', $request->user()->id)
        ->latest()
        ->get();

        return view('orders.index', compact('orders'));
    }
}