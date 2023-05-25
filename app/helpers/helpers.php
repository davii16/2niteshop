<?php

use Carbon\Carbon;
use App\Models\Config;
use App\Models\Category;
use App\Models\Products;
use App\Models\Insurance;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Validation\Rules\Exists;

function isPreOrder($date_sold)
{
    return strtotime($date_sold) >= strtotime(Carbon::now());
}
function statusProduct($date_sold, $qty)
{
    $status = $qty <= 0 ? 2 : 1;
    return isPreOrder($date_sold) ? 3 : $status;
}

function price_product($product,  $ops = "", $options = ['qty' => 1])
{
    $qty = (int) $options['qty'];
    $price = (int) ($product->price - $product->discount);
    $arrIns = explode(",", $ops);

    if (count($arrIns) > 0) {
        foreach ($arrIns as  $ins_id) {
            $item = Insurance::where('id', $ins_id)->first();
            if ($item) {
                $price += (int) $item->price;
            }
        }
    }
    return (int)$price * $qty;
}
function badges($array, $key = null, $type = "primary")
{
    $html = "";
    if (count($array) > 0) {
        foreach ($array as $item) {
            if ($key) {
                $html .= '<span class="badge m-1 badge-' . $type . '">' . $item[$key] . '</span>';
            } else {
                $html .= '<span class="badge m-1 badge-' . $type . '">' . $item . '</span>';
            }
        }
    }
    return $html;
}

function renderROP($r_o_p = [])
{
    $html = "";
    if (count($r_o_p) > 0) {
        foreach ($r_o_p as $item) {
            if (isset($item->name)) {
                $html .= '<span class="badge m-1 badge-primary">' . $item->name . '</span>';
            } else {
                if ($item) {
                    $html .= '<span class="badge m-1 badge-primary">' . $item . '</span>';
                }
            }
        }
    } else {
        $html .= '<span class="badge m-1 badge-primary">User</span>';
    }
    return $html;
}
function getDescByHtml($content = "", $length = 240)
{
    $content = preg_replace('/\s+/', ' ', strip_tags($content));
    // $content = ltrim(Str::limit($content, $length, '..'), ' &nbsp;');
    return ltrim(Str::limit($content, $length, '..'), ' &nbsp;');
}

function isJson($string)
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}
function get_drive_id_from_url($url)
{
    preg_match('~/d/\K[^/]+(?=/)~', $url, $result);
    return $result[0];
}
function price_product_cost($cart)
{
    $qty = (int) $cart->qty;
    $price = (int) ($cart->options->cost);
    $arrIns = explode(",", $cart->options->ins);
    if (count($arrIns) > 0) {
        foreach ($arrIns as  $ins_id) {
            $item = Insurance::where('id', $ins_id)->first();
            if ($item) {
                $price += (int) $item->price;
            }
        }
    }
    return (int)$price * $qty;
}
function array_search_key($needle_key, $array)
{
    foreach ($array as $key => $value) {
        if ($key == $needle_key) return $value;
        if (is_array($value)) {
            if (($result = array_search_key($needle_key, $value)) !== false)
                return $result;
        }
    }
    return false;
}
function get_crawler($key = null)
{
    $crawler = session()->has('crawler') ? session()->get('crawler') : [];

    if (count($crawler) <= 0) {
        return "";
    }
    return array_search_key($key, $crawler);
}
function is_product_new($created_at)
{
    $date1 = Carbon::create($created_at);
    $date2 = Carbon::now()->subDays(7);
    return $date1->gt($date2);
}
function getRelationship($rela = "")
{
    $relationship = config('Relationship.' . $rela);
    return $relationship;
}
function handle_rela($request, $rela, $relaId = 0, $resever = false, $isEdit = false)
{
    $arrela = explode('-', $rela);
    if ($resever) {
        $arrela = array_reverse($arrela);
    }
    $key = $arrela[1];
    $name = $arrela[0];
    $qK = $key . "_id";
    $qN = $name . "_id";
    $rK = "rela__" . $key;
    $model = '\\App\Models\\';
    $relationship = getRelationship($rela);
    if (!$relationship) {
        return;
    }
    $model .= $relationship['modelRela'];
    if (!$request->has($rK)) {
        return;
    }
    if ($request->get($rK)) {
        $selected = explode(",", $request->get($rK));
        try {
            if (count($selected) > 0) {
                foreach ($selected as $id) {
                    if ($isEdit) {
                        $model::whereNotIn($qK,  $selected)->where($qN, $relaId)->delete();
                        $has = $model::where($qK,  $id)->where($qN, $relaId)->first();
                        if (!$has) {
                            $model::create([
                                $qK => $id,
                                $qN => $relaId
                            ]);
                        }
                    } else {
                        $model::create([
                            $qK => $id,
                            $qN => $relaId
                        ]);
                    }
                }
                unset($id);
            }
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    } else {
        if ($isEdit) {
            $model::where($qN, $relaId)->delete();
        }
    }

    return;
}
if (!function_exists('va_get_meta')) {
    function va_get_meta($url)
    {
        $data = [];
        // Extract HTML using curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $res = curl_exec($ch);
        curl_close($ch);

        // Load HTML to DOM object
        $dom = new DOMDocument();
        @$dom->loadHTML($res);

        // Parse DOM to get Title data
        $nodes = $dom->getElementsByTagName('title');
        $data['title'] = $nodes->item(0)->nodeValue;
        // Parse DOM to get meta data
        $metas = $dom->getElementsByTagName('meta');

        $data['desc'] = $data['kws'] = $data['ogimg'] = '';
        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);

            if ($meta->getAttribute('name') == 'description') {
                $data['desc'] = $meta->getAttribute('content');
            }

            if ($meta->getAttribute('name') == 'keywords') {
                $data['kws']  = $meta->getAttribute('content');
            }
            if ($meta->getAttribute('property') == 'og:image') {
                $data['ogimg'] = $meta->getAttribute('content');
            }
        }

        return $data;
    }
}
if (!function_exists('category_child')) {
    function category_child($data, $parent_id = 0)
    {
        $result = array();
        foreach ($data  as $item) {
            if ($item->parent_id == $parent_id) {
                $result[] = $item;
                $child = category_child($data, $item->id);
                $result = array_merge($result, $child);
            }
        }
        return $result;
    }
}
if (!function_exists('timeDiff')) {
    function timeDiff($firstTime, $lastTime): string
    {
        $firstTime = strtotime($firstTime);
        $lastTime = strtotime($lastTime);

        $difference = $lastTime - $firstTime;

        $data['years'] = abs(floor($difference / 31536000));
        $data['months'] = abs(floor(($difference - ($data['years'] * 31536000)) / 2629800));
        $data['days'] = abs(floor(($difference - ($data['years'] * 31536000) - ($data['months'] * 2629800)) / 86400));
        $data['hours'] = abs(floor(($difference - ($data['years'] * 31536000) -  ($data['months'] * 2629800) - ($data['days'] * 86400)) / 3600));
        $data['minutes'] = abs(floor(($difference - ($data['years'] * 31536000) - ($data['months'] * 2629800) - ($data['days'] * 86400) - ($data['hours'] * 3600)) / 60));
        $data['seconds'] = abs(floor(($difference - ($data['years'] * 31536000) - ($data['months'] * 2629800) - ($data['days'] * 86400) - ($data['hours'] * 3600) - ($data['minutes'] * 60))));

        $timeString = '';

        if ($data['years'] > 0) {
            $timeString .= $data['years'] . " Năm ";
        }
        if ($data['months'] > 0) {
            $timeString .= $data['months'] . " Tháng ";
        }
        if ($data['days'] > 0) {
            $timeString .= $data['days'] . " Ngày ";
        }

        if ($data['hours'] > 0) {
            $timeString .= $data['hours'] . " Giờ ";
        }

        if ($data['minutes'] > 0) {
            $timeString .= $data['minutes'] . " Phút ";
        }
        if ($data['seconds'] > 0) {
            $timeString .= $data['seconds'] . " Giây ";
        }

        return $timeString;
    }
}
if (!function_exists('navi_ajax_page')) {
    function navi_ajax_page($number_pages, $page, $size = "", $position = "justify-content-center", $mt = "mt-4", $class = "")
    {
        $output = '';
        $output .= " <nav aria-label='Page navigation example' class='$mt  mr-3'>";
        $output .= "  <ul class='pagination  $position   $size '>";
        if ($page > 1) {
            $page_prev = $page - 1;
            $output .= " <li class='page-item'>
            <a class='page-link' data-page='" . $page_prev . "'><i class='fas fa-chevron-left'></i></a>
        </li>";
        }
        if ($page > 3) {
            $output .= " <li class='page-item '><a class='page-link' data-page='1'>1</a></li>";
            $output .= " <li class='page-item '><a class='page-link' >...</a></li>";
        }
        for ($i = 1; $i <= $number_pages; $i++) {
            if ($i < $page + 2 && $i > $page - 2) {
                if ($i == $page) {
                    $output .= "<li class='page-item active'><a class='page-link' data-page='" . $i . "'>" . $i . "</a></li>";
                } else {
                    $output .= " <li class='page-item '><a class='page-link' data-page='" . $i . "'>" . $i . "</a></li>";
                }
            }
            unset($active);
        }
        if ($page < $number_pages - 2) {
            $output .= " <li class='page-item'><a class='page-link' data-page='" . $number_pages . "' >...</a></li>";
            $output .= " <li class='page-item'><a class='page-link' data-page='" . $number_pages . "'> " . $number_pages . "</a></li>";
        }
        if ($page <  $number_pages) {
            $p_next = $page + 1;
            $output .= "<li class='page-item'>
            <a class='page-link' data-page='" . $p_next . "'><i class='fas fa-chevron-right'></i></a>
        </li>";
        }
        $output .= "</ul>";
        $output .= " </nav>";
        return $output;
    }
}

function new_stt($stt)
{
    if ($stt == 1) {
        $output = "Mới";
    } else {
        $output = "Đăng lâu";
    }
    return $output;
}
function stock_stt($stt)
{
    if ($stt == 1) {
        $output = "Còn hàng";
    } elseif ($stt == 2) {
        $output = "Sắp có hàng";
    } elseif ($stt == 3) {
        $output = "Tạm hết hàng";
    }
    return $output;
}
function highlight_stt($stt)
{
    $name = "";
    switch ($stt) {
        case 1:
            $name =  "HOT";
            break;
        case 2:
            $name =  "Bình Thường";
            break;
        default:
            break;
    }
    return $name;
}
function usage_stt($stt)
{
    if ($stt == 1) {
        $output = "Hàng mới";
    } else {
        $output = "Đã qua sử dụng";
    }
    return $output;
}
function slide_stt($stt)
{
    if ($stt == 1) {
        $output = "Đang hiển thị";
    } else {
        $output = "Chờ hiển thị";
    }
    return $output;
}
function getVal($key = "")
{
    $data = Config::select('value')->where('name', 'LIKE', $key)->first();
    if (!$data) {
        $result = new stdClass();
        $result->value = "";
        return $result;
    }
    return $data;
}
function total()
{
    $total = 0;
    foreach (Cart::instance('shopping')->content() as $cart) {
        $total += $cart->options->sub_total;
    }
    return $total;
}

function empty_cart()
{
    if (Cart::instance('shopping')->count() > 0) {
        return false;
    }
    return true;
}
function status_order($stt)
{
    foreach (config('orders.status') as $key => $status) {
        if ($key == $stt) {
            return $status;
        }
    }
}
function paid_order($stt)
{
    foreach (config('orders.paid') as $key => $status) {
        if ($key == $stt) {
            return $status;
        }
    }
}
