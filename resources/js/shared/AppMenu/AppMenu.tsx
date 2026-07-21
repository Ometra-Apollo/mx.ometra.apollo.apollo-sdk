import { usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState, type SetStateAction } from 'react';

import { DropdownContent, DropdownOption, DropdownRoot } from '@/Components';

import { AppMenuActions } from './AppMenuActions';
import { AppMenuRow } from './AppMenuRow';
import { AppMenuTrigger } from './AppMenuTrigger';
import { getAppMeta, type AppName, type SuiteApplication } from './appMenu.config';
import {
    APP_MENU_USER_APPLICATIONS_ENDPOINT,
    buildAppUrl,
    findApplicationByName,
    getAppFromHostname,
    getAppFromUrlSegment,
    getFirstSegment,
    normalizeApplicationsResponse,
    sortApplications,
} from './appMenu.utils';

const HOVER_CLASS: Record<ReturnType<typeof getAppMeta>['hoverKey'], string> = {
    proteus: 'hover:bg-proteus-hover',
    flare: 'hover:bg-flare-hover',
    ignis: 'hover:bg-ignis-hover',
    pulse: 'hover:bg-pulse-hover',
    apollo: 'hover:bg-apollo-hover',
};

function makeAppOption(application: SuiteApplication): DropdownOption<AppName> {
    const meta = getAppMeta(application.name);

    return {
        value: application.name,
        a11yText: meta.a11y,
        render: <AppMenuRow app={application} />,
        renderSelected: (
            <AppMenuRow
                app={application}
                selected
                clickable={false}
            />
        ),
        itemClassName: HOVER_CLASS[meta.hoverKey],
        selectable: false,
        actions: <AppMenuActions app={application} />,
    };
}

export default function AppMenu() {
    const { url } = usePage();
    const [applications, setApplications] = useState<SuiteApplication[]>([]);

    const currentAppName = useMemo<AppName | null>(() => {
        const hostname = typeof window === 'undefined' ? null : window.location.hostname;
        const appFromHostname = getAppFromHostname(hostname);
        if (appFromHostname) return appFromHostname;

        const segment = getFirstSegment(url);
        return getAppFromUrlSegment(segment);
    }, [url]);

    useEffect(() => {
        const controller = new AbortController();

        fetch(APP_MENU_USER_APPLICATIONS_ENDPOINT, {
            signal: controller.signal,
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        })
            .then(async (response) => {
                if (!response.ok) {
                    throw new Error(`No se pudieron cargar las aplicaciones (${response.status}).`);
                }

                return response.json();
            })
            .then((payload) => {
                if (!controller.signal.aborted) {
                    setApplications(sortApplications(normalizeApplicationsResponse(payload)));
                }
            })
            .catch(() => {
                if (!controller.signal.aborted) {
                    setApplications([]);
                }
            });

        return () => controller.abort();
    }, []);

    const currentApp = useMemo<SuiteApplication | null>(() => {
        const resolvedApp = findApplicationByName(applications, currentAppName);

        if (resolvedApp) return resolvedApp;
        if (!currentAppName) return null;

        return {
            cn: currentAppName.toLowerCase(),
            name: currentAppName,
            url: `${currentAppName.toLowerCase()}.apollo.ometra.mx`,
        };
    }, [applications, currentAppName]);

    const options = useMemo(() => applications.map(makeAppOption), [applications]);

    const handleAppChange = (nextValue: SetStateAction<AppName | null>) => {
        const nextAppName =
            typeof nextValue === 'function' ? nextValue(currentAppName) : nextValue;
        const nextApp = findApplicationByName(applications, nextAppName);

        if (nextApp) {
            window.location.href = buildAppUrl(nextApp.url);
        }
    };

    return (
        <div className="relative">
            <DropdownRoot
                options={options}
                value={currentApp?.name ?? null}
                onValueChange={handleAppChange}
            >
                <AppMenuTrigger app={currentApp} />
                <DropdownContent />
            </DropdownRoot>
        </div>
    );
}
