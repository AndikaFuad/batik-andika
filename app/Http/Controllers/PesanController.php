<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\User;
use App\Services\Midtrans\CreateSnapTokenService;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Http\Request;
use carbon\Carbon;

class PesanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($id)
    {
        //dd($id);
        $barangs = Barang::where('id', $id)->first();

        return view('pesan.index', compact('barangs'));
        
    }

    public function pesan(Request $request, $id)
    {
        // dd($request);
        $barangs = Barang::where('id', $id)->first();
        $tanggal = carbon::now();
        
        //validasi apakah melebihi stok
        if($request->jumlah_pesan > $barangs->stok)
        {
            return redirect('pesan/'.$id);
        }

        //cek validasi
        $cek_pesanan = pesanan::where('user_id',Auth::user()->id)->where('status',0)->first();
        // simpan ke database pesanan
        if(empty($cek_pesanan))
        {
            $pesanan = new Pesanan;
            $pesanan->user_id = Auth::user()->id;
            $pesanan->tanggal = $tanggal;
            $pesanan->status = 0;
            $pesanan->jumlah_harga = 0;
            $pesanan->kode = mt_rand(1000, 9999);
            $pesanan->save();
        }

        //simpan ke database pesanan detail
        $pesanan_baru = pesanan::where('user_id',Auth::user()->id)->where('status',0)->first();

        //cek pesanan detail
        $cek_pesanan_detail = PesananDetail::where('barang_id', $barangs->id)->where('pesanan_id', $pesanan_baru->id)->first();
        if(empty($cek_pesanan_detail))
        {
            $pesanan_detail = new PesananDetail;
            $pesanan_detail->barang_id = $barangs->id;
            $pesanan_detail->pesanan_id = $pesanan_baru->id;
            $pesanan_detail->jumlah = $request->jumlah_pesan;
            $pesanan_detail->jumlah_harga = $barangs->harga*$request->jumlah_pesan;
            $pesanan_detail->save();
        }else
        {
            $pesanan_detail = PesananDetail::where('barang_id', $barangs->id)->where('pesanan_id', $pesanan_baru->id)->first(); 
            $pesanan_detail->jumlah = $pesanan_detail->jumlah+$request->jumlah_pesan;

            //harga sekarang
            $harga_pesanan_detail_baru = $barangs->harga*$request->jumlah_pesan;
            $pesanan_detail->jumlah_harga =  $pesanan_detail->jumlah_harga+  $harga_pesanan_detail_baru; 
            $pesanan_detail->update();
        }

        //jumlah total
        $pesanan = pesanan::where('user_id',Auth::user()->id)->where('status',0)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga+$barangs->harga*$request->jumlah_pesan;
        $pesanan->update(); 
        Alert::success('Pesanan Sukses Masuk Keranjang', 'Success');
        return redirect('check_out');

    }

    public function check_out(Request $request)
    {
        $pesanan = pesanan::where('user_id',Auth::user()->id)->where('status',0)->first();

        
        
        if(!empty($pesanan)){
            $pesanan_details = PesananDetail::where('pesanan_id', $pesanan->id)->get();
            // $snapToken = $pesanan->kode;
            // if (empty($snapToken)) {
            //     $midtrans = new CreateSnapTokenService($pesanan);
            //     $snapToken  = $midtrans->getSnapToken();

            //     $pesanan->kode = $snapToken;
            //     $pesanan->save();
            // }

            return view('pesan.check_out', compact('pesanan', 'pesanan_details'));
        } else {
            return view('pesan.check_out', compact('pesanan'));
        }

    }

    public function delete($id)
    {
        $pesanan_detail = PesananDetail::where('id', $id)->first();
        $pesanan = Pesanan::where('id', $pesanan_detail->pesanan_id)->first();
        $pesanan->jumlah_harga = $pesanan->jumlah_harga-$pesanan_detail->jumlah_harga;
        $pesanan->update();


        $pesanan_detail->delete();

        Alert::error('Pesanan Sukses Dihapus', 'Hapus');
        return redirect('check_out');

    }

    public function konfirmasi()
    {
        $user = User::where('id', Auth::user()->id)->first();
        // dd($user);
        if(empty($user->alamat))
        {
            Alert::error('Lengkapi alamat tujuan', 'Hapus');
            return redirect('check_out');
        }

        $pesanan = pesanan::where('user_id',Auth::user()->id)->where('status',0)->first();
        $pesanan_id = $pesanan->id;
        $pesanan->status = 1;
        $pesanan->update();

        $pesanan_details = PesananDetail::where('pesanan_id', $pesanan_id)->get();
        foreach ($pesanan_details as $pesanan_detail)
        {
            $barangs = Barang::where('id', $pesanan_detail->barang_id)->first();
            $barangs->stok = $barangs->stok-$pesanan_detail->jumlah;
            $barangs->update();
        }

        Alert::success('Pesanan Sukses Check Out Silahkan Lanjut Proses Pembayaran');
        return redirect('history/'.$pesanan_id = $pesanan->id);
    

    }
}
