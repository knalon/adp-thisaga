import { Link } from '@inertiajs/react';
import { PropsWithChildren } from 'react';
import { ChevronLeftIcon } from '@heroicons/react/24/outline';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="min-h-screen flex flex-col bg-gradient-to-b from-primary/20 to-gray-100 py-6 sm:py-12">
            <div className="container mx-auto max-w-md px-4">
                {/* Back to home button */}
                <div className="absolute top-4 left-4">
                    <Link
                        href="/"
                        className="flex items-center text-primary hover:text-primary-focus transition-colors"
                    >
                        <ChevronLeftIcon className="h-5 w-5" />
                        <span className="ml-1 text-sm font-medium">Back to Home</span>
                    </Link>
                </div>

                {/* Logo */}
                <div className="text-center mb-6">
                    <Link href="/">
                        <h1 className="text-3xl font-bold">
                            <span className="text-secondary">A</span>
                            <span className="text-primary">BC</span>
                            <span className="text-accent"> Cars</span>
                        </h1>
                    </Link>
                </div>

                {/* Main content */}
                <div className="bg-white rounded-xl shadow-lg overflow-hidden">
                    {children}
                </div>
            </div>
        </div>
    );
}
