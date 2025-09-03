<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\Menu;
use App\Models\SalesOrder;

class SearchController extends Controller
{
    /**
     * Konfigurasi pencarian global
     */
    public function getSearchHandlers(): array
    {
        return [
            'transaksi_sales_orders' => [
                'model' => SalesOrder::class,
                'fields' => ['no_so', 'customer.kode_customer'],
                'display' => function ($item) {
                    return $item->no_so . ' - ' . optional($item->customer)->kode_customer;
                },
                'icon' => 'visibility',
                'route' => 'transaksi_sales_orders.show',
            ],
            'transaksi_delivery_orders' => [
                'model' => DeliveryOrder::class,
                'fields' => ['no_do'],
                'display' => 'no_do',
                'icon' => 'visibility',
                'route' => 'transaksi_delivery_orders.show',
            ],
            'menus' => [
                'model' => Menu::class,
                'fields' => ['title'],
                'display' => 'title',
                'icon' => 'menu',
                'route' => 'menus.index',
            ],
        ];
    }

    /**
     * API: Pencarian global (versi JSON, bisa dipakai Select2, dsb)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');

            if (empty(trim($query))) {
                return response()->json(
                    array_fill_keys(array_keys($this->getSearchHandlers()), [])
                );
            }

            $results = [];

            foreach ($this->getSearchHandlers() as $key => $handler) {
                $results[$key] = $this->performSearch($query, $handler);
            }

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error("Search error: " . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan saat mencari',
                ...array_fill_keys(array_keys($this->getSearchHandlers()), [])
            ], 500);
        }
    }

    /**
     * Web: Halaman pencarian global
     */
    public function searchAll(Request $request)
    {
        try {
            $query = $request->input('q', '');

            if (empty(trim($query))) {
                return view('search.all', [
                    'results' => [],
                    'query' => $query,
                ]);
            }

            $results = [];

            foreach ($this->getSearchHandlers() as $key => $handler) {
                $results[$key] = [
                    'title' => $handler['title'] ?? ucfirst(str_replace('_', ' ', $key)),
                    'icon' => $handler['icon'],
                    'items' => $this->performFullSearch($query, $handler),
                ];
            }

            return view('search.all', [
                'results' => array_filter($results, fn($r) => !empty($r['items'])),
                'query' => $query,
            ]);
        } catch (\Exception $e) {
            \Log::error("SearchAll error: " . $e->getMessage());
            return view('search.all', [
                'error' => 'Terjadi kesalahan saat mencari',
                'query' => $request->input('q', ''),
            ]);
        }
    }

    /**
     * Eksekusi pencarian versi JSON
     */
    protected function performSearch($query, $handler)
    {
        try {
            $model = $handler['model'];
            $queryBuilder = $model::query();

            $this->applyRelationEagerLoading($queryBuilder, $handler['fields']);

            $queryBuilder->where(function ($q) use ($handler, $query) {
                foreach ($handler['fields'] as $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $relField] = explode('.', $field, 2);
                        $q->orWhereHas(
                            $relation,
                            fn($subQ) =>
                            $subQ->where($relField, 'LIKE', "%{$query}%")
                        );
                    } else {
                        $q->orWhere($field, 'LIKE', "%{$query}%");
                    }
                }
            });

            return $queryBuilder
                ->limit(5)
                ->get()
                ->map(function ($item) use ($handler) {
                    $display = is_callable($handler['display'])
                        ? call_user_func($handler['display'], $item)
                        : $item->{$handler['display']};

                    return [
                        'id' => $item->id,
                        'display' => $display,
                        'icon' => $handler['icon'],
                        'url' => route($handler['route'], $item->id),
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            \Log::error("Search handler error [{$handler['model']}]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Eksekusi pencarian versi view (dengan pagination)
     */
    protected function performFullSearch($query, $handler)
    {
        try {
            $model = $handler['model'];
            $queryBuilder = $model::query();

            $this->applyRelationEagerLoading($queryBuilder, $handler['fields']);

            $queryBuilder->where(function ($q) use ($handler, $query) {
                foreach ($handler['fields'] as $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $relField] = explode('.', $field, 2);
                        $q->orWhereHas(
                            $relation,
                            fn($subQ) =>
                            $subQ->where($relField, 'LIKE', "%{$query}%")
                        );
                    } else {
                        $q->orWhere($field, 'LIKE', "%{$query}%");
                    }
                }
            });

            return $queryBuilder
                ->paginate(10)
                ->through(function ($item) use ($handler) {
                    $display = is_callable($handler['display'])
                        ? call_user_func($handler['display'], $item)
                        : $item->{$handler['display']};

                    return [
                        'id' => $item->id,
                        'display' => $display,
                        'url' => route($handler['route'], $item->id),
                    ];
                });
        } catch (\Exception $e) {
            \Log::error("Full search error [{$handler['model']}]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Helper: Tambahkan eager loading otomatis jika ada relasi
     */
    protected function applyRelationEagerLoading($queryBuilder, array $fields)
    {
        $relations = collect($fields)
            ->filter(fn($field) => str_contains($field, '.'))
            ->map(fn($field) => explode('.', $field)[0])
            ->unique()
            ->toArray();

        if (!empty($relations)) {
            $queryBuilder->with($relations);
        }
    }
}
