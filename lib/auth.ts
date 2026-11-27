import "server-only";
import { cookies } from "next/headers";
import { redirect } from "next/navigation";
import { SignJWT, jwtVerify } from "jose";
import { randomBytes, scryptSync, timingSafeEqual } from "node:crypto";
import { eq } from "drizzle-orm";
import { getDb } from "./db";
import { admins } from "./schema";

const COOKIE = "jc_admin";

function secret() {
  const s = process.env.AUTH_SECRET;
  if (!s && process.env.VERCEL) throw new Error("AUTH_SECRET mungon.");
  return new TextEncoder().encode(s || "local-development-secret-only");
}

export function hashPassword(password: string) {
  const salt = randomBytes(16).toString("hex");
  const hash = scryptSync(password, salt, 64).toString("hex");
  return `scrypt$${salt}$${hash}`;
}

function verifyPassword(password: string, stored: string) {
  const [, salt, hash] = stored.split("$");
  if (!salt || !hash) return false;
  const a = Buffer.from(hash, "hex");
  const b = scryptSync(password, salt, 64);
  return a.length === b.length && timingSafeEqual(a, b);
}

function safeEqual(a: string, b: string) {
  const x = Buffer.from(a);
  const y = Buffer.from(b);
  return x.length === y.length && timingSafeEqual(x, y);
}

export type AdminSession = { id: number; name: string; username: string };

/** Checks a login. Uses the admin saved in the database, or the Vercel env login if none exists yet. */
export async function checkLogin(username: string, password: string): Promise<AdminSession | null> {
  const db = await getDb();
  const all = await db.select().from(admins);
  if (all.length > 0) {
    const a = all.find((x) => x.username.toLowerCase() === username.toLowerCase());
    if (a && verifyPassword(password, a.passwordHash)) return { id: a.id, name: a.fullname, username: a.username };
    return null;
  }
  const envUser = process.env.ADMIN_USERNAME || "";
  const envPass = process.env.ADMIN_PASSWORD || "";
  if (envUser && envPass && safeEqual(username, envUser) && safeEqual(password, envPass)) {
    return { id: 0, name: process.env.ADMIN_NAME || "Administrator", username: envUser };
  }
  return null;
}

export async function needsSetup() {
  const db = await getDb();
  const all = await db.select({ id: admins.id }).from(admins).limit(1);
  return all.length === 0 && !(process.env.ADMIN_USERNAME && process.env.ADMIN_PASSWORD);
}

export async function startSession(admin: AdminSession) {
  const token = await new SignJWT({ ...admin })
    .setProtectedHeader({ alg: "HS256" })
    .setIssuedAt()
    .setExpirationTime("7d")
    .sign(secret());
  (await cookies()).set(COOKIE, token, {
    httpOnly: true,
    sameSite: "lax",
    secure: process.env.NODE_ENV === "production",
    path: "/",
    maxAge: 60 * 60 * 24 * 7,
  });
}

export async function endSession() {
  (await cookies()).delete(COOKIE);
}

export async function getSession(): Promise<AdminSession | null> {
  const token = (await cookies()).get(COOKIE)?.value;
  if (!token) return null;
  try {
    const { payload } = await jwtVerify(token, secret());
    return { id: Number(payload.id), name: String(payload.name), username: String(payload.username) };
  } catch {
    return null;
  }
}

export async function requireAdmin(): Promise<AdminSession> {
  const s = await getSession();
  if (!s) redirect("/admin/login");
  return s;
}

export async function saveAdmin(id: number, fullname: string, username: string, password?: string) {
  const db = await getDb();
  if (id > 0) {
    const values: Partial<typeof admins.$inferInsert> = { fullname, username };
    if (password) values.passwordHash = hashPassword(password);
    await db.update(admins).set(values).where(eq(admins.id, id));
    return id;
  }
  if (!password) throw new Error("Password mungon");
  const [row] = await db.insert(admins).values({ fullname, username, passwordHash: hashPassword(password) }).returning();
  return row.id;
}
