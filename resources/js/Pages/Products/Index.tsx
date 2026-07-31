import { Link } from '@inertiajs/react';
import { EmptyState } from '@/Components/Common/EmptyState';
import { PageHeader } from '@/Components/Common/PageHeader';
import { Pagination } from '@/Components/DataDisplay/Pagination';
import { StatusBadge } from '@/Components/DataDisplay/StatusBadge';
import { Button } from '@/Components/Form/Button';
import { ProductFilters } from '@/Pages/Products/Partials/ProductFilters';
import { ProductTable } from '@/Pages/Products/Partials/ProductTable';
import AppLayout from '@/Layouts/AppLayout';
import type { PaginationMeta } from '@/Types/inertia';
import type { Product } from '@/Types/product';

interface Props { products: Product[]; pagination: PaginationMeta; filters: Record<string, string | undefined>; }

const statusDescriptions = [
  {
    status: 'draft',
    title: 'Draft',
    description: 'Produk masih disiapkan. Biasanya belum siap tampil atau belum siap dijual.',
  },
  {
    status: 'active',
    title: 'Active',
    description: 'Produk sudah aktif dan siap digunakan untuk katalog atau operasional.',
  },
  {
    status: 'archived',
    title: 'Archived',
    description: 'Produk sudah diarsipkan. Data tetap aman, tapi tidak diprioritaskan untuk operasional aktif.',
  },
];

/** Halaman daftar produk: menampilkan filter, tabel produk, pagination, dan keterangan arti setiap status. */
export default function ProductsIndex({ products, pagination, filters }: Props) {
  return (
    <AppLayout>
      <div className="flex items-start justify-between">
        <PageHeader title="Products" description="Kelola data katalog produk, status, stok, dan aksi produk." />
        <Link href="/products/create"><Button>Create Product</Button></Link>
      </div>

      <div className="mb-4 grid gap-3 md:grid-cols-3">
        {statusDescriptions.map((item) => (
          <div key={item.status} className="rounded-xl bg-white p-4 shadow-sm ring-1 ring-slate-900/5">
            <div className="mb-2 flex items-center justify-between">
              <h2 className="text-sm font-bold text-slate-900">Status {item.title}</h2>
              <StatusBadge status={item.status} />
            </div>
            <p className="text-xs leading-relaxed text-slate-500">{item.description}</p>
          </div>
        ))}
      </div>

      <ProductFilters filters={filters} />
      {products.length ? <ProductTable products={products} /> : <EmptyState message="No products found." />}
      <Pagination meta={pagination} />
    </AppLayout>
  );
}
