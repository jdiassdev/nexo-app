export type UserRole = 'god' | 'director' | 'teacher' | 'student';

export interface User {
    id: number;
    school_id: number | null;
    name: string;
    email: string | null;
    enrollment: string | null;
    role: UserRole;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    flash: {
        success: string | null;
        error: string | null;
    };
};
