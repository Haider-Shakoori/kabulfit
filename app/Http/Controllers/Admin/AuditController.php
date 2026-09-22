<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\View\View;

class AuditController extends Controller
{
    public function __invoke(): View
    {
        $this->authorize('viewAny', AuditLog::class);

        return view('admin.audit.index', [
            'logs' => AuditLog::query()->with('actor')->latest('created_at')->paginate(50),
            'seo' => PrivatePageSeo::make('Admin Audit Log', route('admin.audit.index', ['locale' => app()->getLocale()])),
        ]);
    }
}
