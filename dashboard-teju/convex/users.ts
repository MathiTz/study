import {action, internalQuery} from "./_generated/server";
import {v} from "convex/values";
import {Id} from "./_generated/dataModel";
import {internal} from "./_generated/api";

export const internalGetUser = internalQuery({
  args: {
    email: v.string(),
    password: v.string()
  },
  handler: async (ctx, {email, password}) => {
    return await ctx.db
      .query("users")
      .filter(
        q => q.eq(q.field("email"), email)
      )
      .filter(
        q => q.eq(q.field("password"), password)
      )
      .first();
  }
})

export const searchUser = action({
  args: {
    email: v.string(),
    password: v.string()
  },
  handler: async (ctx, {email, password}): Promise<{
    _id: Id<"users">,
    _creationTime: number,
    name: string,
    email: string
  } | null> => {
    return await ctx.runQuery(internal.users.internalGetUser, {email, password});
  }
})

