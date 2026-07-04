# Team Management for Business Providers

> **Extension of:** concept.md — Section 8.7
> **Project:** ServiceHub BD
> **Version:** 1.1
> **Scope:** Business providers only (`provider_type = business`)
> **Desktop-first** — web portal for business dashboard + technician portal; mobile app deferred to Phase 2

---

## Overview

Business providers (registered companies with multiple staff) can manage their workforce through a comprehensive team management subsystem. This transforms a solo-provider platform into an enterprise-ready solution for service companies with field technicians.

| Capability | Description | Value Proposition |
| --- | --- | --- |
| Team Member Onboarding | Invite, onboard, verify technicians | Centralised workforce management |
| Service Skill Assignment | Map technicians to specific services | Smart, accurate job dispatch |
| Role-Based Permissions | Granular access control per team member | Delegate without security risk |
| Attendance Tracking | Clock-in/out with location verification | Reduce time theft, payroll accuracy |
| Live Location Tracking | Real-time GPS tracking during work hours | Customer ETA, dispatch efficiency |
| Compensation Models | Salary, commission, or hybrid | Flexible pay structures |
| Smart Job Assignment | Skill + proximity-based task allocation | Optimise travel, reduce fuel cost |
| Multi-Task Assignment | Assign multiple jobs to one technician | Day planning, route optimisation |
| Route Optimisation | Best-path sequencing for assigned tasks | 20–30% reduction in travel time |
| Equipment & Tool Tracking | Assign, return, and maintain tools per technician | Reduce asset loss |
| Inventory Management | Parts and materials consumed per job | Operational cost visibility |
| Vehicle Management | Track, assign, and maintain company vehicles | Fleet accountability |
| Performance Analytics | Completion rates, ratings, efficiency scores | Data-driven management |

---

## Database Schema

### `team_members`

```sql
team_members (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_profile_id BIGINT UNSIGNED NOT NULL,   -- FK → provider_profiles (business only)

    -- Identity
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(20) UNIQUE NOT NULL,
    profile_photo VARCHAR(255),

    -- Verification
    nid_number VARCHAR(50),
    nid_photo VARCHAR(255),
    passport_number VARCHAR(50),
    passport_photo VARCHAR(255),

    -- Employment
    employee_code VARCHAR(20) UNIQUE NOT NULL,       -- e.g. "EMP-DHK-0042"
    designation VARCHAR(100),                        -- "Senior Technician", "Apprentice"
    joining_date DATE,
    status ENUM('active', 'inactive', 'suspended', 'terminated') DEFAULT 'active',

    compensation_type ENUM('salary', 'commission', 'hybrid') DEFAULT 'salary',
    team_role_id BIGINT UNSIGNED NULL,               -- FK → team_roles

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_business_status (business_profile_id, status),
    INDEX idx_employee_code (employee_code)
);
```

### `team_member_services` *(new)*

Maps each technician to the services they are qualified to handle. Only service requests matching these services will be suggested or auto-assigned to the technician.

```sql
team_member_services (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    team_member_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,             -- FK → services
    business_profile_id BIGINT UNSIGNED NOT NULL,

    skill_level ENUM('junior', 'mid', 'senior') DEFAULT 'mid',
    is_primary BOOLEAN DEFAULT FALSE,                -- main specialty

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (team_member_id) REFERENCES team_members(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    UNIQUE KEY uk_member_service (team_member_id, service_id),
    INDEX idx_service_lookup (service_id, business_profile_id)
);
```

> Smart job assignment filters team members using this table — only technicians with a matching `service_id` are candidates for a given request.

### `team_compensation`

```sql
team_compensation (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    team_member_id BIGINT UNSIGNED NOT NULL,
    effective_from DATE NOT NULL,
    effective_to DATE NULL,                           -- NULL = currently active

    base_salary_monthly DECIMAL(12,2),
    salary_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    commission_type ENUM('percentage', 'fixed_per_job', 'tiered'),
    commission_value DECIMAL(10,2),
    commission_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    weekly_guarantee_amount DECIMAL(10,2),

    payment_frequency ENUM('weekly', 'biweekly', 'monthly') DEFAULT 'monthly',
    next_payout_date DATE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    INDEX idx_effective (effective_from, effective_to)
);
```

### `team_attendance`

```sql
team_attendance (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    team_member_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    clock_in_time TIMESTAMP NOT NULL,
    clock_in_latitude DECIMAL(10,8),
    clock_in_longitude DECIMAL(11,8),
    clock_in_address VARCHAR(500),
    clock_in_photo VARCHAR(255),

    clock_out_time TIMESTAMP NULL,
    clock_out_latitude DECIMAL(10,8) NULL,
    clock_out_longitude DECIMAL(11,8) NULL,
    clock_out_photo VARCHAR(255) NULL,

    total_hours DECIMAL(5,2) GENERATED ALWAYS AS (
        TIMESTAMPDIFF(MINUTE, clock_in_time, clock_out_time) / 60.0
    ) STORED,

    status ENUM('clocked_in', 'on_break', 'clocked_out') DEFAULT 'clocked_in',
    is_verified BOOLEAN DEFAULT FALSE,
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_member_date (team_member_id, DATE(clock_in_time)),
    INDEX idx_status (status)
);
```

### `team_location_tracking`

```sql
team_location_tracking (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    team_member_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    accuracy_meters INT,
    heading DECIMAL(5,2),
    speed_kmh DECIMAL(5,2),
    battery_level INT,
    is_moving BOOLEAN DEFAULT FALSE,
    location_time TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_member_recent (team_member_id, created_at DESC),
    INDEX idx_business_active (business_profile_id, created_at),
    SPATIAL INDEX idx_location_point (geom)
);
```

> **Hot path:** latest location per member is also kept in Redis with a 24h TTL. The DB table serves as the audit trail and is bulk-purged after 30 days.

### `team_job_assignments`

```sql
team_job_assignments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    team_member_id BIGINT UNSIGNED NOT NULL,
    service_request_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    assignment_type ENUM('primary', 'assistant', 'supervisor') DEFAULT 'primary',
    assigned_by BIGINT UNSIGNED NOT NULL,

    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    scheduled_start_time TIMESTAMP,
    scheduled_end_time TIMESTAMP,

    status ENUM(
        'assigned', 'accepted', 'en_route', 'arrived',
        'in_progress', 'paused', 'completed', 'rejected', 'reassigned'
    ) DEFAULT 'assigned',

    travel_time_minutes INT,
    actual_travel_time_minutes INT,
    work_duration_minutes INT,
    distance_traveled_km DECIMAL(8,2),

    arrived_at_location TIMESTAMP NULL,
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,

    customer_rating TINYINT CHECK (customer_rating BETWEEN 1 AND 5),
    customer_feedback TEXT,

    commission_earned DECIMAL(10,2),
    commission_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    FOREIGN KEY (service_request_id) REFERENCES service_requests(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    UNIQUE KEY uk_request_primary (service_request_id, assignment_type),
    INDEX idx_team_member_status (team_member_id, status),
    INDEX idx_scheduled (scheduled_start_time)
);
```

### `team_daily_schedule`

```sql
team_daily_schedule (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    team_member_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,
    schedule_date DATE NOT NULL,

    optimized_route JSON,
    total_distance_km DECIMAL(10,2),
    estimated_total_duration_minutes INT,

    total_jobs_assigned INT DEFAULT 0,
    total_jobs_completed INT DEFAULT 0,
    total_earnings_day DECIMAL(10,2),

    is_published BOOLEAN DEFAULT FALSE,
    is_accepted BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    UNIQUE KEY uk_member_date (team_member_id, schedule_date),
    INDEX idx_date (schedule_date)
);
```

### `team_schedule_waypoints`

```sql
team_schedule_waypoints (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    daily_schedule_id BIGINT UNSIGNED NOT NULL,
    job_assignment_id BIGINT UNSIGNED NOT NULL,
    sequence_order INT NOT NULL,
    estimated_travel_time_from_previous_minutes INT,
    estimated_distance_from_previous_km DECIMAL(8,2),

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (daily_schedule_id) REFERENCES team_daily_schedule(id) ON DELETE CASCADE,
    FOREIGN KEY (job_assignment_id) REFERENCES team_job_assignments(id),
    UNIQUE KEY uk_schedule_order (daily_schedule_id, sequence_order)
);
```

### `team_roles`

```sql
team_roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_profile_id BIGINT UNSIGNED NOT NULL,
    role_name VARCHAR(100) NOT NULL,
    permissions JSON NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    UNIQUE KEY uk_business_role (business_profile_id, role_name)
);
```

**Permissions JSON structure:**

```json
{
  "jobs": {
    "view_assigned": true,
    "view_all_team_jobs": false,
    "accept_reject": true,
    "update_status": true,
    "reassign": false
  },
  "attendance": {
    "clock_in_out": true,
    "view_own_history": true,
    "view_team_attendance": false,
    "edit_attendance": false
  },
  "schedule": {
    "view_daily_schedule": true,
    "request_changes": true,
    "create_schedule": false,
    "optimize_route": false
  },
  "earnings": {
    "view_own_earnings": true,
    "view_team_earnings": false
  },
  "profile": {
    "edit_own_profile": true,
    "edit_team_member_profiles": false,
    "invite_members": false,
    "terminate_members": false
  },
  "reports": {
    "view_own_performance": true,
    "view_team_performance": false,
    "export_reports": false
  },
  "equipment": {
    "view_assigned_equipment": true,
    "report_lost": true,
    "manage_equipment": false
  },
  "inventory": {
    "log_material_usage": true,
    "view_inventory": true,
    "manage_inventory": false
  },
  "vehicles": {
    "view_assigned_vehicle": true,
    "log_fuel": true,
    "manage_vehicles": false
  }
}
```

---

## Equipment & Tool Tracking

Business providers assign tools and equipment to technicians. The system tracks handover, return, condition, loss reports, and maintenance schedules.

### `equipment`

```sql
equipment (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    name VARCHAR(255) NOT NULL,                      -- "Drill Machine", "Multimeter"
    code VARCHAR(50) UNIQUE NOT NULL,                -- "EQ-DHK-0021"
    category VARCHAR(100),                           -- "Power Tools", "Measuring", "HVAC"
    brand VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    purchase_date DATE,
    purchase_price DECIMAL(10,2),
    purchase_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    condition ENUM('new', 'good', 'fair', 'needs_repair', 'retired') DEFAULT 'good',
    status ENUM('available', 'assigned', 'in_maintenance', 'lost', 'retired') DEFAULT 'available',

    notes TEXT,
    photo VARCHAR(255),

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_business_status (business_profile_id, status),
    INDEX idx_code (code)
);
```

### `equipment_assignments`

```sql
equipment_assignments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    equipment_id BIGINT UNSIGNED NOT NULL,
    team_member_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,
    job_assignment_id BIGINT UNSIGNED NULL,          -- NULL = general/permanent assignment

    assigned_by BIGINT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    returned_at TIMESTAMP NULL,
    returned_condition ENUM('good', 'damaged', 'lost') NULL,
    return_notes TEXT,

    status ENUM('assigned', 'returned', 'lost') DEFAULT 'assigned',

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_equipment_status (equipment_id, status),
    INDEX idx_member_active (team_member_id, status)
);
```

### `equipment_maintenance`

```sql
equipment_maintenance (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    equipment_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    maintenance_type ENUM('scheduled', 'repair', 'calibration', 'inspection') DEFAULT 'scheduled',
    description TEXT,
    performed_by VARCHAR(255),                       -- technician name or external vendor
    cost DECIMAL(10,2),
    cost_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    maintenance_date DATE NOT NULL,
    next_maintenance_date DATE,

    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_next_maintenance (next_maintenance_date, status)
);
```

---

## Inventory Management

Track parts, materials, and consumables used by technicians during jobs. Inventory decreases automatically when a technician logs material usage for a completed job.

### `inventory`

```sql
inventory (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    name VARCHAR(255) NOT NULL,                      -- "Capacitor 25µF", "AC Filter", "PVC Pipe ½in"
    sku VARCHAR(100) UNIQUE,                         -- internal stock-keeping unit
    category VARCHAR(100),                           -- "HVAC Parts", "Electrical", "Plumbing"
    unit VARCHAR(50),                                -- "pcs", "meters", "kg", "liters"

    quantity_in_stock DECIMAL(10,2) DEFAULT 0,
    low_stock_threshold DECIMAL(10,2) DEFAULT 5,     -- alert when stock falls below this

    unit_cost DECIMAL(10,2),
    cost_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    supplier_name VARCHAR(255),
    supplier_contact VARCHAR(255),

    notes TEXT,
    photo VARCHAR(255),

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_business_sku (business_profile_id, sku),
    INDEX idx_low_stock (business_profile_id, quantity_in_stock)
);
```

### `inventory_transactions`

```sql
inventory_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    inventory_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    transaction_type ENUM('restock', 'usage', 'adjustment', 'return', 'loss') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,                 -- positive = stock in, negative = stock out
    quantity_before DECIMAL(10,2) NOT NULL,
    quantity_after DECIMAL(10,2) NOT NULL,

    reference_type VARCHAR(50),                      -- 'job_material_usage', 'manual', 'purchase_order'
    reference_id BIGINT UNSIGNED NULL,               -- FK to relevant record

    performed_by BIGINT UNSIGNED,                    -- user_id
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (inventory_id) REFERENCES inventory(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_inventory_date (inventory_id, created_at),
    INDEX idx_type (transaction_type, created_at)
);
```

### `job_material_usage`

Technician logs what parts were used during a specific job. Triggers an `inventory_transactions` deduction automatically.

```sql
job_material_usage (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    job_assignment_id BIGINT UNSIGNED NOT NULL,
    team_member_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,
    inventory_id BIGINT UNSIGNED NOT NULL,

    quantity_used DECIMAL(10,2) NOT NULL,
    unit_cost_at_time DECIMAL(10,2),                 -- snapshot of cost when used
    cost_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    notes TEXT,
    logged_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (job_assignment_id) REFERENCES team_job_assignments(id),
    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    FOREIGN KEY (inventory_id) REFERENCES inventory(id),
    INDEX idx_job (job_assignment_id),
    INDEX idx_inventory_usage (inventory_id, logged_at)
);
```

**Auto-deduction workflow:**
```
Technician marks material usage on job detail page
  → job_material_usage row created
  → inventory_transactions row created (type = 'usage', quantity = negative)
  → inventory.quantity_in_stock decremented
  → if quantity_in_stock < low_stock_threshold → alert sent to business manager
```

---

## Vehicle Management

Track company vehicles (bikes, vans, cars) assigned to technicians, with fuel records, service history, and insurance/registration expiry alerts.

### `vehicles`

```sql
vehicles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    -- Identity
    vehicle_type ENUM('bike', 'car', 'van', 'truck', 'other') NOT NULL,
    make VARCHAR(100),                               -- "Honda", "Toyota"
    model VARCHAR(100),                              -- "CB125", "HiAce"
    year YEAR,
    color VARCHAR(50),
    plate_number VARCHAR(30) UNIQUE NOT NULL,
    vin VARCHAR(50),                                 -- chassis number

    -- Registration & Insurance
    registration_expiry DATE,
    insurance_expiry DATE,
    fitness_expiry DATE,

    -- Fuel
    fuel_type ENUM('petrol', 'diesel', 'cng', 'electric') DEFAULT 'petrol',
    fuel_tank_capacity_liters DECIMAL(6,2),
    current_odometer_km DECIMAL(10,2) DEFAULT 0,

    status ENUM('available', 'assigned', 'in_maintenance', 'retired') DEFAULT 'available',
    photo VARCHAR(255),
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_business_status (business_profile_id, status),
    INDEX idx_plate (plate_number)
);
```

### `vehicle_assignments`

```sql
vehicle_assignments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    team_member_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    assigned_by BIGINT UNSIGNED NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    returned_at TIMESTAMP NULL,

    odometer_at_assignment DECIMAL(10,2),
    odometer_at_return DECIMAL(10,2) NULL,

    status ENUM('active', 'returned') DEFAULT 'active',
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (team_member_id) REFERENCES team_members(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    UNIQUE KEY uk_vehicle_active (vehicle_id, status),  -- one active assignment per vehicle
    INDEX idx_member_vehicle (team_member_id, status)
);
```

### `vehicle_fuel_records`

```sql
vehicle_fuel_records (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    team_member_id BIGINT UNSIGNED NULL,             -- who refuelled
    business_profile_id BIGINT UNSIGNED NOT NULL,

    fuel_date DATE NOT NULL,
    liters_filled DECIMAL(6,2) NOT NULL,
    cost_per_liter DECIMAL(8,2),
    total_cost DECIMAL(10,2),
    cost_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id

    odometer_reading DECIMAL(10,2),
    station_name VARCHAR(255),
    receipt_photo VARCHAR(255),

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_vehicle_date (vehicle_id, fuel_date)
);
```

### `vehicle_maintenance`

```sql
vehicle_maintenance (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    business_profile_id BIGINT UNSIGNED NOT NULL,

    maintenance_type ENUM('oil_change', 'tyre', 'brake', 'engine', 'body', 'inspection', 'other') NOT NULL,
    description TEXT,
    workshop_name VARCHAR(255),

    maintenance_date DATE NOT NULL,
    odometer_at_service DECIMAL(10,2),
    next_service_date DATE,
    next_service_odometer_km DECIMAL(10,2),

    cost DECIMAL(10,2),
    cost_currency_id BIGINT UNSIGNED, // this will be come from currency table, by default use provider_profile->currency_id
    receipt_photo VARCHAR(255),

    status ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (business_profile_id) REFERENCES provider_profiles(id),
    INDEX idx_next_service (next_service_date, status),
    INDEX idx_vehicle_history (vehicle_id, maintenance_date)
);
```

**Expiry alert logic (scheduled job):**
```
Daily job checks:
  - vehicles.registration_expiry within 30 days → alert
  - vehicles.insurance_expiry within 30 days → alert
  - vehicles.fitness_expiry within 30 days → alert
  - vehicle_maintenance.next_service_date within 7 days → alert
  - equipment_maintenance.next_maintenance_date within 7 days → alert
  - inventory.quantity_in_stock < low_stock_threshold → alert
All alerts sent to business manager via notification + dashboard badge.
```

---

## Subscription Plan Additions

New columns on `subscription_plans`:

```sql
ALTER TABLE subscription_plans ADD COLUMN team_member_limit INT DEFAULT 1;
ALTER TABLE subscription_plans ADD COLUMN team_features JSON NULL;
```

**`team_features` example value:**
```json
{
  "attendance": true,
  "location_tracking": true,
  "route_optimization": true,
  "permissions": true,
  "equipment_tracking": true,
  "inventory_management": true,
  "vehicle_management": true
}
```

| Plan | Team Members | Features |
| --- | --- | --- |
| Free | 1 (owner only) | None |
| Starter | 3 | Attendance + job assignment + service skill mapping |
| Quarterly | 5 | + Live location + equipment tracking |
| Half-Yearly | 10 | + Route optimisation + inventory management |
| Yearly | 20 | + Full permissions + vehicle management |
| Bi-Yearly | Unlimited | + Advanced analytics + all features |
| Tri-Yearly | Unlimited | + Dedicated account manager |

---

## Feature Workflows

### Service Skill Assignment

```
When creating or editing a team member:
1. Business admin selects one or more services from their active service list
2. Optionally marks one as primary specialty
3. Sets skill level per service (junior / mid / senior)
4. Saved to team_member_services

During job dispatch:
- System only shows / auto-assigns technicians whose team_member_services
  contains the service_id matching the customer's service request
```

### Attendance

```
1. Technician opens portal → clicks "Clock In"
2. System captures GPS coordinates + timestamp + optional selfie photo
3. Business attendance board updates in real time
4. Optional geofence check: only allow clock-in within service area radius
5. Clock-out records end location and computes total_hours
```

### Live Location

```
- Browser/app sends location every 30–60 seconds while clocked in
- Latest coordinates stored in Redis (24h TTL)
- Rows archived to team_location_tracking for audit (purged after 30 days)
- Business dashboard shows live map with all active technicians
- Customer sees technician ETA when status = en_route (business opt-in feature)
```

### Smart Job Assignment

```
On new inbound service request:
1. Filter team members:
   - service_id exists in team_member_services (skill match)
   - Status = available (not overloaded)
   - Within service radius of job location
2. Score each candidate:
   score = (1 / distance_km)          × 0.40
         + acceptance_rate             × 0.30
         + (customer_rating / 5)       × 0.20
         + (current_job_count == 0)    × 0.10
3. Auto-assign to highest scorer
4. Push notification + in-app alert sent to technician
5. Technician has 5 minutes to accept or decline
6. Auto-reassign on decline or timeout
```

### Route Optimisation

```
Daily schedule generation:
1. Collect all unassigned jobs for the target date
2. Use technician's starting point (home address or last known location)
3. Solve TSP — Nearest Neighbor + 2-opt heuristic (OR-Tools in Phase 2)
4. Constraints: job priority, customer time windows, break times, travel time
5. Persist to team_daily_schedule + team_schedule_waypoints
6. Business manager reviews → publishes to technician portal
```

### Equipment Assignment

```
Assign:
  Business admin selects equipment (status = available) → selects technician
  → equipment_assignments row created → equipment.status = assigned

Return:
  Admin records return → returned_condition captured
  → equipment_assignments.status = returned → equipment.status = available

Lost report:
  Technician or admin marks as lost
  → equipment_assignments.returned_condition = lost
  → equipment.status = lost → alert to business manager
```

### Inventory & Material Usage

```
Technician logs parts used on job detail page:
  → job_material_usage row created per item
  → triggers inventory_transactions (type = usage)
  → inventory.quantity_in_stock decremented
  → if stock < low_stock_threshold: alert sent to business manager

Business manager restocks:
  → inventory_transactions row (type = restock)
  → inventory.quantity_in_stock incremented
```

### Compensation Calculation

```sql
-- Salary-based
total_pay = base_salary_monthly + overtime_hours × overtime_rate

-- Commission-based
total_pay = COUNT(completed_jobs) × commission_per_job
         -- or --
total_pay = SUM(job_value) × commission_percentage

-- Hybrid
total_pay = (base_salary_monthly × attendance_rate)
          + COUNT(completed_jobs) × commission_per_job
```

---

## Permission Roles

| Role | Can Do | Cannot Do |
| --- | --- | --- |
| Manager | Assign jobs, view all locations, manage equipment/inventory/vehicles, run reports | Delete account, change subscription |
| Supervisor | Assign jobs, view team location, approve time-off, edit schedules, log fuel | Edit compensation, invite/terminate members |
| Senior Technician | View own schedule, accept/reject jobs, log material usage, report equipment issues | Assign jobs, manage inventory levels |
| Junior Technician | View own assignments, clock in/out, update job status, log materials | View team data, manage any assets |
| Custom | Business-defined from JSON permission set | — |

---

## Real-time Events (Laravel Reverb)

| Event | Channel | Payload |
| --- | --- | --- |
| Location Update | `private-business.{business_id}` | `{ member_id, lat, lng, heading, speed }` |
| New Job Assignment | `private-team-member.{member_id}` | `{ job_id, customer, location, urgency }` |
| Job Status Change | `private-business.{business_id}` | `{ job_id, status, member_id, timestamp }` |
| Clock-In Alert | `private-business.{business_id}` | `{ member_id, time, location }` |
| Break Request | `private-business.{business_id}` | `{ member_id, type, estimated_duration }` |
| Low Stock Alert | `private-business.{business_id}` | `{ inventory_id, name, quantity_remaining }` |
| Equipment Lost | `private-business.{business_id}` | `{ equipment_id, name, reported_by }` |
| Expiry Alert | `private-business.{business_id}` | `{ type, entity_id, name, expiry_date }` |

---

## Web Routes (Desktop)

```php
// ─── Business: Team Members ───────────────────────────────────────────────
GET    /business/team                               → business.team.index
GET    /business/team/invite                        → business.team.invite
POST   /business/team/invite                        → business.team.invite.store
GET    /business/team/{member}                      → business.team.show
GET    /business/team/{member}/edit                 → business.team.edit
PUT    /business/team/{member}                      → business.team.update
DELETE /business/team/{member}                      → business.team.destroy
POST   /business/team/{member}/role                 → business.team.assign-role
POST   /business/team/{member}/services             → business.team.services.sync

// ─── Business: Roles & Permissions ───────────────────────────────────────
GET    /business/team/roles                         → business.team.roles.index
GET    /business/team/roles/create                  → business.team.roles.create
POST   /business/team/roles                         → business.team.roles.store
GET    /business/team/roles/{role}/edit             → business.team.roles.edit
PUT    /business/team/roles/{role}                  → business.team.roles.update
DELETE /business/team/roles/{role}                  → business.team.roles.destroy

// ─── Business: Job Dispatch ───────────────────────────────────────────────
GET    /business/dispatch                           → business.dispatch.index
POST   /business/dispatch/{job}/assign              → business.dispatch.assign
PUT    /business/dispatch/{assignment}/reassign     → business.dispatch.reassign
GET    /business/dispatch/{job}/suggestions         → business.dispatch.suggestions

// ─── Business: Schedule & Route Optimisation ─────────────────────────────
GET    /business/schedule                           → business.schedule.index
GET    /business/schedule/{date}                    → business.schedule.show
POST   /business/schedule/optimize                  → business.schedule.optimize
POST   /business/schedule/{schedule}/publish        → business.schedule.publish

// ─── Business: Attendance ─────────────────────────────────────────────────
GET    /business/attendance                         → business.attendance.index
GET    /business/attendance/{date}                  → business.attendance.show
GET    /business/attendance/{member}/history        → business.attendance.member-history

// ─── Business: Live Location ──────────────────────────────────────────────
GET    /business/location                           → business.location.live
GET    /business/location/{member}/history          → business.location.member-history

// ─── Business: Equipment & Tools ─────────────────────────────────────────
GET    /business/equipment                          → business.equipment.index
GET    /business/equipment/create                   → business.equipment.create
POST   /business/equipment                          → business.equipment.store
GET    /business/equipment/{equipment}/edit         → business.equipment.edit
PUT    /business/equipment/{equipment}              → business.equipment.update
POST   /business/equipment/{equipment}/assign       → business.equipment.assign
POST   /business/equipment/{equipment}/return       → business.equipment.return
POST   /business/equipment/{equipment}/lost         → business.equipment.lost
GET    /business/equipment/{equipment}/maintenance  → business.equipment.maintenance.index
POST   /business/equipment/{equipment}/maintenance  → business.equipment.maintenance.store

// ─── Business: Inventory ──────────────────────────────────────────────────
GET    /business/inventory                          → business.inventory.index
GET    /business/inventory/create                   → business.inventory.create
POST   /business/inventory                          → business.inventory.store
GET    /business/inventory/{item}/edit              → business.inventory.edit
PUT    /business/inventory/{item}                   → business.inventory.update
POST   /business/inventory/{item}/restock           → business.inventory.restock
GET    /business/inventory/{item}/transactions      → business.inventory.transactions
GET    /business/inventory/low-stock                → business.inventory.low-stock

// ─── Business: Vehicles ───────────────────────────────────────────────────
GET    /business/vehicles                           → business.vehicles.index
GET    /business/vehicles/create                    → business.vehicles.create
POST   /business/vehicles                           → business.vehicles.store
GET    /business/vehicles/{vehicle}/edit            → business.vehicles.edit
PUT    /business/vehicles/{vehicle}                 → business.vehicles.update
POST   /business/vehicles/{vehicle}/assign          → business.vehicles.assign
POST   /business/vehicles/{vehicle}/return          → business.vehicles.return
GET    /business/vehicles/{vehicle}/fuel            → business.vehicles.fuel.index
POST   /business/vehicles/{vehicle}/fuel            → business.vehicles.fuel.store
GET    /business/vehicles/{vehicle}/maintenance     → business.vehicles.maintenance.index
POST   /business/vehicles/{vehicle}/maintenance     → business.vehicles.maintenance.store

// ─── Business: Payroll ────────────────────────────────────────────────────
GET    /business/payroll                            → business.payroll.index
POST   /business/payroll/calculate                  → business.payroll.calculate
POST   /business/payroll/process                    → business.payroll.process
GET    /business/payroll/reports                    → business.payroll.reports
GET    /business/payroll/reports/export             → business.payroll.export

// ─── Business: Performance Analytics ─────────────────────────────────────
GET    /business/analytics/team                     → business.analytics.team
GET    /business/analytics/team/{member}            → business.analytics.member

// ─── Technician Portal ────────────────────────────────────────────────────
GET    /tech/schedule                               → tech.schedule.today
GET    /tech/schedule/{date}                        → tech.schedule.show
GET    /tech/jobs/{assignment}                      → tech.jobs.show
POST   /tech/jobs/{assignment}/status               → tech.jobs.update-status
POST   /tech/jobs/{assignment}/materials            → tech.jobs.log-materials
GET    /tech/attendance/history                     → tech.attendance.history
POST   /tech/attendance/clock-in                    → tech.attendance.clock-in
POST   /tech/attendance/clock-out                   → tech.attendance.clock-out
POST   /tech/location/update                        → tech.location.update        (AJAX/XHR)
GET    /tech/equipment                              → tech.equipment.index
POST   /tech/equipment/{assignment}/report-issue    → tech.equipment.report-issue
GET    /tech/earnings                               → tech.earnings.index
GET    /tech/earnings/{period}                      → tech.earnings.period
```

---

## Dashboard Views

### Business Dashboard

| View | Key Features |
| --- | --- |
| Live Map | Real-time technician positions, colour-coded by status |
| Attendance Board | Who is clocked in, late, absent, on leave |
| Job Dispatch | Assign jobs, view per-technician load, skill-filtered suggestions |
| Route Planner | Auto-optimise tomorrow's schedule, manual drag-adjust |
| Equipment | Asset list, assigned/available status, maintenance due alerts |
| Inventory | Stock levels, low-stock alerts, usage history per job |
| Vehicles | Fleet overview, assignment status, expiry alerts |
| Payroll Centre | Calculate monthly pay, download payslips |
| Performance | Completion rate, avg rating, efficiency by technician |
| Team Management | Add/edit/terminate members, assign roles, set service skills |

### Technician Portal

| View | Key Features |
| --- | --- |
| Today's Schedule | Jobs in optimised order with map |
| Job Detail | Customer info, location link, status update, log materials used |
| Clock In / Out | GPS + optional photo |
| My Equipment | List of currently assigned tools, report issue / lost |
| Earnings | Daily / weekly / monthly breakdown |
| Attendance History | Own clock-in/out records |

---

## Database Indexes

```sql
-- Service skill mapping
CREATE INDEX idx_member_services ON team_member_services (team_member_id);
CREATE INDEX idx_service_dispatch ON team_member_services (service_id, business_profile_id);

-- Location tracking
CREATE INDEX idx_team_location_recent ON team_location_tracking (team_member_id, created_at DESC);
ALTER TABLE team_location_tracking ADD SPATIAL INDEX idx_location_point (geom);

-- Attendance
CREATE INDEX idx_attendance_date_team ON team_attendance (DATE(clock_in_time), team_member_id);
CREATE INDEX idx_attendance_status ON team_attendance (status, clock_in_time);

-- Job assignments
CREATE INDEX idx_job_assignment_status_date ON team_job_assignments (status, scheduled_start_time);
CREATE INDEX idx_job_assignment_team_status ON team_job_assignments (team_member_id, status);

-- Daily schedule
CREATE INDEX idx_daily_schedule_date_business ON team_daily_schedule (schedule_date, business_profile_id);

-- Equipment
CREATE INDEX idx_equipment_business_status ON equipment (business_profile_id, status);
CREATE INDEX idx_equipment_assign_active ON equipment_assignments (team_member_id, status);

-- Inventory
CREATE INDEX idx_inventory_low_stock ON inventory (business_profile_id, quantity_in_stock);
CREATE INDEX idx_inventory_tx_date ON inventory_transactions (inventory_id, created_at);
CREATE INDEX idx_material_usage_job ON job_material_usage (job_assignment_id);

-- Vehicles
CREATE INDEX idx_vehicle_business_status ON vehicles (business_profile_id, status);
CREATE INDEX idx_vehicle_expiry ON vehicles (registration_expiry, insurance_expiry);
CREATE INDEX idx_fuel_vehicle_date ON vehicle_fuel_records (vehicle_id, fuel_date);
CREATE INDEX idx_vehicle_maintenance_next ON vehicle_maintenance (next_service_date, status);
```

---

## Complete Table Reference

| Table | Purpose |
| --- | --- |
| `team_members` | Technicians and staff employed by a business provider |
| `team_member_services` | Services each technician is qualified to handle |
| `team_roles` | Permission role definitions per business |
| `team_compensation` | Salary / commission / hybrid pay structures |
| `team_attendance` | Clock-in/out records with GPS |
| `team_location_tracking` | Live GPS location history |
| `team_job_assignments` | Service request → technician mapping |
| `team_daily_schedule` | Daily optimised job schedule per technician |
| `team_schedule_waypoints` | Ordered stops within a daily schedule |
| `equipment` | Business-owned tools and equipment master list |
| `equipment_assignments` | Tool handover records (assigned / returned / lost) |
| `equipment_maintenance` | Tool maintenance and calibration schedule |
| `inventory` | Parts and materials stock master list |
| `inventory_transactions` | All stock movements (restock, usage, loss) |
| `job_material_usage` | Parts consumed on a specific job assignment |
| `vehicles` | Company vehicle registry |
| `vehicle_assignments` | Vehicle → technician assignment records |
| `vehicle_fuel_records` | Fuel log per vehicle |
| `vehicle_maintenance` | Vehicle service and repair history |

---

## Open Questions

1. Should team members have their own `users` table account (login-capable) or stay as non-auth records managed entirely by the business owner?
2. Clock-in geofence radius — fixed (e.g. 200m) or configurable per business?
3. Location tracking — opt-in consent per technician, or mandatory during clocked-in hours?
4. Route optimisation — in-house heuristic (v1) vs Google Routes Optimization API vs OR-Tools microservice (Phase 2)?
5. Should customers see the assigned technician's name and live ETA on their request page?
6. Should material usage logging deduct from inventory automatically or require manager approval first?
7. Equipment lost reports — trigger automatic compensation deduction from technician payroll?
8. Should vehicles support multiple simultaneous drivers (shared fleet) or strict one-at-a-time assignment?

---

## Document Control

| Version | Date | Notes |
| --- | --- | --- |
| 1.0 | 2026-05-29 | Initial spec. Desktop-first. Extends concept.md §8. |
| 1.1 | 2026-05-29 | Added: team_member_services (skill/service mapping), Equipment & Tool Tracking, Inventory Management, Vehicle Management. Updated permissions JSON, routes, dashboard views, indexes, and open questions. |
