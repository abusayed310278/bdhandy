# Service Marketplace SaaS — Master Design System

> **Project Codename:** ServiceHub BD
> **Version:** 1.1 (DB-Aligned)
> **Document Type:** Authoritative design reference for **Public Frontend** and **Dashboard**
> **Stack:** Tailwind CSS (CDN, dev) · Alpine.js · Blade · RTL/LTR ready
> **Languages:** English (LTR) · Bengali (LTR) · Arabic (RTL)
> **Source of truth ordering:** DATABASE → CONCEPT → DESIGN

---

## 1. Design Principles

1. **Calm, trustworthy, mobile-first.** Service marketplaces win on trust signals. Restraint > flash.
2. **Light, two-accent palette.** Light blue = trust, info, primary actions. Light orange = warmth, energy, secondary highlights.
3. **High contrast text on soft backgrounds.** Slate for text, white/sky for surfaces.
4. **Direction-agnostic.** Every component must work LTR and RTL with zero markup change — only logical properties.
5. **One radius scale, one shadow scale.** Predictable, calm.
6. **Touch-first sizing.** 44px minimum tap targets; 48px on mobile dashboard.
7. **Progressive disclosure.** Don't overwhelm — reveal complexity as the user goes deeper.
8. **Responsive-first.** Every component must work from 360px (small mobile) → 1440px (wide desktop) without horizontal scroll.

---

## 2. Brand Color Palette

Two accent families on a neutral slate foundation. Resist adding more.

### 2.1 Primary — Light Blue (Trust / Action)

| Token | Hex | Tailwind | Usage |
| --- | --- | --- | --- |
| `primary-50` | `#F0F8FF` | `sky-50` | Surface tint, hover bg |
| `primary-100` | `#E0F1FE` | `sky-100` | Selected chips, soft cards |
| `primary-200` | `#BAE0FD` | `sky-200` | Decorative borders |
| `primary-300` | `#7CC8FB` | `sky-300` | Icon strokes |
| `primary-400` | `#38ADF7` | `sky-400` | Hover state |
| `primary-500` | `#0F94EA` | `sky-500` | **Primary button, links** |
| `primary-600` | `#0277C7` | `sky-600` | Pressed, headings on light |
| `primary-700` | `#0561A1` | `sky-700` | Strong text on light |

### 2.2 Secondary — Light Orange (Warmth / Highlight)

| Token | Hex | Tailwind | Usage |
| --- | --- | --- | --- |
| `accent-50` | `#FFF7ED` | `orange-50` | Subtle highlight bg |
| `accent-100` | `#FFEDD5` | `orange-100` | Badge soft fill |
| `accent-200` | `#FED7AA` | `orange-200` | Decorative |
| `accent-300` | `#FDBA74` | `orange-300` | Illustration |
| `accent-400` | `#FB923C` | `orange-400` | Hover |
| `accent-500` | `#F97316` | `orange-500` | **Secondary CTA, hot tags** |
| `accent-600` | `#EA580C` | `orange-600` | Pressed |
| `accent-700` | `#C2410C` | `orange-700` | Strong accent text |

### 2.3 Neutrals

| Token | Hex | Usage |
| --- | --- | --- |
| `bg` | `#FFFFFF` | Page background |
| `bg-soft` | `#F8FAFC` | Section background |
| `bg-muted` | `#F1F5F9` | Card alternate |
| `border` | `#E2E8F0` | Default border |
| `border-strong` | `#CBD5E1` | Inputs, dividers |
| `text-muted` | `#64748B` | Secondary text |
| `text` | `#334155` | Body text |
| `text-strong` | `#0F172A` | Headings |

### 2.4 Status (Sparingly)

| Status | Hex | Tailwind |
| --- | --- | --- |
| Success | `#16A34A` | `green-600` |
| Warning | `#CA8A04` | `yellow-600` |
| Danger | `#DC2626` | `red-600` |
| Info | `#0F94EA` | `sky-500` |

---

## 3. Typography

### 3.1 Font Stack

```css
--font-sans: 'Inter', system-ui, -apple-system, sans-serif;
--font-bn:   'Hind Siliguri', 'Noto Sans Bengali', sans-serif;
--font-ar:   'Cairo', 'Tajawal', 'Noto Naskh Arabic', sans-serif;
```

```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
```

```css
html[lang="en"] body { font-family: 'Inter', system-ui, sans-serif; }
html[lang="bn"] body { font-family: 'Hind Siliguri', system-ui, sans-serif; line-height: 1.75; }
html[lang="ar"] body { font-family: 'Cairo', system-ui, sans-serif; }
```

### 3.2 Type Scale (Responsive)

| Role | Mobile | Desktop | Weight | Tailwind |
| --- | --- | --- | --- | --- |
| Display | 28px | 44px | 700 | `text-3xl md:text-5xl font-bold` |
| H1 | 24px | 32px | 700 | `text-2xl md:text-3xl font-bold` |
| H2 | 20px | 24px | 600 | `text-xl md:text-2xl font-semibold` |
| H3 | 18px | 20px | 600 | `text-lg md:text-xl font-semibold` |
| Body L | 16px | 17px | 400 | `text-base md:text-[17px]` |
| Body | 15px | 15px | 400 | `text-[15px]` |
| Small | 13px | 13px | 400 | `text-[13px]` |
| Tiny / label | 12px | 12px | 500 | `text-xs font-medium uppercase tracking-wide` |

### 3.3 Line Height

- Headings: `leading-tight` (1.2)
- Body: `leading-relaxed` (1.65)
- Bengali body: bump to `leading-[1.75]`

---

## 4. Spacing, Radius, Shadow, Motion

### 4.1 Spacing

Stick to: `0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16, 20, 24`.

### 4.2 Radius

| Token | Value | Usage |
| --- | --- | --- |
| `rounded-md` | 6px | Inputs, small chips |
| `rounded-lg` | 8px | Buttons |
| `rounded-xl` | 12px | Cards |
| `rounded-2xl` | 16px | Hero panels, modals |
| `rounded-full` | 9999px | Avatars, pills, FAB |

### 4.3 Shadow

| Token | Tailwind | Usage |
| --- | --- | --- |
| 0 | `shadow-none` | Default |
| 1 | `shadow-sm` | Hover lift |
| 2 | `shadow-md` | Sticky headers, dropdowns |
| 3 | `shadow-lg` | Modals, popovers |
| 4 | `shadow-xl` | Featured cards |

### 4.4 Motion

| Token | Duration | Ease |
| --- | --- | --- |
| `duration-150` | 150ms | `ease-out` (hover) |
| `duration-200` | 200ms | `ease-in-out` (state) |
| `duration-300` | 300ms | `ease-out` (entry) |

---

## 5. Tailwind CDN Setup with Custom Tokens

Drop this in the `<head>` of every Blade layout.

```html
<!DOCTYPE html>
<html
  lang="{{ app()->getLocale() }}"
  dir="{{ in_array(app()->getLocale(), ['ar','he','fa','ur']) ? 'rtl' : 'ltr' }}"
  class="h-full bg-white text-slate-700"
>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0F94EA">
  <title>@yield('title', 'ServiceHub')</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              50:'#F0F8FF',100:'#E0F1FE',200:'#BAE0FD',300:'#7CC8FB',
              400:'#38ADF7',500:'#0F94EA',600:'#0277C7',700:'#0561A1'
            },
            accent: {
              50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',
              400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C'
            }
          },
          fontFamily: {
            sans: ['Inter','system-ui','sans-serif'],
            bn:   ['"Hind Siliguri"','"Noto Sans Bengali"','sans-serif'],
            ar:   ['Cairo','Tajawal','"Noto Naskh Arabic"','sans-serif']
          },
          boxShadow: {
            soft: '0 4px 20px -8px rgba(15, 148, 234, 0.15)',
            warm: '0 4px 20px -8px rgba(249, 115, 22, 0.15)'
          }
        }
      }
    }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Hind+Siliguri:wght@400;500;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    html[lang="bn"] body { font-family: 'Hind Siliguri', system-ui, sans-serif; line-height: 1.75; }
    html[lang="ar"] body { font-family: 'Cairo', system-ui, sans-serif; }
    html[lang="en"] body { font-family: 'Inter', system-ui, sans-serif; }
    [dir="rtl"] .rtl-flip { transform: scaleX(-1); }
  </style>
</head>
```

---

## 6. RTL/LTR Strategy

### 6.1 Logical Properties — Always

| ❌ Avoid | ✅ Use |
| --- | --- |
| `ml-*` | `ms-*` |
| `mr-*` | `me-*` |
| `pl-*` | `ps-*` |
| `pr-*` | `pe-*` |
| `text-left` | `text-start` |
| `text-right` | `text-end` |
| `border-l-*` | `border-s-*` |
| `rounded-l-*` | `rounded-s-*` |
| `left-*` | `start-*` |
| `right-*` | `end-*` |

### 6.2 Icons That Flip

Chevrons, arrows in CTAs, progress, send button — wrap with `.rtl-flip`.

### 6.3 Icons That Don't Flip

Logos, numbers, phone/email/location pins, social glyphs, play/pause, map.

### 6.4 Number & Currency

- Use Latin digits universally.
- `BDT`: `৳ 1,500` (symbol leading).
- `AED`: `1,500 د.إ` in Arabic (trailing); `AED 1,500` in English.

---

## 7. Responsive Breakpoint Strategy

| Breakpoint | Width | Tailwind | Container | Use Case |
| --- | --- | --- | --- | --- |
| Mobile S | 360–479 | default | full-bleed, `px-4` | Cheap Android |
| Mobile L | 480–639 | `sm:` | full-bleed, `px-4` | Bigger phones |
| Tablet | 640–1023 | `md:` lg→ | `md:max-w-3xl` | iPads |
| Desktop | 1024–1279 | `lg:` | `lg:max-w-5xl` | Laptops |
| Wide | 1280+ | `xl:` | `xl:max-w-7xl` | Monitors |

**Rules:**
- Test 360px first. If it works there, it works everywhere.
- Never use fixed widths > 100vw at any breakpoint.
- Use `flex-wrap` and `grid-cols-1 md:grid-cols-2 lg:grid-cols-3` not magic numbers.
- Touch targets ≥ 44px on mobile, 40px on dashboard desktop.
- Sticky/fixed elements respect `env(safe-area-inset-*)`.

---

# PART A — Public Frontend Design

---

## 8. Page Skeleton

```
┌──────────────────────────────────────────────┐
│  Top Announcement Bar (banners.position=…)   │
├──────────────────────────────────────────────┤
│  Header                                      │
│   Logo · Nav · Search · Lang · Login · CTA   │
├──────────────────────────────────────────────┤
│  Page Content                                │
├──────────────────────────────────────────────┤
│  Footer (multi-column + locale switcher)     │
└──────────────────────────────────────────────┘
```

Container widths per breakpoint as in §7.

---

## 9. Header & Navigation

### 9.1 Desktop Header

```
[Logo]   [Categories ▾] [How it Works] [Become a Provider]   [Search bar]   [🌐 EN/BN/AR] [Login] [Sign Up]
```

- Height: 72px
- `bg-white/90 backdrop-blur border-b border-slate-200`
- Sticky: `sticky top-0 z-40`
- "Become a Provider" is the **only** orange CTA in the header

### 9.2 Mobile Header

```
[☰]  [Logo]                                  [🔍] [👤]
```

- 56px tall
- Hamburger opens slide-in drawer (320px from `start`)
- Drawer holds categories, language switcher, login, dark mode toggle (future)

### 9.3 Language Switcher

```
🌐 EN ▾
   ├ English
   ├ বাংলা
   └ العربية
```

- Stored in cookie + URL prefix
- Globe icon never flips
- POST `/lang/{code}` with CSRF

---

## 10. Homepage Sections

Order top → bottom:

### 10.1 Hero

- 360px tall mobile, 520px desktop
- `bg-gradient-to-br from-primary-50 via-white to-accent-50`
- H1 (Display): "Find trusted local pros, near you"
- Subtitle 1 line, `text-slate-500`
- **Search bar widget** — service input + location input + [Search] button
  - Mobile: stacked vertical inside `rounded-2xl` card
  - Desktop: 3 inline inputs

### 10.2 Trust Strip

- 4 inline stats: `5,000+ Providers`, `20 Cities`, `98% Verified`, `4.8★ Avg`
- `bg-primary-50`, icons in `text-primary-500`

### 10.3 Popular Categories Grid

- 4 cols desktop, 3 tablet, 2 mobile
- Tile: icon + label + provider count
- Hover: `shadow-md`, accent border-bottom

### 10.4 How It Works

- 3 numbered cards in a row (collapses to vertical stack on mobile)
- Circular icon `bg-primary-100 text-primary-600`
- Dashed connector line on desktop

### 10.5 Featured Providers

- Carousel mobile, 4-col grid desktop
- Uses Provider Card (§14)

### 10.6 Recent Requirements Feed (Public)

- "People nearby are looking for…" pulled from recent `customer_requirements` (status = open)
- Each item: category icon + short description + area + time ago
- CTA: "Become a provider to respond"

### 10.7 Coupon Promo Banner (when active)

- Pulled from `banners` where `position=homepage_top` and date range valid
- Full-width row in `bg-accent-50 text-accent-700`
- Format: `🎁 SAVE20 — Get 20% off Yearly plan · Use code SAVE20 · Ends Jun 30`
- Dismissible (cookie remembers)

### 10.8 Testimonials

- 3 quote cards in `bg-white shadow-sm border border-slate-200`
- 5-star row `text-accent-500`

### 10.9 "Become a Provider" CTA

- Full-width orange section `bg-accent-50`
- H2 + paragraph + button `bg-accent-500 text-white`
- Only large orange surface — deliberate hand-off

### 10.10 Footer

- 4 columns: Company · For Customers · For Providers · Support
- Bottom: copyright · language switcher · social icons · app badges (Phase 3)
- `bg-slate-50 border-t border-slate-200`

---

## 11. Search & Listing Page

### 11.1 Layout

```
┌─────────────────────────────────────────────┐
│ Search Bar (sticky)                          │
├──────────────┬──────────────────────────────┤
│ Filters      │  Results header              │
│  - Category  │   "234 providers in Dhaka"   │
│  - Distance  │   [Sort ▾] [Map view]        │
│  - Rating    │                              │
│  - Price     │  ┌─────┐ ┌─────┐ ┌─────┐    │
│  - Verified  │  │card │ │card │ │card │   │
│  - Open now  │  └─────┘ └─────┘ └─────┘    │
│  - Type      │                              │
│              │  [Load more] / pagination    │
└──────────────┴──────────────────────────────┘
```

- Sidebar 280px sticky desktop, drawer mobile
- Results: 1-col mobile, 2-col tablet, 3-col desktop
- Map view: split 50/50 toggle

### 11.2 Filter Patterns

- Collapsible groups (`<details>` + Alpine)
- Active filters shown as removable chips above results: `bg-primary-100 text-primary-700`

---

## 12. Provider Profile Page

### 12.1 Layout (Desktop)

```
┌────────────────────────────────────────────┐
│ Cover (200px gradient or upload)           │
├────────────────────────────────────────────┤
│ [Avatar] Name + Verified                   │
│          ⭐ rating · # reviews              │
│          📍 Areas · Languages               │
│          [Contact] [Save] [Share]          │
├────────────┬───────────────────────────────┤
│ About      │                               │
│ Services   │  Sticky right card:           │
│ Gallery    │   - Hours today               │
│ Reviews    │   - Closed dates (holidays)   │
│ Hours      │   - Response time             │
│ Areas Map  │   - [Get a quote] CTA         │
└────────────┴───────────────────────────────┘
```

### 12.2 Tabs

About · Services · Gallery · Reviews · Hours · Areas.
Sticky tab bar `bg-white border-b border-slate-200`.
Active: `text-primary-600 border-b-2 border-primary-500`.

### 12.3 Quote Modal

Triggered by `[Get a quote]`. Form: service, description, date, attachments. Submit creates a `service_requests` row.

### 12.4 Holiday Chip

When provider has an upcoming `holidays` row within 30 days, show a warm chip in the sticky card:

```
🍂 Closed Jun 15–17 — Eid holiday
```

`bg-accent-50 text-accent-700 ring-1 ring-accent-200 rounded-full px-3 py-1 text-xs`

---

## 13. Buttons

| Variant | Class | When |
| --- | --- | --- |
| Primary | `inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-500 text-white font-medium hover:bg-primary-600 active:bg-primary-700 transition` | Main CTAs |
| Secondary | `… bg-accent-500 text-white hover:bg-accent-600` | "Become a provider", hot CTAs |
| Outline | `… border border-slate-300 text-slate-700 hover:bg-slate-50` | Tertiary |
| Ghost | `… text-slate-700 hover:bg-slate-100` | Toolbar |
| Subtle Primary | `… bg-primary-50 text-primary-700 hover:bg-primary-100` | Inline in cards |
| Danger | `… bg-red-600 text-white hover:bg-red-700` | Destructive |
| Link | `text-primary-600 hover:text-primary-700 underline-offset-4 hover:underline` | Inline links |

Sizes: `sm` (px-3 py-1.5 text-sm), `md` (default), `lg` (px-6 py-3 text-base).
Disabled: `opacity-50 cursor-not-allowed`.
Loading: spinner + dimmed text, full width preserved.

---

## 14. Provider Card (Canonical)

```
┌──────────────────────────────────────────────┐
│ [Photo 64×64]  Rahim AC Service  ✓ Verified  │
│                ⭐ 4.9 (231)                   │
│                📍 Gulshan · 2.4 km            │
│                                              │
│  AC Repair · Installation · Cleaning         │
│                                              │
│  Starts at ৳500            [View profile →]  │
└──────────────────────────────────────────────┘
```

- `bg-white rounded-2xl border border-slate-200 hover:shadow-md transition`
- Photo: `rounded-full`, `start`-side, 56px mobile / 72px desktop
- Verified pill: `bg-green-50 text-green-700`
- Featured providers: `ring-1 ring-primary-200` + "Featured" pill `bg-accent-100 text-accent-700`
- Whole card tappable on mobile
- Arrow `→` (LTR) / `←` (RTL via `rtl-flip`)

---

## 15. Forms

### 15.1 Field Pattern

```html
<label class="block">
  <span class="block text-sm font-medium text-slate-700 mb-1.5">Mobile Number</span>
  <input type="tel"
    class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5
           text-slate-900 placeholder-slate-400 shadow-sm
           focus:border-primary-500 focus:ring-2 focus:ring-primary-100
           focus:outline-none transition"
    placeholder="01XXXXXXXXX">
  <span class="mt-1 block text-xs text-slate-500">We'll send a 6-digit OTP.</span>
</label>
```

Errors: `border-red-400`, hint becomes `text-red-600`.
Required: red asterisk after label, never red label.

### 15.2 OTP Input

6 boxes, 44×52px, `rounded-lg border border-slate-300`. Auto-advance + backspace via Alpine. Resend timer below.

### 15.3 Map Picker (provider service area)

- Google Map, 320px tall
- Search box top with autocomplete
- Drag pin to center
- Slider 1–50 km, live circle on map
- Confirm `bg-primary-500`

### 15.4 Coupon Input (Subscription Checkout)

```
┌───────────────────────────────────┐
│ Coupon code                       │
│ ┌────────────────┬──────────────┐ │
│ │ SAVE20         │   [Apply]    │ │
│ └────────────────┴──────────────┘ │
│ ✓ 20% off applied — saved ৳600    │
└───────────────────────────────────┘
```

- Input + Apply button inline
- Success: green check + savings amount in `text-green-700`
- Error: red text under input ("Invalid code" / "Expired" / "Already used")

---

## 16. Badges & Chips

| Badge | Style |
| --- | --- |
| Verified | `bg-green-50 text-green-700 ring-1 ring-green-200` ✓ |
| Featured | `bg-accent-100 text-accent-700 ring-1 ring-accent-200` ★ |
| New | `bg-primary-100 text-primary-700` |
| Pending | `bg-yellow-50 text-yellow-700` |
| Rejected | `bg-red-50 text-red-700` |
| Top Rated | `bg-gradient-to-r from-accent-100 to-primary-100 text-accent-700` |
| Emergency | `bg-red-50 text-red-700 ring-1 ring-red-200` ⚡ |
| Holiday | `bg-accent-50 text-accent-700 ring-1 ring-accent-200` |

Pill: `inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium`.

---

## 17. Empty / Loading / Error States

### 17.1 Empty

- Centered line-art illustration (primary-300 strokes)
- H3 message + body + primary CTA
- `bg-slate-50 rounded-2xl p-8`

### 17.2 Loading

- Skeletons (`bg-slate-100 animate-pulse rounded-md`) for lists
- Spinner for button-level async
- NProgress-style top bar for page transitions, color `primary-500`

### 17.3 Errors

- Inline alert above form: `bg-red-50 border border-red-200 text-red-700 rounded-lg p-3`
- Toast top-end desktop, top-center mobile: `bg-slate-900 text-white rounded-lg shadow-lg`

---

## 18. PWA & Mobile Polish

- Bottom safe-area: `pb-[env(safe-area-inset-bottom)]`
- Bottom nav on customer mobile (Home · Search · Requests · Saved · Profile) — 64px tall, `bg-white border-t`
- Active tab `text-primary-600` + small dot
- Pull-to-refresh on listing pages

---

## 19. Iconography

- **Lucide Icons** primary set
- 20×20 default, 16×16 inline, 24×24 in buttons
- Stroke 1.75–2.0, `currentColor`
- `.rtl-flip` for direction-sensitive icons

---

# PART B — Dashboard Design

---

## 20. Dashboard Layout

### 20.1 Three-Pane (Desktop)

```
┌─────────┬───────────────────────────────────┐
│         │ Topbar                            │
│ Sidebar ├───────────────────────────────────┤
│ 240px   │                                   │
│         │  Main Content                     │
│         │  - Page header + breadcrumbs      │
│         │  - KPI / Filter row               │
│         │  - Primary content                │
└─────────┴───────────────────────────────────┘
```

- Sidebar: 240px fixed, `bg-white border-e border-slate-200`
- Topbar: 64px, `bg-white border-b border-slate-200`
- Main: `bg-slate-50`, `p-4 md:p-6 lg:p-8`

### 20.2 Mobile Dashboard

- Sidebar collapses behind hamburger drawer
- Topbar shrinks to 56px
- Main padding 16px
- Bottom tab bar replaces sidebar primary nav

---

## 21. Sidebar (Provider Role)

```
ServiceHub  •  Provider

🏠  Dashboard
📋  My Services
📍  Service Areas
🗓  Hours & Holidays
📨  Requests
💼  Leads (Requirements)
💬  Messages         (3)
⭐  Reviews
💳  Subscription
🎁  Coupons
👥  Affiliate
📊  Analytics
🛠  Support Tickets
⚙️  Settings
                 ──────
              Help & Support
```

- Logo + role label, `border-b border-slate-200 pb-4 mb-4`
- Each item: `flex items-center gap-3 px-3 py-2 rounded-lg text-sm`
- Active: `bg-primary-50 text-primary-700 font-medium border-s-2 border-primary-500`
- Hover: `hover:bg-slate-100 text-slate-700`
- Counters: pill `bg-accent-100 text-accent-700 text-xs`
- Bottom: subscription mini-card

### 21.1 Subscription Mini-Card

```
┌──────────────────────────────┐
│  Yearly Plan                 │
│  ✦ 187 days remaining        │
│  [█████████░░░░] 62%         │
│  [Upgrade]                   │
└──────────────────────────────┘
```

- `bg-primary-50 rounded-xl p-3`
- Progress: `bg-primary-200` track, `bg-primary-500` fill
- Upgrade button orange to encourage action

---

## 22. Topbar

```
[Breadcrumb / Page Title]              [Search] [🔔(2)] [🌐 EN] [Avatar ▾]
```

- Page title: `text-lg font-semibold text-slate-900`
- Breadcrumb: small `text-slate-500`
- Search: max 320px input `bg-slate-50`
- Notification bell: dropdown panel
- Avatar dropdown: profile, settings, logout

---

## 23. Dashboard Content Patterns

### 23.1 Page Header

```
H1 — Dashboard           [⤓ Export]   [➕ New Request]
Sub: Welcome back, Rahim
```

Margin bottom `mb-6`.

### 23.2 KPI Cards

4 cards desktop, 2 tablet, 1 mobile.

```
┌────────────────────────┐
│  Active Leads          │
│  142          ↑ 12%    │
│  vs last 30 days       │
└────────────────────────┘
```

- `bg-white rounded-xl border border-slate-200 p-5`
- Title `text-sm text-slate-500 font-medium`
- Number `text-3xl font-bold text-slate-900`
- Delta `text-xs`, green positive / red negative
- Optional sparkline on `end` side (SVG, `text-primary-400`)

### 23.3 Action & Filter Bar (Native)

```
[🔍 Real Search Input]    [Action Group: ⤓ Export ▾] [➕ Primary CTA]
```

- Sticky `sticky top-16 bg-slate-50 z-10 py-3`.
- **Search**: Large native input with persistent background (`bg-slate-50`), focus states, and server-side processing.
- **Action Group**: Dropdown-based export actions (Print, Excel, CSV) using Alpine.js for a professional, compact feel.

### 23.4 Data Tables (Laravel Native)

- **Engine**: Standard Laravel Eloquent queries with `->paginate()` for a robust, SEO-friendly, and performant experience.
- **Header**: `bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-100`.
- **Rows**: `hover:bg-slate-50/50 transition` + `border-b border-slate-100`.
- **Status column**: Pill-style badge with an internal status dot (Green for Active, Slate for Inactive).
- **Action column**: Grouped icon buttons with hover backgrounds and tooltips.
- **Pagination**: Real Laravel pagination links (`$collection->links()`) styled to match the dashboard's design system (custom SVG icons for navigation).
- **On mobile**: Horizontal scroll for tables; prioritize columns or collapse to card list.

### 23.5 Charts

- **Chart.js** for time series, bar, doughnut
- Primary series `primary-500`, secondary `accent-500`, grid `slate-300`
- Area tint `rgba(15, 148, 234, 0.08)`
- Locale-aware number formatting
- Wrap in `bg-white rounded-xl border border-slate-200 p-5`

---

## 24. Provider Dashboard — Specific Modules

### 24.1 Dashboard Home

1. KPI row: Leads (`requirement_proposals` count) · Requests · Earnings est. · Rating
2. "Today's Schedule" card — accepted `service_requests` for today
3. Recent leads list (5 most recent `customer_requirements`)
4. 30-day performance chart
5. Pending verification banner (if not verified)
6. Holiday quick-add prompt (if no upcoming)

### 24.2 Leads Page

- Tabs: New · Saved · Submitted · Lost
- Filters: distance, budget, urgency
- Each lead: full-width card with budget badge, urgency dot, area, time-ago, [View & Propose] CTA
- Free-tier providers see lock overlay on premium leads with [Upgrade] CTA

### 24.3 Service Areas

- List of zones with map preview thumbnails
- "Add area" FAB `bg-accent-500`
- Plan limit indicator: "3 of 5 areas used"
- Edit modal opens map picker (§15.3)

### 24.4 Hours & Holidays

Combined page, two cards:

**Business Hours card**
- 7 rows, Sun–Sat
- Per row: toggle (open/closed), start time, end time
- "Copy to all days" link

**Holidays card**
- Mini calendar with marked dates
- List of upcoming holidays (date + reason)
- "Add holiday" button → modal (date picker + reason input)

### 24.5 Subscription Page

- Current plan banner gradient `from-primary-50 to-accent-50`
- Usage meters: leads used / total, areas used / total, gallery used / total
- Plan comparison grid (4 cards horizontal scroll on mobile)
- Recommended plan: orange ring + "Best value" badge
- Coupon input row above checkout
- Invoice history table (from `subscription_invoices`)

### 24.6 Coupons Page (provider view)

- Available public coupons (`coupons.status=active` and within date range)
- Cards showing code, discount, expiry
- "Copy code" button

### 24.7 Affiliate Page

- Big card at top: referral URL with copy button
- KPI row: Total Referrals · Pending · Approved · Paid
- Earnings chart (monthly)
- Referrals table (`referrals` rows) with status badges
- Payout history
- "How it works" expandable

### 24.8 Analytics (paid plans)

- Funnel: views → contacts → requests → completed
- Hot-zones heatmap
- Top services
- Review sentiment timeline

### 24.9 Support Tickets

- List view with ticket_number, subject, status badge, last_reply_at
- "New Ticket" button → modal with department dropdown
- Ticket detail page: threaded messages, reply textarea, status changer

---

## 25. Customer Dashboard — Specific Modules

### 25.1 Home

- Recent requests carousel
- Saved providers grid
- "Looking for something?" big search card
- Active requirement posts

### 25.2 My Requests

- Tabs: Active · Completed · Cancelled
- Each request card: provider info, status timeline, chat shortcut

### 25.3 My Addresses

- Card list of saved `customer_addresses`
- Each card shows type badge (House/Office/Business), address, primary indicator
- "Set as primary" / "Edit" / "Delete" actions
- "Add address" button → modal with map picker

### 25.4 My Requirements

- List of posts + proposal counts
- Filter: open · assigned · expired

### 25.5 Saved Providers

- Grid of Provider Cards (§14)

### 25.6 Reviews

- Reviews you wrote
- Reviews about you (if customer also has a provider profile)

---

## 26. Admin Dashboard — Specific Modules

### 26.1 Overview

- Big KPI grid (Providers, Customers, MRR, ARPU, Active Subs, Churn)
- Live map of active requests
- New sign-ups today / this week
- Pending verifications counter (priority — orange tint)
- Pending affiliate payouts counter

### 26.2 Verification Queue

- Kanban: Pending · In Review · Approved · Rejected
- Card per provider with documents preview, quick approve/reject
- Bulk actions

### 26.3 Catalog (Categories)

- **Design**: Native Laravel table with real-time server-side search.
- **Multilingual Support**: Edit modal/page writing to the `translations` JSON column.
- **Assets**: Icon and Cover image previews in the table.
- **Actions**: Professional Export dropdown (Excel, CSV, Print).
- **Sort**: Drag-to-reorder (Phase 2) or manual `sort_order` input.

### 26.4 Subscriptions Manager

- Plan editor (writes `subscription_plans`)
- Active subscriptions table

### 26.5 Coupons Manager

- Coupon table with usage stats
- Create coupon modal: code, discount type, value, usage limit, dates
- Status toggle

### 26.6 Affiliate Payouts

- Approval queue (`referrals` where `status=approved`)
- Per-affiliate aggregation
- Bulk payout marking
- Export CSV

### 26.7 Reports & Disputes / Support

- Inbox-style layout
- Support ticket queue with priority badges
- Status workflow

### 26.8 Settings

- Search ranking weights (sliders writing to `settings`)
- Region & locale config
- Email/SMS template editor
- Integration keys (bKash, Stripe, Google Maps) — encrypted

---

## 27. Dashboard Component Library

### 27.1 Card

```html
<div class="bg-white rounded-xl border border-slate-200 p-5">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-semibold text-slate-900">Card title</h3>
    <button class="text-slate-400 hover:text-slate-600">⋯</button>
  </div>
  <div>...</div>
</div>
```

### 27.2 Section Header

```html
<div class="flex items-center justify-between mb-4">
  <div>
    <h2 class="text-xl font-semibold text-slate-900">Recent leads</h2>
    <p class="text-sm text-slate-500">Last 30 days, sorted by date</p>
  </div>
  <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700">
    View all <span class="rtl-flip">→</span>
  </a>
</div>
```

### 27.3 Modal

- Tailwind + Alpine `x-show`, `x-transition`
- Backdrop `bg-slate-900/50`
- Panel `bg-white rounded-2xl shadow-xl max-w-lg w-full p-6`
- Header close icon at `end`
- Footer: cancel ghost (`start`) + primary action (`end`)

### 27.4 Tabs

```html
<nav class="flex gap-1 border-b border-slate-200 overflow-x-auto">
  <button class="px-4 py-2.5 text-sm font-medium border-b-2 border-primary-500 text-primary-600 whitespace-nowrap">All</button>
  <button class="px-4 py-2.5 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700 whitespace-nowrap">Pending</button>
</nav>
```

Mobile: horizontal scroll allowed.

### 27.5 Toast

```html
<div class="fixed top-4 end-4 z-50 bg-slate-900 text-white rounded-lg shadow-lg px-4 py-3 text-sm">
  ✓ Saved successfully
</div>
```

### 27.6 Address Card

```
┌────────────────────────────────────────┐
│ [🏠 House]   ✦ Primary                 │
│ Home — 23, Road 4, Gulshan-2           │
│ Dhaka 1212, Bangladesh                 │
│                       [Edit] [Delete]  │
└────────────────────────────────────────┘
```

`bg-white rounded-xl border border-slate-200 p-4`. Type icon in colored chip. Primary indicator in primary chip.

### 27.7 Affiliate Stat Card

```
┌──────────────────────┐
│ Total Earnings       │
│ ৳ 14,250             │
│ ↑ 12% this month     │
└──────────────────────┘
```

Same as KPI card. Add a "Withdraw" button on the Total Available card.

### 27.8 Coupon Card

```
┌──────────────────────────────────────┐
│  SAVE20                              │
│  20% off Yearly plan                 │
│  Expires Jun 30                      │
│                       [Copy code]    │
└──────────────────────────────────────┘
```

`bg-accent-50 border border-accent-200 rounded-xl p-4 ring-1 ring-accent-100`.

### 27.9 Support Ticket Row

```
TKT-2026-000045  ·  Subject       Department  Priority  Status      Last reply
                                  Billing     [High]    [Replied]   2h ago
```

Status badge maps to color:
- open: primary
- pending: yellow
- replied: green
- resolved: slate
- closed: slate-muted

---

## 28. Dashboard Density & Touch Targets

- Default row height: 48px
- Compact row (admin tables): 40px
- Sidebar items: 40px
- Buttons in dashboard: 40px tall
- 8px breathing room between adjacent interactive elements

---

## 29. Accessibility

- WCAG 2.1 AA on all text/background pairs
- primary-500 on white = 4.55:1 ✓
- accent-500 on white = 3.85:1 — only use for ≥18px or bold; otherwise accent-600
- Focus ring: `focus:outline-none focus:ring-2 focus:ring-primary-200 focus:border-primary-500`
- All icons `aria-label` or sr-only
- `<label for="">` on every form field
- Skip-to-main link top of every page
- Color never the only signal
- `prefers-reduced-motion` respected

```css
@media (prefers-reduced-motion: reduce) {
  * { transition: none !important; animation: none !important; }
}
```

---

## 30. Asset & Imagery Guidelines

- Photos: local providers/customers preferred, otherwise warm natural-light non-staged stock
- Illustrations: minimal line-art primary-300 stroke + accent-300 highlights
- Logos: SVG, min 24px tall
- Profile photos: 200×200 square, lazy-loaded
- Gallery: 4:3 default, masonry desktop, single-column carousel mobile

---

## 31. Sample Blade Master Layout

```blade
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
      dir="{{ in_array(app()->getLocale(),['ar']) ? 'rtl' : 'ltr' }}"
      class="h-full bg-white text-slate-700">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0F94EA">
  <title>@yield('title', config('app.name'))</title>
  @include('layouts.partials.tailwind-config')
  @include('layouts.partials.fonts')
  @stack('head')
</head>
<body class="min-h-full antialiased">
  @include('layouts.partials.announcement-bar')
  @include('layouts.partials.header')
  <main>@yield('content')</main>
  @include('layouts.partials.footer')
  @stack('scripts')
</body>
</html>
```

---

## 32. Design Tokens — Quick Reference

```
COLORS
  primary  500 #0F94EA   600 #0277C7   50 #F0F8FF
  accent   500 #F97316   600 #EA580C   50 #FFF7ED
  text     900 #0F172A   700 #334155   500 #64748B
  surface  white         soft #F8FAFC   muted #F1F5F9
  border   200 #E2E8F0   300 #CBD5E1

RADIUS  md 6 · lg 8 · xl 12 · 2xl 16 · full
SHADOW  sm · md · lg · xl
SPACE   4 (16) · 6 (24) · 8 (32) · 12 (48)

TYPE
  Display 28→44   H1 24→32   H2 20→24   H3 18→20
  Body 15   Body L 16/17   Small 13   Tiny 12

MOTION
  hover 150 ease-out  ·  state 200 ease-in-out  ·  enter 300 ease-out
```

---

## 33. Build Order

1. Master Blade layout + Tailwind config partial
2. Buttons (all variants)
3. Form fields + OTP input + coupon input
4. Badges + chips
5. Cards (generic, provider, address, coupon, affiliate)
6. Header + footer
7. Sidebar + topbar (dashboard)
8. Modal + toast + alert
9. Tabs + filters + table
10. Map picker + autocomplete
11. KPI card + chart wrapper
12. Empty / loading / error states

---

## 34. Don'ts

- ❌ No new accent colors without doc update
- ❌ No `ml-*` / `mr-*` — only logical
- ❌ No `text-left` / `text-right` — only `text-start` / `text-end`
- ❌ No multiple gradients on one screen
- ❌ Primary blue + orange adjacent only at the deliberate "Become a Provider" handoff
- ❌ No drop shadows on every card — reserve for elevation
- ❌ No type-scale overrides per page
- ❌ Don't ship without verifying RTL via `?lang=ar`
- ❌ Don't ship without testing 360px width

---

## 35. Document Control

| Version | Date | Author | Notes |
| --- | --- | --- | --- |
| 1.0 | Earlier | UnlockWare | Initial design system — light blue & orange palette, RTL/LTR |
| **1.2** | **Today** | **Antigravity** | **Migration to Native Laravel Tables. Replaced DataTables JS with server-side pagination & search. Redesigned action bars with professional export dropdowns. Refined status badges and table typography.** |

> Authoritative design reference. Every Blade component, Tailwind utility, and Alpine pattern in the codebase must align with this document. Update here first, then implement.
