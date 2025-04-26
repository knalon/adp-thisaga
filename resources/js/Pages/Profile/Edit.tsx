import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';

export default function Edit({
    auth,
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    return (
        <AppLayout>
            <Head title="Profile" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <h2 className="text-2xl font-semibold leading-tight text-gray-800 mb-6">
                        Profile Settings
                    </h2>

                    <div className="grid grid-cols-1 gap-6">
                        <div className="bg-white p-6 shadow sm:rounded-lg sm:p-8">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">Profile Information</h3>
                            <UpdateProfileInformationForm
                                mustVerifyEmail={mustVerifyEmail}
                                status={status}
                                className="max-w-xl"
                            />
                        </div>

                        <div className="bg-white p-6 shadow sm:rounded-lg sm:p-8">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">Update Password</h3>
                            <UpdatePasswordForm className="max-w-xl" />
                        </div>

                        <div className="bg-white p-6 shadow sm:rounded-lg sm:p-8">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">Delete Account</h3>
                            <DeleteUserForm className="max-w-xl" />
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
