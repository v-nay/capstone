<?php

use App\Model\Config as conf;
use App\Model\Language;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;

function authUser()
{
    return Auth::user();
}

function setRoleCache($user)
{
    return Cache::put('role-'.$user->id, $user->role);
}
function getRoleCache($user)
{
    return Cache::get('role-'.$user->id);
}
function clearRoleCache($user)
{
    return Cache::forget('role-'.$user->id);
}
function frontendUser()
{
    return Auth::guard('frontendUsers')->user();
}
function getCmsConfig($label)
{
    $value = '';
    if ($label == 'cms title') {
        $con = 'title';
    } elseif ($label == 'cms logo') {
        $con = 'logo';
    } elseif ($label == 'cms theme color') {
        $con = 'color';
    }
    $data = Cookie::get($con);
    if (isset($data) || $data !== null) {
        $value = $data;
    } else {
        $data = conf::where('label', $label)->first()->value;
        $value = $data;
    }

    return $value;
}
function generateToken($length)
{
    return bin2hex(openssl_random_pseudo_bytes($length));
}

function showInSideBar($check)
{
    return $check;
}

function modules()
{
    return Config::get('cmsConfig.modules');
}

function configTypes()
{
    return ['file', 'text', 'textarea', 'number'];
}

function globalLanguages()
{
    return Language::where('group', 'backend')->get();
}

function setConfigCookie()
{
    Cookie::queue('title', conf::where('label', 'cms title')->first()->value, 10000);
    Cookie::queue('logo', conf::where('label', 'cms logo')->first()->value, 10000);
    Cookie::queue('color', conf::where('label', 'cms theme color')->first()->value, 10000);
}

function localDatetime($dateTime)
{
    return Carbon::parse($dateTime)->timezone('Asia/Kathmandu');
}

function japaneseDateTime($dateTime)
{
    return Carbon::parse($dateTime)->timezone('Asia/Tokyo');
}

function storeLog($performedOn, $msg)
{
    $now = Carbon::now()->format('Y-m-d H:i:s');
    activity()
        ->performedOn($performedOn)
        ->log($msg.' at '.$now);
}

function logMessage($modelName, $modelId, $eventName)
{
    $user = authUser()->name ?? '--';
    $now = Carbon::now()->format('Y-m-d H:i:s');

    return "$modelName of id {$modelId} was <strong>{$eventName}</strong> by {$user} at {$now}.";
}

function pageIndex($items)
{
    $sn = 0;
    if (method_exists($items, 'perPage') && method_exists($items, 'currentPage')) {
        $sn = $items->perPage() * ($items->currentPage() - 1);
    }

    return $sn;
}

function SN($sn, $key)
{
    return $sn += $key + 1;
}
