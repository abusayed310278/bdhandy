<?php

namespace App\Console\Commands;

use App\Models\EquipmentMaintenance;
use App\Models\Inventory;
use App\Models\Vehicle;
use App\Models\VehicleMaintenance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamExpiryAlertsCommand extends Command
{
    protected $signature   = 'team:expiry-alerts';
    protected $description = 'Daily check: vehicle/equipment expiry & maintenance, low inventory stock — emits alerts.';

    public function handle(): int
    {
        $this->info('Running team expiry alerts…');

        $alerts = 0;
        $alerts += $this->vehicleExpiry();
        $alerts += $this->vehicleNextService();
        $alerts += $this->equipmentNextMaintenance();
        $alerts += $this->lowStock();

        $this->info("Done. {$alerts} alert(s) generated.");
        return self::SUCCESS;
    }

    private function vehicleExpiry(): int
    {
        $threshold = now()->addDays(30);
        $count = 0;

        Vehicle::where('status', '!=', 'retired')
            ->where(function ($q) use ($threshold) {
                $q->whereDate('registration_expiry', '<=', $threshold)
                  ->orWhereDate('insurance_expiry', '<=', $threshold)
                  ->orWhereDate('fitness_expiry', '<=', $threshold);
            })
            ->chunk(100, function ($vehicles) use (&$count, $threshold) {
                foreach ($vehicles as $v) {
                    foreach (['registration_expiry', 'insurance_expiry', 'fitness_expiry'] as $field) {
                        if ($v->$field && $v->$field->lte($threshold)) {
                            $this->notify($v->business_profile_id, "Vehicle {$v->plate_number}: " . str_replace('_', ' ', $field) . " on {$v->$field->format('d M Y')}");
                            $count++;
                        }
                    }
                }
            });

        return $count;
    }

    private function vehicleNextService(): int
    {
        $threshold = now()->addDays(7);
        $count = 0;

        VehicleMaintenance::with('vehicle')
            ->where('status', 'scheduled')
            ->whereNotNull('next_service_date')
            ->whereDate('next_service_date', '<=', $threshold)
            ->chunk(100, function ($records) use (&$count) {
                foreach ($records as $r) {
                    $this->notify($r->business_profile_id, "Vehicle {$r->vehicle?->plate_number}: service due {$r->next_service_date->format('d M Y')}");
                    $count++;
                }
            });

        return $count;
    }

    private function equipmentNextMaintenance(): int
    {
        $threshold = now()->addDays(7);
        $count = 0;

        EquipmentMaintenance::with('equipment')
            ->where('status', 'scheduled')
            ->whereNotNull('next_maintenance_date')
            ->whereDate('next_maintenance_date', '<=', $threshold)
            ->chunk(100, function ($records) use (&$count) {
                foreach ($records as $r) {
                    $this->notify($r->business_profile_id, "Equipment {$r->equipment?->name}: maintenance due {$r->next_maintenance_date->format('d M Y')}");
                    $count++;
                }
            });

        return $count;
    }

    private function lowStock(): int
    {
        $count = 0;

        Inventory::whereColumn('quantity_in_stock', '<=', 'low_stock_threshold')
            ->chunk(100, function ($items) use (&$count) {
                foreach ($items as $item) {
                    $this->notify($item->business_profile_id, "Low stock: {$item->name} ({$item->quantity_in_stock} {$item->unit} remaining)");
                    $count++;
                }
            });

        return $count;
    }

    private function notify(int $businessProfileId, string $body): void
    {
        $userId = DB::table('provider_profiles')->where('id', $businessProfileId)->value('user_id');
        if (!$userId) return;

        Log::info("[team-alert] user_id={$userId}: {$body}");
        Cache::increment("team_alerts:user_{$userId}");
        $this->line("  → user_id={$userId}: {$body}");
    }
}
