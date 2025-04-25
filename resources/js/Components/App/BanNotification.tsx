import React from 'react';
import { Link } from '@inertiajs/react';
import { ExclamationTriangleIcon } from '@heroicons/react/24/solid';

type BanNotificationProps = {
  compact?: boolean;
};

const BanNotification: React.FC<BanNotificationProps> = ({ compact = false }) => {
  if (compact) {
    return (
      <div className="bg-red-600 text-white p-2 shadow-md rounded-md flex items-center justify-between">
        <div className="flex items-center">
          <ExclamationTriangleIcon className="h-5 w-5 mr-2" />
          <span className="text-sm font-medium">Your account has been suspended</span>
        </div>
        <Link href={route('contact')} className="text-xs bg-white text-red-600 py-1 px-2 rounded hover:bg-red-100">
          Contact Support
        </Link>
      </div>
    );
  }

  return (
    <div className="bg-red-600 text-white p-4 shadow-md rounded-md mb-6">
      <div className="flex items-start">
        <ExclamationTriangleIcon className="h-6 w-6 mr-3 mt-0.5" />
        <div className="flex-1">
          <h3 className="text-lg font-bold mb-2">Account Suspended</h3>
          <p className="mb-3">
            Your account has been suspended and your access to certain features has been restricted. 
            While suspended, you cannot list new vehicles, update existing listings, schedule appointments, 
            or complete transactions.
          </p>
          <p className="mb-4">
            If you believe this was done in error or wish to appeal this decision, please contact our support team.
          </p>
          <Link 
            href={route('contact')} 
            className="inline-block bg-white text-red-600 font-medium py-2 px-4 rounded hover:bg-red-100"
          >
            Contact Support
          </Link>
        </div>
      </div>
    </div>
  );
};

export default BanNotification; 