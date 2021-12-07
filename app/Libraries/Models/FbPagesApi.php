<?php

namespace App\Libraries\Models;

use App\Libraries\FbApiQuery;
use App\Models\FbAccount;

class FbPagesApi extends FbApiQuery
{
    private FbAccount $account;

    /**
     * @param FbAccount $account
     */
    public function __construct(FbAccount $account)
    {
        $this->account = $account;
    }

    public function getPagesWithPaginate(): mixed
    {
        $pages = $this->getPages();
        if (\Arr::exists($pages['paging'], 'next')) {
            $this->pagingNext($pages['paging']['next'], $pages);
        }
        return $pages;
    }

    public function getPages()
    {
        $res = $this->client(
            $this->getBaseUrl() . $this->account->facebook_id . '/accounts',
            'GET',
            [
                'fields' => 'access_token,is_published,picture{url},cover{source},name,tasks,category,category_list',
                'access_token' => $this->account->access_token
            ]
        );
        return $res->json();
    }

    public function saveData($pagesData)
    {
        $fill = collect($pagesData)->map(function ($item) {
            return array_merge($item, [
                'team_id' => $this->account->team_id,
                'user_id' => $this->account->user_id,
                'page_id' => $item['id'],
                'picture' => $item['picture']['data']['url'],
                'cover' => \Arr::exists($item, 'cover',) ? $item['cover']['source'] : null
            ]);
        });

        $this->account->pages()->delete();
        $this->account->pages()->createMany($fill);
    }

    public function getFbAccount(): FbAccount
    {
        return $this->account;
    }
}
