<?php

namespace App\Livewire;

use App\Models\Router;
use App\Models\Sale;
use Livewire\Component;
use Livewire\WithPagination;

class SalesReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $filterRouter = '';
    public $filterPayment = '';

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    public function getTodaySalesProperty()
    {
        return Sale::whereDate('created_at', today())->sum('amount');
    }

    public function getWeekSalesProperty()
    {
        return Sale::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount');
    }

    public function getMonthSalesProperty()
    {
        return Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    public function getTotalSalesProperty()
    {
        return Sale::sum('amount');
    }

    public function export()
    {
        // CSV Export logic
        $sales = $this->getSalesQuery()->get();
        
        $csv = "তারিখ,ইউজারনেম,প্যাকেজ,রাউটার,পেমেন্ট,মূল্য\n";
        foreach ($sales as $sale) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s\n",
                $sale->created_at->format('d/m/Y H:i'),
                $sale->voucher->username ?? 'N/A',
                $sale->package->name ?? 'N/A',
                $sale->router->name ?? 'N/A',
                $sale->payment_method,
                $sale->amount
            );
        }
        
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'sales-report-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function getSalesQuery()
    {
        return Sale::with(['voucher', 'package', 'router'])
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->when($this->filterRouter, fn($q) => $q->where('router_id', $this->filterRouter))
            ->when($this->filterPayment, fn($q) => $q->where('payment_method', $this->filterPayment))
            ->orderBy('created_at', 'desc');
    }

    public function render()
    {
        return view('livewire.sales-report', [
            'sales' => $this->getSalesQuery()->paginate(20),
            'routers' => Router::orderBy('name')->get(),
            'todaySales' => $this->todaySales,
            'weekSales' => $this->weekSales,
            'monthSales' => $this->monthSales,
            'totalSales' => $this->totalSales,
        ]);
    }
}
