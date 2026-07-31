import { Link } from '@inertiajs/react';
import { PageHeader } from '@/Components/Common/PageHeader';
import { StatusBadge } from '@/Components/DataDisplay/StatusBadge';
import { Button } from '@/Components/Form/Button';
import { formatCurrency } from '@/Lib/formatCurrency';
import AppLayout from '@/Layouts/AppLayout';
import type { Product } from '@/Types/product';
export default function ProductsShow({ product }: { product: Product }) { return <AppLayout><PageHeader title={product.name} description={product.sku} /><div className="rounded-xl bg-white p-6 shadow-sm"><dl className="grid gap-4 md:grid-cols-2"><div><dt>Status</dt><dd><StatusBadge status={product.status} /></dd></div><div><dt>Price</dt><dd>{formatCurrency(product.price)}</dd></div><div><dt>Stock</dt><dd>{product.stock}</dd></div><div><dt>Description</dt><dd>{product.description}</dd></div></dl><Link href={`/products/${product.id}/edit`}><Button className="mt-6">Edit</Button></Link></div></AppLayout>; }
