<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Api\ResponseTrait;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class ApiController extends Controller
{
    use ResponseTrait;

    protected $fractal;

    public const CODE_NOT_FOUND = '404';

    public const CODE_INTERNAL_ERROR = '500';

    public const CODE_UNAUTHORIZED = '401';

    public const CODE_FORBIDDEN = '403';

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    protected function respondWithItem($item, $callback, $resourceKey = null)
    {
        $resource = new Item($item, $callback);
        $rootScope = $this->fractal->createData($resource);

        return $this->metaEncode($rootScope->toArray());
    }

    protected function respondWithCollection($collection, $callback, $resourceKey)
    {
        $paginatedCollection = $collection->getCollection();

        $resource = new Collection($paginatedCollection, $callback, $resourceKey);

        $resource->setPaginator(new IlluminatePaginatorAdapter($collection));

        $rootScope = $this->fractal->createData($resource);

        return $this->metaEncode($rootScope->toArray());
    }

    protected function respondWithOutPagination($collection, $callback, $resourceKey)
    {
        $resource = new Collection($collection, $callback, $resourceKey);
        $rootScope = $this->fractal->createData($resource);

        return $this->metaEncode($rootScope->toArray());
    }

    protected function respondWithMultipleCollection($collectionArray)
    {
        $resources['data'] = [];
        foreach ($collectionArray as $collection) {
            $resource = new Collection($collection['collection'], $collection['callback'], $collection['key']);
            if (isset($collection['setPagination']) && $collection['setPagination']) {
                $resource->setPaginator(new IlluminatePaginatorAdapter($collection['collection']));
            }
            $data = $this->fractal->createData($resource)->toArray();
            $resources['data'][$collection['key']] = $data;
        }

        return $this->metaEncode($resources);
    }

    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)
      ->respondWithError($message, self::CODE_FORBIDDEN);
    }

    public function errorInternalError($message = 'Internal Error', $code = null)
    {
        return $this->setStatusCode($code !== null ? $code : 500)
      ->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)
      ->respondWithError($message, self::CODE_NOT_FOUND);
    }

    public function responseOk($message = 'OK')
    {
        return $this->setStatusCode(200)
      ->respondWithSuccess($message);
    }
}
