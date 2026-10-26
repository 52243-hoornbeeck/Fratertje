<?php
namespace App\Http\Controllers;

use App\Models\MemberType;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberTypeController extends Controller
{
    public function index() { return view('member-types.index', ['types' => MemberType::with('rates')->orderBy('name')->get()]); }
    public function edit(MemberType $memberType) { return view('member-types.edit', ['type' => $memberType]); }
    public function update(Request $request, MemberType $memberType)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'string', 'regex:/^\d{1,5}(\.\d{1,2})?$/'],
        ]);
        DB::transaction(function () use ($memberType, $data) {
            $memberType->update(['name' => $data['name'], 'description' => $data['description']]);
            $memberType->rates()->updateOrCreate(['year' => now()->year + 1], ['amount_cents' => Money::cents($data['price'])]);
        });
        return redirect()->route('member-types.index')->with('success', 'Lidsoort bijgewerkt. Het nieuwe tarief geldt vanaf volgend kalenderjaar.');
    }
}
