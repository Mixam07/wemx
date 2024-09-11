<?php
namespace Modules\Downloads\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Downloads\Entities\Download;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Nwidart\Modules\Facades\Module;

class DownloadsController extends Controller
{
    public function index()
    {
        $downloads = Download::paginate(10);
        return view('downloads::admin.index', compact('downloads'));
    }

    public function create()
    {
        return view('downloads::admin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required',
            'package' => 'nullable',
            'name' => 'required',
            'allow_guest' => 'boolean',
            'file' => 'required|file|mimes:zip',
        ]);

        if($request->input('allow_guest') AND !empty($request->input('package', []))) {
            return redirect()->back()->withError('The allow guest field cannot be true if you also require a user to have a package.');
        }

        if ($request->hasFile('file')) {
            $zipFile = $request->file('file');
            $filename = time() . '.' . $zipFile->getClientOriginalExtension();
        
            // Store in the "storage/app/modules/downloads" directory
            Storage::disk('local')->putFileAs('modules/downloads', $zipFile, $filename);
        }

        $fileSize = $zipFile->getSize();

        $download = new Download;
        $download->description = $request->description;
        $download->package = $request->input('package', []);
        $download->name = $request->name;
        $download->allow_guest = $request->input('allow_guest');
        $download->file_path = $filename;
        $download->file_size = $fileSize;
        $download->save();

    
        return redirect()->route('downloads.index')->with('success', 'Download created successfully.');
    }

    public function download(Download $download)
    {
        $filePath = storage_path('app/modules/downloads/' . $download->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->withError("File in folder {$filePath} does not exists");
        }

        if(!is_readable($filePath)) {
            return redirect()->back()->withError("File is not readable, please ensure /storage has the correct 755 permissions");
        }

        if(!$download->canDownload()) {
            return redirect()->back()->withError('You don\'t have any permissions to download this resource');
        }

        return response()->download($filePath, $download->name . '.zip', ['Content-Type' => 'application/zip']);
    }

    public function edit(Download $download)
    {
        return view('downloads::admin.edit', compact('download'));
    }

    public function update(Request $request, Download $download)
    {    
        $request->validate([
            'description' => 'required',
            'package' => 'nullable',
            'name' => 'required',
            'allow_guest' => 'boolean', // Make sure 'allow_guest' is treated as a boolean
            'file' => 'file|mimes:zip',
        ]);

        if($request->input('allow_guest') AND !empty($request->input('package', []))) {
            return redirect()->back()->withError('The allow guest field cannot be true if you also require a user to have a package.');
        }
    
        if ($request->hasFile('file')) {
            $zipFile = $request->file('file');
            Storage::disk('local')->putFileAs('modules/downloads', $zipFile, $download->file_path);
        }
    
        $download->description = $request->description;
        $download->package = $request->input('package', []);
        $download->name = $request->name;
        $download->allow_guest = $request->allow_guest;
    
        $download->save();
    
        return redirect()->route('downloads.index')->with('success', 'Download updated successfully.');
    }
    public function destroy(Download $download)
    {
        Storage::delete('downloads/' . $download->file_path);
        $download->delete();

        return redirect()->route('downloads.index')->with('success', 'Download deleted successfully.');
    }
}
