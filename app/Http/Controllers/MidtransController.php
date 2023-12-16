<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function index(){
        // MIDTRANS_CLIENT_KEY=SB-Mid-client-cUIyfRasTW6iswE2
        // MIDTRANS_SERVER_KEY=SB-Mid-server-7O12d3SafjzW84_4Rx5GBkE1
        // MIDTRANS_IS_PRODUCTION=false
        // MIDTRANS_IS_SANITIZED=true
        // MIDTRANS_IS_3DS=true
        
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isSanitized = Config::$is3ds = true;
        $transaction_details = array(
            'order_id' => rand(),
            'gross_amount' => 94000, // no decimal allowed for creditcard
        );
        // Optional
        $item_details = array (
            array(
                'id' => 'a1',
                'price' => 94000,
                'quantity' => 1,
                'name' => "Apple"
            ),
          );
        // Optional
        $customer_details = array(
            'first_name'    => "Andri",
            'last_name'     => "Litani",
            'email'         => "andri@litani.com",
            'phone'         => "081122334455",
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
        return json_encode($snap_token);
    }
}
