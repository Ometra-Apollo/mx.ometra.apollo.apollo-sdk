import { useEffect, useMemo, useState } from "react";
import {
    AudioFileIcon,
    ChevronDown,
    ChevronRight,
    DiscIcon,
    FileIcon,
    FolderSolidIcon,
    FolderUp,
    ImageFileIcon,
    LoaderCircle,
    PersonalFolderIcon,
    SharedFolderIcon,
    VideoFileIcon,
    X,
} from "./Icons";

export type DirectoryTreeMedia = {
    id: string;
    name: string;
    type?: string | null;
};

export type DirectoryTreeDirectory = {
    id: string;
    name: string;
    parent_id?: string | null;
    children?: DirectoryTreeDirectory[];
    children_recursive?: DirectoryTreeDirectory[];
    media?: DirectoryTreeMedia[];
    node_type?:
        | "user_root"
        | "personal_root"
        | "shared_root"
        | "normal"
        | string;
};

export type DirectoryTreeSelectableType = "directory" | "media";

type DirectoryTreeProps = {
    directories?: DirectoryTreeDirectory;
    directoriesEndpoint?: string;
    directoriesQuery?: Record<
        string,
        string | number | boolean | null | undefined
    >;
    onSelect?: (id: string, name: string, recursive: boolean) => void;
    selectedName?: string;
    selectedId?: string | null;
    recursive?: boolean;
    onRecursiveChange?: (value: boolean) => void;
    showRecursiveToggle?: boolean;
    compactSelectedView?: boolean;
    onExplorerOpenChange?: (open: boolean) => void;
    treeOnly?: boolean;
    selectableRootId?: string | null;
    selectableItemType?: DirectoryTreeSelectableType;
    isLoading?: boolean;
    translateName?: (name: string) => string;
};

type DirectoryItemProps = {
    directory: DirectoryTreeDirectory;
    level: number;
    selectedId: string | null;
    expandedIds: string[];
    selectableRootId?: string | null;
    selectableItemType: DirectoryTreeSelectableType;
    rootDirectory: DirectoryTreeDirectory;
    translateName: (name: string) => string;
    onSelect: (id: string, name: string) => void;
    onToggle: (id: string) => void;
};

function getDirectoryChildren(
    directory: DirectoryTreeDirectory,
): DirectoryTreeDirectory[] {
    if ((directory.children?.length ?? 0) > 0) {
        return directory.children ?? [];
    }

    return directory.children_recursive ?? [];
}

function findDirectoryPath(
    directory: DirectoryTreeDirectory,
    targetId: string,
    path: string[] = [],
): string[] | null {
    if (directory.id === targetId) {
        return [...path, directory.id];
    }

    for (const child of directory.children ?? []) {
        const result = findDirectoryPath(child, targetId, [
            ...path,
            directory.id,
        ]);
        if (result) return result;
    }

    for (const child of directory.children_recursive ?? []) {
        const result = findDirectoryPath(child, targetId, [
            ...path,
            directory.id,
        ]);
        if (result) return result;
    }

    return null;
}

function findDirectoryName(
    directory: DirectoryTreeDirectory,
    targetId: string,
): string | null {
    if (directory.id === targetId) return directory.name;

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

function findDirectoryPathNames(
    directory: DirectoryTreeDirectory,
    targetId: string,
    translateName: (name: string) => string,
    path: string[] = [],
): string[] | null {
    const nextPath = [...path, translateName(directory.name)];

    if (directory.id === targetId) {
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

function findRootDirectories(
    directory: DirectoryTreeDirectory,
    roots: string[] = [],
): string[] {
    if (directory.parent_id === null || directory.parent_id === undefined) {
        roots.push(directory.id);
    }

    for (const child of directory.children ?? []) {
        findRootDirectories(child, roots);
    }

    for (const child of directory.children_recursive ?? []) {
        findRootDirectories(child, roots);
    }

    return roots;
}

function getDirectoryIcon(directory: DirectoryTreeDirectory) {
    switch (directory.node_type) {
        case "user_root":
            return <DiscIcon className="h-5 w-5" />;
        case "personal_root":
            return <PersonalFolderIcon className="h-5 w-5" />;
        case "shared_root":
            return <SharedFolderIcon className="h-5 w-5" />;
        default:
            return <FolderSolidIcon className="h-5 w-5 text-gray-700" />;
    }
}

function getMediaIcon(media: DirectoryTreeMedia) {
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

function buildUrl(
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

function getDirectoryFromResponse(payload: any): DirectoryTreeDirectory | null {
    return (
        payload?.data?.directory ?? payload?.directory ?? payload?.data ?? null
    );
}

function DirectoryItem({
    directory,
    level,
    selectedId,
    expandedIds,
    selectableRootId,
    selectableItemType,
    rootDirectory,
    translateName,
    onSelect,
    onToggle,
}: DirectoryItemProps) {
    const children = getDirectoryChildren(directory);
    const media = selectableItemType === "media" ? (directory.media ?? []) : [];
    const hasChildren = children.length > 0 || media.length > 0;
    const isExpanded = expandedIds.includes(directory.id);
    const isSelected = selectedId === directory.id;
    const isWithinSelectableRoot =
        !selectableRootId ||
        findDirectoryPath(rootDirectory, directory.id)?.includes(
            selectableRootId,
        );
    const isSelectable =
        selectableItemType === "directory" && isWithinSelectableRoot;
    const canToggle = hasChildren && isWithinSelectableRoot;

    return (
        <div>
            <div
                className={`flex h-7 items-center gap-2 pr-3 text-base transition-colors ${isWithinSelectableRoot ? `${isSelectable || canToggle ? "cursor-pointer" : "cursor-default"} ${isSelected ? "bg-gray-100" : "hover:bg-gray-50"}` : "cursor-not-allowed opacity-45"}`}
                style={{ paddingLeft: `${level * 0.5 + 1}rem` }}
                onClick={() => {
                    if (isSelectable) {
                        onSelect(directory.id, directory.name);
                    } else if (canToggle) {
                        onToggle(directory.id);
                    }
                }}
            >
                <button
                    type="button"
                    onClick={(event) => {
                        event.stopPropagation();
                        if (canToggle) {
                            onToggle(directory.id);
                        }
                    }}
                    className="flex h-4 w-4 shrink-0 items-center justify-center text-foreground"
                    aria-label={
                        hasChildren
                            ? isExpanded
                                ? "Contraer carpeta"
                                : "Expandir carpeta"
                            : "Sin subcarpetas"
                    }
                >
                    {hasChildren ? (
                        isExpanded ? (
                            <ChevronDown className="h-3.5 w-3.5" />
                        ) : (
                            <ChevronRight className="h-3.5 w-3.5" />
                        )
                    ) : (
                        <span className="block h-3.5 w-3.5" />
                    )}
                </button>

                <span className="flex h-5 w-5 shrink-0 items-center justify-center">
                    {getDirectoryIcon(directory)}
                </span>

                <span className="truncate font-medium text-gray-700">
                    {translateName(directory.name)}
                </span>
            </div>

            {hasChildren && isExpanded && (
                <div>
                    {children.map((child) => (
                        <DirectoryItem
                            key={child.id}
                            directory={child}
                            level={level + 1}
                            selectedId={selectedId}
                            expandedIds={expandedIds}
                            selectableRootId={selectableRootId}
                            selectableItemType={selectableItemType}
                            rootDirectory={rootDirectory}
                            translateName={translateName}
                            onSelect={onSelect}
                            onToggle={onToggle}
                        />
                    ))}
                    {media.map((item) => (
                        <div
                            key={item.id}
                            className={`flex h-7 cursor-pointer items-center gap-2 pr-3 text-base transition-colors ${selectedId === item.id ? "bg-gray-100" : "hover:bg-gray-50"}`}
                            style={{
                                paddingLeft: `${(level + 1) * 0.5 + 1}rem`,
                            }}
                            onClick={() => onSelect(item.id, item.name)}
                        >
                            <span className="block h-3.5 w-3.5 shrink-0" />
                            <span className="flex h-5 w-5 shrink-0 items-center justify-center">
                                {getMediaIcon(item)}
                            </span>
                            <span className="truncate font-medium text-gray-700">
                                {item.name}
                            </span>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

export default function DirectoryTree({
    directories,
    directoriesEndpoint = "/_apollo/proteus/directories",
    directoriesQuery,
    onSelect,
    selectedId,
    selectedName,
    recursive,
    onRecursiveChange,
    showRecursiveToggle = true,
    compactSelectedView = false,
    onExplorerOpenChange,
    treeOnly = false,
    selectableRootId,
    selectableItemType = "directory",
    isLoading = false,
    translateName = (name) => name,
}: DirectoryTreeProps) {
    const [loadedDirectories, setLoadedDirectories] =
        useState<DirectoryTreeDirectory | null>(directories ?? null);
    const [isFetchingDirectories, setIsFetchingDirectories] =
        useState<boolean>(!directories);
    const [confirmedDirectoryId, setConfirmedDirectoryId] = useState<
        string | null
    >(selectedId ?? null);
    const [confirmedDirectoryName, setConfirmedDirectoryName] =
        useState<string>(selectedName ?? "");
    const [pendingDirectoryId, setPendingDirectoryId] = useState<string | null>(
        selectedId ?? null,
    );
    const [pendingDirectoryName, setPendingDirectoryName] = useState<string>(
        selectedName ?? "",
    );
    const [expandedIds, setExpandedIds] = useState<string[]>([]);
    const [isRecursive, setIsRecursive] = useState<boolean>(recursive ?? false);
    const [isExplorerOpen, setIsExplorerOpen] = useState<boolean>(false);
    const directoriesQueryKey = JSON.stringify(directoriesQuery ?? {});
    const activeDirectories = directories ?? loadedDirectories;
    const isBusy = isLoading || isFetchingDirectories;

    useEffect(() => {
        if (directories) {
            setLoadedDirectories(directories);
            setIsFetchingDirectories(false);
            return;
        }

        const controller = new AbortController();

        setIsFetchingDirectories(true);

        fetch(buildUrl(directoriesEndpoint, directoriesQuery), {
            signal: controller.signal,
            headers: { Accept: "application/json" },
        })
            .then((response) => response.json())
            .then((payload) => {
                if (!controller.signal.aborted) {
                    setLoadedDirectories(getDirectoryFromResponse(payload));
                }
            })
            .catch(() => {
                if (!controller.signal.aborted) {
                    setLoadedDirectories(null);
                }
            })
            .finally(() => {
                if (!controller.signal.aborted) {
                    setIsFetchingDirectories(false);
                }
            });

        return () => controller.abort();
    }, [directories, directoriesEndpoint, directoriesQueryKey]);

    useEffect(() => {
        if (activeDirectories) {
            setExpandedIds(findRootDirectories(activeDirectories));
        }
    }, [activeDirectories]);

    useEffect(() => {
        if (recursive !== undefined) {
            setIsRecursive(recursive);
        }
    }, [recursive]);

    useEffect(() => {
        setConfirmedDirectoryId(selectedId ?? null);
        setPendingDirectoryId(selectedId ?? null);

        if (!activeDirectories) {
            return;
        }

        if (selectedId) {
            const name =
                findDirectoryName(activeDirectories, selectedId) ??
                selectedName ??
                "";
            setConfirmedDirectoryName(name);
            setPendingDirectoryName(name);

            const path = findDirectoryPath(activeDirectories, selectedId);
            if (path) {
                setExpandedIds((current) =>
                    Array.from(new Set([...current, ...path])),
                );
            }
        } else {
            setConfirmedDirectoryName(selectedName ?? "");
            setPendingDirectoryName(selectedName ?? "");
        }
    }, [activeDirectories, selectedId, selectedName]);

    const confirmedDirectoryPath = useMemo(() => {
        if (!activeDirectories || !confirmedDirectoryId) {
            return "";
        }

        return (
            findDirectoryPathNames(
                activeDirectories,
                confirmedDirectoryId,
                translateName,
            )?.join("/") ?? confirmedDirectoryName
        );
    }, [
        activeDirectories,
        confirmedDirectoryId,
        confirmedDirectoryName,
        translateName,
    ]);

    const handleToggleDirectory = (id: string) => {
        setExpandedIds((current) =>
            current.includes(id)
                ? current.filter((item) => item !== id)
                : [...current, id],
        );
    };

    const handleOpenExplorer = () => {
        if (confirmedDirectoryId) {
            setPendingDirectoryId(confirmedDirectoryId);
            setPendingDirectoryName(confirmedDirectoryName);
        }

        setIsExplorerOpen(true);
        onExplorerOpenChange?.(true);
    };

    const handlePreviewDirectory = (id: string, name: string) => {
        setPendingDirectoryId(id);
        setPendingDirectoryName(name);

        if (treeOnly && selectableItemType === "media") {
            setConfirmedDirectoryId(id);
            setConfirmedDirectoryName(name);
            onSelect?.(id, name, isRecursive);
        }

        if (!activeDirectories) {
            return;
        }

        const path = findDirectoryPath(activeDirectories, id);
        if (path) {
            setExpandedIds((current) =>
                Array.from(new Set([...current, ...path])),
            );
        }
    };

    const handleConfirmDirectory = () => {
        if (!pendingDirectoryId) {
            return;
        }

        setConfirmedDirectoryId(pendingDirectoryId);
        setConfirmedDirectoryName(pendingDirectoryName);
        setIsExplorerOpen(false);
        onExplorerOpenChange?.(false);
        onSelect?.(pendingDirectoryId, pendingDirectoryName, isRecursive);
    };

    const handleCancelExplorer = () => {
        setPendingDirectoryId(confirmedDirectoryId);
        setPendingDirectoryName(confirmedDirectoryName);
        setIsExplorerOpen(false);
        onExplorerOpenChange?.(false);
    };

    const clearSelection = () => {
        setConfirmedDirectoryId(null);
        setConfirmedDirectoryName("");
        setPendingDirectoryId(null);
        setPendingDirectoryName("");
        setIsExplorerOpen(false);
        onExplorerOpenChange?.(false);
        onSelect?.("", "", isRecursive);
    };

    const renderRecursiveCheckbox = () => (
        <label className="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-gray-700">
            <input
                type="checkbox"
                checked={isRecursive}
                onChange={(event) => {
                    const value = event.target.checked;
                    setIsRecursive(value);
                    onRecursiveChange?.(value);
                }}
                className="h-4 w-4"
            />
            Incluir subcarpetas
        </label>
    );

    const renderTree = () => (
        <DirectoryItem
            directory={activeDirectories as DirectoryTreeDirectory}
            level={0}
            selectedId={pendingDirectoryId}
            expandedIds={expandedIds}
            selectableRootId={selectableRootId}
            selectableItemType={selectableItemType}
            rootDirectory={activeDirectories as DirectoryTreeDirectory}
            translateName={translateName}
            onSelect={handlePreviewDirectory}
            onToggle={handleToggleDirectory}
        />
    );

    if (!activeDirectories) {
        return (
            <div className="relative flex min-h-[120px] w-full items-center justify-center bg-white text-sm text-gray-500">
                {isBusy ? (
                    <LoaderCircle
                        className="h-5 w-5 animate-spin text-gray-900"
                        aria-label="Cargando directorios"
                    />
                ) : (
                    "No se pudieron cargar los directorios."
                )}
            </div>
        );
    }

    const summaryPanel = (
        <div className="relative bg-gray-50 px-6 pt-6 pb-4">
            <h2 className="text-sm font-medium text-foreground">
                Ubicacion de la carpeta base
            </h2>

            {!confirmedDirectoryId ? (
                <button
                    type="button"
                    onClick={handleOpenExplorer}
                    disabled={isBusy}
                    className="mt-3 flex min-h-[58px] w-full items-center rounded-lg border border-dashed border-foreground bg-white px-2 py-3 text-left disabled:cursor-wait disabled:opacity-60"
                >
                    <span className="flex h-8 w-8 shrink-0 items-center justify-center bg-gray-900 text-white">
                        <FolderUp className="h-5 w-5" />
                    </span>
                    <span className="flex-1 text-center text-xs font-normal text-gray-700">
                        Haz click para elegir carpeta
                    </span>
                </button>
            ) : (
                <div className="mt-3 space-y-3">
                    <div className="flex min-h-[58px] items-center gap-3 rounded-lg border border-dashed border-gray-300 bg-white px-2 py-3">
                        <button
                            type="button"
                            onClick={handleOpenExplorer}
                            disabled={isBusy}
                            className="flex min-w-0 flex-1 items-center gap-3 text-left disabled:cursor-wait disabled:opacity-60"
                        >
                            <span className="flex h-8 w-8 shrink-0 items-center justify-center bg-gray-900 text-white">
                                <FolderUp className="h-5 w-5" />
                            </span>
                            <span className="min-w-0 flex-1 truncate text-center text-sm font-semibold text-foreground">
                                {confirmedDirectoryPath}
                            </span>
                        </button>

                        <button
                            type="button"
                            onClick={clearSelection}
                            disabled={isBusy}
                            className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-gray-700 text-white disabled:cursor-wait disabled:opacity-60"
                            aria-label="Limpiar carpeta seleccionada"
                        >
                            <X className="h-3 w-3" />
                        </button>
                    </div>

                    {showRecursiveToggle &&
                        !compactSelectedView &&
                        renderRecursiveCheckbox()}
                </div>
            )}

            {showRecursiveToggle &&
                !confirmedDirectoryId &&
                !compactSelectedView && (
                    <div className="mt-4">{renderRecursiveCheckbox()}</div>
                )}

            {isBusy && (
                <div className="absolute inset-0 z-10 flex items-center justify-center bg-gray-50/75">
                    <LoaderCircle
                        className="h-5 w-5 animate-spin text-gray-900"
                        aria-label="Cargando ubicacion de la carpeta base"
                    />
                </div>
            )}
        </div>
    );

    const explorerContent = (
        <div>
            <div className="px-6 pt-6 pb-0">
                <h2 className="text-sm font-medium text-foreground">
                    Seleccionar carpeta
                </h2>

                <div className="mt-3 overflow-hidden rounded-none bg-white shadow-none">
                    <div className="h-[312px] overflow-y-auto bg-white py-3">
                        {renderTree()}
                    </div>
                </div>
            </div>

            <div className="flex w-full flex-col gap-4 bg-gray-100 px-6 py-6 md:flex-row md:items-center md:justify-between">
                {showRecursiveToggle && renderRecursiveCheckbox()}

                <div className="flex w-full gap-2 md:w-auto md:justify-end">
                    <button
                        type="button"
                        onClick={handleCancelExplorer}
                        className="h-8 flex-1 rounded-full border border-gray-900 bg-white px-4 text-sm font-medium text-gray-900 md:w-[120px] md:flex-none"
                    >
                        Volver
                    </button>
                    <button
                        type="button"
                        onClick={handleConfirmDirectory}
                        disabled={!pendingDirectoryId}
                        className="h-8 flex-1 rounded-full bg-gray-900 px-4 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-60 md:w-[120px] md:flex-none"
                    >
                        Seleccionar
                    </button>
                </div>
            </div>
        </div>
    );

    if (treeOnly) {
        if (selectableItemType === "directory") {
            return (
                <div className="relative h-full w-full bg-white">
                    {isExplorerOpen ? explorerContent : summaryPanel}
                </div>
            );
        }

        return (
            <div className="relative h-full w-full overflow-y-auto bg-white py-3">
                {renderTree()}
            </div>
        );
    }

    return (
        <div className="relative w-full">
            {!isExplorerOpen && summaryPanel}
            {isExplorerOpen && !compactSelectedView && explorerContent}
        </div>
    );
}
