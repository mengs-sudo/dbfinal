@extends('layouts.app')

@section('title', 'Inventory Valuation - Inventory Management System')
@section('page-title', 'Inventory Valuation')

@section('content')
    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-sack-dollar"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Inventory Value</div>
                    <div class="stat-number">${{ number_format($totals->total_valuation, 2) }}</div>
                    <div class="stat-desc">Quantity &times; unit cost, all items</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Items Counted</div>
                    <div class="stat-number">{{ $totals->total_items }}</div>
                    <div class="stat-desc">Distinct products in stock</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon teal">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Units on Hand</div>
                    <div class="stat-number">{{ number_format($totals->total_quantity) }}</div>
                    <div class="stat-desc">Sum of all quantities</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Categories</div>
                    <div class="stat-number">{{ $totalCategories }}</div>
                    <div class="stat-desc">Categories represented in stock</div>
                </div>
            </div>
        </div>
    </div>

    @php
        // Shared color palette (mirrors the .stat-icon color classes already
        // used across the app) so the donut slices, the legend dots, and
        // the summary cards all stay visually consistent. "Uncategorized"
        // always gets the neutral gray at the end of the palette.
        $chartPalette = ['#0F766E', '#3B82F6', '#22C55E', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316', '#EC4899'];
        $uncategorizedColor = '#94A3B8';
    @endphp

    <div class="row g-4">
        {{-- Valuation by category --}}
        <div class="col-lg-5">
            {{-- Donut chart: inventory valuation by category --}}
            <div class="table-container mb-4">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-chart-pie me-2 text-primary"></i>Valuation by Category</h6>
                </div>
                <div class="p-3">
                    @if($categoryChart->isEmpty())
                        <p class="text-center text-secondary py-4 mb-0">
                            <i class="fas fa-inbox me-2"></i>No categorized items yet
                        </p>
                    @else
                        {{--
                            FIX: the chart needs a fixed-size PARENT wrapper.
                            Do not put max-width/max-height (or any size constraint)
                            directly on the <canvas> itself — Chart.js measures the
                            canvas's rendered box to compute its internal pixel size,
                            and if the canvas's own inline CSS is also constraining
                            that same box, each resize pass nudges the size slightly,
                            which triggers another resize pass, which nudges it again.
                            That feedback loop is the "zooming in and out" you saw,
                            and a tooltip-triggered resize on hover is what pushed it
                            to collapse to 0px and disappear.

                            The wrapper below fixes the box size explicitly and the
                            canvas just fills it (width:100%; height:100%), so there's
                            nothing left for Chart.js to fight with.
                        --}}
                        <div class="d-flex justify-content-center mb-3">
                            <div style="position: relative; width: 220px; height: 220px;">
                                <canvas id="categoryValuationChart"></canvas>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0">
                            @foreach($categoryChart as $i => $row)
                                @php
                                    $dotColor = $row['label'] === 'Uncategorized'
                                        ? $uncategorizedColor
                                        : $chartPalette[$i % count($chartPalette)];
                                @endphp
                                <li class="d-flex align-items-center justify-content-between py-1" style="font-size: 13px;">
                                    <span class="d-flex align-items-center gap-2">
                                        <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color: {{ $dotColor }};"></span>
                                        {{ $row['label'] }}
                                    </span>
                                    <span class="fw-medium">
                                        ${{ number_format($row['value'], 2) }}
                                        <span class="text-secondary">({{ number_format($row['percentage'], 1) }}%)</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-tags me-2 text-primary"></i>Value by Category</h6>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($valuationByCategory as $row)
                                <tr>
                                    <td>{{ $row->category_name }}</td>
                                    <td>{{ number_format($row->total_quantity) }}</td>
                                    <td>${{ number_format($row->total_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No categorized items yet
                                    </td>
                                </tr>
                            @endforelse
                            @if($uncategorizedValue->total_quantity > 0)
                                <tr>
                                    <td class="text-secondary">Uncategorized</td>
                                    <td>{{ number_format($uncategorizedValue->total_quantity) }}</td>
                                    <td>${{ number_format($uncategorizedValue->total_value, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Per-item valuation table --}}
        <div class="col-lg-7">
            <div class="table-container">
                <div class="table-header">
                    <h6 class="table-title"><i class="fas fa-list me-2 text-primary"></i>Item Valuation</h6>
                    <div class="table-toolbar">
                        <form action="{{ route('reports.valuation') }}" method="GET" class="d-flex gap-2">
                            <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Unit Cost</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td><span class="code-badge">{{ $item->item_code }}</span></td>
                                    <td>{{ $item->item_name }}</td>
                                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="fw-medium">${{ number_format($item->inventory_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-secondary py-4">
                                        <i class="fas fa-inbox me-2"></i>No inventory items found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <div class="pagination-info">
                        Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items
                    </div>
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if($categoryChart->isNotEmpty())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            (function () {
                const labels = @json($categoryChart->pluck('label'));
                const values = @json($categoryChart->pluck('value'));
                const percentages = @json($categoryChart->pluck('percentage'));
                const palette = @json($chartPalette);
                const uncategorizedColor = @json($uncategorizedColor);

                const colors = labels.map((label, i) =>
                    label === 'Uncategorized' ? uncategorizedColor : palette[i % palette.length]
                );

                const ctx = document.getElementById('categoryValuationChart');
                if (!ctx) return;

                // FIX: guard against double-initialization. If this script ever
                // runs twice on the same canvas (e.g. back/forward cache, a
                // partial page re-render, or the script tag being included twice),
                // Chart.js would attach a second chart + a second resize observer
                // to the same canvas, and the two instances fighting over the
                // canvas's size is another way to get the exact zoom/shrink loop
                // you were seeing.
                const existing = Chart.getChart(ctx);
                if (existing) {
                    existing.destroy();
                }

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderColor: '#fff',
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        // FIX: false, because the wrapper div now fixes the
                        // aspect ratio (220x220). With maintainAspectRatio:
                        // true AND a size-constrained canvas, Chart.js was
                        // computing two different "correct" sizes and
                        // oscillating between them.
                        maintainAspectRatio: false,
                        // FIX: debounces the resize handling so a burst of
                        // resize/tooltip events (like on hover) doesn't cause
                        // Chart.js to redraw at an intermediate/incorrect size.
                        resizeDelay: 100,
                        cutout: '65%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const pct = percentages[context.dataIndex];
                                        const value = values[context.dataIndex].toLocaleString('en-US', {
                                            style: 'currency',
                                            currency: 'USD',
                                        });
                                        return `${context.label}: ${value} (${pct}%)`;
                                    },
                                },
                            },
                        },
                    },
                });
            })();
        </script>
    @endif
@endpush