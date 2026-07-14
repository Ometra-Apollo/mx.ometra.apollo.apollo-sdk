import { Link } from '@inertiajs/react';

import { cn } from '@/lib/utils';

import { APP_META, type AppIcon, type AppName } from './appMenu.config';
import { buildAppUrl } from './appMenu.utils';

type AppMenuRowProps = {
    Icon: AppIcon;
    name: AppName;
    selected?: boolean;
    clickable?: boolean;
};

export function AppMenuRow({
    Icon,
    name,
    selected,
    clickable = true,
}: AppMenuRowProps) {
    const url = buildAppUrl(APP_META[name].url);

    const handleClick = (e: React.MouseEvent) => {
        if (!clickable) return;

        e.preventDefault();
        window.location.href = url;
    };

    const content = (
        <>
            <Icon
                aria-hidden="true"
                width={selected ? 44 : 33}
                height={selected ? 44 : 33}
                className={cn(
                    'shrink-0 transition-transform group-hover:scale-105',
                    selected ? 'absolute -top-2.5 left-0.5 mr-1.5' : '',
                )}
            />

            <span
                className={cn(
                    'transition-all',
                    !selected && 'group-hover:underline',
                    selected ? 'ml-9 text-2xl font-medium' : 'text-lg',
                )}
            >
                {name}
            </span>
        </>
    );

    const className = 'ml-2 flex w-full items-center gap-2 outline-none group';

    if (!clickable) {
        return <div className={className}>{content}</div>;
    }

    return (
        <Link onClick={handleClick} href={url} className={className}>
            {content}
        </Link>
    );
}
