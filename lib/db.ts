import "server-only";
import { drizzle as drizzleNeon, type NeonHttpDatabase } from "drizzle-orm/neon-http";
import { neon } from "@neondatabase/serverless";
import { sql } from "drizzle-orm";
import * as schema from "./schema";
import { SCHEMA_SQL } from "./setup-sql";
import { seed } from "./seed";

export type Db = NeonHttpDatabase<typeof schema>;

const g = globalThis as unknown as { __jcDb?: Promise<Db> };

function databaseUrl() {
  return process.env.DATABASE_URL || process.env.POSTGRES_URL || "";
}

async function connect(): Promise<Db> {
  const url = databaseUrl();
  let db: Db;
  if (url) {
    db = drizzleNeon(neon(url), { schema });
  } else {
    if (process.env.VERCEL) {
      throw new Error("DATABASE_URL mungon. Lidhni një databazë Neon në Vercel → Storage.");
    }
    // Local development: an embedded Postgres saved in node_modules/.cache/jc-pglite.
    const { PGlite } = await import("@electric-sql/pglite");
    const { drizzle } = await import("drizzle-orm/pglite");
    const { mkdirSync } = await import("node:fs");
    mkdirSync("./node_modules/.cache", { recursive: true });
    const client = new PGlite("./node_modules/.cache/jc-pglite");
    db = drizzle(client, { schema }) as unknown as Db;
  }

  for (const statement of SCHEMA_SQL.split(";").map((s) => s.trim()).filter(Boolean)) {
    await db.execute(sql.raw(statement));
  }
  const count = await db.execute(sql`SELECT COUNT(*)::int AS c FROM categories`);
  const rows = (count as unknown as { rows?: { c: number }[] }).rows ?? (count as unknown as { c: number }[]);
  if (Number(rows[0]?.c ?? 0) === 0) {
    await seed(db);
  }
  return db;
}

export function getDb(): Promise<Db> {
  if (!g.__jcDb) {
    g.__jcDb = connect().catch((err) => {
      g.__jcDb = undefined;
      throw err;
    });
  }
  return g.__jcDb;
}
