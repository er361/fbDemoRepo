<?php

namespace App\Libraries;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class FbApiQuery
{
    const BASE_URL = "https://graph.facebook.com/v12.0/";

    /**
     * @param $url
     * @param $method
     * @param $reqData
     * @param array $options
     * @return \GuzzleHttp\Promise\PromiseInterface|Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function client($url, $method, $reqData, $options = [])
    {
        $res = match ($method) {
            'GET' => \Http::withOptions($options)->get($url, $reqData),
            'POST' => \Http::withOptions($options)->post($url, $reqData),
            'PUT' => \Http::withOptions($options)->put($url, $reqData),
            'DELETE' => \Http::withOptions($options)->delete($url, $reqData),
        };

        $error = \Arr::exists($res->json(), 'error');
        $res->throwIf($error);

        return $res;
    }

    public function getBaseUrl()
    {
        return self::BASE_URL;
    }

    protected function withPaginate($data)
    {
        if (\Arr::exists($data['paging'], 'next')) {
            $this->pagingNext($data['paging']['next'], $data);
        }
        return $data;
    }

    protected function pagingNext($nextUrl, array &$populateArr)
    {
        $nextPage = $this->getNextPage($nextUrl);
        if (\Arr::exists($nextPage['paging'], 'next')) {
            $populateArr = collect($populateArr)->concat($nextPage['data'])->toArray();

            $this->pagingNext($nextPage['paging']['next'], $populateArr);
        } else {
            $populateArr = collect($populateArr)->concat($nextPage['data'])->toArray();
        }
    }

    protected function getNextPage($url)
    {
        $res = \Http::get($url);

        if (Arr::exists($res->json(), 'error')) {
            $this->handleError($res);
        }

        return $res->json();
    }

    /**
     * @param Response $res
     * @throws \Exception
     */
    protected function handleError(Response $res): void
    {
        throw new \Exception($res->json()['error']['message']);
    }
}
