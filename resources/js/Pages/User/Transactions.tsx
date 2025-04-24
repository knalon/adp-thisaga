import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps, Transaction } from '@/types';

interface Props extends Record<string, unknown> {
  transactions: {
    data: Transaction[];
  };
}

export default function UserTransactions({ transactions }: PageProps<Props>) {
  // Format date to local date and time string
  const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString();
  };

  // Format currency
  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
    }).format(amount);
  };

  return (
    <AppLayout>
      <Head title="My Transactions" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-6">My Transactions</h1>

          <div className="bg-white shadow rounded-lg overflow-hidden">
            <div className="p-6 bg-white border-b border-gray-200">
              <h2 className="text-lg font-medium text-gray-900">Transaction History</h2>
              <p className="text-sm text-gray-500 mt-1">
                View your purchase history and transaction details
              </p>
            </div>
            <div className="overflow-x-auto">
              {transactions.data.length > 0 ? (
                <table className="table w-full">
                  <thead>
                    <tr>
                      <th>Reference</th>
                      <th>Car</th>
                      <th>Amount</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {transactions.data.map((transaction) => (
                      <tr key={transaction.id}>
                        <td className="font-mono text-sm">
                          {transaction.transaction_reference}
                        </td>
                        <td>
                          <Link 
                            href={route('cars.show', transaction.car?.slug)} 
                            className="hover:underline"
                          >
                            {transaction.car?.make} {transaction.car?.model} ({transaction.car?.registration_year})
                          </Link>
                        </td>
                        <td className="font-bold text-secondary">
                          {formatCurrency(transaction.final_price)}
                        </td>
                        <td>{formatDateTime(transaction.created_at)}</td>
                        <td>
                          <span className="badge badge-success">Completed</span>
                        </td>
                        <td>
                          <div className="flex space-x-2">
                            <Link 
                              href={route('user.transactions.show', transaction.id)} 
                              className="btn btn-sm btn-primary"
                            >
                              View Details
                            </Link>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              ) : (
                <div className="p-6 text-center">
                  <p className="text-gray-500">You don't have any transactions yet.</p>
                  <Link 
                    href={route('cars.index')} 
                    className="btn btn-primary mt-4"
                  >
                    Browse Cars
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
} 