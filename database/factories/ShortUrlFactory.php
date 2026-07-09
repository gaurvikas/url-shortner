<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    public function definition(): array
    {
        $company = Company::factory()->create();
        $user = User::factory()->for($company)->create();

        return [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'original_url' => fake()->url(),
            'short_code' => Str::random(7),
            'visits' => 0,
        ];
    }
}
