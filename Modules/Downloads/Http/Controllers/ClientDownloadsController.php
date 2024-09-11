<?php
namespace Modules\Downloads\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Downloads\Entities\Download;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ClientDownloadsController extends Controller
{
    public function index()
    {
        $downloads = Download::latest()->paginate(10);    
        return view('downloads::client.download', compact('downloads'));
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

        $download->increment('downloads_count');
        return response()->download($filePath, $download->name . '.zip', ['Content-Type' => 'application/zip']);
    }

    public static function humanFilesize($bytes, $decimals = 2)
    {
        $size = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
}
