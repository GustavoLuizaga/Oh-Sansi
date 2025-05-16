<?php

namespace App\Http\Controllers\BackupController;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
class BackupController extends Controller
{
    public function index()
    {
        $logs = AuditLog::all(); // Trae todos los registros

        return view('backupAdmin.backup', compact('logs'));
     
    }

    public function createBackup() {}
}
