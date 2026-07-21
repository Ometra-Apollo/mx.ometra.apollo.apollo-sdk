import { CircleButton, Link } from '@/Components';
import { cn } from '@/lib/utils';

import { ACTION_ICON, getAppMeta, type SuiteApplication } from './appMenu.config';
import { buildAppUrl } from './appMenu.utils';

type AppMenuActionsProps = {
    app: SuiteApplication;
};

export function AppMenuActions({ app }: AppMenuActionsProps) {
    const meta = getAppMeta(app.name);

    if (meta.actions.length === 0) return null;

    return (
        <div className="pointer-events-none ml-auto flex items-center gap-1 opacity-0 transition-opacity duration-150 group-hover:pointer-events-auto group-hover:opacity-100">
            {meta.actions.map((action) => (
                <Link
                    href={buildAppUrl(app.url)}
                    variant="text"
                    className="flex items-center"
                    target={action === 'newWindow' ? '_blank' : undefined}
                    rel={
                        action === 'newWindow'
                            ? 'noopener noreferrer'
                            : undefined
                    }
                    key={action}
                >
                    <CircleButton className="h-full w-6 bg-transparent hover:bg-white hover:drop-shadow-[0_0_10px_rgba(0,0,0,0.15)]">
                        <span className={cn(meta.iconColor, 'cursor-pointer')}>
                            {ACTION_ICON[action]}
                        </span>
                    </CircleButton>
                </Link>
            ))}
        </div>
    );
}
