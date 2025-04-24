import ApplicationLogo from '@/Components/App/ApplicationLogo';
import Dropdown from '@/Components/Core/Dropdown';
import NavLink from '@/Components/Core/NavLink';
import ResponsiveNavLink from '@/Components/Core/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren, ReactNode, useState } from 'react';
import Navbar from '@/Components/App/Navbar';
import { useEffect, useRef } from 'react';

interface SuccessMessage {
  message: string;
  time: number;
  id: number;
}

export default function AuthenticatedLayout(
  {
    header,
    children,
}: PropsWithChildren<{ header?: ReactNode }>) {
    const { props } = usePage();
    const user = props.auth?.user;

    const [successMessages, setSuccessMessages] = useState<SuccessMessage[]>([]);
    const timeoutRefs = useRef<{ [key: number]: ReturnType<typeof setTimeout> }>({}); 

    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

        useEffect(() => {
      const success = props.success as SuccessMessage | undefined;
      if (success?.message) {
        const newMessage: SuccessMessage = {
          message: success.message,
          time: success.time || Date.now(),
          id: success.time || Date.now(),
            };

            setSuccessMessages((prevMessages) => [newMessage, ...prevMessages]);

            const timeoutId = setTimeout(() => {
              setSuccessMessages((prevMessages) => 
                prevMessages.filter((msg) => msg.id !== newMessage.id)
            );

            delete timeoutRefs.current[newMessage.id];
            }, 5000);

            timeoutRefs.current[newMessage.id] = timeoutId;
          }
          }, [props.success]);

    // Get the error message in a way that can be safely rendered
    const errorMessage = props.error ? String(props.error) : null;

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <Navbar/>

            {errorMessage && (
              <div className="container mx-auto px-8 mt-8">
                <div className="alert alert-error">
                  {errorMessage}
                  </div>
              </div>
            )}

            {successMessages.length > 0 && (
              <div className="toast toast-top toast-end z-[1000] mt-16">
                {successMessages.map((msg) => (
                  <div className="alert alert-success" key={msg.id}>
                    <span>{msg.message}</span>
                  </div>
                ))}
              </div>
            )}

            <main>{children}</main>
        </div>
    );
}
