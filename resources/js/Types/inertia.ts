export interface AuthUser { id: number; name: string; email: string; role: 'admin' | 'manager' | 'viewer'; }
export interface PageProps { appName: string; auth: { user: AuthUser | null }; flash: { success?: string | null; error?: string | null }; requestId?: string | null; }
export interface PaginationMeta { current_page: number; per_page: number; last_page: number; total: number; has_more: boolean; }
