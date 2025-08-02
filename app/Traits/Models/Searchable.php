<?php

namespace App\Traits\Models;

trait Searchable
{
    /**
     * Scope untuk pencarian fleksibel.
     * Contoh: search=name,desc:sepatu => cari "sepatu" di kolom name dan desc.
     * Contoh: search=sepatu => cari "sepatu" di semua kolom.
     */
    public function scopeSearch($query, $search)
    {
        // Cek jika data kosong atau search tidak valid
        if (!$search || !is_string($search)) return $query;

        // Ambil baris pertama untuk mendeteksi kolom
        $first = $query->first();
        if (!$first) return $query;

        $originalData = collect($first->getOriginal());
        $keys = $originalData->keys(); // semua nama kolom

        // Pisahkan antara kolom dan keyword pencarian
        $searchParts = collect(explode(':', $search, 2));

        if (isset($searchParts[1])) {
            // Jika formatnya search=kolom1,kolom2:keyword
            $fields = collect(explode(',', (string) $searchParts[0]))->map(fn ($f) => trim($f));
            $keyword = $searchParts[1];
        } else {
            // Jika formatnya search=keyword, pakai semua kolom
            $fields = $keys;
            $keyword = $searchParts[0];
        }

        // Filter hanya kolom yang benar-benar ada di tabel
        $validFields = $fields->filter(fn ($item) => $keys->contains($item));

        // Jika tidak ada kolom valid, kembalikan query tanpa search
        if ($validFields->isEmpty()) return $query;

        // Bangun query pencarian
        return $query->where(function ($q) use ($validFields, $keyword) {
            foreach ($validFields as $field) {
                $q->orWhere($field, 'LIKE', '%' . $keyword . '%');
            }
        });
    }
}
