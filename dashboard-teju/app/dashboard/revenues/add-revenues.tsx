import * as React from "react"
import {PropsWithChildren} from "react"
import {Button} from "@/components/ui/button"
import {Drawer, DrawerClose, DrawerContent, DrawerDescription, DrawerHeader, DrawerTitle,} from "@/components/ui/drawer"
import {useMutation} from "convex/react";
import {api} from "@/convex/_generated/api";
import {Label} from "@/components/ui/label";
import {Input} from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue
} from "@/components/ui/select";

export function AddRevenues({isOpen, setIsClosed}: PropsWithChildren<{ isOpen: boolean, setIsClosed: () => void }>) {
  const addExpense = useMutation(api.revenues.add)

  async function handleSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault()
    const formData = new FormData(event.currentTarget)
    const amount = Number(formData.get("amount"))
    const name = String(formData.get("name"))
    const type = String(formData.get("type"))
    await addExpense({amount, name, type, date: new Date().toISOString()})
    setIsClosed()
  }

  return (
    <Drawer open={isOpen} direction="right" onClose={setIsClosed}>
      <DrawerContent>
        <DrawerHeader className="text-left">
          <DrawerTitle>Adicionar despesa</DrawerTitle>
          <DrawerDescription>
            <form onSubmit={handleSubmit}>
              <div className="flex w-full gap-6">
                <div className="w-full">
                  <Label>
                    Nome
                  </Label>
                  <Input
                    className="outline-none focus-visible:ring-0"
                    type="text"
                    name="name"
                    required
                  />
                </div>
                <div className="w-full">
                  <Label>
                    Valor
                  </Label>
                  <Input
                    className="outline-none focus-visible:ring-0"
                    type="number"
                    name="amount"
                    required
                  />
                </div>
                <div className="w-full">
                  <Label>
                    Tipo
                  </Label>
                  <Select>
                    <SelectTrigger>
                      <SelectValue placeholder="Selecione o tipo"/>
                    </SelectTrigger>
                    <SelectContent>
                      <SelectGroup>
                        <SelectLabel>Tipo</SelectLabel>
                        <SelectItem value="STABLE">Fixo</SelectItem>
                        <SelectItem value="VARIABLE">Variável</SelectItem>
                      </SelectGroup>
                    </SelectContent>
                  </Select>
                </div>
              </div>
              <div className="my-4 flex align-middle justify-center gap-4">
                <Button type="submit">Adicionar</Button>
                <DrawerClose asChild>
                  <Button onClick={setIsClosed} variant="outline">Cancel</Button>
                </DrawerClose>
              </div>
            </form>
          </DrawerDescription>
        </DrawerHeader>
      </DrawerContent>
    </Drawer>
  )
}
