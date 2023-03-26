@extends('layouts.app')
@section('import_js')
    <script src="{{ $file->import_js('home.js') }}"></script>
@endsection
@section('banner')
    <div class="banner">
        <a href="{{ url($banner->link) }}" class="d-block">
            <img src="{{ $file->ver_img($banner->img) }}" alt="{{ $banner->name }}" class="w-100 lazy">
        </a>
    </div>
@endsection
@section('content')
    @if (getVal('background')->value != null)
        @php
            $bg = $file->ver_img(getVal('background')->value);
        @endphp
        <style>
            body {
                background-image: url({{ $bg }});
                background-attachment: fixed;
                background-repeat: no-repeat;
            }

            #biad__content--home {
                background: white;
                padding-left: 0;
                padding-right: 0;
            }

            #biad__header--bot {
                background: white;
            }

            #biad__header--bot>div {
                padding-left: 0;
                padding-right: 0;
            }

            .show__home {
                padding-left: 10px;
                padding-right: 10px;
            }

            .show__home--box:last-child {
                margin-bottom: 0 !important;
                padding-bottom: 100px;
            }
        </style>
    @endif
    <div id="biad__content--home" class="container">
        <div class="w-100 home">
            <div class="home__left">
                <x-client.menu.menu />
            </div>
            <div class="home__right">
                <div class="home__right--slide">
                    <div id="homeSlide" class="carousel slide w-100" data-ride="carousel">
                        <ol class="carousel-indicators">
                            @for ($i = 0; $i < count($slides); $i++)
                                @if ($i == 0)
                                    <li data-target="#homeSlide" data-slide-to="{{ $i }}" class="active">
                                    </li>
                                @else
                                    <li data-target="#homeSlide" data-slide-to="{{ $i }}"></li>
                                @endif
                            @endfor
                        </ol>
                        <div class="carousel-inner">
                            @foreach ($slides as $key => $slide)
                                @if ($loop->first)
                                    <div class="carousel-item active">
                                        <a href="{{ url($slide->link) }}" class="d-block">
                                            <img class="d-block w-100 img-fluid lazy" alt="{{ $slide->name }}"
                                                src="{{ $file->ver_img($slide->img) }}">
                                        </a>
                                    </div>
                                @else
                                    <div class="carousel-item">
                                        <a href="{{ url($slide->link) }}" class="d-block">
                                            <img class="d-block w-100 img-fluid lazy" alt="{{ $slide->name }}"
                                                src="{{ $file->ver_img($slide->img) }}">
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <button class="slide__btn --prev" type="button" data-target="#homeSlide" data-slide="prev">
                            <i class="fas fa-angle-left"></i>
                        </button>
                        <button class="slide__btn --next" type="button" data-target="#homeSlide" data-slide="next">
                            <i class="fas fa-angle-right"></i>
                        </button>
                    </div>
                </div>
                <div class="home__right--banner">
                    @foreach ($banners as $bn)
                        @if ($bn->position == 'Phải')
                            <a href="{{ url($bn->link) }}" class="d-block">
                                <img src="{{ $file->ver_img($bn->img) }}" class="lazy" alt="{{ $bn->name }}"
                                    width="100%" height="auto" alt="{{ $bn->name }}">
                            </a>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
        {{-- END HOME MENU + SLIDE --}}
        <div class="w-100 bot__banner owl-carousel owl-theme">
            @foreach ($banners as $bt)
                @if ($bt->position == 'Dưới')
                    <div class="item bot__banner--item">
                        <a href="{{ url($bt->link) }}" class="d-block w-100">
                            <img src="{{ $file->ver_img($bt->img) }}" class="img-fluid lazy" alt="{{ $bt->name }}">
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
        {{-- END BOTTOM BANNER --}}
        <div class="w-100 owl-carousel owl-theme mb-4">
            <div class="item">
                <a class="d-block w-100">
                    <img src="{{ $file->ver_img_local('client/images/plc-1.png') }}" class="img-fluid lazy" alt="plc-1">
                </a>
            </div>
            <div class="item">
                <a class="d-block w-100">
                    <img src="{{ $file->ver_img_local('client/images/plc-2.png') }}" class="img-fluid lazy" alt="policy">
                </a>
            </div>
            <div class="item">
                <a class="d-block w-100">
                    <img src="{{ $file->ver_img_local('client/images/plc-3.png') }}" class="img-fluid lazy" alt="policy">
                </a>
            </div>
            <div class="item">
                <a class="d-block w-100">
                    <img src="{{ $file->ver_img_local('client/images/plc-4.png') }}" class="img-fluid lazy" alt="policy">
                </a>
            </div>
        </div>
        {{-- --------------- --}}
        <div class="w-100 show__home">
            @foreach ($show_home as $item)
                <x-client.home.section :item="$item" />
            @endforeach
        </div>
    </div>

    {{-- start home__blogs --}}
    <div id="home__blogs">
        <div id="home__blogs--content" class="container">
            <a href="{{ url('tin-tuc') }}" id="home__blogs--title">
                <img src="{{ $file->ver_img_local('client/images/bang-tin-home-banner-1280x80.jpg') }}" alt="Bảng Tin"
                    class="img-fluid lazy">
            </a>
            <div id="area__blogs">
                <div class="tab-content" id="myTabContent__blogs">
                    <div class="tab-pane active" id="tab__blogs" role="tabpanel">
                        <div class="swiper mySwiper">
                            <div class="swiper-wrapper swiper-blogs">
                                @foreach ($blogs as $blog)
                                    <div class="swiper-slide">
                                        <x-blogsubitem :blog="$blog" />
                                    </div>
                                    @php
                                        unset($blog);
                                    @endphp
                                @endforeach
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- end home__blogs --}}
@endsection
