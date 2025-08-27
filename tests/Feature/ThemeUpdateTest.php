<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemeUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_update_themes_in_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->put(route('theme.update'), [
            'theme' => 'halloween',
        ])->assertValid()->assertRedirect();

        $this->assertEquals('halloween', session('theme'));
    }
}
