<?php

namespace App\Http\Middleware;

use App\Http\BrowserDetection;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\ResourceServer;

class LogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function __construct(Route $route, ResourceServer $server)
    {
        $this->route = $route;
        $this->server = $server;
    }

    protected function customRequestLogger($request, $response)
    {
        if ((bool) env('PINEWHEEL_MIDDLEWARE', true)) {
            try {
                $url = $request->server('PATH_INFO') ?? '/';
                $url = trim($url);
                if ($url == '/') {
                    $url = $request->server('REQUEST_URI') ?? '/';
                    $url = @explode('?', $url)[0];
                    $url = trim($url);
                }

                $absUrl = $this->route->uri();

                if ($absUrl[0] != '/' && $url[0] == '/') {
                    $absUrl = '/'.$absUrl;
                }

                $type = 'web';
                if (strpos($url, '/api/v') !== false) {
                    $type = 'api';
                }

                $userId = authUser()->id ?? null;

                $agent = new BrowserDetection();
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                $agent = $agent->getAll($useragent);
                $payload = in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) ? $this->parsePayload($request->all()) : '';
                $logData = [
                    'dType' => $request->header('dType') ?? '',
                    'brand' => $request->header('brand') ?? '',
                    'device' => $request->header('device') ?? '',
                    'os' => $agent['os_name'] ?? '',
                    'osVer' => $agent['os_version'] ?? '',
                    'brw' => $agent['browser_name'] ?? '',
                    'brwVer' => $agent['browser_version'] ?? '',
                    'level' => $request->header('level') ?? '',
                    'code' => $response->getStatusCode(),
                    'msg' => Response::$statusTexts[$response->getStatusCode()] ?? 'Ok',
                    'method' => $request->method(),
                    'uid' => $userId ?? '',
                    'uag' => $request->server('HTTP_USER_AGENT'),
                    'did' => $request->header('deviceId') ?? '',
                    'sessionId' => $request->header('Session-Id') ?? '',
                    'payload' => $payload,
                    'latLng' => '',
                    'url' => $url,
                    'absUrl' => $absUrl,
                    'params' => $request->server('QUERY_STRING') ?? '',
                    'date' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
                    'time' => microtime(true) - LARAVEL_START,
                    'family' => filter_var($request->ip(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 'IPV4' : 'IPV6',
                    'ip' => $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $request->ip(),
                    'apiVer' => 'v1',
                    'host' => '',
                    'country' => '',
                    'ctrCode' => '',
                    'region' => '',
                    'city' => '',
                    'cntLng' => $request->header('content-length') ?? '',
                    'appVer' => $request->header('appVer') ?? '',
                    'type' => $type ?? '',
                    'referral' => $_SERVER['HTTP_REFERER'] ?? $request->server('HTTP_REFERER'), // Pass page/tab name here
                    'project' => '',
                    'projectType' => '',

                ];

                $today = Carbon::today()->toDateString();
                $logs_path = storage_path().'/logs/ai'.'/'.$today.'.log';
                $data['data'] = [];

                if (is_dir(storage_path().'/logs/ai') != true) {
                    \File::makeDirectory(storage_path().'/logs/ai', $mode = 0755, true);
                }
                if (! \File::exists(storage_path().'/logs/ai')) {
                    \File::put($logs_path, json_encode($logData, JSON_UNESCAPED_SLASHES));
                } else {
                    \File::append($logs_path, PHP_EOL.json_encode($logData, JSON_UNESCAPED_SLASHES));
                }
            } catch (\Exception $e) {
                \Log::error("error in logger $e");
            }
        }
    }

    private function parsePayload($data)
    {
        if (is_array($data) && isset($data['data'])) {
            if (isset($data['data']['password'])) {
                unset($data['data']['password']);
            }
            if (isset($data['data']['password'])) {
                unset($data['data']['password']);
            }
            if (isset($data['data']['confirm_password'])) {
                unset($data['data']['confirm_password']);
            }
            if (isset($data['data']['id'])) {
                unset($data['data']['id']);
            }
            if (isset($data['data']['user_id'])) {
                unset($data['data']['user_id']);
            }
            if (isset($data['data']['username'])) {
                unset($data['data']['username']);
            }
            if (isset($data['data']['email'])) {
                unset($data['data']['email']);
            }
            if (isset($data['data']['new-password'])) {
                unset($data['data']['new-password']);
            }
        }

        if (is_array($data) && (! isset($data['data']))) {
            if (isset($data['password'])) {
                unset($data['password']);
            }
            if (isset($data['password'])) {
                unset($data['password']);
            }
            if (isset($data['confirm_password'])) {
                unset($data['confirm_password']);
            }
            if (isset($data['id'])) {
                unset($data['id']);
            }
            if (isset($data['user_id'])) {
                unset($data['user_id']);
            }
            if (isset($data['username'])) {
                unset($data['username']);
            }
            if (isset($data['email'])) {
                unset($data['email']);
            }
            if (isset($data['client_secret'])) {
                unset($data['client_secret']);
            }
            if (isset($data['client_id'])) {
                unset($data['client_id']);
            }
        }

        return json_encode($data);
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);
        try {
            $this->customRequestLogger($request, $response);
        } catch (\Exception $e) {
            \Log::error("error in logger $e");
        }

        return $response;
    }

    public function terminate($request, $response)
    {
        if (! empty($request->all())) {
            \Log::info('app.requests', ['ip' => $request->ip(), 'user_id' => isset(Auth::user()->id) ? Auth::user()->id : null, 'username' => isset(Auth::user()->username) ? Auth::user()->username : null, 'uri' => ! empty($request->fullUrl()) ? $request->fullUrl() : null, 'method' => ! empty($request->method()) ? $request->method() : null, 'request' => $request->except('password', '_token')]);
        }
    }
}
