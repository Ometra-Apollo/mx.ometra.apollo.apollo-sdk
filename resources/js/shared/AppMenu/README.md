# AppMenu compartido

Carpeta autocontenida publicable desde `ometra/apollo-sdk`.

```bash
php artisan vendor:publish --tag=apollo-app-menu
```

## Dependencias esperadas

- Alias `@/Components`
- Alias `@/lib/utils`
- `@inertiajs/react`
- Clases Tailwind de hover por app:
  - `hover:bg-proteus-hover`
  - `hover:bg-flare-hover`
  - `hover:bg-ignis-hover`
  - `hover:bg-pulse-hover`
  - `hover:bg-apollo-hover`

## Punto de entrada

```ts
import { AppMenu } from '@/shared/AppMenu';
```

## Archivos configurables

- `appMenu.config.tsx`: apps, iconos, colores, acciones y URLs
- `appMenu.utils.ts`: resolución de app actual y armado de URLs por ambiente
