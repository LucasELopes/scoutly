import { useCallback, useState } from "react";
import { useRouter } from "next/navigation";
import { AxiosError } from "axios";
import { userType } from "../types/user";
import { AuthOptions, AuthResponse } from "../types/auth";
import { useAuthStore } from "../store/authStore";
import baseApi from "../services/api";

interface UseAuthReturn {
  token: string | null;
  error: string | null;
  loading: boolean;
  isAuthenticated: boolean;
  isVerified: boolean;
  user: userType | null;
  login: (credentials: AuthOptions) => Promise<void>;
  logout: () => Promise<void>;
}

export function useAuth(): UseAuthReturn {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const { setAuth, clearAuth, getToken, getUser, isAuthenticated, isVerified } = useAuthStore();
  const router = useRouter();

  const login = useCallback(
    async ({ email, password }: AuthOptions): Promise<void> => {
      setLoading(true);
      setError(null);

      try {
        const response = await baseApi.post<AuthResponse>("/api/login", {
          email,
          password,
        });
        setAuth(response.data);
        router.push("/dashboard");
      } catch (error) {
        if (error instanceof AxiosError) {
          setError(error.response?.data.message || "Falha no login");
        } else {
          setError("Ocorreu um erro inesperado");
        }
        throw error;
      } finally {
        setLoading(false);
      }
    },
    [setAuth, router],
  );

  const logout = useCallback(async (): Promise<void> => {
    try {
      await baseApi.post("/logout");
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      clearAuth();
      router.push("/auth/sign-in");
    }
  }, [clearAuth, router]);

  return {
    token: getToken(),
    loading,
    error,
    isAuthenticated: isAuthenticated(),
    isVerified: isVerified(),
    user: getUser(),
    login,
    logout,
  };
}