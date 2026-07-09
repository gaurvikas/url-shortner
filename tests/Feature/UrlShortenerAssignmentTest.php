<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();

        $this->actingAs($admin)
            ->post(route('short-urls.store'), ['original_url' => 'https://example.com/long-url'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('short_urls', [
            'company_id' => $company->id,
            'user_id' => $admin->id,
            'original_url' => 'https://example.com/long-url',
        ]);
    }

    public function test_member_can_create_short_urls(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member()->for($company)->create();

        $this->actingAs($member)
            ->post(route('short-urls.store'), ['original_url' => 'https://laravel.com/docs'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('short_urls', [
            'company_id' => $company->id,
            'user_id' => $member->id,
            'original_url' => 'https://laravel.com/docs',
        ]);
    }

    public function test_super_admin_cannot_create_short_urls(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('short-urls.store'), ['original_url' => 'https://example.com'])
            ->assertForbidden();

        $this->assertDatabaseCount('short_urls', 0);
    }

    public function test_admin_only_sees_short_urls_from_their_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();
        $companyUser = User::factory()->member()->for($company)->create();
        $otherUser = User::factory()->member()->for($otherCompany)->create();

        $visible = ShortUrl::factory()->for($company)->for($companyUser, 'user')->create([
            'original_url' => 'https://visible.example.com',
        ]);
        $hidden = ShortUrl::factory()->for($otherCompany)->for($otherUser, 'user')->create([
            'original_url' => 'https://hidden.example.com',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee($visible->original_url);
        $response->assertDontSee($hidden->original_url);
    }

    public function test_member_only_sees_their_own_short_urls(): void
    {
        $company = Company::factory()->create();
        $member = User::factory()->member()->for($company)->create();
        $otherMember = User::factory()->member()->for($company)->create();

        $visible = ShortUrl::factory()->for($company)->for($member, 'user')->create([
            'original_url' => 'https://mine.example.com',
        ]);
        $hidden = ShortUrl::factory()->for($company)->for($otherMember, 'user')->create([
            'original_url' => 'https://theirs.example.com',
        ]);

        $response = $this->actingAs($member)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee($visible->original_url);
        $response->assertDontSee($hidden->original_url);
    }

    public function test_short_urls_are_publicly_resolvable_and_redirect(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->member()->for($company)->create();
        $shortUrl = ShortUrl::factory()->for($company)->for($user, 'user')->create([
            'short_code' => 'AbC123x',
            'original_url' => 'https://example.com/destination',
            'visits' => 0,
        ]);

        $this->get('/'.$shortUrl->short_code)
            ->assertRedirect('https://example.com/destination');

        $this->assertDatabaseHas('short_urls', [
            'id' => $shortUrl->id,
            'visits' => 1,
        ]);
    }

    public function test_super_admin_can_invite_admin_in_new_company(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('invitations.store'), [
                'company_name' => 'Sembark Tech',
                'name' => 'Client Admin',
                'email' => 'client-admin@example.com',
                'role' => User::ROLE_ADMIN,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('companies', ['name' => 'Sembark Tech']);
        $this->assertDatabaseHas('invitations', [
            'email' => 'client-admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_admin_can_invite_admin_or_member_in_their_company(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();

        $this->actingAs($admin)
            ->post(route('invitations.store'), [
                'name' => 'Team Member',
                'email' => 'member@example.com',
                'role' => User::ROLE_MEMBER,
            ])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($admin)
            ->post(route('invitations.store'), [
                'name' => 'Team Admin',
                'email' => 'admin@example.com',
                'role' => User::ROLE_ADMIN,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'member@example.com',
            'role' => User::ROLE_MEMBER,
        ]);
        $this->assertDatabaseHas('invitations', [
            'company_id' => $company->id,
            'email' => 'admin@example.com',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function test_invited_user_can_accept_invitation(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->for($company)->create();
        $invitation = Invitation::create([
            'company_id' => $company->id,
            'name' => 'Invited Member',
            'email' => 'invited@example.com',
            'role' => User::ROLE_MEMBER,
            'token' => 'test-token',
            'invited_by' => $admin->id,
        ]);

        $this->post(route('invitations.accept', $invitation->token), [
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'company_id' => $company->id,
            'email' => 'invited@example.com',
            'role' => User::ROLE_MEMBER,
        ]);
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    public function test_super_admin_invites_admin_who_accepts_logs_in_and_invites_member(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->post(route('invitations.store'), [
                'company_name' => 'Client Company',
                'name' => 'Client Admin',
                'email' => 'client-admin@example.com',
                'role' => User::ROLE_ADMIN,
            ])
            ->assertRedirect(route('dashboard'));

        $adminInvitation = Invitation::where('email', 'client-admin@example.com')->firstOrFail();

        $this->post(route('invitations.accept', $adminInvitation->token), [
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard'));

        $acceptedAdmin = User::where('email', 'client-admin@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($acceptedAdmin);
        $this->assertTrue($acceptedAdmin->isAdmin());
        $this->assertNotNull($adminInvitation->fresh()->accepted_at);

        $this->post(route('logout'));
        $this->assertGuest();

        $this->post(route('login'), [
            'email' => 'client-admin@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($acceptedAdmin);

        $this->post(route('invitations.store'), [
            'name' => 'Client Member',
            'email' => 'client-member@example.com',
            'role' => User::ROLE_MEMBER,
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('invitations', [
            'company_id' => $acceptedAdmin->company_id,
            'email' => 'client-member@example.com',
            'role' => User::ROLE_MEMBER,
        ]);
    }
}
