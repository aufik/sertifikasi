import { router } from '@inertiajs/react';
import { Input } from '@/Components/Form/Input';
import { Select } from '@/Components/Form/Select';
import { useDebounce } from '@/Hooks/useDebounce';
import { useEffect, useState } from 'react';
export function ProductFilters({ filters }: { filters: Record<string, string | undefined> }) { const [search, setSearch] = useState(filters.search ?? ''); const debounced = useDebounce(search); useEffect(() => { router.get('/products', { ...filters, search: debounced }, { preserveState: true, replace: true }); }, [debounced]); return <div className="mb-4 grid gap-3 md:grid-cols-3"><Input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search name or SKU" /><Select defaultValue={filters.status ?? ''} onChange={(e) => router.get('/products', { ...filters, status: e.target.value }, { preserveState: true })}><option value="">All Status</option><option value="active">Active</option><option value="draft">Draft</option><option value="archived">Archived</option></Select></div>; }
