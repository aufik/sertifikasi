export interface ApiMeta {
  success: boolean;
  code: number;
  message: string;
  request_id?: string | null;
  timestamp?: string;
  pagination?: {
    current_page: number;
    per_page: number;
    last_page: number;
    total: number;
    has_more?: boolean;
  };
}

export interface ApiResponse<T> {
  meta: ApiMeta;
  data: T;
  errors?: Record<string, string[]>;
}
