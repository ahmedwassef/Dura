<?php

namespace App\Enums;

enum PermissionName: string
{
    case ArchiveView = 'archive.view';
    case ArchiveDownload = 'archive.download';
    case FormsView = 'forms.view';
    case FormsCreate = 'forms.create';
    case FormsUpdate = 'forms.update';
    case FormsDelete = 'forms.delete';
    case PatientsSearch = 'patients.search';
    case AdminPanel = 'admin.panel';
    case AdminApproveMedReport = 'admin.approve-med-report';
    case AdminApproveDiscount = 'admin.approve-discount';
    case AdminFinancialReport = 'admin.financial-report';
    case AdminManageUsers = 'admin.manage-users';
}
