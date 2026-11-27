"use server";

import { redirect } from "next/navigation";
import { revalidatePath } from "next/cache";
import { eq } from "drizzle-orm";
import { getDb } from "@/lib/db";
import { authors, books, categories, gallery, messages, news, orderItems, orders, settings } from "@/lib/schema";
import { checkLogin, endSession, requireAdmin, saveAdmin, startSession, getSession } from "@/lib/auth";
import { removeImage, saveImage } from "@/lib/upload";
import { ORDER_STATUSES } from "@/lib/format";

export type ActionState = { error?: string; ok?: string };

const str = (fd: FormData, k: string, max = 20000) => String(fd.get(k) ?? "").trim().slice(0, max);
const num = (fd: FormData, k: string) => {
  const n = Number(String(fd.get(k) ?? "").replace(",", "."));
  return Number.isFinite(n) ? n : 0;
};
const idOrNull = (fd: FormData, k: string) => {
  const n = Number(fd.get(k));
  return Number.isInteger(n) && n > 0 ? n : null;
};
const dateOrNull = (fd: FormData, k: string) => {
  const v = str(fd, k, 10);
  return /^\d{4}-\d{2}-\d{2}$/.test(v) ? v : null;
};

function done(path: string, publicPaths: string[] = []) {
  revalidatePath("/", "layout");
  publicPaths.forEach((p) => revalidatePath(p));
  redirect(path);
}

async function image(fd: FormData, field: string, folder: string, current: string | null) {
  if (fd.get(`${field}_remove`) === "1") {
    await removeImage(current);
    current = null;
  }
  const uploaded = await saveImage(fd.get(field), folder);
  if (uploaded) {
    await removeImage(current);
    return uploaded;
  }
  return current;
}

/* ---------------- login ---------------- */

export async function login(_: ActionState, fd: FormData): Promise<ActionState> {
  const username = str(fd, "username", 100);
  const password = String(fd.get("password") ?? "");
  if (fd.get("setup") === "1") {
    const { needsSetup } = await import("@/lib/auth");
    if (!(await needsSetup())) return { error: "Llogaria ekziston tashmë. Rifreskoni faqen." };
    const fullname = str(fd, "fullname", 120);
    if (!fullname || !username) return { error: "Plotësoni emrin dhe username." };
    if (password.length < 8) return { error: "Password duhet të ketë të paktën 8 karaktere." };
    const id = await saveAdmin(0, fullname, username, password);
    await startSession({ id, name: fullname, username });
    redirect("/admin");
  }
  const admin = await checkLogin(username, password);
  if (!admin) return { error: "Username ose password është gabim." };
  await startSession(admin);
  redirect("/admin");
}

export async function logout() {
  await endSession();
  redirect("/admin/login");
}

/* ---------------- books ---------------- */

export async function saveBook(id: number, _: ActionState, fd: FormData): Promise<ActionState> {
  await requireAdmin();
  const db = await getDb();
  const title = str(fd, "title", 250);
  if (!title) return { error: "Shkruani titullin e librit." };
  const price = num(fd, "price");
  if (price < 0) return { error: "Çmimi nuk mund të jetë negativ." };
  const existing = id ? (await db.select().from(books).where(eq(books.id, id)))[0] : undefined;
  let cover: string | null;
  try {
    cover = await image(fd, "cover", "books", existing?.cover ?? null);
  } catch (e) {
    return { error: (e as Error).message };
  }
  const values = {
    title,
    price: price.toFixed(2),
    authorId: idOrNull(fd, "authorId"),
    categoryId: idOrNull(fd, "categoryId"),
    description: str(fd, "description"),
    publishDate: dateOrNull(fd, "publishDate"),
    featured: fd.get("featured") === "on",
    inStock: fd.get("inStock") === "on",
    cover,
  };
  if (existing) await db.update(books).set(values).where(eq(books.id, id));
  else await db.insert(books).values(values);
  done("/admin/librat?ok=1");
  return {};
}

export async function deleteBook(id: number) {
  await requireAdmin();
  const db = await getDb();
  const [b] = await db.delete(books).where(eq(books.id, id)).returning();
  if (b) await removeImage(b.cover);
  done("/admin/librat?ok=deleted");
}

/* ---------------- authors ---------------- */

export async function saveAuthor(id: number, _: ActionState, fd: FormData): Promise<ActionState> {
  await requireAdmin();
  const db = await getDb();
  const fullname = str(fd, "fullname", 200);
  if (!fullname) return { error: "Shkruani emrin e autorit." };
  const existing = id ? (await db.select().from(authors).where(eq(authors.id, id)))[0] : undefined;
  let photo: string | null;
  try {
    photo = await image(fd, "photo", "authors", existing?.photo ?? null);
  } catch (e) {
    return { error: (e as Error).message };
  }
  const values = { fullname, biography: str(fd, "biography"), photo };
  if (existing) await db.update(authors).set(values).where(eq(authors.id, id));
  else await db.insert(authors).values(values);
  done("/admin/autoret?ok=1");
  return {};
}

export async function deleteAuthor(id: number) {
  await requireAdmin();
  const db = await getDb();
  await db.update(books).set({ authorId: null }).where(eq(books.authorId, id));
  const [a] = await db.delete(authors).where(eq(authors.id, id)).returning();
  if (a) await removeImage(a.photo);
  done("/admin/autoret?ok=deleted");
}

/* ---------------- categories ---------------- */

export async function saveCategory(id: number, _: ActionState, fd: FormData): Promise<ActionState> {
  await requireAdmin();
  const db = await getDb();
  const name = str(fd, "name", 100);
  if (!name) return { error: "Shkruani emrin e kategorisë." };
  const values = { name, icon: str(fd, "icon", 40) || "book" };
  if (id) await db.update(categories).set(values).where(eq(categories.id, id));
  else await db.insert(categories).values(values);
  done("/admin/kategorite?ok=1");
  return {};
}

export async function deleteCategory(id: number) {
  await requireAdmin();
  const db = await getDb();
  await db.update(books).set({ categoryId: null }).where(eq(books.categoryId, id));
  await db.delete(categories).where(eq(categories.id, id));
  done("/admin/kategorite?ok=deleted");
}

/* ---------------- news ---------------- */

export async function saveNews(id: number, _: ActionState, fd: FormData): Promise<ActionState> {
  await requireAdmin();
  const db = await getDb();
  const title = str(fd, "title", 250);
  if (!title) return { error: "Shkruani titullin e lajmit." };
  const existing = id ? (await db.select().from(news).where(eq(news.id, id)))[0] : undefined;
  let img: string | null;
  try {
    img = await image(fd, "image", "news", existing?.image ?? null);
  } catch (e) {
    return { error: (e as Error).message };
  }
  const values = {
    title,
    category: str(fd, "category", 80),
    excerpt: str(fd, "excerpt", 400),
    content: str(fd, "content"),
    publishedAt: dateOrNull(fd, "publishedAt") ?? new Date().toISOString().slice(0, 10),
    image: img,
  };
  if (existing) await db.update(news).set(values).where(eq(news.id, id));
  else await db.insert(news).values(values);
  done("/admin/lajmet?ok=1");
  return {};
}

export async function deleteNews(id: number) {
  await requireAdmin();
  const db = await getDb();
  const [n] = await db.delete(news).where(eq(news.id, id)).returning();
  if (n) await removeImage(n.image);
  done("/admin/lajmet?ok=deleted");
}

/* ---------------- gallery ---------------- */

export async function addGallery(_: ActionState, fd: FormData): Promise<ActionState> {
  await requireAdmin();
  const db = await getDb();
  const files = fd.getAll("images").filter((f) => typeof f !== "string" && f.size > 0);
  if (!files.length) return { error: "Zgjidhni të paktën një foto." };
  const title = str(fd, "title", 200);
  try {
    for (const f of files) {
      const url = await saveImage(f, "gallery");
      if (url) await db.insert(gallery).values({ image: url, title });
    }
  } catch (e) {
    return { error: (e as Error).message };
  }
  done("/admin/galeria?ok=1");
  return {};
}

export async function renameGallery(id: number, fd: FormData) {
  await requireAdmin();
  const db = await getDb();
  await db.update(gallery).set({ title: str(fd, "title", 200) }).where(eq(gallery.id, id));
  done("/admin/galeria?ok=1");
}

export async function deleteGallery(id: number) {
  await requireAdmin();
  const db = await getDb();
  const [g] = await db.delete(gallery).where(eq(gallery.id, id)).returning();
  if (g) await removeImage(g.image);
  done("/admin/galeria?ok=deleted");
}

/* ---------------- messages ---------------- */

export async function toggleMessage(id: number, read: boolean) {
  await requireAdmin();
  const db = await getDb();
  await db.update(messages).set({ isRead: read }).where(eq(messages.id, id));
  revalidatePath("/admin", "layout");
}

export async function deleteMessage(id: number) {
  await requireAdmin();
  const db = await getDb();
  await db.delete(messages).where(eq(messages.id, id));
  done("/admin/mesazhet?ok=deleted");
}

/* ---------------- orders ---------------- */

export async function setOrderStatus(id: number, fd: FormData) {
  await requireAdmin();
  const status = str(fd, "status", 20);
  if (!(status in ORDER_STATUSES)) return;
  const db = await getDb();
  await db.update(orders).set({ status }).where(eq(orders.id, id));
  revalidatePath("/admin", "layout");
}

export async function deleteOrder(id: number) {
  await requireAdmin();
  const db = await getDb();
  await db.delete(orderItems).where(eq(orderItems.orderId, id));
  await db.delete(orders).where(eq(orders.id, id));
  done("/admin/porosite?ok=deleted");
}

/* ---------------- settings ---------------- */

export async function saveSettings(_: ActionState, fd: FormData): Promise<ActionState> {
  await requireAdmin();
  const db = await getDb();
  const values = {
    phone: str(fd, "phone", 60),
    email: str(fd, "email", 160),
    address: str(fd, "address", 200),
    facebook: str(fd, "facebook", 300),
    instagram: str(fd, "instagram", 300),
    shippingPrice: Math.max(0, num(fd, "shippingPrice")).toFixed(2),
  };
  for (const k of ["facebook", "instagram"] as const) {
    if (values[k] && !/^https?:\/\//.test(values[k])) values[k] = "https://" + values[k];
  }
  await db.insert(settings).values({ id: 1, ...values }).onConflictDoUpdate({ target: settings.id, set: values });
  revalidatePath("/", "layout");
  return { ok: "Cilësimet u ruajtën." };
}

export async function saveAccount(_: ActionState, fd: FormData): Promise<ActionState> {
  const s = await getSession();
  if (!s) redirect("/admin/login");
  const fullname = str(fd, "fullname", 120);
  const username = str(fd, "username", 100);
  const password = String(fd.get("password") ?? "");
  const confirm = String(fd.get("confirm") ?? "");
  if (!fullname || !username) return { error: "Plotësoni emrin dhe username." };
  if (password && password.length < 8) return { error: "Password i ri duhet të ketë të paktën 8 karaktere." };
  if (password !== confirm) return { error: "Password-et nuk përputhen." };
  if (s.id === 0 && !password) return { error: "Vendosni një password të ri për ta ruajtur llogarinë." };
  const id = await saveAdmin(s.id, fullname, username, password || undefined);
  await startSession({ id, name: fullname, username });
  revalidatePath("/admin", "layout");
  return { ok: "Llogaria u përditësua." };
}
