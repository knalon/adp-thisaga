import React from 'react';

interface NoDataIllustrationProps {
    className?: string;
}

const NoDataIllustration: React.FC<NoDataIllustrationProps> = ({ className = '' }) => {
    return (
        <div className={`flex flex-col items-center justify-center p-8 ${className}`}>
            <svg
                className="w-24 h-24 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <p className="mt-4 text-lg font-medium text-gray-600">No data available</p>
            <p className="mt-2 text-sm text-gray-500">Try adjusting your filters or search criteria</p>
        </div>
    );
};

export default NoDataIllustration;
