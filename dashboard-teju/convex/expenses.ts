import {mutation, query} from "./_generated/server";
import {v} from "convex/values";

export const get = query({
  args: {},
  handler: async (ctx,) => {
    return await ctx.db
      .query("expenses")
      .collect()
  }
})

export const add = mutation({
  args: {
    name: v.string(),
    amount: v.number(),
    date: v.string(),
  },
  handler: async (ctx, {name, date, amount}) => {
    return await ctx.db.insert("expenses", {
      name,
      date,
      amount
    });
  }
});

export const remove = mutation({
  args: {
    id: v.id("expenses")
  },
  handler: async (ctx, {id}) => {
    return await ctx.db.delete(id);
  }
});

export const update = mutation({
  args: {
    id: v.id("expenses"),
    name: v.string(),
    date: v.string(),
    amount: v.number(),
  },
  handler: async (ctx, {id, name, date, amount}) => {
    return await ctx.db.replace(id, {
      name,
      date,
      amount
    });
  }
});

export const getOne = query({
  args: {
    id: v.id("expenses")
  },
  handler: async (ctx, {id}) => {
    return await ctx.db
      .query("expenses")
      .filter(q => q.eq(q.field("_id"), id))
      .collect()
  }
})
