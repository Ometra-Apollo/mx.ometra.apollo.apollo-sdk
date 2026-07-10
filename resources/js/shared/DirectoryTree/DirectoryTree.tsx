import { useEffect, useMemo, useState } from "react";
import { FolderUp, LoaderCircle, X } from "./Icons";
import DirectoryItem from "./DirectoryItem";
import type {
    DirectoryTreeDirectory,
    DirectoryTreeProps,
} from "./types";
import {
    buildUrl,
    findDirectoryName,
    findDirectoryPath,
    findDirectoryPathNames,
    findRootDirectories,
    getDirectoryId,
    getDirectoryFromResponse,
    insertDirectoryChild,
} from "./utils";

function RecursiveToggle({
    checked,
    onChange,
}: {
    checked: boolean;
    onChange: (value: boolean) => void;
}) {
    return (
        <label className="flex cursor-pointer items-center gap-2.5 text-sm font-medium text-gray-700">
            <input
                type="checkbox"
                checked={checked}
                onChange={(event) => onChange(event.target.checked)}
                className="h-4 w-4"
            />
            Incluir subcarpetas
        </label>
    );
}

function getCsrfToken(): string | null {
    if (typeof document === "undefined") {
        return null;
    }

    const metaToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    if (metaToken) {
        return metaToken;
    }

    const xsrfCookie = document.cookie
        .split("; ")
        .find((item) => item.startsWith("XSRF-TOKEN="))
        ?.split("=")[1];

    return xsrfCookie ? decodeURIComponent(xsrfCookie) : null;
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
    createDirectoryEndpoint = "/_apollo/proteus/directories",
    onCreateFolder,
    createFolderLabel = "Crear carpeta",
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
    const [activeDirectoryId, setActiveDirectoryId] = useState<string | null>(
        selectedId ?? null,
    );
    const [expandedIds, setExpandedIds] = useState<string[]>([]);
    const [isRecursive, setIsRecursive] = useState<boolean>(recursive ?? false);
    const [isExplorerOpen, setIsExplorerOpen] = useState<boolean>(false);
    const [creatingFolderParentId, setCreatingFolderParentId] = useState<
        string | null
    >(null);
    const [creatingFolderName, setCreatingFolderName] =
        useState<string>("Nueva carpeta");
    const [isSubmittingFolder, setIsSubmittingFolder] =
        useState<boolean>(false);
    const directoriesQueryKey = JSON.stringify(directoriesQuery ?? {});
    const activeDirectories = loadedDirectories;
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
            credentials: "same-origin",
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
        setActiveDirectoryId(selectedId ?? null);

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

    const handleRecursiveChange = (value: boolean) => {
        setIsRecursive(value);
        onRecursiveChange?.(value);
    };

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
            setActiveDirectoryId(confirmedDirectoryId);
        }

        setCreatingFolderParentId(null);
        setCreatingFolderName("Nueva carpeta");
        setIsExplorerOpen(true);
        onExplorerOpenChange?.(true);
    };

    const handlePreviewDirectory = (
        id: string,
        name: string,
        isVirtual = false,
    ) => {
        setActiveDirectoryId(id);

        if (isVirtual) {
            setPendingDirectoryId(null);
            setPendingDirectoryName("");
        } else {
            setPendingDirectoryId(id);
            setPendingDirectoryName(name);
        }

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
        setActiveDirectoryId(confirmedDirectoryId);
        setCreatingFolderParentId(null);
        setCreatingFolderName("Nueva carpeta");
        setIsExplorerOpen(false);
        onExplorerOpenChange?.(false);
    };

    const clearSelection = () => {
        setConfirmedDirectoryId(null);
        setConfirmedDirectoryName("");
        setPendingDirectoryId(null);
        setPendingDirectoryName("");
        setActiveDirectoryId(null);
        setCreatingFolderParentId(null);
        setCreatingFolderName("Nueva carpeta");
        setIsExplorerOpen(false);
        onExplorerOpenChange?.(false);
        onSelect?.("", "", isRecursive);
    };

    const handleStartCreateFolder = () => {
        if (!activeDirectoryId) {
            return;
        }

        setCreatingFolderParentId(activeDirectoryId);
        setCreatingFolderName("Nueva carpeta");
        setExpandedIds((current) =>
            current.includes(activeDirectoryId)
                ? current
                : [...current, activeDirectoryId],
        );
    };

    const handleCancelCreateFolder = () => {
        setCreatingFolderParentId(null);
        setCreatingFolderName("Nueva carpeta");
        setIsSubmittingFolder(false);
    };

    const handleConfirmCreateFolder = async () => {
        if (!creatingFolderParentId) {
            return;
        }

        const folderName = creatingFolderName.trim();

        if (!folderName) {
            return;
        }

        setIsSubmittingFolder(true);

        try {
            if (onCreateFolder) {
                await onCreateFolder({
                    parentId: creatingFolderParentId,
                    name: folderName,
                });
            } else {
                const response = await fetch(createDirectoryEndpoint, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        ...(getCsrfToken()
                            ? { "X-CSRF-TOKEN": getCsrfToken() as string }
                            : {}),
                    },
                    body: JSON.stringify({
                        parent_id: creatingFolderParentId,
                        name: folderName,
                    }),
                });

                if (!response.ok) {
                    throw new Error("No se pudo crear la carpeta.");
                }

                const payload = await response.json();
                const createdDirectory = getDirectoryFromResponse(payload);

                if (createdDirectory && activeDirectories) {
                    setLoadedDirectories(
                        insertDirectoryChild(
                            activeDirectories,
                            creatingFolderParentId,
                            createdDirectory,
                        ),
                    );

                    const createdDirectoryId = getDirectoryId(createdDirectory);

                    setExpandedIds((current) =>
                        Array.from(
                            new Set([
                                ...current,
                                creatingFolderParentId,
                                createdDirectoryId,
                            ]),
                        ),
                    );
                    setActiveDirectoryId(createdDirectoryId);
                    setPendingDirectoryId(createdDirectoryId);
                    setPendingDirectoryName(createdDirectory.name);
                }
            }
        } catch {
        } finally {
            setCreatingFolderParentId(null);
            setCreatingFolderName("Nueva carpeta");
            setIsSubmittingFolder(false);
        }
    };

    const renderRecursiveToggle = () => (
        <RecursiveToggle
            checked={isRecursive}
            onChange={handleRecursiveChange}
        />
    );

    const renderTree = () => (
        <DirectoryItem
            directory={activeDirectories as DirectoryTreeDirectory}
            level={0}
            selectedId={activeDirectoryId}
            expandedIds={expandedIds}
            selectableRootId={selectableRootId}
            selectableItemType={selectableItemType}
            rootDirectory={activeDirectories as DirectoryTreeDirectory}
            translateName={translateName}
            onSelect={handlePreviewDirectory}
            onToggle={handleToggleDirectory}
            creatingFolderParentId={creatingFolderParentId}
            creatingFolderName={creatingFolderName}
            isSubmittingFolder={isSubmittingFolder}
            onCreateFolderNameChange={setCreatingFolderName}
            onConfirmCreateFolder={handleConfirmCreateFolder}
            onCancelCreateFolder={handleCancelCreateFolder}
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
                        renderRecursiveToggle()}
                </div>
            )}

            {showRecursiveToggle &&
                !confirmedDirectoryId &&
                !compactSelectedView && (
                    <div className="mt-4">{renderRecursiveToggle()}</div>
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
        <div className="bg-white">
            <div className="px-6 pt-6 pb-0">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <h2 className="text-sm font-medium text-foreground">
                        Seleccionar carpeta
                    </h2>

                    {selectableItemType === "directory" &&
                        (onCreateFolder || createDirectoryEndpoint) && (
                        <button
                            type="button"
                            onClick={handleStartCreateFolder}
                            disabled={isBusy || !activeDirectoryId}
                            className="inline-flex h-10 items-center justify-center rounded-full bg-orange-400 px-5 text-sm font-medium text-gray-900 transition hover:bg-orange-500 disabled:cursor-wait disabled:opacity-60"
                        >
                            {createFolderLabel}
                        </button>
                        )}
                </div>

                <div className="mt-3 overflow-hidden rounded-none bg-white shadow-none">
                    <div className="h-[312px] overflow-y-auto bg-white py-3">
                        {renderTree()}
                    </div>
                </div>
            </div>

            <div className="flex w-full flex-col gap-4 bg-gray-100 px-6 py-6 md:flex-row md:items-center md:justify-between">
                {showRecursiveToggle && renderRecursiveToggle()}

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
        <div className="relative w-full bg-white">
            {!isExplorerOpen && summaryPanel}
            {isExplorerOpen && !compactSelectedView && explorerContent}
        </div>
    );
}
