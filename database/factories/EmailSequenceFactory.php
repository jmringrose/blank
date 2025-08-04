<?php

namespace Database\Factories;

use App\Models\EmailSequence;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class EmailSequenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailSequence::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'first' => $this->faker->unique()->firstName(),
            'last' => $this->faker->unique()->lastName(),
            'unsub_token' => $this->faker->unique()->md5(),
            'current_step' => $this->faker->numberBetween(1, 5), // Random step between 1 and 5
            'next_send_at' => Carbon::now()->addDays($this->faker->numberBetween(-3, 7)), // Random future date
        ];
    }
}
