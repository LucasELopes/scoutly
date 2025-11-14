"use client";

import Image from "next/image";
import { FormEvent, useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/src/hook/useAuth";
import { LuUserRound } from "react-icons/lu";
import { MdLockOutline } from "react-icons/md";
import { useAuthStore } from "@/src/store/authStore";

export default function SignInPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const { login, error, loading } = useAuth();
  const { setAuth, clearAuth, getToken, getUser, isAuthenticated, isVerified } = useAuthStore();
  const router = useRouter();

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    await login({ email, password });
  };

  useEffect(() => {
    if(isAuthenticated()) {
      router.push('/auth/dashboard')
    }
  },[])

  return (
    <div className="bg-[#ECEDF3] text-black flex flex-col max-md:gap-9 items-center justify-center min-h-screen md:pb-20">
      <main className="p-6 pt-6 max-md:mb-36 max-md:w-11/12 md:px-16 md:py-14 md:shadow-xl/30 md:inset-shadow-sm/30 rounded-2xl bg-white">
        <form onSubmit={handleSubmit} className="flex flex-col">
          <div className="flex ring-1 ring-gray-400 rounded-2xl items-center justify-between gap-3 px-4 py-2 mb-4 bg-white-dark">
            <LuUserRound className="w-5 h-5 text-gray-400" />
            <input
              className="px-2 w-full focus:border-none focus:outline-none md:text-lg font-mediuma placeholder:font-semibold"
              type="email"
              name="email"
              placeholder="Email"
              required
              onChange={(e) => setEmail(e.target.value)}
            />
          </div>
          <div className="flex ring-1 ring-gray-400 rounded-2xl items-center justify-center gap-3 px-4 py-2 mb-4 bg-white-dark">
            <MdLockOutline className="w-5 h-5 text-gray-400"/>
            <input
              className="px-2 w-full focus:border-none focus:outline-none md:text-lg placeholder:font-semibold"
              type="password"
              name="password"
              placeholder="Senha"
              required
              onChange={(e) => setPassword(e.target.value)}
            />
          </div>
          <p
            className="text-sm font-semibold text-center max-md:text-[12px] text-red-500 md:flex pb-4 pt-1"
            hidden={!error}
          >
            {error}
            {error === "Credenciais inválidas" && (
              <a href="/auth/forgot-password">
                ,{" "}
                <span className="underline decoration-1 hover:text-red-800">
                  Clique aqui
                </span>{" "}
                para redefinir a senha!
              </a>
            )}
          </p>
          <div className="flex justify-center items-center">
            <button
              type="submit"
              disabled={loading}
              className="w-4/5 bg-green shadow-xl/25 text-white px-2 py-1 font-bold text-xl rounded-2xl cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
            >
              {loading ? <>Carregando...</> : "Login"}
            </button>
          </div>
        </form>
      </main>
    </div>
  );
}