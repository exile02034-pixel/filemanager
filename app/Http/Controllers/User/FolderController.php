<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Services\FolderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FolderController extends Controller
    {
        protected $folderService;

        public function __construct(FolderService $folderService)
        {
            $this->folderService = $folderService;
        }

       public function index(Request $request){
            $search = $request->input('search');
            $sort = $request->input('sort', 'recent');

            $folders = $request->user()->folders()
                ->when($search, function ($q) use ($search) {
                    // Case-insensitive search
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($sort === 'year', function ($q) {
                    // Order by year (PostgreSQL)
                    $q->orderByRaw('EXTRACT(YEAR FROM created_at) DESC');
                })
                ->when($sort === 'month', function ($q) {
                    // Order by month (PostgreSQL)
                    $q->orderByRaw('EXTRACT(MONTH FROM created_at) DESC');
                })
                ->when($sort === 'recent', function ($q) {
                    $q->orderBy('created_at', 'desc');
                })
                ->get();

            return Inertia::render('user/Profile/folders/index', [
                'folders' => $folders,
                'search' => $search,
                'sort' => $sort,
            ]);
        }

        public function store(Request $request){
    $request->validate([
        'name' => 'required|string|max:255',
        'parent_id' => 'nullable|uuid|exists:folders,id',
        'action' => 'nullable|in:overwrite,version', // optional
    ]);

    $userId = $request->user()->id;
    $parentId = $request->parent_id;
    $folderName = $request->name;

    // Check if folder with same name exists
    $existingFolder = Folder::where('user_id', $userId)
        ->where('parent_id', $parentId)
        ->where('name', $folderName)
        ->first();

    // No action provided → just report duplicate
   if ($existingFolder && !$request->action) {
    return back()->withErrors(['duplicate' => $folderName]);
}

    if ($existingFolder && $request->action === 'overwrite') {
        // Delete existing folder entirely
        $this->folderService->deleteFolder($existingFolder, true);
    }

    if ($existingFolder && $request->action === 'version') {
        // Append version number
        $version = 2;
        $baseName = $folderName;
        while (Folder::where('user_id', $userId)
            ->where('parent_id', $parentId)
            ->where('name', $folderName)
            ->exists()) {
            $folderName = $baseName . " v{$version}";
            $version++;
        }
    }

    // Create folder
    $folder = $this->folderService->createFolder($userId, $folderName, $parentId);

    return back()->with('success', "Folder '{$folder->name}' created successfully.");
}

            public function show(Request $request, Folder $folder)
        {

            if ($folder->user_id !== $request->user()->id) {
                abort(403);
            }


            $subfolders = $folder->children()
                ->orderBy('created_at', 'desc')
                ->get();


            $files = $folder->files()
                ->orderBy('created_at', 'desc')
                ->get();

            return Inertia::render('user/Profile/folders/show', [
                'folder' => $folder,
                'subfolders' => $subfolders,
                'files' => $files,
            ]);
        }

        public function update(Request $request, $id)
        {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $folder = Folder::findOrFail($id);

            // Authorization check
            $this->authorize('update', $folder);

            $this->folderService->renameFolder($folder, $request->name);

            return back()->with('success', 'Folder renamed successfully.');
        }

        // Delete folder
        public function destroy($id)
        {
            $folder = Folder::findOrFail($id);

            $this->authorize('delete', $folder);

            $this->folderService->deleteFolder($folder, true);

            return back()->with('success', 'Folder deleted successfully.');
        }
    }
