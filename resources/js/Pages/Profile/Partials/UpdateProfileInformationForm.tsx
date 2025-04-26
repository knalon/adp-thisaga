import InputError from '@/Components/Core/InputError';
import InputLabel from '@/Components/Core/InputLabel';
import PrimaryButton from '@/Components/Core/PrimaryButton';
import TextInput from '@/Components/Core/TextInput';
import { Transition } from '@headlessui/react';
import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler, useState } from 'react';

export default function UpdateProfileInformation({
    mustVerifyEmail,
    status,
    className = '',
}: {
    mustVerifyEmail: boolean;
    status?: string;
    className?: string;
}) {
    const user = usePage().props.auth.user;
    const [photoPreview, setPhotoPreview] = useState<string | null>(null);

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            name: user.name,
            email: user.email,
            phone: user.phone || '',
            address: user.address || '',
            city: user.city || '',
            state: user.state || '',
            postal_code: user.postal_code || '',
            country: user.country || '',
            profile_photo: null as File | null,
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();

        patch(route('profile.update'));
    };

    const handlePhotoChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            setData('profile_photo', file);

            // Preview
            const reader = new FileReader();
            reader.onload = (e) => {
                setPhotoPreview(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    return (
        <section className={className}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Profile Information
                </h2>

                <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your account's profile information and email address.
                </p>
            </header>

            <form onSubmit={submit} className="mt-6 space-y-6">
                <div className="space-y-6">
                    {/* Profile Photo */}
                    <div>
                        <InputLabel htmlFor="profile_photo" value="Profile Photo" />

                        <div className="mt-2 flex items-center space-x-4">
                            <div className="h-16 w-16 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                {photoPreview ? (
                                    <img src={photoPreview} alt="Profile Preview" className="h-full w-full object-cover" />
                                ) : user.profile_photo_url ? (
                                    <img src={user.profile_photo_url} alt={user.name} className="h-full w-full object-cover" />
                                ) : (
                                    <span className="text-gray-400 text-2xl">{user.name.charAt(0)}</span>
                                )}
                            </div>

                            <input
                                type="file"
                                id="profile_photo"
                                name="profile_photo"
                                accept="image/*"
                                className="hidden"
                                onChange={handlePhotoChange}
                            />

                            <label
                                htmlFor="profile_photo"
                                className="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Select New Photo
                            </label>
                        </div>

                        <InputError className="mt-2" message={errors.profile_photo} />
                    </div>

                    {/* Name */}
                    <div>
                        <InputLabel htmlFor="name" value="Name" />

                        <TextInput
                            id="name"
                            className="mt-1 block w-full"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            required
                            autoComplete="name"
                        />

                        <InputError className="mt-2" message={errors.name} />
                    </div>

                    {/* Email */}
                    <div>
                        <InputLabel htmlFor="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            className="mt-1 block w-full"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            required
                            autoComplete="username"
                        />

                        <InputError className="mt-2" message={errors.email} />
                    </div>

                    {/* Phone */}
                    <div>
                        <InputLabel htmlFor="phone" value="Phone" />

                        <TextInput
                            id="phone"
                            type="tel"
                            className="mt-1 block w-full"
                            value={data.phone}
                            onChange={(e) => setData('phone', e.target.value)}
                            autoComplete="tel"
                        />

                        <InputError className="mt-2" message={errors.phone} />
                    </div>

                    {/* Address */}
                    <div>
                        <InputLabel htmlFor="address" value="Address" />

                        <TextInput
                            id="address"
                            className="mt-1 block w-full"
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                            autoComplete="street-address"
                        />

                        <InputError className="mt-2" message={errors.address} />
                    </div>

                    {/* City, State, Postal Code */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <InputLabel htmlFor="city" value="City" />

                            <TextInput
                                id="city"
                                className="mt-1 block w-full"
                                value={data.city}
                                onChange={(e) => setData('city', e.target.value)}
                                autoComplete="address-level2"
                            />

                            <InputError className="mt-2" message={errors.city} />
                        </div>

                        <div>
                            <InputLabel htmlFor="state" value="State/Province" />

                            <TextInput
                                id="state"
                                className="mt-1 block w-full"
                                value={data.state}
                                onChange={(e) => setData('state', e.target.value)}
                                autoComplete="address-level1"
                            />

                            <InputError className="mt-2" message={errors.state} />
                        </div>

                        <div>
                            <InputLabel htmlFor="postal_code" value="Postal Code" />

                            <TextInput
                                id="postal_code"
                                className="mt-1 block w-full"
                                value={data.postal_code}
                                onChange={(e) => setData('postal_code', e.target.value)}
                                autoComplete="postal-code"
                            />

                            <InputError className="mt-2" message={errors.postal_code} />
                        </div>
                    </div>

                    {/* Country */}
                    <div>
                        <InputLabel htmlFor="country" value="Country" />

                        <TextInput
                            id="country"
                            className="mt-1 block w-full"
                            value={data.country}
                            onChange={(e) => setData('country', e.target.value)}
                            autoComplete="country-name"
                        />

                        <InputError className="mt-2" message={errors.country} />
                    </div>
                </div>

                {mustVerifyEmail && user.email_verified_at === null && (
                    <div>
                        <p className="mt-2 text-sm text-gray-800 dark:text-gray-200">
                            Your email address is unverified.
                            <Link
                                href={route('verification.send')}
                                method="post"
                                as="button"
                                className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                            >
                                Click here to re-send the verification email.
                            </Link>
                        </p>

                        {status === 'verification-link-sent' && (
                            <div className="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                                A new verification link has been sent to your
                                email address.
                            </div>
                        )}
                    </div>
                )}

                <div className="flex items-center gap-4">
                    <PrimaryButton disabled={processing}>Save</PrimaryButton>

                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out"
                        enterFrom="opacity-0"
                        leave="transition ease-in-out"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-gray-600 dark:text-gray-400">
                            Saved.
                        </p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}
