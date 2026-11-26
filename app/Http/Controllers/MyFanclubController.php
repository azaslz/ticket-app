<?php

namespace App\Http\Controllers;

use App\Models\FanclubMembership;
use App\Models\Fanclub;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyFanclubController extends Controller
{
    public function index(): View
    {
        $accountId = auth()->id();
        $memberships = $accountId
            ? FanclubMembership::where('account_id', $accountId)
                ->where('joinfc_status', '加入中')
                ->whereNull('joinfc_limit')
                ->orderByDesc('joinfc_join_day')
                ->get()
            : collect();

        return view('myfanclub', [
            'memberships' => $memberships,
            'fanclubs' => Fanclub::all()->keyBy('id'),
        ]);
    }

    public function joinForm(Request $request): View
    {
        $joinedIds = FanclubMembership::where('account_id', auth()->id())
            ->where('joinfc_status', '加入中')
            ->whereNull('joinfc_limit')
            ->pluck('fanclub_id')
            ->all();

        return view('fanclubs.join', [
            'fanclubs' => Fanclub::where('is_active', true)
                ->whereNotIn('id', $joinedIds)
                ->orderBy('name')
                ->get(),
            'selectedFanclubId' => $request->integer('fanclub_id'),
        ]);
    }

    public function joinSelected(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fanclub_id' => ['required', 'integer', 'exists:fanclubs,id'],
        ]);

        $club = Fanclub::where('is_active', true)->findOrFail($data['fanclub_id']);
        $membership = FanclubMembership::where('account_id', auth()->id())
            ->where('fanclub_id', $club->id)
            ->where('joinfc_status', '加入中')
            ->whereNull('joinfc_limit')
            ->first();

        if ($membership) {
            return back()->withInput()->with('error', 'このファンクラブには既に加入しています。');
        }

        FanclubMembership::updateOrCreate(
            ['account_id' => auth()->id(), 'fanclub_id' => $club->id],
            ['joinfc_join_day' => now()->toDateString(), 'joinfc_limit' => null, 'joinfc_status' => '加入中'],
        );

        return redirect()->route('myfanclub.index')->with('success', 'ファンクラブへの加入が完了しました。');
    }

    public function leaveForm(int $fanclub): View
    {
        $membership = FanclubMembership::with('fanclub')
            ->where('account_id', auth()->id())
            ->where('fanclub_id', $fanclub)
            ->where('joinfc_status', '加入中')
            ->whereNull('joinfc_limit')
            ->firstOrFail();

        abort_unless($membership->fanclub, 404);

        return view('fanclubs.leave', [
            'fanclub' => $membership->fanclub,
            'fanclubId' => $fanclub,
            'membership' => $membership,
        ]);
    }

    public function join(int $fanclub): RedirectResponse
    {
        $club = Fanclub::where('is_active', true)->findOrFail($fanclub);
        $activeMembership = FanclubMembership::where('account_id', auth()->id())
            ->where('fanclub_id', $club->id)
            ->where('joinfc_status', '加入中')
            ->whereNull('joinfc_limit')
            ->exists();

        if ($activeMembership) {
            return back()->with('error', 'このファンクラブには既に加入しています。');
        }

        FanclubMembership::updateOrCreate(
            ['account_id' => auth()->id(), 'fanclub_id' => $fanclub],
            ['joinfc_join_day' => now()->toDateString(), 'joinfc_limit' => null, 'joinfc_status' => '加入中'],
        );

        return redirect()->route('myfanclub.index')->with('success', $club->name.'に加入しました。');
    }

    public function leave(int $fanclub): RedirectResponse
    {
        $club = Fanclub::findOrFail($fanclub);

        $deleted = FanclubMembership::where('account_id', auth()->id())
            ->where('fanclub_id', $fanclub)
            ->where('joinfc_status', '加入中')
            ->whereNull('joinfc_limit')
            ->delete();

        if ($deleted === 0) {
            return redirect()->route('myfanclub.index')->with('error', '選択されたユーザーは既にこのファンクラブを退会しています。');
        }

        return redirect()->route('myfanclub.index')->with('success', 'ファンクラブへの退会が完了しました。');
    }
}
