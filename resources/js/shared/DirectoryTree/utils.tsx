import {
    AudioFileIcon,
    DiscIcon,
    FileIcon,
    FolderSolidIcon,
    ImageFileIcon,
    PersonalFolderIcon,
    SharedFolderIcon,
    VideoFileIcon,
} from "./Icons";
import type {
    DirectoryTreeDirectory,
    DirectoryTreeMedia,
    DirectoryTreeProps,
} from "./types";

export function getDirectoryId(directory: DirectoryTreeDirectory): string {
    return directory.id_directory;
}

export function getDirectoryChildren(
    directory: DirectoryTreeDirectory,
): DirectoryTreeDirectory[] {
    if ((directory.children?.length ?? 0) > 0) {
        return directory.children ?? [];
    }

    return directory.children_recursive ?? [];
}

export function findDirectoryPath(
    directory: DirectoryTreeDirectory,
    targetId: string,
    path: string[] = [],
): string[] | null {
    const directoryId = getDirectoryId(directory);

    if (directoryId === targetId) {
        return [...path, directoryId];
    }

    for (const child of directory.children ?? []) {
        const result = findDirectoryPath(child, targetId, [...path, directoryId]);
        if (result) return result;
    }

    for (const child of directory.children_recursive ?? []) {
        const result = findDirectoryPath(child, targetId, [...path, directoryId]);
        if (result) return result;
    }

    return null;
}

export function findDirectoryName(
    directory: DirectoryTreeDirectory,
    targetId: string,
): string | null {
    if (getDirectoryId(directory) === targetId) return directory.name;

    for (const child of directory.children ?? []) {
        const result = findDirectoryName(child, targetId);
        if (result) return result;
    }

    for (const child of directory.children_recursive ?? []) {
        const result = findDirectoryName(child, targetId);
        if (result) return result;
    }

    return null;
}

export function findDirectoryPathNames(
    directory: DirectoryTreeDirectory,
    targetId: string,
    translateName: (name: string) => string,
    path: string[] = [],
): string[] | null {
    const nextPath = [...path, translateName(directory.name)];

    if (getDirectoryId(directory) === targetId) {
        return nextPath;
    }

    for (const child of directory.children ?? []) {
        const result = findDirectoryPathNames(
            child,
            targetId,
            translateName,
            nextPath,
        );
        if (result) return result;
    }

    for (const child of directory.children_recursive ?? []) {
        const result = findDirectoryPathNames(
            child,
            targetId,
            translateName,
            nextPath,
        );
        if (result) return result;
    }

    return null;
}

export function findRootDirectories(
    directory: DirectoryTreeDirectory,
    roots: string[] = [],
): string[] {
    if (directory.id_parent === null || directory.id_parent === undefined) {
        roots.push(getDirectoryId(directory));
    }

    for (const child of directory.children ?? []) {
        findRootDirectories(child, roots);
    }

    for (const child of directory.children_recursive ?? []) {
        findRootDirectories(child, roots);
    }

    return roots;
}

export function getDirectoryIcon(directory: DirectoryTreeDirectory) {
    if (directory.is_virtual) {
        switch (directory.id_directory) {
            case "user_root":
                return <DiscIcon className="h-5 w-5" />;
            case "personal_root":
                return <PersonalFolderIcon className="h-5 w-5" />;
            case "shared_root":
                return <SharedFolderIcon className="h-5 w-5" />;
        }
    }

    return <FolderSolidIcon className="h-5 w-5 text-gray-700" />;
}

export function getMediaIcon(media: DirectoryTreeMedia) {
    switch (media.type?.toLowerCase()) {
        case "image":
            return <ImageFileIcon className="h-5 w-5 text-gray-700" />;
        case "video":
            return <VideoFileIcon className="h-5 w-5 text-gray-700" />;
        case "audio":
            return <AudioFileIcon className="h-5 w-5 text-gray-700" />;
        default:
            return <FileIcon className="h-5 w-5 text-gray-700" />;
    }
}

export function getMediaId(media: DirectoryTreeMedia): string {
    return media.id ?? media.id_media ?? "";
}

export function buildUrl(
    endpoint: string,
    query: DirectoryTreeProps["directoriesQuery"],
): string {
    const params = new URLSearchParams();

    Object.entries(query ?? {}).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== "") {
            params.set(key, String(value));
        }
    });

    const queryString = params.toString();
    return queryString ? `${endpoint}?${queryString}` : endpoint;
}

export function getDirectoryFromResponse(
    payload: any,
): DirectoryTreeDirectory | null {
    const directory =
        payload?.data?.directory ??
        payload?.directory ??
        payload?.data?.directories?.[0] ??
        payload?.directories?.[0] ??
        payload?.data ??
        null;

    if (!directory || Array.isArray(directory)) {
        return null;
    }

    return directory;
}

export function insertDirectoryChild(
    tree: DirectoryTreeDirectory,
    parentId: string,
    child: DirectoryTreeDirectory,
): DirectoryTreeDirectory {
    if (getDirectoryId(tree) === parentId) {
        const nextChildren =
            tree.children !== undefined
                ? [...(tree.children ?? []), child]
                : tree.children_recursive !== undefined
                  ? [...(tree.children_recursive ?? []), child]
                  : [child];

        if (tree.children !== undefined || tree.children_recursive === undefined) {
            return {
                ...tree,
                children: nextChildren,
            };
        }

        return {
            ...tree,
            children_recursive: nextChildren,
        };
    }

    return {
        ...tree,
        children: tree.children?.map((item) =>
            insertDirectoryChild(item, parentId, child),
        ),
        children_recursive: tree.children_recursive?.map((item) =>
            insertDirectoryChild(item, parentId, child),
        ),
    };
}
