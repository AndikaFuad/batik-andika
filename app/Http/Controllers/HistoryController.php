<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $pesanans = Pesanan::where('user_id', Auth::user()->id)->where('status', '!=', 0)->get();

        return view('history.index', compact('pesanans'));
    }

    public function detail(Request $request, $id)
    {
        
        $pesanan = pesanan::where('id', $id)->first();
        $pesanan_detail = PesananDetail::where('pesanan_id', $pesanan->id)->get();
        // $pesanans = PesananDetail::where('pesanan_id', $pesanan->id)->get()->toArray();
        
        if ($request->ajax()) {
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
            Config::$isSanitized = Config::$is3ds = true;
            $transaction_details = array(
                'order_id' => rand(),
                'gross_amount' => $pesanan->jumlah_harga+$pesanan->kode, // no decimal allowed for creditcard
            );
            // Optional
            $item_details =  array (
                array(
                    'id' => 'a1',
                    'price' => $pesanan->jumlah_harga+$pesanan->kode,
                    'quantity' => 1,
                    'name' => "Apple"
                ),
              );
            // Optional
            $customer_details = array(
                'first_name'    => Auth::user()->name,
                'last_name'     => "",
                'email'         => Auth::user()->email,
                'phone'         => Auth::user()->nohp,
                'billing_address'  => "malang",
                'shipping_address' => "malang"
            );
            // Fill transaction details
            $transaction = array(
                'transaction_details' => $transaction_details,
                'customer_details' => $customer_details,
                'item_details' => $item_details,
            );
            
            $snap_token = '';
            try {
                $snap_token = Snap::getSnapToken($transaction);
            }
            catch (\Exception $e) {
                echo $e->getMessage();
            }
            // return $snap_token;
           return response()->json($snap_token);
        }
       

        return view('history.detail', compact('pesanan', 'pesanan_detail', 'id'));        
    }    
    public function bayar(Request $request) {
        return json_encode("success");
    }
}
