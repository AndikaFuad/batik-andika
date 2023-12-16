@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 mb-5">
            <img src="{{ url('images/logo.png') }}" class="rounded mx-auto d-block" width="700" alt="">
        </div>
        @foreach ($barangs as $barangs)
            <div class="col-md-4">
                <div class="card">
                    <img  src="{{ url('uploads', $barangs->gambar) }}" class="card-img-top" alt="...">
                    <div class="card-body">
                      <h5 class="card-title">{{ $barangs->nama_barang }}</h5>
                      <p class="card-text">
                        <strong>Harga :</strong> Rp. {{ number_format($barangs->harga) }} <br>
                        <strong>Stok :</strong>  {{ $barangs->stok }} <br>
                        <hr>
                        <strong>Keterangan :</strong> <br> 
                        {{ $barangs->keterangan }} <br>
                      </p>
                      <a href="{{ url('pesan') }}/{{ $barangs->id }}" class="btn btn-primary"><i class="fa fa-shopping-cart"></i> Pesan</a>
                    </div>
                  </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
