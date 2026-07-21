import { DEFAULT_APPS_ORDER, type AppName, type SuiteApplication } from './appMenu.config';

export const APP_MENU_USER_APPLICATIONS_ENDPOINT = '/_apollo/suite/applications/user';

const APP_NAME_BY_SEGMENT = new Map(
    DEFAULT_APPS_ORDER.map((app) => [app.toLowerCase(), app]),
);

export function parseAppName(value: string | null | undefined): AppName | null {
    if (!value) return null;

    return APP_NAME_BY_SEGMENT.get(value.toLowerCase()) ?? null;
}

export function getAppFromHostname(
    hostname: string | null | undefined,
): AppName | null {
    if (!hostname) return null;

    const segments = hostname.toLowerCase().split('.').filter(Boolean);

    for (const segment of segments) {
        const app = parseAppName(segment);
        if (app) return app;
    }

    return null;
}

export function getAppFromUrlSegment(
    segment: string | null | undefined,
): AppName | null {
    if (!segment) return 'Apollo';

    return parseAppName(segment);
}

export function buildAppUrl(appUrl: string) {
    const hostname = window.location.hostname;
    let prefix = '';

    if (hostname.startsWith('local.')) {
        prefix = 'local.';
    } else if (hostname.startsWith('dev.')) {
        prefix = 'dev.';
    } else if (hostname.startsWith('staging.')) {
        prefix = 'staging.';
    }

    return `https://${prefix}${appUrl}`;
}

export function getFirstSegment(url: string) {
    return url.split('?')[0].split('/').filter(Boolean)[0] ?? null;
}

export function sortApplications(applications: SuiteApplication[]) {
    const order = new Map<AppName, number>(DEFAULT_APPS_ORDER.map((app, index) => [app, index]));

    return [...applications].sort((left, right) => {
        const leftOrder = order.get(left.name) ?? Number.MAX_SAFE_INTEGER;
        const rightOrder = order.get(right.name) ?? Number.MAX_SAFE_INTEGER;

        if (leftOrder !== rightOrder) {
            return leftOrder - rightOrder;
        }

        return left.name.localeCompare(right.name);
    });
}

export function findApplicationByName(
    applications: SuiteApplication[],
    appName: AppName | null,
): SuiteApplication | null {
    if (!appName) return null;

    return applications.find((application) => application.name === appName) ?? null;
}

export function normalizeApplicationsResponse(payload: unknown): SuiteApplication[] {
    if (!payload || typeof payload !== 'object') {
        return [];
    }

    const directApplications = (payload as { applications?: unknown }).applications;
    const nestedApplications = (payload as { data?: { applications?: unknown } }).data?.applications;
    const applications = Array.isArray(directApplications)
        ? directApplications
        : Array.isArray(nestedApplications)
            ? nestedApplications
            : [];

    return applications.flatMap((application) => {
        if (!application || typeof application !== 'object') return [];

        const { cn, name, url } = application as Partial<SuiteApplication>;

        if (typeof cn !== 'string' || typeof name !== 'string' || typeof url !== 'string') {
            return [];
        }

        return [{ cn, name: name.trim(), url }];
    });
}
