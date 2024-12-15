"use client"

import {ColumnDef} from "@tanstack/react-table"

export type RevenuesType = {
  amount: number; name: string; date: string; type: string;
}

const typeMapper = {
  "STABLE": "Fixo",
  "VARIABLE": "Variável",
}

export const columns: ColumnDef<RevenuesType>[] = [
  {
    accessorKey: "amount",
    header: "Custo",
    cell: ({row}) => {
      const amount = parseFloat(row.getValue("amount"))
      const formatted = new Intl.NumberFormat("pt-BR", {
        style: "currency",
        currency: "BRL",
      }).format(amount)

      return <div className="text-left font-medium">{formatted}</div>
    }
  },
  {
    accessorKey: "name",
    header: "Nome",
  },
  {
    accessorKey: "date",
    header: "Data",
    cell: ({row}) => {
      const date = new Date(row.getValue("date"))
      const formatted = new Intl.DateTimeFormat("pt-BR").format(date)

      return <div>{formatted}</div>
    }
  },
  {
    accessorKey: "type",
    header: "Tipo",
    cell: ({row}) => {
      const type = row.getValue("type") as keyof typeof typeMapper

      return <div>{typeMapper[type]}</div>
    }
  }
]
