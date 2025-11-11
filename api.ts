import axios from 'axios';

// API Base URL - Update this to match your PHP backend
const API_BASE_URL = 'http://159.65.2.46:8000/api';

// Create axios instance
const api = axios.create({
    baseURL: API_BASE_URL,
    // withCredentials: true,
    timeout: 10000,
    headers: {
        'Content-Type': 'application/json',
    },
});


// Add request interceptor to include JWT token
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
            const bearerToken = token.startsWith('Bearer ') ? token : `Bearer ${token}`;
            config.headers.Authorization = bearerToken;
        }
        
        console.log('API Request:', {
            url: config.url,
            method: config.method,
            baseURL: config.baseURL,
            data: config.data,
            headers: config.headers
        });
        
        return config;
    },
    (error) => {
        console.error('Request interceptor error:', error);
        return Promise.reject(error);
    }
);

// Response interceptor to handle token expiration and errors
api.interceptors.response.use(
    (response) => {
        console.log('API Response:', {
            url: response.config.url,
            method: response.config.method,
            status: response.status,
            data: response.data
        });
        return response.data;
    },
    (error) => {
        console.error('API Error:', {
            url: error.config?.url,
            method: error.config?.method,
            status: error.response?.status,
            statusText: error.response?.statusText,
            data: error.response?.data,
            message: error.message
        });
        
        // Handle 401 unauthorized errors
        if (error.response?.status === 401) {
            // Token expired or invalid
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            window.location.href = '/login';
        }

        // Handle other errors
        if (error.response) {
            // Server responded with error status
            const message = error.response.data?.message || 'An error occurred';
            const customError: any = new Error(message);
            customError.response = error.response;
            throw customError;
        } else if (error.request) {
            // Request was made but no response received
            throw new Error('Network error - please check your connection');
        } else {
            // Something else happened
            throw new Error(error.message);
        }
    }
);

// Helper function to normalize detail_images
export const normalizeDetailImages = (detailImages: string[] | string): string[] => {
    if (typeof detailImages === 'string') {
        try {
            const parsed = JSON.parse(detailImages);
            return Array.isArray(parsed) ? parsed : [];
        } catch {
            return [];
        }
    }
    return Array.isArray(detailImages) ? detailImages : [];
};

// API Types
export interface Product {
    id: number;
    code: string;
    name: string;
    price: number;
    description: string;
    quantity: number;
    specifications: {
        brand?: string;
        origin?: string;
        made_in?: string;
        volume?: string;
        skin_type?: string;
    };
    usage: string;
    ingredients: string;
    category_id: number;
    category_name?: string;
    main_image?: string;
    detail_images: string[] | string; // Can be array or JSON string from MySQL
    created_at: string;
    updated_at: string;
    deleted_at?: string;
}

export interface Category {
    id: number;
    name: string;
    description?: string;
    created_at: string;
    updated_at: string;
    deleted_at?: string;
}

export interface DashboardStats {
    products: {
        total: number;
        change: string;
        change_type: string;
    };
    categories: {
        total: number;
        change: string;
        change_type: string;
    };
    orders: {
        total: number;
        change: string;
        change_type: string;
    };
    customers: {
        total: number;
        change: string;
        change_type: string;
    };
}

export interface BestSellingProduct {
    id: number;
    name: string;
    category: string;
    sold: number;
    change: string;
}

export interface RecentActivity {
    id: number;
    action: string;
    time: string;
    type: 'create' | 'update' | 'delete';
}

export interface User {
    id: number;
    name: string;
    email: string;
    gender: string;
    phone: string;
    address: string;
    birthday?: string;
    avatar?: string;
    created_at: string;
    updated_at: string;
}

export interface Employee {
    id: number;
    account_id: number;
    full_name: string;
    phone?: string;
    address?: string;
    birthday?: string;
    gender?: string;
    email: string;
    role?: string;
    account_status: string;
    account_created: string;
    created_at: string;
    updated_at: string;
}

export interface CreateUserData {
    full_name: string;
    email?: string;
    gender: string;
    phone: string;
    address: string;
    birthday?: string;
    avatar?: string;
}

export interface UpdateUserData {
    full_name?: string;
    email?: string;
    gender?: string;
    phone?: string;
    address?: string;
    birthday?: string;
    avatar?: string;
}

export interface PasswordData {
    current_password: string;
    new_password: string;
    confirm_password: string;
}

export interface LoginUser {
    id: number;
    email: string;
    full_name: string;
    role: string;
    account_status: string;
}

export interface LoginResponse {
    token: string;
    user: LoginUser;
}

export interface ApiResponse<T> {
    success: boolean;
    message: string;
    data: T;
}

export interface OrderItem {
    id: number;
    product_id: number;
    quantity: number;
    price: number;
    product_name: string;
    product_image?: string;
}

export interface Order {
    id: number;
    user_id: number;
    total_amount: number;
    order_code: string;
    status: 'pending' | 'paid' | 'shipped' | 'completed' | 'cancelled';
    shipping_address: string;
    payment_method: 'cod' | 'vnpay';
    created_at: string;
    updated_at: string;
    order_items: OrderItem[];
    // User information
    receiver_name?: string;
    receiver_phone?: string;
}

export interface OrderPagination {
    current_page: number;
    total_pages: number;
    total_records: number;
    limit: number;
    has_next: boolean;
    has_prev: boolean;
}

export interface OrderListResponse {
    orders: Order[];
    pagination: OrderPagination;
}

export interface OrderSearchParams {
    q?: string;
    status?: 'pending' | 'paid' | 'shipped' | 'completed' | 'cancelled';
    start_date?: string;
    end_date?: string;
    page?: number;
    limit?: number;
}

export interface CreateOrderData {
    user_id: number;
    shipping_address: string;
    payment_method: 'cod' | 'vnpay';
    order_items: {
        product_id: number;
        quantity: number;
    }[];
}

export interface UpdateOrderData {
    id: number;
    status?: 'pending' | 'paid' | 'shipped' | 'completed' | 'cancelled';
    shipping_address?: string;
}

// Product API
export const productApi = {
    // Get all products with pagination
    getAll: (params?: {
        page?: number;
        limit?: number;
        search?: string;
        category_id?: number;
        sort_by?: string;
        sort_order?: 'ASC' | 'DESC';
    }): Promise<ApiResponse<{
        data: Product[];
        pagination: {
            current_page: number;
            total_pages: number;
            total_items: number;
            per_page: number;
            has_next: boolean;
            has_prev: boolean;
        };
    }>> => {
        return api.get('/products', { params });
    },

    // Get product by ID
    getById: (id: number): Promise<ApiResponse<Product>> => {
        return api.get(`/products/${id}`);
    },

    // Create new product
    create: (product: Omit<Product, 'id' | 'created_at' | 'updated_at'> | FormData): Promise<ApiResponse<null>> => {
        return api.post('/products', product);
    },

    // Update product
    update: (id: number, product: Partial<Product> | FormData): Promise<ApiResponse<null>> => {
        return api.put(`/products/${id}`, product);
    },

    // Delete product (soft delete)
    delete: (id: number): Promise<ApiResponse<null>> => {
        return api.delete(`/products/${id}`);
    },

    // Restore deleted product
    restore: (id: number): Promise<ApiResponse<null>> => {
        return api.post(`/products/${id}/restore`);
    },

    // Get deleted products
    getDeleted: (): Promise<ApiResponse<Product[]>> => {
        return api.get('/products/deleted');
    },

    // Get products by category
    getByCategory: (categoryId: number): Promise<ApiResponse<Product[]>> => {
        return api.get(`/products/category/${categoryId}`);
    },
};

// Category API
export const categoryApi = {
    // Get all categories
    getAll: (): Promise<ApiResponse<Category[]>> => {
        return api.get('/categories');
    },

    // Get category by ID
    getById: (id: number): Promise<ApiResponse<Category>> => {
        return api.get(`/categories/${id}`);
    },

    // Create new category
    create: (category: Omit<Category, 'id' | 'created_at' | 'updated_at'>): Promise<ApiResponse<null>> => {
        return api.post('/categories', category);
    },

    // Update category
    update: (id: number, category: Partial<Category>): Promise<ApiResponse<null>> => {
        return api.put(`/categories/${id}`, category);
    },

    // Delete category (soft delete)
    delete: (id: number): Promise<ApiResponse<null>> => {
        return api.delete(`/categories/${id}`);
    },

    // Restore deleted category
    restore: (id: number): Promise<ApiResponse<null>> => {
        return api.post(`/categories/${id}/restore`);
    },

    // Get deleted categories
    getDeleted: (): Promise<ApiResponse<Category[]>> => {
        return api.get('/categories/deleted');
    },
};

// Dashboard API
export const dashboardApi = {
    // Get dashboard statistics
    getStats: (): Promise<ApiResponse<DashboardStats>> => {
        return api.get('/dashboard/stats');
    },

    // Get best selling products
    getBestSelling: (): Promise<ApiResponse<BestSellingProduct[]>> => {
        return api.get('/dashboard/best-selling');
    },

    // Get recent activity
    getRecentActivity: (): Promise<ApiResponse<RecentActivity[]>> => {
        return api.get('/dashboard/recent-activity');
    },

    // Get category distribution
    getCategoryDistribution: (): Promise<ApiResponse<Array<{ name: string, value: number, color: string }>>> => {
        return api.get('/dashboard/category-distribution');
    },

    // Get monthly stats
    getMonthlyStats: (params?: { start_date?: string, end_date?: string }): Promise<ApiResponse<Array<{ name: string, value: number }>>> => {
        return api.get('/dashboard/monthly-stats', { params });
    },

    // Get customer stats
    getCustomerStats: (params?: { start_date?: string, end_date?: string }): Promise<ApiResponse<{
        total_customers: number;
        new_customers: number;
        returning_customers: number;
        customer_growth: string;
        top_cities: Array<{ name: string, count: number }>;
    }>> => {
        return api.get('/dashboard/customer-stats', { params });
    },

    // Get revenue stats
    getRevenueStats: (params?: { start_date?: string, end_date?: string }): Promise<ApiResponse<{
        total_revenue: number;
        monthly_revenue: number;
        revenue_growth: string;
        average_order_value: number;
        top_products: Array<{ name: string, revenue: number }>;
        revenue_by_month: Array<{ month: string, revenue: number }>;
    }>> => {
        return api.get('/dashboard/revenue-stats', { params });
    },
};

// User API
export const userApi = {
    // Get all users
    getAll: (): Promise<ApiResponse<User[]>> => {
        return api.get('/users');
    },

    // Get user by ID
    getById: (id: number): Promise<ApiResponse<User>> => {
        return api.get(`/users/${id}`);
    },

    // Create new user
    create: (user: CreateUserData): Promise<ApiResponse<null>> => {
        return api.post('/users', user);
    },

    // Update user
    update: (id: number, user: UpdateUserData): Promise<ApiResponse<null>> => {
        return api.put(`/users/${id}`, user);
    },

    // Delete user (soft delete)
    delete: (id: number): Promise<ApiResponse<null>> => {
        return api.delete(`/users/${id}`);
    },

    // Restore deleted user
    restore: (id: number): Promise<ApiResponse<null>> => {
        return api.post(`/users/${id}/restore`);
    },

    // Get deleted users
    getDeleted: (): Promise<ApiResponse<User[]>> => {
        return api.get('/users/deleted');
    },

    // Change user password
    changePassword: (id: number, passwordData: PasswordData): Promise<ApiResponse<null>> => {
        return api.post(`/users/${id}/change-password`, passwordData);
    },
};

// Image Upload API
export const imageUploadApi = {
    // Upload single image
    uploadSingle: (file: File): Promise<ApiResponse<{ url: string, filename: string, size: number, type: string }>> => {
        const formData = new FormData();
        formData.append('image', file);
        return api.post('/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },

    // Upload multiple images
    uploadMultiple: (files: File[]): Promise<ApiResponse<{ images: Array<{ url: string, filename: string, size: number, type: string }>, count: number, warnings?: string[] }>> => {
        const formData = new FormData();

        // IMPORTANT: Use 'images[]' for PHP to recognize as array
        files.forEach((file) => {
            formData.append('images[]', file);
        });

        return api.post('/upload/multiple', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },

    // Delete image
    deleteImage: (filename: string): Promise<ApiResponse<null>> => {
        return api.delete(`/upload/${filename}`);
    },
};

// Order API
export const orderApi = {
    // Get all orders
    getAll: (): Promise<ApiResponse<Order[]>> => {
        return api.get('/orders');
    },

    // Get orders with pagination and filters
    getPaginated: (params?: OrderSearchParams): Promise<ApiResponse<OrderListResponse>> => {
        return api.get('/orders', { params });
    },

    // Search orders
    search: (query: string): Promise<ApiResponse<Order[]>> => {
        return api.get('/orders/{query}', { params: { q: query } });
    },

    // Get order by ID
    getById: (id: number): Promise<ApiResponse<Order>> => {
        return api.get(`/orders/${id}`);
    },

    // Create new order
    create: (orderData: CreateOrderData): Promise<ApiResponse<Order>> => {
        return api.post('/orders', orderData);
    },

    // Update order
    update: (id: number, orderData: UpdateOrderData): Promise<ApiResponse<Order>> => {
        return api.put(`/orders/${id}`, orderData);
    },

    // update order status
    updateStatus: (id: number, status: 'paid' | 'cancelled'|'shipped'|'completed'): Promise<ApiResponse<Order>> => {
        return api.patch(`/orders/${id}/status`, { status });
    },

    // Approve order (change status to paid)
    approve: (id: number): Promise<ApiResponse<Order>> => {
        return api.patch(`/orders/${id}/status`, { status: 'paid' });
    },

    // Complete order (change status to completed)
    complete: (id: number): Promise<ApiResponse<Order>> => {
        return api.patch(`/orders/${id}/status`, { status: 'completed' });
    },

    // Ship order (change status to shipped)
    ship: (id: number): Promise<ApiResponse<Order>> => {
        return api.patch(`/orders/${id}/status`, { status: 'shipped' });
    },

    // Cancel order (change status to cancelled)
    cancel: (id: number): Promise<ApiResponse<Order>> => {
        return api.patch(`/orders/${id}/status`, { status: 'cancelled' });
    },
};

// Auth API
export const authApi = {
    login: (email: string, password: string): Promise<ApiResponse<LoginResponse>> => {
        return api.post('/auth/login', { email, password });
    },
    logout: (): Promise<ApiResponse<null>> => {
        return api.post('/auth/logout');
    },
    me: (): Promise<ApiResponse<LoginUser>> => {
        return api.get('/auth/me');
    },
};

// Employee API
export const employeeApi = {
    // Get all employees
    getAll: (): Promise<ApiResponse<Employee[]>> => {
        return api.get('/employees');
    },

    // Get employee by ID
    getById: (id: number): Promise<ApiResponse<Employee>> => {
        return api.get(`/employees/${id}`);
    },

    // Create new employee
    create: (employee: {
        email: string;
        password: string;
        full_name: string;
        phone?: string;
        address?: string;
        birthday?: string;
        gender?: string;
    }): Promise<ApiResponse<{ id: number; account_id: number; email: string; full_name: string }>> => {
        return api.post('/employees', employee);
    },

    // Update employee
    update: (id: number, employee: {
        email?: string;
        password?: string;
        full_name?: string;
        phone?: string;
        address?: string;
        birthday?: string;
        gender?: string;
    }): Promise<ApiResponse<null>> => {
        return api.put(`/employees/${id}`, employee);
    },

    // Delete employee
    delete: (id: number): Promise<ApiResponse<null>> => {
        return api.delete(`/employees/${id}`);
    },
};

export default api;
