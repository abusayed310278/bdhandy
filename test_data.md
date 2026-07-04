# Team Management — Test Data & Step-by-Step Walkthrough

> **Companion to:** `team_management.md`
> **Purpose:** End-to-end manual test plan with demo data for every capability listed in the spec.
> **Audience:** QA / business / developer running through the freshly-built system.
> **Base URL:** `http://bdhandy.test`

---

## 0. Prerequisites (one-time setup)

Before running any module test you need a working business account.

### 0.1 Login as a Business Provider

| Field | Value |
| --- | --- |
| Email | `business@example.com` *(or your seeded business account)* |
| Password | `password` |

The account **must**:
- Have role `business` (Spatie role)
- Have a `provider_profiles` row with `provider_type = business`
- Have `verification_status = approved`
- Hold an active subscription (any plan)
- Have at least 1 `provider_service_areas` row
- Have at least 3 `provider_services` rows (so you can assign skills to team members)

> If you don't have a business account, register one at `/register` → choose **Business** → complete onboarding wizard → approve via `/admin/providers/{id}/approve`.

After login you should see the new sidebar groups: **Team Management**, **Assets**, **Operations**.

---

## 1. Team Roles

**Spec ref:** `team_management.md` § Database Schema → `team_roles`, § Permission Roles

### 1.1 Create the four predefined roles

URL: `/business/team/roles/create`

Create one role at a time using these test data sets:

#### Role A — Manager
| Field | Value |
| --- | --- |
| Role Name | `Manager` |
| Default Role | unchecked |
| Permissions | ✅ Tick ALL boxes in every group |

Expected: Redirect to `/business/team/roles` with "Role created" toast. New card visible.

#### Role B — Supervisor
| Field | Value |
| --- | --- |
| Role Name | `Supervisor` |
| Default Role | unchecked |
| Permissions (Jobs) | view_assigned, view_all_team_jobs, accept_reject, update_status, reassign |
| Permissions (Attendance) | view_team_attendance |
| Permissions (Schedule) | view_daily_schedule, create_schedule, optimize_route |
| Permissions (Equipment) | manage_equipment |
| Permissions (Vehicles) | log_fuel, manage_vehicles |

#### Role C — Senior Technician
| Field | Value |
| --- | --- |
| Role Name | `Senior Technician` |
| Default Role | unchecked |
| Permissions (Jobs) | view_assigned, accept_reject, update_status |
| Permissions (Attendance) | clock_in_out, view_own_history |
| Permissions (Schedule) | view_daily_schedule, request_changes |
| Permissions (Earnings) | view_own_earnings |
| Permissions (Equipment) | view_assigned_equipment, report_lost |
| Permissions (Inventory) | log_material_usage, view_inventory |

#### Role D — Junior Technician
| Field | Value |
| --- | --- |
| Role Name | `Junior Technician` |
| Default Role | ✅ checked |
| Permissions (Jobs) | view_assigned, update_status |
| Permissions (Attendance) | clock_in_out, view_own_history |
| Permissions (Schedule) | view_daily_schedule |
| Permissions (Inventory) | log_material_usage |

### 1.2 Verify list view

URL: `/business/team/roles`

Expected:
- 4 cards visible
- "Junior Technician" shows "Default" badge
- Permission summary chips show group counts (e.g. `jobs (2)`, `attendance (2)`)
- Each card shows `0 members` initially

### 1.3 Edge cases to test

- Try to delete a role → should succeed (since 0 members)
- Try to create another role named "Manager" → should fail with unique constraint
- Edit "Junior Technician" → uncheck `is_default` → save → toggle should persist

---

## 2. Team Members + Service Skills

**Spec ref:** `team_management.md` § `team_members`, § `team_member_services`

### 2.1 Add Team Member #1 (Senior AC Technician)

URL: `/business/team/invite`

| Field | Value |
| --- | --- |
| Full Name | `Rahim Uddin` |
| Phone | `+8801711000001` |
| Email | `rahim@example.com` |
| Designation | `Senior AC Technician` |
| Joining Date | today's date |
| Role | `Senior Technician` |
| Compensation Type | `salary` |
| Services | ✅ AC Repair (skill: senior, ★ primary), ✅ Electrical (skill: mid) |

Expected:
- Redirect to `/business/team/{id}` with success toast containing employee code (e.g. `EMP-DHK-0001`)
- Show page lists 2 services, AC Repair marked "★ Primary"

### 2.2 Add Team Member #2 (Junior Plumber)

| Field | Value |
| --- | --- |
| Full Name | `Karim Mia` |
| Phone | `+8801711000002` |
| Email | (leave blank) |
| Designation | `Plumbing Apprentice` |
| Joining Date | today's date |
| Role | `Junior Technician` |
| Compensation Type | `commission` |
| Services | ✅ Plumbing (skill: junior, ★ primary) |

### 2.3 Add Team Member #3 (Hybrid Mid-level)

| Field | Value |
| --- | --- |
| Full Name | `Salma Akter` |
| Phone | `+8801711000003` |
| Designation | `Mid Electrician` |
| Role | `Senior Technician` |
| Compensation Type | `hybrid` |
| Services | ✅ Electrical (skill: mid, ★ primary), ✅ AC Repair (skill: junior) |

### 2.4 Verify list view

URL: `/business/team`

Expected:
- 3 members visible in table
- Each shows employee code, designation, role, service chips, status badge (green "Active")
- Total counter = 3, Active = 3

### 2.5 Edit a member

URL: `/business/team/{Rahim's id}/edit`

Change:
- Designation → `Lead AC Specialist`
- Add service → ✅ HVAC Installation (skill: senior)
- Status → keep `active`

Expected: Show page now lists 3 services. The new service chip appears.

### 2.6 Edge cases

- Try to invite another member with phone `+8801711000001` → should fail (unique)
- Terminate Karim Mia → confirms popup → he disappears from list, status becomes "terminated" + soft-deleted
- Verify Salma Akter's profile shows correct compensation type "Hybrid"

---

## 3. Job Dispatch + Smart Suggestions

**Spec ref:** `team_management.md` § Smart Job Assignment, § `team_job_assignments`

### 3.1 Pre-requisite: create test service requests

Run in Tinker (`php artisan tinker`):

```php
$profile = App\Models\ProviderProfile::where('provider_type','business')->first();
$service = App\Models\Service::first();
$customer = App\Models\User::role('customer')->first();

for ($i = 1; $i <= 3; $i++) {
    App\Models\ServiceRequest::create([
        'request_number'   => 'REQ-TEST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
        'customer_id'      => $customer->id,
        'provider_id'      => $profile->user_id,
        'service_id'       => $service->id,
        'title'            => "Test Job #$i",
        'description'      => "Auto-generated for dispatch testing",
        'address'          => "Dhaka, Bangladesh",
        'latitude'         => 23.81 + ($i * 0.01),
        'longitude'        => 90.41 + ($i * 0.01),
        'urgency'          => ['normal','urgent','emergency'][$i % 3],
        'request_status'   => 'accepted',
        'payment_status'   => 'pending',
        'preferred_date'   => now()->addDays($i)->toDateString(),
    ]);
}
```

### 3.2 View the dispatch board

URL: `/business/dispatch`

Expected:
- Left panel: 3 unassigned jobs (REQ-TEST-0001, 0002, 0003) with urgency badges
- Right panel: 3 team members with active job counter "0 active"

### 3.3 Assign Job #1 to Rahim (skill match)

In the REQ-TEST-0001 card:
- Member select → `Rahim Uddin (0 active)`
- Scheduled start time → today + 2 hours
- Click **Assign**

Expected: Job disappears from unassigned list. Rahim's card shows `1 active`.

### 3.4 Assign Job #2 to Karim

Repeat for REQ-TEST-0002 → Karim Mia. Karim's card now shows `1 active`.

### 3.5 Test skill-filtered suggestions endpoint

URL (GET, returns JSON): `/business/dispatch/{job_id}/suggestions`

Expected JSON: list of `team_members` whose `team_member_services.service_id` matches the job's `service_id`, ordered by `active_jobs` ascending.

### 3.6 Edge cases

- Try assigning REQ-TEST-0003 to Karim (now 1 active) — should succeed with `2 active`
- The smart assignment algorithm from the spec (`§ Smart Job Assignment`) is not auto-executed; it's exposed via the suggestions endpoint for the dispatch UI to consume

---

## 4. Schedule & Route Optimisation

**Spec ref:** `team_management.md` § Route Optimisation, § `team_daily_schedule`, § `team_schedule_waypoints`

### 4.1 Set scheduled times on the 3 assignments

In Tinker:
```php
App\Models\TeamJobAssignment::all()->each(function ($a, $i) {
    $a->update(['scheduled_start_time' => now()->addHours($i + 1)]);
});
```

### 4.2 Open the schedule

URL: `/business/schedule`

Pick today's date → click **View** → goes to `/business/schedule/{Y-m-d}`

Expected: shows 0 schedules (no `team_daily_schedule` rows yet) but the unscheduled job list is populated.

### 4.3 Optimise Rahim's day

On the schedule show page, click **Optimize** for Rahim's row.

Expected:
- New `team_daily_schedule` row inserted for Rahim
- Waypoints created in `scheduled_start_time` order
- Schedule card now shows Rahim's name + waypoint count

### 4.4 Publish the schedule

Click **Publish** on Rahim's card.

Expected:
- Card now shows green **Published** badge
- `team_daily_schedule.is_published = true`

### 4.5 Verify technician view

URL: `/tech/schedule` (logged in as business user — Phase 1 behaviour, will be team-member sessions in Phase 2)

Expected:
- Ordered list of waypoints
- "Navigate" link (Google Maps direction URL)
- Status update buttons per job

---

## 5. Attendance Tracking

**Spec ref:** `team_management.md` § Attendance, § `team_attendance`

### 5.1 Tech clock-in

URL (POST form): `/tech/attendance/clock-in`

Body (when wired to a UI button):
```
latitude  = 23.8103
longitude = 90.4125
address   = "Gulshan, Dhaka"
```

For now, run via Tinker to simulate:
```php
$profile = auth()->user()->providerProfile ?? App\Models\ProviderProfile::where('provider_type','business')->first();
App\Models\TeamAttendance::create([
    'team_member_id' => 1,
    'business_profile_id' => $profile->id,
    'clock_in_time' => now()->subHours(4),
    'clock_in_latitude' => 23.8103,
    'clock_in_longitude' => 90.4125,
    'clock_in_address' => 'Gulshan, Dhaka',
    'status' => 'clocked_in',
]);
```

### 5.2 Verify business attendance board

URL: `/business/attendance`

Expected:
- Row for Rahim showing clock_in time, "—" for clock-out, status `clocked_in`
- Stats: "Clocked In Now: 1"

### 5.3 Clock out

```php
$rec = App\Models\TeamAttendance::latest('clock_in_time')->first();
$rec->clock_out_time = now();
$rec->clock_out_latitude = 23.815;
$rec->clock_out_longitude = 90.418;
$rec->status = 'clocked_out';
$rec->computeTotalHours();
$rec->save();
```

### 5.4 Re-verify attendance board

URL: `/business/attendance?date={today}`

Expected:
- Total hours column now shows ~4.0h
- Status badge → green `clocked out`
- Click **Verify** → flag flips, badge changes to `✓ Verified`

### 5.5 Member history

URL: `/business/attendance/{member_id}/history`

Expected: paginated list of all attendance records for that member.

---

## 6. Live Location Tracking

**Spec ref:** `team_management.md` § Live Location, § `team_location_tracking`

### 6.1 Simulate a location ping

POST `/tech/location/update` with payload:
```
member_id = 1
latitude  = 23.8205
longitude = 90.4200
accuracy  = 12
heading   = 87.5
speed     = 22.5
battery   = 78
is_moving = 1
```

Or via Tinker:
```php
App\Models\TeamLocationTracking::create([
    'team_member_id' => 1,
    'business_profile_id' => 1,
    'latitude' => 23.8205,
    'longitude' => 90.4200,
    'accuracy_meters' => 12,
    'heading' => 87.5,
    'speed_kmh' => 22.5,
    'battery_level' => 78,
    'is_moving' => true,
    'location_time' => now(),
]);
Cache::put('team_location:1', ['lat'=>23.8205,'lng'=>90.4200,'speed'=>22.5,'is_moving'=>true,'updated_at'=>now()->toIso8601String()], now()->addDay());
```

### 6.2 Open the live map

URL: `/business/location`

Expected:
- Leaflet map centred near Dhaka
- One green dot for Rahim (clocked in + has cached location)
- Sidebar lists all active members; Rahim shows "🟢 Moving · 22.5 km/h"
- Click Rahim's card → map pans to his marker

### 6.3 Edge cases

- Add a second ping for Karim with `is_moving = false` → sidebar shows "🔴 Stationary"
- Map auto-refreshes after 45 seconds

---

## 7. Equipment & Tool Tracking

**Spec ref:** `team_management.md` § Equipment & Tool Tracking

### 7.1 Add 3 equipment items

URL: `/business/equipment/create`

#### Item 1 — Drill Machine
| Field | Value |
| --- | --- |
| Name | `Bosch Cordless Drill` |
| Category | `Power Tools` |
| Brand | `Bosch` |
| Model | `GSB 12V-15` |
| Serial Number | `BSH-2024-A001` |
| Purchase Date | `2026-01-15` |
| Purchase Price | `8500.00` |
| Condition | `good` |
| Notes | `Standard issue for AC technicians` |

#### Item 2 — Multimeter
| Field | Value |
| --- | --- |
| Name | `Digital Multimeter` |
| Category | `Measuring` |
| Brand | `Fluke` |
| Model | `117` |
| Condition | `new` |

#### Item 3 — Vacuum Pump
| Field | Value |
| --- | --- |
| Name | `Refrigerant Vacuum Pump` |
| Category | `HVAC` |
| Brand | `Robinair` |
| Condition | `fair` |

### 7.2 Verify list

URL: `/business/equipment`

Expected: 3 rows, each with auto-generated `code` like `EQ-0001`, `EQ-0002`, `EQ-0003`. All show status **available**.

### 7.3 Assign Drill to Rahim

In Tinker (UI assign button works the same):
```php
$eq = App\Models\Equipment::where('name','like','%Drill%')->first();
$assign = App\Models\EquipmentAssignment::create([
    'equipment_id' => $eq->id,
    'team_member_id' => 1,
    'business_profile_id' => $eq->business_profile_id,
    'assigned_by' => auth()->id() ?? 1,
    'status' => 'assigned',
]);
$eq->update(['status' => 'assigned']);
```

Expected on `/business/equipment`: Drill row now shows "Assigned To: Rahim Uddin", status badge → blue **assigned**.

### 7.4 Return the drill (with damage)

Click **Return** on Drill row (when UI is wired) or:
```php
$assign->update(['returned_at' => now(), 'returned_condition' => 'damaged', 'return_notes' => 'Battery worn out', 'status' => 'returned']);
$eq->update(['status' => 'needs_repair', 'condition' => 'needs_repair']);
```

Expected: status → amber `needs_repair`, condition → amber `needs repair`.

### 7.5 Report lost equipment

For the Multimeter:
```php
$multi = App\Models\Equipment::where('name','like','%Multimeter%')->first();
$multi->update(['status' => 'lost']);
```

Expected: row shows red **lost** badge.

### 7.6 Add maintenance record

URL: `/business/equipment/{drill_id}/maintenance`

| Field | Value |
| --- | --- |
| Type | `repair` |
| Date | today |
| Next Date | today + 90 days |
| Performed By | `Local Service Centre` |
| Cost | `1500.00` |
| Status | `completed` |
| Description | `Battery replacement` |

Expected: row appears in maintenance history table.

### 7.7 Tech-side view

URL: `/tech/equipment`

Expected: Shows currently-assigned equipment for the logged-in business (Phase 1 limitation). "Report Issue" button reveals dropdown to mark damaged/lost.

---

## 8. Inventory Management + Material Usage

**Spec ref:** `team_management.md` § Inventory Management, § Auto-deduction workflow

### 8.1 Add 4 inventory items

URL: `/business/inventory/create`

#### Item 1
| Field | Value |
| --- | --- |
| Name | `AC Capacitor 25µF` |
| SKU | `CAP-25UF` |
| Category | `HVAC Parts` |
| Unit | `pcs` |
| Quantity in Stock | `20` |
| Low Stock Threshold | `5` |
| Unit Cost | `350.00` |
| Supplier | `Daewoo Electronics BD` |

#### Item 2
| Field | Value |
| --- | --- |
| Name | `AC Filter Standard` |
| SKU | `FILT-STD` |
| Category | `HVAC Parts` |
| Unit | `pcs` |
| Quantity | `10` |
| Threshold | `3` |
| Unit Cost | `450.00` |

#### Item 3
| Field | Value |
| --- | --- |
| Name | `PVC Pipe ½ inch` |
| SKU | `PVC-HALF` |
| Category | `Plumbing` |
| Unit | `meters` |
| Quantity | `50` |
| Threshold | `10` |
| Unit Cost | `45.00` |

#### Item 4 — already at low stock
| Field | Value |
| --- | --- |
| Name | `Copper Wire 2.5mm` |
| SKU | `WIRE-25` |
| Unit | `meters` |
| Quantity | `4` |
| Threshold | `10` |
| Unit Cost | `120.00` |

### 8.2 Verify list & low-stock highlight

URL: `/business/inventory`

Expected:
- 4 rows
- "Copper Wire 2.5mm" row is highlighted (amber tinted background) with "⚠ Low stock" badge

URL: `/business/inventory/low-stock`

Expected: only Copper Wire 2.5mm appears.

### 8.3 Restock the copper wire

In the row's inline restock form: quantity = `50` → click **+ Restock**.

Expected:
- Stock jumps from 4 → 54
- Low-stock highlight disappears
- View transactions → `/business/inventory/{id}/transactions` → 1 row of type **restock**, before=4, after=54

### 8.4 Simulate technician logging materials on a job

In Tinker (since tech UI needs full setup):
```php
$job = App\Models\TeamJobAssignment::first();
$cap = App\Models\Inventory::where('sku','CAP-25UF')->first();
$filt = App\Models\Inventory::where('sku','FILT-STD')->first();
$pvc = App\Models\Inventory::where('sku','PVC-HALF')->first();

// Mimic JobController@logMaterials POST
\DB::transaction(function () use ($job, $cap, $filt, $pvc) {
    foreach ([[$cap, 2], [$filt, 1], [$pvc, 3]] as [$item, $qty]) {
        $before = $item->quantity_in_stock;
        $after = max(0, $before - $qty);
        App\Models\JobMaterialUsage::create([
            'job_assignment_id' => $job->id,
            'team_member_id' => 1,
            'business_profile_id' => $item->business_profile_id,
            'inventory_id' => $item->id,
            'quantity_used' => $qty,
            'unit_cost_at_time' => $item->unit_cost,
        ]);
        App\Models\InventoryTransaction::create([
            'inventory_id' => $item->id,
            'business_profile_id' => $item->business_profile_id,
            'transaction_type' => 'usage',
            'quantity' => -$qty,
            'quantity_before' => $before,
            'quantity_after' => $after,
            'reference_type' => 'job_material_usage',
            'reference_id' => $job->id,
        ]);
        $item->update(['quantity_in_stock' => $after]);
    }
});
```

### 8.5 Re-verify inventory

URL: `/business/inventory`

Expected:
- AC Capacitor: 20 → 18
- AC Filter: 10 → 9
- PVC Pipe: 50 → 47
- No new low-stock alerts (all still above thresholds)

URL: `/business/inventory/{capacitor_id}/transactions`

Expected: 2 rows now — original creation (implicit), and the new **usage** row with quantity `-2`, reference `job_material_usage`.

### 8.6 Edge case — over-usage

Try logging 30 capacitors when only 18 remain. The controller uses `max(0, $before - $qty)`, so stock floors at 0. Transaction `quantity_after` would be 0. Verify in DB.

---

## 9. Vehicle Management

**Spec ref:** `team_management.md` § Vehicle Management

### 9.1 Add 2 vehicles

URL: `/business/vehicles/create`

#### Vehicle 1 — Bike
| Field | Value |
| --- | --- |
| Type | `bike` |
| Plate Number | `DHA-12-3456` |
| Make | `Honda` |
| Model | `CB125` |
| Year | `2023` |
| Color | `Red` |
| Registration Expiry | today + 25 days *(triggers alert)* |
| Insurance Expiry | today + 90 days |
| Fitness Expiry | today + 180 days |
| Fuel Type | `petrol` |
| Tank Capacity | `13` |
| Current Odometer | `4250` |

#### Vehicle 2 — Van
| Field | Value |
| --- | --- |
| Type | `van` |
| Plate Number | `DHA-99-8877` |
| Make | `Toyota` |
| Model | `HiAce` |
| Year | `2021` |
| Color | `White` |
| Registration Expiry | today + 365 days |
| Insurance Expiry | today + 15 days *(triggers alert)* |
| Fuel Type | `diesel` |
| Current Odometer | `48000` |

### 9.2 Verify list

URL: `/business/vehicles`

Expected:
- 2 cards
- Both cards show **Expiry alert within 30 days** banner (bike: registration; van: insurance)
- Status badge → green **available**

### 9.3 Assign bike to Rahim

In Tinker (or via assign button when wired):
```php
$bike = App\Models\Vehicle::where('plate_number','DHA-12-3456')->first();
App\Models\VehicleAssignment::create([
    'vehicle_id' => $bike->id,
    'team_member_id' => 1,
    'business_profile_id' => $bike->business_profile_id,
    'assigned_by' => auth()->id() ?? 1,
    'odometer_at_assignment' => 4250,
]);
$bike->update(['status' => 'assigned']);
```

### 9.4 Log fuel

URL: `/business/vehicles/{bike_id}/fuel`

| Field | Value |
| --- | --- |
| Date | today |
| Liters | `8.5` |
| Cost/Liter | `130.00` |
| Odometer | `4380` |
| Station | `Padma Oil – Gulshan` |

Expected:
- Total cost = 1105.00
- Vehicle's `current_odometer_km` updated to 4380
- Row appears in fuel log table

### 9.5 Log maintenance

URL: `/business/vehicles/{bike_id}/maintenance`

| Field | Value |
| --- | --- |
| Type | `oil_change` |
| Status | `completed` |
| Date | today |
| Next Date | today + 90 days |
| Odometer at Service | `4380` |
| Next Service Odometer | `7380` |
| Workshop | `Honda Service Centre` |
| Cost | `850.00` |

Expected: record appears in maintenance history.

### 9.6 Return the bike

```php
$assign = App\Models\VehicleAssignment::where('status','active')->first();
$assign->update(['returned_at' => now(), 'odometer_at_return' => 4380, 'status' => 'returned']);
$bike->update(['status' => 'available', 'current_odometer_km' => 4380]);
```

Expected: vehicle card status flips back to **available**.

---

## 10. Compensation & Payroll

**Spec ref:** `team_management.md` § Compensation Calculation, § `team_compensation`

### 10.1 Set compensation for each member

In Tinker:
```php
$currency = App\Models\Currency::first();

// Rahim — pure salary, BDT 30000/mo
App\Models\TeamCompensation::create([
    'team_member_id' => 1,
    'effective_from' => now()->startOfMonth(),
    'base_salary_monthly' => 30000,
    'salary_currency_id' => $currency->id,
    'payment_frequency' => 'monthly',
]);

// Karim — commission only, BDT 500 per completed job
App\Models\TeamCompensation::create([
    'team_member_id' => 2,
    'effective_from' => now()->startOfMonth(),
    'commission_type' => 'fixed_per_job',
    'commission_value' => 500,
    'commission_currency_id' => $currency->id,
    'payment_frequency' => 'monthly',
]);

// Salma — hybrid: BDT 15000 base + BDT 300/job
App\Models\TeamCompensation::create([
    'team_member_id' => 3,
    'effective_from' => now()->startOfMonth(),
    'base_salary_monthly' => 15000,
    'salary_currency_id' => $currency->id,
    'commission_type' => 'fixed_per_job',
    'commission_value' => 300,
    'commission_currency_id' => $currency->id,
    'weekly_guarantee_amount' => 4000,
    'payment_frequency' => 'monthly',
]);
```

### 10.2 Mark some jobs as completed (with commission earned)

```php
App\Models\TeamJobAssignment::where('team_member_id', 2)
    ->update(['status' => 'completed', 'completed_at' => now()->subDays(3), 'commission_earned' => 500]);

// Repeat for another Karim job
$job = App\Models\TeamJobAssignment::where('team_member_id', 2)->first();
App\Models\TeamJobAssignment::create([
    'team_member_id' => 2,
    'service_request_id' => $job->service_request_id,
    'business_profile_id' => $job->business_profile_id,
    'assignment_type' => 'assistant',
    'assigned_by' => 1,
    'status' => 'completed',
    'completed_at' => now()->subDay(),
    'commission_earned' => 500,
]);
```

### 10.3 View payroll

URL: `/business/payroll?month={current Y-m}`

Expected:
| Member | Type | Hours | Jobs | Salary | Commission | Total Pay |
| --- | --- | --- | --- | --- | --- | --- |
| Rahim | salary | 4.0 | 0 | 30000 | — | ৳ 30000 |
| Karim | commission | 0 | 2 | — | 1000 | ৳ 1000 |
| Salma | hybrid | 0 | 0 | — | — | ৳ 15000 |

Grand Total card: ৳ 46000

### 10.4 Export CSV

Click **Export CSV** button.

Expected: download `payroll-YYYY-MM.csv` with one row per member.

### 10.5 Mark period processed

Click **Mark Period as Processed** button.

Expected: `team_compensation.next_payout_date` advanced one month forward for each active member.

---

## 11. Performance Analytics

**Spec ref:** `team_management.md` § Performance Analytics

### 11.1 Add a customer rating to a completed job

```php
$job = App\Models\TeamJobAssignment::where('status','completed')->first();
$job->update([
    'customer_rating' => 5,
    'customer_feedback' => 'Excellent, quick service!',
    'actual_travel_time_minutes' => 25,
    'work_duration_minutes' => 90,
]);
```

### 11.2 View team analytics

URL: `/business/analytics/team`

Expected:
- KPI cards: Total Jobs, Completed, Avg Completion Rate, Avg Rating
- Per-member table ordered by completion rate descending
- Karim should have completion rate = 100%, avg rating = 5.0
- "View →" link on each row navigates to member detail

### 11.3 View individual member analytics

URL: `/business/analytics/team/{karim_id}`

Expected:
- 5 KPI cards: Jobs, Completed, Avg Rating, Avg Travel (min), Avg Work (min)
- Recent jobs table (last 30 days)

### 11.4 Tech earnings view

URL: `/tech/earnings`

Expected: total earnings card + paginated list of completed jobs with commission per row. Tabs: Today / Week / Month / Year.

---

## 12. Expiry & Low-Stock Scheduled Command

**Spec ref:** `team_management.md` § Expiry alert logic

### 12.1 Set up data to trigger every alert type

```php
// Make sure something triggers each branch:

// Vehicle registration expiring soon (already done in 9.1)
App\Models\Vehicle::first()->update(['registration_expiry' => now()->addDays(10)]);

// Vehicle next-service due soon
$vm = App\Models\VehicleMaintenance::first();
if ($vm) $vm->update(['status' => 'scheduled', 'next_service_date' => now()->addDays(3)]);

// Equipment next-maintenance due soon
$em = App\Models\EquipmentMaintenance::first();
if ($em) $em->update(['status' => 'scheduled', 'next_maintenance_date' => now()->addDays(2)]);

// Drop an inventory item below threshold
$inv = App\Models\Inventory::where('sku','CAP-25UF')->first();
$inv->update(['quantity_in_stock' => 2]); // threshold is 5
```

### 12.2 Run the command

```bash
php artisan team:expiry-alerts
```

Expected stdout:
```
Running team expiry alerts…
  → user_id=X: Vehicle DHA-12-3456: registration expiry on DD MMM YYYY
  → user_id=X: Vehicle DHA-12-3456: service due DD MMM YYYY
  → user_id=X: Equipment Bosch Cordless Drill: maintenance due DD MMM YYYY
  → user_id=X: Low stock: AC Capacitor 25µF (2 pcs remaining)
Done. 4 alert(s) generated.
```

### 12.3 Verify side-effects

- Check `storage/logs/laravel.log` — 4 `[team-alert]` info lines
- Verify Redis/cache counter: `Cache::get('team_alerts:user_X')` should equal 4

### 12.4 Idempotency

Re-run the command. Expected: 4 alerts again (no dedup logic in v1 — by design, business sees daily reminders until they resolve the underlying issue).

### 12.5 Verify automatic scheduling

```bash
php artisan schedule:list
```

Expected: shows `team:expiry-alerts` scheduled at `0 7 * * *` (daily 07:00).

---

## 13. Full Walkthrough Cheat-Sheet

A condensed test sequence to verify end-to-end in ~10 minutes:

| # | Action | Where | Expected |
| --- | --- | --- | --- |
| 1 | Create 4 roles | `/business/team/roles/create` | Cards visible |
| 2 | Invite 3 members | `/business/team/invite` | Members listed |
| 3 | Edit member services | `/business/team/{id}/edit` | Skills updated |
| 4 | Seed 3 service requests | Tinker | Visible in dispatch |
| 5 | Assign all 3 jobs | `/business/dispatch` | Job count rises |
| 6 | Optimize schedule | `/business/schedule/{date}` | Waypoints created |
| 7 | Publish schedule | Same page | Green badge |
| 8 | Simulate clock-in | Tinker | Shows in board |
| 9 | Simulate location ping | Tinker | Marker on live map |
| 10 | Add 3 equipment | `/business/equipment/create` | Codes auto-assigned |
| 11 | Assign + return drill | Tinker | Status flips |
| 12 | Add 4 inventory items | `/business/inventory/create` | One low-stock highlighted |
| 13 | Restock copper wire | Inline form | Stock updates |
| 14 | Log materials on a job | Tinker | Stock decremented |
| 15 | Add 2 vehicles | `/business/vehicles/create` | Expiry banners visible |
| 16 | Log fuel + service | Vehicle pages | Tables populate |
| 17 | Set 3 compensations | Tinker | DB rows |
| 18 | Complete jobs | Tinker | Status updated |
| 19 | View payroll | `/business/payroll` | Totals correct |
| 20 | Export CSV | Button | File downloads |
| 21 | View team analytics | `/business/analytics/team` | KPIs populated |
| 22 | Run expiry command | CLI | 4 alerts logged |

---

## Document Control

| Version | Date | Notes |
| --- | --- | --- |
| 1.0 | 2026-05-30 | Initial walkthrough. Covers all 12 capabilities listed in team_management.md Overview table. |
