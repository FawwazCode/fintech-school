@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Daftar Produk</h2>

    {{-- Form Search --}}
    <form method="GET" action="{{ route('stores.index') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
            <button class="btn btn-outline-primary" type="submit">Cari</button>
        </div>
    </form>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        @foreach($items as $item)
        @php
            $imageUrl = $item->image
                ? asset('storage/' . $item->image)
                : asset('images/default.png'); // default jika gambar tidak ada
        @endphp
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden hover-shadow transition-all">
                <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $item->name }}" style="height: 200px; object-fit: cover;">
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-semibold text-dark">{{ $item->name }}</h5>
                    <p class="card-text text-muted mb-1">Stock: <strong>{{ $item->stock }}</strong></p>
                    <p class="card-text text-muted mb-3">Harga: <span class="text-success fw-bold">Rp{{ number_format($item->price, 0, ',', '.') }}</span></p>

                    <div class="mt-auto d-flex justify-content-end">
                        {{-- Tombol Beli --}}
                        <x-button :action="route('stores.store', [$item->id])" class="btn btn-primary rounded-pill px-4 py-2" style="width: 300px;">
                            <i class="fas fa-cart-plus me-1"></i> Beli
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $items->links() }}
    </div>
</div>

{{-- Modal Detail Produk --}}
{{-- <div class="modal fade" id="modalShowProduct" tabindex="-1" aria-labelledby="modalShowProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <img id="modalImage" class="img-fluid mb-3" alt="Gambar Produk">
                <h5 id="modalName"></h5>
                <p id="modalDesc"></p>
                <p><strong>Stock:</strong> <span id="modalStock"></span></p>
                <p><strong>Harga:</strong> Rp<span id="modalPrice"></span></p>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@push('scripts')
<script>
    const modalShowProduct = document.getElementById('modalShowProduct');
    modalShowProduct.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const item = JSON.parse(button.getAttribute('data-item'));

        const modalImage = modalShowProduct.querySelector('#modalImage');
        const modalName = modalShowProduct.querySelector('#modalName');
        const modalDesc = modalShowProduct.querySelector('#modalDesc');
        const modalStock = modalShowProduct.querySelector('#modalStock');
        const modalPrice = modalShowProduct.querySelector('#modalPrice');

        modalImage.src = item.image ? `/storage/images/${item.image}` : '/images/default.png';
        modalImage.alt = item.name;
        modalName.textContent = item.name;
        modalDesc.textContent = item.desc || '-';
        modalStock.textContent = item.stock;
        modalPrice.textContent = Number(item.price).toLocaleString('id-ID');
    });
</script>
@endpush
