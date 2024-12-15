'use client'

import {UserAuthForm} from "@/app/login/components/user-auth-form";

export default function Login() {
  return (
    <div className="lg:p-8">
      <div className="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
        <div className="flex flex-col space-y-2 text-center">
          <h1 className="text-2xl font-semibold tracking-tight">
            Log in
          </h1>
          <p className="text-sm text-muted-foreground">
            Enter your credentials below to enter the dashboard
          </p>
        </div>
        <UserAuthForm/>
      </div>
    </div>
  )
}
