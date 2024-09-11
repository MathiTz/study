import {mutation, query} from "./_generated/server";
import {v} from "convex/values";

export const get = query({
  args: {},
  handler: async (ctx) => {
    return await ctx.db
      .query("revenues")
      .collect()
  }
})

export const add = mutation({
  args: {
    name: v.string(),
    amount: v.number(),
    type: v.string(),
    date: v.string(),
  },
  handler: async (ctx, {name, date, amount, type}) => {
    return await ctx.db.insert("revenues", {
      name,
      date,
      amount,
      type
    });
  }
});

export const remove = mutation({
  args: {
    id: v.id("revenues")
  },
  handler: async (ctx, {id}) => {
    return await ctx.db.delete(id);
  }
});

export const update = mutation({
  args: {
    id: v.id("revenues"),
    name: v.string(),
    date: v.string(),
    amount: v.number(),
    type: v.string(),
  },
  handler: async (ctx, {id, name, date, amount, type}) => {
    return await ctx.db.replace(id, {
      name,
      date,
      amount,
      type
    });
  }
});

export const getOne = query({
  args: {
    id: v.id("revenues")
  },
  handler: async (ctx, {id}) => {
    return await ctx.db
      .query("revenues")
      .filter(q => q.eq(q.field("_id"), id))
      .collect()
  }
});

