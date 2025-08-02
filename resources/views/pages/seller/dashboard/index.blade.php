@php
    use App\Models\{Item, Transaction};
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $today = request()->today;
    $user  = auth()->user();
    $items = Item::where('seller_id', $user->id);
    $trxs  = $today ? Transaction::today() : new Transaction();
    $trxs  = $trxs->where('receiver_id', $user->id)->where('type', 2)->get();

    $paidCount     = $trxs->where('status', 2)->count();
    $pendingCount  = $trxs->where('status', 1)->count();

    // Data grafik batang: total transaksi lunas per hari (7 hari terakhir)
    $barChartData = Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('receiver_id', $user->id)
        ->where('type', 2)
        ->where('status', 2)
        ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get()
        ->keyBy('date');

    $dates = collect();
    $counts = collect();

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->toDateString();
        $dates->push(Carbon::parse($date)->translatedFormat('D, d M'));
        $counts->push($barChartData[$date]->count ?? 0);
    }
@endphp

<x-layout app>
    <x-layout.section title="Dashboard Seller" />

    <x-view.row>
        <x-card.info title="Saldo Saat Ini" value="Rp {{ number_format($user->balance) }}" icon="fa-money-bill-wave text-primary" />
        <x-card.info title="Total Produk" value="{{ number_format($items->count()) }}" icon="fa-box text-info" color="info" />
        <x-card.info title="Transaksi Pending" value="{{ number_format($pendingCount) }}" icon="fa-clock text-warning" color="warning" />
        <x-card.info title="Transaksi Lunas" value="{{ number_format($paidCount) }}" icon="fa-cart-shopping text-success" color="success" />
    </x-view.row>

    {{-- Grafik --}}
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow rounded">
                <div class="card-body">
                    <h5 class="card-title">Statistik Transaksi</h5>
                    <canvas id="trxChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mt-4 mt-md-0">
            <div class="card shadow rounded">
                <div class="card-body">
                    <h5 class="card-title">Penjualan per Hari</h5>
                    <canvas id="barChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Grafik --}}
    <script>
        // Doughnut Chart
        const trxCtx = document.getElementById('trxChart').getContext('2d');
        new Chart(trxCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lunas', 'Pending'],
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: [{{ $paidCount }}, {{ $pendingCount }}],
                    backgroundColor: ['#198754', '#ffc107'],
                    borderColor: ['#198754', '#ffc107'],
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Bar Chart
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'Transaksi Lunas',
                    data: {!! json_encode($counts) !!},
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</x-layout>
