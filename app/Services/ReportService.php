<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\SalesRepository;

class ReportService
{
    protected const EXPORT_LIMIT = 5000;

    public function __construct(
        protected SalesRepository $salesRepo,
        protected ProductRepository $productRepo,
    ) {}

    public function getSalesReport(array $filters): array
    {
        $sales = $this->salesRepo->getFilteredExport($filters, self::EXPORT_LIMIT);
        $products = $this->productRepo->all();

        $totalRevenue = $sales->sum('grand_total');
        $totalQty = $sales->sum('qty');

        return [
            'sales' => $sales,
            'products' => $products,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_qty' => $totalQty,
                'total_transactions' => $sales->count(),
            ],
        ];
    }
}
