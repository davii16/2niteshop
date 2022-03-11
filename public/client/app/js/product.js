$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var heightContent = $("#dtl__info--wrapper").height();
    if (heightContent <= 200) {
        $(".see__detail").css('display', 'none');
    } else {
        $(".see__detail").css('display', 'flex');
    }
    function get_checked(class_name) {
        var filter = [];
        $('.' + class_name + ':checked').each(function () {
            filter.push($(this).val());
        });
        return filter;
    }
    function fomat($price = "", $class = "") {
        var url = route('format');
        $.ajax({
            type: "post",
            url: url,
            data: { price: $price },
            dataType: "json",
            success: function (data) {
                $("." + $class).html(data.price);
            }
        });
    }
    // detail product
    $(document).on('change', '.input-number', function () {
        var minValue = parseInt($(this).attr('min'));
        var maxValue = parseInt($(this).attr('max'));
        var valueCurrent = parseInt($(this).val());
        var insurPrice = parseInt($('.insur__item-active').attr('data-price'));
        if (isNaN(insurPrice)) {
            var insurPrice = 0;
        }
        var price_prd = parseInt($('#price_prd').val());
        if (isNaN(price_prd)) {
            var price_prd = $(this).attr('data-prd');
        }
        var sub_total = parseInt(valueCurrent * price_prd);
        var total = parseInt(sub_total + insurPrice);
        var id = $(this).attr('data-id');
        var name = $(this).attr('name');
        if (valueCurrent >= minValue) {
            $(".btn-number[data-type='minus'][data-field='" + name + "']").removeAttr('disabled')
        } else {
            alert('Sorry, the minimum value was reached');
            $(this).val($(this).data('oldValue'));
        }
        if (valueCurrent <= maxValue) {
            $(".btn-number[data-type='plus'][data-field='" + name + "']").removeAttr('disabled')
        } else {
            alert('Sorry, the maximum value was reached');
            $(this).val($(this).data('oldValue'));
        }
        $(".prd__dtl--price span").attr('data-price', total);
        if ($("#quickModal").hasClass('show')) {
            $("#outputDetail #button-cart").attr('data-qty', valueCurrent);
            $("#outputDetail #button-cart").attr('data-price', total);
        } else {
            $("#button-cart").attr('data-price', total);
            $("#button-cart").attr('data-qty', valueCurrent);
        }
        $(".btn-cart-" + id).attr('data-price', total);
        $(".btn-cart-" + id).attr('data-qty', valueCurrent);
        if (total != 0) {
            fomat(total, "prd__dtl--price span");
        } else {
            $(".prd__dtl--price span").text("CALL-1900 0116");
        }

    });
    // end input number
    // end active insur
    $(document).on('click', ".insur__item", function () {
        var price = parseInt($(this).attr('data-price'));
        var price_prd = parseInt($('#price_prd').val());
        var qty = parseInt($('.input-number').val());
        var sub_total = parseInt(price_prd * qty);
        var total = parseInt(sub_total + price);
        $('.insur__item').removeClass('insur__item-active');
        $(this).addClass('insur__item-active');
        $(".prd__dtl--price span").attr('data-price', total);
        $("#button-cart").attr('data-price', total);
        $("#button-cart").attr('data-op', parseInt($(this).attr('data-id')));
        fomat(total, "prd__dtl--price span");
    });
    // end detail
    if (!isNaN(parseInt($('.insur__item-active').attr('data-id')))) {
        $("#button-cart").attr('data-op', parseInt($('.insur__item-active').attr('data-id')));
    }
    // ///////////
    $(document).on('click', ".see__detail--btn", function () {
        $('.prd__dtl--info').css('max-height', '100%');
        $(this).addClass('close');
        $(".see__detail--btn span").text("Thu gọn");
        $(".see__detail--btn i").removeClass('fa-long-arrow-alt-down');
        $(".see__detail--btn i").addClass('fa-long-arrow-alt-up');

    });
    $(document).on('click', ".see__detail--btn.close", function () {
        $('.prd__dtl--info').css('max-height', '400px');
        $(this).removeClass('close');
        $(".see__detail--btn span").text("Xem chi tiết thông số kỹ thuật");
        $(".see__detail--btn i").removeClass('fa-long-arrow-alt-up');
        $(".see__detail--btn i").addClass('fa-long-arrow-alt-down');

    });
    $(document).on('click', ".see__full--btn", function () {
        $('#dtl__info').css('height', 'auto');
        $(this).addClass('close');
        $(".see__full--btn span").text("Thu gọn");
        $(".see__full--btn i").removeClass('fa-long-arrow-alt-down');
        $(".see__full--btn i").addClass('fa-long-arrow-alt-up');

    });
    $(document).on('click', ".see__full--btn.close", function () {
        $('#dtl__info').css('height', '200px');
        $(this).removeClass('close');
        $(".see__full--btn span").text("Xem chi tiết bài viết");
        $(".see__full--btn i").removeClass('fa-long-arrow-alt-up');
        $(".see__full--btn i").addClass('fa-long-arrow-alt-down');

    });
    $('#nav-dtlContent').html(function (i, h) {
        return h.replace(/&nbsp;/g, '');
    });
    $(document).on('change', "#sort", function () {
        var page = 1;
        var genre = get_checked('game_genre');
        var dataView = $(this).attr('data-view');
        var id = $(this).attr('data-id');
        var val = $(this).val();
        var op = $("#sort :selected").attr('ord');
        var url1 = route('index_ajax');
        var url = new URL($("#index_current_url").val());
        var sps = url.searchParams;
        if (val != "id") {
            sps.append('sort', val);
            sps.append('ord', op);
            sps.append('page', 1);
        }
        if (genre.length != 0) {
            sps.append('genre', genre.toString());
            var genres = genre.toString();
        } else {
            var genres = 0;
        }
        history.replaceState("", "", url.toString());
        $.ajax({
            type: "post",
            url: url1,
            data: { id: id, page: page, ord: op, sort: val, view: dataView, genre: genres },
            dataType: "json",
            beforeSend: function () {
                $.loading();
            },
            success: function (data) {
                $("#outputGrid").html(data.html);
                $("#outputList").html(data.html_2);
                $('[data-toggle="tooltip"]').tooltip();
                if (dataView == "grid") {
                    $("#grid .products__page").html(data.page);
                } else {
                    $("#list .products__page").html(data.page);
                }
                $.end_loading();
            }
        });
    });
    $(document).on('click', ".products__page .page-link", function () {
        var page = $(this).attr('data-page');
        var genre = get_checked('game_genre');
        var dataView = $("#sort").attr('data-view');
        var id = $("#sort").attr('data-id');
        var val = $("#sort").val();
        var op = $("#sort :selected").attr('ord');
        var url1 = route('index_ajax');
        var url = new URL($("#index_current_url").val());
        var sps = url.searchParams;
        if (val != "id") {
            sps.append('sort', val);
            sps.append('ord', op);
        }
        if (page != 1) {
            sps.append('page', page);
        }
        if (genre.length != 0) {
            sps.append('genre', genre.toString());
            var genres = genre.toString();
        } else {
            var genres = 0;
        }
        history.replaceState("", "", url.toString());
        $.ajax({
            type: "post",
            url: url1,
            data: { id: id, page: page, ord: op, sort: val, view: dataView, genre: genres },
            dataType: "json",
            beforeSend: function () {
                $.loading();
            },
            success: function (data) {
                $("#outputGrid").html(data.html);
                $("#outputList").html(data.html_2);
                $('[data-toggle="tooltip"]').tooltip();
                $(".products__page").html(data.page);
                $(document).scrollTop(0);
                $.end_loading();
            }
        });
    });

    $(document).on('click', ".game_genre", function () {
        var genre = get_checked('game_genre');
        var page = 1;
        var dataView = $("#sort").attr('data-view');
        var id = $("#sort").attr('data-id');
        var val = $("#sort").val();
        var op = $("#sort :selected").attr('ord');
        var url1 = route('index_ajax');
        var url = new URL($("#index_current_url").val());
        var sps = url.searchParams;
        if (genre.length != 0) {
            sps.append('genre', genre.toString());
            var genres = genre.toString();
        } else {
            var genres = 0;
        }
        history.replaceState("", "", url.toString());
        $.ajax({
            type: "post",
            url: url1,
            data: { id: id, page: page, view: dataView, genre: genres },
            dataType: "json",
            beforeSend: function () {
                $.loading();
            },
            success: function (data) {
                $("#outputGrid").html(data.html);
                $("#outputList").html(data.html_2);
                $('[data-toggle="tooltip"]').tooltip();
                if (dataView == "grid") {
                    $("#grid .products__page").html(data.page);
                } else {
                    $("#list .products__page").html(data.page);
                }
                $.end_loading();
            }
        });

    });

    // END READYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY
});
