# Breaking Changes

## v3.4.0

No breaking changes were introduced in this release.

## v3.3.0

No breaking changes were introduced in this release.

---

## Apollo SDK migration

This release completes the clean-cut migration to `ometra/apollo-sdk`. All consumers must use `config/apollo.php` and the modular API.

Required module URL variables:

- `PROTEUS_BASE_URL`
- `PULSE_BASE_URL`
- `FLARE_BASE_URL`
- `IGNIS_BASE_URL`

Example:

```php
use Ometra\Apollo\Sdk\Facades\Apollo;

$media = Apollo::proteus()->media()->index(['type' => 'image']);
```

---

### 1. Flat API removed

Root resource methods are gone. Use `Apollo::proteus()->{resource}()->{action}()`.

---

### 2. Legacy wrapper classes removed

The old root entrypoint, facade, service provider, config file, API wrapper classes, and exception class are removed. Direct usage of the underlying HTTP client is rarely needed; prefer the module resources.

---

### 3. Authentication remains Caronte-owned

Apollo does not define authentication config. Caronte remains configured in its own SDK and Apollo reuses its HTTP helpers for application, group, user, and tenant headers.

---

### 4. Tenant context

The SDK reads the tenant ID from `Equidna\BeeHive\Tenancy\TenantContext` at
request time. **You must set a tenant before calling any user-authenticated endpoint.**

```php
use Equidna\BeeHive\Tenancy\TenantContext;

app(TenantContext::class)->set('your-tenant-id');
```

Application-authenticated endpoints also include `X-Tenant-Id`
when a tenant is active; they work without one but will be tenant-scoped when
provided.

---

### 5. DB migrations removed

The service provider no longer publishes or runs any database migrations.
If your application depended on Proteus migrations (e.g., `proteus_apps` table),
remove those migrations from your project manually.

---

### 6. Config structure

`config/apollo.php` contains only module URL configuration. Re-publish the config after upgrading:

```bash
php artisan vendor:publish --tag=apollo-config --force
```

---

### 7. Partials (`DownloadMedia`, `PayloadFormatting`) removed

These were internal helpers. Multipart formatting and download handling are now
implemented inside Apollo module resources and are not part of the public API.

---

### 8. Removed guide files

`IMPLEMENTATION_GUIDE.md` and `PROTEUS_APPS_GUIDE.md` are deleted. Refer to:

- `README.md` for installation, configuration, and usage.
- `docs/api-contract.md` for the full endpoint contract.
