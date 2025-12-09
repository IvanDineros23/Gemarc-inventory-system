# Toast Notification Flow Diagram

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    Gemarc Inventory System                      │
│                    Toast Notification Flow                      │
└─────────────────────────────────────────────────────────────────┘

                          ┌──────────────────┐
                          │   User Action    │
                          │  (Click, Submit) │
                          └────────┬─────────┘
                                   │
                    ┌──────────────┴──────────────┐
                    │                             │
            ┌───────▼────────┐          ┌────────▼─────────┐
            │  Form Submit   │          │  AJAX Request    │
            │  (Page Reload) │          │  (Background)    │
            └───────┬────────┘          └────────┬─────────┘
                    │                            │
        ┌───────────▼─────────────┐  ┌──────────▼────────────────┐
        │   Controller Process    │  │  Controller Process       │
        │   (Store/Update/Delete) │  │  (Store/Update/Delete)    │
        └───────────┬─────────────┘  └──────────┬────────────────┘
                    │                            │
    ┌───────────────┼────────────────────────────┼──────────────┐
    │               │                            │              │
┌───▼──────┐    ┌───▼──────┐               ┌────▼────┐    ┌────▼────┐
│ Success  │    │  Error   │               │ Success │    │  Error  │
│  (201)   │    │  (422)   │               │(200 OK) │    │ (4xx)   │
└───┬──────┘    └───┬──────┘               └────┬────┘    └────┬────┘
    │               │                           │             │
┌───▼───────────────▼──────────────┐   ┌────────▼─────────────▼────┐
│  Redirect with Session Message   │   │   Return JSON Response    │
│  - success: 'message'            │   │   - data: {...}          │
│  - error: 'message'              │   │   - errors: {...}        │
└───┬──────────────────────────────┘   └────────┬─────────────────┘
    │                                           │
    │   ┌─────────────────────────────────────┬─┘
    │   │                                     │
┌───▼───▼──────────────────────────────────┐  │
│   Browser: Load New Page                 │  │
│   Layout renders session messages        │  │
└───┬──────────────────────────────────────┘  │
    │                                        │
    │   ┌────────────────────────────────────┘
    │   │
┌───▼───▼──────────────────────────────────┐
│   Layout: app.blade.php                  │
│   - If session('success') → show toast   │
│   - If session('error') → show toast     │
└───┬──────────────────────────────────────┘
    │
    │   ┌──────────────────────────────────┐
    │   │                                  │
┌───▼───▼──────────────────────────────────┐  │
│   JavaScript: app.js                     │  │
│   - window.showToast() creates toast    │  │
│   - Auto-fade after 4 seconds           │  │
│   - Remove from DOM                     │  │
└──────────────────────────────────────────┘  │
                                             │
        ┌────────────────────────────────────┘
        │
        └──────────────────────────────────┐
                                          │
                        ┌─────────────────▼─────────────┐
                        │   User Sees Toast             │
                        │   ✅ Success (Green)          │
                        │   ❌ Error (Red)              │
                        │   ⚠️  Warning (Yellow)        │
                        │   ℹ️  Info (Blue)             │
                        │                               │
                        │   Fades out automatically     │
                        └───────────────────────────────┘
```

---

## AJAX Request Flow

```
User Action
    ↓
JavaScript Event Handler
    ↓
Prepare FormData / Request Body
    ↓
Fetch API Call
    ├─ POST/PUT/DELETE to controller
    ├─ X-CSRF-TOKEN in headers
    └─ X-Requested-With: XMLHttpRequest
    ↓
Controller Processes Request
    ├─ Validates data
    ├─ Creates/Updates/Deletes model
    └─ Returns JSON response
    ↓
Client Receives Response
    ├─ Check resp.ok
    ├─ Parse JSON
    └─ Extract data/errors
    ↓
Display Toast
    ├─ Success: showToast(message, 'success')
    ├─ Error: showToast(errors, 'error')
    └─ Auto-fade after 4 seconds
    ↓
Update DOM
    ├─ Add/update table rows
    ├─ Refresh dashboard stats
    └─ Clear form inputs
```

---

## Session Request Flow

```
User Action (Form Submit)
    ↓
JavaScript Form Handler
    ↓
Fetch/Submit to controller
    ├─ POST to create/update/delete
    └─ Form data includes CSRF token
    ↓
Controller Processes
    ├─ Validates
    ├─ Creates model
    ├─ Prepares response
    └─ with('success'/'error', 'message')
    ↓
Redirect Response
    └─ HTTP 302 redirect
    ↓
Browser Loads Redirected Page
    ├─ GET request to new route
    └─ Session data attached
    ↓
Blade Template Renders
    ├─ @if(session('success'))
    │   └─ Display success toast div
    ├─ @if(session('error'))
    │   └─ Display error toast div
    └─ @endif
    ↓
JavaScript Auto-Runs (DOMContentLoaded)
    ├─ Select toast element
    ├─ Set opacity: 1 (visible)
    ├─ Wait 4 seconds
    ├─ Fade to opacity: 0
    └─ Remove from DOM
```

---

## Toast Display Timeline

```
Timeline (4000ms total)
├─ 0ms     : Toast appears (opacity: 1)
│           Fixed position: top-right
│           Shadow and styling applied
│
├─ 3800ms  : Still visible
│           User can read message
│
├─ 4000ms  : Fade begins
│           opacity: 1 → 0 (500ms transition)
│
├─ 4500ms  : Fade complete
│           JavaScript removes from DOM
│
└─ After   : DOM cleaned up
            No memory leaks
```

---

## Toast Color Scheme

```
┌─────────────────────────────────────────────────┐
│  Toast Type     Color     Tailwind Class         │
├─────────────────────────────────────────────────┤
│  ✅ Success     Green     bg-green-500          │
│  ❌ Error       Red       bg-red-600            │
│  ⚠️  Warning    Yellow    bg-yellow-500         │
│  ℹ️  Info       Blue      bg-blue-500           │
└─────────────────────────────────────────────────┘
```

---

## Page Coverage Map

```
┌───────────────────────────────────────────────────────────┐
│                  Gemarc Inventory Pages                   │
├───────────────────────────────────────────────────────────┤
│                                                           │
│  Dashboard                                   No toasts    │
│  ├─ Summary cards                     (displays data)     │
│  └─ Charts                                                │
│                                                           │
│  Receiving Entry              ✅ 16 AJAX toasts          │
│  ├─ Add product              ✅ Success/Error            │
│  ├─ List products            ✅ Success/Error            │
│  ├─ Receive entry            ✅ Success/Error            │
│  ├─ Edit receiving           ✅ Success/Error            │
│  └─ Delete receiving         ✅ Success/Error            │
│                                                           │
│  Delivery Entry               ✅ 1 Validation toast       │
│  ├─ Add to cart              ✅ Warning                  │
│  └─ Submit delivery          Session redirect            │
│                                                           │
│  Delivery Review              ✅ 2 AJAX toasts           │
│  ├─ View deliveries          (no toasts)                │
│  ├─ Approve                  ✅ Success/Error            │
│  └─ Reject                   ✅ Success/Error            │
│                                                           │
│  Product Management           Session toasts             │
│  ├─ Add product              ✅ Session message          │
│  ├─ Edit product             ✅ Session message          │
│  └─ Delete product           ✅ Session message          │
│                                                           │
│  Profile Settings             Session toasts             │
│  ├─ Update profile           ✅ Session message          │
│  ├─ Change password          ✅ Session message          │
│  ├─ Database backup          ✅ Session message          │
│  └─ Database restore         ✅ Session message          │
│                                                           │
└───────────────────────────────────────────────────────────┘
```

---

## State Transitions

```
╔════════════════════════════════════════════════╗
║           Toast Notification States            ║
╠════════════════════════════════════════════════╣
║                                                ║
║  HIDDEN → VISIBLE → FADING → REMOVED (DOM)    ║
║    ↑       opacity   opacity      cleanup      ║
║    │        = 1      = 0                       ║
║    │       (0ms)   (4000ms)    (4500ms)       ║
║    │                                           ║
║    └─── Cycles for each notification ──────┐  ║
║       (Multiple toasts possible)           │  ║
║                                            │  ║
║    Session toast:  ~5s (4s + 1s buffer)   │  ║
║    AJAX toast:     ~4s (configurable)     └──┘
║                                                ║
╚════════════════════════════════════════════════╝
```

---

## Error Handling Flow

```
AJAX Request
    ↓
    ├─ Network Error
    │  └─ catch (err)
    │     └─ showToast('Network error', 'error')
    │
    ├─ Invalid Response (4xx/5xx)
    │  └─ if (!resp.ok)
    │     ├─ Parse error JSON
    │     ├─ Extract validation errors
    │     └─ showToast(errors, 'error')
    │
    ├─ Validation Failed
    │  └─ data.errors = {...}
    │     └─ showToast(formatted_errors, 'error')
    │
    └─ Success (200)
       ├─ resp.ok = true
       ├─ Parse response JSON
       └─ showToast('Success', 'success')
```

---

**Diagram Created:** December 9, 2025  
**Part of:** Toast Notifications Implementation Guide
