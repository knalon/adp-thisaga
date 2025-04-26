import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { ArrowLeftIcon, EnvelopeIcon, PhoneIcon, TrashIcon, UserIcon } from '@heroicons/react/24/outline';
import { Inertia } from '@inertiajs/inertia';

interface ContactMessage {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  subject: string;
  message: string;
  is_read: boolean;
  created_at: string;
  updated_at: string;
  user_id: number | null;
  user?: {
    name: string;
    email: string;
  };
}

interface ContactMessageShowProps extends PageProps {
  contactMessage: ContactMessage;
}

export default function ContactMessageShow({ auth, contactMessage, flash }: ContactMessageShowProps) {
  const deleteMessage = () => {
    if (confirm('Are you sure you want to delete this message?')) {
      Inertia.delete(route('admin.contact-messages.destroy', contactMessage.id));
    }
  };

  return (
    <AppLayout>
      <Head title={`${contactMessage.subject} | Contact Message`} />

      <div className="py-12">
        <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
          <div className="flex justify-between items-center mb-6">
            <div className="flex items-center gap-3">
              <Link
                href={route('admin.contact-messages.index')}
                className="btn btn-ghost btn-sm"
              >
                <ArrowLeftIcon className="h-4 w-4" />
                <span>Back to Messages</span>
              </Link>
              <h1 className="text-2xl font-bold text-gray-900">Message Details</h1>
            </div>
            <button
              onClick={deleteMessage}
              className="btn btn-error btn-sm"
            >
              <TrashIcon className="h-4 w-4 mr-1" />
              Delete Message
            </button>
          </div>

          {flash?.success && (
            <div className="alert alert-success mb-6">
              {flash.success}
            </div>
          )}

          <div className="bg-white shadow-md rounded-lg overflow-hidden">
            <div className="p-6 border-b border-gray-200">
              <h2 className="text-xl font-bold mb-1">{contactMessage.subject}</h2>
              <div className="text-sm text-gray-500">
                Received on {new Date(contactMessage.created_at).toLocaleString()}
              </div>
            </div>

            <div className="p-6 border-b border-gray-200 bg-gray-50">
              <div className="flex flex-col md:flex-row gap-6">
                <div className="md:w-1/3">
                  <h3 className="text-lg font-medium mb-4">Sender Information</h3>
                  <div className="space-y-3">
                    <div className="flex items-start gap-2">
                      <UserIcon className="h-5 w-5 text-gray-400 mt-0.5" />
                      <div>
                        <div className="font-medium">{contactMessage.name}</div>
                        {contactMessage.user && (
                          <div className="text-xs text-primary">Registered User</div>
                        )}
                      </div>
                    </div>
                    <div className="flex items-center gap-2">
                      <EnvelopeIcon className="h-5 w-5 text-gray-400" />
                      <a href={`mailto:${contactMessage.email}`} className="text-primary hover:underline">
                        {contactMessage.email}
                      </a>
                    </div>
                    {contactMessage.phone && (
                      <div className="flex items-center gap-2">
                        <PhoneIcon className="h-5 w-5 text-gray-400" />
                        <a href={`tel:${contactMessage.phone}`} className="text-primary hover:underline">
                          {contactMessage.phone}
                        </a>
                      </div>
                    )}
                  </div>
                </div>
                <div className="md:w-2/3">
                  <h3 className="text-lg font-medium mb-4">Message</h3>
                  <div className="bg-white p-4 rounded-lg border border-gray-200 whitespace-pre-wrap">
                    {contactMessage.message}
                  </div>
                </div>
              </div>
            </div>

            <div className="p-6 flex justify-between">
              <Link
                href={route('admin.contact-messages.index')}
                className="btn btn-outline btn-sm"
              >
                Back to All Messages
              </Link>
              {contactMessage.email && (
                <a
                  href={`mailto:${contactMessage.email}?subject=Re: ${contactMessage.subject}`}
                  className="btn btn-primary btn-sm"
                >
                  <EnvelopeIcon className="h-4 w-4 mr-1" />
                  Reply via Email
                </a>
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
