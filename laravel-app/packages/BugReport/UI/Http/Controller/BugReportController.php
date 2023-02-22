<?php

namespace BugReport\UI\Http\Controller;

use App\Http\Controllers\Controller;
use App\Models\Bug;
use BugReport\Domain\Type\Status;
use BugReport\Infrastructure\Query\BugReportSearchQuery;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use PDO;

/**
 * バグレポート機能 Controller
 */
class BugReportController extends Controller
{
    /**
     * all
     */
    public function all()
    {
        // DB接続情報
        $dsn = 'mysql:'
            . 'dbname=' . config('database.connections.mysql.database') . ';'
            . 'host=' . config('database.connections.mysql.host') . ';'
            . 'port=' . config('database.connections.mysql.port');
        $user     = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        // Query生成
        $conn = new PDO($dsn, $user, $password);
        $query = new BugReportSearchQuery($conn);

        // 検索条件
        $now = CarbonImmutable::now();
        $from_day = $now->addDays(-30);
        $to_day   = $now;

        // レスポンス
        return $query->findAll([
            'startAt' => $from_day,
            'endAt'   => $to_day,
            'status'  => Status::NEW->value
        ])->map(function ($entity) {
            return [
                'id' => $entity->id,
                'summary' => Str::limit($entity->summary, 50, '...'),
                'reported_at' => CarbonImmutable::parse($entity->reported_at)->format('Y-m-d'),
            ];
        });
    }

    /**
     * index
     */
    public function index()
    {
        // 検索条件
        $now = CarbonImmutable::now();
        $from_day = $now->addDays(-30);
        $to_day   = $now;

        // 検索
        $bugs = Bug::select(['id', 'summary', 'reported_at'])
            ->where('reported_at', '>=', $from_day)
            ->where('reported_at', '<', $to_day)
            ->where('status', '=', Status::NEW->value)
            ->get();

        // レスポンス
        return $bugs->map(function ($entity) {
            return [
                'id' => $entity->id,
                'summary' => Str::limit($entity->summary, 50, '...'),
                'reported_at' => CarbonImmutable::parse($entity->reported_at)->format('Y-m-d'),
            ];
        });
    }
}
