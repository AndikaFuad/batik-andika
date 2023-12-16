@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ url('history') }}" class="btn btn-primary"><i class="fa fa-arrow-left fa-lg"></i> Kembali </a>
            </div>
            <div class="col-md-12 mt-1">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('history') }}">Riwayat Pemesanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Pemesanan </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3>Sukses Check Out</h3>
                        <h5>Pesanan Anda Sudah Sukses Di Check Out Selanjutnnya Untuk Pembayaran Silahkan Transfer
                            <strong>Bank BRI Nomer Rekening : <br> 32113-821312-123</strong> dengan nominal wajib :
                            <strong>{{ number_format($pesanan->kode + $pesanan->jumlah_harga) }}</strong>
                        </h5>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-body">
                        <h3><i class="fa fa-shopping-cart">Detail Pemesanan</i></h3>
                        {{-- @if (!empty($pesanan)) --}}

                        <p align="right">Tanggal Pesan : {{ $pesanan->tanggal ?? null }}</p>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $no = 1; ?>
                                @if (empty($pesanan))
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td align="left"></td>
                                        <td align="left"></td>
                                        <td>

                                        </td>
                                    </tr>
                                @else
                                    @foreach ($pesanan_detail as $pesanan_detail)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $pesanan_detail->barang->nama_barang ?? null }}</td>
                                            <td>{{ $pesanan_detail->jumlah ?? null }} kain</td>
                                            <td align="left">Rp.
                                                {{ number_format($pesanan_detail->barang->harga) ?? null }}</td>
                                            <td align="left">Rp. {{ number_format($pesanan_detail->jumlah_harga) ?? null }}
                                            </td>

                                        </tr>
                                        <tr>

                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td colspan="4" align="right"><strong>Total Harga:</strong></td>
                                        <td align="right"><strong>Rp. {{ number_format($pesanan->jumlah_harga) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="right"><strong>Kode Unik:</strong></td>
                                        <td align="right"><strong>{{ number_format($pesanan->kode) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="right"><strong>Total yang Harus Ditransfer :</strong>
                                        </td>
                                        <td align="right">
                                            <strong>{{ number_format($pesanan->kode + $pesanan->jumlah_harga) }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" align="right"><button type="submit" class="btn btn-success"
                                                id="pay-button">Bayar</button></td>
                                    </tr>
                                @endif


                            </tbody>
                        </table>
                        {{-- @endif --}}
                    </div>
                </div>
            </div>
            <div class="'col-md-12">

            </div>
        </div>
    </div>
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        const tombolBayar = document.querySelector('#pay-button');
        tombolBayar.addEventListener('click', function(e) {
            e.preventDefault();
            // console.log('kode');

            $.ajax({
                url: location.origin + "/history/{{ $id }}",

                method: 'get',
                dataType: 'json',
                success: function(result) {
                    // console.log(result);
                    snap.pay(result, {
                        // Optional
                        onSuccess: function(result) {
                            /* You may add your own js here, this is just example */
                            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                            console.log(result)
                        },
                        // Optional
                        onPending: function(result) {
                            /* You may add your own js here, this is just example */
                            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                            console.log(result)
                        },
                        // Optional
                        onError: function(result) {
                            /* You may add your own js here, this is just example */
                            // document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
                            console.log(result)
                        }
                    });
                }
            });
        });
    </script>
@endsection
