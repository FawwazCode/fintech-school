<x-layout app>
    <x-layout.section title="Items" />

    <x-card class="mb-4 border-0 shadow-sm rounded-4">
        {{-- Header --}}
        <x-card.head class="align-items-center">
            <x-text bold color="primary" value="Items" />

            <x-button.modal
                class="ms-3 btn btn-sm btn-success rounded-pill px-3"
                target="modalAddItem"
                title="Add Item"
            >
                <i class="fa-solid fa-plus me-1"></i> Tambah
            </x-button.modal>

            <x-form method="GET" class="ms-auto d-none d-md-flex">
                <x-input
                    name="search"
                    placeholder="Search item..."
                    value="{{ request()->search ?? '' }}"
                    class="me-2 rounded-pill"
                />
                <x-button
                    outline
                    type="submit"
                    value="Cari"
                    class="btn btn-outline-primary rounded-pill px-3"
                />
            </x-form>
        </x-card.head>

        {{-- Body --}}
        <x-card.body class="table-responsive p-4" style="min-height: 400px">

            {{-- Modal Add --}}
            <x-modal
                id="modalAddItem"
                title="Add Item"
                :action="route('items.store')"
                enctype="multipart/form-data"
            >
                <x-modal.body>
                    <x-input type="text" name="name" label="Name:" class="mb-3" />
                    <x-input type="number" name="stock" label="Stock:" class="mb-3" />
                    <x-input type="number" name="price" label="Price:" class="mb-3" />
                    <x-input type="file" name="image" label="Image:" class="mb-3" />
                </x-modal.body>
            </x-modal>

            {{-- Modal Edit --}}
            <x-modal
                id="modalEditItem"
                title="Edit Item"
                action=" "
                method="PUT"
                enctype="multipart/form-data"
            >
                <x-modal.body>
                    <x-input type="text" name="name" label="Name:" class="mb-3" />
                    <x-input type="number" name="stock" label="Stock:" class="mb-3" />
                    <x-input type="number" name="price" label="Price:" class="mb-3" />
                    <x-input type="file" name="image" label="Image:" class="mb-3" />
                </x-modal.body>
            </x-modal>

            {{-- Tabel --}}
            <table class="table table-hover align-middle table-borderless mb-0">
                <thead class="table-light">
                    <tr class="align-middle">
                        <th>#</th>
                        <th>Nama</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Gambar</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    @php
                        $page    = $items->currentPage();
                        $perPage = $items->perPage();
                        $number  = $loop->iteration + $perPage * ($page - 1);
                    @endphp

                    <tr class="bg-white rounded-3 shadow-sm">
                        <td>{{ $number }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->stock }}</td>
                        <td>Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                        <td>
                            @if ($item->image)
                                <div style="width: 300px; height: auto;">
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        class="img-fluid rounded shadow-sm border"
                                        style="object-fit: cover; width: 100%; max-height: 300px;">
                                </div>
                            @else
                                <span class="text-muted">No Image</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <x-button
                                    color="danger"
                                    :action="route('items.destroy', [$item->id])"
                                    method="DELETE"
                                    class="btn-sm"
                                >
                                    <i class="fas fa-trash"></i>
                                </x-button>
                                <x-button.modal
                                    color="warning"
                                    :data="$item"
                                    :action="route('items.update', [$item->id])"
                                    class="btn-sm text-white"
                                    target="modalEditItem"
                                >
                                    <i class="fas fa-pencil-alt"></i>
                                </x-button.modal>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-end">
                {{ $items->links() }}
            </div>
        </x-card.body>
    </x-card>
</x-layout>
