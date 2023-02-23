<?php

namespace Database\Factories;

use App\Models\User;
use BugReport\Domain\Type\Status;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bug>
 */
class BugFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if ((User::all())->count() === 0) {
            User::factory()->create();
        }
        $now         = CarbonImmutable::now();
        $user_id     = $this->faker->randomElement(User::all()->pluck('name', 'id')->keys());
        $status      = $this->faker->randomElement(collect(Status::cases())->pluck('name', 'value')->keys()->toArray());
        $reported_at = $now->addDays($this->faker->numberBetween(-30, 30));

        return [
            'status'      => $status,
            'summary'     => $this->faker->realText(200),
            'reported_by' => $user_id,
            'reported_at' => $reported_at,
            'created_by'  => $user_id,
            'created_at'  => $reported_at,
            'updated_by'  => $user_id,
            'updated_at'  => $reported_at,
        ];
    }
}
