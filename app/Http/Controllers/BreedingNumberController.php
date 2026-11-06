<?php
namespace App\Http\Controllers;
use App\Http\Requests\BreedingNumberRequest;
use App\Models\BreedingNumber;
use App\Models\Member;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class BreedingNumberController extends Controller {
    public function index(Request $request) {
        $request->validate(['archive'=>['nullable','in:1']]);
        $query=BreedingNumber::with('member.memberType');
        if ($request->input('archive')==='1') { $query->onlyTrashed(); }
        return view('breeding-numbers.index',['numbers'=>$query->orderBy('breeding_number')->paginate(20)->withQueryString()]);
    }
    public function create() {
        return view('breeding-numbers.form',['number'=>new BreedingNumber,'members'=>Member::whereDoesntHave('breedingNumber',fn($q)=>$q->withTrashed())->orderBy('last_name')->get()]);
    }
    public function store(BreedingNumberRequest $request) {
        $data=$request->validated();
        $number=$this->conflictSafe(fn()=>DB::transaction(function () use ($data) {
            $member=Member::lockForUpdate()->findOrFail($data['member_id']);
            return $member->breedingNumber()->create(['breeding_number'=>$data['breeding_number'],'issue_year'=>$data['issue_year']]);
        }));
        return redirect()->route('breeding-numbers.show',$number)->with('success','Het kweeknummer is toegevoegd.');
    }
    public function show(BreedingNumber $breedingNumber) { return view('breeding-numbers.show',['number'=>$breedingNumber->load('member')]); }
    public function edit(BreedingNumber $breedingNumber) { return view('breeding-numbers.form',['number'=>$breedingNumber->load('member'),'members'=>collect()]); }
    public function update(BreedingNumberRequest $request,BreedingNumber $breedingNumber) {
        $data=$request->validated();
        $this->conflictSafe(fn()=>DB::transaction(function () use ($data,$breedingNumber) {
            Member::withTrashed()->lockForUpdate()->findOrFail($breedingNumber->member_id);
            BreedingNumber::lockForUpdate()->findOrFail($breedingNumber->id)->update(['breeding_number'=>$data['breeding_number'],'issue_year'=>$data['issue_year']]);
        }));
        return redirect()->route('breeding-numbers.show',$breedingNumber)->with('success','Het kweeknummer is bijgewerkt.');
    }
    public function destroy(BreedingNumber $breedingNumber) {
        DB::transaction(function () use ($breedingNumber) {
            $member=Member::withTrashed()->lockForUpdate()->findOrFail($breedingNumber->member_id);
            $member->update(['is_active'=>false]);
            BreedingNumber::lockForUpdate()->findOrFail($breedingNumber->id)->delete();
        });
        return redirect()->route('breeding-numbers.index')->with('success','Het nummer is gearchiveerd en blijft gereserveerd. Het lid is inactief; de overige gegevens zijn bewaard.');
    }
    public function restore(string $breedingNumber) {
        DB::transaction(function () use ($breedingNumber) {
            $number=BreedingNumber::onlyTrashed()->findOrFail($breedingNumber);
            Member::withTrashed()->lockForUpdate()->findOrFail($number->member_id);
            BreedingNumber::onlyTrashed()->lockForUpdate()->findOrFail($number->id)->restore();
        });
        return redirect()->route('breeding-numbers.show',$breedingNumber)->with('success','Het kweeknummer is hersteld. De status van het lid blijft ongewijzigd.');
    }
    private function conflictSafe(callable $action) {
        try { return $action(); }
        catch (QueryException $e) {
            if (in_array((string)$e->getCode(),['23000','23505'],true)) { throw ValidationException::withMessages(['breeding_number'=>'Dit nummer of lid heeft al een koppeling. Vernieuw de pagina en controleer het nummerarchief.']); }
            throw $e;
        }
    }
}
