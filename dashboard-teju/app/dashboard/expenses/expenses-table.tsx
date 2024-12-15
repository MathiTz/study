"use client"

import {columns} from "./columns"
import {DataTable} from "../../components/data-table"
import getExpenses from "@/api/queries/expenses/get";
import {formatMoney} from "@/utils";

export default function ExpensesTable() {
  const expenses = getExpenses();
  const expensesTotal = expenses?.reduce((acc, expense) => acc + expense.amount, 0);

  return (
    <div className="container mx-auto py-10">
      <h1>Total: {formatMoney(expensesTotal as number)}</h1>
      <DataTable columns={columns} data={expenses || []}/>
    </div>
  )
}
