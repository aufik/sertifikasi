import type { PropsWithChildren } from 'react';

/** Wrapper untuk halaman guest seperti halaman Login. */
export default function GuestLayout({ children }: PropsWithChildren) {
  return (
    <main className="min-h-screen bg-slate-900">
      {children}
    </main>
  );
}
