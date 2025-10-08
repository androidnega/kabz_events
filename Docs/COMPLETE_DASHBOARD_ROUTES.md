# 📍 Complete Dashboard Routes Reference

## All Dashboard URLs (57 Routes Total)

### 🎯 Main Entry Point
```
GET  /dashboard                    → Auto-redirect based on user role
```

---

## 👑 Super Admin Routes (11 routes)

### Dashboard
```
GET  /dashboard/super-admin        → superadmin.dashboard
```

### SMS Management
```
GET  /dashboard/sms-test           → superadmin.sms.test
POST /dashboard/sms-test           → superadmin.sms.test.send
```

### Backup Management
```
GET    /dashboard/backups          → superadmin.backups.index
POST   /dashboard/backups/create   → superadmin.backups.create
GET    /dashboard/backups/{id}/download → superadmin.backups.download
DELETE /dashboard/backups/{id}     → superadmin.backups.destroy
```

### Location Management
```
GET    /dashboard/locations        → superadmin.locations.index
POST   /dashboard/locations        → superadmin.locations.store
GET    /dashboard/locations/upload → superadmin.locations.upload
POST   /dashboard/locations/import → superadmin.locations.import
DELETE /dashboard/locations/{id}   → superadmin.locations.destroy
```

---

## 👨‍💼 Admin Routes (18 routes)

### Dashboard
```
GET  /dashboard/admin              → admin.dashboard
```

### Vendor Verifications
```
GET  /dashboard/verifications      → admin.verifications.index
POST /dashboard/verifications/{id}/approve → admin.verifications.approve
POST /dashboard/verifications/{id}/reject  → admin.verifications.reject
POST /dashboard/verifications/{vendorId}/suspend → admin.verifications.suspend
POST /dashboard/verifications/{vendorId}/cancel  → admin.verifications.cancel
```

### Client Management
```
GET  /dashboard/clients            → admin.clients.index
GET  /dashboard/clients/{id}       → admin.clients.show
POST /dashboard/clients/{id}/activate   → admin.clients.activate
POST /dashboard/clients/{id}/deactivate → admin.clients.deactivate
POST /dashboard/clients/{id}/reset-password → admin.clients.resetPassword
```

### Reports & Issues
```
GET  /dashboard/reports            → admin.reports.index
POST /dashboard/reports/{id}/resolve → admin.reports.resolve
POST /dashboard/reports/{id}/reopen  → admin.reports.reopen
```

### User Management (Resource)
```
GET    /dashboard/users            → admin.users.index
POST   /dashboard/users            → admin.users.store
GET    /dashboard/users/create     → admin.users.create
GET    /dashboard/users/{user}     → admin.users.show
PUT    /dashboard/users/{user}     → admin.users.update
DELETE /dashboard/users/{user}     → admin.users.destroy
GET    /dashboard/users/{user}/edit → admin.users.edit
```

---

## 🛠️ Vendor Routes (11 routes)

### Dashboard
```
GET  /dashboard/vendor             → vendor.dashboard
```

### Service Management (Resource)
```
GET    /dashboard/services         → vendor.services.index
POST   /dashboard/services         → vendor.services.store
GET    /dashboard/services/create  → vendor.services.create
GET    /dashboard/services/{service} → vendor.services.show
PUT    /dashboard/services/{service} → vendor.services.update
DELETE /dashboard/services/{service} → vendor.services.destroy
GET    /dashboard/services/{service}/edit → vendor.services.edit
```

### Verification
```
GET  /dashboard/verification       → vendor.verification
POST /dashboard/verification       → vendor.verification.store
```

### Subscriptions
```
GET  /dashboard/subscriptions      → vendor.subscriptions
POST /dashboard/subscriptions/{plan} → vendor.subscriptions.subscribe
```

---

## 👤 Client Routes (1 route)

### Dashboard
```
GET  /dashboard/client             → client.dashboard
```

---

## 🔄 Common Routes (All Authenticated Users) (10 routes)

### Profile Management
```
GET    /dashboard/profile          → profile.edit
PATCH  /dashboard/profile          → profile.update
DELETE /dashboard/profile          → profile.destroy
```

### Messages System
```
GET  /dashboard/messages           → messages.index
GET  /dashboard/messages/conversation → messages.conversation
POST /dashboard/messages/send      → messages.store (rate limited)
```

### Notifications
```
POST /dashboard/notifications/{id}/read → notifications.read
POST /dashboard/notifications/mark-all-read → notifications.markAllAsRead
```

### Vendor Registration (Upgrade)
```
GET  /dashboard/vendor-register    → vendor.register
POST /dashboard/vendor-register    → vendor.store
```

---

## 📊 Route Summary

| Role | Routes Count |
|------|--------------|
| Super Admin | 11 routes |
| Admin | 18 routes |
| Vendor | 11 routes |
| Client | 1 route |
| Common (All Users) | 10 routes |
| **Main Entry** | 1 route |
| **TOTAL** | **57 routes** |

---

## 🔒 Security

All routes are protected by:
- `auth` middleware - Must be authenticated
- Role-specific middleware (`role:super_admin`, `role:admin`, etc.)
- Rate limiting on sensitive actions (messages, interactions)

---

## ✅ Benefits of Unified Structure

1. **Consistent URLs** - Everything under `/dashboard/*`
2. **Easy to Remember** - Predictable URL patterns
3. **Professional Appearance** - Clean, organized structure
4. **Centralized Management** - Single prefix for all authenticated areas
5. **Better Security** - Clear separation of concerns
6. **Scalable** - Easy to add new dashboard features

---

## 🎯 Usage Examples

```blade
{{-- Auto-redirect to user's dashboard --}}
<a href="{{ route('dashboard') }}">My Dashboard</a>

{{-- Profile (works for all users) --}}
<a href="{{ route('profile.edit') }}">Edit Profile</a>

{{-- Messages (works for all users) --}}
<a href="{{ route('messages.index') }}">My Messages</a>

{{-- Super Admin SMS Test --}}
<a href="{{ route('superadmin.sms.test') }}">SMS Test</a>

{{-- Admin Verifications --}}
<a href="{{ route('admin.verifications.index') }}">Vendor Verifications</a>

{{-- Vendor Services --}}
<a href="{{ route('vendor.services.index') }}">My Services</a>
```

---

**All dashboard functionality is now unified under `/dashboard/*`** 🎉

