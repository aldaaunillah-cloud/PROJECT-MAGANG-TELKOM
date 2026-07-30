<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReminderService
{
    protected Reminder $model;

    public function __construct(Reminder $reminder)
    {
        $this->model = $reminder;
    }

    /**
     * Get reminders with filters
     */
    public function getReminders(Request $request)
    {
        $query = $this->model->with(['customer', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('snd', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return $query->orderBy('created_at', 'desc')->paginate(30);
    }

    /**
     * Create new reminder
     */
    public function createReminder(array $data): Reminder
    {
        return $this->model->create($data);
    }

    /**
     * Update reminder status
     */
    public function updateStatus(int $id, string $status): bool
    {
        $reminder = $this->model->findOrFail($id);
        return $reminder->update(['status' => $status]);
    }

    /**
     * Get reminder by customer
     */
    public function getRemindersByCustomer(int $customerId)
    {
        return $this->model->where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}