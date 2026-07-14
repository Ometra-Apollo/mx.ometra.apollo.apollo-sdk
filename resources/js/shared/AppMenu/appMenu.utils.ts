import { APP_NAMES, type AppName } from './appMenu.config';

export function parseAppName(value: string | null | undefined): AppName | null {
    if (!value) return null;

    const normalized = value.toLowerCase();

    return APP_NAMES.find((app) => app.toLowerCase() === normalized) ?? null;
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

    if (hostname.startsWith('dev.')) {
        prefix = 'dev.';
    } else if (hostname.startsWith('staging.')) {
        prefix = 'staging.';
    }

    return `https://${prefix}${appUrl}`;
}

export function getFirstSegment(url: string) {
    return url.split('?')[0].split('/').filter(Boolean)[0] ?? null;
}
