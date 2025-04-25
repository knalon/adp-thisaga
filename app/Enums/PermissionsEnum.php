<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    case ApproveCars = 'ApproveCars';
    case ManageUsers = 'ManageUsers';
    case ManageAppointments = 'ManageAppointments';
    case ManageTransactions = 'ManageTransactions';
}
