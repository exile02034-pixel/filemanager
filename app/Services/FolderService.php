<?php

namespace App\Services;

use App\Models\Folder;

class FolderService
{
    /**
     * Create a new folder
     *
     * @param int $userId
     * @param string $name
     * @param string|null $parentId
     * @return Folder
     */
    public function createFolder(int $userId, string $name, ?string $parentId = null): Folder
    {
        return Folder::create([
            'user_id' => $userId,
            'name' => $name,
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Rename an existing folder
     *
     * @param Folder $folder
     * @param string $newName
     * @return Folder
     */
    public function renameFolder(Folder $folder, string $newName): Folder
    {
        $folder->update(['name' => $newName]);
        return $folder;
    }

    /**
     * Delete folder and optionally its children and files
     *
     * @param Folder $folder
     * @param bool $deleteContents
     * @return void
     */
    public function deleteFolder(Folder $folder, bool $deleteContents = true): void
    {
        if ($deleteContents) {
            // Delete child folders recursively
            foreach ($folder->children as $child) {
                $this->deleteFolder($child, true);
            }

            // Delete all files in this folder
            foreach ($folder->files as $file) {
                $file->delete(); // later you can call FileService to handle storage cleanup
            }
        }

        $folder->delete();
    }

    /**
     * Get folders for a user (optionally by parent)
     *
     * @param int $userId
     * @param string|null $parentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listFolders(int $userId, ?string $parentId = null)
    {
        return Folder::where('user_id', $userId)
                     ->where('parent_id', $parentId)
                     ->with('children')
                     ->get();
    }
}