@extends('layouts.app')
@section('import_js')
    <script src="{{ $file->ver('client/app/js/cart.js') }}"></script>
@endsection
@section('margin')
    dtl__margin option_cart
@endsection
@section('content')
    <div id="breadCrumb">
        <div class="container">
            <ol class="b__crumb">
                <li class="b__crumb--item">
                    <i class="fas fa-home"></i>
                </li>
                <li class="b__crumb--item">
                    <i class="fas fa-long-arrow-alt-right"></i>
                </li>
                <li class="b__crumb--item">
                    Giỏ Hàng
                </li>
            </ol>
        </div>
    </div>

    <div id="cart">
        <div class="container" id="cart__show">
            {{-- <x-client.cart.show /> --}}
            <div class="d-flex justify-content-center">
                <img src="https://res.cloudinary.com/vanh-tech/image/upload/v1676041175/Ellipsis-1s-200px_hpwekn.gif"
                    alt="loading-cart" width="100">
            </div>
        </div>
    </div>
@endsection
