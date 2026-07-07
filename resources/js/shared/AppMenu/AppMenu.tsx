import { usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import { DropdownContent, DropdownOption, DropdownRoot } from '@/Components';

import { AppMenuActions } from './AppMenuActions';
import { AppMenuRow } from './AppMenuRow';
import { AppMenuTrigger } from './AppMenuTrigger';
import { APP_META, APPS_ORDER, type AppName } from './appMenu.config';
import {
    getAppFromHostname,
    getAppFromUrlSegment,
    getFirstSegment,
} from './appMenu.utils';

const HOVER_CLASS: Record<(typeof APP_META)[AppName]['hoverKey'], string> = {
    proteus: 'hover:bg-proteus-hover',
    flare: 'hover:bg-flare-hover',
    ignis: 'hover:bg-ignis-hover',
    pulse: 'hover:bg-pulse-hover',
    apollo: 'hover:bg-apollo-hover',
};

function makeAppOption(name: AppName): DropdownOption<AppName> {
    const meta = APP_META[name];

    return {
        value: name,
        a11yText: meta.a11y,
        render: <AppMenuRow Icon={meta.icon} name={name} />,
        renderSelected: (
            <AppMenuRow
                Icon={meta.icon}
                name={name}
                selected
                clickable={false}
            />
        ),
        itemClassName: HOVER_CLASS[meta.hoverKey],
        selectable: false,
        actions: (
            <AppMenuActions app={name} color={meta.iconColor} url={meta.url} />
        ),
    };
}

export default function AppMenu() {
    const { url } = usePage();
    const [app, setApp] = useState<AppName | null>(() => {
        const appFromHostname = getAppFromHostname(window.location.hostname);
        if (appFromHostname) return appFromHostname;

        const segment = getFirstSegment(url);
        return getAppFromUrlSegment(segment);
    });

    const options = useMemo(() => APPS_ORDER.map(makeAppOption), []);

    return (
        <div className="relative">
            <DropdownRoot options={options} value={app} onValueChange={setApp}>
                <AppMenuTrigger app={app} />
                <DropdownContent />
            </DropdownRoot>
        </div>
    );
}
