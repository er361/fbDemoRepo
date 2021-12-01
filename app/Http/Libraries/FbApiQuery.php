<?php

namespace App\Http\Libraries;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

class FbApiQuery
{
    const BASE_URL = "https://graph.facebook.com/v12.0/";

    public function client($url, $method, $options)
    {
        $res = match ($method) {
            'GET' => \Http::withOptions($options)->get($url),
            'POST' => \Http::withOptions($options)->post($url),
            'PUT' => \Http::withOptions($options)->put($url),
            'DELETE' => \Http::withOptions($options)->delete($url),
        };

        $error = \Arr::exists($res->json(), 'error');
        $res->throwIf($error);

        return $res;
    }

    public function getBaseUrl()
    {
        return self::BASE_URL;
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
