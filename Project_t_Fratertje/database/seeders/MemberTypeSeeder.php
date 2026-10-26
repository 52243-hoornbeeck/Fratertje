<?php
namespace Database\Seeders;

use App\Models\MemberType;
use Illuminate\Database\Seeder;

class MemberTypeSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['adult', 'Volwassen lid', 'Vanaf 18 jaar; verplicht NBvV-lid.', 3600, ['Volwassen lid', 'Volwassenlid', 'Standaardlid', 'Seniorlid', 'Seniorenlid']],
            ['youth', 'Jeugdlid', 'Tot 18 jaar; verplicht NBvV-lid.', 1800, ['Jeugdlid', 'Jeugd lid', 'Juniorlid']],
            ['guest', 'Gastlid', 'Alle leeftijden; geen NBvV-lid.', 1800, ['Gastlid', 'Gast lid']],
        ];
        foreach ($defaults as [$code, $name, $description, $cents, $aliases]) {
            $type = MemberType::where('code', $code)->first();
            if ($type) { continue; }
            // Reuse recognized legacy records so existing foreign keys remain intact.
            $type = MemberType::whereNull('code')->whereIn('name', $aliases)->orderBy('id')->first();
            if (! $type) {
                $type = new MemberType;
                $type->name = $name;
                $type->description = $description;
                $type->price = $cents / 100;
            } else {
                $cents = \App\Support\Money::cents(number_format((float) $type->price, 2, '.', ''));
            }
            $type->code = $code;
            $type->save();
            $type->rates()->firstOrCreate(['year' => now()->year], ['amount_cents' => $cents]);
        }
    }
}
