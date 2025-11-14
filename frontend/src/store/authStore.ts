import { create } from "zustand";
import { createJSONStorage, persist } from "zustand/middleware";
import { AuthResponse } from "../types/auth";
import { userType } from "../types/user";
import baseApi from "../services/api";

interface AuthState {
  auth: AuthResponse | null;
  expirationTime: number | null;
  currentUser: userType | null;
  isLoadingUser: boolean;
  setAuth: (authData: AuthResponse) => void;
  clearAuth: () => void;
  getToken: () => string | null;
  getUser: () => userType | null;
  getRole: () => string | null;
  isAuthenticated: () => boolean;
  isVerified: () => boolean;
  fetchCurrentUser: () => Promise<void>;
  setCurrentUser: (user: userType) => void;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      auth: null,
      expirationTime: null,
      currentUser: null,
      isLoadingUser: false,

      setAuth: (authData: AuthResponse) => {
        const expirationTime = Date.now() + authData.expires_in * 1000;
        set({ auth: authData, expirationTime });
      },

      clearAuth: () =>
        set({
          auth: null,
          expirationTime: null,
          currentUser: null,
        }),

      setCurrentUser: (user: userType) => {
        set({ currentUser: user });
      },

      fetchCurrentUser: async () => {
        const token = get().getToken();
        if (!token) return;

        set({ isLoadingUser: true });

        try {
          const response = await baseApi.get("/profile", {
            headers: {
              Authorization: `Bearer ${token}`,
            },
          });

          if (response.status === 200) {
            const userData: userType = response.data;
            set({ currentUser: userData, isLoadingUser: false });
          } else {
            get().clearAuth();
            set({ isLoadingUser: false });
          }
        } catch (error) {
          console.error("Erro ao buscar usuário atual:", error);
          set({ isLoadingUser: false });
        }
      },

      isAuthenticated: () => {
        const { auth, expirationTime } = get();
        if (!auth?.access_token || !expirationTime) return false;
        return Date.now() < expirationTime;
      },

      isVerified: () => {
        const { currentUser } = get();

        if (currentUser) {
          return (
            currentUser.email_verified_at !== null &&
            currentUser.email_verified_at !== undefined
          );
        }

        return false;
      },

      getToken: () => {
        const { auth, expirationTime } = get();
        if (expirationTime && Date.now() > expirationTime) {
          get().clearAuth();
          return null;
        }
        return auth?.access_token || null;
      },

      getUser: () => {
        const { currentUser, expirationTime } = get();
        if (expirationTime && Date.now() > expirationTime) {
          get().clearAuth();
          return null;
        }

        return currentUser || null;
      },

      getRole: () => {
        const { currentUser, expirationTime } = get();
        if (expirationTime && Date.now() > expirationTime) {
          get().clearAuth();
          return null;
        }

        return currentUser?.role || null;
      },
    }),
    {
      name: "auth-storage",
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        auth: state.auth,
        expirationTime: state.expirationTime,
        currentUser: state.currentUser,
      }),
      onRehydrateStorage: (state) => {
        if (
          state.auth &&
          state.auth.access_token &&
          Date.now() < state.expirationTime!
        ) {
          state.fetchCurrentUser();
        }
      },
    },
  ),
);