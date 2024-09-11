<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Facades\AdminTheme as Theme;
use App\Models\Punishment;

class PunishmentController extends Controller
{
    public function bans()
    {
        $punishments = Punishment::where('type', 'ban')->orWhere('type', 'ipban')->orWhere('type', 'unbanned')->latest()->paginate(15);
        return Theme::view('punishments.bans', compact('punishments'));
    }

    public function warnings()
    {
        $punishments = Punishment::where('type', 'warning')->latest()->paginate(15);
        return Theme::view('punishments.warnings', compact('punishments'));
    }

    public function unban(Punishment $punishment)
    {
        $punishment->unban();
        return redirect()->back()->with('success', $punishment->user->username . ' has been unbanned.');
    }

    public function destroy(Punishment $punishment)
    {
        $punishment->delete();
        return redirect()->back()->with('success', 'Ban has been deleted');
    }
}
