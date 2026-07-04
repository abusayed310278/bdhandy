# Service Marketplace SaaS — Master Concept Document

> **Project Codename:** ServiceHub BD
> **Version:** 2.1 (DB-Aligned Master Spec)
> **Tech Lead:** Unlock — UnlockWare LLC
> **Target Markets:** Bangladesh (Primary), GCC/UAE (Secondary), Global (Future)
> **Document Type:** Authoritative concept and architecture reference
> **Source of truth ordering:** DATABASE → CONCEPT → DESIGN

---

## 1. Executive Summary

A multilingual, multi-region service marketplace SaaS platform connecting customers with verified local service providers (electricians, plumbers, AC technicians, cleaners, etc.). Initially focused on Bangladesh with full Bengali support, expanded to UAE/Middle East with Arabic + RTL support, and architecturally ready for global rollout.

### Core Differentiators

1. **Subscription-based revenue** — no per-job commission, predictable provider economics.
2. **Dual marketplace flow** — customers find providers (search) AND providers find customers (requirement posts/leads).
3. **True multilingual + bidirectional** — EN / BN / AR with full RTL support, dynamic locale detection.
4. **Radius-based geo-matching** — providers define service zones; system uses Haversine + spatial indexing.
5. **PWA-first** — installable, offline-capable, push-notification ready.
6. **Trust layer** — NID, trade license, mobile, email verification with admin moderation.
7. **Built-in growth engine** — affiliate / referral system + coupon engine from day one.

---

## 2. Strategic Goals

| Goal | Metric (Year 1) |
| --- | --- |
| Onboard verified providers | 5,000+ |
| Active monthly customers | 50,000+ |
| Cities covered (BD) | 20+ |
| Cities covered (UAE) | 5+ (Phase 2) |
| Subscription conversion rate | ≥ 12% of free providers |
| Customer repeat-booking rate | ≥ 35% |
| Average match time (request → provider response) | < 8 minutes |
| Affiliate-driven sign-ups | ≥ 15% of new providers (by Q3) |

---

## 3. User Personas

### 3.1 Customer
Homeowner, tenant, small business owner (22–55). Mobile-dominant. Pain points: trust, opaque pricing, no-shows.

### 3.2 Freelancer Provider
Solo technician, single skill. 95% mobile, low digital literacy. Needs simple onboarding, leads, free trial.

### 3.3 Business Provider
Registered company with staff, multiple categories. Desktop + mobile. Needs multi-area coverage, analytics, branding.

### 3.4 Affiliate
Provider, customer, or external partner earning commission for referring providers. Each opt-in user gets a `referral_code`. Tracks earnings, payouts, and minimum-payout thresholds.

### 3.5 Admin / Support / Moderator
UnlockWare staff. Verification queue, dispute handling, support tickets, content moderation.

---

## 4. Business Model — Subscription-Driven SaaS

### 4.1 Revenue Streams

| Stream | Priority | Notes |
| --- | --- | --- |
| Provider subscriptions | Primary | Monthly → 36-month plans |
| Featured placement / boosts | Secondary | Add-on |
| Promoted requirement leads | Secondary | Per-lead unlock for free-tier |
| Verified badge fee | Secondary | Included in higher plans |
| Coupon-driven promotions | Acquisition tool | Drives conversion, not revenue |
| Affiliate commissions paid out | Acquisition cost | Cost line, not revenue |
| Banner advertising | Tertiary (future) | Brand sponsorships |

### 4.2 Subscription Plan Structure (`subscription_plans`)

Each plan stores:
`duration_months · price · currency_id · discount_percent · lead_limit · service_area_limit · gallery_limit · search_rank_weight · is_featured · is_verified_badge_included`

| Plan | Duration | Discount | Audience |
| --- | --- | --- | --- |
| Free | Forever | — | Casual freelancers |
| Trial | 3 months | 100% (promo) | Onboarding incentive |
| Starter | 1 month | 0% | Solo freelancers |
| Quarterly | 3 months | 5% | Mid-tier |
| Half-Yearly | 6 months | 10% | Committed providers |
| Yearly | 12 months | 20% | Most popular |
| Bi-Yearly | 24 months | 30% | Loyal businesses |
| Tri-Yearly | 36 months | 40% | Long-term partners |

### 4.3 Plan-Gated Features (driven by DB columns)

| DB Column | Free | Starter | Yearly | Bi/Tri-Yearly |
| --- | --- | --- | --- | --- |
| `lead_limit` | 5 / mo | 50 / mo | unlimited | unlimited |
| `service_area_limit` | 1 | 3 | 5 | unlimited |
| `gallery_limit` | 5 | 20 | 100 | unlimited |
| `search_rank_weight` | 1.0× | 1.2× | 1.6× | 2.0× |
| `is_featured` | false | false | true | true |
| `is_verified_badge_included` | false | false | true | true |

### 4.4 Coupons (`coupons`)

- `code` · `discount_type` (percentage / fixed) · `discount_value` · `usage_limit` · `used_count` · `start_date` · `end_date` · `status`.
- Applied at subscription checkout.
- Admin-creatable; can be public (homepage banner) or private (single-use, hand-shared).
- Stacking: one coupon per invoice (config in `settings`).

### 4.5 Affiliate Program

Two tables: `affiliate_system` (config per user) + `referrals` (event log).

**Affiliate config**
- `referral_code` (unique 6–8 char, shareable URL `/r/{code}`)
- `commission_type` (percentage / fixed) + `commission_value`
- `minimum_payout` (e.g. ৳500)
- `total_earnings`, `total_paid`
- `status` (active / inactive / suspended)

**Referral event**
- `affiliate_id` (who earned)
- `referred_user_id`
- `subscription_id` (triggers commission on first paid subscription)
- `commission_amount`
- `commission_status` (pending / approved / paid / rejected)
- `paid_at`

**Workflow**
1. Affiliate shares `/r/{code}`.
2. Cookie stored 30 days (`settings.affiliate.cookie_days`).
3. Referred user signs up → `referrals` row created with `status = pending`.
4. On first paid subscription → status becomes `approved`, commission computed.
5. Admin approves payout when balance ≥ `minimum_payout`.
6. Payout marked `paid` with `paid_at`.

---

## 5. Geographic & Multi-Region Architecture

### 5.1 Region Hierarchy

```
countries → divisions → districts → areas (with lat/lng)
```

Customer addresses (`customer_addresses`) and provider coverage (`provider_service_areas`) both reference this hierarchy plus their own lat/lng.

### 5.2 Initial Coverage

- **Bangladesh — Phase 1**: Dhaka, Gazipur, Narayanganj, Chittagong, Sylhet, Khulna, Rajshahi, Barisal, Rangpur, Mymensingh.
- **UAE — Phase 2**: Abu Dhabi, Dubai, Sharjah, Ajman, Ras Al Khaimah.
- **Future**: Uzbekistan, Saudi Arabia, Qatar.

### 5.3 Currency Table (`currency`)

| symbol | name | status |
| --- | --- | --- |
| ৳ | BDT | active |
| AED / د.إ | AED | active |
| $ | USD | active |
| UZS | UZS | inactive (until Phase 3) |

Every monetary column (`subscription_plans.price`, `provider_services.price_*`, `service_requests.estimated_price`, `requirement_proposals.proposed_price`, `coupons.discount_value` when fixed, etc.) carries `currency_id`. No automatic conversion — providers operate in their region's currency.

### 5.4 Country Locale Mapping (`countries`)

`locale` + `direction` columns drive UI behaviour:

| Country | locale | direction |
| --- | --- | --- |
| Bangladesh | bn | ltr |
| UAE | ar | rtl |
| Uzbekistan | uz | ltr |
| Global default | en | ltr |

---

## 6. Multilingual & Bidirectional (RTL/LTR) Architecture

### 6.1 Supported Languages

| Code | Language | Direction | Region |
| --- | --- | --- | --- |
| `en` | English | LTR | Global |
| `bn` | Bengali / বাংলা | LTR | Bangladesh |
| `ar` | Arabic / العربية | **RTL** | UAE, Saudi |
| `uz` | Uzbek (Latin) | LTR | Uzbekistan (future) |

### 6.2 User Preference

`users.preferred_language` stores per-user choice. Resolution order:

1. URL prefix `/{locale}/…`
2. `users.preferred_language`
3. Cookie `app_locale`
4. `Accept-Language` header
5. Country default (`countries.locale`)
6. Fallback → `en`

### 6.3 Translation Storage

**Files** (`/resources/lang/{locale}/*.php`) for UI strings.

**JSON column** `translations` on `categories` and `services` for catalog content:

```json
{
  "en": { "name": "Electrician", "description": "Wiring, repairs..." },
  "bn": { "name": "ইলেকট্রিশিয়ান", "description": "..." },
  "ar": { "name": "كهربائي", "description": "..." }
}
```

Provider-supplied free text (bio, service description, requirement description) is stored once in whatever language the provider used. UI shows "Translate" button (Phase 3, AI).

### 6.4 RTL Strategy

- Tailwind logical utilities (`ms-`, `me-`, `ps-`, `pe-`, `text-start`, `text-end`, `border-s`, `rounded-s-*`).
- `<html dir="rtl">` set server-side from `users.preferred_language` or `countries.direction`.
- `.rtl-flip` helper to invert direction-sensitive icons.
- Per-locale font swap.

### 6.5 Font Stack

| Language | Font | Fallback |
| --- | --- | --- |
| English | Inter | system-ui |
| Bengali | Hind Siliguri / Noto Sans Bengali | sans-serif |
| Arabic | Cairo / Tajawal | Noto Naskh Arabic |

---

## 7. Authentication & Identity

### 7.1 Registration Channels (`users`)

- Email + password
- Mobile + OTP (`otp_codes`)
- Social: Google, Facebook, Apple (`provider`, `provider_user_id`, `access_token`, `refresh_token`)

### 7.2 User Code

Each user gets a 6-digit unique `user_code` — short, shareable, easy to recite over phone. Used in support tickets and admin lookup.

### 7.3 OTP (`otp_codes`)

- Types: `login`, `register`, `forgot_password`.
- 6-digit numeric, 5-minute expiry.
- Rate-limit: 3 sends / 15 min per phone.
- One active OTP per type per phone.

### 7.4 Verification Layers

| Layer | Storage | For |
| --- | --- | --- |
| Email | `users.email_verified_at` | All users |
| Phone | `users.phone_verified_at` | All users |
| NID / Passport / Emirates ID | `provider_documents` (`document_type_id` lookup) | Providers |
| Trade License / VAT | `provider_documents` | Business providers |

### 7.5 Document Type Lookup (`document_type`)

| type | provider_type |
| --- | --- |
| nid | freelancer / business |
| passport | freelancer / business |
| emirates_id | freelancer / business |
| trade_license | business |
| vat_certificate | business (optional) |

Admin-editable per region without code changes.

### 7.6 Account Roles

Powered by Spatie Permission package:

```
super_admin · admin · moderator · support
provider (freelancer / business)
customer
affiliate (overlay role)
```

---

## 8. Provider System

### 8.1 Onboarding Flow

```
1. Choose role: Customer | Freelancer | Business
2. Complete profile (provider_profiles)
3. Add services (provider_services)
4. Set primary service area (provider_service_areas + map)
5. Upload identity (provider_documents)
6. submit the form then verification email sent. after verify successfully
7. Wait for admin verification
8. Activate on Free plan automatically
```

### 8.2 Provider Profile (`provider_profiles`)

- `provider_type` (freelancer | business)
- `business_name`, `slug`, `logo`, `cover_photo`
- `description`, `years_of_experience`, `experience_level` (beginner / intermediate / expert)
- `languages`
- `emergency_available`
- `is_verified`, `verification_status`, `is_featured`
- `primary_phone`, `whatsapp_number`, `website`, social URLs
- `status` (active / inactive / suspended)
- Soft-deletable

### 8.3 Multi-Area Coverage (`provider_service_areas`)

A provider may define multiple coverage zones, each with country/division/district/area FKs + lat/lng + `radius_km`. Plan's `service_area_limit` enforces max count.

### 8.4 Business Hours (`provider_business_hours` + `day_of_week`)

- 7 rows per provider (Sun–Sat) keyed by `day_of_week_id`.
- `start_time`, `end_time`, `is_closed`.
- One continuous window per day in v1 (no split shifts).

### 8.5 Holidays (`holidays`)

- Provider-specific date closures (festival, vacation).
- Fields: `provider_profile_id`, `date_of_holiday`, `reason`.
- Customer UI shows "Closed: {reason}" on profile and blocks booking that date.
- Admin can push **global** platform holidays (`provider_profile_id` null).

### 8.6 Gallery (`provider_gallery`)

- `url`, `caption`, `sort_order`, `is_video` (bool).
- Stored on S3/Bunny. `gallery_limit` enforced from plan.

---

## 9. Customer System

### 9.1 Customer Addresses (`customer_addresses`)

A customer can save multiple addresses, each typed (`house` / `office` / `business`) with country/division/district/area FKs + lat/lng. One is selected when creating a request; one marked "primary".

### 9.2 Saved Providers (`saved_providers`)

Bookmarked providers, shown on dashboard "Saved" tab.

### 9.3 Saved Searches (`saved_searches`)

`keyword` + `filters` JSON. Future: nightly job triggers notification when new matches appear.

---

## 10. Pricing System (`services.pricing_type`, `provider_services`)

### 10.1 Models — DB-aligned

| `pricing_type` | Usage | Columns used |
| --- | --- | --- |
| `fixed` | Standard tasks | `price_fixed` |
| `range` | Variable scope | `price_min`, `price_max` |
| `hourly` | Long jobs | `price_fixed` (per hour) + `duration_minutes` |
| `quote` | Custom jobs | No price displayed; provider quotes after request |

`currency_id` on every priced row.

### 10.2 Display Rules

- Server stores raw amounts in DB units.
- Display layer formats per locale: `৳ 1,500`, `AED 150.00`, `د.إ 150.00`.

---

## 11. Search & Discovery

### 11.1 Search Inputs

- Free text (service name, business name)
- Category
- Customer location (auto / manual / saved address)
- Filters: rating, price, verified, freelancer/business, language, emergency-available, currently open (hours-aware)
- Sort: distance, rating, response time, price, plan-weighted

### 11.2 Geo Matching — Haversine

```
distance_km = 2 * R * asin(sqrt(
  sin²((lat2−lat1)/2) +
  cos(lat1) * cos(lat2) * sin²((lon2−lon1)/2)
))
R = 6371 km
```

Provider visible IF any of their `provider_service_areas` rows has `distance(customer, area) ≤ radius_km`.

Optimisations:
- MySQL spatial index on generated `POINT` column.
- Bounding-box pre-filter (`MBRContains`) before Haversine.
- Optional Redis geo-set for hot zones.

### 11.3 Ranking Score

```
score = (1 / (distance_km + 1))            × W_distance    (0.40)
      + (rating / 5.0)                     × W_rating      (0.25)
      + (plan.search_rank_weight)          × W_plan        (0.15)
      + (is_verified ? 1 : 0)              × W_verified    (0.10)
      + response_rate                      × W_response    (0.07)
      + (emergency_match ? 1 : 0)          × W_emergency   (0.03)
```

Weights stored in `settings` table so admins can re-tune without deploy.

### 11.4 Search Logs (`search_logs`)

Captures every search: `keyword`, `category_id`, `latitude`, `longitude`, `results_count`, optional `user_id`. Fuels analytics ("top searched services") and zero-result gap analysis.

---

## 12. Service Request System

### 12.1 Lifecycle (`service_requests.request_status`)

```
pending → accepted → in_progress → completed
                                ↘ disputed → (admin) resolved
       ↘ cancelled
       ↘ expired (auto job)
```

Each transition writes a row to `request_status_logs` (`old_status`, `new_status`, `changed_by`, `notes`).

### 12.2 Fields

- `request_number` (human-readable, e.g. `REQ-2026-000128`)
- `title`, `description`
- `preferred_date`, `preferred_time`
- `address`, `latitude`, `longitude` (snapshot — not FK)
- `urgency` (normal / urgent / emergency)
- `estimated_price`, `final_price`, `currency_id`
- `payment_status` (pending / paid / failed / refunded)
- `cancellation_reason`
- `completed_at`
- Soft-deletable

### 12.3 Attachments (`request_attachments`)

`file`, `file_type` (image / video / pdf). Max 5 per request (config in `settings`).

---

## 13. Customer Requirement Posts (`customer_requirements`)

### 13.1 Concept

Customer publishes a public request. Nearby providers (whose service area covers the customer's `lat/lng`) see it and submit proposals.

### 13.2 Fields

- `title`, `description`, `category_id`, `service_id`
- `budget_type` (fixed / range / negotiable)
- `budget_fixed`, `budget_min`, `budget_max`, `currency_id`
- `urgency`
- `preferred_date`
- `address`, `latitude`, `longitude`
- `expiry_at` (default +24h, max +7 days)
- `visibility_radius_km` — customer override on "nearby"
- `status` (open / assigned / completed / expired / cancelled)
- Soft-deletable

### 13.3 Proposals (`requirement_proposals`)

- `message`, `proposed_price`, `currency_id`, `estimated_arrival_time`
- `status` (pending / accepted / rejected / withdrawn)

### 13.4 Lead Access by Plan

Plan's `lead_limit` enforces monthly proposal cap:
- Free → 5 / mo
- Starter → 50 / mo
- Yearly+ → unlimited

### 13.5 Anti-Spam

- Customer post rate limit: 3/day, 10/week (`settings`)
- Provider proposal rate limit per hour
- Auto-flag duplicate text via hash match

---

## 14. Reviews (`reviews` + `review_replies`)

- Tied to `service_request_id` — only completed requests reviewable.
- `rating` (1–5), `review` (text), `is_approved` (admin moderation).
- Provider may reply once (`review_replies`).
- Review window: 30 days post-completion (config).
- Average rating recomputed on approve/edit/delete.

---

## 15. Conversations & Messages

### 15.1 Conversations (`conversations`)

- `type` — `request_chat` (tied to `related_request_id`) or `support_chat`.

### 15.2 Messages (`messages`)

- `message_type` — text / image / voice / file.
- `is_read`, `read_at` per message.

### 15.3 Realtime Stack

- Laravel Reverb (WebSocket) on a dedicated subdomain.
- Channel `private-conversation.{conversation_id}`.
- Presence in Redis.

---

## 16. Payments

### 16.1 Subscription Payments

`subscription_invoices`:
`invoice_number`, `subtotal`, `discount`, `total`, `currency_id`, `payment_method`, `payment_status`, `paid_at`.

`payment_transactions`:
`gateway`, `transaction_id`, `amount`, `currency_id`, `status`, `gateway_response`.

### 16.2 Gateways

| Gateway | Region |
| --- | --- |
| bKash | Bangladesh |
| SSLCommerz | Bangladesh (cards) |
| Stripe | UAE / global |
| Telr / Tap | UAE (Phase 2) |
| Nagad / Rocket | Bangladesh (Phase 1.5+) |

### 16.3 Subscription Workflow

```
1. Provider selects plan + optional coupon
2. App creates pending subscription + invoice
3. Redirect to gateway / SDK
4. Webhook on success → mark invoice paid → activate subscription
5. Affiliate hook: if user has open `referrals` row → mark approved + compute commission
6. Coupon hook: increment `coupons.used_count`
7. Renewal reminders: 7d, 1d, day-of
8. Grace period: 3 days post-`end_date` (`subscription_status = 'grace'`)
9. Auto-downgrade job moves expired subscriptions to free plan
```

---

## 17. Notifications

### 17.1 Tables

- `notifications` — `user_id`, `type`, `title`, `body`, `data` (JSON), `is_read`
- `notification_preferences` — `email_enabled`, `sms_enabled`, `push_enabled`, `whatsapp_enabled`, `marketing_enabled`

### 17.2 Channels

| Channel | Use | Tech |
| --- | --- | --- |
| Email | Transactional + marketing | Laravel Mail + SES/Mailgun |
| SMS | OTP, urgent | SSL Wireless / Twilio |
| Push (web) | PWA users | Web Push + VAPID |
| In-app | All events | DB-backed list |
| WhatsApp | Optional updates | WhatsApp Business / n8n |

### 17.3 Notification Types (`notifications.type`)

- `request` — new request, status change
- `payment` — invoice, renewal
- `system` — verification result, account
- `promotion` — coupons, banners
- `chat` — new message
- `affiliate` — referral approved, payout sent

---

## 18. Support System

### 18.1 Tickets (`support_tickets`)

- `ticket_number` (e.g. `TKT-2026-000045`)
- `user_id`, `subject`, `description`
- `priority` (low / medium / high / urgent)
- `department` (technical / billing / verification / general)
- `assigned_to`, `status` (open / pending / replied / resolved / closed)
- `last_reply_at`

### 18.2 Ticket Messages (`support_ticket_messages`)

Threaded back-and-forth. `sender_id` + `message` + optional `attachment`.

### 18.3 Workflow

```
User opens ticket → auto-assigned by department → support replies
→ user replies / closes → SLA timer based on priority
Urgent: 4h · High: 12h · Medium: 24h · Low: 48h
```

---

## 19. CMS & Marketing Content

### 19.1 Banners (`banners`)

- `title`, `image`, `url`, `position` (homepage_top / sidebar / footer)
- `start_date`, `end_date`, `status`
- Admin schedules campaigns; multiple positions supported.

### 19.2 FAQs (`faqs`)

- `question`, `answer` (multilingual via JSON column or per-locale rows — pick one).
- `sort_order`, `status`.
- Rendered on /help and as structured data on category landing pages.

### 19.3 Contact Messages (`contact_messages`)

Form submissions from /contact. Status flow: new → pending → replied → closed / spam.

---

## 20. Settings (`settings`)

Key/value store typed by `string | json | integer | boolean`. Admin-editable. Examples:

- `search.weight.distance` = 0.40
- `search.weight.rating` = 0.25
- `affiliate.cookie_days` = 30
- `affiliate.min_payout` = 500
- `requirement.expiry_default_hours` = 24
- `request.max_attachments` = 5
- `subscription.grace_days` = 3
- `otp.expiry_minutes` = 5
- `gateway.bkash.app_key` (encrypted)

---

## 21. Activity Logs (`activity_logs`)

Every meaningful action recorded:
`user_id`, `action`, `model_type`, `model_id`, `ip_address`, `user_agent`.

Powered by Spatie ActivityLog. Used in admin audit trails and forensic investigation.

---

## 22. Technology Stack

### 22.1 Backend
- Laravel 12, PHP 8.3+
- MySQL 8 (with spatial indexes)
- Redis (cache, queues, presence)
- Laravel Sanctum, Reverb, Horizon, Telescope
- Spatie: Permission, MediaLibrary, ActivityLog, Backup

### 22.2 Frontend
- Blade + Tailwind CSS (CDN in dev, `rtlcss` compiled in prod)
- Alpine.js (interactive bits)
- Livewire (admin dashboard reactivity)
- Vue.js for chat & complex SPA pieces (Phase 2)

### 22.3 Maps
- Google Maps JS API + Places + Geocoding
- OpenStreetMap + Leaflet fallback

### 22.4 DevOps
- Nginx + PHP-FPM, Supervisor for queues + Reverb
- GitHub Actions CI/CD
- Cloudflare CDN
- VPS (Hetzner / DigitalOcean) initially, scaling to LB cluster

---

## 23. Database Architecture — DB-aligned summary

### 23.1 Principles

- Integer / bigint primary keys (matches user schema).
- Human-readable public IDs as separate columns: `user_code`, `request_number`, `ticket_number`, `invoice_number`.
- Soft deletes on: `provider_profiles`, `service_requests`, `customer_requirements`.
- Timestamps on every table.
- Spatial indexes on lat/lng.
- Lookup tables: `currency`, `day_of_week`, `document_type` — admin-editable.
- `translations` JSON column on `categories` and `services`.

### 23.2 Table Map (Authoritative)

```
Identity & Auth
  users · otp_codes · roles & permissions (Spatie)

Customer
  customer_addresses · saved_providers · saved_searches

Provider
  provider_profiles · provider_documents · document_type
  provider_service_areas
  provider_services · provider_business_hours · day_of_week · holidays
  provider_gallery

Geography
  countries · divisions · districts · areas

Catalog
  categories · services
  currency

Requests & Requirements
  service_requests · request_attachments · request_status_logs
  customer_requirements · requirement_attachments · requirement_proposals

Communication
  conversations · messages

Reviews
  reviews · review_replies

Monetisation
  subscription_plans · subscriptions
  subscription_invoices · payment_transactions
  coupons
  affiliate_system · referrals

Notifications
  notifications · notification_preferences

Support
  support_tickets · support_ticket_messages

Content / CMS
  banners · faqs · contact_messages

System
  settings · activity_logs · search_logs
```

### 23.3 Important Indexes

```sql
-- Spatial
ALTER TABLE provider_service_areas ADD SPATIAL INDEX idx_psa_geom (geom);
ALTER TABLE customer_requirements  ADD SPATIAL INDEX idx_req_geom (geom);

-- Filters
CREATE INDEX idx_pp_status_verified ON provider_profiles (status, is_verified);
CREATE INDEX idx_ps_provider_service ON provider_services (provider_profile_id, service_id);
CREATE INDEX idx_subs_status_end ON subscriptions (subscription_status, end_date);

-- Lookups
CREATE UNIQUE INDEX idx_users_email ON users (email);
CREATE UNIQUE INDEX idx_users_phone ON users (phone);
CREATE UNIQUE INDEX idx_users_user_code ON users (user_code);
CREATE UNIQUE INDEX idx_aff_referral ON affiliate_system (referral_code);
CREATE UNIQUE INDEX idx_coupons_code ON coupons (code);

-- Hot reads
CREATE INDEX idx_notif_user_read ON notifications (user_id, is_read);
CREATE INDEX idx_msg_conversation_created ON messages (conversation_id, created_at);
```

---

## 24. API Architecture

### 24.1 Surface

- **Public** — search, listings, categories, regions (rate-limited).
- **Customer** — profile, addresses, requests, requirements, chat.
- **Provider** — profile, services, leads, subscription, analytics, hours/holidays.
- **Affiliate** — referral stats, payout history.
- **Admin** — moderation, reports, system management.
- **Webhooks** — payment gateways, SMS DLR.

### 24.2 Versioning

`/api/v1/...` — major bumps for breaking changes, 6-month deprecation window.

### 24.3 Response Envelope

```json
{
  "ok": true,
  "data": { },
  "meta": { "page": 1, "per_page": 20, "total": 124 },
  "errors": null,
  "locale": "bn",
  "request_id": "uuid"
}
```

---

## 25. Security & Compliance

- CSRF on all forms
- Rate limiting per IP + per user
- Brute-force lock (5 fails / 15 min)
- OTP for sensitive flows
- File validation (MIME, size, dimension)
- Signed URLs for private assets
- HTTPS + HSTS + CSP
- Activity logging on admin actions
- GDPR data export & deletion endpoints
- NID / Emirates ID encrypted at rest
- Documents stored in private bucket
- PCI scope minimisation — no card data on platform

---

## 26. SEO & Marketing

### 26.1 Dynamic Landing Pages

```
/{locale}/{category-slug}-in-{area-slug}
/en/electrician-in-dhaka
/bn/বিদ্যুৎ-মিস্ত্রি-ঢাকা
/ar/سباك-في-أبوظبي
```

Each page renders H1, top 10 verified providers, avg price range, reviews snippet, FAQs (schema.org), local business schema, OG + Twitter cards.

### 26.2 Sitemap

- Static for marketing pages.
- Dynamic for provider profiles and category × area combos.
- Sitemap index regenerated nightly.

### 26.3 Performance Targets

- LCP < 2.0s on 3G
- CLS < 0.1
- TTI < 3.5s on mid-range Android
- Lighthouse PWA score ≥ 90

---

## 27. PWA Strategy

- Manifest with EN/BN/AR variants
- Workbox service worker
- Offline shell + cached static assets
- Push notifications via VAPID
- Install prompts after 2nd visit
- App shortcuts: "Find Provider", "My Requests", "Post Requirement"

---

## 28. Admin Dashboard Capabilities

- KPI cards (Providers, Customers, MRR, ARPU, Churn, MAU)
- Verification queue (Kanban)
- Subscription analytics
- Revenue charts (daily / monthly / yearly)
- Top searched services (from `search_logs`)
- Provider leaderboard
- Support ticket inbox
- Affiliate payout queue
- Coupon creator & analytics
- Banner & landing page CMS
- Multilingual category/service editor
- Settings (search weights, plan features, regions, gateway keys)
- Activity log explorer
- Backup & restore (Spatie Backup)

---

## 29. Development Roadmap

| Phase | Month | Theme |
| --- | --- | --- |
| 1 | M1 | Foundation: auth, RBAC, multilingual scaffold, region & currency seeders, lookup tables |
| 2 | M2 | Catalog & profiles: categories, services, provider onboarding, gallery, business hours, holidays, multi-area zones, customer addresses |
| 3 | M3 | Discovery: search engine, Haversine matching, filters, sort, ranking score, SEO landing pages |
| 4 | M4 | Requests & requirements: service request lifecycle, requirement marketplace, proposals, notifications |
| 5 | M5 | Chat & reviews: Reverb WebSockets, chat UI, presence, reviews + replies |
| 6 | M6 | Monetisation: plans, subscriptions, invoices, bKash + Stripe, coupons, affiliate engine |
| 7 | M7 | Trust & verification: doc queue, badges, support tickets, activity logs |
| 8 | M8 | PWA & mobile polish, performance, RTL polish |
| 9 | M9 | Admin power tools: analytics, CMS, bulk ops, backup |
| 10 | M10 | Launch & optimise: beta in 2 cities → soft launch → public launch |
| 11+ | Year 2 | AI features, UAE rollout, native apps |

---

## 30. MVP Cut Line

### Must include

- Auth (email + phone OTP) with EN/BN
- Provider onboarding + verification
- Categories, services, profiles, gallery
- Map-based service area + radius
- Multiple customer addresses
- Search by category + nearby
- Service request flow
- Requirement posting
- Reviews (1–5 stars + text)
- Free + Monthly + Yearly subscription
- Coupon engine
- bKash subscription payment
- Admin verification queue
- Support tickets
- PWA install + offline shell

### Defer to v1.1+

- Live chat (use phone first)
- Stripe + multi-gateway
- Arabic UI (UAE Phase 2)
- Affiliate payouts (engine in place, payouts manual at first)
- Featured placement
- Native apps
- AI features

---

## 31. KPIs & Telemetry

- DAU / WAU / MAU split by role
- Provider activation funnel (sign-up → verified → first lead → first job)
- Customer search → contact → completion funnel
- Average match time
- Subscription conversion & churn
- Revenue per region & per plan
- Coupon redemption rate
- Affiliate-driven sign-up share
- Average rating per category
- Response time per provider
- 99th percentile API latency
- Error rate per route
- Push opt-in rate
- PWA install rate

Stack: Laravel logs + Sentry + self-hosted analytics (Plausible/Umami) + admin dashboard widgets.

---

## 32. Open Questions

1. Escrow model — Phase 2 or stay offline-only?
2. Native app stack — Flutter vs React Native?
3. Search engine — pure MySQL spatial vs Elasticsearch / Meilisearch?
4. Currency display for tourists/expats?
5. Should reviews require verified phone or just account?
6. Provider verification SLA — 24h, 48h, 72h?
7. Affiliate commission tier model — flat or tiered by plan?
8. Coupon stacking allowed?

---

## 33. Glossary

| Term | Meaning |
| --- | --- |
| **Provider** | Freelancer or business offering services |
| **Service Area** | Geographic zone (lat/lng + radius) where a provider operates |
| **Lead** | A customer requirement post visible to nearby providers |
| **Proposal** | A provider's bid on a customer requirement |
| **Verification Status** | One of: pending / in_review / approved / rejected |
| **Plan Weight** | Multiplier applied in search ranking based on subscription tier |
| **Trust Signal** | Visible indicator of provider reliability |
| **Logical Property** | CSS property that adapts to writing direction |
| **Region** | Country-level container for divisions, districts, areas |
| **PWA** | Progressive Web App — installable, offline-capable |
| **Affiliate** | User earning commission for referring new paying providers |
| **Coupon** | Discount code applied at subscription checkout |
| **User Code** | 6-digit unique identifier on every user (`users.user_code`) |

---

## 34. Document Control

| Version | Date | Author | Notes |
| --- | --- | --- | --- |
| 1.0 | Initial | UnlockWare | Draft concept |
| 2.0 | Earlier | UnlockWare | Advanced master spec — Arabic/RTL, multi-region, scoring formula |
| **2.1** | **This release** | **UnlockWare** | **DB-aligned. Added affiliate system, coupons, support tickets, customer addresses, holidays, currency table, document_type lookup, day_of_week lookup. Priority ordering established: DB → Concept → Design.** |

> Source-of-truth ordering: **DATABASE → CONCEPT → DESIGN**.
> Any deviation in implementation must be reflected here through a versioned update.
