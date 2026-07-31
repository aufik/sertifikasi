import { Link, router } from '@inertiajs/react';
import { DataTable } from '@/Components/DataDisplay/DataTable';
import { StatusBadge } from '@/Components/DataDisplay/StatusBadge';
import { formatCurrency } from '@/Lib/formatCurrency';
import { usePermission } from '@/Hooks/usePermission';
import type { Product } from '@/Types/product';
export function ProductTable({ products }: { products: Product[] }) { const { canDelete, canMutate } = usePermission(); return <DataTable><thead className="bg-slate-50"><tr>{['Name','SKU','Status','Price','Stock','Actions'].map((h) => <th key={h} className="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">{h}</th>)}</tr></thead><tbody className="divide-y divide-slate-100">{products.map((p) => <tr key={p.id}><td className="px-4 py-3 font-medium">{p.name}</td><td className="px-4 py-3">{p.sku}</td><td className="px-4 py-3"><StatusBadge status={p.status} /></td><td className="px-4 py-3">{formatCurrency(p.price)}</td><td className="px-4 py-3">{p.stock}</td><td className="space-x-2 px-4 py-3 text-sm"><Link href={`/products/${p.id}`}>Show</Link>{canMutate && <Link href={`/products/${p.id}/edit`}>Edit</Link>}{canDelete && <button className="text-red-600" onClick={() => window.confirm('Delete product?') && router.delete(`/products/${p.id}`)}>Delete</button>}</td></tr>)}</tbody></DataTable>; }
