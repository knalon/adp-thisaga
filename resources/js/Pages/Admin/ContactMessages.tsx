import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { ChatBubbleLeftRightIcon, CheckCircleIcon, TrashIcon } from '@heroicons/react/24/outline';

interface ContactMessage {
  id: number;
  name: string;
  email: string;
  phone: string | null;
  subject: string;
  message: string;
  is_read: boolean;
  created_at: string;
}

interface ContactMessagesProps extends PageProps {
  contactMessages: {
    data: ContactMessage[];
    links: any[];
    current_page: number;
    last_page: number;
    total: number;
  };
  unreadCount: number;
}

export default function ContactMessages({ auth, contactMessages, unreadCount, flash }: ContactMessagesProps) {
  const [selectedMessages, setSelectedMessages] = useState<number[]>([]);

  const { post, processing } = useForm({
    ids: [] as number[]
  });

  const markAsRead = (id: number) => {
    router.patch(route('admin.contact-messages.mark-read', id));
  };

  const deleteMessage = (id: number) => {
    if (confirm('Are you sure you want to delete this message?')) {
      router.delete(route('admin.contact-messages.destroy', id));
    }
  };

  const markMultipleAsRead = () => {
    if (selectedMessages.length === 0) return;

    post(route('admin.contact-messages.mark-multiple-read'), {
      data: { ids: selectedMessages },
      onSuccess: () => {
        setSelectedMessages([]);
      }
    });
  };

  const toggleSelectAll = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.checked) {
      const allIds = contactMessages.data.map(message => message.id);
      setSelectedMessages(allIds);
    } else {
      setSelectedMessages([]);
    }
  };

  const toggleSelect = (id: number) => {
    if (selectedMessages.includes(id)) {
      setSelectedMessages(selectedMessages.filter(messageId => messageId !== id));
    } else {
      setSelectedMessages([...selectedMessages, id]);
    }
  };

  return (
    <AppLayout>
      <Head title="Contact Messages | Admin" />

      <div className="py-12">
        <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div className="flex justify-between items-center mb-6">
            <h1 className="text-3xl font-bold text-gray-900">Contact Messages</h1>
            <div className="flex items-center gap-2">
              <span className="badge badge-primary">{unreadCount} Unread</span>
              <Link href={route('admin.dashboard')} className="btn btn-outline btn-sm">
                Back to Dashboard
              </Link>
            </div>
          </div>

          {flash?.success && (
            <div className="alert alert-success mb-6">
              {flash.success}
            </div>
          )}

          <div className="bg-white shadow-md rounded-lg overflow-hidden">
            {contactMessages.data.length > 0 ? (
              <>
                <div className="p-4 border-b border-gray-200 bg-gray-50">
                  <div className="flex justify-between items-center">
                    <div className="flex items-center gap-4">
                      <label className="flex items-center gap-2 cursor-pointer">
                        <input
                          type="checkbox"
                          className="checkbox"
                          checked={selectedMessages.length === contactMessages.data.length}
                          onChange={toggleSelectAll}
                        />
                        <span>Select All</span>
                      </label>
                      {selectedMessages.length > 0 && (
                        <button
                          onClick={markMultipleAsRead}
                          disabled={processing}
                          className="btn btn-sm btn-primary"
                        >
                          Mark Selected as Read
                        </button>
                      )}
                    </div>
                    <div className="text-sm text-gray-500">
                      Showing {contactMessages.data.length} of {contactMessages.total} messages
                    </div>
                  </div>
                </div>

                <div className="overflow-x-auto">
                  <table className="table w-full">
                    <thead>
                      <tr>
                        <th className="w-10"></th>
                        <th className="w-10"></th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th className="w-20">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      {contactMessages.data.map((message) => (
                        <tr key={message.id} className={message.is_read ? '' : 'font-bold bg-blue-50'}>
                          <td>
                            <input
                              type="checkbox"
                              className="checkbox"
                              checked={selectedMessages.includes(message.id)}
                              onChange={() => toggleSelect(message.id)}
                            />
                          </td>
                          <td>
                            {!message.is_read && (
                              <div className="badge badge-primary badge-sm"></div>
                            )}
                          </td>
                          <td>
                            <div className="font-medium">{message.name}</div>
                            <div className="text-sm text-gray-500">{message.email}</div>
                          </td>
                          <td>
                            <Link
                              href={route('admin.contact-messages.show', message.id)}
                              className="hover:text-primary transition-colors"
                            >
                              {message.subject}
                            </Link>
                          </td>
                          <td>
                            {new Date(message.created_at).toLocaleDateString()}
                          </td>
                          <td>
                            <div className="flex gap-2">
                              {!message.is_read && (
                                <button
                                  onClick={() => markAsRead(message.id)}
                                  className="btn btn-ghost btn-sm"
                                  title="Mark as Read"
                                >
                                  <CheckCircleIcon className="h-4 w-4 text-green-600" />
                                </button>
                              )}
                              <button
                                onClick={() => deleteMessage(message.id)}
                                className="btn btn-ghost btn-sm"
                                title="Delete Message"
                              >
                                <TrashIcon className="h-4 w-4 text-red-600" />
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>

                {contactMessages.links && contactMessages.links.length > 3 && (
                  <div className="p-4 flex justify-center">
                    <div className="btn-group">
                      {contactMessages.links.map((link, i) => (
                        <Link
                          key={i}
                          href={link.url}
                          className={`btn btn-sm ${link.active ? 'btn-active' : ''} ${!link.url ? 'btn-disabled' : ''}`}
                          dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                      ))}
                    </div>
                  </div>
                )}
              </>
            ) : (
              <div className="p-12 text-center">
                <ChatBubbleLeftRightIcon className="h-16 w-16 text-gray-400 mx-auto mb-4" />
                <h3 className="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                <p className="text-gray-500 max-w-md mx-auto">
                  When visitors send contact messages, they will appear here.
                </p>
              </div>
            )}
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
