import { cn } from '@/lib/utils';
import { ChevronDown } from 'lucide-react';
import type { RefObject } from 'react';

import { useDropdownCtx } from '@/Components/ui/Dropdown/context';

import { APP_META, type AppName } from './appMenu.config';
import { buildAppUrl } from './appMenu.utils';

type AppMenuTriggerProps = {
    app: AppName | null;
    className?: string;
};

export function AppMenuTrigger({ app, className = '' }: AppMenuTriggerProps) {
    const { open, setOpen, disabled, triggerRef } = useDropdownCtx<AppName>();

    if (!app) return null;

    const meta = APP_META[app];
    const Icon = meta.icon;

    const handleHomeClick = () => {
        if (disabled) return;

        window.location.href = buildAppUrl(meta.url);
    };

    return (
        <div className={cn('bg-blush-white flex h-10 max-w-49 min-w-49 items-center rounded-full', disabled ? 'opacity-60' : '', className)}>
            <button
                type="button"
                onClick={handleHomeClick}
                disabled={disabled}
                aria-label={`Ir al inicio de ${app}`}
                className={cn('-mt-3 ml-2 flex shrink-0 items-center justify-center', disabled ? 'cursor-not-allowed' : 'cursor-pointer')}
            >
                <Icon aria-hidden="true" width={33} height={44} className="shrink-0 transition-transform hover:scale-105" />
            </button>

            <button
                ref={triggerRef as RefObject<HTMLButtonElement>}
                type="button"
                disabled={disabled}
                aria-haspopup="listbox"
                aria-expanded={open}
                onClick={() => !disabled && setOpen((value) => !value)}
                className={cn(
                    'flex min-w-0 flex-1 items-center justify-between gap-2 py-2 pr-3 pl-2',
                    disabled ? 'cursor-not-allowed' : 'cursor-pointer',
                )}
            >
                <span className="truncate text-left text-2xl">{app}</span>

                <ChevronDown
                    width={16}
                    height={16}
                    className={cn('shrink-0 text-[#1f1f1f] transition-transform', open && 'rotate-180')}
                    aria-hidden="true"
                />
            </button>
        </div>
    );
}
