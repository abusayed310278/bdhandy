# ServiceHub BD — Complete Workflow Reference

> **Audience:** Anyone onboarding to the platform — business owners, team managers, or technicians.
> **Scope:** Full lifecycle of a service request, team member activities, and asset management.
> **Base URL:** `https://bdhandy.test`

---

## Part 1 — Service Request Lifecycle

### Overview

```
Customer Request → Business Accepts → Job Dispatched → Team Member Accepts
→ Work Performed → Job Completed → Invoice Created → Payment → Payout
```

---

### Step 1 — Customer Places a Request

- Customer visits the platform and submits a service request.
- Fields: service type, address, preferred date/time, urgency (normal / urgent / emergency), description, optional attachments.
- A unique `request_number` is auto-generated (e.g. `REQ-0001`).
- `ServiceRequest.request_status` = **`pending`**

---

### Step 2 — Business Provider Reviews & Accepts

**Portal:** `/provider/requests`

1. Business sees the incoming request in their requests list.
2. They review the details — customer info, location, service, urgency.
3. They click **"Mark as Accepted"**.
4. `ServiceRequest.request_status` → **`accepted`**

> ⚠️ Only `accepted` requests appear in Job Dispatch. Pending or cancelled requests cannot be assigned to team members.

---

### Step 3 — Business Dispatches the Job

**Portal:** `/business/dispatch`

1. The left panel shows all **accepted, unassigned** service requests.
2. The right panel shows all **active team members** with their current job load.
3. Business selects a team member and a **scheduled date & time** (required).
4. Clicks **Assign**.

**What happens:**
- A `TeamJobAssignment` record is created with `status = assigned`.
- The job disappears from the unassigned queue.
- The team member's active job count increments.

> **Unassign:** If needed before the member accepts, the business can click **Unassign** next to the job in the Team Load panel. This marks the assignment `reassigned` and returns the job to the queue.
>
> **Reassign:** Same button flow, but selects a different member. The existing assignment row is updated in place (no duplicate created).

---

### Step 4 — Schedule Published (Optional but Recommended)

**Portal:** `/business/schedule/{date}`

1. Business visits the Schedule page and selects the job date.
2. Clicks **Create Schedule** (or **Optimize**) for the relevant team member.
3. The system sequences the member's jobs for that day by `scheduled_start_time`.
4. Business clicks **Publish**.

**What happens:**
- A `TeamDailySchedule` record is created/updated with `is_published = true`.
- `TeamScheduleWaypoints` are created with sequence numbers (stop 1, stop 2, etc.).
- The published schedule becomes visible on the team member's portal.

---

### Step 5 — Team Member Sees the Job

**Team Member Portal:** `/tech/schedule` (Today's Schedule) or `/tech/jobs` (My All Jobs)

- **Today's Schedule** shows jobs in optimised sequence with stop numbers, addresses, and scheduled times.
- **My All Jobs** shows a full calendar view of all assigned jobs across all dates.
- Each job card shows a **View** button linking to the full job detail page.

---

### Step 6 — Team Member Accepts or Rejects

**Portal:** `/tech/jobs/{id}` or inline on `/tech/schedule`

The job starts in `assigned` status. Two buttons are shown:

| Button | Action |
|--------|--------|
| **Accept** | Assignment → `accepted`; ServiceRequest → **`in_progress`** |
| **Reject** | Assignment → `rejected`; ServiceRequest stays `accepted` (business must reassign) |

> Once accepted or rejected, Accept/Reject buttons are hidden permanently. Only operational status buttons are shown next.

---

### Step 7 — Team Member Performs the Work

**Portal:** `/tech/jobs/{id}` or `/tech/schedule`

After accepting, operational status buttons appear **only on or after the scheduled date**. If today is before the scheduled date, a notice is shown: *"Operational actions available from {date}"*.

**Status progression:**

```
accepted → en_route → arrived → in_progress (Start Work) → [paused →] completed
```

| Status | Meaning |
|--------|---------|
| `en_route` | Member is travelling to the customer location |
| `arrived` | Member has reached the site |
| `in_progress` | Work has started (timestamp recorded: `started_at`) |
| `paused` | Work temporarily stopped |
| `completed` | Job finished (timestamp recorded: `completed_at`) |

> **Permissions matter:** The `update_status` permission on the team member's role controls access to these buttons. `accept_reject` is a separate permission guarding Accept/Reject specifically.

---

### Step 8 — Job Completion Syncs the Service Request

When the team member marks the assignment **`completed`**:

- `TeamJobAssignment.status` → `completed`
- `TeamJobAssignment.completed_at` → current timestamp
- `ServiceRequest.request_status` → **`completed`**
- `ServiceRequest.completed_at` → current timestamp

The business provider's request page now shows the completed status and unlocks invoice creation.

---

### Step 9 — Business Creates Invoice

**Portal:** `/provider/requests/{id}`

Once the request is `completed`, a **"Create Invoice / Receipt"** button appears.

1. Business clicks **Create Invoice** → navigates to `/provider/invoices/create?request={id}`.
2. Fills in: line items, pricing, tax, discount, currency, due date.
3. Invoice is saved with `payment_status = pending`.
4. Invoice can be viewed, shared with the customer, and marked as paid.

---

### Step 10 — Team Member Assignment Card on Request Page

**Portal (Business only):** `/provider/requests/{id}`

A **"Assigned Team Member"** card is visible to business users only (view-only):

- Member name, designation, employee code
- Current assignment status badge (colour-coded)
- Timestamps: Scheduled, Arrived, Started, Completed

---

## Part 2 — Team Member Compensation & Payroll

### Compensation Types

Set when inviting a team member at `/business/team/invite`:

| Type | How earnings are calculated |
|------|-----------------------------|
| **Salary** | Fixed monthly base salary regardless of jobs completed |
| **Commission** | Per-job fixed amount or percentage of job value |
| **Hybrid** | Base salary + per-job commission, with optional weekly guarantee |

Compensation details are stored in `team_compensation` with an `effective_from` date. Multiple records track pay changes over time; only the record with `effective_to = NULL` is the current active one.

---

### Commission Tracking per Job

When a job assignment is completed, the business can record `commission_earned` on the `TeamJobAssignment` record (set during dispatch or payroll calculation).

---

### Payroll Calculation

**Portal:** `/business/payroll?month={Y-m}`

1. Business navigates to Payroll for a given month.
2. System calculates pay per member:
   - **Salary:** `base_salary_monthly`
   - **Commission:** `SUM(commission_earned)` for completed jobs in period
   - **Hybrid:** `base_salary_monthly + SUM(commission_earned)`, floored by `weekly_guarantee_amount` if applicable
3. Table shows: Member | Type | Completed Jobs | Salary | Commission | **Total Pay**
4. Grand Total card summarises the payroll run.

**Process Payroll:**
1. Business clicks **Mark Period as Processed**.
2. `next_payout_date` advances one payment cycle (weekly / biweekly / monthly).
3. Records serve as the permanent payout audit trail.

**Export:**
- Click **Export CSV** → downloads `payroll-YYYY-MM.csv` with one row per member.

---

## Part 3 — Attendance Tracking

### Team Member Clocks In

**Portal:** `/tech/attendance`

1. Member opens Attendance page.
2. Clicks **Clock In** → a form expands asking for an optional location note.
3. The browser auto-captures GPS coordinates (if permitted).
4. Submits → `TeamAttendance` record created with `clock_in_time`, `status = clocked_in`.

> **Permission required:** `attendance.clock_in_out` must be enabled on the member's team role.

### Clock Out

1. While clocked in, a **Clock Out** button replaces the Clock In widget.
2. GPS coordinates captured on submission.
3. `clock_out_time` recorded; `total_hours` calculated automatically.
4. `status` → `clocked_out`.

### Business Verifies Attendance

**Portal:** `/business/attendance` (today) or `/business/attendance/{date}`

- Business sees all members with clock-in/out times, hours, and status.
- Clicks **Verify** to mark a record as verified (`is_verified = true`).
- Member history at `/business/attendance/{member}/history` shows all-time attendance with KPI summary (total days, completed days, total hours, verified count).

---

## Part 4 — Equipment Lifecycle

```
Add Equipment → Assign to Member → Member Uses on Jobs → Return / Report Lost → Maintenance
```

### 4.1 Add Equipment

**Portal:** `/business/equipment/create`

Fields: Name, Category, Brand, Model, Serial Number, Purchase Date, Purchase Price, Condition, Notes, Photo.

- Auto-generated equipment code: `EQ-0001`, `EQ-0002`, …
- Initial `status = available`, `condition` as entered.

---

### 4.2 Assign to Team Member

**Portal:** `/business/equipment` → click **Assign** on any `available` row

1. A dropdown opens inline.
2. Select the team member from the list.
3. Click **Confirm Assign**.

**What happens:**
- `EquipmentAssignment` record created with `status = assigned`.
- `Equipment.status` → `assigned`.
- "Assigned To" column updates to show the member's name.

> Equipment can only be assigned when `status = available`. An already-assigned item shows Return/Lost buttons instead.

---

### 4.3 Team Member Views Equipment

**Team Member Portal:** `/tech/equipment`

- Shows all equipment currently assigned to the member (`status = assigned`).
- Each item has a **Report Issue** button.

**Report Issue flow:**
1. Member selects condition: `damaged` or `lost`.
2. Submits → assignment updated; if `lost`, `Equipment.status` → `lost`.

> **Permission required:** `equipment.report_lost` on the team role.

---

### 4.4 Return Equipment

**Portal:** `/business/equipment` → click **Return** on an `assigned` row

1. Dropdown opens with condition select: Good / Damaged / Lost.
2. Optional return notes.
3. Click **Confirm Return**.

**What happens:**
- `EquipmentAssignment.returned_at`, `returned_condition`, `status = returned`.
- If condition = `good` → `Equipment.status = available`, `condition = good`
- If condition = `damaged` → `Equipment.status = needs_repair`, `condition = needs_repair`

---

### 4.5 Maintenance Records

**Portal:** `/business/equipment/{id}/maintenance`

- Log scheduled, repair, calibration, or inspection maintenance.
- Record: type, date, next date, performed by, cost, status.
- **Alert:** If `next_maintenance_date` is within 7 days, a daily scheduled command (`team:expiry-alerts`) sends an alert to the business manager.

---

### 4.6 Equipment Status Flow

```
available → assigned → returned → available (good) / needs_repair (damaged)
                     → lost
needs_repair → [after repair] → available
available / needs_repair → retired
```

---

## Part 5 — Inventory & Material Usage Lifecycle

```
Add Inventory → Restock → Team Member Logs Usage on Job → Stock Decremented → Low-Stock Alert → Restock
```

### 5.1 Add Inventory Item

**Portal:** `/business/inventory/create`

Fields: Name, SKU, Category, Unit (pcs/meters/kg/liters), Quantity in Stock, Low Stock Threshold, Unit Cost, Currency, Supplier.

---

### 5.2 Team Member Logs Materials on a Job

**Team Member Portal:** `/tech/jobs/{id}`

The **Log Materials Used** section is always visible on the job detail page.

1. Member selects an inventory item from the dropdown (shows current stock).
2. Enters quantity used.
3. Clicks **Log Materials**.

**What happens (atomic transaction):**
- `JobMaterialUsage` record created (links job, member, inventory item, quantity, cost snapshot).
- `InventoryTransaction` record created: `transaction_type = usage`, `quantity = -N`, before/after stock snapshot.
- `Inventory.quantity_in_stock` decremented by quantity used.
- If new stock < `low_stock_threshold` → **alert triggered** (dashboard badge + notification to business manager).

> **Permission required:** `inventory.log_material_usage` on the team role.

---

### 5.3 View Transaction History

**Portal:** `/business/inventory/{item}/transactions`

Full audit trail of every restock, usage, adjustment, return, or loss for an item.

---

### 5.4 Restock

**Portal:** `/business/inventory` → inline restock form on each row

1. Enter quantity to add.
2. Click **+ Restock**.
3. `InventoryTransaction` created: `type = restock`, stock incremented.
4. Low-stock badge disappears if stock now above threshold.

---

### 5.5 Low-Stock Dashboard

**Portal:** `/business/inventory/low-stock`

Shows only items where `quantity_in_stock < low_stock_threshold`, with highlighted rows and ⚠ badge.

---

## Part 6 — Vehicle Lifecycle

```
Add Vehicle → Assign to Member → Member Logs Fuel → Maintenance Logged → Return → Expiry Alerts
```

### 6.1 Add Vehicle

**Portal:** `/business/vehicles/create`

Fields: Type (bike/car/van/truck), Make, Model, Year, Color, Plate Number, VIN, Registration Expiry, Insurance Expiry, Fitness Expiry, Fuel Type, Tank Capacity, Current Odometer.

---

### 6.2 Assign to Team Member

**Portal:** `/business/vehicles/{id}` → Assign button

- One active assignment per vehicle (enforced by unique key).
- `odometer_at_assignment` recorded.
- `Vehicle.status` → `assigned`.

---

### 6.3 Member Logs Fuel

**Portal:** `/business/vehicles/{id}/fuel`

Fields: Date, Liters, Cost per Liter (auto-calculates total), Odometer Reading, Station Name, Receipt Photo.

- `Vehicle.current_odometer_km` updated to new odometer reading.
- Full fuel history preserved.

---

### 6.4 Maintenance Records

**Portal:** `/business/vehicles/{id}/maintenance`

Types: oil change, tyre, brake, engine, body, inspection, other.

Fields: Date, Odometer at Service, Next Service Date, Next Service Odometer, Workshop, Cost.

**Alert:** If `next_service_date` is within 7 days → daily alert to business manager.

---

### 6.5 Return Vehicle

**Portal:** `/business/vehicles` → Return button

- `odometer_at_return` recorded.
- `VehicleAssignment.status = returned`.
- `Vehicle.status` → `available`.
- `Vehicle.current_odometer_km` updated.

---

### 6.6 Expiry Alerts

The `team:expiry-alerts` artisan command runs daily at 07:00 and checks:

| Check | Threshold |
|-------|-----------|
| Vehicle registration expiry | Within 30 days |
| Vehicle insurance expiry | Within 30 days |
| Vehicle fitness expiry | Within 30 days |
| Vehicle next service date | Within 7 days |
| Equipment next maintenance | Within 7 days |
| Inventory below threshold | Immediately |

All alerts are logged and sent as notifications to the business manager.

---

## Quick Reference — Status Tables

### ServiceRequest.request_status

| Status | Meaning |
|--------|---------|
| `pending` | Customer submitted, awaiting business response |
| `accepted` | Business accepted, ready for dispatch |
| `in_progress` | Team member accepted the job assignment |
| `completed` | Team member marked job complete |
| `cancelled` | Cancelled by business or customer |

### TeamJobAssignment.status

| Status | Triggered by | Effect on ServiceRequest |
|--------|-------------|--------------------------|
| `assigned` | Business dispatches | — |
| `accepted` | Team member accepts | → `in_progress` |
| `rejected` | Team member rejects | — (business must reassign) |
| `en_route` | Team member | — |
| `arrived` | Team member | — |
| `in_progress` | Team member (Start Work) | — |
| `paused` | Team member | — |
| `completed` | Team member | → `completed` |
| `reassigned` | Business unassigns/reassigns | job returns to dispatch queue |

### Equipment.status

| Status | How reached |
|--------|------------|
| `available` | Added new, or returned in good condition |
| `assigned` | Business assigns to a member |
| `needs_repair` | Returned damaged |
| `in_maintenance` | Manually set during maintenance |
| `lost` | Reported lost by member or business |
| `retired` | Manually retired |

---

## Portal URL Reference

### Business Portal

| Page | URL |
|------|-----|
| Incoming Requests | `/provider/requests` |
| Request Detail | `/provider/requests/{id}` |
| Job Dispatch | `/business/dispatch` |
| Daily Schedule | `/business/schedule/{date}` |
| Attendance Board | `/business/attendance` |
| Member Attendance History | `/business/attendance/{member}/history` |
| Live Location | `/business/location` |
| Team Members | `/business/team` |
| Invite Member | `/business/team/invite` |
| Team Roles | `/business/team/roles` |
| Equipment | `/business/equipment` |
| Inventory | `/business/inventory` |
| Vehicles | `/business/vehicles` |
| Payroll | `/business/payroll` |
| Team Analytics | `/business/analytics/team` |

### Team Member Portal

| Page | URL |
|------|-----|
| Today's Schedule | `/tech/schedule` |
| My All Jobs (Calendar) | `/tech/jobs` |
| Job Detail | `/tech/jobs/{id}` |
| Attendance / Clock In-Out | `/tech/attendance` |
| My Equipment | `/tech/equipment` |
| My Earnings | `/tech/earnings` |

---

*Document version: 1.0 — 2026-05-30*
