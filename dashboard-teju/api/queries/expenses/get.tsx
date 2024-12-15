import {useQuery} from "convex/react";
import {api} from "@/convex/_generated/api";

export default function () {
  return useQuery(api.expenses.get, {})
}
