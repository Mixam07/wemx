<?php

namespace Modules\Artisan\Http\Controllers;

use App\Facades\AdminTheme;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Artisan\Jobs\RunArtisanCommandJob;

class ArtisanController extends Controller
{
    public function index()
    {
        $commandsList = Artisan::all();
        return view(AdminTheme::serviceView('artisan', 'index'), compact('commandsList'));
    }

    public function command()
    {
        $command = request()->input('command');
        RunArtisanCommandJob::dispatch($command);
        return redirect()->back()->with('success', 'Command has been queued.');
    }

    public function commandApi()
    {
        $command = request()->input('command');
        RunArtisanCommandJob::dispatch(str_replace('artisan', '', $command));
        return response()->json(['command' => $command]);
    }

    public function readLogs()
    {
        $logFileName = 'artisan/artisan-commands.log';
        $logs = Storage::disk()->get($logFileName);
        return response()->json(['logs' => $logs]);
    }

    public function clearLogs()
    {
        $logFileName = 'artisan/artisan-commands.log';
        Storage::disk()->delete($logFileName);
        return redirect()->back();
    }

    public function adminDebugToggle()
    {
        $adminDebug = \Cache::get('admin_debug', false);
        \Cache::put('admin_debug', !$adminDebug);
        return redirect()->back()->with('success', 'Admin area debug mode has been toggled.');
    }

    public function envEditor()
    {
        $nonEditableKeys = ['APP_KEY', 'DB_PASSWORD'];
        $env = file_get_contents(base_path('.env'));
        return view(AdminTheme::serviceView('artisan', 'env'), compact('nonEditableKeys', 'env'));
    }

    public function envEditorSave()
    {
        $nonEditableKeys = ['APP_KEY', 'DB_PASSWORD'];
        if (!request()->input('key') || !request()->input('value')) {
            return redirect()->back()->with('error', 'Key and value are required.');
        }
        if (in_array(request()->input('key'), $nonEditableKeys)) {
            return redirect()->back()->with('error', 'You cannot edit this key.');
        }

        $bc_dir = base_path('env_backup');
        if (!file_exists($bc_dir)) {
            mkdir($bc_dir, 0777, true);
        }
        copy(base_path('.env'), $bc_dir . '/.env_' . date('Y-m-d_H-i-s') . '.bak');
        RunArtisanCommandJob::dispatch('env:editor --key=' . request()->input('key') . ' --value=' . request()->input('value'));
        return redirect()->back()->with('success', 'Environment file has been updated. ' . request()->input('key') . '=' . request()->input('value'));
    }

    public function envBackups()
    {
        $bc_dir = base_path('env_backup');
        $backups = [];
        if (file_exists($bc_dir)) {
            $backups = File::allFiles(base_path('env_backup'), true);
        }
        return view(AdminTheme::serviceView('artisan', 'env-backups'), compact('backups'));
    }

    public function envBackupsDownload($file)
    {
        return response()->download(base_path('env_backup/' . $file));
    }

    public function envBackupsDelete($file)
    {
        File::delete(base_path('env_backup/' . $file));
        return redirect()->back()->with('success', 'Backup has been deleted.');
    }

    public function envBackupsRestore($file)
    {
        File::copy(base_path('env_backup/' . $file), base_path('.env'));
        return redirect()->back()->with('success', 'Backup has been restored.');
    }
}
