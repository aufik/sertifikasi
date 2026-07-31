export type ProductStatus = 'draft' | 'active' | 'archived';
export interface Product { id: string; category_id: string; name: string; sku: string; description: string; price: number; stock: number; status: ProductStatus; created_at: string | null; updated_at: string | null; }
export interface Category { id: string; name: string; slug: string; type: 'physical' | 'digital' | 'service'; }
