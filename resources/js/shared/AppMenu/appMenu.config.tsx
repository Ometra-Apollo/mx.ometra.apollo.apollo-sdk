import { ArrowRightLeftIcon, HomeIcon, NewWindowIcon } from '@/Components';
import type { ComponentType, ReactNode, SVGProps } from 'react';
import { AerisIcon, ApolloIcon, FlareIcon, IgnisIcon, ProteusIcon, PulseIcon } from './appMenu.icons';

export type AppName = string;

export type AppIcon = ComponentType<SVGProps<SVGSVGElement>>;

export type AppAction = 'switch' | 'newWindow' | 'home';

export type SuiteApplication = {
    cn: string;
    name: AppName;
    url: string;
};

export type AppMeta = {
    icon: AppIcon;
    a11y: string;
    hoverKey: 'proteus' | 'flare' | 'ignis' | 'pulse' | 'apollo';
    iconColor: string;
    actions: AppAction[];
};

function FallbackAppIcon(props: SVGProps<SVGSVGElement>) {
    return (
        <svg viewBox="0 0 44 44" fill="none" xmlns="http://www.w3.org/2000/svg" {...props}>
            <rect x="6" y="6" width="32" height="32" rx="16" fill="currentColor" opacity="0.15" />
            <path d="M16 16h5v5h-5zm7 0h5v5h-5zm-7 7h5v5h-5zm7 0h5v5h-5z" fill="currentColor" />
        </svg>
    );
}

export const ACTION_ICON: Record<AppAction, ReactNode> = {
    switch: <ArrowRightLeftIcon width={24} height={24} />,
    newWindow: <NewWindowIcon width={24} height={24} />,
    home: <HomeIcon width={24} height={24} />,
};

export const DEFAULT_APPS_ORDER = [
    'Aeris',
    'Proteus',
    'Flare',
    'Ignis',
    'Pulse',
    'Apollo',
] as const;

const DEFAULT_APP_META: AppMeta = {
    icon: FallbackAppIcon,
    a11y: 'APP',
    hoverKey: 'apollo',
    iconColor: 'text-black',
    actions: ['newWindow'],
};

export const APP_META: Record<string, AppMeta> = {
    Aeris: {
        icon: AerisIcon,
        a11y: 'AERIS',
        hoverKey: 'apollo',
        iconColor: 'text-[#826717]',
        actions: ['newWindow'],
    },
    Proteus: {
        icon: ProteusIcon,
        a11y: 'PROTEUS',
        hoverKey: 'proteus',
        iconColor: 'text-foreground',
        actions: ['newWindow'],
    },
    Flare: {
        icon: FlareIcon,
        a11y: 'FLARE',
        hoverKey: 'flare',
        iconColor: 'text-[#540863]',
        actions: ['newWindow'],
    },
    Ignis: {
        icon: IgnisIcon,
        a11y: 'IGNIS',
        hoverKey: 'ignis',
        iconColor: 'text-[#B2620C]',
        actions: ['newWindow'],
    },
    Pulse: {
        icon: PulseIcon,
        a11y: 'PULSE',
        hoverKey: 'pulse',
        iconColor: 'text-[#4A7E8C]',
        actions: ['newWindow'],
    },
    Apollo: {
        icon: ApolloIcon,
        a11y: 'APOLLO',
        hoverKey: 'apollo',
        iconColor: 'text-black',
        actions: ['home'],
    },
};

export function getAppMeta(appName: AppName): AppMeta {
    return APP_META[appName] ?? {
        ...DEFAULT_APP_META,
        a11y: appName.toUpperCase(),
    };
}
