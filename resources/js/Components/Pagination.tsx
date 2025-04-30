import React from 'react';
import { Link } from '@inertiajs/react';

interface PaginationProps {
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

const Pagination: React.FC<PaginationProps> = ({ links }) => {
    return (
        <div className="flex justify-center mt-4">
            <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                {links.map((link, key) => {
                    if (!link.url) {
                        return (
                            <span
                                key={key}
                                className={`relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ${
                                    link.active
                                        ? 'z-10 bg-primary border-primary text-white'
                                        : 'text-gray-500'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        );
                    }

                    return (
                        <Link
                            key={key}
                            href={link.url}
                            className={`relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium ${
                                link.active
                                    ? 'z-10 bg-primary border-primary text-white'
                                    : 'text-gray-500 hover:bg-gray-50'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                })}
            </nav>
        </div>
    );
};

export default Pagination;
