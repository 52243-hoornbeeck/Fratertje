<?php
namespace App\Http\Controllers;
use App\Http\Requests\MemberTypeRequest;
use App\Models\MemberType;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class MemberTypeController extends Controller {
    public function index(Request $request) {
        $request->validate(['archive'=>['nullable','in:1']]);
        $query=MemberType::with('rates');
        if ($request->input('archive')==='1') { $query->onlyTrashed(); }
        return view('member-types.index',['types'=>$query->orderBy('name')->get()]);
    }
    public function create() { return view('member-types.edit',['type'=>new MemberType]); }
    public function store(MemberTypeRequest $request) {
        $data=$request->validated();
        DB::transaction(function () use ($data) {
            $type=new MemberType(['name'=>$data['name'],'description'=>$data['description']]);
            $type->code='custom-'.Str::lower(Str::random(8));
            $type->price=Money::cents($data['price'])/100;
            $type->save();
            $type->rates()->create(['year'=>now()->year,'amount_cents'=>Money::cents($data['price'])]);
        });
        return redirect()->route('member-types.index')->with('success','De lidsoort is toegevoegd.');
    }
    public function edit(MemberType $memberType) { return view('member-types.edit',['type'=>$memberType]); }
    public function update(MemberTypeRequest $request,MemberType $memberType) {
        $data=$request->validated();
        DB::transaction(function () use ($memberType,$data) {
            $memberType->update(['name'=>$data['name'],'description'=>$data['description']]);
            $memberType->rates()->updateOrCreate(['year'=>now()->year+1],['amount_cents'=>Money::cents($data['price'])]);
        });
        return redirect()->route('member-types.index')->with('success','Lidsoort bijgewerkt. Het nieuwe tarief geldt vanaf volgend kalenderjaar.');
    }
    public function destroy(MemberType $memberType) {
        DB::transaction(function () use ($memberType) {
            $type=MemberType::lockForUpdate()->findOrFail($memberType->id);
            if ($type->members()->withTrashed()->exists()) {
                throw ValidationException::withMessages(['name'=>'Deze lidsoort is nog gekoppeld aan leden, mogelijk in het ledenarchief. Kies voor die leden eerst een andere lidsoort.']);
            }
            $type->delete();
        });
        return redirect()->route('member-types.index')->with('success','De lidsoort is gearchiveerd. Gegevens en tarieven blijven bewaard.');
    }
    public function restore(string $memberType) {
        MemberType::onlyTrashed()->findOrFail($memberType)->restore();
        return redirect()->route('member-types.index')->with('success','De lidsoort is teruggezet uit het archief.');
    }
}
