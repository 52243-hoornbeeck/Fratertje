<?php
namespace Tests\Feature;

use App\Models\BreedingNumber;
use App\Models\Member;
use App\Models\MemberType;
use App\Models\User;
use App\Services\MemberService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AdministrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now()->setDate(2026, 9, 29)->setTime(12, 0));
        $this->seed(DatabaseSeeder::class);
    }

    private function signIn(): void { $this->actingAs(User::where('email', 'beheerder@fratertje.test')->firstOrFail()); }
    private function payload(string $code = 'adult', string $number = 'AB12'): array
    {
        return [
            'first_name' => 'Robin', 'last_name' => 'Vogel',
            'birth_date' => $code === 'youth' ? '2012-01-01' : '1990-01-01',
            'member_type_id' => MemberType::where('code', $code)->value('id'), 'is_active' => '1',
            'street' => 'Vogelstraat', 'house_number' => '12A', 'postal_code' => '2801 AB', 'city' => 'Gouda',
            'breeding_number' => $code === 'guest' ? null : $number, 'issue_year' => $code === 'guest' ? null : '2026',
        ];
    }

    public function test_administration_requires_authentication(): void
    {
        foreach (['/dashboard', '/members', '/members/create', '/member-types', '/breeding-numbers'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/members', $this->payload())->assertRedirect('/login');
        $this->assertDatabaseCount('members', 0);
    }

    public function test_login_logout_and_wrong_password(): void
    {
        $this->post('/login', ['email' => 'beheerder@fratertje.test', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', ['email' => 'beheerder@fratertje.test', 'password' => 'Fratertje-Demo-2026!'])->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_member_address_and_number_are_saved_together_and_pages_render(): void
    {
        $this->signIn();
        $this->post('/members', $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $member = Member::firstOrFail();
        $this->assertSame('Gouda', $member->address->city);
        $this->assertSame('AB12', $member->breedingNumber->breeding_number);
        foreach (['/dashboard', '/members', '/members/create', "/members/{$member->id}", "/members/{$member->id}/edit", '/member-types', '/breeding-numbers', '/member-types/'.$member->member_type_id.'/edit'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->put('/members/'.$member->id, array_replace($this->payload(), ['city' => 'Utrecht']))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('addresses', 1);
        $this->assertSame('Utrecht', $member->fresh()->address->city);
    }

    public function test_guest_cannot_have_number_and_adult_cannot_omit_it(): void
    {
        $this->signIn();
        $this->post('/members', array_replace($this->payload('guest'), ['breeding_number' => 'XX99']))->assertSessionHasErrors('breeding_number');
        $this->post('/members', array_replace($this->payload(), ['breeding_number' => '']))->assertSessionHasErrors('breeding_number');
        $this->post('/members', array_replace($this->payload(), ['issue_year' => '']))->assertSessionHasErrors('issue_year');
        $this->assertDatabaseCount('members', 0);
        $this->assertDatabaseCount('addresses', 0);
        $this->post('/members', $this->payload('guest'))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('breeding_numbers', 0);
    }

    public function test_age_boundary_is_validated_on_eighteenth_birthday(): void
    {
        $this->signIn();
        $this->post('/members', array_replace($this->payload('youth'), ['birth_date' => '2008-09-29']))->assertSessionHasErrors('member_type_id');
        $this->post('/members', array_replace($this->payload('adult'), ['birth_date' => '2008-09-30']))->assertSessionHasErrors('member_type_id');
        $this->post('/members', array_replace($this->payload('adult'), ['birth_date' => '2008-09-29']))->assertSessionHasNoErrors();
        $this->post('/members', array_replace($this->payload('youth', 'CD34'), ['birth_date' => '2008-09-30']))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('members', 2);
    }

    public function test_invalid_birthdate_and_duplicate_normalized_number_are_rejected(): void
    {
        $this->signIn();
        $this->post('/members', array_replace($this->payload(), ['birth_date' => '2026-02-30']))->assertSessionHasErrors('birth_date');
        $this->post('/members', $this->payload())->assertSessionHasNoErrors();
        $this->post('/members', $this->payload('adult', 'ab12'))->assertSessionHasErrors('breeding_number');
        $this->assertDatabaseCount('members', 1);
    }

    public function test_soft_delete_keeps_details_and_reserves_number_and_restore_works(): void
    {
        $this->signIn();
        $this->post('/members', $this->payload());
        $member = Member::firstOrFail();
        $this->delete('/members/'.$member->id)->assertRedirect('/members');
        $this->assertSoftDeleted('members', ['id' => $member->id]);
        $this->get('/members')->assertDontSee('Robin Vogel');
        $this->get('/members?archive=1')->assertSee('Robin Vogel');
        $this->get('/members/'.$member->id)->assertOk()->assertSee('Gearchiveerd');
        $this->get('/members/'.$member->id.'/edit')->assertNotFound();
        $this->post('/members', $this->payload())->assertSessionHasErrors('breeding_number');
        $this->post('/members/'.$member->id.'/restore')->assertRedirect('/members/'.$member->id);
        $this->assertNotSoftDeleted('members', ['id' => $member->id]);
        $this->assertDatabaseCount('addresses', 1);
        $this->assertDatabaseCount('breeding_numbers', 1);
    }

    public function test_guest_conversion_archives_number_and_conversion_back_restores_it(): void
    {
        $this->signIn();
        $this->post('/members', $this->payload());
        $member = Member::firstOrFail();
        $this->put('/members/'.$member->id, $this->payload('guest'))->assertSessionHasNoErrors();
        $this->assertSoftDeleted('breeding_numbers', ['member_id' => $member->id]);
        $this->assertNull($member->fresh()->breedingNumber);
        $this->post('/members', $this->payload())->assertSessionHasErrors('breeding_number');
        $this->put('/members/'.$member->id, $this->payload())->assertSessionHasNoErrors();
        $this->assertNotSoftDeleted('breeding_numbers', ['member_id' => $member->id]);
        $this->assertDatabaseCount('breeding_numbers', 1);
    }

    public function test_search_and_filters_are_combined(): void
    {
        $this->signIn();
        $this->post('/members', $this->payload());
        $this->post('/members', array_replace($this->payload('guest'), ['first_name' => 'Sam', 'last_name' => 'Merel', 'is_active' => '0']));
        $this->get('/members?q=AB12')->assertSee('Robin Vogel')->assertDontSee('Sam Merel');
        $this->get('/members?status=inactive')->assertSee('Sam Merel')->assertDontSee('Robin Vogel');
        $guest = MemberType::where('code', 'guest')->value('id');
        $this->get('/members?member_type_id='.$guest.'&status=active')->assertDontSee('Sam Merel')->assertDontSee('Robin Vogel');
    }

    public function test_price_change_only_affects_next_year_and_seeding_preserves_it(): void
    {
        $this->signIn();
        $type = MemberType::where('code', 'adult')->firstOrFail();
        $this->put('/member-types/'.$type->id, ['name' => 'Volwassen lid', 'description' => 'NBvV-lid', 'price' => '42.50'])->assertSessionHasNoErrors();
        $this->assertSame(3600, $type->rateForYear(2026)->amount_cents);
        $this->assertSame(4250, $type->rateForYear(2027)->amount_cents);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(4250, $type->rateForYear(2027)->amount_cents);
        $this->assertDatabaseCount('member_types', 3);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_database_unique_constraint_rolls_back_entire_member_transaction(): void
    {
        $service = app(MemberService::class);
        $service->save($this->payload());
        try {
            // Deliberately bypass the request to exercise the database race fallback.
            $service->save(array_replace($this->payload(), ['first_name' => 'Duplicate']));
            $this->fail('A duplicate number should fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('breeding_number', $exception->errors());
        }
        $this->assertDatabaseCount('members', 1);
        $this->assertDatabaseCount('addresses', 1);
        $this->assertDatabaseCount('breeding_numbers', 1);
    }
}
