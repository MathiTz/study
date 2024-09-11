"use client"

import {FormEvent, HTMLAttributes, useState} from "react"
import {cn} from "@/lib/utils";
import {Button} from '@/components/ui/button'
import {Input} from '@/components/ui/input'
import {Label} from '@/components/ui/label'
import {Icons} from '@/components/icons'
import {useAuthProvider} from "@/context/auth-provider";

interface UserAuthFormProps extends HTMLAttributes<HTMLDivElement> {
}

export function UserAuthForm({className, ...props}: UserAuthFormProps) {
  const [isLoading, setIsLoading] = useState<boolean>(false)
  const {handleSubmit} = useAuthProvider();

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault()
    setIsLoading(true)
    handleSubmit({
      // @ts-ignore
      email: event.target?.email?.value,
      // @ts-ignore
      password: event.target?.password?.value
    })

    setTimeout(() => {
      setIsLoading(false)
    }, 3000)
  }

  return (
    <div className={cn("grid gap-6", className)} {...props}>
      <form onSubmit={onSubmit}>
        <div className="grid gap-2">
          <div className="grid gap-1">
            <Label className="pb-3" htmlFor="email">
              Email
            </Label>
            <Input
              id="email"
              placeholder="name@example.com"
              type="email"
              autoCapitalize="none"
              autoComplete="email"
              autoCorrect="off"
              disabled={isLoading}
            />
          </div>
          <hr/>
          <div className="grid gap-1">
            <Label className="pb-3" htmlFor="password">
              Password
            </Label>
            <Input
              id="password"
              type="password"
              disabled={isLoading}
            />
          </div>
          <Button disabled={isLoading}>
            {isLoading && (
              <Icons.spinner className="mr-2 h-4 w-4 animate-spin"/>
            )}
            Sign In with Email
          </Button>
        </div>
      </form>
    </div>
  )
}
