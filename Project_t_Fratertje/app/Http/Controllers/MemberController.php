<?php
namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest;
use App\Models\BreedingNumber;
use App\Models\Member;
use App\Models\MemberType;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'member_type_id' => ['nullable', 'integer', 'exists:member_types,id'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'archive' => ['nullable', Rule::in(['1'])],
        ]);
        $query = Member::with(['memberType', 'address', 'breedingNumber']);
        if ($request->input('archive') === '1') { $query->onlyTrashed(); }
        if ($request->filled('q')) {
            $term = '%'.$filters['q'].'%';
            $query->where(function ($query) use ($term) {
                $query->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)
                    ->orWhereHas('breedingNumber', fn ($q) => $q->where('breeding_number', 'like', $term));
            });
        }
        if ($request->filled('member_type_id')) { $query->where('member_type_id', $filters['member_type_id']); }
        if ($request->filled('status')) { $query->where('is_active', $filters['status'] === 'active'); }
        return view('members.index', ['members' => $query->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString(), 'types' => $this->types()]);
    }

    private function types() { return MemberType::whereIn('code', ['adult', 'youth', 'guest'])->orderBy('name')->get(); }
    public function create() { return view('members.form', ['member' => new Member(['is_active' => true]), 'types' => $this->types()]); }
    public function store(MemberRequest $request, MemberService $service)
    {
        $member = $service->save($request->validated());
        return redirect()->route('members.show', $member)->with('success', 'Het lid is toegevoegd.');
    }
    public function show(Member $member)
    {
        $member->load(['memberType', 'address', 'breedingNumber']);
        $oldNumber = BreedingNumber::onlyTrashed()->where('member_id', $member->id)->first();
        return view('members.show', compact('member', 'oldNumber'));
    }
    public function edit(Member $member) { return view('members.form', ['member' => $member->load(['address', 'breedingNumber']), 'types' => $this->types()]); }
    public function update(MemberRequest $request, Member $member, MemberService $service)
    {
        $service->save($request->validated(), $member);
        return redirect()->route('members.show', $member)->with('success', 'De gegevens zijn bijgewerkt.');
    }
    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')->with('success', 'Het lid staat nu in het archief. De gegevens en het kweeknummer zijn bewaard.');
    }
    public function restore(string $member)
    {
        $record = Member::onlyTrashed()->findOrFail($member);
        $record->restore();
        return redirect()->route('members.show', $record)->with('success', 'Het lid is teruggezet. Controleer de lidsoort als de leeftijd inmiddels is veranderd.');
    }
}
