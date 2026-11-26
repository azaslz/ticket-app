<?php

namespace App\Http\Controllers;

use App\Models\Fanclub;
use App\Models\FanclubMembership;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FanclubSearchController extends Controller
{
    public function users(Request $request): View
    {
        $flow = $request->query('flow', 'join');
        abort_unless(in_array($flow, ['join', 'leave'], true), 404);

        $fanclubId = $request->integer('fanclub_id');
        if ($flow === 'leave') {
            abort_unless($fanclubId > 0, 404);
            FanclubMembership::where('account_id', auth()->id())
                ->where('fanclub_id', $fanclubId)
                ->where('joinfc_status', '加入中')
                ->whereNull('joinfc_limit')
                ->firstOrFail();
        }

        $selectionUrl = $flow === 'join'
            ? route('myfanclub.join.form', array_filter(['fanclub_id' => $fanclubId ?: null]))
            : route('myfanclub.leave.form', $fanclubId);

        return view('search.users', [
            'user' => auth()->user(),
            'flow' => $flow,
            'selectionUrl' => $selectionUrl,
        ]);
    }

    public function artists(Request $request): View
    {
        $flow = $request->query('flow', 'join');
        abort_unless(in_array($flow, ['join', 'leave'], true), 404);

        if ($flow === 'join') {
            $joinedIds = FanclubMembership::where('account_id', auth()->id())
                ->where('joinfc_status', '加入中')
                ->whereNull('joinfc_limit')
                ->pluck('fanclub_id');

            $fanclubs = Fanclub::where('is_active', true)
                ->whereNotIn('id', $joinedIds)
                ->orderBy('name')
                ->get();
        } else {
            $fanclubs = FanclubMembership::with('fanclub')
                ->where('account_id', auth()->id())
                ->where('joinfc_status', '加入中')
                ->whereNull('joinfc_limit')
                ->get()
                ->pluck('fanclub')
                ->filter();
        }

        return view('search.artists', [
            'fanclubs' => $fanclubs,
            'flow' => $flow,
        ]);
    }
}
