<?php
namespace Tests\Feature;
use App\Models\{Member,MemberType,BreedingNumber,User};
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudCompletionTest extends TestCase
{
    use RefreshDatabase;
    protected function setUp(): void {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }
    private function signIn(): void { $this->actingAs(User::factory()->create()); }
    private function member(array $overrides=[]): Member {
        $data=array_replace([
            'first_name'=>'Demo','last_name'=>'Testlid','birth_date'=>'1990-01-01',
            'member_type_id'=>MemberType::where('code','guest')->value('id'),'is_active'=>'0',
            'street'=>'Teststraat','house_number'=>'1','postal_code'=>'1234 AB','city'=>'Teststad',
            'breeding_number'=>'','issue_year'=>null,
        ],$overrides);
        $this->post('/members',$data)->assertSessionHasNoErrors();
        return Member::latest('id')->firstOrFail();
    }
    public function test_member_type_full_lifecycle_and_archive_seeding(): void {
        $this->signIn();
        $this->get('/member-types/create')->assertOk();
        $this->post('/member-types',['name'=>'Erelid','description'=>'Ereleden','price'=>'21.00'])->assertSessionHasNoErrors();
        $type=MemberType::where('name','Erelid')->firstOrFail();
        $this->assertSame(2100,$type->rateForYear(now()->year)->amount_cents);
        $this->get('/member-types/'.$type->id.'/edit')->assertOk();
        $this->put('/member-types/'.$type->id,['name'=>'Erelid gewijzigd','description'=>'Nieuwe omschrijving','price'=>'25.00'])->assertSessionHasNoErrors();
        $this->assertSame(2100,$type->rateForYear(now()->year)->amount_cents);
        $this->assertSame(2500,$type->rateForYear(now()->year+1)->amount_cents);
        $this->delete('/member-types/'.$type->id)->assertSessionHasNoErrors();
        $this->assertSoftDeleted('member_types',['id'=>$type->id]);
        $this->get('/member-types')->assertDontSee('Erelid gewijzigd');
        $this->get('/member-types?archive=1')->assertSee('Erelid gewijzigd');
        $this->post('/member-types/'.$type->id.'/restore')->assertSessionHasNoErrors();
        $this->assertNotSoftDeleted('member_types',['id'=>$type->id]);
        $guest=MemberType::where('code','guest')->firstOrFail();
        $this->delete('/member-types/'.$guest->id)->assertSessionHasNoErrors();
        $this->seed(DatabaseSeeder::class);
        $this->assertSoftDeleted('member_types',['id'=>$guest->id]);
        $this->assertSame(1,MemberType::withTrashed()->where('code','guest')->count());
    }
    public function test_linked_member_type_cannot_be_archived_even_for_archived_members(): void {
        $this->signIn();$member=$this->member();
        $this->delete('/member-types/'.$member->member_type_id)->assertSessionHasErrors('name');
        $member->delete();
        $this->delete('/member-types/'.$member->member_type_id)->assertSessionHasErrors('name');
        $this->assertNotSoftDeleted('member_types',['id'=>$member->member_type_id]);
    }
    public function test_archived_type_cannot_be_assigned_and_custom_active_type_requires_number(): void {
        $this->signIn();
        $type=MemberType::where('code','guest')->firstOrFail();$type->delete();
        $this->post('/members',['member_type_id'=>$type->id])->assertSessionHasErrors('member_type_id');
        $this->post('/member-types',['name'=>'Nieuw','description'=>'Nieuwe soort','price'=>'10.00'])->assertSessionHasNoErrors();
        $custom=MemberType::where('name','Nieuw')->firstOrFail();
        $this->post('/members',['first_name'=>'Demo','last_name'=>'Actief','birth_date'=>'1990-01-01','member_type_id'=>$custom->id,'is_active'=>'1','street'=>'Teststraat','house_number'=>'1','postal_code'=>'1234 AB','city'=>'Teststad'])->assertSessionHasErrors('breeding_number');
        $this->assertDatabaseCount('members',0);
    }
    public function test_standalone_number_full_crud_unique_archive_and_restore(): void {
        $this->signIn();$first=$this->member();$second=$this->member(['first_name'=>'Tweede']);
        $this->get('/breeding-numbers/create')->assertOk()->assertSee($first->full_name);
        $this->post('/breeding-numbers',['member_id'=>$first->id,'breeding_number'=>'nb123','issue_year'=>'2026'])->assertSessionHasNoErrors();
        $number=BreedingNumber::firstOrFail();
        $this->assertSame('NB123',$number->breeding_number);
        $this->get('/breeding-numbers/'.$number->id)->assertOk()->assertSee('NB123');
        $this->get('/breeding-numbers/'.$number->id.'/edit')->assertOk();
        $this->put('/breeding-numbers/'.$number->id,['member_id'=>$first->id,'breeding_number'=>'NB456','issue_year'=>'2025'])->assertSessionHasNoErrors();
        $this->assertSame('NB456',$number->fresh()->breeding_number);
        $this->post('/breeding-numbers',['member_id'=>$second->id,'breeding_number'=>'nb456','issue_year'=>'2026'])->assertSessionHasErrors('breeding_number');
        $first->update(['is_active'=>true]);
        $this->delete('/breeding-numbers/'.$number->id)->assertSessionHasNoErrors();
        $this->assertFalse($first->fresh()->is_active);
        $this->assertSoftDeleted('breeding_numbers',['id'=>$number->id]);
        $this->get('/breeding-numbers?archive=1')->assertSee('NB456');
        $this->get('/breeding-numbers/'.$number->id)->assertOk()->assertSee('Gearchiveerd');
        $this->post('/breeding-numbers',['member_id'=>$second->id,'breeding_number'=>'NB456','issue_year'=>'2026'])->assertSessionHasErrors('breeding_number');
        $this->post('/breeding-numbers',['member_id'=>$first->id,'breeding_number'=>'OTHER','issue_year'=>'2026'])->assertSessionHasErrors('member_id');
        $this->post('/breeding-numbers/'.$number->id.'/restore')->assertSessionHasNoErrors();
        $this->assertNotSoftDeleted('breeding_numbers',['id'=>$number->id]);
        $this->assertFalse($first->fresh()->is_active);
        $this->assertDatabaseCount('breeding_numbers',1);
    }
    public function test_number_validation_and_member_link_cannot_be_changed(): void {
        $this->signIn();$member=$this->member();$other=$this->member(['first_name'=>'Ander']);
        $this->post('/breeding-numbers',['member_id'=>$member->id,'breeding_number'=>'!','issue_year'=>now()->year+1])->assertSessionHasErrors(['breeding_number','issue_year']);
        $this->post('/breeding-numbers',['member_id'=>$member->id,'breeding_number'=>'GOOD','issue_year'=>'2026'])->assertSessionHasNoErrors();
        $number=BreedingNumber::firstOrFail();
        $this->put('/breeding-numbers/'.$number->id,['member_id'=>$other->id,'breeding_number'=>'GOOD','issue_year'=>'2026'])->assertSessionHasErrors('member_id');
        $this->assertSame($member->id,$number->fresh()->member_id);
    }
    public function test_new_management_routes_require_authentication(): void {
        foreach(['/member-types/create','/breeding-numbers/create','/breeding-numbers/1/edit'] as $url) { $this->get($url)->assertRedirect('/login'); }
        foreach(['/member-types','/breeding-numbers','/member-types/1/restore','/breeding-numbers/1/restore'] as $url) { $this->post($url,[])->assertRedirect('/login'); }
        $this->delete('/member-types/1')->assertRedirect('/login');
        $this->delete('/breeding-numbers/1')->assertRedirect('/login');
    }
}
