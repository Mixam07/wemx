<?php

namespace Modules\Locales\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Locales\Models\Manager;
use App\Facades\AdminTheme as Theme;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class LocalesController extends Controller
{
    public function index()
    {
        $langs = new Manager();

        return view(Theme::moduleView('Locales', 'index'), [
            'list' => $langs->getInstalled(),
            'localizations' => $langs->getAvailable(),
        ]);
    }


    public function generate(Request $request)
    {
        Artisan::call("locales:lang --action=generate --locale={$request->input('lang_code')}");
        return redirect()->back()->with('success', Artisan::output());
    }

    public function import()
    {
        Artisan::call("locales:lang --action=import");
        return redirect()->back()->with('success', Artisan::output());
    }

    public function remove($code)
    {
        $path = resource_path('lang') . "/{$code}";
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        }
        return redirect()->back()->with('success', "Localization {$code} removed");
    }

    public function translate($code)
    {
        // We get the paths to the source and destination folders
        $source = \App::langPath() . DIRECTORY_SEPARATOR . 'en';
        $destination = \App::langPath() . DIRECTORY_SEPARATOR . $code;

        // Create the destination folder if it does not already exist
        if (!is_dir($destination)) {
            File::makeDirectory($destination);
        }

        // We copy the files from the source folder
        $files = File::allFiles($source);
        foreach ($files as $file) {
            $relativePath = dirname(str_replace($source, '', $file));
            $newFilePath = $destination . DIRECTORY_SEPARATOR . $relativePath . DIRECTORY_SEPARATOR . basename($file);
            if (!File::exists($newFilePath)) {
                File::copy($file, $newFilePath);
            }
            
        }

        foreach (\Module::collections() as $module) {
            try {
                $module_source = $module->getExtraPath('Resources' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'en');
                $module_destination = $module->getExtraPath('Resources' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $code);
                if (!is_dir($module_destination)) {
                    File::makeDirectory($module_destination);
                }
                $files = File::allFiles($module_source);
                foreach ($files as $file) {
                    $relativePath = dirname(str_replace($module_source, '', $file));
                    $newFilePath = $module_destination . DIRECTORY_SEPARATOR . $relativePath . DIRECTORY_SEPARATOR . basename($file);
                    if (!File::exists($newFilePath)) {
                        File::copy($file, $newFilePath);
                    }
                }
            } catch (\Exception $e) {
                // We process copying errors
            }
        }

        return view(Theme::moduleView('Locales', 'translate'), [
            'code' => $code,
            'files' => File::allFiles($destination),
        ]);
    }

    public function translateFile($code)
    {
        if (!isset($_POST['file']) or empty($_POST['file'])) {
            return redirect()->back()->with('error', "The file does not exist");
        }
        $file = pathinfo($_POST['file']);
        $sourceArr = File::getRequire(dirname($file['dirname']) . '/en/' . $file['basename']);
        $contentArr = File::getRequire($_POST['file']);

        return view(Theme::moduleView('Locales', 'translate-file'), [
            'contentArr' => $contentArr,
            'source' => $sourceArr,
            'code' => $code,
            'file' => $_POST['file']
        ]);
    }

    public function translateSave(Request $request, $code)
    {
        $data = $request->except('_token', 'file--path');
        $newData = [];
        array_walk_recursive($data, function ($value, $key) use (&$newData) {
            $parts = explode('--', $key);
            $current = &$newData;
            foreach ($parts as $part) {
                if (!isset($current[$part])) {
                    $current[$part] = [];
                }
                $current = &$current[$part];
            }
            $current = $value;
        });
        file_put_contents($request->input('file--path'), '<?php return ' . var_export($newData, true) . ';');
        return redirect(route('locales.translate', ['code' => $code]))->with('success', "Localization {$request->input('file--path')} saved");
    }


    public function translateApi(Request $request)
    {
        $text = $request->input('text');
        $lang = $request->input('lang');
        $response = Http::get('https://api.mymemory.translated.net/get', [
            'q' => $text,
            'langpair' => $lang,
        ]);
        $translation = $response->json()['responseData']['translatedText'];
        return response()->json(['translation' => $translation]);
    }

    public function toggleUserLang($locale)
    {
        $user = auth()->user();
        if ($user) {
            $user->language = $locale;
            $user->save();
        }
        app()->setLocale($locale);
        cookie('locale', $locale, 60 * 24 * 365);
        session(['locale' => $locale]);
        return redirect()->back();
    }
}
