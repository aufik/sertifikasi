export const endpoints = {
  products: {
    list: '/products',
    trash: '/products/trash',
    show: (id: string) => `/products/${id}`,
    create: '/products',
    update: (id: string) => `/products/${id}`,
    delete: (id: string) => `/products/${id}`,
    restore: (id: string) => `/products/${id}/restore`,
    purge: (id: string) => `/products/${id}/purge`,
    restock: (id: string) => `/products/${id}/restock`,
  },
} as const;
