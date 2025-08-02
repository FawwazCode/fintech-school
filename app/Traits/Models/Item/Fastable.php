<?php

namespace App\Traits\Models\Item;

use App\Models\{Item, User};
use Exception;
use Illuminate\Support\Facades\Validator;

trait Fastable
{
    public function scopeFastPaginate($query, $data = [])
    {
        $data   = (object) $data;
        $search = $data->search ?? null;
        $items  = $query->with(['seller'])->latest();

        // Cek apakah model punya method scopeSearch
        if ($search && method_exists($query->getModel(), 'scopeSearch')) {
            $items = $items->search($search);
        }

        return $items->paginate(10)->withQueryString();
    }

     public function scopeFastCreate($query, $data, $user = null) {
    $data   = (object) $data;
    $user   = $user ? User::find($user->id ?? $user) : auth()->user();

    $validator = Validator::make($data->all() ?? $data, [
        'name'  => 'required|min:2|max:50',
        'stock' => 'nullable|numeric|digits_between:1,18',
        'price' => 'required|numeric|digits_between:1,18',
        'desc'  => 'nullable|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) throw new \Exception($validator->errors()->first());

    $imagePath = null;
    if (request()->hasFile('image')) {
        $imagePath = request()->file('image')->store('images', 'public');
    }

    return Item::create([
        'seller_id' => $user->id,
        'name'      => $data->name,
        'stock'     => $data->stock,
        'price'     => $data->price,
        'desc'      => $data->desc,
        'image'     => $imagePath,
    ]);
}

    public function scopeFastUpdate($query, $data, $item) {
    $item = Item::find($item->id ?? $item);
    $data = (object) $data;

    if (!$item) throw new \Exception('Item not found');
    if ($item->seller_id !== auth()->id()) throw new \Exception('Forbidden');

    $validator = Validator::make($data->all() ?? $data, [
        'name'  => 'nullable|min:2|max:50',
        'stock' => 'nullable|numeric|digits_between:1,18',
        'price' => 'nullable|numeric|digits_between:1,18',
        'desc'  => 'nullable|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) throw new \Exception($validator->errors()->first());

    $imagePath = $item->image;
    if (request()->hasFile('image')) {
        $imagePath = request()->file('image')->store('images', 'public');
    }

    $item->update([
        'name'  => $data->name ?? $item->name,
        'stock' => $data->stock ?? $item->stock,
        'price' => $data->price ?? $item->price,
        'desc'  => $data->desc,
        'image' => $imagePath,
    ]);

    return $item;
}

    public function scopeFastDelete($query, $item) {
        $itemId = $item->id ?? null;
        $item   = Item::find($itemId ?? $item);

        if(!$item) throw new Exception('Item not found');
        if($item->seller_id !== auth()->id()) throw new Exception('Forbidden');

        $item->delete();

        return $item;
    }
}
