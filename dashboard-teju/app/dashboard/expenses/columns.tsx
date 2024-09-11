"use client"

import {ColumnDef} from "@tanstack/react-table"
import {formatMoney} from "@/utils";

export type ExpensesType = {
  amount: number;
  name: string;
  date: string;
}

export const columns: ColumnDef<ExpensesType>[] = [
  {
    accessorKey: "amount",
    header: "Custo",
    cell: ({row}) => {
      const amount = parseFloat(row.getValue("amount"))

      return <div className="text-left font-medium">{formatMoney(amount)}</div>
    }
  },
  {
    accessorKey: "name",
    header: "Descrição",
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
]
