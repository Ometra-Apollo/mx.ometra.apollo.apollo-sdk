# UI Components

This guide documents the shared UI components published by Apollo SDK.

## Publish In Host App

Publish only AppMenu:

```bash
php artisan vendor:publish --tag=apollo-app-menu
```

Publish only DirectoryTree:

```bash
php artisan vendor:publish --tag=apollo-directory-tree
```

Publish both together with config and error pages:

```bash
php artisan vendor:publish --tag=apollo
```

## AppMenu

### Purpose

`AppMenu` renders the suite app switcher (Proteus, Flare, Ignis, Pulse, Apollo) and builds environment-aware links (`dev.`, `staging.`, prod).

### Import

```ts
import { AppMenu } from '@/shared/AppMenu';
```

### Expected Dependencies

- `@inertiajs/react` (uses `usePage()`)
- `@/Components` alias
- `@/lib/utils` alias
- Tailwind hover classes in your host app:
  - `hover:bg-proteus-hover`
  - `hover:bg-flare-hover`
  - `hover:bg-ignis-hover`
  - `hover:bg-pulse-hover`
  - `hover:bg-apollo-hover`

### Behavior

1. Detects current app by hostname (for example: `ignis.apollo.ometra.mx`).
2. If hostname detection fails, it uses the first segment of the Inertia URL.
3. Clicking the current app icon navigates to that app home.
4. App actions are defined in `appMenu.config.tsx`.

### Files You Can Customize

- `resources/js/shared/AppMenu/appMenu.config.tsx`
  - App order (`APPS_ORDER`)
  - Icons, colors, a11y labels, and actions
  - Target host per app (`url`)
- `resources/js/shared/AppMenu/appMenu.utils.ts`
  - App resolution by hostname/URL segment
  - Environment-aware URL builder

### Basic Example

```tsx
import { AppMenu } from '@/shared/AppMenu';

export function Header() {
    return (
        <header className="flex items-center justify-between p-4">
            <h1 className="text-xl font-semibold">Dashboard</h1>
            <AppMenu />
        </header>
    );
}
```

## DirectoryTree

### Purpose

`DirectoryTree` lets users select a folder or media item, browse a recursive tree, and optionally create folders.

### Import

```ts
import { DirectoryTree } from '@/shared/DirectoryTree';
```

### Data Source

You can use two modes:

1. Controlled via props: pass `directories` and no fetch is made.
2. Auto-load: if `directories` is not provided, it fetches from `directoriesEndpoint`.

Default endpoint:

```txt
/_apollo/proteus/directories
```

This route is provided by the SDK and uses `web` + `caronte.session` middleware.

### Main Props

- `directories?: DirectoryTreeDirectory`
- `directoriesEndpoint?: string` (default `/_apollo/proteus/directories`)
- `directoriesQuery?: Record<string, string | number | boolean | null | undefined>`
- `onSelect?: (id: string, name: string, recursive: boolean) => void`
- `selectedId?: string | null`
- `selectedName?: string`
- `recursive?: boolean`
- `onRecursiveChange?: (value: boolean) => void`
- `showRecursiveToggle?: boolean` (default `true`)
- `compactSelectedView?: boolean` (default `false`)
- `onExplorerOpenChange?: (open: boolean) => void`
- `treeOnly?: boolean` (default `false`)
- `selectableRootId?: string | null`
- `selectableItemType?: 'directory' | 'media'` (default `'directory'`)
- `isLoading?: boolean` (default `false`)
- `translateName?: (name: string) => string` (default identity)
- `createDirectoryEndpoint?: string` (default `/_apollo/proteus/directories`)
- `onCreateFolder?: ({ parentId, name }) => void | Promise<void>`
- `createFolderLabel?: string` (default `Create folder`)

### Selection Flows

#### Folder Selection (default)

1. Open explorer.
2. Preview folder.
3. Confirm with `Select` button.
4. Triggers `onSelect(id, name, recursive)`.

#### Media Selection

Set `selectableItemType="media"`.

- Clicking a media item selects it and triggers `onSelect`.
- In `treeOnly` mode, selection is immediate (no confirm step).

### Restricting To A Subtree

Use `selectableRootId` to allow selection only inside a specific root node.

### Create Folder

Two strategies are supported:

1. Default: automatic POST to `createDirectoryEndpoint` with `{ parent_id, name }`.
2. Custom: provide `onCreateFolder` and handle persistence outside the component.

The component tries to resolve CSRF token from:

1. `<meta name="csrf-token" />`
2. `XSRF-TOKEN` cookie

### Example: Folder Picker

```tsx
import { DirectoryTree } from '@/shared/DirectoryTree';

export function FolderPicker() {
    return (
        <DirectoryTree
            onSelect={(id, name, recursive) => {
                console.log({ id, name, recursive });
            }}
        />
    );
}
```

### Example: Media Picker

```tsx
import { DirectoryTree } from '@/shared/DirectoryTree';

export function MediaPicker() {
    return (
        <DirectoryTree
            treeOnly
            selectableItemType="media"
            directoriesQuery={{ type: 'image' }}
            onSelect={(id, name) => {
                console.log('selected media', id, name);
            }}
        />
    );
}
```

### Example: Custom Folder Creation

```tsx
import { DirectoryTree } from '@/shared/DirectoryTree';

export function FolderPickerCustomCreate() {
    return (
        <DirectoryTree
            onCreateFolder={async ({ parentId, name }) => {
                await fetch('/my-endpoint/directories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({
                        parent_id: parentId,
                        name,
                    }),
                });
            }}
        />
    );
}
```

## Integration Recommendations

1. Publish components using their dedicated tags and version those changes in your host app.
2. Keep `@/Components` and `@/lib/utils` aliases compatible in your host app.
3. If you customize routes, align `directoriesEndpoint` and `createDirectoryEndpoint` with your backend.
4. Add E2E tests for folder/media selection and folder creation.
