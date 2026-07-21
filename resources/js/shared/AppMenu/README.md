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

- `appMenu.config.tsx`: orden predeterminado, iconos, colores y acciones de apps conocidas
- `appMenu.utils.ts`: resolución de app actual y armado de URLs por ambiente

Las aplicaciones y sus URLs se obtienen de `/_apollo/suite/applications/user`.
El componente admite los formatos `{ applications: [...] }` y
`{ data: { applications: [...] } }`; una app sin metadata local usa el icono y
la acción fallback.
