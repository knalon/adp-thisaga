<?php

namespace App\Enums;

enum PermissionsEnum: string
{
    // Public pages permissions
    case ViewHome = 'view_home';
    case ViewAboutUs = 'view_about_us';
    case ViewContactUs = 'view_contact_us';
    case ViewCarListings = 'view_car_listings';
    case ViewCarDetails = 'view_car_details';
    case Register = 'register';
    case Login = 'login';

    // Authenticated user permissions
    case ViewUserDashboard = 'view_user_dashboard';
    case ViewUserProfile = 'view_user_profile';
    case EditUserProfile = 'edit_user_profile';
    case PlaceBids = 'place_bids';
    case ScheduleAppointments = 'schedule_appointments';

    // Admin permissions
    case ViewAdminDashboard = 'view_admin_dashboard';
    case ViewAdminProfile = 'view_admin_profile';
    case EditAdminProfile = 'edit_admin_profile';
    case ManageCars = 'manage_cars';
    case ManageUsers = 'manage_users';
    case ManageAppointments = 'manage_appointments';
    case ManageTransactions = 'manage_transactions';
    case ManageBids = 'manage_bids';
    case ViewReports = 'view_reports';
}
