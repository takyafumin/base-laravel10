<?php

namespace Tests\Feature\BugReport\UI\Http\Controller;

use App\Models\Bug;
use BugReport\Domain\Type\Status;
use BugReport\UI\Http\Controller\BugReportController;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

/**
 * BugReportController::index() Test
 */
class BugReportControllerIndexTest extends TestCase
{
    use WithFaker;

    /**
     * Test 正常処理
     */
    public function test_正常処理(): void
    {
        $now = CarbonImmutable::now();
        $from_day = $now->addDays(-30);
        $to_day   = $now;
        $testDatalist = $this->setupTestData($from_day, $to_day);
        $expect = $testDatalist->filter(function ($entity) use ($from_day, $to_day) {
            $reported_at = $entity['reported_at'];
            $status      = $entity['status'];
            if ($reported_at->lt($from_day)) {
                return false;
            }
            if ($reported_at->gte($to_day)) {
                return false;
            }
            if ($status !== Status::NEW->value) {
                return false;
            }
            return true;
        })
        ->values()
        ->map(function ($entity) {
            return [
                'id'          => $entity['id'],
                'summary'     => Str::limit($entity['summary'], 50, '...'),
                'reported_at' => ($entity['reported_at'])->format('Y-m-d'),
            ];
        });
        $target = new BugReportController();
        $response = $target->index();

        assertEquals($expect->count(), $response->count(), 'レスポンスの件数が同じではない');
        assertEquals($expect, $response, 'レスポンスが同じではない');
    }

    /**
     * Setup Test Data
     *
     * @param CarbonImmutable $from_day
     * @param CarbonImmutable $to_day
     * @return Collection
     */
    private function setupTestData(
        CarbonImmutable $from_day,
        CarbonImmutable $to_day
    ): Collection {
        $list = new Collection();
        // fromより1日前
        $list->add([
            'id'          => 1,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $from_day->adddays(-1),
            'status'      => Status::NEW->value
        ]);
        // fromと同日
        $list->add([
            'id'          => 2,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $from_day,
            'status'      => Status::NEW->value
        ]);
        // fromの翌日
        $list->add([
            'id'          => 3,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $from_day->adddays(1),
            'status'      => Status::NEW->value
        ]);
        // fromと同日でステータスが新規以外(処理中)
        $list->add([
            'id'          => 4,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $from_day,
            'status'      => Status::DOING->value
        ]);
        // toの前日
        $list->add([
            'id'          => 5,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $to_day->addDays(-1),
            'status'      => Status::NEW->value
        ]);
        // toと同日
        $list->add([
            'id'          => 6,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $to_day,
            'status'      => Status::DONE->value,
        ]);
        // toの前日でステータスが新規以外(処理済)
        $list->add([
            'id'          => 7,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $to_day,
            'status'      => Status::DONE->value
        ]);
        // toの翌日
        $list->add([
            'id'          => 8,
            'summary'     => $this->faker->realText(1024),
            'reported_at' => $to_day->addDays(1),
            'status'      => Status::NEW->value
        ]);
        foreach ($list as $value) {
            Bug::factory()->create($value);
        }

        return $list;
    }
}
