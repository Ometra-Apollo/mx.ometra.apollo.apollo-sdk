# DirectoryTree compartido

Carpeta autocontenida publicable desde `ometra/apollo-sdk`.

```bash
php artisan vendor:publish --tag=apollo-directory-tree
```

## Dependencias esperadas

- `react`
- Tailwind

## Punto de entrada

```ts
import { DirectoryTree } from '@/shared/DirectoryTree';
```

Si no pasas `directories`, el componente carga por default desde:

```txt
/_apollo/proteus/directories
```

Esa ruta esta registrada desde `src/routes/web.php` y usa `web` + `caronte.session` para que `Apollo::proteus()->directories()->index()` reciba el token de usuario desde la sesion. No debe vivir bajo `api/*`, porque Caronte trata esas rutas como Bearer-token API.

## Seleccion de archivos

```tsx
<DirectoryTree
    treeOnly
    selectableItemType="media"
    directoriesQuery={{ type: 'image' }}
    onSelect={(id, name, recursive) => {
        // id y name pertenecen al archivo seleccionado
        // recursive indica si se deben incluir subcarpetas
    }}
/>
```

Por defecto `selectableItemType` es `"directory"`.
El fetch default manda `only_tree=true` para directorios y `only_tree=false` para media.

Usa `showRecursiveToggle={false}` para ocultar "Incluir subcarpetas".

## Boton opcional para crear carpeta

Cuando `treeOnly` es `false`, puedes mostrar un boton alineado con "Seleccionar carpeta" usando `onCreateFolder`.

Por default, al confirmar con el check, el componente hace `POST` a `/_apollo/proteus/directories` con `{ name, parent_id }`, y la ruta web del SDK delega a `Apollo::proteus()->directories()->store(...)`.

```tsx
<DirectoryTree
    createDirectoryEndpoint="/_apollo/proteus/directories"
    onCreateFolder={({ parentId, name }) => {
        // override opcional si quieres manejar la persistencia fuera del componente
    }}
    createFolderLabel="Crear carpeta"
/>
```
