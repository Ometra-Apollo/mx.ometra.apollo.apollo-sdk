import type { SVGProps } from 'react';

type IconProps = SVGProps<SVGSVGElement>;

export function ChevronDown(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="m6 9 6 6 6-6" />
        </svg>
    );
}

export function ChevronRight(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="m9 18 6-6-6-6" />
        </svg>
    );
}

export function FileIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="M6 22a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h8a2.4 2.4 0 0 1 1.704.706l3.588 3.588A2.4 2.4 0 0 1 20 8v12a2 2 0 0 1-2 2z" />
            <path d="M14 2v5a1 1 0 0 0 1 1h5" />
        </svg>
    );
}

export function FolderUp(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z" />
            <path d="M12 10v6" />
            <path d="m9 13 3-3 3 3" />
        </svg>
    );
}

export function LoaderCircle(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="M21 12a9 9 0 1 1-6.219-8.56" />
        </svg>
    );
}

export function X(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="M18 6 6 18" />
            <path d="m6 6 12 12" />
        </svg>
    );
}

export function Check(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={2} strokeLinecap="round" strokeLinejoin="round" {...props}>
            <path d="M20 6 9 17l-5-5" />
        </svg>
    );
}

export function ImageFileIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 24 24" fill="currentColor">
            <path d="M5 21C4.45 21 3.97933 20.8043 3.588 20.413C3.19667 20.0217 3.00067 19.5507 3 19V5C3 4.45 3.196 3.97933 3.588 3.588C3.98 3.19667 4.45067 3.00067 5 3H19C19.55 3 20.021 3.196 20.413 3.588C20.805 3.98 21.0007 4.45067 21 5V19C21 19.55 20.8043 20.021 20.413 20.413C20.0217 20.805 19.5507 21.0007 19 21H5ZM6 17H18L14.25 12L11.25 16L9 13L6 17Z" />
        </svg>
    );
}

export function VideoFileIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 24 24" fill="currentColor">
            <path fillRule="evenodd" clipRule="evenodd" d="M4 3C3.46957 3 2.96086 3.21071 2.58579 3.58579C2.21071 3.96086 2 4.46957 2 5V19C2 19.5304 2.21071 20.0391 2.58579 20.4142C2.96086 20.7893 3.46957 21 4 21H20C20.5304 21 21.0391 20.7893 21.4142 20.4142C21.7893 20.0391 22 19.5304 22 19V5C22 4.46957 21.7893 3.96086 21.4142 3.58579C21.0391 3.21071 20.5304 3 20 3H4ZM8.625 8.63C8.64719 8.43882 8.71376 8.25547 8.81939 8.09458C8.92502 7.93369 9.0668 7.79972 9.2334 7.70335C9.4 7.60698 9.58682 7.55089 9.77896 7.53954C9.97109 7.5282 10.1632 7.56191 10.34 7.638C10.844 7.854 11.908 8.34 13.256 9.118C14.2034 9.65944 15.1182 10.2558 15.996 10.904C16.1503 11.0188 16.2757 11.1682 16.362 11.34C16.4484 11.5119 16.4933 11.7016 16.4933 11.894C16.4933 12.0864 16.4484 12.2761 16.362 12.448C16.2757 12.6198 16.1503 12.7692 15.996 12.884C15.1182 13.5315 14.2033 14.1272 13.256 14.668C12.3137 15.2184 11.34 15.7132 10.34 16.15C10.1632 16.2263 9.97106 16.2602 9.77885 16.2489C9.58664 16.2377 9.39973 16.1816 9.23306 16.0852C9.0664 15.9888 8.9246 15.8548 8.81902 15.6938C8.71344 15.5328 8.64699 15.3493 8.625 15.158C8.50501 14.0742 8.44625 12.9844 8.449 11.894C8.449 10.343 8.561 9.175 8.625 8.63Z" />
        </svg>
    );
}

export function AudioFileIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 24 24" fill="currentColor">
            <path d="M6 22C5.45 22 4.97933 21.8043 4.588 21.413C4.19667 21.0217 4.00067 20.5507 4 20V4C4 3.45 4.196 2.97933 4.588 2.588C4.98 2.19667 5.45067 2.00067 6 2H13.175C13.4417 2 13.696 2.05 13.938 2.15C14.18 2.25 14.3923 2.39167 14.575 2.575L19.425 7.425C19.6083 7.60833 19.75 7.821 19.85 8.063C19.95 8.305 20 8.559 20 8.825V20C20 20.55 19.8043 21.021 19.413 21.413C19.0217 21.805 18.5507 22.0007 18 22H6ZM13 8C13 8.28333 13.096 8.521 13.288 8.713C13.48 8.905 13.7173 9.00067 14 9H18L13 4V8ZM10.75 19C11.3833 19 11.9167 18.7833 12.35 18.35C12.7833 17.9167 13 17.3833 13 16.75V13H15C15.2833 13 15.521 12.904 15.713 12.712C15.905 12.52 16.0007 12.2827 16 12C15.9993 11.7173 15.9033 11.48 15.712 11.288C15.5207 11.096 15.2833 11 15 11H13C12.7167 11 12.4793 11.096 12.288 11.288C12.0967 11.48 12.0007 11.7173 12 12V14.875C11.8167 14.7417 11.621 14.6457 11.413 14.587C11.205 14.5283 10.984 14.4993 10.75 14.5C10.1167 14.5 9.58333 14.7167 9.15 15.15C8.71667 15.5833 8.5 16.1167 8.5 16.75C8.5 17.3833 8.71667 17.9167 9.15 18.35C9.58333 18.7833 10.1167 19 10.75 19Z" />
        </svg>
    );
}

export function FolderSolidIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 24 24" fill="none">
            <path d="M10 4H4C2.89 4 2 4.89 2 6V18C2 18.5304 2.21071 19.0391 2.58579 19.4142C2.96086 19.7893 3.46957 20 4 20H20C20.5304 20 21.0391 19.7893 21.4142 19.4142C21.7893 19.0391 22 18.5304 22 18V8C22 7.46957 21.7893 6.96086 21.4142 6.58579C21.0391 6.21071 20.5304 6 20 6H12L10 4Z" fill="currentColor" />
        </svg>
    );
}

export function DiscIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 24 24" fill="none">
            <rect width="24" height="24" rx="2" fill="currentColor" opacity="0.18" />
            <circle cx="12" cy="12" r="7" stroke="currentColor" strokeWidth="2" />
            <circle cx="12" cy="12" r="2" fill="currentColor" />
        </svg>
    );
}

export function PersonalFolderIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 20 20" fill="none">
            <path d="M10 3.333C10.884 3.333 11.732 3.685 12.357 4.31C12.982 4.935 13.333 5.783 13.333 6.667C13.333 7.551 12.982 8.399 12.357 9.024C11.732 9.649 10.884 10 10 10C9.116 10 8.268 9.649 7.643 9.024C7.018 8.399 6.667 7.551 6.667 6.667C6.667 5.783 7.018 4.935 7.643 4.31C8.268 3.685 9.116 3.333 10 3.333ZM10 11.667C13.683 11.667 16.667 13.158 16.667 15V16.667H3.333V15C3.333 13.158 6.317 11.667 10 11.667Z" fill="currentColor" />
        </svg>
    );
}

export function SharedFolderIcon(props: IconProps) {
    return (
        <svg xmlns="http://www.w3.org/2000/svg" {...props} viewBox="0 0 20 20" fill="none">
            <path d="M10 3.5A2.914 2.914 0 1 1 10 9.328A2.914 2.914 0 0 1 10 3.5ZM3.724 5.517A2.017 2.017 0 1 1 3.724 9.552A2.017 2.017 0 0 1 3.724 5.517ZM1.035 14.707A3.586 3.586 0 0 1 5.655 11.272A5.813 5.813 0 0 0 4.173 15.155V15.603C4.173 15.923 4.24 16.225 4.36 16.5H1.931A.897.897 0 0 1 1.035 15.603V14.707ZM15.64 16.5C15.761 16.225 15.828 15.923 15.828 15.603V15.155C15.828 13.665 15.267 12.303 14.346 11.272A3.586 3.586 0 0 1 18.966 14.707V15.603A.897.897 0 0 1 18.069 16.5H15.64ZM14.259 7.534A2.017 2.017 0 1 1 16.276 9.552A2.017 2.017 0 0 1 14.259 7.534ZM5.517 15.155A4.483 4.483 0 0 1 10 10.672A4.483 4.483 0 0 1 14.483 15.155V15.603A.897.897 0 0 1 13.586 16.5H6.414A.897.897 0 0 1 5.517 15.603V15.155Z" fill="currentColor" />
        </svg>
    );
}
