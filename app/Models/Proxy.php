<?php

namespace App\Models;

use App\Models\Helpers\Uuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function Symfony\Component\Translation\t;

class Proxy extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_ERROR = 'error';

    protected $table = 'proxies';
    protected $fillable = [
        'team_id',
        'user_id',
        'type',
        'name',
        'host',
        'port',
        'login',
        'password',
        'change_ip_url',
        'status',
        'external_ip',
        'change_ip_url',
        'expiration_date',
    ];

    public function check()
    {
        try {
            $credentials = $this->login && $this->password ? "$this->login:$this->password@" : '';
            $url = "$this->type://$credentials$this->host:$this->port";
            $res = Http::withOptions([
                'timeout' => 10.0,
                "proxy" => $url
            ])->get('http://ip-api.com/json');

            $this->update([
                'status' => self::STATUS_ACTIVE,
                'external_ip' => $res->json('query')
            ]);
        } catch (\Exception $exception) {
            $this->update(['status' => self::STATUS_ERROR]);
            Log::warning($exception->getMessage());
            return false;
        }
        return true;
    }
}
