<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportsExport implements WithMultipleSheets
{
    use Exportable;

    protected $users;
    protected $archiveFiles;
    protected $usersByRole;
    protected $totalFileSize;

    public function __construct($users, $archiveFiles, $usersByRole = [], $totalFileSize = '0 B')
    {
        $this->users = $users;
        $this->archiveFiles = $archiveFiles;
        $this->usersByRole = $usersByRole;
        $this->totalFileSize = $totalFileSize;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        // Summary Sheet
        $sheets[] = new class($this->usersByRole, $this->totalFileSize, $this->users, $this->archiveFiles) implements FromCollection, WithTitle {
            protected $usersByRole;
            protected $totalFileSize;
            protected $users;
            protected $archiveFiles;
            
            public function __construct($usersByRole, $totalFileSize, $users, $archiveFiles)
            {
                $this->usersByRole = $usersByRole;
                $this->totalFileSize = $totalFileSize;
                $this->users = $users;
                $this->archiveFiles = $archiveFiles;
            }
            
            public function collection()
            {
                $data = collect([
                    ['System Report Summary', ''],
                    ['Generated', now()->format('Y-m-d H:i:s')],
                    ['', ''],
                    ['Statistics', ''],
                    ['Total Users', $this->users->count()],
                    ['Total Archive Files', $this->archiveFiles->count()],
                    ['Total Storage Used', $this->totalFileSize],
                    ['', ''],
                    ['Users by Role', ''],
                ]);
                
                foreach ($this->usersByRole as $role => $count) {
                    $data->push([$role, $count]);
                }
                
                return $data;
            }
            
            public function title(): string
            {
                return 'Summary';
            }
        };
        
        // Users Sheet
        $sheets[] = new class($this->users) implements FromCollection, WithTitle {
            protected $users;
            
            public function __construct($users)
            {
                $this->users = $users;
            }
            
            public function collection()
            {
                $data = collect([
                    ['Name', 'Email', 'Role', 'Status', 'Created At']
                ]);
                
                foreach ($this->users as $user) {
                    $data->push([
                        $user['name'] ?? $user->decrypted_name ?? 'N/A',
                        $user['email'] ?? $user->decrypted_email ?? 'N/A',
                        $user['role'] ?? $user->role,
                        $user['status'] ?? $user->status,
                        $user['created_at'] ?? $user->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                return $data;
            }
            
            public function title(): string
            {
                return 'Users';
            }
        };
        
        // Archives Sheet
        $sheets[] = new class($this->archiveFiles) implements FromCollection, WithTitle {
            protected $archiveFiles;
            
            public function __construct($archiveFiles)
            {
                $this->archiveFiles = $archiveFiles;
            }
            
            public function collection()
            {
                $data = collect([
                    ['File Name', 'File Type', 'Size', 'Category', 'Program', 'Uploader', 'Uploader Role', 'Uploaded At']
                ]);
                
                foreach ($this->archiveFiles as $file) {
                    $data->push([
                        $file['original_name'] ?? $file->original_name,
                        $file['file_type'] ?? $file->file_type,
                        $file['file_size'] ?? $file->file_size,
                        $file['category'] ?? ($file->category ? $file->category->name : 'Uncategorized'),
                        $file['program'] ?? ($file->program ? $file->program->name : 'N/A'),
                        $file['uploader_name'] ?? ($file->uploader ? ($file->uploader->decrypted_name ?? 'Unknown') : 'Unknown'),
                        $file['uploader_role'] ?? ($file->uploader ? $file->uploader->role : 'Unknown'),
                        $file['created_at'] ?? $file->created_at->format('Y-m-d H:i:s'),
                    ]);
                }
                
                return $data;
            }
            
            public function title(): string
            {
                return 'Archives';
            }
        };
        
        return $sheets;
    }
}