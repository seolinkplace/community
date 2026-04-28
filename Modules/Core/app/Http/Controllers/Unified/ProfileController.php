<?php

namespace Modules\Core\Http\Controllers\Unified;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Core\Services\GdprDeletionService;

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

        (new GdprDeletionService())->anonymize($user);

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

        $this->writeCsvCursor(
            $tmpDir . '/campaigns.csv',
            ['id', 'name', 'status', 'created_at'],
            $user->campaigns()->select(['id', 'name', 'status', 'created_at'])->cursor(),
            fn($r) => [$r->id, $r->name, $r->status, $r->created_at]
        );

        $this->writeCsvCursor(
            $tmpDir . '/orders.csv',
            ['id', 'campaign_id', 'site_id', 'placement_type', 'status', 'price_per_day', 'started_at', 'created_at'],
            \App\Models\CampaignLink::whereHas('campaign', fn($q) => $q->where('user_id', $user->id))
                ->select(['id', 'campaign_id', 'site_id', 'placement_type', 'status', 'price_per_day', 'started_at', 'created_at'])
                ->cursor(),
            fn($r) => [$r->id, $r->campaign_id, $r->site_id, $r->placement_type, $r->status, $r->price_per_day, $r->started_at, $r->created_at]
        );

        $wallet = $user->wallet;
        if ($wallet) {
            $this->writeCsvCursor(
                $tmpDir . '/wallet_transactions.csv',
                ['id', 'amount', 'balance_after', 'type', 'description', 'created_at'],
                \App\Models\WalletTransaction::where('wallet_id', $wallet->id)
                    ->select(['id', 'amount', 'balance_after', 'type', 'description', 'created_at'])
                    ->cursor(),
                fn($r) => [$r->id, $r->amount, $r->balance_after, $r->type, $r->description, $r->created_at]
            );
        }

        $this->writeCsvCursor(
            $tmpDir . '/articles.csv',
            ['id', 'site_id', 'title', 'status', 'created_at'],
            $user->articles()->select(['id', 'site_id', 'title', 'status', 'created_at'])->cursor(),
            fn($r) => [$r->id, $r->site_id, $r->title, $r->status, $r->created_at]
        );

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

    private function writeCsvCursor(string $path, array $headers, iterable $cursor, callable $mapper): void
    {
        $f = fopen($path, 'w');
        fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($f, $headers);
        foreach ($cursor as $row) {
            fputcsv($f, $mapper($row));
        }
        fclose($f);
    }
}
