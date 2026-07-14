import { Check, ChevronDown, ChevronRight, FolderSolidIcon, X } from "./Icons";
import type { DirectoryItemProps } from "./types";
import {
    findDirectoryPath,
    getDirectoryChildren,
    getDirectoryIcon,
    getDirectoryId,
    getMediaId,
    getMediaIcon,
} from "./utils";

export default function DirectoryItem({
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
    creatingFolderParentId,
    creatingFolderName = "",
    isSubmittingFolder = false,
    onCreateFolderNameChange,
    onConfirmCreateFolder,
    onCancelCreateFolder,
}: DirectoryItemProps) {
    const directoryId = getDirectoryId(directory);
    const children = getDirectoryChildren(directory);
    const media = selectableItemType === "media" ? (directory.media ?? []) : [];
    const isCreatingFolderHere = creatingFolderParentId === directoryId;
    const hasChildren =
        children.length > 0 ||
        media.length > 0 ||
        isCreatingFolderHere;
    const isExpanded = expandedIds.includes(directoryId);
    const isSelected = selectedId === directoryId;
    const isWithinSelectableRoot =
        !selectableRootId ||
        findDirectoryPath(rootDirectory, directoryId)?.includes(selectableRootId);
    const isSelectable =
        selectableItemType === "directory" &&
        isWithinSelectableRoot &&
        !directory.is_virtual;
    const canToggle = hasChildren && isWithinSelectableRoot;

    return (
        <div>
            <div
                className={`flex h-7 items-center gap-2 pr-3 text-base transition-colors ${isWithinSelectableRoot ? `${isSelectable || canToggle ? "cursor-pointer" : "cursor-default"} ${isSelected ? "bg-gray-100" : "hover:bg-gray-50"}` : "cursor-not-allowed opacity-45"}`}
                style={{ paddingLeft: `${level * 0.5 + 1}rem` }}
                onClick={() => {
                    if (selectableItemType === "directory" && isWithinSelectableRoot) {
                        onSelect(directoryId, directory.name, directory.is_virtual);
                    }
                }}
            >
                <button
                    type="button"
                    onClick={(event) => {
                        event.stopPropagation();
                        if (canToggle) {
                            onToggle(directoryId);
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
                            key={child.id_directory}
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
                            creatingFolderParentId={creatingFolderParentId}
                            creatingFolderName={creatingFolderName}
                            isSubmittingFolder={isSubmittingFolder}
                            onCreateFolderNameChange={onCreateFolderNameChange}
                            onConfirmCreateFolder={onConfirmCreateFolder}
                            onCancelCreateFolder={onCancelCreateFolder}
                        />
                    ))}
                    {isCreatingFolderHere && (
                        <div
                            className="flex min-h-9 items-center gap-2 bg-orange-50 pr-3 text-base"
                            style={{
                                paddingLeft: `${(level + 1) * 0.5 + 1}rem`,
                            }}
                        >
                            <span className="block h-3.5 w-3.5 shrink-0" />
                            <span className="flex h-5 w-5 shrink-0 items-center justify-center text-gray-700">
                                <FolderSolidIcon className="h-5 w-5 text-gray-700" />
                            </span>
                            <input
                                type="text"
                                value={creatingFolderName}
                                onChange={(event) =>
                                    onCreateFolderNameChange?.(event.target.value)
                                }
                                className="my-1 h-8 min-w-0 flex-1 rounded-sm border border-orange-100 bg-white px-3 text-sm font-medium text-gray-700 outline-none ring-0 placeholder:text-gray-400 focus:border-orange-300"
                                placeholder="Nueva carpeta"
                            />
                            <button
                                type="button"
                                onClick={onConfirmCreateFolder}
                                disabled={
                                    isSubmittingFolder ||
                                    creatingFolderName.trim().length === 0
                                }
                                className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-orange-500 text-white disabled:cursor-not-allowed disabled:opacity-60"
                                aria-label="Confirmar creacion de carpeta"
                            >
                                <Check className="h-4 w-4" />
                            </button>
                            <button
                                type="button"
                                onClick={onCancelCreateFolder}
                                disabled={isSubmittingFolder}
                                className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-gray-500 text-white disabled:cursor-not-allowed disabled:opacity-60"
                                aria-label="Cancelar creacion de carpeta"
                            >
                                <X className="h-4 w-4" />
                            </button>
                        </div>
                    )}
                    {media.map((item) => (
                        <div
                            key={getMediaId(item)}
                            className={`flex h-7 cursor-pointer items-center gap-2 pr-3 text-base transition-colors ${selectedId === getMediaId(item) ? "bg-gray-100" : "hover:bg-gray-50"}`}
                            style={{
                                paddingLeft: `${(level + 1) * 0.5 + 1}rem`,
                            }}
                            onClick={() =>
                                onSelect(getMediaId(item), item.name, false)
                            }
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
