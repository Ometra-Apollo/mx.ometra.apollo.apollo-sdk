import { ApolloIcon, ArrowRightLeftIcon, FlareIcon, HomeIcon, IgnisIcon, NewWindowIcon, ProteusIcon, SonusIcon as PulseIcon } from '@/Components';
import type { ComponentType, ReactNode, SVGProps } from 'react';

export const APP_NAMES = ['Proteus', 'Flare', 'Ignis', 'Pulse', 'Apollo'] as const;

export type AppName = (typeof APP_NAMES)[number];

export type AppIcon = ComponentType<SVGProps<SVGSVGElement>>;

export type AppAction = 'switch' | 'newWindow' | 'home';

export type AppMeta = {
    icon: AppIcon;
    a11y: string;
    hoverKey: 'proteus' | 'flare' | 'ignis' | 'pulse' | 'apollo';
    iconColor: string;
    actions: AppAction[];
    url: string;
};

export const ACTION_ICON: Record<AppAction, ReactNode> = {
    switch: <ArrowRightLeftIcon width={24} height={24} />,
    newWindow: <NewWindowIcon width={24} height={24} />,
    home: <HomeIcon width={24} height={24} />,
};

export const APPS_ORDER: AppName[] = [...APP_NAMES];

export const APP_META: Record<AppName, AppMeta> = {
    Proteus: {
        icon: ProteusIcon,
        a11y: 'PROTEUS',
        hoverKey: 'proteus',
        iconColor: 'text-foreground',
        actions: ['newWindow'],
        url: 'proteus.apollo.ometra.mx',
    },
    Flare: {
        icon: FlareIcon,
        a11y: 'FLARE',
        hoverKey: 'flare',
        iconColor: 'text-[#540863]',
        actions: ['newWindow'],
        url: 'flare.apollo.ometra.mx',
    },
    Ignis: {
        icon: IgnisIcon,
        a11y: 'IGNIS',
        hoverKey: 'ignis',
        iconColor: 'text-[#B2620C]',
        actions: ['newWindow'],
        url: 'ignis.apollo.ometra.mx',
    },
    Pulse: {
        icon: PulseIcon,
        a11y: 'PULSE',
        hoverKey: 'pulse',
        iconColor: 'text-[#4A7E8C]',
        actions: ['newWindow'],
        url: 'pulse.apollo.ometra.mx',
    },
    Apollo: {
        icon: ApolloIcon,
        a11y: 'APOLLO',
        hoverKey: 'apollo',
        iconColor: 'text-black',
        actions: ['home'],
        url: 'apollo.ometra.mx',
    },
};

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
