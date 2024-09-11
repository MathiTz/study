import {defineSchema, defineTable} from "convex/server";
import {v} from "convex/values";

// The schema is entirely optional.
// You can delete this file (schema.ts) and the
// app will continue to work.
// The schema provides more precise TypeScript types.
export default defineSchema({
  messages: defineTable({
    author: v.string(),
    body: v.string(),
  }),
  users: defineTable({
    name: v.string(),
    email: v.string(),
    password: v.string(),
  }),
  expenses: defineTable({
    name: v.string(),
    amount: v.number(),
    date: v.string(),
  }),
  revenues: defineTable({
    name: v.string(),
    amount: v.number(),
    type: v.string(),
    date: v.string(),
  }),
});
