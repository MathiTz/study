'use client'

import {createContext, PropsWithChildren, useContext, useEffect, useState} from "react";
import {useAction} from "convex/react";
import {api} from "@/convex/_generated/api";
import {useRouter} from "next/navigation";

type CredentialsType = {
  email: string,
  password: string
}

type UserData = {
  email?: string,
  name?: string
}

type AuthProviderProps = {
  // AuthProviderProps is a type that has a property called user
  user: UserData,
  handleSubmit: (data: CredentialsType) => any
}

const AuthProvider = createContext({} as AuthProviderProps)

function AuthContextProvider({children}: PropsWithChildren) {
  const {push} = useRouter()
  const [user, setUser] = useState<UserData>({})
  const getUserAction = useAction(api.users.searchUser)

  async function handleSubmit(data: CredentialsType) {
    try {
      const response = await getUserAction(data)
      if (response) {
        localStorage.setItem('user', JSON.stringify(response))
        setUser({
          email: response.email,
          name: response.name
        })
      }
      push('/dashboard')

      return response;
    } catch (err) {
      console.error(err)
    }
  }

  async function signOut() {
    localStorage.removeItem('user')
    setUser({})
  }

  useEffect(() => {
    const userOnLocalStorage = localStorage.getItem('user')

    if (!userOnLocalStorage) {
      push('/login')
    } else {
      setUser(JSON.parse(userOnLocalStorage))
    }

  }, [])

  return (
    <AuthProvider.Provider value={{user, handleSubmit}}>
      {children}
    </AuthProvider.Provider>
  )
}

function useAuthProvider() {
  return useContext(AuthProvider)
}

export {
  AuthContextProvider,
  useAuthProvider
}
