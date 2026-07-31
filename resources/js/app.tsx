import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import { ToastProvider } from '@/Components/Feedback/ToastProvider';

createInertiaApp({
  title: (title) => title ? `${title} - Product Management Service` : 'Product Management Service',
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true });
    return pages[`./Pages/${name}.tsx`];
  },
  setup({ el, App, props }) {
    createRoot(el).render(<ToastProvider><App {...props} /></ToastProvider>);
  },
  progress: { color: '#2563eb' },
});
