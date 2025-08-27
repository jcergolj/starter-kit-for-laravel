<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jcergolj\InAppNotifications\Facades\InAppNotification;

class ThemeController extends Controller
{
    public function update(Request $request)
    {
        $request->session()->put('theme', $request->input('theme'));

        if ($request->wantsTurboStream()) {
            return response()->noContent();
        }

        InAppNotification::success(__('Theme updated.'));

        return back();
    }
}
