"use client"

import { useAuthStore } from "@/src/store/authStore";

export default function Dashboard() {

  const {getUser} = useAuthStore();

  return(
    <div>
      {getUser()?.name}
    </div>
  );
}