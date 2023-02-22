<?php

namespace BugReport\Infrastructure\Query;

use App\Models\Bug;
use PDO;

/**
 * バグレポート検索クエリ
 */
class BugReportSearchQuery
{
    /**
     * コンストラクタ
     *
     * @param PDO $pdo
     */
    public function __construct(
        private PDO $pdo
    ) {
    }

    /**
     * 全件検索
     */
    public function findAll($params)
    {
        // SQL
        $sql = 'SELECT id, summary, reported_at FROM bugs WHERE reported_at >= :startAt AND reported_at < :endAt AND status = :status';

        // Exec
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return collect($stmt->fetchAll(\PDO::FETCH_CLASS, Bug::class));
    }
}
