import ReactDOMServer from 'react-dom/server';
import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route } from '../../vendor/tightenco/ziggy/dist/index.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

interface Ziggy {
  location: string;
  [key: string]: any;
}

createServer((page) =>
    createInertiaApp({
        page,
        render: ReactDOMServer.renderToString,
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.tsx`, import.meta.glob('./Pages/**/*.tsx')),
        setup: ({ App, props }) => {
            const ziggy = page.props.ziggy as Ziggy;
            
            (global.route as any) = (name: string, params: any, absolute: boolean) =>
                route(name, params, absolute, {
                    ...ziggy,
                    location: new URL(ziggy.location),
                });

            return <App {...props} />;
        },
    })
);
