"use client"

import {columns} from "./columns"
import {DataTable} from "../../components/data-table"
import getRevenues from "@/api/queries/revenues/get";

export default function RevenuesTable() {
  const revenues = getRevenues();

  return (
    <div className="container mx-auto py-10">
      <DataTable columns={columns} data={revenues || []}/>
    </div>
  )
}
