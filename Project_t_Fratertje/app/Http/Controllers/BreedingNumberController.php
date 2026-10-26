<?php
namespace App\Http\Controllers;

use App\Models\BreedingNumber;

class BreedingNumberController extends Controller
{
    public function index()
    {
        return view('breeding-numbers.index', ['numbers' => BreedingNumber::withTrashed()->with('member.memberType')->orderBy('breeding_number')->paginate(20)]);
    }
}
