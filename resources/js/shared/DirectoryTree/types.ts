export type DirectoryTreeMedia = {
    id?: string;
    id_media?: string;
    name: string;
    type?: string | null;
};

export type VirtualDirectoryId = "user_root" | "personal_root" | "shared_root";

export type DirectoryTreeBaseDirectory = {
    name: string;
    id_parent?: string | null;
    children?: DirectoryTreeDirectory[];
    children_recursive?: DirectoryTreeDirectory[];
    media?: DirectoryTreeMedia[];
};

export type DirectoryTreeVirtualDirectory = DirectoryTreeBaseDirectory & {
    id_directory: VirtualDirectoryId;
    is_virtual: true;
};

export type DirectoryTreePersistedDirectory = DirectoryTreeBaseDirectory & {
    id_directory: string;
    is_virtual?: false;
};

export type DirectoryTreeDirectory =
    | DirectoryTreeVirtualDirectory
    | DirectoryTreePersistedDirectory;

export type DirectoryTreeSelectableType = "directory" | "media";

export type DirectoryTreeProps = {
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
    createDirectoryEndpoint?: string;
    onCreateFolder?: (payload: {
        parentId: string;
        name: string;
    }) => void | Promise<void>;
    createFolderLabel?: string;
};

export type DirectoryItemProps = {
    directory: DirectoryTreeDirectory;
    level: number;
    selectedId: string | null;
    expandedIds: string[];
    selectableRootId?: string | null;
    selectableItemType: DirectoryTreeSelectableType;
    rootDirectory: DirectoryTreeDirectory;
    translateName: (name: string) => string;
    onSelect: (id: string, name: string, isVirtual?: boolean) => void;
    onToggle: (id: string) => void;
    creatingFolderParentId?: string | null;
    creatingFolderName?: string;
    isSubmittingFolder?: boolean;
    onCreateFolderNameChange?: (value: string) => void;
    onConfirmCreateFolder?: () => void;
    onCancelCreateFolder?: () => void;
};
