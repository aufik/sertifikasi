import { Sidebar } from '@/Components/Navigation/Sidebar';
import { Topbar } from '@/Components/Navigation/Topbar';
import { useFlashToast } from '@/Hooks/useFlashToast';
import type { PropsWithChildren } from 'react';
export default function AppLayout({ children }: PropsWithChildren) { useFlashToast(); return <div className="flex min-h-screen bg-slate-50"><Sidebar /><main className="flex-1"><Topbar /><div className="p-6">{children}</div></main></div>; }
