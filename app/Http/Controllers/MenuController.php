<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /** De 14 officiële allergenen (EU), in het Nederlands */
    public const ALLERGENEN = ['gluten', 'schaaldieren', 'ei', 'vis', 'pinda', 'soja', 'melk', 'noten', 'selderij', 'mosterd', 'sesam', 'sulfiet', 'lupine', 'weekdieren'];

    private function regels(bool $aanmaken): array
    {
        $kern = $aanmaken ? ['required'] : ['sometimes', 'required'];

        return [
            'naam' => [...$kern, 'string', 'max:100'],
            'prijs' => [...$kern, 'integer', 'min:0', 'max:99900'],
            'categorie' => [...$kern, 'string', 'max:50'],
            'beschrijving' => ['sometimes', 'nullable', 'string', 'max:300'],
            'icoon' => ['sometimes', 'nullable', 'string', 'max:16'],
            'ingredienten' => ['sometimes', 'nullable', 'array', 'max:30'],
            'ingredienten.*' => ['string', 'max:50'],
            'allergenen' => ['sometimes', 'nullable', 'array'],
            'allergenen.*' => [Rule::in(self::ALLERGENEN)],
            'opties' => ['sometimes', 'nullable', 'array', 'max:10'],
            'opties.*.naam' => ['required', 'string', 'max:60'],
            'opties.*.type' => ['required', Rule::in(['een', 'meerdere'])],
            'opties.*.keuzes' => ['required', 'array', 'min:1', 'max:20'],
            'opties.*.keuzes.*.naam' => ['required', 'string', 'max:60'],
            'opties.*.keuzes.*.prijs' => ['required', 'integer', 'min:0', 'max:9900'],
            'actief' => ['sometimes', 'boolean'],
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->regels(true));
        $data['volgorde'] = ((int) $request->user()->menuItems()->max('volgorde')) + 1;

        return response()->json($request->user()->menuItems()->create($data));
    }

    public function update(Request $request, MenuItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $item->update($request->validate($this->regels(false)));

        return response()->json($item->refresh());
    }

    public function destroy(Request $request, MenuItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);
        $item->delete();

        return response()->json(['ok' => true]);
    }
}
