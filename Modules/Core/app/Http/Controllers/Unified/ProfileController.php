<?php

namespace Modules\Core\Http\Controllers\Unified;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $user = auth('unified')->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => __('auth.wrong_password')]);
        }

        $user->update([
            'name'            => 'Deleted User',
            'email'           => 'deleted_' . $user->id . '@deleted.invalid',
            'password'        => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
            'gdpr_deleted'    => true,
            'gdpr_deleted_at' => now(),
            'status'          => 'banned',
        ]);

        auth('unified')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('unified.login')
            ->with('success', __('auth.account_deleted_success'));
    }

    public function exportData(Request $request)
    {
        $user = auth('unified')->user();

        $tmpDir = sys_get_temp_dir() . '/gdpr_' . $user->id . '_' . time();
        mkdir($tmpDir);

        $this->writeCsv($tmpDir . '/profile.csv', [
            ['id', 'name', 'email', 'status', 'created_at'],
            [$user->id, $user->name, $user->email, $user->status, $user->created_at],
        ]);

        $roles = $user->roles()->get(['role', 'status', 'created_at']);
        $rows = [['role', 'status', 'created_at']];
        foreach ($roles as $r) $rows[] = [$r->role, $r->status, $r->created_at];
        $this->writeCsv($tmpDir . '/roles.csv', $rows);

        $campaigns = $user->campaigns()->get(['id', 'name', 'status', 'created_at']);
        $rows = [['id', 'name', 'status', 'created_at']];
        foreach ($campaigns as $r) $rows[] = [$r->id, $r->name, $r->status, $r->created_at];
        $this->writeCsv($tmpDir . '/campaigns.csv', $rows);

        $orders = \App\Models\CampaignLink::whereHas('campaign', fn($q) => $q->where('user_id', $user->id))
            ->get(['id', 'campaign_id', 'site_id', 'placement_type', 'status', 'price_per_day', 'started_at', 'created_at']);
        $rows = [['id', 'campaign_id', 'site_id', 'placement_type', 'status', 'price_per_day', 'started_at', 'created_at']];
        foreach ($orders as $r) $rows[] = [$r->id, $r->campaign_id, $r->site_id, $r->placement_type, $r->status, $r->price_per_day, $r->started_at, $r->created_at];
        $this->writeCsv($tmpDir . '/orders.csv', $rows);

        $wallet = $user->wallet;
        if ($wallet) {
            $txs = \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                ->get(['id', 'amount', 'balance_after', 'type', 'description', 'created_at']);
            $rows = [['id', 'amount', 'balance_after', 'type', 'description', 'created_at']];
            foreach ($txs as $r) $rows[] = [$r->id, $r->amount, $r->balance_after, $r->type, $r->description, $r->created_at];
            $this->writeCsv($tmpDir . '/wallet_transactions.csv', $rows);
        }

        $articles = $user->articles()->get(['id', 'site_id', 'title', 'status', 'created_at']);
        $rows = [['id', 'site_id', 'title', 'status', 'created_at']];
        foreach ($articles as $r) $rows[] = [$r->id, $r->site_id, $r->title, $r->status, $r->created_at];
        $this->writeCsv($tmpDir . '/articles.csv', $rows);

        $sites = $user->sites()->get(['id', 'domain', 'platform_type', 'status', 'created_at']);
        if ($sites->isNotEmpty()) {
            $rows = [['id', 'domain', 'platform_type', 'status', 'created_at']];
            foreach ($sites as $r) $rows[] = [$r->id, $r->domain, $r->platform_type, $r->status, $r->created_at];
            $this->writeCsv($tmpDir . '/sites.csv', $rows);
        }

        $withdrawals = $user->withdrawals()->get(['id', 'amount', 'status', 'created_at']);
        if ($withdrawals->isNotEmpty()) {
            $rows = [['id', 'amount', 'status', 'created_at']];
            foreach ($withdrawals as $r) $rows[] = [$r->id, $r->amount, $r->status, $r->created_at];
            $this->writeCsv($tmpDir . '/withdrawals.csv', $rows);
        }

        $zipPath = $tmpDir . '/gdpr_export.zip';
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        foreach (glob($tmpDir . '/*.csv') as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        return response()->download($zipPath, 'gdpr_export_' . date('Y-m-d') . '.zip')->deleteFileAfterSend(true);
    }

    private function writeCsv(string $path, array $rows): void
    {
        $f = fopen($path, 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        foreach ($rows as $row) fputcsv($f, $row);
        fclose($f);
    }
}
