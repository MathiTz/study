"use client"

import Image from "next/image";
import {Button} from "@/components/ui/button";
import {Input} from "@/components/ui/input";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuShortcut,
  DropdownMenuTrigger
} from "@/components/ui/dropdown-menu";
import {Avatar, AvatarFallback, AvatarImage} from "@/components/ui/avatar";
import {DropdownMenuItem} from "@radix-ui/react-dropdown-menu";
import {cn} from "@/lib/utils";
import Link from "next/link";
import {useState} from "react";
import {AddExpenses} from "@/app/dashboard/expenses/add-expenses";
import {Tabs, TabsContent, TabsList, TabsTrigger} from "@/components/ui/tabs";
import ExpensesTable from "@/app/dashboard/expenses/expenses-table";
import RevenuesTable from "@/app/dashboard/revenues/revenues-table";
import {AddRevenues} from "@/app/dashboard/revenues/add-revenues";

export default function Dashboard() {
  const [openAddExpense, setOpenAddExpense] = useState(false)
  const [openAddRevenue, setOpenAddRevenue] = useState(false)

  return (
    <>
      <div className="md:hidden">
        <Image
          src="/examples/dashboard-light.png"
          width={1280}
          height={866}
          alt="Dashboard"
          className="block dark:hidden"
        />
        <Image
          src="/examples/dashboard-dark.png"
          width={1280}
          height={866}
          alt="Dashboard"
          className="hidden dark:block"
        />
      </div>
      <div className="hidden flex-col md:flex">
        <div className="border-b">
          <div className="flex h-16 items-center px-4">
            <nav
              className={cn("flex items-center space-x-4 lg:space-x-6", 'mx-6')}
            >
              <Link
                href="/examples/dashboard"
                className="text-sm font-medium transition-colors hover:text-primary"
              >
                Overview
              </Link>
            </nav>
            <div className="ml-auto flex items-center space-x-4">
              <div>
                <Input
                  type="search"
                  placeholder="Search..."
                  className="md:w-[100px] lg:w-[300px]"
                />
              </div>
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" className="relative h-8 w-8 rounded-full">
                    <Avatar className="h-8 w-8">
                      <AvatarImage src="/avatars/01.png" alt="@shadcn"/>
                      <AvatarFallback>SC</AvatarFallback>
                    </Avatar>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="w-56" align="end" forceMount>
                  <DropdownMenuLabel className="font-normal">
                    <div className="flex flex-col space-y-1">
                      <p className="text-sm font-medium leading-none">shadcn</p>
                      <p className="text-xs leading-none text-muted-foreground">
                        m@example.com
                      </p>
                    </div>
                  </DropdownMenuLabel>
                  <DropdownMenuSeparator/>
                  <DropdownMenuGroup>
                    <DropdownMenuItem>
                      Profile
                      <DropdownMenuShortcut>⇧⌘P</DropdownMenuShortcut>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                      Billing
                      <DropdownMenuShortcut>⌘B</DropdownMenuShortcut>
                    </DropdownMenuItem>
                    <DropdownMenuItem>
                      Settings
                      <DropdownMenuShortcut>⌘S</DropdownMenuShortcut>
                    </DropdownMenuItem>
                    <DropdownMenuItem>New Team</DropdownMenuItem>
                  </DropdownMenuGroup>
                  <DropdownMenuSeparator/>
                  <DropdownMenuItem>
                    Log out
                    <DropdownMenuShortcut>⇧⌘Q</DropdownMenuShortcut>
                  </DropdownMenuItem>
                </DropdownMenuContent>
              </DropdownMenu>
            </div>
          </div>
        </div>
        <div className="flex-1 space-y-4 p-8 pt-6">
          <div className="flex items-center justify-between space-y-2">
            <h2 className="text-3xl font-bold tracking-tight">Custos mensais</h2>
            <div className="flex items-center space-x-4">
              <Button onClick={() => setOpenAddExpense(true)} variant="default">Adicionar despesa</Button>
              <Button onClick={() => setOpenAddRevenue(true)} variant="default">Adicionar receita</Button>
            </div>
          </div>
        </div>
        <Tabs defaultValue="expenses" className="w-full">
          <TabsList className="grid w-full grid-cols-2">
            <TabsTrigger value="expenses">Custos</TabsTrigger>
            <TabsTrigger value="revenues">Receita</TabsTrigger>
          </TabsList>
          <TabsContent value="expenses">
            <ExpensesTable/>
          </TabsContent>
          <TabsContent value="revenues">
            <RevenuesTable/>
          </TabsContent>
        </Tabs>
        <AddExpenses isOpen={openAddExpense} setIsClosed={() => setOpenAddExpense(false)}/>
        <AddRevenues isOpen={openAddRevenue} setIsClosed={() => setOpenAddRevenue(false)}/>
      </div>
    </>
  )
}
